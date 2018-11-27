<?php
class adsforwp_admin_analytics{
            
public function __construct() {                           
    }
    
    public function adsforwp_admin_analytics_hooks(){
        
         add_action('wp_ajax_nopriv_adsforwp_insert_ad_impression', array($this, 'adsforwp_insert_ad_impression'));      
         add_action('wp_ajax_adsforwp_insert_ad_impression', array($this, 'adsforwp_insert_ad_impression'));
         
         add_action('wp_ajax_nopriv_adsforwp_insert_ad_clicks', array($this, 'adsforwp_insert_ad_clicks'));      
         add_action('wp_ajax_adsforwp_insert_ad_clicks', array($this, 'adsforwp_insert_ad_clicks'));
         
         add_action('wp_ajax_nopriv_adsforwp_insert_ad_clicks_amp', array($this, 'adsforwp_insert_ad_clicks_amp'));      
         add_action('wp_ajax_adsforwp_insert_ad_clicks_amp', array($this, 'adsforwp_insert_ad_clicks_amp'));
         
         add_action('wp_ajax_nopriv_adsforwp_insert_ad_impression_amp', array($this, 'adsforwp_insert_ad_impression_amp'));      
         add_action('wp_ajax_adsforwp_insert_ad_impression_amp', array($this, 'adsforwp_insert_ad_impression_amp'));
                  
         add_action('amp_post_template_data',array($this, 'adsforwp_enque_analytics_amp_script'));                  
         add_filter('amp_post_template_footer', array($this, 'adsforwp_add_analytics_amp_tags'));                             
    }


    public function adsforwp_enque_analytics_amp_script($data){
        if ( empty( $data['amp_component_scripts']['amp-analytics'] ) ) {
                $data['amp_component_scripts']['amp-analytics'] = 'https://cdn.ampproject.org/v0/amp-analytics-latest.js';
        }
        if ( empty( $data['amp_component_scripts']['amp-bind'] ) ) {
                $data['amp_component_scripts']['amp-bind'] = 'https://cdn.ampproject.org/v0/amp-bind-0.1.js';
        }
                return $data;         
    }
    
