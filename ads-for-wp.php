<?php
/*
Plugin Name: Ads for WP - Advanced Ads & Adsense Solution for WP & AMP
Plugin URI: https://wordpress.org/plugins/ads-for-wp/
Description: ADs for WP is an Advanced Ad Inserter solution built for WordPress & AMP. Easy to Use, Unlimited Incontent Ads, Adsense, Premium Features and more
Version: 1.0.4
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

if ( ! defined( 'ADSFORWP_VERSION' ) ) {
	define( 'ADSFORWP_VERSION', '1.0.4' );
}
require_once( ABSPATH . "wp-includes/pluggable.php" );
/* Loading Backend files files*/
require ( ADSFORWP_PLUGIN_DIR.'/admin/control-center.php' );
require ( ADSFORWP_PLUGIN_DIR.'/admin/ads-newsletter.php' );
require ( ADSFORWP_PLUGIN_DIR.'/admin/ads-widget.php' );
require  ADSFORWP_PLUGIN_DIR . '/admin/common-functions.php';
require  ADSFORWP_PLUGIN_DIR . '/admin/settings.php';


/* Loading Metaboxes*/
require ( ADSFORWP_PLUGIN_DIR.'/metaboxes/ads-type.php' );
require ( ADSFORWP_PLUGIN_DIR.'/metaboxes/display.php' );
require ( ADSFORWP_PLUGIN_DIR.'/metaboxes/ads-visibility.php' );
require ( ADSFORWP_PLUGIN_DIR.'/metaboxes/ad-groups.php' );
require ( ADSFORWP_PLUGIN_DIR.'/metaboxes/ads-expire.php' );

/* Loading frontend files*/
require  ADSFORWP_PLUGIN_DIR . '/output/functions.php';

/* Function to check other plugin is install or not*/
add_action( 'admin_init', 'adsforwp_check_plugin' );
function adsforwp_check_plugin() {
  if ( is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php') ) {
    require ( ADSFORWP_PLUGIN_DIR.'/metaboxes/amp-compatibility.php' );	
  }
}



