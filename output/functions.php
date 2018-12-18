<?php
/**
 * This class handle all the user end related functions
 */
class adsforwp_output_functions{
    
    private $_amp_conditions = array();
    private $_display_tag_list = array();
    private $is_amp = false;     
    public  $visibility = null;
    public  $amp_ads_id = array();
    public function __construct() {  
         $this->_amp_conditions = array(
                    'adsforwp_after_featured_image',
                    'adsforwp_below_the_header',
                    'adsforwp_below_the_footer',
                    'adsforwp_above_the_footer',
                    'adsforwp_above_the_post_content',
                    'adsforwp_below_the_post_content',
                    'adsforwp_below_the_title',
                    'adsforwp_above_related_post',
                    'adsforwp_below_author_box',
                    'adsforwp_ads_in_loops'
                    );
         $this->_display_tag_list = array(
                    '</p>' => 'p_tag',
                    '</div>' => 'div_tag',
                    '<img>' => 'img_tag',                    
                    );
         if(!is_admin()){            
             add_action( 'init', array( $this, 'init' ) );             
         }
         
    }
    /**
     * We are here calling all required hooks
     */    
    public function adsforwp_hooks(){
        //Adsense Auto Ads hooks for amp and non amp starts here       
        add_filter('widget_text', 'do_shortcode');        
        add_action('wp_head', array($this, 'adsforwp_adblocker_detector'));
        add_action('wp_head', array($this, 'adsforwp_adsense_auto_ads'));
        add_action('amp_post_template_head',array($this, 'adsforwp_adsense_auto_ads_amp_script'),1);
        add_action('amp_post_template_footer',array($this, 'adsforwp_adsense_auto_ads_amp_tag'));
        
        //Adsense Auto Ads hooks for amp and non amp ends here
        
        add_filter('the_content', array($this, 'adsforwp_display_ads'));            
        add_shortcode('adsforwp', array($this,'adsforwp_manual_ads'));
        add_shortcode('adsforwp-group', array($this, 'adsforwp_group_ads'));
        add_action('wp_ajax_nopriv_adsforwp_get_groups_ad', array($this, 'adsforwp_get_groups_ad'));
        add_action('wp_ajax_adsforwp_get_groups_ad', array($this, 'adsforwp_get_groups_ad'));
        
        //Hooks for sticky ads
        add_action('wp_footer', array($this, 'adsforwp_display_sticky_ads')); 
        add_action('amp_post_template_css',array($this, 'adsforwp_enque_amp_script'));
        add_action('amp_post_template_footer',array($this, 'adsforwp_display_sticky_ads_amp'));
        
         add_action('wp_ajax_nopriv_adsforwp_update_amp_sticky_ad_status', array($this, 'adsforwp_update_amp_sticky_ad_status'));
         add_action('wp_ajax_nopriv_adsforwp_check_amp_sticky_ad_status', array($this, 'adsforwp_check_amp_sticky_ad_status'));
         
         add_action('wp_ajax_adsforwp_update_amp_sticky_ad_status', array($this, 'adsforwp_update_amp_sticky_ad_status'));
         add_action('wp_ajax_adsforwp_check_amp_sticky_ad_status', array($this, 'adsforwp_check_amp_sticky_ad_status'));
    }
    
    public function init(){
        
        ob_start(array($this, "adsforwp_display_custom_target_ad"));
    }
    public function adsforwp_display_custom_target_ad($content){       
                                 
                 //For single ad starts here
                 $all_ads_id = json_decode(get_transient('adsforwp_transient_ads_ids'), true);
                 
                 if(!empty($all_ads_id)){
                     
                   foreach($all_ads_id as $ad_id){                     
                     
                   $wheretodisplay = get_post_meta($ad_id,$key='wheretodisplay',true);   
                   
                   if($wheretodisplay == 'custom_target'){                                                          
                  
                   $ad_code =  $this->adsforwp_get_ad_code($ad_id, $type="AD");      
                       
                   $post_meta = get_post_meta($ad_id,$key='',true);
                  
                   if($post_meta['adsforwp_custom_target_position'][0] == 'existing_element'){
                       $action = $post_meta['adsforwp_existing_element_action'][0];
                       $jquery_selector = $post_meta['adsforwp_jquery_selector'][0];
                                                                     
                      
                       switch ($action) {
                           case 'prepend_content':
                               
                              $explod_elemnet ='';                               
                              if(strchr($jquery_selector, '#')){
                                $jquery_selector = str_replace('#', '', $jquery_selector);  
                                
                                $jquery_selector = str_replace('.', '', $jquery_selector); 
                                preg_match_all('/<[^>]*id="[^"]*\b'.$jquery_selector.'\b[^"]*"[^>]*>/', $content, $matches);
                                $explod_elemnet = explode(' ', $matches[0][0]);
                                $content = str_replace($matches[0][0], $ad_code.$explod_elemnet[0].' id="'.$jquery_selector.'">', $content);
                                
                              }
                              if(strchr($jquery_selector, '.')){
                                $jquery_selector = str_replace('.', '', $jquery_selector); 
                                preg_match_all('/<[^>]*class="[^"]*\b'.$jquery_selector.'\b[^"]*"[^>]*>/', $content, $matches);
                                $explod_elemnet = explode(' ', $matches[0][0]);
                                $content = str_replace($matches[0][0], $ad_code.$explod_elemnet[0].' class="'.$jquery_selector.'">', $content);
                              }                                                                                             
                               
                               break;
                           case 'append_content':   
                               
                              $explod_elemnet ='';                               
                              if(strchr($jquery_selector, '#')){
                                $jquery_selector = str_replace('#', '', $jquery_selector); 
                                
                                preg_match_all('/<[^>]*id="[^"]*\b'.$jquery_selector.'\b[^"]*"[^>]*>/', $content, $matches);
                                $explod_elemnet = explode(' ', $matches[0][0]);
                                $content = str_replace($matches[0][0], $explod_elemnet[0].' id="'.$jquery_selector.'">'.$ad_code, $content);
                              }
                              if(strchr($jquery_selector, '.')){
                                $jquery_selector = str_replace('.', '', $jquery_selector); 
                                preg_match_all('/<[^>]*class="[^"]*\b'.$jquery_selector.'\b[^"]*"[^>]*>/', $content, $matches);
                                $explod_elemnet = explode(' ', $matches[0][0]);
                                $content = str_replace($matches[0][0], $explod_elemnet[0].' class="'.$jquery_selector.'">'.$ad_code, $content);
                              }                                                                                            

                               break;                                                      
                           default:
                               break;
                       }
                   }
                   
                   if($post_meta['adsforwp_custom_target_position'][0] == 'new_element'){
                       $new_element_div = html_entity_decode($post_meta['adsforwp_new_element'][0]);                                              
                       $content = str_replace($new_element_div, $ad_code, $content);
                   }                                                                                  
                   }
                 }                        
                 }
                                  
                 //For single ad ends here
                 
                 //For group ads starts here
                 
                 $all_ads_id = json_decode(get_transient('adsforwp_groups_transient_ids'), true);    
                 
                 if(!empty($all_ads_id)){
                     
                   foreach($all_ads_id as $ad_id){                     
                     
                   $wheretodisplay = get_post_meta($ad_id,$key='wheretodisplay',true);   
                   
                   if($wheretodisplay == 'custom_target'){                                                          
                   $widget='';
                   $ad_code =  $this->$this->adsforwp_group_ads($atts=null, $ad_id, $widget);       
                       
                   $post_meta = get_post_meta($ad_id,$key='',true);
                  
                   if($post_meta['adsforwp_custom_target_position'][0] == 'existing_element'){
                       $action = $post_meta['adsforwp_existing_element_action'][0];
                       $jquery_selector = $post_meta['adsforwp_jquery_selector'][0];
                                                                     
                      
                       switch ($action) {
                           case 'prepend_content':
                               
                              $explod_elemnet ='';                               
                              if(strchr($jquery_selector, '#')){
                                $jquery_selector = str_replace('#', '', $jquery_selector);  
                                
                                $jquery_selector = str_replace('.', '', $jquery_selector); 
                                preg_match_all('/<[^>]*id="[^"]*\b'.$jquery_selector.'\b[^"]*"[^>]*>/', $content, $matches);
                                $explod_elemnet = explode(' ', $matches[0][0]);
                                $content = str_replace($matches[0][0], $ad_code.$explod_elemnet[0].' id="'.$jquery_selector.'">', $content);
                                
                              }
                              if(strchr($jquery_selector, '.')){
                                $jquery_selector = str_replace('.', '', $jquery_selector); 
                                preg_match_all('/<[^>]*class="[^"]*\b'.$jquery_selector.'\b[^"]*"[^>]*>/', $content, $matches);
                                $explod_elemnet = explode(' ', $matches[0][0]);
                                $content = str_replace($matches[0][0], $ad_code.$explod_elemnet[0].' class="'.$jquery_selector.'">', $content);
                              }                                                                                             
                               
                               break;
                           case 'append_content':   
                               
                              $explod_elemnet ='';                               
                              if(strchr($jquery_selector, '#')){
                                $jquery_selector = str_replace('#', '', $jquery_selector); 
                                
                                preg_match_all('/<[^>]*id="[^"]*\b'.$jquery_selector.'\b[^"]*"[^>]*>/', $content, $matches);
                                $explod_elemnet = explode(' ', $matches[0][0]);
                                $content = str_replace($matches[0][0], $explod_elemnet[0].' id="'.$jquery_selector.'">'.$ad_code, $content);
                              }
                              if(strchr($jquery_selector, '.')){
                                $jquery_selector = str_replace('.', '', $jquery_selector); 
                                preg_match_all('/<[^>]*class="[^"]*\b'.$jquery_selector.'\b[^"]*"[^>]*>/', $content, $matches);
                                $explod_elemnet = explode(' ', $matches[0][0]);
                                $content = str_replace($matches[0][0], $explod_elemnet[0].' class="'.$jquery_selector.'">'.$ad_code, $content);
                              }                                                                                            

                               break;                                                      
                           default:
                               break;
                       }
                   }
                   
                   if($post_meta['adsforwp_custom_target_position'][0] == 'new_element'){
                       $new_element_div = html_entity_decode($post_meta['adsforwp_new_element'][0]);                                              
                       $content = str_replace($new_element_div, $ad_code, $content);
                   }                                                                                  
                   }
                 }                        
                 }                 
                 //For group ads ends here                                  
                 return $content;
    }        
    
    
    public function adsforwp_enque_amp_script(){
        ?>       
        .adsforwp-stick-ad{
            padding-top:20px;
        }               
        .afw_ad_amp_achor{
            text-align:center;
        }        
        .adsforwp-sticky-ad-close {
          position: absolute;
          right: 0px;
          top: 0px;
          padding:2px;
          cursor:pointer;
          color:#000;
          background-color:#fff;
          border: #fff;
        }
        .adsforwp-sticky-ad-close:after{
        display: inline-block;
        content: "\00d7"; 
        }
        <?php        
    }
    
