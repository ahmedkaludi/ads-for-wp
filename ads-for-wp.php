<?php
/*
Plugin Name: Ads for WP
Plugin URI: https://wordpress.org/plugins/ads-for-wp/
Description: Ads for WP - The best Advertisement plugin in WordPress
Version: 0.1
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
define('ADSFORWP_VERSION','0.1');


/* Adding Files*/
require ( ADSFORWP_PLUGIN_DIR.'/widget/ads-widget.php' );
// Options panel
require ( ADSFORWP_PLUGIN_DIR.'/admin/control-center.php' );
require ( ADSFORWP_PLUGIN_DIR.'/admin/global-metaboxes.php' );
require_once  ADSFORWP_PLUGIN_DIR . '/includes/options.php';
// Adding CMB2
require_once  ADSFORWP_PLUGIN_DIR . '/includes/cmb2/init.php';

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

	// Hide ads on the page where content is less then 150 words
 	if ( $content_count && $content_count < 150 ) {
 		return $show = 'no';
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
	$meta_details = get_metadata('post',$id,'adsforwp-advert-data');
	$post_ad_data = $meta_details[0];
	$post_id = $id;

	foreach ($post_ad_data as $key => $ad_config) {
		$post_ad_id = $ad_config['ads_id'];
		$post_ad_id = (int)$post_ad_id;
	}
	return $post_ad_id;
}

add_action('pre_amp_render_post','ampforwp_display_amp_ads');
function ampforwp_display_amp_ads(){
	$id 		= get_the_ID();
	$post_ad_id = get_ad_id($id);
	$ad_type 	= get_post_meta($post_ad_id,'ad_type_format',true);
	$ad_vendor	= get_post_meta($post_ad_id,'ad_vendor',true);
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
				break;
			case '6':
				//  "After Content";
				 	if('1' === $ad_vendor){
						 add_action('ampforwp_after_post_content','ampforwp_adsense_ads');
					}
					// DFP Ad
					else if('2' === $ad_vendor){
						add_action('ampforwp_after_post_content','ampforwp_dfp_ads');
					}
					// Custom Ad
					else if('3' === $ad_vendor){
						add_action('ampforwp_after_post_content','ampforwp_custom_ads');
					}
				break;
			case '7':
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
				break;
			case '8':
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
				break;
			default:
				//  "please select the postion to display";
				break;
		}

	}

	// InContent Ads
	else if('2' === $ad_type){
		$ad_position = get_post_meta($post_ad_id,'incontent_ad_type',true);
		add_filter('the_content', 'adsforwp_insert_ads');		
	}
}

// Adsense Ad code generator 

function ampforwp_adsense_ads(){

	$post_adsense_ad_id = get_ad_id(get_the_ID());
	$width				= get_post_meta($post_adsense_ad_id,'adsense_width',true);
	$height				= get_post_meta($post_adsense_ad_id,'adsense_height',true);
	$ad_client			= get_post_meta($post_adsense_ad_id,'adsense_ad_client',true);
	$ad_slot			= get_post_meta($post_ad_id,'adsense_ad_slot',true);
	$ad_parallax		= get_post_meta($post_adsense_ad_id,'adsense_parallax',true);
	$ad_code 			= '<amp-ad class="ampforwp_adsense_ads"
								type="adsense"
								width="'. $width .'"
								height="'. $height .'"
								data-ad-client="'. $ad_client .'"
								data-ad-slot="'. $ad_slot .'"
							></amp-ad>';
	
		echo $ad_code;	
	
}

function ampforwp_incontent_adsense_ads($id){
	$post_adsense_ad_id = $id;
	if(NULL != $post_adsense_ad_id){
		// do nothing
	}
	else{
		$post_adsense_ad_id = get_ad_id(get_the_ID());
	}
	$width				= get_post_meta($post_adsense_ad_id,'adsense_width',true);
	$height				= get_post_meta($post_adsense_ad_id,'adsense_height',true);
	$ad_client			= get_post_meta($post_adsense_ad_id,'adsense_ad_client',true);
	$ad_slot			= get_post_meta($post_ad_id,'adsense_ad_slot',true);
	$ad_parallax		= get_post_meta($post_adsense_ad_id,'adsense_parallax',true);
	$ad_code 			= '<amp-ad class="ampforwp_incontent_adsense_ads"
								type="adsense"
								width="'. $width .'"
								height="'. $height .'"
								data-ad-client="'. $ad_client .'"
								data-ad-slot="'. $ad_slot .'"
							></amp-ad>';
	
	return $ad_code;	
}

// DoubleClick Ad Code generator

function ampforwp_dfp_ads(){

	$post_dfp_ad_id = get_ad_id(get_the_ID());
	$width			= get_post_meta($post_dfp_ad_id,'dfp_width',true);
	$height			= get_post_meta($post_dfp_ad_id,'dfp_height',true);
	$ad_slot		= get_post_meta($post_dfp_ad_id,'dfp_ad_slot',true);
	$ad_parallax	= get_post_meta($post_dfp_ad_id,'dfp_parallax',true);
	$ad_code		= '<amp-ad class="ampforwp_dfp_ads"
							type="doubleclick"
							width="'. $width .'"
							height="'. $height .'"
							data-slot="'. $data_slot .'"
						></amp-ad>';
	echo $ad_code;
}

function ampforwp_incontent_dfp_ads($id){
	$post_dfp_ad_id = $id;
	if(NULL != $post_dfp_ad_id){
		// do nothing
	}
	else{
		$post_dfp_ad_id = get_ad_id(get_the_ID());
	}
	$width			= get_post_meta($post_dfp_ad_id,'dfp_width',true);
	$height			= get_post_meta($post_dfp_ad_id,'dfp_height',true);
	$ad_slot		= get_post_meta($post_dfp_ad_id,'dfp_ad_slot',true);
	$ad_parallax	= get_post_meta($post_dfp_ad_id,'dfp_parallax',true);
	$ad_code		= '<amp-ad class="ampforwp_incontent_dfp_ads"
							type="doubleclick"
							width="'. $width .'"
							height="'. $height .'"
							data-slot="'. $data_slot .'"
						></amp-ad>';
	return $ad_code;
}

// Custom Ad Code generator

function ampforwp_custom_ads(){

	$post_custom_ad_id = get_ad_id(get_the_ID());
	$custom_ad_code	   = get_post_meta($post_custom_ad_id,'custom_ad',true);
	$ad_code 		   = '<div class="ampforwp_custom_ads">
							'.$custom_ad_code.'
							</div>';
	echo $ad_code;
}

function ampforwp_incontent_custom_ads($id){
	$post_custom_ad_id = $id;
	if(NULL != $post_custom_ad_id){
		// do nothing
	}
	else{
		$post_custom_ad_id = get_ad_id(get_the_ID());
	}
	$custom_ad_code	   = get_post_meta($post_custom_ad_id,'custom_ad',true);
	$ad_code 		   = '<div class="ampforwp_incontent_custom_ads">
							'.$custom_ad_code.'
							</div>';
	return $ad_code;
}