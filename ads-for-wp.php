<?php
/*
Plugin Name: Ads for WP - Advanced Ads & Adsense Solution for WP & AMP
Plugin URI: https://wordpress.org/plugins/ads-for-wp/
Description: ADs for WP is an Advanced Ad Inserter solution built for WordPress & AMP. Easy to Use, Unlimited Incontent Ads, Adsense, Premium Features and more
Version: 1.2
Author: Ahmed Kaludi, Mohammed Kaludi
Author URI: http://adsforwp.com/
Donate link: https://www.paypal.me/Kaludi/25usd
Text Domain: ads-for-wp
Domain Path: /languages
License: GPL2+
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('ADSFORWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('ADSFORWP_PLUGIN_DIR_URI', plugin_dir_url(__FILE__));
define( 'ADSFORWP_LIB_PATH', dirname( __FILE__ ) . '/admin/inc/' );
if ( ! defined( 'ADSFORWP_VERSION' ) ) {
	define( 'ADSFORWP_VERSION', '1.2' );
}
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
        $review_notice_bar_status_date = get_option( "review_notice_bar_close_date");        
        $review_notice_bar_never = get_option( "adsforwp_review_never");
        
        if(in_array($current_date,$list_of_date) && $review_notice_bar_status_date !=$current_date && $review_notice_bar_never !='never'){
           echo '<div class="updated notice is-dismissible message notice notice-alt adsforwp-feedback-notice">
                <p><span class="dashicons dashicons-thumbs-up"></span> 
                '.esc_html__('You have been using the Ads For WP plugin for some time now, do you like it?, If so,', 'ads-for-wp').'						
                <a target="_blank" href="https://wordpress.org/plugins/ads-for-wp/#reviews">				
                '.esc_html__('please write us a review', 'ads-for-wp').'              
                </a> 
                <button style="margin-left:10px;" class="button button-primary adsforwp-feedback-notice-remindme">'.esc_html__('Remind Me Later', 'ads-for-wp').'</button>
                <button style="margin-left:10px;" class="button button-primary adsforwp-feedback-notice-close">'.esc_html__('No Thanks', 'ads-for-wp').'</button> '
                    . ' </p> </div>';                       
        }  
}

add_filter('plugin_row_meta' , 'adsforwp_add_plugin_meta_links', 10, 2);

function adsforwp_add_plugin_meta_links($meta_fields, $file) {
    if ( plugin_basename(__FILE__) == $file ) {
      $plugin_url = "https://wordpress.org/support/plugin/ads-for-wp";  
      $hire_url = "https://ampforwp.com/hire/";
      $meta_fields[] = "<a href='" . esc_url($plugin_url) . "' target='_blank'>" . esc_html__('Support Forum', 'ads-for-wp') . "</a>";
      $meta_fields[] = "<a href='" . esc_url($hire_url) . "' target='_blank'>" . esc_html__('Hire Us', 'ads-for-wp') . "</a>";
      $meta_fields[] = "<a href='" . esc_url($plugin_url) . "/reviews#new-post' target='_blank' title='" . esc_html__('Rate', 'ads-for-wp') . "'>
            <i class='adsforwp-wdi-rate-stars'>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "</i></a>";      
      echo "<style>"
        . ".adsforwp-wdi-rate-stars{display:inline-block;color:#ffb900;position:relative;top:3px;}"
        . ".adsforwp-wdi-rate-stars svg{fill:#ffb900;}"
        . ".adsforwp-wdi-rate-stars svg:hover{fill:#ffb900}"
        . ".adsforwp-wdi-rate-stars svg:hover ~ svg{fill:none;}"
        . "</style>";
    }

    return $meta_fields;
  }