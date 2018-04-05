<?php
/*
Plugin Name: Ads for WP
Plugin URI: https://wordpress.org/plugins/ads-for-wp/
Description: Ads for WP - The best Advertisement plugin in WordPress
Version: 1.0.9
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
	define( 'ADSFORWP_VERSION', '1.0.9' );
}
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'ADSFORWP_STORE_URL', 'https://accounts.ampforwp.com/' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'ADSFORWP_ITEM_NAME', 'ADS for WP' );

// the download ID. This is the ID of your product in EDD and should match the download ID visible in your Downloads list (see example below)
//define( 'AMPFORWP_ITEM_ID', 2502 );
// the name of the settings page for the license input to be displayed
define( 'ADSFORWP_LICENSE_PAGE', 'ads-for-wp-license' );


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


// Let's start displaying Ads On AMP

// Funtion to get the ad's id which are custom post types

function get_ad_id($id){
	$meta_details = get_metadata('post',$id,'adsforwp-advert-data',true);
	$post_ad_data = $meta_details;
	$post_id 	  = $id;
	$post_ad_id   = '';
	$all_ads_post = get_posts( array( 'post_type' => 'ads-for-wp-ads','posts_per_page' => -1));
	foreach ($all_ads_post as $ads) {
		$post_ad_id = $ads->ID;
		$ad_position = get_post_meta($post_ad_id,'normal_ad_type',true);
		if('12' === $ad_position){
			$inbetween_id = $post_ad_id;
			return $inbetween_id;
		}
		if('13' === $ad_position){
			$inbetween_id = $post_ad_id;
			return $inbetween_id;
		}
	}
	// foreach ($post_ad_data as $key => $ad_config) {
	// 	$post_ad_id = $ad_config['ads_id'];
	// 	$post_ad_id = (int)$post_ad_id;
	// }
	return $post_ad_id;
}

add_action('pre_amp_render_post','ampforwp_display_amp_ads');
function ampforwp_display_amp_ads(){
	$all_ads_post = get_posts( array( 'post_type' => 'ads-for-wp-ads','posts_per_page' => -1));
	
	foreach ($all_ads_post as $ads) {
		$post_ad_id = $ads->ID;
		$args = array (
	    	'id'        =>  $post_ad_id, // id
	    );

		$selected_ads_for 	= get_post_meta($post_ad_id,'select_ads_for',true);
		$ad_type 			= get_post_meta($post_ad_id,'ad_type_format',true);
		$ad_vendor			= get_post_meta($post_ad_id,'ad_vendor',true);
		$visibility_status  = get_post_meta($post_ad_id,'ad_vendor',true);
		
		if('1' === $selected_ads_for) {
				$global_visibility  = get_post_meta($post_ad_id,'ad_visibility_status',true);
			if($global_visibility != 'hide') {
				// Normal Ads
				if('1' === $ad_type){
					$ad_position = get_post_meta($post_ad_id,'normal_ad_type',true);
					switch ($ad_position) {

						case '1':
							//  "Above Header";
							 if('1' === $ad_vendor){
							 		// $id = get_ad_id(get_the_ID());
									 add_action('ampforwp_header_top_design2','ampforwp_adsense_ads');
								}
								else if('2' === $ad_vendor){
									add_action('ampforwp_header_top_design2','ampforwp_dfp_ads');
								}
								else if('3' === $ad_vendor){
									add_action('ampforwp_header_top_design2','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_header_top_design2',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '2':
							//  "Below Header";
								if('1' === $ad_vendor){
									 add_action('ampforwp_after_header','ampforwp_adsense_ads');
								}
								else if('2' === $ad_vendor){
									add_action('ampforwp_after_header','ampforwp_dfp_ads');
								}
								else if('3' === $ad_vendor){
									add_action('ampforwp_after_header','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_after_header',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '3':
							//  "Before Title";
								//  Adsense Ad
								if('1' === $ad_vendor){
									 add_action('ampforwp_above_the_title','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('ampforwp_above_the_title','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('ampforwp_above_the_title','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_above_the_title',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '4':
							//  "After Title";
							 	if('1' === $ad_vendor){
									 add_action('ampforwp_below_the_title','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('ampforwp_below_the_title','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('ampforwp_below_the_title','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_below_the_title',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '5':
							//  "Before Content";
								if('1' === $ad_vendor){
									 add_action('ampforwp_before_post_content','ampforwp_adsense_ads');
								} 
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('ampforwp_before_post_content','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('ampforwp_before_post_content','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_before_post_content',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '6':
							//  "After Featured Image";
							 	if('1' === $ad_vendor){
									 add_action('ampforwp_after_featured_image_hook','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('ampforwp_after_featured_image_hook','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('ampforwp_after_featured_image_hook','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_after_featured_image_hook',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '7':
							//  "After Content";
							 	if('1' === $ad_vendor){
							 		if(is_single()){
									 add_action('ampforwp_after_post_content','ampforwp_adsense_ads');
							 		}
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									if(is_single()){
										add_action('ampforwp_after_post_content','ampforwp_dfp_ads');
									}
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									if(is_single()){
										add_action('ampforwp_after_post_content','ampforwp_custom_ads');
									}
								}
								else if('4' === $ad_vendor){
									if(is_single()){
										add_action('ampforwp_after_post_content',function() use ( $args ) { 
		              						 adsforwp_media_net_ads( $args ); });
									}
								}
							break;
						case '8':
							//  "Above Related Posts";
							 	if('1' === $ad_vendor){
									 add_action('ampforwp_above_related_post','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('ampforwp_above_related_post','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('ampforwp_above_related_post','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_above_related_post',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '9':
							//  "Below Related Posts";
							 	if('1' === $ad_vendor){
									 add_action('ampforwp_below_related_post','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('ampforwp_below_related_post','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('ampforwp_below_related_post','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_below_related_post',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '10':
							//  "Before Footer";
							 	if('1' === $ad_vendor){
									 add_action('amp_post_template_above_footer','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('amp_post_template_above_footer','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('amp_post_template_above_footer','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('amp_post_template_above_footer',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '11':
							//  "After Footer";
							 	if('1' === $ad_vendor){
									 add_action('ampforwp_global_after_footer','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									 add_action('ampforwp_global_after_footer','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									 add_action('ampforwp_global_after_footer','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_global_after_footer',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						default:
							//  "please select the postion to display";
							break;
					}

				}
			}
		}

		// FOR AMP BY AUTOMATTIC Normal And Incontent Ads
		elseif('2' === $selected_ads_for) {
			$amp_ad_type 			= get_post_meta($post_ad_id,'_amp_ad_type_format',true);
			$amp_ad_vendor			= get_post_meta($post_ad_id,'_amp_ad_vendor',true);
			$global_visibility  = get_post_meta($post_ad_id,'_amp_ad_visibility_status',true);
			if($global_visibility != 'hide'){
			// Normal
				if('1' === $amp_ad_type){
					switch ($amp_ad_vendor) {
						case '1':
							add_action('amp_post_template_footer','ampforwp_adsense_ads');
							break;
						case '2':
							add_action('amp_post_template_footer','ampforwp_dfp_ads');
							break;
						case '3':
							add_action('amp_post_template_footer','ampforwp_custom_ads');
							break;
						case '4':
							add_action('amp_post_template_footer','adsforwp_media_net_ads');
							break;
						default:
							add_action('amp_post_template_footer','ampforwp_adsense_ads');
							break;
					}
					
				}
			}
		}
	}
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
    if( isset($selectedOption['amp-license']) && "" != $selectedOption['amp-license'] && isset($selectedOption['amp-license']['ads-for-wp'])){

       $pluginsDetail = $selectedOption['amp-license']['ads-for-wp'];
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
	
		if(empty($redux_builder_amp['amp-license']['ads-for-wp']['license'])){
			echo "<tr class='active'><td>&nbsp;</td><td colspan='2'><a href='".esc_url(  self_admin_url( 'admin.php?page=amp_options&tab=2' )  )."'>Please enter the license key</a> to get the <strong>latest features</strong> and <strong>stable updates</strong></td></tr>";
			    }
	}, 10, 3 );


