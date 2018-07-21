<?php
/*
Plugin Name: Ads for WP
Plugin URI: https://wordpress.org/plugins/ads-for-wp/
Description: Ads for WP - The best Advertisement plugin in WordPress
Version: 1.0
Author: Ahmed Kaludi, Mohammed Kaludi
Author URI: https://ampforwp.com/
Donate link: https://www.paypal.me/Kaludi/25
Text Domain: ads-for-wp
License: GPL2+
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('adsforwp_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('adsforwp_PLUGIN_DIR_URI', plugin_dir_url(__FILE__));

if ( ! defined( 'adsforwp_VERSION' ) ) {
	define( 'adsforwp_VERSION', '1.1.2' );
}
/* Adding Files*/
//require ( adsforwp__PLUGIN_DIR.'/widget/ads-widget.php' );
// Options panel
require ( adsforwp_PLUGIN_DIR.'/admin/control-center.php' );
//Loading custom admin menu
//Loading Metaboxes
require ( adsforwp_PLUGIN_DIR.'/metaboxes/ads-type.php' );
require ( adsforwp_PLUGIN_DIR.'/metaboxes/display.php' );
require ( adsforwp_PLUGIN_DIR.'/metaboxes/ads-visibility.php' );

//Function to check other plugin is install or not
add_action( 'admin_init', 'adsforwp_check_plugin' );
function adsforwp_check_plugin() {
  if ( is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php') ) {
    require ( adsforwp_PLUGIN_DIR.'/metaboxes/amp-compatibility.php' );	
  }
}//Loading ads vendor files and function
require_once  adsforwp_PLUGIN_DIR . '/ads-vendor/functions.php';
require_once  adsforwp_PLUGIN_DIR . '/admin/common-functions.php';