<?php
class adsforwp_admin_analytics{
            
    public function __construct() {                           
        
    }
    
    /**
     * This is the list of hooks used in this class
     */
    public function adsforwp_admin_analytics_hooks(){
        
         add_action('wp_ajax_nopriv_adsforwp_insert_ad_impression', array($this, 'adsforwp_insert_ad_impression'));      
         add_action('wp_ajax_adsforwp_insert_ad_impression', array($this, 'adsforwp_insert_ad_impression'));
         
         add_action('wp_ajax_nopriv_adsforwp_insert_ad_clicks', array($this, 'adsforwp_insert_ad_clicks'));      
         add_action('wp_ajax_adsforwp_insert_ad_clicks', array($this, 'adsforwp_insert_ad_clicks'));
         
         add_action('wp_ajax_nopriv_adsforwp_insert_ad_clicks_amp', array($this, 'adsforwp_insert_ad_clicks_amp'));      
         add_action('wp_ajax_adsforwp_insert_ad_clicks_amp', array($this, 'adsforwp_insert_ad_clicks_amp'));
         
         add_action('wp_ajax_nopriv_adsforwp_insert_ad_impression_amp', array($this, 'adsforwp_insert_ad_impression_amp'));      
         add_action('wp_ajax_adsforwp_insert_ad_impression_amp', array($this, 'adsforwp_insert_ad_impression_amp'));
                  
         add_filter('amp_post_template_data',array($this, 'adsforwp_enque_analytics_amp_script'));                  
         add_filter('amp_post_template_footer', array($this, 'adsforwp_add_analytics_amp_tags'));                             
    }


    /**
     * Here, We are enquing amp scripts.
     * @param type $data
     * @return string
     */
    public function adsforwp_enque_analytics_amp_script($data){
        if ( empty( $data['amp_component_scripts']['amp-analytics'] ) ) {
                $data['amp_component_scripts']['amp-analytics'] = 'https://cdn.ampproject.org/v0/amp-analytics-latest.js';
        }
        if ( empty( $data['amp_component_scripts']['amp-bind'] ) ) {
                $data['amp_component_scripts']['amp-bind'] = 'https://cdn.ampproject.org/v0/amp-bind-0.1.js';
        }
        if ( empty( $data['amp_component_scripts']['amp-user-notification'] ) ) {
                $data['amp_component_scripts']['amp-user-notification'] = 'https://cdn.ampproject.org/v0/amp-user-notification-0.1.js';
        }
        if ( empty( $data['amp_component_scripts']['amp-ad'] ) ) {
                $data['amp_component_scripts']['amp-ad'] = 'https://cdn.ampproject.org/v0/amp-ad-latest.js';
        }
        if ( empty( $data['amp_component_scripts']['amp-iframe'] ) ) {
                $data['amp_component_scripts']['amp-iframe'] = 'https://cdn.ampproject.org/v0/amp-iframe-latest.js';
        }
        return $data;         
    }
    
