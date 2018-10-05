<?php
/**
 * This class handle all the user end related functions
 */
class adsforwp_output_functions{
    
    private $is_amp = false;     
    public  $visibility = null;
    public  $amp_ads_id = array();
    public function __construct() {  
        
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
            }
            //Ads positioning ends here
            
            //Groups positioning starts here
            $all_group_post = json_decode(get_transient('adsforwp_groups_transient_ids'), true);            
            if($all_group_post){
                
            foreach($all_group_post as $group){                               
            $post_group_id = $group;             
                                                 
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
                     $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
							<div class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'"><a target="_blank" href="'.esc_url($ad_image_redirect_url).'"><amp-img class="afw_ad_amp_'.esc_attr($post_ad_id).'" src="'.esc_url($ad_image).'" height="'. esc_attr($adsforwp_ad_img_height).'" width="'.esc_attr($adsforwp_ad_img_width).'"></amp-img></a></div>
							</div>';
                                                       
                             
                    }   
                    }else{
                        if($adsforwp_non_amp_visibility !='hide'){
                         $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw_ad_image afw_ad afwadid-'.esc_attr($post_ad_id).'">
							<a target="_blank" href="'.esc_url($ad_image_redirect_url).'"><img height="'. esc_attr($adsforwp_ad_img_height).'" width="'.esc_attr($adsforwp_ad_img_width).'" src="'.esc_url($ad_image).'"></a>
							</div>';        
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
                                        id : "'.$ad_now_widget_id.'",
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
                         $ad_code = '<div data-ad-id="'.esc_attr($post_ad_id).'" style="text-align:-webkit-'.esc_attr($ad_alignment).'; margin-top:'.esc_attr($ad_margin_top).'px; margin-bottom:'.esc_attr($ad_margin_bottom).'px; margin-left:'.esc_attr($ad_margin_left).'px; margin-right:'.esc_attr($ad_margin_right).'px;" class="afw afw-ga afw_ad afwadid-'.esc_attr($post_ad_id).'">
                                     <div class="afw_ad_amp_anchor_'.esc_attr($post_ad_id).'">
                                       ss<amp-ad 
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
                    }else{     
                     if($adsforwp_non_amp_visibility !='hide'){   
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
                    break;                
                default:
                    break;
            }                                                                                                                
            break;
            
            case 'media_net':
                        
            $ad_data_cid = $post_meta_dataset['data_cid'][0];
            $ad_data_crid = $post_meta_dataset['data_crid'][0];    
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
        $wheretodisplay = '';       
            
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
        if($wheretodisplay !='ad_shortcode' && isset($post_group_meta['adsforwp_ad_align'])){
        $ad_alignment      = $post_group_meta['adsforwp_ad_align'][0];    
        }
            
           
        $ad_code ="";    
        if($this->is_amp){            
           
        if($post_group_data){
        $ad_code =  $this->adsforwp_get_ad_code(array_rand($post_group_data), $type="GROUP");              
        }                
        }else{            
                
        $post_data = get_post_meta($post_group_id,$key='',true);                
        
        if($post_group_data){
        $adsresultset = array();  
        $response = array();           
        foreach($post_group_data as $post_ad_id => $post){
        $ad_detail = get_post_meta($post_ad_id,$key='',true);        
        $adsresultset[] = array(
                'ad_id' => $post_ad_id,
                'ad_type' => $ad_detail['select_adtype'][0],
                'ad_adsense_type' => $ad_detail['adsense_type'][0],
                'ad_custom_code' => $ad_detail['custom_code'][0],
                'ad_data_client_id' => $ad_detail['data_client_id'][0],
                'ad_data_ad_slot' => $ad_detail['data_ad_slot'][0],
                'ad_data_cid' => $ad_detail['data_cid'][0],
                'ad_data_crid' => $ad_detail['data_crid'][0],
                'ad_banner_size' => $ad_detail['banner_size'][0],
                'ad_image' => $ad_detail['adsforwp_ad_image'][0],
                'ad_redirect_url' => $ad_detail['adsforwp_ad_redirect_url'][0],
                'ad_img_height' => $ad_detail['adsforwp_ad_img_height'][0],
                'ad_img_width' => $ad_detail['adsforwp_ad_img_width'][0],                
        ) ; 
        
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
        $ad_code .='<div style="display:none;" data-group-ad-id="'.esc_attr($post_group_id).'" class="afw_ad_container_pre"></div><div data-id="'.esc_attr($post_group_id).'" class="afw afw_ad_container"></div>';
        
        
        }else{
        $post_group_data = get_post_meta($post_group_id,$key='adsforwp_ads',true);         
        $ad_code =  $this->adsforwp_get_ad_code(array_rand($post_group_data), $type="GROUP");   
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
};
