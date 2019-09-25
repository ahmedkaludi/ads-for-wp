<?php
/**
 * This class handle all the user end related functions
 */
class adsforwp_output_functions{
    
    private $_amp_conditions   = array();
    private $_display_tag_list = array();
    private $is_amp            = false;     
    public  $visibility        = null;
    public  $amp_ads_id        = array();
    
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
                    '</p>'    => 'p_tag',
                    '</div>'  => 'div_tag',
                    '<img>'   => 'img_tag',                    
                    );
                          
    }
    /**
     * We are here calling all required hooks
     */    
    public function adsforwp_hooks(){
        
         if(!is_admin()){            
             
             if ((function_exists( 'ampforwp_is_amp_endpoint' )) || function_exists( 'is_amp_endpoint' )) {
                       add_action( 'amp_init', array( $this, 'init' ) );             
             }else{
                       add_action( 'init', array( $this, 'init' ) );
             }
                                       
         }         
        //Adsense Auto Ads hooks for amp and non amp starts here       
        add_filter('widget_text', 'do_shortcode');    
        
        add_action('wp_head', array($this, 'adsforwp_ezoic_ads_script'),10);
        add_action('wp_head', array($this, 'adsforwp_mediavines_ads_script'));

        add_action('wp_head', array($this, 'adsforwp_taboola_ads_script'),10);
        add_action('wp_head', array($this, 'adsforwp_outbrain_script'),10);

        add_action('wp_head', array($this, 'adsforwp_adblocker_detector'));
        add_action('wp_head', array($this, 'adsforwp_adsense_auto_ads'));
        add_action('wp_head', array($this, 'adsforwp_doubleclick_head_code'));
        
        //Sticky Adsense Ads
        add_action('amp_post_template_footer',array($this, 'adsforwp_insert_sticky_ads_code'), 12);
        
        //Background Ad        
                
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
        add_action('wp_footer', array($this, 'adsforwp_taboola_footer_loader_js'));
        add_filter('amp_post_template_data',array($this, 'adsforwp_enque_ads_specific_amp_script'));         
        add_action('amp_post_template_css',array($this, 'adsforwp_add_amp_stick_ad_css'));        
        add_action('amp_post_template_css',array($this, 'adsforwp_global_css_for_amp'));
        add_action('amp_post_template_footer',array($this, 'adsforwp_display_sticky_ads_amp'),9);
        
        add_action('wp_ajax_nopriv_adsforwp_update_amp_sticky_ad_status', array($this, 'adsforwp_update_amp_sticky_ad_status'));
        add_action('wp_ajax_nopriv_adsforwp_check_amp_sticky_ad_status', array($this, 'adsforwp_check_amp_sticky_ad_status'));
         
        add_action('wp_ajax_adsforwp_update_amp_sticky_ad_status', array($this, 'adsforwp_update_amp_sticky_ad_status'));
        add_action('wp_ajax_adsforwp_check_amp_sticky_ad_status', array($this, 'adsforwp_check_amp_sticky_ad_status'));
        
        add_action('amp_post_template_css',array($this, 'adsforwp_background_ad_css'));
                
    }
    
    public function init(){
        
            set_transient('adsforwp_transient_amp_ids', '');
                                                                              
            ob_start(array($this, "adsforwp_display_custom_target_ad"));
            
            ob_start(array($this, "adsforwp_display_background_ad"));       
        
    }
    /**
     * This function is used to show ads based on html element target by user 
     * @param type $content
     * @return type string
     */
    
    public function adsforwp_get_custom_target_ad_code($content, $ad_id, $ad_type){
                        
                    if($ad_type == 'group'){                     
                      $ad_code =  $this->$this->adsforwp_group_ads($atts=null, $ad_id, '');   
                    }

                    if($ad_type == 'ad'){
                      $ad_code   =  $this->adsforwp_get_ad_code($ad_id, $type="AD");        
                    }
                                               
                   $post_meta = get_post_meta($ad_id,$key='',true);
                  
                   if(adsforwp_rmv_warnings($post_meta, 'adsforwp_custom_target_position', 'adsforwp_array') == 'existing_element'){
                       
                                $action          = adsforwp_rmv_warnings($post_meta, 'adsforwp_existing_element_action', 'adsforwp_array');
                                $jquery_selector = adsforwp_rmv_warnings($post_meta, 'adsforwp_jquery_selector', 'adsforwp_array');
                                
                                if($jquery_selector){
                                 
                                if(strchr($jquery_selector, '#')){
                                  $jquery_selector = str_replace('#', '', $jquery_selector);                                                                                                                                  
                                  preg_match_all('/<[^>]*id="[^"]*\b'.$jquery_selector.'\b[^"]*"[^>]*>/', $content, $matches);
                                  $split = preg_split('/<[^>]*id="[^"]*\b'.$jquery_selector.'\b[^"]*"[^>]*>/', $content);
                                }
                                if(strchr($jquery_selector, '.')){
                                  $jquery_selector = str_replace('.', '', $jquery_selector); 
                                  preg_match_all('/<[^>]*class="[^"]*\b'.$jquery_selector.'\b[^"]*"[^>]*>/', $content, $matches);
                                  $split = preg_split('/<[^>]*class="[^"]*\b'.$jquery_selector.'\b[^"]*"[^>]*>/', $content);
                                }   
                                                             
                                if(is_array($split) && !empty($split)){
                                    
                                    $all_matches = $split;
                                    
                                    foreach($all_matches as $key => $match){
                                            
                                        if(isset($matches[0][$key])){
                                            
                                            if($action == 'prepend_content'){
                                                $all_matches[$key] = $match.$ad_code.$matches[0][$key];
                                            }
                                        
                                            if($action == 'append_content'){
                                                $all_matches[$key] = $match.$matches[0][$key].$ad_code;
                                            }
                                            
                                        }
                                                                                                                                                                                                                                                                                                                                                                                                                                           
                                    }
                                                                                                         
                                   $content = implode( '', $all_matches );    
                                }
                                    
                           }                                
                                             
                   }
                   
                   if(adsforwp_rmv_warnings($post_meta, 'adsforwp_custom_target_position', 'adsforwp_array') == 'new_element'){
                       
                       $new_element_div = html_entity_decode(adsforwp_rmv_warnings($post_meta, 'adsforwp_new_element', 'adsforwp_array'));                                              
                       $content = str_replace($new_element_div, $ad_code, $content);
                       
                   }
                      
        return $content;
        
    }
    public function adsforwp_display_custom_target_ad($content){       
                                 
                 //For single ad starts here
                 $all_ads_id = adsforwp_get_ad_ids();
                 $service = new adsforwp_output_service();
                 
                 if(!empty($all_ads_id)){
                     
                   foreach($all_ads_id as $ad_id){                     
                     
                   $wheretodisplay = get_post_meta($ad_id,$key='wheretodisplay',true);   
                   
                   if($wheretodisplay == 'custom_target'){                                                          
                  
                       $ad_status = $service->adsforwp_is_condition($ad_id);
                       
                       if($ad_status){
                           $content = $this->adsforwp_get_custom_target_ad_code($content, $ad_id, 'ad');
                       }
                                                                                                                                                   
                    }
                  }                        
                 }
                   //For group ads                                                
                 $all_ads_id = adsforwp_get_group_ad_ids();    
                 
                 if(!empty($all_ads_id)){
                     
                   foreach($all_ads_id as $ad_id){                     
                     
                   $wheretodisplay = get_post_meta($ad_id,$key='wheretodisplay',true);   
                   
                   if($wheretodisplay == 'custom_target'){
                       
                       $ad_status = $service->adsforwp_is_condition($ad_id);
                       
                       if($ad_status){
                           $content = $this->adsforwp_get_custom_target_ad_code($content, $ad_id, 'group');                                                                              
                       }
                       
                   }
                 }                        
                 }                 
                 //For group ads ends here                                  
                 return $content;
    }      
    /**
     * Function to add css globally on AMP
     */
    public function adsforwp_global_css_for_amp(){
         ?>
           ins{
                background: yellow;
            }
            .afw a {
               display:block;
            }
         <?php 
    }       
    /**
     * Function to add css for sticky ads on AMP
     */
    public function adsforwp_add_amp_stick_ad_css(){
                
        $all_ads_id = adsforwp_get_ad_ids();
                
        if(!empty($all_ads_id)){
            
        foreach($all_ads_id as $ad_id){     
            
            $wheretodisplay = get_post_meta($ad_id,$key = 'wheretodisplay',true);  
            
             if($wheretodisplay == 'sticky'){  
                 
               $ad_code =  $this->adsforwp_get_ad_code($ad_id, $type="AD"); 
               
               if($ad_code){
                   
                 $output_service = new adsforwp_output_service();                      
                 $output_service->adsforwp_enque_amp_sticky_ad_css($ad_id);
                                      
               }
               
             }
             
            }
        }
        
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
                 $cookie_data .= esc_attr($ad_id);                     
        } else {
                 $cookie_data .= ','.esc_attr($ad_id);               
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
        $all_ads_id = adsforwp_get_ad_ids();
                
        if(!empty($all_ads_id)){
            
        foreach($all_ads_id as $ad_id){     
            
            $wheretodisplay = get_post_meta($ad_id,$key = 'wheretodisplay',true);  
            
             if($wheretodisplay == 'sticky'){  
                 
               $ad_code =  $this->adsforwp_get_ad_code($ad_id, $type="AD"); 
               
               if($ad_code){
                                                                 
                echo '<amp-user-notification
                        layout="nodisplay"
                        id="amp-user-notification_'.esc_attr($ad_id).'" class="afw_ad_amp_'.$ad_id.'">                          
                        <div class="adsforwp-stick-ad">'.$ad_code.'</div>  
                        <button on="tap:amp-user-notification_'.esc_attr($ad_id).'.dismiss" class="adsforwp-sticky-ad-close"></button>
                     </amp-user-notification>';
               }
               
             }
             
            }
        }
        //Ads stick ends here
        
        //Group stick starts here
        
        $all_group_post = adsforwp_get_group_ad_ids();          
        
        if(!empty($all_group_post)){
            
        foreach($all_group_post as $ad_id){   
            
            $wheretodisplay = get_post_meta($ad_id,$key='wheretodisplay',true); 
            
             if($wheretodisplay == 'sticky'){  
                                
               $ad_code =  $this->adsforwp_group_ads($atts=null, $ad_id); 
               
               if($ad_code){
                                                   
                echo '<amp-user-notification
                        layout="nodisplay"
                        id="amp-user-notification_'.esc_attr($ad_id).'" class="afw_ad_amp_'.$ad_id.'">                       
                        <div class="adsforwp-stick-ad">'.$ad_code.'</div>                      
                        <button on="tap:amp-user-notification_'.esc_attr($ad_id).'.dismiss" class="adsforwp-sticky-ad-close"></button>
                     </amp-user-notification>';
               }
               
             }
             
            }
        }                
        //Group stick ends here
        
    }    
    public function adsforwp_taboola_footer_loader_js(){
      $all_ads_id    = adsforwp_get_ad_ids();
        if($all_ads_id){
          foreach($all_ads_id as $ad_id){
                $post_meta_dataset = array();                      
                $post_meta_dataset = get_post_meta($ad_id,$key='',true);
                $post_type = get_post_meta( $ad_id, 'select_adtype', true );
                $publisher_id   = adsforwp_rmv_warnings($post_meta_dataset, 'taboola_publisher_id', 'adsforwp_array'); 
              if($post_type == 'taboola' && !empty($publisher_id)){ ?>
            <script type='text/javascript'>
              window._taboola = window._taboola || [];
              _taboola.push({flush: true});
            </script>
      <?php  }
          }
        }
    }
    
    public function adsforwp_display_sticky_ads(){                
        
        $explod_ad_id = array();
        
        if(isset($_COOKIE['adsforwp-stick-ad-id7'])){
            
        $ad_id_list = $_COOKIE['adsforwp-stick-ad-id7'];
        $explod_ad_id = explode(',', $ad_id_list);    
        
        }  
        
        $common_function_obj = new adsforwp_admin_common_functions();
        
         //Ads Sticky starts here
        $ad_code    ='';
        $all_ads_id = adsforwp_get_ad_ids();
                        
        if(!empty($all_ads_id)){
            
        foreach($all_ads_id as $ad_id){
              
             $wheretodisplay = get_post_meta($ad_id,$key='wheretodisplay',true); 
            
             if($wheretodisplay == 'sticky' && !in_array($ad_id, $explod_ad_id)){  
                 
              $in_group       = $common_function_obj->adsforwp_check_ads_in_group($ad_id); 
                 
              
              if(empty($in_group)){
                  $ad_code .=  $this->adsforwp_get_ad_code($ad_id, $type="AD");   
              }
              
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
        $all_group_post = array();
        $group_ad_code  = '';
        $all_group_post = adsforwp_get_group_ad_ids();
        
        if(!empty($all_group_post)){
            
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
                              
                    $ad_revenue_sharing         = '';
                    $ad_owner_revenue_per       = '';
                    $ad_author_revenue_per      = '';  
                    $display_per_in_minute      = '';
                    $author_adsense_ids         = array();
                    
                    $settings                   = adsforwp_defaultSettings();
                    
                    if(is_array($settings) && array_key_exists('ad_revenue_sharing', $settings)){
                        
                    $ad_revenue_sharing         = adsforwp_rmv_warnings($settings, 'ad_revenue_sharing', 'adsforwp_string');  
                    $ad_owner_revenue_per       = adsforwp_rmv_warnings($settings, 'ad_owner_revenue_per', 'adsforwp_string');
                    $ad_author_revenue_per      = adsforwp_rmv_warnings($settings, 'ad_author_revenue_per', 'adsforwp_string');
                    $display_per_in_minute      = (60*$ad_owner_revenue_per)/100;
                    
                    }                    
                    $current_second = date("s"); 
                    
                    if((!($current_second <= $display_per_in_minute)) && isset($settings['ad_revenue_sharing'])){
                        
                     $author_adsense_ids['author_pub_id']     =  get_the_author_meta( 'adsense_pub_id' );                     
                     $author_adsense_ids['author_ad_slot_id'] =  get_the_author_meta( 'adsense_ad_slot_id' );                     
                     
                    }                           
                    return $author_adsense_ids;                    
    }
    
    public function adsforwp_get_adsense_publisher_id(){     
        
                    $data_ad_client ='';
                    $response = array();
                    $cc_args  = array(
                        'posts_per_page'   => -1,
                        'post_type'        => 'adsforwp',
                        'meta_key'         => 'adsense_type',
                        'meta_value'       => 'adsense_auto_ads',
                    );
                    
                    $postdata = new WP_Query($cc_args);  
                    $auto_adsense_post = $postdata->posts; 
                    
                    if($postdata->post_count >0){                   
                    
                    $data_ad_client     = get_post_meta($auto_adsense_post[0]->ID,$key='data_client_id',true); 
                    $author_adsense_ids = $this->adsforwp_get_pub_id_on_revenue_percentage();
                    
                    if($author_adsense_ids){
                        
                        $author_pub_id = adsforwp_rmv_warnings($author_adsense_ids, 'author_pub_id', 'adsforwp_string');

                        if($author_pub_id){

                            $data_ad_client = $author_pub_id;     

                        }   
                    }   
                    
                        $response = array('post_id' => $auto_adsense_post[0]->ID, 'data_ad_client' => $data_ad_client);
                    }     
                    
                    return $response;
    }    
    /**
     * Function to display background
     * @param type $content
     * @return type string
     */
    public function adsforwp_display_background_ad($content){
                
          $all_ads_id = adsforwp_get_ad_ids(); 
         
          if($all_ads_id){
              
              $service = new adsforwp_output_service();
              
              
              foreach ($all_ads_id as $ad_id){
                  
                    $post_type = get_post_meta( $ad_id, 'select_adtype', true );  
                           
                    if($post_type == 'ad_background'){  
                                                                                   
                    $condition_status         = $service->adsforwp_is_condition($ad_id);                                          

                    if ($condition_status) {                       
                         
                      $after_body ='';
                      $media_value_meta = get_post_meta( $ad_id, 'ad_background_image_detail', true );  
                                                                                                          
                     if(isset($media_value_meta)){   
                         
                        $redirect_url = get_post_meta( $ad_id, 'ad_background_redirect_url', true );                          
                        $after_body.=''
                                    . '<div class="adsforwp-bg-wrapper">
                                       <a style="background-image: url('.esc_url($media_value_meta['thumbnail']).')" class="adsforwp-bg-ad" target="_blank" href="'.esc_url($redirect_url).'">'
                                    . '</a>'                               
                                    . '<div class="adsforwp-bg-content">';   
                        $before_body = '</div></div>';
                        
                     }                        
                        $content = preg_replace("/(\<body.*\>)/", "$1".$after_body, $content);
                        $content = preg_replace("/(\<\/body.*\>)/", $before_body."$1", $content);
                        
                       break;
                     
                     
                    }
                                    
               }
              
              }                                      
          
          }
          
          return $content; 
        
    }   
    /**
     * Function to add background ad css in Non AMP
     */
    public function adsforwp_background_ad_css(){
        
          $all_ads_id = adsforwp_get_ad_ids(); 
          
          if($all_ads_id){
              
              $service = new adsforwp_output_service();
                            
              foreach ($all_ads_id as $ad_id){
                  
                    $post_type = get_post_meta( $ad_id, 'select_adtype', true );  
                           
                    if($post_type == 'ad_background'){                  
                                            
                    $condition_status         = $service->adsforwp_is_condition($ad_id);                      
                    
                    if ($condition_status) {      
                                                                                              
                            $media_value_meta = get_post_meta( $ad_id, 'ad_background_image_detail', true );                             
                       
                            if(isset($media_value_meta)){
                                ?>
                               .adsforwp-bg-ad{                             
                                  position: fixed;
                                  top: 0;
                                  left: 0;
                                  height: 100%;
                                  width: 100%;
                                  background-position: center;
                                  background-repeat: no-repeat; 
                                  background-size: cover;
                               }
                              .adsforwp-bg-content{
                                  z-index:1;
                                  margin: auto;
                                  position: absolute;
                                  top: 0; 
                                  left: 0; 
                                  bottom: 0; 
                                  right: 0;
                               }
                               .h_m{
                                 z-index: 1;
                                 position: relative;
                               }
                               .content-wrapper{
                                   position: relative;
                                   z-index: 0;
                                   margin: 0 16%
                               }
                               .cntr, .amp-wp-article{
                                  background:#ffffff;
                               }
                               .footer{
                                  background:#ffffff;
                               }
                              @media(max-width:768px){
                                 .adsforwp-bg-ad{
                                   position:relative;
                                 }
                                 .content-wrapper{
                                   margin:0;
                                 }
                               }
                            <?php

                            }
                            break; 
                                               
                    }                                    
                 }
              }                                      
          
          }
        
    }        
    /**
     * we are here enqueying adsense auto ads script for amp posts
     */
    public function adsforwp_adsense_auto_ads_amp_script(){
        
          $result = $this->adsforwp_get_adsense_publisher_id(); 
          
          if($result){
              
          $post_id                  = adsforwp_rmv_warnings($result, 'post_id', 'adsforwp_string');   
          
          $service = new adsforwp_output_service();
          $ad_status = $service->adsforwp_is_condition($post_id);
          
            if($ad_status){

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
            $content =  '<amp-auto-ads class="amp-auto-ads afw_'.esc_attr($post_id).'"
                                type="adsense"
                                data-ad-client="'.esc_attr(adsforwp_rmv_warnings($result, 'data_ad_client', 'adsforwp_string')).'">
                            </amp-auto-ads>';
             
            $service = new adsforwp_output_service();
            $ad_status = $service->adsforwp_is_condition($post_id);
            
            if($ad_status){
                echo $content;
            }
            
            }            
    }
    /**
     * we are here integrating adsense auto ads for ever non amp posts
     */
    public function adsforwp_adsense_auto_ads(){
            
            $result = $this->adsforwp_get_adsense_publisher_id();             
            if($result){
                
            $post_id = adsforwp_rmv_warnings($result, 'post_id', 'adsforwp_string'); 
            
            $content = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                  <script>
                  (adsbygoogle = window.adsbygoogle || []).push({
                  google_ad_client: "'.esc_attr(adsforwp_rmv_warnings($result, 'data_ad_client', 'adsforwp_string')).'",
                  enable_page_level_ads: true
                  }); 
                 </script>';            
            
            $service = new adsforwp_output_service();
            $ad_status = $service->adsforwp_is_condition($post_id);
            
            if($ad_status){
                echo $content;
            }
            
            }
           
    }   
    public function adsforwp_enque_ads_specific_amp_script($data){
        $all_ads_id    = adsforwp_get_ad_ids();
        if($all_ads_id){
            foreach($all_ads_id as $ad_id){
                $post_type = get_post_meta( $ad_id, 'select_adtype', true );
                $post_meta_dataset = array();                      
                $post_meta_dataset = get_post_meta($ad_id,$key='',true);
                $adsense_type = adsforwp_rmv_warnings($post_meta_dataset, 'adsense_type', 'adsforwp_array');
                $outbrain_type   = adsforwp_rmv_warnings($post_meta_dataset, 'outbrain_type', 'adsforwp_array');
                if(( $post_type == 'adsense' && $adsense_type == 'adsense_sticky_ads') || ($post_type == 'outbrain' && $outbrain_type == 'outbrain_sticky_ads') ){
                    $service = new adsforwp_output_service();
                    $ad_status = $service->adsforwp_is_condition($ad_id);
                    if($ad_status){
                        if ( empty( $data['amp_component_scripts']['amp-sticky-ad'] ) ) {
                            $data['amp_component_scripts']['amp-sticky-ad'] = 'https://cdn.ampproject.org/v0/amp-sticky-ad-latest.js';
                        }
                    }
                }
            }
        }
        return $data;         
    }
    public function adsforwp_insert_sticky_ads_code(){                                   
                   
            $all_ads_id    = adsforwp_get_ad_ids(); 
                                   
            if($all_ads_id){
            
                foreach($all_ads_id as $ad_id){
                    
                    $post_type = get_post_meta( $ad_id, 'select_adtype', true );                    
                           
                    if($post_type == 'adsense'){
                        
                        $post_meta_dataset          = array();                      
                        $post_meta_dataset          = get_post_meta($ad_id,$key='',true);
                        
                        $adsense_type               = adsforwp_rmv_warnings($post_meta_dataset, 'adsense_type', 'adsforwp_array');
                        
                        
                        if($adsense_type == 'adsense_sticky_ads'){
                            
                         $ad_slot     = adsforwp_rmv_warnings($post_meta_dataset, 'data_ad_slot', 'adsforwp_array');                           
                         $ad_client   = adsforwp_rmv_warnings($post_meta_dataset, 'data_client_id', 'adsforwp_array'); 
                        
                         $width  = '200';
                         $height = '200';
                         
                         $banner_size = adsforwp_rmv_warnings($post_meta_dataset, 'banner_size', 'adsforwp_array');  
                         
                         if($banner_size !=''){
                
                            $explode_size = explode('x', $banner_size);            
                            $width        = adsforwp_rmv_warnings($explode_size, 0, 'adsforwp_string');            
                            $height       = adsforwp_rmv_warnings($explode_size, 1, 'adsforwp_string');                               

                         }
                         
                         $service = new adsforwp_output_service();
                         $ad_status = $service->adsforwp_is_condition($ad_id);
                        
                            if($ad_status){
                            
                               if($ad_client && $ad_slot){
                                 
                                    $output  = '<amp-sticky-ad layout="nodisplay">';
                                    $output .= '<amp-ad class="amp-sticky-ads afw_'.esc_attr($ad_id).'"
                                                     type="adsense"
                                                     width='. esc_attr($width) .'
                                                     height='. esc_attr($height) . '
                                                     data-ad-client="'. esc_attr($ad_client) .'"
                                                     data-ad-slot="'.  esc_attr($ad_slot) .'"
                                                     data-enable-refresh="10">';
                                    $output	.=	'</amp-ad>';
                                    $output	.= '</amp-sticky-ad>';
                                    echo $output;
                                    break;
                               
                               }                                
                               
                              }
                                                                                                                                                                                                                                                                                                                                                
                        }                                                
                        
                      }
                          
                    if($post_type == 'outbrain'){
                      $post_meta_dataset          = array();
                      $post_meta_dataset          = get_post_meta($ad_id,$key='',true);
                        $outbrain_type   = adsforwp_rmv_warnings($post_meta_dataset, 'outbrain_type', 'adsforwp_array');
                        if($outbrain_type == 'outbrain_sticky_ads'){
                          $width  = '200';
                          $height = '200';
                          $banner_size = adsforwp_rmv_warnings($post_meta_dataset, 'banner_size', 'adsforwp_array'); 
                          if($banner_size !=''){
                
                            $explode_size = explode('x', $banner_size);            
                            $width        = adsforwp_rmv_warnings($explode_size, 0, 'adsforwp_string');            
                            $height       = adsforwp_rmv_warnings($explode_size, 1, 'adsforwp_string');                               

                          }
                          $service = new adsforwp_output_service();
                          $ad_status = $service->adsforwp_is_condition($ad_id);
                          $outbrain_widget_ids     = adsforwp_rmv_warnings($post_meta_dataset, 'outbrain_widget_ids', 'adsforwp_array');
                          if($ad_status){
                            if(!empty($outbrain_widget_ids)){
                                $output  = '<amp-sticky-ad layout="nodisplay">';
                                    $output .= '<amp-ad class="amp-sticky-ads afw_'.esc_attr($ad_id).'"
                                                  type="outbrain"
                                                  width='. esc_attr($width) .'
                                                  height='. esc_attr($height) . '
                                                  data-widgetids='.esc_attr($outbrain_widget_ids).'
                                                  data-enable-refresh="10">';
                                $output .=  '</amp-ad>';
                                $output .= '</amp-sticky-ad>';
                                echo $output;
                                break;
                            }
                          }
                        }
                    }
                      
                               
                }                                                       
            }                                                    
        
    }     
    
    public function adsforwp_doubleclick_head_code(){
                            
            $data_slot  = '';                   
            $all_ads_id = adsforwp_get_ad_ids(); 
                                  
            if($all_ads_id){
            
                foreach($all_ads_id as $ad_id){
                    
                    $post_type = get_post_meta( $ad_id, 'select_adtype', true );  
                    $ad_div_gpt = $ad_slot_id = $height = $width ='';
                    if($post_type == 'doubleclick'){
                                                                        
                        $post_meta_dataset          = array();                      
                        $post_meta_dataset          = get_post_meta($ad_id,$key='',true);
                        
                        $ad_slot_id  = adsforwp_rmv_warnings($post_meta_dataset, 'dfp_slot_id', 'adsforwp_array');                           
                        $ad_div_gpt  = adsforwp_rmv_warnings($post_meta_dataset, 'dfp_div_gpt_ad', 'adsforwp_array'); 
                        
                         $width  = '200';
                         $height = '200';
                         $banner_size = adsforwp_rmv_warnings($post_meta_dataset, 'banner_size', 'adsforwp_array');  
                         
                         if($banner_size !=''){
                
                                $explode_size = explode('x', $banner_size);            
                                $width        = adsforwp_rmv_warnings($explode_size, 0, 'adsforwp_string');            
                                $height       = adsforwp_rmv_warnings($explode_size, 1, 'adsforwp_string');                               

                             }
                                                                                                                                                                                                                                               
                                                                                                
                            }

                            $service = new adsforwp_output_service();
                            $ad_status = $service->adsforwp_is_condition($ad_id);

                            if($ad_status){
                                $data_slot .="googletag.defineSlot('".esc_attr($ad_slot_id)."', [".esc_attr($width).", ".esc_attr($height)."], '".esc_attr($ad_div_gpt)."').addService(googletag.pubads());";
                            }   

                            }

                            if( $data_slot !=''){
                                
                             echo "<script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>
                                   <script>
                                    var googletag = googletag || {};
                                    googletag.cmd = googletag.cmd || [];
                                  </script>

                                  <script>
                                    googletag.cmd.push(function() {                                                   
                                      ".$data_slot."  
                                      googletag.pubads().enableSingleRequest();
                                      googletag.enableServices();
                                    });
                                  </script>";   
                                
                            }                            

            }                                                    
        
    }    
    /**
     * This hook function display content in post. we are modifying post content here
     * @param type $content
     * @return type string
     */
    public function adsforwp_display_ads($content){       
                                                                            
            if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
                $this->is_amp = true;        
            }         
                  
            //Ads positioning starts here
            $all_ads_post = adsforwp_get_ad_ids();                 
            $service = new adsforwp_output_service();
            
            if($all_ads_post){
                
            foreach($all_ads_post as $ads){   
                
            $post_ad_id          = $ads;      
                                    
            $ad_status = $service->adsforwp_is_condition($post_ad_id);
            
            if($ad_status){
                
            $common_function_obj = new adsforwp_admin_common_functions();
            $in_group            = $common_function_obj->adsforwp_check_ads_in_group($post_ad_id);
           
            if(empty($in_group)){            
                                        
            $where_to_display   = ""; 
            $adposition         = "";    
            $post_meta_dataset  = array();
            $post_meta_dataset  = get_post_meta($post_ad_id,$key='',true);
            $ad_code            = $this->adsforwp_get_ad_code($post_ad_id, $type="AD");
                                    
            $where_to_display   = adsforwp_rmv_warnings($post_meta_dataset, 'wheretodisplay', 'adsforwp_array');                          
            $adposition         = adsforwp_rmv_warnings($post_meta_dataset, 'adposition', 'adsforwp_array');    
            
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
                
                $entered_tag_name     = '';                
                $display_tag_name     = '';  
                $every_paragraphs     = 0;
                
                $every_paragraphs    = adsforwp_rmv_warnings($post_meta_dataset, 'ads_on_every_paragraphs_number', 'adsforwp_array');
                
                $paragraph_id        = adsforwp_rmv_warnings($post_meta_dataset, 'paragraph_number', 'adsforwp_array');   
                                                
                $display_tag_name    = adsforwp_rmv_warnings($post_meta_dataset, 'display_tag_name', 'adsforwp_array');                                  
                
                $entered_tag_name    = '</'.adsforwp_rmv_warnings($post_meta_dataset, 'entered_tag_name', 'adsforwp_array').'>';                  
                               
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
                                        
                    if($this->is_amp){
                        
                        preg_match_all( '/<amp-img[^>]+\>/' , $content, $match );
                        
                    }else{
                        
                        preg_match_all( '/<img[^>]+\>/' , $content, $match );
                        
                    }
                                                    
                    $paragraphs       = array_pop($match);                      
                    $p_number         = $paragraph_id;  
                   
                    foreach ($paragraphs as $index => $paragraph) {
                                                                     
                       if($every_paragraphs == 1){
                                                                                
                           if ( $paragraph_id == $index + 1 ) {
                                $paragraphs[$index] .= $ad_code;
                                $paragraph_id += $p_number;
                           }
                           
                       }else{
                           
                            if ( $paragraph_id == $index + 1 ) {
                                $paragraphs[$index] .= $ad_code;
                            }
                           
                       }
                       
                     }
                     
                  $content = implode( '', $paragraphs );  
                                     
                }
                               
                }else{
                    
                   $paragraphs       = explode( $closing_p, $content );                   
                   $p_number         = $paragraph_id;  
                   
                    foreach ($paragraphs as $index => $paragraph) {

                       if ( trim( $paragraph ) ) {
                           $paragraphs[$index] .= $closing_p;
                       }
                                                                     
                       if($every_paragraphs == 1){
                                                                                
                           if ( $paragraph_id == $index + 1 ) {
                                $paragraphs[$index] .= $ad_code;
                                $paragraph_id += $p_number;
                           }
                           
                       }else{
                           
                            if ( $paragraph_id == $index + 1 ) {
                                $paragraphs[$index] .= $ad_code;
                            }
                           
                       }
                       
                     }
                     
                  $content = implode( '', $paragraphs );   
                }                                               
                }
        
              if($adposition == '50_of_the_content'){
                   
                 $closing_p        = '</p>';
                 $paragraphs       = explode( $closing_p, $content );       
                 $total_paragraphs = count($paragraphs);
                 $paragraph_id     = round($total_paragraphs /2);  
                 
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
            }
            //Ads positioning ends here
            
            //Groups positioning starts here
            $all_group_post = adsforwp_get_group_ad_ids();
            
            if($all_group_post){
                
            foreach($all_group_post as $group){
                                                
            $post_group_id = $group;             
            
            $ad_status = $service->adsforwp_is_condition($post_group_id);
            
            if($ad_status){
            $where_to_display   = ''; 
            $adposition         = '';    
            $widget             = '';
            
            $post_meta_dataset = array();
            $post_meta_dataset = get_post_meta($post_group_id,$key='',true);
            
            $ad_code           = $this->adsforwp_group_ads($atts=null, $post_group_id, $widget);  
                                    
            $where_to_display  = adsforwp_rmv_warnings($post_meta_dataset, 'wheretodisplay', 'adsforwp_array');                          
            $adposition        = adsforwp_rmv_warnings($post_meta_dataset, 'adposition', 'adsforwp_array');    
            
                                                                                                                                             
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
                
                $entered_tag_name     = '';                
                $display_tag_name     = '';  
                $every_paragraphs     = 0;
                
                $every_paragraphs    = adsforwp_rmv_warnings($post_meta_dataset, 'ads_on_every_paragraphs_number', 'adsforwp_array');
                
                $paragraph_id        = adsforwp_rmv_warnings($post_meta_dataset, 'paragraph_number', 'adsforwp_array');   
                                                
                $display_tag_name    = adsforwp_rmv_warnings($post_meta_dataset, 'display_tag_name', 'adsforwp_array');                                  
                
                $entered_tag_name    = '</'.adsforwp_rmv_warnings($post_meta_dataset, 'entered_tag_name', 'adsforwp_array').'>';                  
                               
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
                                        
                    if($this->is_amp){
                        
                        preg_match_all( '/<amp-img[^>]+\>/' , $content, $match );
                        
                    }else{
                        
                        preg_match_all( '/<img[^>]+\>/' , $content, $match );
                        
                    }
                                                    
                    $paragraphs       = array_pop($match);                      
                    $p_number         = $paragraph_id;  
                   
                    foreach ($paragraphs as $index => $paragraph) {
                                                                     
                       if($every_paragraphs == 1){
                                                                                
                           if ( $paragraph_id == $index + 1 ) {
                                $paragraphs[$index] .= $ad_code;
                                $paragraph_id += $p_number;
                           }
                           
                       }else{
                           
                            if ( $paragraph_id == $index + 1 ) {
                                $paragraphs[$index] .= $ad_code;
                            }
                           
                       }
                       
                     }
                     
                  $content = implode( '', $paragraphs );  
                                     
                }
                               
                }else{
                    
                   $paragraphs       = explode( $closing_p, $content );                   
                   $p_number         = $paragraph_id;  
                   
                    foreach ($paragraphs as $index => $paragraph) {

                       if ( trim( $paragraph ) ) {
                           $paragraphs[$index] .= $closing_p;
                       }
                                                                     
                       if($every_paragraphs == 1){
                                                                                
                           if ( $paragraph_id == $index + 1 ) {
                                $paragraphs[$index] .= $ad_code;
                                $paragraph_id += $p_number;
                           }
                           
                       }else{
                           
                            if ( $paragraph_id == $index + 1 ) {
                                $paragraphs[$index] .= $ad_code;
                            }
                           
                       }
                       
                     }
                     
                  $content = implode( '', $paragraphs );   
                }                                               
                }
        
               if($adposition == '50_of_the_content'){
                   
                 $closing_p        = '</p>';
                 $paragraphs       = explode( $closing_p, $content );       
                 $total_paragraphs = count($paragraphs);
                 $paragraph_id     = round($total_paragraphs /2);       
                 
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
            //Groups positioning ends here
            
                
        return $content;    
    }    
    /**
     * we are generating html or amp code for ads which will be displayed in post content.
     * @param type $post_ad_id
     * @return string 
     */
    public function adsforwp_get_ad_code($post_ad_id, $type, $all_condition_status = null){
                        
            $settings                   = adsforwp_defaultSettings();
            
            $service = new adsforwp_output_service();
            $ad_status = $service->adsforwp_is_condition($post_ad_id);
                                
            if($ad_status || $type=='GROUP' || $all_condition_status){
                 
            if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
                
                $this->is_amp = true;   
            
            }
            
            $ad_image                   ='';
            $ad_redirect_url            ='';
            $ad_type                    ='';
            $ad_code                    ='';               
            
            $custom_ad_code             ='';
            $where_to_display           ='';                                                                                                             
            $ad_responsive              ='';
                        
            $ad_margin_top              = 0;
            $ad_margin_bottom           = 0;
            $ad_margin_left             = 0;
            $ad_margin_right            = 0;
            $ad_alignment               = '';
            $ad_text_wraparound         = '';
            $ad_text_wrap               = 'none';          
            $post_meta_dataset          = array();                      
            $post_meta_dataset          = get_post_meta($post_ad_id,$key='',true);   
            
            
            $where_to_display           = adsforwp_rmv_warnings($post_meta_dataset, 'wheretodisplay', 'adsforwp_array');  
            
            if($type =="AD"){
                
                $ad_margin_top          = '';
                $ad_margin_bottom       = '';
                $ad_margin_left         = '';
                $ad_margin_right        = '';

                $margin_post_meta       = get_post_meta($post_ad_id, $key='adsforwp_ad_margin',true);
            
                $ad_margin_top          = adsforwp_rmv_warnings($margin_post_meta, 'ad_margin_top', 'adsforwp_string');                                       
                $ad_margin_bottom       = adsforwp_rmv_warnings($margin_post_meta, 'ad_margin_bottom', 'adsforwp_string');                                      
                $ad_margin_left         = adsforwp_rmv_warnings($margin_post_meta, 'ad_margin_left', 'adsforwp_string');                                                  
                $ad_margin_right        = adsforwp_rmv_warnings($margin_post_meta, 'ad_margin_right', 'adsforwp_string');  
            
                if($where_to_display !='ad_shortcode'){

                    $ad_alignment            = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_ad_align', 'adsforwp_array');                

                }
            } 
            $ad_text_wraparound            = adsforwp_rmv_warnings($post_meta_dataset, 'ads_text_wrap', 'adsforwp_array');           
            $ad_responsive               = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_ad_responsive', 'adsforwp_array');                            
            $custom_ad_code              = adsforwp_rmv_warnings($post_meta_dataset, 'custom_code', 'adsforwp_array');                            
            $ad_image                    = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_ad_image', 'adsforwp_array');                            
            $ad_redirect_url             = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_ad_redirect_url', 'adsforwp_array');                                                  
            $ad_type                     = adsforwp_rmv_warnings($post_meta_dataset, 'select_adtype', 'adsforwp_array');                                                      
                                                
            $sponsership_label           = '';
            
            if(isset($settings['ad_sponsorship_label']) && isset($settings['ad_sponsorship_label_text']) && $settings['ad_sponsorship_label_text'] !=''){
                $sponsership_label = '<div style="text-align:'.$ad_alignment.';">'.$settings['ad_sponsorship_label_text'].'</div>';
            }
            if($ad_text_wraparound == 1 && $where_to_display == 'between_the_content'){
                if($ad_alignment == 'left'){
                  $ad_text_wrap = 'left';
                }elseif($ad_alignment == 'right'){
                  $ad_text_wrap = 'right';
                }
            }
                                    
            if($ad_type !=""){  
                                                                                 
            switch ($ad_type) {
              case 'mediavine':
                $width='300';
                $height='250';
                $banner_size = adsforwp_rmv_warnings($post_meta_dataset, 'banner_size', 'adsforwp_array');
                if($banner_size !=''){
                  $explode_size = explode('x', $banner_size);              
                  $width = $explode_size[0];            
                  $height = $explode_size[1];
                }
                $mediavine_site_id   = adsforwp_rmv_warnings($post_meta_dataset, 'mediavine_site_id', 'adsforwp_array');
                  if($this->is_amp){
                    $this->amp_ads_id[] = $post_ad_id;
                      if(!empty($mediavine_site_id) ){
                          $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                  '.$sponsership_label.'
                                   <div class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                                     <amp-ad class="afw_ad_amp_'.esc_attr($post_ad_id).'" width="'. esc_attr($width) .'"
                                          height="'. esc_attr($height) .'"
                                          type="mediavine"
                                          data-site="'.esc_attr($mediavine_site_id).'">
                                    </amp-ad>
                                  </div>
                                  </div>';
                      }
                  }
                  
              break;
              case 'taboola':
                   $publisher_id   = adsforwp_rmv_warnings($post_meta_dataset, 'taboola_publisher_id', 'adsforwp_array');
                   $post_slug = get_post_field( 'post_name', $post_ad_id );
                   $placement_id = $post_slug.'-'.$post_ad_id;
                    if($this->is_amp){
                        $this->amp_ads_id[] = $post_ad_id;
                        if(!empty($publisher_id) ){
                          $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                  '.$sponsership_label.'
                                   <div class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                                     <amp-embed class="afw_ad_amp_'.esc_attr($post_ad_id).'" width="100" height="283"
                                         type=taboola
                                         layout=responsive
                                         heights="(min-width:1907px) 39%, (min-width:1200px) 46%, (min-width:780px) 64%, (min-width:480px) 98%, (min-width:460px) 167%, 196%"
                                         data-publisher="'.esc_attr($publisher_id).'"
                                         data-mode="thumbnails-a"
                                         data-placement="'.esc_attr($placement_id).'"
                                         data-article="auto">
                                    </amp-embed>
                                  </div>
                                  </div>';
                        }
                
                   }else{
                      if( !empty($publisher_id) ){
                        
                        $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                  '.$sponsership_label.'
                                   <div id="'.esc_attr($placement_id).'" class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'"></div>
                                      <script type="text/javascript">
                                        window._taboola = window._taboola || [];
                                        _taboola.push({
                                        mode:"thumbnails-a", 
                                        container:"'.esc_attr($placement_id).'", 
                                        placement:"'.esc_attr($placement_id).'", 
                                        target_type: "mix"
                                        });
                                      </script>
                                   
                              </div>';
                      }

                   }
              break;
             
              case 'outbrain':
                $outbrain_type   = adsforwp_rmv_warnings($post_meta_dataset, 'outbrain_type', 'adsforwp_array');
                $outbrain_widget_ids   = adsforwp_rmv_warnings($post_meta_dataset, 'outbrain_widget_ids', 'adsforwp_array');
                switch ($outbrain_type) {
                  case 'normal':
                    $width='200';
                    $height='200';
                    $banner_size = adsforwp_rmv_warnings($post_meta_dataset, 'banner_size', 'adsforwp_array');
                    if($banner_size !=''){
                      $explode_size = explode('x', $banner_size);              
                      $width = $explode_size[0];            
                      $height = $explode_size[1];
                    }            
                    if($this->is_amp){
                        $this->amp_ads_id[] = $post_ad_id;
                         if($ad_responsive == 1){
                            if(!empty($outbrain_widget_ids) ){
                              $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                      '.$sponsership_label.'
                                       <div class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                                         <amp-embed class="afw_ad_amp_'.esc_attr($post_ad_id).'" width="100" height="100"                                        
                                          type="outbrain"
                                          layout="responsive"
                                          data-widgetIds="'.esc_attr($outbrain_widget_ids).'"
                                          data-enable-refresh="10">
                                      </amp-embed>
                                      </div>
                                      </div>';
                            }
                        }else{
                            if(!empty($outbrain_widget_ids)){
                                 $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                    '.$sponsership_label.'
                                     <div class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                                       <amp-embed class="afw_ad_amp_'.esc_attr($post_ad_id).'" width="'. esc_attr($width) .'"
                                          height="'. esc_attr($height) .'" 
                                          type="outbrain"
                                          data-widgetIds="'.esc_attr($outbrain_widget_ids).'"
                                          data-enable-refresh="10">
                                      </amp-embed>
                                    </div>
                                    </div>';
                                 
                             }
                        }
                    }else{
                        $ad_code = '<div class="afw_ad_amp_outbrain" data-widget-id="'.esc_attr($outbrain_widget_ids).'"></div>';
                    }
                    break;
                  
                  default:
                    
                    break;
                }
              break;
            case 'mantis':
                    $width='300';
                    $height='250';
                    $banner_size = adsforwp_rmv_warnings($post_meta_dataset, 'banner_size', 'adsforwp_array');
                    if($banner_size !=''){
                      $explode_size = explode('x', $banner_size);              
                      $width = $explode_size[0];            
                      $height = $explode_size[1];
                    }
                    $mantis_ad_id = adsforwp_rmv_warnings($post_meta_dataset, 'mantis_property_id', 'adsforwp_array');
                    $mantis_zone_name = adsforwp_rmv_warnings($post_meta_dataset, 'mantis_zone_name', 'adsforwp_array');
                    $mantis_display_type = adsforwp_rmv_warnings($post_meta_dataset, 'mantis_display_type', 'adsforwp_array');
                    if($this->is_amp){
                        $this->amp_ads_id[] = $post_ad_id;
                         
                          if( !empty($mantis_ad_id) && $mantis_display_type == 'recommend'){
                              $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_custom afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                            '.$sponsership_label.'
                                                    <amp-embed class="afw_ad_amp_'.esc_attr($post_ad_id).'" width="100" height="283"
                                                        type="mantis-'.esc_attr($mantis_display_type).'"
                                                        layout=responsive
                                                        heights="(min-width:1907px) 56%, (min-width:1100px) 64%, (min-width:780px) 75%, (min-width:480px) 105%, 200%"
                                                        data-property="'.esc_attr($mantis_ad_id).'">
                                                    </amp-embed></div>';
                          }elseif( !empty($mantis_ad_id) && $mantis_display_type == 'display'){
                              $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_custom afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                              '.$sponsership_label.'
                                                    <amp-ad class="afw_ad_amp_'.esc_attr($post_ad_id).'" width="'. esc_attr($width) .'"
                                                            height="'. esc_attr($height) .'"
                                                            type = "mantis-'.esc_attr($mantis_display_type).'"
                                                            data-property = "'.esc_attr($mantis_ad_id).'"
                                                            data-zone="medium-rectangle">
                                                        </amp-ad>
                                                      </div>';
                          }
                         
                  }else{
                     
                      if( !empty($mantis_ad_id) && $mantis_display_type == 'recommend'){
                            $ad_code = '<div id="afwp_mantis__recommended"></div>
                                    <script type="text/javascript" data-cfasync="false">
                                        MANTIS_RECOMMEND = {
                                            property: "'.esc_attr($mantis_ad_id).'",
                                            render: "afwp_mantis__recommended"
                                        };
                                    </script>
                                    <script type="text/javascript" data-cfasync="false">
                                      var z = document.createElement("script");
                                      z.type = "text/javascript";
                                      z.async = true;
                                      z.src = "//assets.mantisadnetwork.com/recommend.min.js";
                                      var s = document.getElementsByTagName(\'head\')[0];
                                      s.parentNode.insertBefore(z, s);
                                    </script>
                                    <link href="//assets.mantisadnetwork.com/recommend.3columns.css" rel="stylesheet" type="text/css" />';
                      }elseif( !empty($mantis_ad_id) && $mantis_display_type == 'display' ){
                            $ad_code = '<div data-mantis-zone="'.esc_attr($mantis_zone_name).'"></div>
                                      <script type="text/javascript">
                                          var mantis = mantis || [];
                                          mantis.push(["display","load",{
                                              property: "'.esc_attr($mantis_ad_id).'"
                                          }]);
                                      </script>';
                      }
                  }
            break;
            case 'mgid':
                
                    $data_publisher   = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_mgid_data_publisher', 'adsforwp_array');
                    $data_widget      = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_mgid_data_widget', 'adsforwp_array');
                    $data_container   = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_mgid_data_container', 'adsforwp_array');                     
                    $data_js_src      = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_mgid_data_js_src', 'adsforwp_array');                     
                    $mgid_size = '';
                    
                    
                    $banner_size = adsforwp_rmv_warnings($post_meta_dataset, 'banner_size', 'adsforwp_array'); 
                    
                    if($banner_size !=''){
                        
                        $explode_size = explode('x', $banner_size);              
                        $width  = $explode_size[0];            
                        $height = $explode_size[1];                               
                    
                        if($width && $height){
                            $mgid_size = 'width="'.esc_attr($width).'" height="'.esc_attr($height).'"';
                        }
                    
                    }
                    
                                        
                    if($this->is_amp){
                     
                        if($data_publisher && $data_widget && $data_container){

                            $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_custom afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                            '.$sponsership_label.'
                                                            <amp-embed '.$mgid_size.'
                                                             type="mgid"
                                                             data-publisher="'. esc_attr($data_publisher).'"
                                                             data-widget="'. esc_attr($data_widget).'"
                                                             data-container="'. esc_attr($data_container).'">
                                                             </amp-embed>

                                                            </div>';    

                        }    
                                                                  
                    }else{
                        
                        if($data_publisher && $data_widget && $data_container && $data_js_src){
                                                                                    
                            $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_custom afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                            '.$sponsership_label.'                                                                   
                                                            <div id="'. esc_attr($data_container).'"> 
                                                            <script> 
                                                             (function() {
                                                                var D = new Date(),
                                                                    d = document,
                                                                    b = "body",
                                                                    ce = "createElement",
                                                                    ac = "appendChild",
                                                                    st = "style",
                                                                    ds = "display",
                                                                    n = "none",
                                                                    gi = "getElementById",
                                                                    lp = d.location.protocol,
                                                                    wp = lp.indexOf("http") == 0 ? lp : "https:";
                                                                var i = d[ce]("iframe");
                                                                i[st][ds] = n;
                                                                d[gi]("'. esc_attr($data_container).'")[ac](i);
                                                                try {
                                                                    var iw = i.contentWindow.document;
                                                                    iw.open();
                                                                    iw.writeln("<ht" + "ml><bo" + "dy></bo" + "dy></ht" + "ml>");
                                                                    iw.close();
                                                                    var c = iw;
                                                                } catch (e) {
                                                                    var iw = d;
                                                                    var c = d[gi]("'. esc_attr($data_container).'");
                                                                }
                                                                var dv = iw[ce]("div");
                                                                dv.id = "MG_ID";
                                                                dv[st][ds] = n;
                                                                dv.innerHTML = '. esc_attr($data_widget).';
                                                                c[ac](dv);
                                                                var s = iw[ce]("script");
                                                                s.async = "async";
                                                                s.defer = "defer";
                                                                s.charset = "utf-8";
                                                                s.src = wp + "'.esc_url($data_js_src).'?t=" + D.getYear() + D.getMonth() + D.getUTCDate() + D.getUTCHours();
                                                                c[ac](s);
                                                            })();
                                                           </script> </div>
                                                            </div>';                                                        
                            
                        }
                                                                                          
                    }                                                                                
            break;
            
            case 'custom':
                  $common_function_obj = new adsforwp_admin_common_functions();
                  $allowed_html = $common_function_obj->adsforwp_expanded_allowed_tags(); 
                    if($this->is_amp){
                     
                        if($custom_ad_code){

                            $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_custom afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                            '.$sponsership_label.'
                                                            '.wp_kses($custom_ad_code, $allowed_html).'
                                                            </div>';    

                        }    
                                                                  
                    }else{
                        
                        if($custom_ad_code){
                            
                            $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_custom  afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                        '.$sponsership_label.'
							'.wp_kses($custom_ad_code, $allowed_html).'
							</div>';     
                            
                        }
                                                                                          
                    }                                                                                
            break;
            
            case 'ad_image':
                    
                    $ad_img_width  = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_ad_img_width', 'adsforwp_array');
                    $ad_img_height = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_ad_img_height', 'adsforwp_array');                     
                                                            
                    if($this->is_amp){
                                                                      
                     $this->amp_ads_id[] = $post_ad_id;   
                     
                     if($ad_responsive == 1){
                                                 
                         if($where_to_display == 'sticky'){
                             
                             if($ad_image){
                                 
                                 $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                        '.$sponsership_label.'
							<div class="afw_ad_amp_achor afw_ad_amp_anchor_'.esc_attr($post_ad_id).'"><a target="_blank" href="'.esc_url($ad_redirect_url).'" rel="nofollow"><div class="afw_ad_amp_'.esc_attr($post_ad_id).'" style="background-image: url('.esc_url($ad_image).');height:'.esc_attr($ad_img_height).'px;width:'.esc_attr($ad_img_width).'px;display:inline-block;background-size: contain; background-repeat: no-repeat;" ></div>	</a></div>
							</div>'; 
                                 
                             }
                                                                                       
                         }else{
                             
                             if($ad_image){
                                 
                                 $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                        '.$sponsership_label.'   
							<div class="afw_ad_amp_achor afw_ad_amp_anchor_'.esc_attr($post_ad_id).'"><a target="_blank" href="'.esc_url($ad_redirect_url).'" rel="nofollow"><amp-img layout="responsive" class="afw_ad_amp_'.esc_attr($post_ad_id).'" src="'.esc_url($ad_image).'" height="'. esc_attr($ad_img_height).'" width="'.esc_attr($ad_img_width).'"></amp-img></a></div>                                                                                                                       
							</div>'; 
                                 
                             }
                             
                         }
                                                    
                     }else{
                         
                         if($where_to_display == 'sticky'){
                             
                             if($ad_image){
                                 
                                 $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                        '.$sponsership_label.'
							<div class="afw_ad_amp_achor afw_ad_amp_anchor_'.esc_attr($post_ad_id).'"><a target="_blank" href="'.esc_url($ad_redirect_url).'" rel="nofollow"><div class="afw_ad_amp_'.esc_attr($post_ad_id).'" style="background-image: url('.esc_url($ad_image).');height:'.esc_attr($ad_img_height).'px;width:'.esc_attr($ad_img_width).'px;display:inline-block;background-size: contain; background-repeat: no-repeat;" ></div>	</a></div>
							</div>'; 
                                 
                             }
                             
                             
                             
                         }else{
                            
                             if($ad_image){
                                 
                                 $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                        '.$sponsership_label.'
							<div class="afw_ad_amp_achor afw_ad_amp_anchor_'.esc_attr($post_ad_id).'"><a target="_blank" href="'.esc_url($ad_redirect_url).'" rel="nofollow"><amp-img class="afw_ad_amp_'.esc_attr($post_ad_id).'" src="'.esc_url($ad_image).'" height="'. esc_attr($ad_img_height).'" width="'.esc_attr($ad_img_width).'"></amp-img></a></div>                                                                                                                        
							</div>';  
                                 
                             }
                             
                             
                         }
                         
                          
                     }
                     
                      
                    }else{
                                                                            
                        if($ad_responsive == 1){
                             if($ad_alignment == 'left'){
                                
                             }
                             $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                            '.$sponsership_label.'
							<a target="_blank" href="'.esc_url($ad_redirect_url).'" rel="nofollow"><img height="auto" max-width="100%" src="'.esc_url($ad_image).'"></a>                                                            
							</div>';  
                         }else{
                             
                             $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                        '.$sponsership_label.'
							<a target="_blank" href="'.esc_url($ad_redirect_url).'" rel="nofollow"><img height="'. esc_attr($ad_img_height).'" width="'.esc_attr($ad_img_width).'" src="'.esc_url($ad_image).'"></a>                                                          
							</div>';  
                             
                         }             
                   }                                                                               
            break;
            
            case 'contentad':
                    
                    $contentad_id        = adsforwp_rmv_warnings($post_meta_dataset, 'contentad_id', 'adsforwp_array');
                    $contentad_id_d      = adsforwp_rmv_warnings($post_meta_dataset, 'contentad_id_d', 'adsforwp_array');
                    $contentad_widget_id = adsforwp_rmv_warnings($post_meta_dataset, 'contentad_widget_id', 'adsforwp_array');                     
                    
                    if($this->is_amp){
                        
                    $this->amp_ads_id[]     = $post_ad_id;                                        
                        
                    if($contentad_id && $contentad_id_d && $contentad_widget_id){
                     
                        $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                        '.$sponsership_label.'
                                                        <a class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
							<amp-ad
                                                                class="afw_ad_amp_'.esc_attr($post_ad_id).'"
                                                                width=300
                                                                height=250
                                                                type="contentad"
                                                                data-id="'.esc_attr($contentad_id).'"
                                                                data-d="'.esc_attr($contentad_id_d).'"
                                                                data-wid="'.esc_attr($contentad_widget_id).'"
                                                                data-enable-refresh="10">
                                                              </amp-ad>
                                                        </a>
							</div>';
                        
                    }                        
                      
                    }else{
                                   
                        if($contentad_id && $contentad_id_d && $contentad_widget_id){
                            
                         $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                        '.$sponsership_label.'
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
                    
                    $ad_now_widget_id = adsforwp_rmv_warnings($post_meta_dataset, 'ad_now_widget_id', 'adsforwp_array');                    
                
                    if(!$this->is_amp){
                        
                     if($ad_now_widget_id){
                        
                         $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
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
                                                     
                    }                                                                                
            break;
            
            case 'infolinks':
                    
                    $infolinks_pid = adsforwp_rmv_warnings($post_meta_dataset, 'infolinks_pid', 'adsforwp_array');
                    $infolinks_wsid = adsforwp_rmv_warnings($post_meta_dataset, 'infolinks_wsid', 'adsforwp_array');
                    
                    if(!$this->is_amp){
                        
                        if($infolinks_pid && $infolinks_wsid){
                         
                            $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                  <script type="text/javascript">
                                    var infolinks_pid = '.esc_attr($infolinks_pid).';
                                    var infolinks_wsid = '.esc_attr($infolinks_wsid).';
                                  </script>
                                <script type="text/javascript" src="http://resources.infolinks.com/js/infolinks_main.js"></script>
                                </div>';
                            
                        }                                                     
                    }                                                                                
            break;
                       
            case 'adsense':
            $adsense_type      = '';
            $author_ad_slot_id = '';
                        
            $adsense_type       = adsforwp_rmv_warnings($post_meta_dataset, 'adsense_type', 'adsforwp_array');                 
            $ad_client          = adsforwp_rmv_warnings($post_meta_dataset, 'data_client_id', 'adsforwp_array');              
            $author_adsense_ids = $this->adsforwp_get_pub_id_on_revenue_percentage();
            
            if($author_adsense_ids){
                
            $author_pub_id     = adsforwp_rmv_warnings($author_adsense_ids, 'author_pub_id', 'adsforwp_string');
            $author_ad_slot_id = adsforwp_rmv_warnings($author_adsense_ids, 'author_ad_slot_id', 'adsforwp_string');   
            
            if($author_pub_id){
                
            $ad_client = $author_pub_id;     
            
            }
            }                                    
            switch ($adsense_type) {
                case 'in_article_ads':
                    $ad_slot = adsforwp_rmv_warnings($post_meta_dataset, 'data_ad_slot', 'adsforwp_array');
                    if($author_ad_slot_id){
                        $ad_slot = $author_ad_slot_id;
                    }
                    if(!$this->is_amp){
                        if($ad_client && $ad_slot){
                             $ad_code =  '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                                    <ins class="adsbygoogle"
                                         style="display:block; text-align:center;"
                                         data-ad-layout="in-article"
                                         data-ad-format="fluid"
                                         data-ad-client="'.esc_attr($ad_client).'"
                                         data-ad-slot="'.esc_attr($ad_slot).'"></ins>
                                    <script>
                                         (adsbygoogle = window.adsbygoogle || []).push({});
                                    </script>
                                    </div>';
                        }
                    }
                break;
                case 'matched_content_ads':
                    $ad_slot = adsforwp_rmv_warnings($post_meta_dataset, 'data_ad_slot', 'adsforwp_array');
                    $matched_content_type = adsforwp_rmv_warnings($post_meta_dataset, 'matched_content_type', 'adsforwp_array');
                    $rows = adsforwp_rmv_warnings($post_meta_dataset, 'matched_content_rows', 'adsforwp_array');
                    $columns = adsforwp_rmv_warnings($post_meta_dataset, 'matched_content_columns', 'adsforwp_array');
                    if($author_ad_slot_id){
                        $ad_slot = $author_ad_slot_id;
                    }
                    $width='200';
                    $height='200';
                    $banner_size = adsforwp_rmv_warnings($post_meta_dataset, 'banner_size', 'adsforwp_array'); 
                    
                    if($banner_size !=''){
                        
                    $explode_size = explode('x', $banner_size);              
                    $width = $explode_size[0];            
                    $height = $explode_size[1];                               
                    
                    }
                    if($this->is_amp){
                      $this->amp_ads_id[] = $post_ad_id;
                          if($ad_client && $ad_slot){
                              $ad_code = '<amp-ad class="afw_ad_amp_'.esc_attr($post_ad_id).'"                                        
                                        type="adsense"
                                        width="100vw" height=320  
                                        data-ad-client="'. esc_attr($ad_client) .'"
                                        data-ad-slot="'.esc_attr($ad_slot).'"
                                        data-matched-content-ui-type="'.esc_attr($matched_content_type).'"
                                        data-matched-content-rows-num="'.esc_attr($rows).'"
                                        data-matched-content-columns-num="'.esc_attr($columns).'"
                                        data-auto-format="rspv"
                                        data-full-width>
                                        <div overflow></div>
                                    </amp-ad>';
                          }
                    }else{
                      if($ad_client && $ad_slot){
                          $ad_code =  '<ins class="adsbygoogle"
                                             style="display:block"
                                             data-ad-client="'. esc_attr($ad_client) .'"
                                             data-ad-slot="'.esc_attr($ad_slot).'"
                                             data-matched-content-ui-type="'.esc_attr($matched_content_type).'"
                                             data-matched-content-rows-num="'.esc_attr($rows).'"
                                             data-matched-content-columns-num="'.esc_attr($columns).'"
                                             data-ad-format="autorelaxed"></ins>
                                        <script>
                                        (adsbygoogle = window.adsbygoogle || []).push({});
                                        </script>';
                      }
                    }
                break;
                case 'normal':
                    $ad_slot = adsforwp_rmv_warnings($post_meta_dataset, 'data_ad_slot', 'adsforwp_array'); 
                    if($author_ad_slot_id){
                        
                    $ad_slot = $author_ad_slot_id;     
                    
                    }
                    $width='200';
                    $height='200';
                    $banner_size = adsforwp_rmv_warnings($post_meta_dataset, 'banner_size', 'adsforwp_array'); 
                    
                    if($banner_size !=''){
                        
                    $explode_size = explode('x', $banner_size);              
                    $width = $explode_size[0];            
                    $height = $explode_size[1];                               
                    
                    }            
                    if($this->is_amp){
                       
                        $this->amp_ads_id[] = $post_ad_id;
                        
                         if($ad_responsive == 1){
                             
                             if($ad_client && $ad_slot){
                              
                                 $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                    '.$sponsership_label.'
                                     <div class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                                       <amp-ad class="afw_ad_amp_'.esc_attr($post_ad_id).'" width="100vw" height=320                                        
                                        type="adsense"                                        
                                        data-ad-client="'. esc_attr($ad_client) .'"
                                        data-ad-slot="'.esc_attr($ad_slot).'"
                                        data-auto-format="rspv"
                                        data-enable-refresh="10" 
                                        data-full-width>
                                        <div overflow></div>
                                    </amp-ad>
                                    </div>
                                    </div>';
                                                                  
                             }                                                        
                             
                         }else{
                           
                             if($ad_client && $ad_slot){
                                 
                                 $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                    '.$sponsership_label.'
                                     <div class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                                       <amp-ad 
                                        class="afw_ad_amp_'.esc_attr($post_ad_id).'"
                                        type="adsense"
                                        width="'. esc_attr($width) .'"
                                        height="'. esc_attr($height) .'"
                                        data-ad-client="'. esc_attr($ad_client) .'"
                                        data-ad-slot="'.esc_attr($ad_slot).'"
                                        data-enable-refresh="10">
                                    </amp-ad>
                                    </div>
                                    </div>';
                                 
                             }
                                                                                       
                         }                                                        
                                                     
                    }else{     
                                              
                      if($ad_responsive == 1){
                           
                          if($ad_client && $ad_slot){
                          
                              $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                                '.$sponsership_label.'
                                                                <script async="" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">
                                                                </script>
                                                                <ins 
                                                                class="adsbygoogle" 
                                                                style="background:none;display:inline-block;width:'.esc_attr($width).'px;height:'.esc_attr($height).'px;max-width:100%;"                                                                                                                           
                                                                data-ad-client="'.esc_attr($ad_client).'"
                                                                data-ad-slot="'.esc_attr($ad_slot).'"
                                                                data-ad-format="auto"
                                                                data-full-width-responsive="true">
                                                                </ins>
                                                                <script>
                                                                        (adsbygoogle = window.adsbygoogle || []).push({});
                                                                </script>
                                                        </div>';
                              
                          }
                          
                      }else{
                          
                          if($ad_client && $ad_slot){
                            
                              $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                                '.$sponsership_label.'
                                                                <script async="" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">
                                                                </script>
                                                                <ins class="adsbygoogle" style="background:none;display:inline-block;width:'.esc_attr($width).'px;height:'.esc_attr($height).'px" data-ad-client="'.esc_attr($ad_client).'" data-ad-slot="'.esc_attr($ad_slot).'">
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
                
            $ad_data_cid  = $ad_data_crid = '';                         
                        
            $ad_data_cid  = adsforwp_rmv_warnings($post_meta_dataset, 'data_cid', 'adsforwp_array');                           
            $ad_data_crid = adsforwp_rmv_warnings($post_meta_dataset, 'data_crid', 'adsforwp_array');       
            
            $width  = '200';
            $height = '200';
            $banner_size = adsforwp_rmv_warnings($post_meta_dataset, 'banner_size', 'adsforwp_array');    
            
            if($banner_size !=''){
                
            $explode_size = explode('x', $banner_size);            
            $width        = adsforwp_rmv_warnings($explode_size, 0, 'adsforwp_string');            
            $height       = adsforwp_rmv_warnings($explode_size, 1, 'adsforwp_string');                               
            
            }            
            if($this->is_amp){
                                                    
                 $this->amp_ads_id[] = $post_ad_id;
                                  
                    if($ad_data_cid && $ad_data_crid){
                        
                     $ad_code ='<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw-md afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                    '.$sponsership_label.'
                            <a class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                            <amp-ad 
                                class="afw_ad_amp_'.esc_attr($post_ad_id).'"
				type="medianet"
				width="'. esc_attr($width) .'"
				height="'. esc_attr($height) .'"
                                data-tagtype="cm"    
				data-cid="'. esc_attr($ad_data_cid).'"
				data-crid="'.esc_attr($ad_data_crid).'"
        data-enable-refresh="10">
			    </amp-ad>  
                            </a>
                            </div>';   
                        
                    }                                         
                                            
				                
            }else{                                  
                
                if($ad_data_crid && $ad_data_cid){
                    
                 $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw-md afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                '.$sponsership_label.'
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
            
            case 'doubleclick':
            $validation = "false";                        
            $ad_slot_id  = adsforwp_rmv_warnings($post_meta_dataset, 'dfp_slot_id', 'adsforwp_array');                           
            $ad_div_gpt  = adsforwp_rmv_warnings($post_meta_dataset, 'dfp_div_gpt_ad', 'adsforwp_array'); 
            $width='200';
            $height='200';
            $banner_size = adsforwp_rmv_warnings($post_meta_dataset, 'banner_size', 'adsforwp_array');    
            
            if($banner_size !=''){
                $explode_size = explode('x', $banner_size);            
                $width        = adsforwp_rmv_warnings($explode_size, 0, 'adsforwp_string');            
                $height       = adsforwp_rmv_warnings($explode_size, 1, 'adsforwp_string');
            }
            $dfp_multisize_ads  = adsforwp_rmv_warnings($post_meta_dataset, 'dfp_multisize_ads', 'adsforwp_array');       
            $dfp_sizes  = adsforwp_rmv_warnings($post_meta_dataset, 'dfp_multisize_ads_sizes', 'adsforwp_array');
            $dfp_multi_validation  = adsforwp_rmv_warnings($post_meta_dataset, 'dfp_multisize_validation', 'adsforwp_array');
            if($dfp_multi_validation == 1){
                $validation = "true";
            }
            if($this->is_amp){                                
                    
                 $this->amp_ads_id[] = $post_ad_id;
                  if($where_to_display == 'sticky'){
                        if($dfp_multisize_ads == 1){
                            $amp_ad_code = '<amp-ad 
                                      class="afw_ad_amp_'.esc_attr($post_ad_id).'"
                                        type="doubleclick"
                                        layout="fixed"
                                        width="'. esc_attr($width) .'"
                                        height="'. esc_attr($height).'" 
                                        data-slot="'.esc_attr($ad_slot_id).'"
                                        data-multi-size="'.esc_attr($dfp_sizes).'"
                                        data-multi-size-validation="'.esc_attr($validation).'"
                                        data-enable-refresh="10">
                                           <div fallback>
                                           <p>Thank you for trying AMP!</p>
                                           <p>We have no ad to show to you!</p>
                                           </div>
                                        </amp-ad>';
                        }else{
                            $amp_ad_code = '<amp-ad 
                                      class="afw_ad_amp_'.esc_attr($post_ad_id).'"
                                        type="doubleclick"
                                        width="'. esc_attr($width) .'"
                                        height="'. esc_attr($height).'"                                        
                                        data-slot="'.esc_attr($ad_slot_id).'"
                                        data-enable-refresh="10">
                                           <div fallback>
                                           <p>Thank you for trying AMP!</p>
                                           <p>We have no ad to show to you!</p>
                                           </div>
                                  </amp-ad>';
                        }
                      

                    if($ad_slot_id){
                        $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                                        '.$sponsership_label.'
                            <a class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                                   
                          </a>
                        </div>';
                    }
                  }else{
                    if($ad_slot_id){
                        if($dfp_multisize_ads == 1){
                          $amp_ad_code = '<amp-ad 
                                      class="afw_ad_amp_'.esc_attr($post_ad_id).'"
                                        type="doubleclick"
                                        layout="fixed"
                                        width="'. esc_attr($width) .'"
                                        height="'. esc_attr($height).'" 
                                        data-slot="'.esc_attr($ad_slot_id).'"
                                        data-multi-size="'.esc_attr($dfp_sizes).'"
                                        data-multi-size-validation="'.esc_attr($validation).'"
                                        data-enable-refresh="10">
                                           <div fallback>
                                           <p>Thank you for trying AMP!</p>
                                           <p>We have no ad to show to you!</p>
                                           </div>
                                  </amp-ad>';
                        }else{
                          $amp_ad_code = '<amp-ad 
                                      class="afw_ad_amp_'.esc_attr($post_ad_id).'"
                                        type="doubleclick"
                                        width="'. esc_attr($width) .'"
                                        height="'. esc_attr($height) .'"                                        
                                        data-slot="'.esc_attr($ad_slot_id).'"
                                        data-enable-refresh="10">
                                           <div fallback>
                                           <p>Thank you for trying AMP!</p>
                                           <p>We have no ad to show to you!</p>
                                           </div>
                                  </amp-ad>'; 
                        }
                         
                        $ad_code ='<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;float:'.esc_attr($ad_text_wrap).';" class="afw afw-md afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                  <a class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                                    '.$amp_ad_code.'
                                  </a>
                                  </div>';     
                       }
                 }                                           				                
            }else{      
                              
                if($ad_div_gpt){
                    
                    $ad_code = '<div id="'.esc_attr($ad_div_gpt).'" style="height:'.esc_attr($height).'px; width:'.esc_attr($width).'px;">
                        <script>
                        googletag.cmd.push(function() { googletag.display("'.esc_attr($ad_div_gpt).'"); });
                        </script>
                        </div>';   
                    
                }
             
                
            
            }
            break;
                                    
            default:
            break;
        }      
                          
             $amp_ads_id_json = json_encode($this->amp_ads_id);             
             set_transient('adsforwp_transient_amp_ids', $amp_ads_id_json); 
                              
             return $ad_code;
                
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
                                 
            $ad_code =  $this->adsforwp_get_ad_code($post_ad_id, $type="AD");          
            return $ad_code;     
          
        }
            
    }    
    /**
     * We are displaying groups as per shortcode. eg [[adsforwp-group id="0000"]
     * @param type $atts
     * @return type string
     */
    public function adsforwp_group_ads($atts, $group_id = null, $widget=null, $all_condition_status = null) { 
                       
        if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
            $this->is_amp = true;        
        }        
        $post_group_id  =   adsforwp_rmv_warnings($atts, 'id', 'adsforwp_string'); 
        
        if($group_id){
            
            $post_group_id  =   $group_id;     
        
        } 
        
        $service = new adsforwp_output_service();
        $ad_status = $service->adsforwp_is_condition($post_group_id);
        
        if($ad_status || $widget =='widget' || $all_condition_status){
                                                               
        $ad_alignment  = $wheretodisplay = '';                        
        $ad_margin_top = $ad_margin_bottom = $ad_margin_left = $ad_margin_right = 0;
                        
        $post_group_data                = get_post_meta($post_group_id,$key='adsforwp_ads',true);
        $post_group_meta                = get_post_meta($post_group_id,$key='',true);
        $margin_post_meta               = get_post_meta($post_group_id, $key='adsforwp_ad_margin',true);
        
        
        $ad_margin_top                  = adsforwp_rmv_warnings($margin_post_meta, 'ad_margin_top', 'adsforwp_string');                    
        $ad_margin_bottom               = adsforwp_rmv_warnings($margin_post_meta, 'ad_margin_bottom', 'adsforwp_string');                  
        $ad_margin_left                 = adsforwp_rmv_warnings($margin_post_meta, 'ad_margin_left', 'adsforwp_string');                
        $ad_margin_right                = adsforwp_rmv_warnings($margin_post_meta, 'ad_margin_right', 'adsforwp_string');  
        
        $wheretodisplay                 = adsforwp_rmv_warnings($post_group_meta, 'wheretodisplay', 'adsforwp_array');                                
        
        if($wheretodisplay !='ad_shortcode' && isset($post_group_meta['adsforwp_ad_align'])){
            
        $ad_alignment      = $post_group_meta['adsforwp_ad_align'][0];    
        
        }
                       
        $ad_code            ="";  
        $group_ad_code      ="";
        $filter_group_ids   = array();   
        
        if($this->is_amp){            
                            
        if($post_group_data){
            
           foreach ($post_group_data as $group_id=>$value){
            if(get_post_status($group_id) == 'publish'){
              $output_service = new adsforwp_output_service();                    
                $expiry_status = $output_service->adsforwp_check_ad_expiry_date($group_id);
                if( $expiry_status){
                  $filter_group_ids[$group_id] = $value; 
                }
            }
            
         } 
         if($filter_group_ids){
             $ad_code =  $this->adsforwp_get_ad_code(array_rand($filter_group_ids), $type="GROUP");              
         }
        
        
        }  
               
        }else{   
                                
        $post_data = get_post_meta($post_group_id,$key='',true);  
        
        if($post_group_data){
            
        $adsresultset = array();  
        $response = array();    
        
        foreach($post_group_data as $post_ad_id => $post){
        
        $select_ad_type     = '';
        $data_cid           = '';
        $data_crid          = '';
        $ad_detail          = get_post_meta($post_ad_id,$key='',true);                  
        $select_ad_type     = adsforwp_rmv_warnings($ad_detail, 'select_adtype', 'adsforwp_array');                   
        $data_cid           = adsforwp_rmv_warnings($ad_detail, 'data_cid', 'adsforwp_array');                   
        $data_crid          = adsforwp_rmv_warnings($ad_detail, 'data_crid', 'adsforwp_array');                   
        
        if(!empty($ad_detail) && $select_ad_type !='' && get_post_status($post_ad_id) == 'publish'){
          $output_service = new adsforwp_output_service();               
          $expiry_status = $output_service->adsforwp_check_ad_expiry_date($post_ad_id);
          if($expiry_status){
            $adsresultset[] = array(
                'ad_id'                     => $post_ad_id,
                'ad_type'                   => adsforwp_rmv_warnings($ad_detail, 'select_adtype', 'adsforwp_array'),
                'ad_adsense_type'           => adsforwp_rmv_warnings($ad_detail, 'adsense_type', 'adsforwp_array'),
                'ad_custom_code'            => adsforwp_rmv_warnings($ad_detail, 'custom_code', 'adsforwp_array'),
                'ad_data_client_id'         => adsforwp_rmv_warnings($ad_detail, 'data_client_id', 'adsforwp_array'),
                'ad_data_ad_slot'           => adsforwp_rmv_warnings($ad_detail, 'data_ad_slot', 'adsforwp_array'),
                'ad_data_cid'               => $data_cid,
                'ad_data_crid'              => $data_crid,
                'ad_banner_size'            => adsforwp_rmv_warnings($ad_detail, 'banner_size', 'adsforwp_array'),
                'ad_image'                  => adsforwp_rmv_warnings($ad_detail, 'adsforwp_ad_image', 'adsforwp_array'),
                'ad_redirect_url'           => adsforwp_rmv_warnings($ad_detail, 'adsforwp_ad_redirect_url', 'adsforwp_array'),
                'ad_img_height'             => adsforwp_rmv_warnings($ad_detail, 'adsforwp_ad_img_height', 'adsforwp_array'),
                'ad_img_width'              => adsforwp_rmv_warnings($ad_detail, 'adsforwp_ad_img_width', 'adsforwp_array'),                
        ) ; 
          }
        
        
        }
        
        }
        $response['afw_group_id'] = $post_group_id;
        
        $response['adsforwp_refresh_type']           = adsforwp_rmv_warnings($post_data, 'adsforwp_refresh_type', 'adsforwp_array');                
        $response['adsforwp_group_ref_interval_sec'] = adsforwp_rmv_warnings($post_data, 'adsforwp_group_ref_interval_sec', 'adsforwp_array');               
        $response['adsforwp_group_type']             = adsforwp_rmv_warnings($post_data, 'adsforwp_group_type', 'adsforwp_array');
        
        $response['ads'] = $adsresultset;  
        if($response['adsforwp_refresh_type'] == 'on_interval'){
                
        $ad_code ='<div class="afw-groups-ads-json" afw-group-id="'.esc_attr($post_group_id).'" data-json="'. esc_attr(json_encode($response)).'">';           
        $ad_code .='</div>';
        $ad_code .='<div style="display:none;" data-id="'.esc_attr($post_group_id).'" class="afw_ad_container_pre"></div><div data-id="'.esc_attr($post_group_id).'" class="afw afw_ad_container"></div>';
        
        
        }else{
                
        $post_group_data = get_post_meta($post_group_id,$key='adsforwp_ads',true);  
        
        foreach ($post_group_data as $group_id=>$value){
            
            if(get_post_status($group_id) == 'publish'){
              $output_service = new adsforwp_output_service();
              $expiry_status = $output_service->adsforwp_check_ad_expiry_date($group_id);   
              if($expiry_status){
                $filter_group_ids[$group_id] = $value; 
              }
              
            }
            
           }       
            if($filter_group_ids){
                $ad_code =  $this->adsforwp_get_ad_code(array_rand($filter_group_ids), $type="GROUP"); 
            }
                              
          }        
         }
                              
       } 
                                      
       $group_ad_code  = '<div data-id="'.esc_attr($post_group_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_group afw_group afwadgroupid-'.esc_attr($post_group_id).'">';
       $group_ad_code .= $ad_code;
       $group_ad_code .='</div>';       
              
       return $group_ad_code;
       
      }    
               
    }
    /**
     * This is a ajax handler function for ads groups. 
     * @return type json string
     */
    public function adsforwp_get_groups_ad(){  
        
        $ad_id                  = sanitize_text_field($_GET['ad_id']);        
        $ads_group_id           = sanitize_text_field($_GET['ads_group_id']);
        $ads_group_type         = sanitize_text_field($_GET['ads_group_type']);
        $ads_group_data         = get_post_meta($ads_group_id,$key='adsforwp_ads',true);
        
        switch ($ads_group_type) {
            
            case 'rand':
                
                if(is_array($ads_group_data)){
                    $ad_code =  $this->adsforwp_get_ad_code(array_rand($ads_group_data), $type="GROUP");
                }
            
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
    /**
     * Function to detect adblocker 
     * Adblocker blocks all the js from adsforwp thats why we have not used wp_enqueue_script here.
     * Instead we directly added the javascript to work the ads when ad blocker support is enable in adsforwp settings
     */

    public function adsforwp_mediavines_ads_script(){
        $all_ads_id    = adsforwp_get_ad_ids();
        $service = new adsforwp_output_service();
        $post_meta_dataset = array();
        if($all_ads_id){
            foreach($all_ads_id as $ad_id){
                $ad_status = $service->adsforwp_is_condition($ad_id);
                $post_meta_dataset = array();                      
                $post_meta_dataset = get_post_meta($ad_id,$key='',true);
                $post_type = get_post_meta( $ad_id, 'select_adtype', true );
                $mediavine_site_id   = adsforwp_rmv_warnings($post_meta_dataset, 'mediavine_site_id', 'adsforwp_array');
                if( $ad_status && $post_type == 'mediavine' && !empty($mediavine_site_id)){
                  ?>
                  <link rel='dns-prefetch' href='//scripts.mediavine.com' />
                  <script type='text/javascript' async="async" data-noptimize="1" data-cfasync="false" src='//scripts.mediavine.com/tags/<?php echo esc_attr($mediavine_site_id);?>.js?ver=5.2.3'></script>
                  <?php
                }
            }
        }
    }
    public function adsforwp_ezoic_ads_script(){
        $all_ads_id    = adsforwp_get_ad_ids();
        $service = new adsforwp_output_service();
        
        if($all_ads_id){
          foreach($all_ads_id as $ad_id){
                $ad_status = $service->adsforwp_is_condition($ad_id);
                $post_meta_dataset = array();                      
                $post_meta_dataset = get_post_meta($ad_id,$key='',true);
                $post_type = get_post_meta( $ad_id, 'select_adtype', true ); 
                $ezoic_slot_id   = adsforwp_rmv_warnings($post_meta_dataset, 'ezoic_slot_id', 'adsforwp_array');
                if( $ad_status && $post_type == 'ezoic' && !empty($ezoic_slot_id)){
        ?>
        <!-- AMPforWP Ezoic Code -->
        <script>var ezoicId = <?php echo esc_attr($ezoic_slot_id);?>;</script>
        <script type="text/javascript" src="//go.ezoic.net/ezoic/ezoic.js"></script>
        <!-- AMPforWP Ezoic Code -->
        <?php
                }
          }
        }
    }
    public function adsforwp_taboola_ads_script(){
        $all_ads_id    = adsforwp_get_ad_ids();
        if($all_ads_id){
          foreach($all_ads_id as $ad_id){
                $post_meta_dataset = array();                      
                $post_meta_dataset = get_post_meta($ad_id,$key='',true);
                $post_type = get_post_meta( $ad_id, 'select_adtype', true ); 
                $publisher_id   = adsforwp_rmv_warnings($post_meta_dataset, 'taboola_publisher_id', 'adsforwp_array');
                if($post_type == 'taboola' && !empty($publisher_id)){
          ?>
          <script type='text/javascript'>window._taboola = window._taboola || [];
          _taboola.push({article:'auto'});
          !function (e, f, u) {
            e.async = 1;
            e.src = u;
            f.parentNode.insertBefore(e, f);
          }(document.createElement('script'), document.getElementsByTagName('script')[0], '//cdn.taboola.com/libtrc/<?php echo esc_attr($publisher_id);?>/loader.js');
          </script>
          <?php
                }
          }
        }
    }

    public function adsforwp_outbrain_script(){
      $all_ads_id    = adsforwp_get_ad_ids();
        if($all_ads_id){
          $service = new adsforwp_output_service();
            foreach($all_ads_id as $ad_id){
                $post_type = get_post_meta( $ad_id, 'select_adtype', true ); 
                if($post_type == 'outbrain'){
                  $post_meta_dataset = array();                      
                  $post_meta_dataset = get_post_meta($ad_id,$key='',true);
                  $outbrain_widget_ids   = adsforwp_rmv_warnings($post_meta_dataset, 'outbrain_widget_ids', 'adsforwp_array');
                  $ad_status = $service->adsforwp_is_condition($ad_id);
                  if($ad_status && !empty($outbrain_widget_ids)){
                  ?>
                  <script type="text/javascript" async="async" src="http://widgets.outbrain.com/outbrain.js "></script>
                  <?php
                }
                }
            }
        }
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
