<?php
/*
Plugin Name: Ads for WP
Plugin URI: https://wordpress.org/plugins/ads-for-wp/
Description: Ads for WP - The best Advertisement plugin in WordPress
Version: 1.1.2
Author: Ahmed Kaludi, Mohammed Kaludi
Author URI: https://ampforwp.com/
Donate link: https://www.paypal.me/Kaludi/25
Text Domain: ads-for-wp
License: GPL2+
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('ADS_FOR_WP_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('ADS_FOR_WP_PLUGIN_DIR_URI', plugin_dir_url(__FILE__));
define('ADS_FOR_WP_IMAGE_DIR',plugin_dir_url(__FILE__).'images');
define('ADS_FOR_WP_PLUGIN_DIR_PATH', plugin_dir_path( __DIR__ ) );


if ( ! defined( 'ADS_FOR_WP_VERSION' ) ) {
	define( 'ADS_FOR_WP_VERSION', '1.1.2' );
}
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'ADS_FOR_WP_STORE_URL', 'https://accounts.ampforwp.com/' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file
// the name of your product. This should match the download name in EDD exactly
define( 'ADS_FOR_WP_ITEM_NAME', 'ADS for WP' );
// the download ID. This is the ID of your product in EDD and should match the download ID visible in your Downloads list (see example below)
//define( 'AMPFORWP_ITEM_ID', 2502 );
// the name of the settings page for the license input to be displayed
define( 'ADS_FOR_WP_LICENSE_PAGE', 'ads-for-wp-license' );
if(! defined('AMP_ADS_FOR_WP_ITEM_FOLDER_NAME')){
    $folderName = basename(__DIR__);
    define( 'AMP_ADS_FOR_WP_ITEM_FOLDER_NAME', $folderName );
}
/* Adding Files*/
//require ( ADSFORWP_PLUGIN_DIR.'/widget/ads-widget.php' );
// Options panel
require ( ADS_FOR_WP_PLUGIN_DIR.'/admin/control-center.php' );
//Loading custom admin menu
require  ADS_FOR_WP_PLUGIN_DIR . '/settings/function-admin.php';

//Loading Metaboxes
require ( ADS_FOR_WP_PLUGIN_DIR.'/metaboxes/ads-type-metabox.php' );
require ( ADS_FOR_WP_PLUGIN_DIR.'/metaboxes/display-metabox.php' );
require ( ADS_FOR_WP_PLUGIN_DIR.'/metaboxes/ads-visibility-metabx.php' );

add_action( 'admin_init', 'check_some_other_plugin' );
function check_some_other_plugin() {
  if ( is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php') ) {
    require ( ADS_FOR_WP_PLUGIN_DIR.'/metaboxes/amp-metabox.php' );	
  }
}//Loading ads vendor files
require_once  ADS_FOR_WP_PLUGIN_DIR . '/ads-vendor/functions.php';