<?php
/*
Plugin Name: Ads for WP - Advanced Ads & Adsense Solution for WP & AMP
Plugin URI: https://wordpress.org/plugins/ads-for-wp/
Description: ADs for WP is an Advanced Ad Inserter solution built for WordPress & AMP. Easy to Use, Unlimited Incontent Ads, Adsense, Premium Features and more
Version: 1.0.7
Author: Ahmed Kaludi, Mohammed Kaludi
Author URI: http://adsforwp.com/
Donate link: https://www.paypal.me/Kaludi/25usd
Text Domain: ads-for-wp
License: GPL2+
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('ADSFORWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('ADSFORWP_PLUGIN_DIR_URI', plugin_dir_url(__FILE__));
define( 'ADSFORWP_LIB_PATH', dirname( __FILE__ ) . '/admin/inc/' );
if ( ! defined( 'ADSFORWP_VERSION' ) ) {
	define( 'ADSFORWP_VERSION', '1.0.7' );
}
require_once ABSPATH . "wp-includes/pluggable.php";
/* Loading Backend files files*/
require_once  ADSFORWP_PLUGIN_DIR.'/admin/control-center.php';
require_once  ADSFORWP_PLUGIN_DIR.'/admin/ads-newsletter.php';
require_once  ADSFORWP_PLUGIN_DIR.'/admin/ads-widget.php';
require_once  ADSFORWP_PLUGIN_DIR.'/admin/common-functions.php';
require_once  ADSFORWP_PLUGIN_DIR.'/admin/settings.php';

require_once  ADSFORWP_PLUGIN_DIR.'/admin/inc/analytics-settings.php';
require_once  ADSFORWP_PLUGIN_DIR.'/admin/inc/analytics-common-functions.php';

require_once  ADSFORWP_PLUGIN_DIR.'/admin/ajax-selectbox.php';
require_once  ADSFORWP_PLUGIN_DIR.'/admin/file-creation.php';
require_once  ADSFORWP_PLUGIN_DIR.'/admin/analytics-settings.php';
require_once  ADSFORWP_PLUGIN_DIR.'/admin/analytics.php';

/* Loading Metaboxes*/
require_once  ADSFORWP_PLUGIN_DIR.'/view/ads-type.php';
require_once  ADSFORWP_PLUGIN_DIR.'/view/display.php';
require_once  ADSFORWP_PLUGIN_DIR.'/view/ads-visibility.php';
require_once  ADSFORWP_PLUGIN_DIR.'/view/ad-groups.php';
require_once  ADSFORWP_PLUGIN_DIR.'/view/ads-expire.php';
require_once  ADSFORWP_PLUGIN_DIR.'/view/placement.php';
require_once  ADSFORWP_PLUGIN_DIR.'/view/visitor-condition.php';

/* Loading frontend files*/
require_once  ADSFORWP_PLUGIN_DIR.'/output/functions.php';
require_once  ADSFORWP_PLUGIN_DIR.'/output/amp-condition-display.php';


register_activation_hook( __FILE__, 'adsforwp_on_activation' );
function adsforwp_on_activation() {    
    add_option('adsforwp_do_activation_redirect', true);   
    
    set_transient( 'adsforwp_admin_notice_transient', true, 5 );
    update_option( "adsforwp_activation_date", date("Y-m-d"));
}
/* Function to check other plugin is install or not*/
add_action( 'admin_init', 'adsforwp_check_plugin' );
function adsforwp_check_plugin() {
    
    if (get_option('adsforwp_do_activation_redirect', false)) {
        delete_option('adsforwp_do_activation_redirect');
        if(!isset($_GET['activate-multi']))
        {
            $url = esc_url( admin_url( 'edit.php?post_type=adsforwp' ) );
            wp_redirect($url);
            exit;
        }
    }      
  if ( is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php') ) {
         require ADSFORWP_PLUGIN_DIR.'/view/amp-compatibility.php';	
  }
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'adsforwp_action_links' );
function adsforwp_action_links ( $links ) {
        $mylinks = array(
        '<a href="' . admin_url( 'edit.php?post_type=adsforwp&page=adsforwp' ) . '">'.esc_html__('Settings', 'ads-for-wp').'</a>',
        );
return array_merge( $links, $mylinks );
}
/**
 * set user defined message on plugin activate
 */
add_action( 'admin_notices', 'adsforwp_admin_notice' );

function adsforwp_admin_notice(){    
    /* Check transient, if available display notice */
    if( get_transient( 'adsforwp_admin_notice_transient' ) ){
        echo '<div class="updated notice is-dismissible message notice notice-alt adsforwp-setup-notice">
             <p><span class="dashicons dashicons-thumbs-up"></span>'.esc_html__('Thank you for using Ads For WP plugin!', 'ads-for-wp').'               
             <a href="'.esc_url( admin_url( 'edit.php?post_type=adsforwp' ) ).'"> '.esc_html__('Start adding ads', 'schema-and-structured-data-for-wp') .'</a>
             </p></div>';             
        /* Delete transient, only display this notice once. */
        delete_transient( 'adsforwp_admin_notice_transient' );
    }             
     //Feedback notice
        $activation_date =  get_option("adsforwp_activation_date");  

        $one_day    = date('Y-m-d',strtotime("+1 day",  strtotime($activation_date))); 
        $seven_days = date('Y-m-d',strtotime("+7 day",  strtotime($activation_date)));
        $one_month  = date('Y-m-d',strtotime("+30 day", strtotime($activation_date)));
        $sixty_days = date('Y-m-d',strtotime("+60 day", strtotime($activation_date)));
        $six_month  = date('Y-m-d',strtotime("+180 day", strtotime($activation_date)));
        $one_year   = date('Y-m-d',strtotime("+365 day", strtotime($activation_date))); 

        $current_date = date("Y-m-d");    
        $list_of_date = array($one_day, $seven_days, $one_month, $sixty_days, $six_month, $one_year);
    
    if(in_array($current_date,$list_of_date)){
        echo '<div class="updated notice is-dismissible message notice notice-alt adsforwp-feedback-notice">
            <p><span class="dashicons dashicons-thumbs-up"></span> 
            '.esc_html__('You have been using the Ads For WP plugin for some time now, do you like it?, If so,', 'ads-for-wp').'						
            <a target="_blank" href="https://wordpress.org/plugins/ads-for-wp/#reviews">				
	    '.esc_html__('please write us a review', 'ads-for-wp').'
	    </a></p> </div>';                       
    }  
}


