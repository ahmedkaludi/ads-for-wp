<?php
/*
Plugin Name: Ads for WP - Advanced Ads & Adsense Solution for WP & AMP
Plugin URI: https://wordpress.org/plugins/ads-for-wp/
Description: ADs for WP is an Advanced Ad Inserter solution built for WordPress & AMP. Easy to Use, Unlimited Incontent Ads, Adsense, Premium Features and more
Version: 1.0.1
Author: Ahmed Kaludi, Mohammed Kaludi
Author URI: https://ampforwp.com/
Donate link: https://www.paypal.me/Kaludi/25usd
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
require_once  adsforwp_PLUGIN_DIR . '/output/functions.php';
require_once  adsforwp_PLUGIN_DIR . '/admin/common-functions.php';