    public function adsforwp_update_amp_sticky_ad_status(){
        
        if ( ! isset( $_GET['adsforwp_front_nonce'] ) ){
            return; 
         }
        if ( !wp_verify_nonce( $_GET['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ){
           return;  
        }  
        
        $ad_id = sanitize_text_field($_GET['ad_id']);  
        $cookie_data = '';
        if(!isset($_COOKIE['adsforwp-stick-ad-id7']) && $_COOKIE['adsforwp-stick-ad-id7'] =='') {
                 $cookie_data .= $ad_id;                     
        } else {
                 $cookie_data .= ','.$ad_id;               
        }
        setcookie('adsforwp-stick-ad-id7', $cookie_data, time() + (86400 * 15), "/");       
    }
    
    public function adsforwp_check_amp_sticky_ad_status(){        
        
        if ( ! isset( $_GET['adsforwp_front_nonce'] ) ){
            return; 
         }
        if ( !wp_verify_nonce( $_GET['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ){
           return;  
        }  
                               
        $ad_id = sanitize_text_field($_GET['ad_id']);           
        
        $common_function_obj = new adsforwp_admin_common_functions();
        $in_group = $common_function_obj->adsforwp_check_ads_in_group($ad_id);
        
        if(isset($_COOKIE['adsforwp-stick-ad-id7'])){
            $ad_id_list = $_COOKIE['adsforwp-stick-ad-id7'];
            $explod_ad_id = explode(',', $ad_id_list);      
        }     
        $wheretodisplay = get_post_meta($ad_id,$key='wheretodisplay',true);  
        
            if(get_post_type($ad_id) =='adsforwp-groups'){
                                
                if($wheretodisplay == 'sticky' && !in_array($ad_id, $explod_ad_id)){  
                  echo json_encode(array('showNotification'=>true));   
                }else{
                  echo json_encode(array('showNotification'=>false));      
                }
                
            }else{
                                
                if($wheretodisplay == 'sticky' && !in_array($ad_id, $explod_ad_id) && empty($in_group)){  
                  echo json_encode(array('showNotification'=>true));   
                }else{
                  echo json_encode(array('showNotification'=>false));      
                }
                
            }
                                           
        wp_die();
        
    }
    
    public function adsforwp_display_sticky_ads_amp(){      
        
        //Ads stick starts here
        $all_ads_id = json_decode(get_transient('adsforwp_transient_ads_ids'), true);
        $nonce = wp_create_nonce('adsforwp_ajax_check_front_nonce');   
        
        if(!empty($all_ads_id)){
        foreach($all_ads_id as $ad_id){          
            $wheretodisplay = get_post_meta($ad_id,$key='wheretodisplay',true);  
             if($wheretodisplay == 'sticky'){  
               $ad_code =  $this->adsforwp_get_ad_code($ad_id, $type="AD"); 
               if($ad_code){
                $showurl = admin_url('admin-ajax.php?action=adsforwp_check_amp_sticky_ad_status&timestamp='.time().'&adsforwp_front_nonce='.$nonce.'&ad_id='.$ad_id);                                  
                $dismissurl = admin_url('admin-ajax.php?action=adsforwp_update_amp_sticky_ad_status&timestamp='.time().'&adsforwp_front_nonce='.$nonce.'&ad_id='.$ad_id);                                  
                echo '<amp-user-notification
                        layout="nodisplay"
                        id="amp-user-notification_'.esc_attr($ad_id).'"  
                        data-show-if-href="'.esc_url($showurl).'"
                        data-dismiss-href="'.esc_url($dismissurl).'">
                        <div class="adsforwp-stick-ad">'.$ad_code.'</div>                      
                        <button on="tap:amp-user-notification_'.esc_attr($ad_id).'.dismiss" class="adsforwp-sticky-ad-close"></button>
                     </amp-user-notification>';
               }
             }
            }
        }
        //Ads stick ends here
        
        //Group stick starts here
        
        $all_group_post = json_decode(get_transient('adsforwp_groups_transient_ids'), true);          
        
        if(!empty($all_group_post)){
        foreach($all_group_post as $ad_id){          
            $wheretodisplay = get_post_meta($ad_id,$key='wheretodisplay',true);  
             if($wheretodisplay == 'sticky'){  
               $widget ='';
               $ad_code =  $this->$this->adsforwp_group_ads($atts=null, $ad_id, $widget); 
               if($ad_code){
                $showurl = admin_url('admin-ajax.php?action=adsforwp_check_amp_sticky_ad_status&timestamp='.time().'&adsforwp_front_nonce='.$nonce.'&ad_id='.$ad_id);                                  
                $dismissurl = admin_url('admin-ajax.php?action=adsforwp_update_amp_sticky_ad_status&timestamp='.time().'&adsforwp_front_nonce='.$nonce.'&ad_id='.$ad_id);                                  
                echo '<amp-user-notification
                        layout="nodisplay"
                        id="amp-user-notification_'.esc_attr($ad_id).'"  
                        data-show-if-href="'.esc_url($showurl).'"
                        data-dismiss-href="'.esc_url($dismissurl).'">
                        <div class="adsforwp-stick-ad">'.$ad_code.'</div>                      
                        <button on="tap:amp-user-notification_'.esc_attr($ad_id).'.dismiss" class="adsforwp-sticky-ad-close"></button>
                     </amp-user-notification>';
               }
             }
            }
        }                
        //Group stick ends here
        
    }

    public function adsforwp_display_sticky_ads(){                
        
        $explod_ad_id = array();
        if(isset($_COOKIE['adsforwp-stick-ad-id7'])){
        $ad_id_list = $_COOKIE['adsforwp-stick-ad-id7'];
        $explod_ad_id = explode(',', $ad_id_list);      
        }  
        $common_function_obj = new adsforwp_admin_common_functions();
        
         //Ads Sticky starts here
        $ad_code ='';
        $all_ads_id = json_decode(get_transient('adsforwp_transient_ads_ids'), true);
                        
        if(!empty($all_ads_id)){
        foreach($all_ads_id as $ad_id){
            
            $in_group = $common_function_obj->adsforwp_check_ads_in_group($ad_id); 
            $wheretodisplay = get_post_meta($ad_id,$key='wheretodisplay',true);  
             if($wheretodisplay == 'sticky' && !in_array($ad_id, $explod_ad_id) && empty($in_group)){  
               $ad_code .=  $this->adsforwp_get_ad_code($ad_id, $type="AD");   
             }
             
        }    
        }                    
        if($ad_code){
            echo '<div class="adsforwp-footer-prompt">'
           . '<a href="#" class="adsforwp-sticky-ad-close"></a>'  
           . '<div class="adsforwp-stick-ad">'.$ad_code.'</div>'                
           . '</div>';
        }    
        //Ads Sticky ends here
        
        //Group Sticky starts here
        $group_ad_code ='';
        $all_group_post = json_decode(get_transient('adsforwp_groups_transient_ids'), true);    
                        
        if(!empty($all_ads_id)){
        foreach($all_group_post as $ad_id){            
            $widget = '';                        
            $wheretodisplay = get_post_meta($ad_id,$key='wheretodisplay',true);  
             if($wheretodisplay == 'sticky' && !in_array($ad_id, $explod_ad_id)){  
               $group_ad_code .=  $this->adsforwp_group_ads($atts=null, $ad_id, $widget);   
             }
             
        }    
        }                    
        if($group_ad_code){
            echo '<div class="adsforwp-footer-prompt">'
           . '<a href="#" class="adsforwp-sticky-ad-close"></a>'  
           . '<div class="adsforwp-stick-ad">'.$group_ad_code.'</div>'                
           . '</div>';
        }    
        //Group Sticky ends here
    }            
    /**
     * This function returns publisher id or data ad client id for adsense ads
     * @return type
     */
    
    public function adsforwp_get_pub_id_on_revenue_percentage(){
        
                    $settings = adsforwp_defaultSettings();  
                    $ad_revenue_sharing ='';
                    $ad_owner_revenue_per='';
                    $ad_author_revenue_per='';  
                    $owner_display_per_in_minute ='';
                    $author_adsense_ids = array();
                    if(array_key_exists('ad_revenue_sharing', $settings)){
                    $ad_revenue_sharing    = $settings['ad_revenue_sharing'];  
                    $ad_owner_revenue_per  = $settings['ad_owner_revenue_per'];
                    $ad_author_revenue_per = $settings['ad_author_revenue_per'];
                    $owner_display_per_in_minute = (60*$ad_owner_revenue_per)/100;
                    }                    
                    $current_second = date("s"); 
                    if((!($current_second <= $owner_display_per_in_minute)) && isset($settings['ad_revenue_sharing'])){
                     $author_adsense_ids['author_pub_id'] =  get_the_author_meta( 'adsense_pub_id' );                     
                     $author_adsense_ids['author_ad_slot_id'] =  get_the_author_meta( 'adsense_ad_slot_id' );                     
                    }                           
                    return $author_adsense_ids;                    
    }
    public function adsforwp_get_adsense_publisher_id(){                    
                    $data_ad_client ='';
                    $response = array();
                    $cc_args = array(
                        'posts_per_page'   => -1,
                        'post_type'        => 'adsforwp',
                        'meta_key'         => 'adsense_type',
                        'meta_value'       => 'adsense_auto_ads',
                    );
                    $postdata = new WP_Query($cc_args);  
                    $auto_adsense_post = $postdata->posts; 
                    if($postdata->post_count >0){                   
                    
                    $data_ad_client = get_post_meta($auto_adsense_post[0]->ID,$key='data_client_id',true); 
                    $author_adsense_ids = $this->adsforwp_get_pub_id_on_revenue_percentage();
                    if($author_adsense_ids){
                    $author_pub_id = $author_adsense_ids['author_pub_id'];
                    if($author_pub_id){
                    $data_ad_client = $author_pub_id;     
                    }   
                    }                   
                    $response = array('post_id' => $auto_adsense_post[0]->ID, 'data_ad_client' => $data_ad_client);
                    }                    
                    return $response;
    }
    /**
     * we are here enqueying adsense auto ads script for amp posts
     */
    public function adsforwp_adsense_auto_ads_amp_script(){
        
          $result = $this->adsforwp_get_adsense_publisher_id(); 
          if($result){
          $post_id = $result['post_id'];   
          $placement_obj = new adsforwp_view_placement();
          $condition_status = $placement_obj->adsforwp_get_post_conditions_status($post_id);   
          
          $visitor_condition_obj = new adsforwp_view_visitor_condition();
          $visitor_condition_status = $visitor_condition_obj->adsforwp_visitor_conditions_status($post_id);
          
          
          if (( $condition_status ===1 || $condition_status === true || $condition_status==='notset' ) && ( $visitor_condition_status ===1 || $visitor_condition_status === true || $visitor_condition_status==='notset' )) {
           echo '<script async custom-element="amp-auto-ads" src="https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js"></script>';   
          }                    
          }
        
    }
    /**
     * we are here integrating adsense auto ads amp tag for amp posts
     */
    public function adsforwp_adsense_auto_ads_amp_tag(){  
        
            $result = $this->adsforwp_get_adsense_publisher_id();                          
            if($result){
            $post_id = $result['post_id'];
            $content =  '<amp-auto-ads
                            type="adsense"
                            data-ad-client="'.esc_attr($result['data_ad_client']).'">
                        </amp-auto-ads>';
            $this->adsforwp_adsense_auto_ads_content($content, $post_id);
            }            
    }
    /**
     * we are here integrating adsense auto ads for ever non amp posts
     */
    public function adsforwp_adsense_auto_ads(){
            
            $result = $this->adsforwp_get_adsense_publisher_id();            
            if($result){
            $post_id = $result['post_id']; 
            $content = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                  <script>
                  (adsbygoogle = window.adsbygoogle || []).push({
                  google_ad_client: "'.esc_attr($result['data_ad_client']).'",
                  enable_page_level_ads: true
                  }); 
                 </script>';
            $this->adsforwp_adsense_auto_ads_content($content, $post_id);
            }
           
    }
    
    public function adsforwp_adsense_auto_ads_content($content, $post_id){
        
            $placement_obj = new adsforwp_view_placement();
            $condition_status = $placement_obj->adsforwp_get_post_conditions_status($post_id);
            
            $visitor_condition_obj = new adsforwp_view_visitor_condition();
            $visitor_condition_status = $visitor_condition_obj->adsforwp_visitor_conditions_status($post_id);
                                                
            if (( $condition_status ===1 || $condition_status === true || $condition_status==='notset' ) && ( $visitor_condition_status ===1 || $visitor_condition_status === true || $visitor_condition_status==='notset' )) {     
            $current_post_data = get_post_meta(get_the_ID(),$key='',true);                  
            if(array_key_exists('ads-for-wp-visibility', $current_post_data)){
            $this->visibility = $current_post_data['ads-for-wp-visibility'][0];    
            }  
            if($this->visibility != 'hide') {
            $post_meta_dataset = get_post_meta($post_id,$key='',true);                        
            $current_date = date("Y-m-d");
            $adsforwp_ad_expire_enable = $post_meta_dataset['adsforwp_ad_expire_day_enable'][0];      
            $ad_expire_from = $post_meta_dataset['adsforwp_ad_expire_from'][0];      
            $ad_expire_to = $post_meta_dataset['adsforwp_ad_expire_to'][0];      
            $adsforwp_ad_days_enable = $post_meta_dataset['adsforwp_ad_expire_day_enable'][0];      
            $adsforwp_ad_expire_days = get_post_meta($post_id,$key='adsforwp_ad_expire_days',true);                   
                                     
            if($adsforwp_ad_expire_enable){
                
             if($ad_expire_from && $ad_expire_to )  {     
                 
                if($ad_expire_from <= $current_date && $ad_expire_to >=$current_date){
                    
                 if($adsforwp_ad_days_enable){
                     
                    foreach ($adsforwp_ad_expire_days as $days){
                        
                        if(date('Y-m-d', strtotime($days))==$current_date){
                            
                         echo $content;     
                        }
                    }      
                }else{
                echo $content;          
                }                                                        
                }                             
            }else{
              echo $content;    
            }
            }else{
              if($adsforwp_ad_days_enable){
                  
                    foreach ($adsforwp_ad_expire_days as $days){
                        
                        if(date('Y-m-d', strtotime($days))==$current_date){
                            
                        echo $content;
                        }
                    }      
                }else{
                 echo $content;
                }
            }
            }
         }
    }
    /**
     * This hook function display content in post. we are modifying post content here
     * @param type $content
     * @return type string
     */
    public function adsforwp_display_ads($content){       
        
         delete_transient('adsforwp_transient_amp_ids');                                      
        $current_post_data = get_post_meta(get_the_ID(),$key='',true);                  
        if(array_key_exists('ads-for-wp-visibility', $current_post_data)){
        $this->visibility = $current_post_data['ads-for-wp-visibility'][0];    
        }
        if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
            $this->is_amp = true;        
        }         
        if($this->visibility != 'hide') {            
            //Ads positioning starts here
            $all_ads_post = json_decode(get_transient('adsforwp_transient_ads_ids'), true);              
            if($all_ads_post){
                
            foreach($all_ads_post as $ads){                               
            $post_ad_id = $ads;             
            $common_function_obj = new adsforwp_admin_common_functions();
            $in_group = $common_function_obj->adsforwp_check_ads_in_group($post_ad_id);
           
            if(empty($in_group)){                
            $amp_display_condition = get_post_meta($post_ad_id,$key='wheretodisplayamp',true);
            if(in_array($amp_display_condition, $this->_amp_conditions) && $this->is_amp){
                return $content;
            }                    
            $where_to_display=""; 
            $adposition="";    
            $post_meta_dataset = array();
            $post_meta_dataset = get_post_meta($post_ad_id,$key='',true);
            $ad_code =  $this->adsforwp_get_ad_code($post_ad_id, $type="AD"); 
            if(array_key_exists('wheretodisplay', $post_meta_dataset)){
            $where_to_display = $post_meta_dataset['wheretodisplay'][0];  
            }
            if(array_key_exists('adposition', $post_meta_dataset)){
            $adposition = $post_meta_dataset['adposition'][0];    
            }                                                                                                                                        
           //Displays all ads according to their settings paragraphs starts here              
            switch ($where_to_display) {
                
             case 'after_the_content':
              $content = $content.$ad_code;
              break;
          
             case 'before_the_content':
              $content = $ad_code.$content;
              break;
          
             case 'between_the_content':        
              if($adposition == 'number_of_paragraph'){
                $paragraph_id = $post_meta_dataset['paragraph_number'][0];   
                $entered_tag_name ='';                
                $display_tag_name ='';                
                
                if(isset($post_meta_dataset['display_tag_name'])){
                  $display_tag_name = $post_meta_dataset['display_tag_name'][0];                  
                }
                if(isset($post_meta_dataset['entered_tag_name'])){
                  $entered_tag_name = '</'.$post_meta_dataset['entered_tag_name'][0].'>';                  
                }                 
                if($display_tag_name !=''){                    
                    if($display_tag_name == 'custom_tag'){
                     $closing_p = $entered_tag_name;   
                    }else{                        
                     $closing_p = array_search($display_tag_name,$this->_display_tag_list);      
                    }                                     
                }else{
                 $closing_p = '</p>';   
                }
                if($closing_p == '<img>'){                                                                         
                if($paragraph_id){
                 preg_match_all( '/<img[^>]+\>/' , $content, $match );
                 $adsforwp_images = array_pop($match);                    
                 $image_ad = $adsforwp_images[$paragraph_id-1].$ad_code;               
                 $content  = str_replace( $adsforwp_images[$paragraph_id-1], $image_ad, $content ); 
                }
                               
                }else{
                 $paragraphs = explode( $closing_p, $content );                  
                    foreach ($paragraphs as $index => $paragraph) {

                     if ( trim( $paragraph ) ) {
                           $paragraphs[$index] .= $closing_p;
                       }
                       if ( $paragraph_id == $index + 1 ) {
                           $paragraphs[$index] .= $ad_code;
                       }
                     }
                  $content = implode( '', $paragraphs );   
                }                                               
                }
        
               if($adposition == '50_of_the_content'){
                 $closing_p = '</p>';
                 $paragraphs = explode( $closing_p, $content );       
                 $total_paragraphs = count($paragraphs);
                 $paragraph_id = round($total_paragraphs /2);       
                 foreach ($paragraphs as $index => $paragraph) {
                    if ( trim( $paragraph ) ) {
                        $paragraphs[$index] .= $closing_p;
                    }
                    if ( $paragraph_id == $index + 1 ) {
                        $paragraphs[$index] .= $ad_code;
                    }
                  }
                   $content = implode( '', $paragraphs ); 
                 }
                break;             
             default:
               break;
          }      
          //Displays all ads according to their settings paragraphs ends here                       
            }
         }                          
            }
            //Ads positioning ends here
            
            //Groups positioning starts here
            $all_group_post = json_decode(get_transient('adsforwp_groups_transient_ids'), true);            
            if($all_group_post){
                
            foreach($all_group_post as $group){                               
            $post_group_id = $group;             
            
            $amp_display_condition = get_post_meta($post_group_id,$key='wheretodisplayamp',true);
            if(in_array($amp_display_condition, $this->_amp_conditions) && $this->is_amp){
                return $content;
            }                        
            $where_to_display=""; 
            $adposition="";    
            $post_meta_dataset = array();
            $post_meta_dataset = get_post_meta($post_group_id,$key='',true);
            $widget = '';
            $ad_code =  $this->adsforwp_group_ads($atts=null, $post_group_id, $widget);  
            if(array_key_exists('wheretodisplay', $post_meta_dataset)){
            $where_to_display = $post_meta_dataset['wheretodisplay'][0];  
            }
            if(array_key_exists('adposition', $post_meta_dataset)){
            $adposition = $post_meta_dataset['adposition'][0];    
            }
                                                                                                                                             
           //Displays all ads according to their settings paragraphs starts here              
            switch ($where_to_display) {
                
             case 'after_the_content':
              $content = $content.$ad_code;
              break;
          
             case 'before_the_content':
              $content = $ad_code.$content;
              break;
          
             case 'between_the_content':        
              if($adposition == 'number_of_paragraph'){
                $paragraph_id = $post_meta_dataset['paragraph_number'][0];   
                $closing_p = '</p>';
                $paragraphs = explode( $closing_p, $content );   
                foreach ($paragraphs as $index => $paragraph) {

                 if ( trim( $paragraph ) ) {
                       $paragraphs[$index] .= $closing_p;
                   }
                   if ( $paragraph_id == $index + 1 ) {
                       $paragraphs[$index] .= $ad_code;
                   }
                 }
                        $content = implode( '', $paragraphs );
                }
        
               if($adposition == '50_of_the_content'){
                 $closing_p = '</p>';
                 $paragraphs = explode( $closing_p, $content );       
                 $total_paragraphs = count($paragraphs);
                 $paragraph_id = round($total_paragraphs /2);       
                 foreach ($paragraphs as $index => $paragraph) {
                    if ( trim( $paragraph ) ) {
                        $paragraphs[$index] .= $closing_p;
                    }
                    if ( $paragraph_id == $index + 1 ) {
                        $paragraphs[$index] .= $ad_code;
                    }
                  }
                   $content = implode( '', $paragraphs ); 
                 }
                break;             
             default:
               break;
          }      
          //Displays all ads according to their settings paragraphs ends here   
                               
         }                          
            }
            //Groups positioning ends here
            
         }         
        return $content;    
    }    
    /**
     * we are generating html or amp code for ads which will be displayed in post content.
     * @param type $post_ad_id
     * @return string 
     */
    public function adsforwp_get_ad_code($post_ad_id, $type){
            
            $amp_ads_id = json_decode(get_transient('adsforwp_transient_amp_ids'), true); 
            
            $visitor_condition_status ='';
            $condition_status ='';
            if($type =="AD"){                                                
            $placement_obj = new adsforwp_view_placement();
            $condition_status = $placement_obj->adsforwp_get_post_conditions_status($post_ad_id);
            
            $visitor_condition_obj = new adsforwp_view_visitor_condition();
            $visitor_condition_status = $visitor_condition_obj->adsforwp_visitor_conditions_status($post_ad_id);
            
            }              
            if((($condition_status ===1 || $condition_status === true || $condition_status==='notset')&& ($visitor_condition_status ===1 || $visitor_condition_status === true || $visitor_condition_status==='notset')) || $type=='GROUP' ){
            if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
            $this->is_amp = true;        
            }
            $ad_image="";
            $ad_image_redirect_url="";
            $ad_type="";
            $ad_code ="";   
            $ad_expire_to ="";
            $ad_expire_from ="";
            $custom_ad_code="";
            $where_to_display="";                        
            $amp_compatibility ="";              
            $adsforwp_ad_expire_enable ="";                        
            $adsforwp_ad_days_enable ="";
            $adsforwp_non_amp_visibility ="";
            $adsforwp_ad_responsive ="";
            
            
            $ad_margin_top     = 0;
            $ad_margin_bottom  = 0;
            $ad_margin_left    = 0;
            $ad_margin_right   = 0;
            $ad_alignment      = '';
            
            $adsforwp_ad_expire_days =array();
            $post_meta_dataset = array();                      
            $post_meta_dataset = get_post_meta($post_ad_id,$key='',true);             
            if(array_key_exists('wheretodisplay', $post_meta_dataset)){
            $where_to_display = $post_meta_dataset['wheretodisplay'][0];  
            }
            if($type =="AD"){
            $ad_margin_top ='';
            $ad_margin_bottom ='';
            $ad_margin_left ='';
            $ad_margin_right ='';
            
            $margin_post_meta  = get_post_meta($post_ad_id, $key='adsforwp_ad_margin',true);
            if(isset($margin_post_meta['ad_margin_top'])){
             $ad_margin_top     = $margin_post_meta['ad_margin_top'];   
            }
            
            if(isset($margin_post_meta['ad_margin_bottom'])){
             $ad_margin_bottom  = $margin_post_meta['ad_margin_bottom'];  
            }
            
            if(isset($margin_post_meta['ad_margin_left'])){
             $ad_margin_left    = $margin_post_meta['ad_margin_left'];  
            }
                        
            if(isset($margin_post_meta['ad_margin_right'])){
             $ad_margin_right    = $margin_post_meta['ad_margin_right'];  
            }            
            if($where_to_display !='ad_shortcode'){
            if(isset($post_meta_dataset['adsforwp_ad_align'])){
            $ad_alignment      = $post_meta_dataset['adsforwp_ad_align'][0];    
            }                    
            }
            } 
            if(array_key_exists('adsforwp_ad_responsive', $post_meta_dataset)){
            $adsforwp_ad_responsive = $post_meta_dataset['adsforwp_ad_responsive'][0];    
            }
            if(array_key_exists('custom_code', $post_meta_dataset)){
            $custom_ad_code = $post_meta_dataset['custom_code'][0];    
            }            
            if(array_key_exists('adsforwp_ad_image', $post_meta_dataset)){
            $ad_image = $post_meta_dataset['adsforwp_ad_image'][0];    
            }
            if(array_key_exists('adsforwp_ad_redirect_url', $post_meta_dataset)){
            $ad_image_redirect_url = $post_meta_dataset['adsforwp_ad_redirect_url'][0];                          
            }                                    
            if(array_key_exists('select_adtype', $post_meta_dataset)){
            $ad_type = $post_meta_dataset['select_adtype'][0];      
            }
            if(array_key_exists('adsforwp_ad_expire_day_enable', $post_meta_dataset)){
            $adsforwp_ad_expire_enable = $post_meta_dataset['adsforwp_ad_expire_day_enable'][0];      
            }            
            if(array_key_exists('adsforwp_ad_expire_from', $post_meta_dataset)){
            $ad_expire_from = $post_meta_dataset['adsforwp_ad_expire_from'][0];      
            }
            if(array_key_exists('adsforwp_ad_expire_to', $post_meta_dataset)){
            $ad_expire_to = $post_meta_dataset['adsforwp_ad_expire_to'][0];      
            }
            if(array_key_exists('adsforwp_ad_expire_day_enable', $post_meta_dataset)){
            $adsforwp_ad_days_enable = $post_meta_dataset['adsforwp_ad_expire_day_enable'][0];      
            }
            if(array_key_exists('ads_for_wp_non_amp_visibility', $post_meta_dataset)){
            $adsforwp_non_amp_visibility = $post_meta_dataset['ads_for_wp_non_amp_visibility'][0];      
            }
            if(array_key_exists('adsforwp_ad_expire_days', $post_meta_dataset)){
            $adsforwp_ad_expire_days = get_post_meta($post_ad_id,$key='adsforwp_ad_expire_days',true);
            }
            
            if($ad_type !=""){                                        
            if(array_key_exists('ads-for-wp_amp_compatibilty', $post_meta_dataset)){
            $amp_compatibility = $post_meta_dataset['ads-for-wp_amp_compatibilty'][0];              
            }
                                         
            switch ($ad_type) {
            case 'custom':
                    if($this->is_amp){
                     if($amp_compatibility != 'disable'){
                     $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_custom afw_ad afwadid-'.esc_attr($post_ad_id).'">
							'.$custom_ad_code.'
							</div>';    
                    }   
                    }else{
                    if($adsforwp_non_amp_visibility !='hide'){
                    $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_custom  afw_ad afwadid-'.esc_attr($post_ad_id).'">
							'.$custom_ad_code.'
							</div>';     
                    }                               
                    }                                                                                
            break;
            case 'ad_image':
                    
                    $adsforwp_ad_img_width = $post_meta_dataset['adsforwp_ad_img_width'][0];
                    $adsforwp_ad_img_height = $post_meta_dataset['adsforwp_ad_img_height'][0];                     
                    if($this->is_amp){
                     if($amp_compatibility != 'disable'){
                     $amp_ads_id[] = $post_ad_id;   
                     
                     if($adsforwp_ad_responsive !='' && $adsforwp_ad_responsive ==1){
                     $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
							<div class="afw_ad_amp_achor afw_ad_amp_anchor_'.esc_attr($post_ad_id).'"><a target="_blank" href="'.esc_url($ad_image_redirect_url).'" rel="nofollow"><amp-img layout="responsive" class="afw_ad_amp_'.esc_attr($post_ad_id).'" src="'.esc_url($ad_image).'" height="'. esc_attr($adsforwp_ad_img_height).'" width="'.esc_attr($adsforwp_ad_img_width).'"></amp-img></a></div>
							</div>';    
                     }else{
                     $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
							<div class="afw_ad_amp_achor afw_ad_amp_anchor_'.esc_attr($post_ad_id).'"><a target="_blank" href="'.esc_url($ad_image_redirect_url).'" rel="nofollow"><amp-img class="afw_ad_amp_'.esc_attr($post_ad_id).'" src="'.esc_url($ad_image).'" height="'. esc_attr($adsforwp_ad_img_height).'" width="'.esc_attr($adsforwp_ad_img_width).'"></amp-img></a></div>
							</div>';    
                     }
                     
                    }   
                    }else{
                        if($adsforwp_non_amp_visibility !='hide'){
                            
                         if(isset($adsforwp_ad_responsive)){
                             
                             $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
							<a target="_blank" href="'.esc_url($ad_image_redirect_url).'" rel="nofollow"><img height="auto" max-width="100%" src="'.esc_url($ad_image).'"></a>
							</div>';  
                         }else{
                             
                             $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
							<a target="_blank" href="'.esc_url($ad_image_redirect_url).'" rel="nofollow"><img height="'. esc_attr($adsforwp_ad_img_height).'" width="'.esc_attr($adsforwp_ad_img_width).'" src="'.esc_url($ad_image).'"></a>
							</div>';  
                             
                         }                                                              
                    } 
                    
                   }                                                                               
            break;
            
            case 'contentad':
                    
                    $contentad_id = $post_meta_dataset['contentad_id'][0];
                    $contentad_id_d = $post_meta_dataset['contentad_id_d'][0];
                    $contentad_widget_id = $post_meta_dataset['contentad_widget_id'][0];                     
                    if($this->is_amp){
                       $amp_ads_id[] = $post_ad_id;
                     if($amp_compatibility != 'disable'){
                     $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                        <a class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
							<amp-ad
                                                                class="afw_ad_amp_'.esc_attr($post_ad_id).'"
                                                                width=300
                                                                height=250
                                                                type="contentad"
                                                                data-id="'.esc_attr($contentad_id).'"
                                                                data-d="'.esc_attr($contentad_id_d).'"
                                                                data-wid="'.esc_attr($contentad_widget_id).'">
                                                              </amp-ad>
                                                        </a>
							</div>';    
                    }   
                    }else{
                      if($adsforwp_non_amp_visibility !='hide'){  
                    $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
							<div id="contentad'.$contentad_widget_id.'"></div><!-- Load Widget Here --></div>
                                                            <script type="text/javascript">
                                                            (function(d) {
                                                              var params =
                                                              {
                                                                id: "'.esc_attr($contentad_id).'",
                                                                d: "'.esc_attr($contentad_id_d).'",
                                                                wid: "'.esc_attr($contentad_widget_id).'",
                                                                cb: (new Date()).getTime()
                                                              };

                                                              var qs=[];
                                                              for(var key in params) qs.push(key+"="+encodeURIComponent(params[key]));
                                                              var s = d.createElement("script");s.type="text/javascript";s.async=true;
                                                              var p = "https:" == document.location.protocol ? "https" : "http";
                                                              s.src = p + "://api.content.ad/Scripts/widget2.aspx?" + qs.join("&");
                                                              d.getElementById("contentad'.esc_attr($contentad_widget_id).'").appendChild(s);
                                                            })();
                                                          </script>
							</div>';        
                    }  
                   }
            break;
            
            case 'ad_now':
                    
                    $ad_now_widget_id = $post_meta_dataset['ad_now_widget_id'][0];                    
                    if(!$this->is_amp){
                     $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
				 <div id="SC_TBlock_'.$ad_now_widget_id.'" class="SC_TBlock">loading...</div>
                                 <script type="text/javascript">
                                      (sc_adv_out = window.sc_adv_out || []).push({
                                        id : "'.esc_attr($ad_now_widget_id).'",
                                        domain : "n.ads1-adnow.com"
                                       });
                                 </script>
                                 <script type="text/javascript" src="//st-n.ads1-adnow.com/js/a.js"></script>    
				 </div>';        
                    }                                                                                
            break;
            
            case 'infolinks':
                    
                    $infolinks_pid = $post_meta_dataset['infolinks_pid'][0];
                    $infolinks_wsid = $post_meta_dataset['infolinks_wsid'][0];
                    if(!$this->is_amp){
                     $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                  <script type="text/javascript">
                                    var infolinks_pid = '.esc_attr($infolinks_pid).';
                                    var infolinks_wsid = '.esc_attr($infolinks_wsid).';
                                  </script>
                                <script type="text/javascript" src="http://resources.infolinks.com/js/infolinks_main.js"></script>
                                </div>';        
                    }                                                                                
            break;
            
           //adsense ads logic code starts here
            case 'adsense':
            $adsense_type = '';
            $author_ad_slot_id ='';
            if(array_key_exists('adsense_type', $post_meta_dataset)){
             $adsense_type = $post_meta_dataset['adsense_type'][0];     
            }           
            $ad_client = $post_meta_dataset['data_client_id'][0];    
            $author_adsense_ids = $this->adsforwp_get_pub_id_on_revenue_percentage();
            if($author_adsense_ids){
            $author_pub_id = $author_adsense_ids['author_pub_id'];
            $author_ad_slot_id = $author_adsense_ids['author_ad_slot_id'];   
            if($author_pub_id){
            $ad_client = $author_pub_id;     
            }
            }                                    
            switch ($adsense_type) {
                case 'normal':
                    $ad_slot = $post_meta_dataset['data_ad_slot'][0]; 
                    if($author_ad_slot_id){
                    $ad_slot = $author_ad_slot_id;     
                    }
                    $width='200';
                    $height='200';
                    $banner_size = $post_meta_dataset['banner_size'][0]; 
                    if($banner_size !=''){
                    $explode_size = explode('x', $banner_size);              
                    $width = $explode_size[0];            
                    $height = $explode_size[1];                               
                    }            
                    if($this->is_amp){
                       
                        $amp_ads_id[] = $post_ad_id;
                        if($amp_compatibility != 'disable'){
                           
                         if(isset($adsforwp_ad_responsive)){
                             
                             $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                     <div class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                                       <amp-ad width="100vw" height=320                                        
                                        type="adsense"                                        
                                        data-ad-client="'. esc_attr($ad_client) .'"
                                        data-ad-slot="'.esc_attr($ad_slot).'"
                                        data-auto-format="rspv" 
                                        data-full-width>
                                        <div overflow></div>
                                    </amp-ad>
                                    </div>
                                    </div>';
                             
                         }else{
                           
                          $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                     <div class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                                       <amp-ad 
                                        class="afw_ad_amp_'.esc_attr($post_ad_id).'"
                                        type="adsense"
                                        width="'. esc_attr($width) .'"
                                        height="'. esc_attr($height) .'"
                                        data-ad-client="'. esc_attr($ad_client) .'"
                                        data-ad-slot="'.esc_attr($ad_slot).'">
                                    </amp-ad>
                                    </div>
                                    </div>';   
                             
                             
                         }                                                        
                        }                             
                    }else{     
                     if($adsforwp_non_amp_visibility !='hide'){ 
                         
                      if(isset($adsforwp_ad_responsive)){
                          $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                                <script async="" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">
                                                                </script>
                                                                <ins 
                                                                class="adsbygoogle" 
                                                                style="display:block;                                                                                                                              
                                                                data-ad-client="'.esc_attr($ad_client).'"
                                                                data-ad-slot="'.esc_attr($ad_slot).'"
                                                                data-ad-format="auto">
                                                                </ins>
                                                                <script>
                                                                        (adsbygoogle = window.adsbygoogle || []).push({});
                                                                </script>
                                                        </div>';
                      }else{
                        $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                                <script async="" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">
                                                                </script>
                                                                <ins class="adsbygoogle" style="display:inline-block;width:'.esc_attr($width).'px;height:'.esc_attr($height).'px" data-ad-client="'.esc_attr($ad_client).'" data-ad-slot="'.esc_attr($ad_slot).'">
                                                                </ins>
                                                                <script>
                                                                        (adsbygoogle = window.adsbygoogle || []).push({});
                                                                </script>
                                                        </div>';  
                      }                         
                     
                    }
                    }
                    break;                
                default:
                    break;
            }                                                                                                                
            break;
            
            case 'media_net':
            $ad_data_cid ='';            
            $ad_data_crid ='';
            
            if(isset($post_meta_dataset['data_cid'])){
             $ad_data_cid = $post_meta_dataset['data_cid'][0];   
            }
            if(isset($post_meta_dataset['data_crid'])){
             $ad_data_crid = $post_meta_dataset['data_crid'][0];       
            }            
            $width='200';
            $height='200';
            $banner_size = $post_meta_dataset['banner_size'][0];    
            if($banner_size !=''){
            $explode_size = explode('x', $banner_size);            
            $width = $explode_size[0];            
            $height = $explode_size[1];                               
            }            
            if($this->is_amp){
                if($amp_compatibility != 'disable'){
                 $amp_ads_id[] = $post_ad_id;
                 $ad_code = 
                            '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw-md afw_ad afwadid-'.esc_attr($post_ad_id).'">
                            <a class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                            <amp-ad 
                                class="afw_ad_amp_'.esc_attr($post_ad_id).'"
				type="medianet"
				width="'. esc_attr($width) .'"
				height="'. esc_attr($height) .'"
                                data-tagtype="cm"    
				data-cid="'. esc_attr($ad_data_cid).'"
				data-crid="'.esc_attr($ad_data_crid).'">
			    </amp-ad>;  
                            </a>
                            </div>';    
                }                             
				                
            }else{      
            if($adsforwp_non_amp_visibility !='hide'){
             $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw-md afw_ad afwadid-'.esc_attr($post_ad_id).'">
						<script id="mNCC" language="javascript">
                                                            medianet_width = "'.esc_attr($width).'";
                                                            medianet_height = "'.esc_attr($height).'";
                                                            medianet_crid = "'.esc_attr($ad_data_crid).'"
                                                            medianet_versionId ="3111299"
                                                   </script>
                                                   <script src="//contextual.media.net/nmedianet.js?cid='.esc_attr($ad_data_cid).'"></script>		
						</div>';   
            }       
            }
            break;
            default:
            break;
        }      
                          
         $amp_ads_id_json = json_encode($amp_ads_id);
         set_transient('adsforwp_transient_amp_ids', $amp_ads_id_json); 
        
            $current_date = date("Y-m-d"); 
            
            if($adsforwp_ad_expire_enable){
                
             if($ad_expire_from && $ad_expire_to )  {     
                 
                if($ad_expire_from <= $current_date && $ad_expire_to >=$current_date){
                    
                 if($adsforwp_ad_days_enable){
                     
                    foreach ($adsforwp_ad_expire_days as $days){
                        
                        if(date('Y-m-d', strtotime($days))==$current_date){
                            
                         return $ad_code;     
                        }
                    }      
                }else{
                return $ad_code;          
                }                                                        
                }                             
            }else{
              return $ad_code;    
            }
            }else{
              if($adsforwp_ad_days_enable){
                  
                    foreach ($adsforwp_ad_expire_days as $days){
                        
                        if(date('Y-m-d', strtotime($days))==$current_date){
                            
                        return $ad_code;     
                        }
                    }      
                }else{
                 return $ad_code;     
                }
            }
        
      }  
    }
            
}

    /**
     * We are displaying ads as per shortcode. eg ["adsforwp id="000"]
     * @param type $atts
     * @return type string
     */
    public function adsforwp_manual_ads($atts) {	
        
        $post_ad_id =   $atts['id'];           
        if($post_ad_id){
            
        $placement_obj = new adsforwp_view_placement();
        $condition_status = $placement_obj->adsforwp_get_post_conditions_status($post_ad_id);  
        
        $visitor_condition_obj = new adsforwp_view_visitor_condition();
        $visitor_condition_status = $visitor_condition_obj->adsforwp_visitor_conditions_status($post_ad_id);
        
        
                 
        if(($condition_status ===1 || $condition_status === true || $condition_status==='notset')&& ($visitor_condition_status ===1 || $visitor_condition_status === true || $visitor_condition_status==='notset')){
            
            if($this->visibility != 'hide') {                                    
            $ad_code =  $this->adsforwp_get_ad_code($post_ad_id, $type="AD");          
            return $ad_code;  
            }   
          }        
        }
            
    }
    
    /**
     * We are displaying groups as per shortcode. eg [[adsforwp-group id="0000"]
     * @param type $atts
     * @return type string
     */
    public function adsforwp_group_ads($atts, $group_id = null, $widget=null) { 
                       
        if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
            $this->is_amp = true;        
        }        
        $post_group_id  =   $atts['id']; 
        if($group_id){
        $post_group_id  =   $group_id;     
        } 
        
        $placement_obj = new adsforwp_view_placement();
        $condition_status = $placement_obj->adsforwp_get_post_conditions_status($post_group_id);  
        
        $visitor_condition_obj = new adsforwp_view_visitor_condition();
        $visitor_condition_status = $visitor_condition_obj->adsforwp_visitor_conditions_status($post_group_id);
        
        if((($condition_status ===1 || $condition_status === true || $condition_status==='notset') && ($visitor_condition_status ===1 || $visitor_condition_status === true || $visitor_condition_status==='notset') ) || $widget =='widget'){
        if($this->visibility != 'hide') {
        
        $ad_alignment ='';     
        $ad_margin_top     = 0;
        $ad_margin_bottom  = 0;
        $ad_margin_left    = 0;
        $ad_margin_right   = 0;
        $wheretodisplay    = '';       
        $amp_compatibility = '';
        $adsforwp_non_amp_visibility='';
        
        $post_group_data   = get_post_meta($post_group_id,$key='adsforwp_ads',true);
        $post_group_meta   = get_post_meta($post_group_id,$key='',true);
        $margin_post_meta  = get_post_meta($post_group_id, $key='adsforwp_ad_margin',true);
        
        if(isset($margin_post_meta['ad_margin_top'])){
        $ad_margin_top     = $margin_post_meta['ad_margin_top'];    
        }
        if(isset($margin_post_meta['ad_margin_bottom'])){
         $ad_margin_bottom  = $margin_post_meta['ad_margin_bottom'];  
        }
        if(isset($margin_post_meta['ad_margin_left'])){
        $ad_margin_left    = $margin_post_meta['ad_margin_left'];
        }
        if(isset($margin_post_meta['ad_margin_right'])){
        $ad_margin_right   = $margin_post_meta['ad_margin_right'];    
        }
        if(isset($post_group_meta['wheretodisplay'])){
            $wheretodisplay = $post_group_meta['wheretodisplay'][0];
        }
        if(isset($post_group_meta['ads_for_wp_non_amp_visibility'])){
        $adsforwp_non_amp_visibility = $post_group_meta['ads_for_wp_non_amp_visibility'][0];
        }
        if(array_key_exists('ads-for-wp_amp_compatibilty', $post_group_meta)){
        $amp_compatibility = $post_group_meta['ads-for-wp_amp_compatibilty'][0];              
        }        
        if($wheretodisplay !='ad_shortcode' && isset($post_group_meta['adsforwp_ad_align'])){
        $ad_alignment      = $post_group_meta['adsforwp_ad_align'][0];    
        }
            
           
        $ad_code ="";  
        $group_ad_code="";
        $filter_group_ids = array();    
        if($this->is_amp){            
        
        if($amp_compatibility != 'disable'){    
        if($post_group_data){
           foreach ($post_group_data as $group_id=>$value){
            if(get_post_status($group_id) == 'publish'){
              $filter_group_ids[$group_id] = $value; 
            }
         } 
        $ad_code =  $this->adsforwp_get_ad_code(array_rand($filter_group_ids), $type="GROUP");              
        }  
        
        }
        }else{            
        if($adsforwp_non_amp_visibility !='hide'){          
        $post_data = get_post_meta($post_group_id,$key='',true);                        
        if($post_group_data){
        $adsresultset = array();  
        $response = array();    
        
        foreach($post_group_data as $post_ad_id => $post){
        $ad_detail = get_post_meta($post_ad_id,$key='',true);  
        $select_ad_type = '';
        $data_cid = '';
        $data_crid = '';
        if(isset($ad_detail['select_adtype'])){
         $select_ad_type = $ad_detail['select_adtype'][0];   
        }        
        if(isset($ad_detail['data_cid'])){
         $data_cid = $ad_detail['data_cid'][0];   
        }
        if(isset($ad_detail['data_crid'])){
         $data_crid = $ad_detail['data_crid'][0];   
        }        
        if(!empty($ad_detail) && $select_ad_type !='' && get_post_status($post_ad_id) == 'publish'){
        $adsresultset[] = array(
                'ad_id' => $post_ad_id,
                'ad_type' => $ad_detail['select_adtype'][0],
                'ad_adsense_type' => $ad_detail['adsense_type'][0],
                'ad_custom_code' => $ad_detail['custom_code'][0],
                'ad_data_client_id' => $ad_detail['data_client_id'][0],
                'ad_data_ad_slot' => $ad_detail['data_ad_slot'][0],
                'ad_data_cid' => $data_cid,
                'ad_data_crid' => $data_crid,
                'ad_banner_size' => $ad_detail['banner_size'][0],
                'ad_image' => $ad_detail['adsforwp_ad_image'][0],
                'ad_redirect_url' => $ad_detail['adsforwp_ad_redirect_url'][0],
                'ad_img_height' => $ad_detail['adsforwp_ad_img_height'][0],
                'ad_img_width' => $ad_detail['adsforwp_ad_img_width'][0],                
        ) ; 
        }
        
        }
        $response['afw_group_id'] = $post_group_id;
        $response['adsforwp_refresh_type'] = $post_data['adsforwp_refresh_type'][0];
        if(isset($post_data['adsforwp_group_ref_interval_sec'])){
        $response['adsforwp_group_ref_interval_sec'] = $post_data['adsforwp_group_ref_interval_sec'][0];    
        }        
        $response['adsforwp_group_type'] = $post_data['adsforwp_group_type'][0];
        $response['ads'] = $adsresultset;  
        if($response['adsforwp_refresh_type'] == 'on_interval'){
                
        $ad_code ='<div class="afw-groups-ads-json" afw-group-id="'.esc_attr($post_group_id).'" data-json="'. esc_attr(json_encode($response)).'">';           
        $ad_code .='</div>';
        $ad_code .='<div style="display:none;" data-id="'.esc_attr($post_group_id).'" class="afw_ad_container_pre"></div><div data-id="'.esc_attr($post_group_id).'" class="afw afw_ad_container"></div>';
        
        
        }else{
                
        $post_group_data = get_post_meta($post_group_id,$key='adsforwp_ads',true);  
        
        foreach ($post_group_data as $group_id=>$value){
            if(get_post_status($group_id) == 'publish'){
              $filter_group_ids[$group_id] = $value; 
            }
        }       
        $ad_code =  $this->adsforwp_get_ad_code(array_rand($filter_group_ids), $type="GROUP"); 
        
        
        }        
        }
        }                           
       } 
                                      
       $group_ad_code = '<div data-id="'.esc_attr($post_group_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_group afw_group afwadgroupid-'.esc_attr($post_group_id).'">';
       $group_ad_code .= $ad_code;
       $group_ad_code .='</div>';       
              
       return $group_ad_code;
       }     
        }    
               
    }

    /**
     * This is a ajax handler function for ads groups. 
     * @return type json string
     */
    public function adsforwp_get_groups_ad(){  
        
        $ad_id = sanitize_text_field($_GET['ad_id']);        
        $ads_group_id = sanitize_text_field($_GET['ads_group_id']);
        $ads_group_type = sanitize_text_field($_GET['ads_group_type']);
        $ads_group_data = get_post_meta($ads_group_id,$key='adsforwp_ads',true);
        switch ($ads_group_type) {
            case 'rand':
            $ad_code =  $this->adsforwp_get_ad_code(array_rand($ads_group_data), $type="GROUP");
                break;            
            case 'ordered':                
            $ad_code =  $this->adsforwp_get_ad_code($ad_id, $type="GROUP");    
                break;
            
            default:
                break;
        }                
        if($ad_code){
        echo json_encode(array('status'=> 't','ad_code'=> $ad_code));        
        }else{
        echo json_encode(array('status'=> 'f','ad_code'=> 'group code not available'));                                 
        }
        
           wp_die();           
}
    public function adsforwp_adblocker_detector(){
        ?>
        <script type="text/javascript">              
              jQuery(document).ready( function($) {    
                  if ($('#adsforwp-hidden-block').length == 0 ) {
                       $.getScript("<?php echo site_url().'/'.'front.js' ?>");
                  }
                 
              });
         </script>
       <?php
    }
}
if (class_exists('adsforwp_output_functions')) {
	$adsforwp_function_obj = new adsforwp_output_functions;
        $adsforwp_function_obj->adsforwp_hooks();
}
