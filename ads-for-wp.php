<?php
/*
Plugin Name: Ads for WP
Plugin URI: https://wordpress.org/plugins/ads-for-wp/
Description: Ads for WP - The best Advertisement plugin in WordPress
Version: 1.1.2
Author: Ahmed Kaludi, Mohammed Kaludi
Author URI: https://ampforwp.com/
Donate link: https://www.paypal.me/Kaludi/25
License: GPL2+
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('ADSFORWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('ADSFORWP_PLUGIN_DIR_URI', plugin_dir_url(__FILE__));
define('ADSFORWP_IMAGE_DIR',plugin_dir_url(__FILE__).'images');
define('ADSFORWP_PLUGIN_DIR_PATH', plugin_dir_path( __DIR__ ) );


if ( ! defined( 'ADSFORWP_VERSION' ) ) {
	define( 'ADSFORWP_VERSION', '1.1.2' );
}
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'ADSFORWP_STORE_URL', 'https://accounts.ampforwp.com/' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'ADSFORWP_ITEM_NAME', 'ADS for WP' );

// the download ID. This is the ID of your product in EDD and should match the download ID visible in your Downloads list (see example below)
//define( 'AMPFORWP_ITEM_ID', 2502 );
// the name of the settings page for the license input to be displayed
define( 'ADSFORWP_LICENSE_PAGE', 'ads-for-wp-license' );
if(! defined('AMP_ADSFORWP_ITEM_FOLDER_NAME')){
    $folderName = basename(__DIR__);
    define( 'AMP_ADSFORWP_ITEM_FOLDER_NAME', $folderName );
}


/* Adding Files*/
require ( ADSFORWP_PLUGIN_DIR.'/widget/ads-widget.php' );
// Options panel
require ( ADSFORWP_PLUGIN_DIR.'/admin/control-center.php' );
require ( ADSFORWP_PLUGIN_DIR.'/admin/global-metaboxes.php' );
require_once  ADSFORWP_PLUGIN_DIR . '/includes/options.php';
// Adding CMB2
require_once  ADSFORWP_PLUGIN_DIR . '/includes/cmb2/init.php';

// Adding necessary files
require_once  ADSFORWP_PLUGIN_DIR . '/ads/functions.php';
require_once  ADSFORWP_PLUGIN_DIR . '/ads/adsense.php';
require_once  ADSFORWP_PLUGIN_DIR . '/ads/dfp.php';
require_once  ADSFORWP_PLUGIN_DIR . '/ads/custom.php';
require_once  ADSFORWP_PLUGIN_DIR . '/ads/media-net.php';


/*
 * Advertisement Controller
 * 
 * Want to hide add the ads in the current page?
 * Pass 'no' adsforwp_advert_on_off filter 
*/
add_filter('adsforwp_advert_on_off', 'adsforwp_hide_ads_controller');
function adsforwp_hide_ads_controller($show) {
	global $post;

	if ( is_singular()  ) { 
		$content 		= get_post_field('post_content', $post->ID );
		$content_count 	= str_word_count($content);
	}

	$current = adsforwp_get_meta_post( 'adsforwp_ads_meta_box_ads_on_off' );

	if ( $current === 'hide' ) {
		$show = 'no';
	}
	return $show;

}

// Get all the post types and add metaboxs of the ads in this post types
add_action('admin_init', 'adsforwp_generate_postype');
function adsforwp_generate_postype(){
	adsforwp_post_types();
}

function adsforwp_post_types(){
	$args 			= "";
	$get_post_types = "";
	$post_types 	= array();

	$args = array(
	   'public'   => true,
	);

	$get_post_types = get_post_types($args);

	// Remove the unwanted post types 
	unset($get_post_types['attachment']);
	unset($get_post_types['ads-for-wp-ads']);
	 
	foreach ( $get_post_types  as $post_type  ) {
	
		$post_types[] = $post_type;
	}
	$post_types = apply_filters( 'adsforwp_modify_post_types', $post_types );

	return $post_types;
}


/***
* License Activation code
***/

require_once dirname( __FILE__ ) . '/updater/EDD_SL_Plugin_Updater.php';

// Check for updates
function ads_for_wp_plugin_updater() {

	// retrieve our license key from the DB
	//$license_key = trim( get_option( 'amp_ads_license_key' ) );
	$selectedOption = get_option('redux_builder_amp',true);
    $license_key = '';//trim( get_option( 'amp_ads_license_key' ) );
    $pluginItemName = '';
    $pluginItemStoreUrl = '';
    $pluginstatus = '';
    if( isset($selectedOption['amp-license']) && "" != $selectedOption['amp-license'] && isset($selectedOption['amp-license'][AMP_ADSFORWP_ITEM_FOLDER_NAME])){

       $pluginsDetail = $selectedOption['amp-license'][AMP_ADSFORWP_ITEM_FOLDER_NAME];
       $license_key = $pluginsDetail['license'];
       $pluginItemName = $pluginsDetail['item_name'];
       $pluginItemStoreUrl = $pluginsDetail['store_url'];
       $pluginstatus = $pluginsDetail['status'];
    }
	
	// setup the updater
	$edd_updater = new ADSFORWP_EDD_SL_Plugin_Updater( ADSFORWP_STORE_URL, __FILE__, array(
			'version' 	=> ADSFORWP_VERSION, 				// current version number
			'license' 	=> $license_key, 						// license key (used get_option above to retrieve from DB)
			'license_status'=>$pluginstatus,
			'item_name' => ADSFORWP_ITEM_NAME, 			// name of this plugin
			'author' 	=> 'Mohammed Kaludi',  					// author of this plugin
			'beta'		=> false,
		)
	);
}
add_action( 'admin_init', 'ads_for_wp_plugin_updater', 0 );

// Notice to enter license key once activate the plugin

$path = plugin_basename( __FILE__ );
	add_action("after_plugin_row_{$path}", function( $plugin_file, $plugin_data, $status ) {
		global $redux_builder_amp;
		if(! defined('AMP_ADSFORWP_ITEM_FOLDER_NAME')){
	    $folderName = basename(__DIR__);
            define( 'AMP_ADSFORWP_ITEM_FOLDER_NAME', $folderName );
        }
        $pluginsDetail = $redux_builder_amp['amp-license'][AMP_ADSFORWP_ITEM_FOLDER_NAME];
        $pluginstatus = $pluginsDetail['status'];

        if(empty($redux_builder_amp['amp-license'][AMP_ADSFORWP_ITEM_FOLDER_NAME]['license'])){
			echo "<tr class='active'><td>&nbsp;</td><td colspan='2'><a href='".esc_url(  self_admin_url( 'admin.php?page=amp_options&tabid=opt-go-premium' )  )."'>Please enter the license key</a> to get the <strong>latest features</strong> and <strong>stable updates</strong></td></tr>";
			   }elseif($pluginstatus=="valid"){
			   	$update_cache = get_site_transient( 'update_plugins' );
            $update_cache = is_object( $update_cache ) ? $update_cache : new stdClass();
            if(isset($update_cache->response[ AMP_ADSFORWP_ITEM_FOLDER_NAME ]) 
                && empty($update_cache->response[ AMP_ADSFORWP_ITEM_FOLDER_NAME ]->download_link) 
              ){
               unset($update_cache->response[ AMP_ADSFORWP_ITEM_FOLDER_NAME ]);
            }
            set_site_transient( 'update_plugins', $update_cache );
            
        }
    }, 10, 3 );