    /**
     * Here, We are adding amp analytics tag for every ad serve on page
     */
    public function adsforwp_add_analytics_amp_tags(){
        
        $settings = adsforwp_defaultSettings();
        
        if(isset($settings['ad_performance_tracker'])){
         
        if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
            
        $amp_ads_id = json_decode(get_transient('adsforwp_transient_amp_ids'), true);    
        
        if(!empty($amp_ads_id)){
            
          $amp_ads_id = array_unique($amp_ads_id);  
          
        }   
        
        $ad_impression_script = ''; 
        $ad_clicks_script     = '';
        
        $nonce                = wp_create_nonce('adsforwp_ajax_check_front_nonce');        
        $ad_impression_url    = admin_url('admin-ajax.php?action=adsforwp_insert_ad_impression_amp&adsforwp_front_nonce='.$nonce);                              
        $ad_clicks_url        = admin_url('admin-ajax.php?action=adsforwp_insert_ad_clicks_amp&adsforwp_front_nonce='.$nonce);                              
                
        if($amp_ads_id){
            
         foreach($amp_ads_id as $ad_id){
             
            $ad_impression_script .= '<amp-analytics><script type="application/json">
                  {
                    "requests": {
                      "event": "'.esc_url($ad_impression_url).'&event=${eventId}"
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
                                      "event": "'.esc_url($ad_clicks_url).'&event=${eventId}"
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
         
    }
    
    /**
     * Function to insert ad impression for both (AMP and NON AMP)
     * @global type $wpdb
     * @param type $ad_id
     * @param type $device_name
     */
    public function adsforwp_insert_impression($ad_id, $device_name){
                  
                global $wpdb;
                
                $today = adsforwp_get_date('day');
                
                $stats = $wpdb->get_var($wpdb->prepare("SELECT `id` FROM `{$wpdb->prefix}adsforwp_stats` WHERE `ad_id` = %d AND `ad_device_name` = %s AND `ad_thetime` = %d;", $ad_id, trim($device_name), $today ));
                
                if($stats > 0) {
                        $wpdb->query("UPDATE `{$wpdb->prefix}adsforwp_stats` SET `ad_impressions` = `ad_impressions` + 1 WHERE `id` = {$stats};");
                } else {
                        $wpdb->insert($wpdb->prefix.'adsforwp_stats', array('ad_id' => $ad_id, 'ad_thetime' => $today, 'ad_clicks' => 0, 'ad_impressions' => 1, 'ad_device_name' => trim($device_name)));
                }                                                     
        
    }
    
    /**
     * Function to insert ad clicks for both (AMP and NON AMP)
     * @global type $wpdb
     * @param type $ad_id
     * @param type $device_name
     */
    public function adsforwp_insert_clicks($ad_id, $device_name){
        
        
                global $wpdb;
                
                $today = adsforwp_get_date('day');
                
                $stats = $wpdb->get_var($wpdb->prepare("SELECT `id` FROM `{$wpdb->prefix}adsforwp_stats` WHERE `ad_id` = %d AND `ad_device_name` = %s AND `ad_thetime` = %d;", $ad_id, trim($device_name), $today ));
                
                if($stats > 0) {
                        $wpdb->query("UPDATE `{$wpdb->prefix}adsforwp_stats` SET `ad_clicks` = `ad_clicks` + 1 WHERE `id` = {$stats};");
                } else {
                        $wpdb->insert($wpdb->prefix.'adsforwp_stats', array('ad_id' => $ad_id, 'ad_thetime' => $today, 'ad_clicks' => 0, 'ad_impressions' => 1, 'ad_device_name' => trim($device_name)));
                }        
        
    }
    
    /**
     * Ajax handler to get ad impression in AMP
     * @return type void
     */
    public function adsforwp_insert_ad_impression_amp(){  
           
            if ( ! isset( $_GET['adsforwp_front_nonce'] ) ){
                return; 
             }
            if ( !wp_verify_nonce( $_GET['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ){
               return;  
            }  
                               
           $ad_id       = sanitize_text_field($_GET['event']);           
           $device_name = 'amp';           
           
           if($ad_id){
               $this->adsforwp_insert_impression($ad_id, $device_name);
           }
                                      
           wp_die();           
    }        
    
    /**
     * Ajax handler to get ad impression in NON AMP
     * @return type void
     */
    public function adsforwp_insert_ad_impression(){  
        
            if ( ! isset( $_POST['adsforwp_front_nonce'] ) ){
                return; 
             }
            if ( !wp_verify_nonce( $_POST['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ){
               return;  
            }  
            
            $ad_ids = array_map('sanitize_text_field', $_POST['ad_ids']);
                        
            $device_name = sanitize_text_field($_POST['device_name']);            
            
            if($ad_ids){
                
                foreach ($ad_ids as $ad_id){
                    
                 if($ad_id){
                     
                    $this->adsforwp_insert_impression($ad_id, $device_name);
                    
                  }
                }//Foreach closed     
            }        
           wp_die();           
    }
    
    /**
     * Ajax handler to get ad clicks in NON AMP
     * @return type void
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
            
            if($ad_id){     
              
                $this->adsforwp_insert_clicks($ad_id, $device_name);
                              
            }                           
           wp_die();           
    }
    
    /**
     * Ajax handler to get ad clicks in AMP
     * @return type void
     */
    public function adsforwp_insert_ad_clicks_amp(){        
        
            if ( ! isset( $_GET['adsforwp_front_nonce'] ) ){
                return; 
             }
            if ( !wp_verify_nonce( $_GET['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ){
               return;  
            }  
            
            $ad_id = sanitize_text_field($_GET['event']);
            $device_name = 'amp';            
            
            if($ad_id){    
                
                $this->adsforwp_insert_clicks($ad_id, $device_name);
                
            }                           
           wp_die();           
    }

}
if (class_exists('adsforwp_admin_analytics')) {
	$adsforwp_analytics_hooks_obj =new adsforwp_admin_analytics;
        $adsforwp_analytics_hooks_obj->adsforwp_admin_analytics_hooks();        
};