    public function adsforwp_add_analytics_amp_tags(){
        
         if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
        $amp_ads_id = json_decode(get_transient('adsforwp_transient_amp_ids'), true);         
        $nonce = wp_create_nonce('adsforwp_ajax_check_front_nonce');        
        $ad_impression_url = admin_url('admin-ajax.php?action=adsforwp_insert_ad_impression_amp&adsforwp_front_nonce='.$nonce.'&event=${eventId}');                              
        $ad_clicks_url = admin_url('admin-ajax.php?action=adsforwp_insert_ad_clicks_amp&adsforwp_front_nonce='.$nonce.'&event=${eventId}');                              
        $ad_impression_script = ''; 
        $ad_clicks_script = '';
        if($amp_ads_id){
         foreach($amp_ads_id as $ad_id){
            $ad_impression_script .= '<amp-analytics><script type="application/json">
                  {
                    "requests": {
                      "event": "'.$ad_impression_url.'"
                    },
                    "triggers": {
                      "trackPageview": {
                        "on": "visible",
                        "request": "event",
                        "visibilitySpec": {
                          "selector": ".afw_ad_amp_'.esc_attr($ad_id).'",
                          "visiblePercentageMin": 20,
                          "totalTimeMin": 500,
                          "continuousTimeMin": 200
                        },                                  
                        "vars": {
                          "eventId": "'.esc_attr($ad_id).'"
                        }
                      }
                    }
                  }</script></amp-analytics>                                  
                ';     
            
            $ad_clicks_script .='<amp-analytics>
                                <script type="application/json">
                                  {
                                    "requests": {
                                      "event": "'.$ad_clicks_url.'"
                                    },
                                    "triggers": {
                                      "trackAnchorClicks": {
                                        "on": "click",
                                        "selector": ".afw_ad_amp_anchor_'.esc_attr($ad_id).'",
                                        "request": "event",
                                        "vars": {
                                          "eventId": "'.esc_attr($ad_id).'"
                                        }
                                      }
                                    }
                                  }
                                </script>
                              </amp-analytics>';                        
            
           }   
        }                   
          echo $ad_impression_script; 
          echo $ad_clicks_script;
         }
         
    }
    
    
    public function adsforwp_insert_ad_impression_amp(){  
           
            if ( ! isset( $_GET['adsforwp_front_nonce'] ) ){
                return; 
             }
            if ( !wp_verify_nonce( $_GET['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ){
               return;  
            }  
        
           $ad_id = sanitize_text_field($_GET['event']);           
           $device_name = 'amp';
           $key_name = $device_name.'-count';
            if($ad_id){
                
                 $post_impression_count = get_post_meta($ad_id, $key='ad_impression_count', true );            
                 $post_impression_count++;        
                 update_post_meta( $ad_id, 'ad_impression_count', $post_impression_count);          
                 //Device specific count
                 $device_impression_count = get_post_meta($ad_id, $key=$key_name, true );          
                 $device_impression_count++;         
                 update_post_meta( $ad_id, $key_name, $device_impression_count);

                 $optionDetails = get_option("adsforwp_ads-".date('Y-m-d'));
                 if($optionDetails){
                    if(!isset($optionDetails[$device_name][$ad_id]['impression'])){
                        $optionDetails[$device_name][$ad_id]['impression'] = 1;
                    }else{
                        $optionDetails[$device_name][$ad_id]['impression'] += 1;
                    }
                 }else{
                    $optionDetails[$device_name][$ad_id]['impression'] = 1;
                 }
                 $optionDetails['complete'][$device_name]['impression'] = (isset($optionDetails['complete'][$device_name]['impression'])? $optionDetails['complete'][$device_name]['impression']: 0)+1;
                 update_option("adsforwp_ads-".date('Y-m-d'), $optionDetails);                      
            }    
           wp_die();           
    }
    
    
    /**
     * We are inserting ad impression after all the ads
     */
    public function adsforwp_insert_ad_impression(){  
        
            if ( ! isset( $_POST['adsforwp_front_nonce'] ) ){
                return; 
             }
            if ( !wp_verify_nonce( $_POST['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ){
               return;  
            }                
            $ad_ids = $_POST['ad_ids'];
            $device_name = sanitize_text_field($_POST['device_name']);
            $key_name = $device_name.'-count';
            if($ad_ids){
                foreach ($ad_ids as $ad_id){
                 $post_impression_count = get_post_meta($ad_id, $key='ad_impression_count', true );            
                 $post_impression_count++;        
                 update_post_meta( $ad_id, 'ad_impression_count', $post_impression_count);          
                 //Device specific count
                 $device_impression_count = get_post_meta($ad_id, $key=$key_name, true );          
                 $device_impression_count++;         
                 update_post_meta( $ad_id, $key_name, $device_impression_count);  

                 //option save
                 $optionDetails = get_option("adsforwp_ads-".date('Y-m-d'));
                 if($optionDetails){
                    if(!isset($optionDetails[$device_name][$ad_id]['impression'])){
                        $optionDetails[$device_name][$ad_id]['impression'] = 1;
                    }else{
                        $optionDetails[$device_name][$ad_id]['impression'] += 1;
                    }
                 }else{
                    $optionDetails[$device_name][$ad_id]['impression'] = 1;
                 }
                 $optionDetails['complete'][$device_name]['impression'] = (isset($optionDetails['complete'][$device_name]['impression'])? $optionDetails['complete'][$device_name]['impression']: 0)+1;
                 update_option("adsforwp_ads-".date('Y-m-d'), $optionDetails);
                }//Foreach closed     
            }        
           wp_die();           
    }
    /**
     * We are inserting ad clicks
     */
    public function adsforwp_insert_ad_clicks(){  
            
            if ( ! isset( $_POST['adsforwp_front_nonce'] ) ){
                return; 
             }
            if ( !wp_verify_nonce( $_POST['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ){
               return;  
            }            
            $device_name = sanitize_text_field($_POST['device_name']);
            $ad_id = sanitize_text_field($_POST['ad_id']);
            $key_name = $device_name.'-clicks';
            if($ad_id){               
                $ad_clicks_count = get_post_meta($ad_id, $key='ad_clicks', true );                
                $ad_clicks_count++;        
                update_post_meta( $ad_id, 'ad_clicks', $ad_clicks_count);                         
                //Device specific clicks  
                $ad_clicks_count = get_post_meta($ad_id, $key=$key_name, true );          
                $ad_clicks_count++;         
                update_post_meta( $ad_id, $key_name, $ad_clicks_count);   

                //option save
                 $optionDetails = get_option("adsforwp_ads-".date('Y-m-d'));
                 if($optionDetails){
                    if(!isset($optionDetails[$device_name][$ad_id]['click'])){
                        $optionDetails[$device_name][$ad_id]['click'] = 1;
                    }else{
                        $optionDetails[$device_name][$ad_id]['click'] += 1;
                    }
                 }else{
                    $optionDetails[$device_name][$ad_id]['click'] = 1;
                 }
                 $optionDetails['complete'][$device_name]['click'] = (isset($optionDetails['complete'][$device_name]['click'])? $optionDetails['complete'][$device_name]['click']: 0)+1;
                 update_option("adsforwp_ads-".date('Y-m-d'), $optionDetails);
              
            }                           
           wp_die();           
    }
    
    public function adsforwp_insert_ad_clicks_amp(){        
        
            if ( ! isset( $_GET['adsforwp_front_nonce'] ) ){
                return; 
             }
            if ( !wp_verify_nonce( $_GET['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ){
               return;  
            }  
            
            $ad_id = sanitize_text_field($_GET['event']);
            $device_name = 'amp';
            $key_name = $device_name.'-clicks';
            if($ad_id){               
                $ad_clicks_count = get_post_meta($ad_id, $key='ad_clicks', true );                
                $ad_clicks_count++;        
                update_post_meta( $ad_id, 'ad_clicks', $ad_clicks_count);                         
                //Device specific clicks  
                $ad_clicks_count = get_post_meta($ad_id, $key=$key_name, true );          
                $ad_clicks_count++;         
                update_post_meta( $ad_id, $key_name, $ad_clicks_count);   
              
                //option save
                $optionDetails = get_option("adsforwp_ads-".date('Y-m-d'));
                 if($optionDetails){
                    if(!isset($optionDetails[$device_name][$ad_id]['click'])){
                        $optionDetails[$device_name][$ad_id]['click'] = 1;
                    }else{
                        $optionDetails[$device_name][$ad_id]['click'] += 1;
                    }
                 }else{
                    $optionDetails[$device_name][$ad_id]['click'] = 1;
                 }
                 $optionDetails['complete'][$device_name]['click'] = (isset($optionDetails['complete'][$device_name]['click'])? $optionDetails['complete'][$device_name]['click']: 0)+1;
                 update_option("adsforwp_ads-".date('Y-m-d'), $optionDetails);
            }                           
           wp_die();           
    }

}
if (class_exists('adsforwp_admin_analytics')) {
	$adsforwp_analytics_hooks_obj =new adsforwp_admin_analytics;
        $adsforwp_analytics_hooks_obj->adsforwp_admin_analytics_hooks();        
};
