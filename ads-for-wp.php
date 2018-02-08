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
	$id 				= get_the_ID();
	$post_ad_id 		= get_ad_id($id);
	$selected_ads_for 	= get_post_meta($post_ad_id,'select_ads_for',true);
	$ad_type 			= get_post_meta($post_ad_id,'ad_type_format',true);
	$ad_vendor			= get_post_meta($post_ad_id,'ad_vendor',true);
	if('1' === $selected_ads_for){
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
					break;
				case '7':
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
					break;
				default:
					//  "please select the postion to display";
					break;
			}

		}
	

		// InContent Ads
		else if('2' === $ad_type){
			$ad_position = get_post_meta($post_ad_id,'incontent_ad_type',true);
			switch ($ad_vendor) {
				case '1':
					add_filter('the_content', 'ampforwp_incontent_adsense_ads');;
					break;
				case '2':
					add_filter('the_content','ampforwp_incontent_dfp_ads');
					break;
				case '3':
					add_filter('the_content','ampforwp_incontent_custom_ads');
					break;
				default:
					add_filter('the_content','ampforwp_incontent_adsense_ads');
					break;
			};		
		}
	}
// FOR AMP BY AUTOMATTIC Normal And Incontent Ads
	elseif('2' === $selected_ads_for){
		$amp_ad_type 			= get_post_meta($post_ad_id,'_amp_ad_type_format',true);
		$amp_ad_vendor			= get_post_meta($post_ad_id,'_amp_ad_vendor',true);
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
				default:
					add_action('amp_post_template_footer','ampforwp_adsense_ads');
					break;
			}
			
		}
		// InContent
		elseif ('2' === $amp_ad_type) {
			$ad_position = get_post_meta($post_ad_id,'_amp_incontent_ad_type',true);
			switch ($amp_ad_vendor) {
				case '1':
					add_filter('the_content', 'ampforwp_incontent_adsense_ads');;
					break;
				case '2':
					add_filter('the_content','ampforwp_incontent_dfp_ads');
					break;
				case '3':
					add_filter('the_content','ampforwp_incontent_custom_ads');
					break;
				default:
					add_filter('the_content','ampforwp_incontent_adsense_ads');
					break;
			}
				
		}
	}
}

// Adsense Ad code generator 

function ampforwp_adsense_ads(){

	$post_adsense_ad_id = get_ad_id(get_the_ID());
	$selected_ads_for 	= get_post_meta($post_adsense_ad_id,'select_ads_for',true);
	$dimensions 		= get_adsense_dimensions($post_adsense_ad_id);
	$width				= $dimensions['width'];
	$height				= $dimensions['height'];
	if('1' === $selected_ads_for){
		$ad_client			= get_post_meta($post_adsense_ad_id,'adsense_ad_client',true);
		$ad_slot			= get_post_meta($post_adsense_ad_id,'adsense_ad_slot',true);
		$ad_parallax		= get_post_meta($post_adsense_ad_id,'adsense_parallax',true);
	}
	elseif('2' === $selected_ads_for){
		$ad_client			= get_post_meta($post_adsense_ad_id,'_amp_adsense_ad_client',true);
		$ad_slot			= get_post_meta($post_adsense_ad_id,'_amp_adsense_ad_slot',true);
		$ad_parallax		= get_post_meta($post_adsense_ad_id,'_amp_adsense_parallax',true);
	}
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
	$selected_ads_for 	= get_post_meta($post_adsense_ad_id,'select_ads_for',true);
	$dimensions 		= get_adsense_dimensions($post_adsense_ad_id);
	$width				= $dimensions['width'];
	$height				= $dimensions['height'];
	if('1' === $selected_ads_for){
		$ad_client			= get_post_meta($post_adsense_ad_id,'adsense_ad_client',true);
		$ad_slot			= get_post_meta($post_ad_id,'adsense_ad_slot',true);
		$ad_parallax		= get_post_meta($post_adsense_ad_id,'adsense_parallax',true);
	}
	elseif('2' === $selected_ads_for){
		$ad_client			= get_post_meta($post_adsense_ad_id,'_amp_adsense_ad_client',true);
		$ad_slot			= get_post_meta($post_adsense_ad_id,'_amp_adsense_ad_slot',true);
		$ad_parallax		= get_post_meta($post_adsense_ad_id,'_amp_adsense_parallax',true);
	}
	
	$ad_code 			= '<amp-ad class="ampforwp_incontent_adsense_ads"
								type="adsense"
								width="'. $width .'"
								height="'. $height .'"
								data-ad-client="'. $ad_client .'"
								data-ad-slot="'. $ad_slot .'"
							></amp-ad>';
	
	return $ad_code;	
}

function ampforwp_sticky_adsense_ads(){

	$sticky_adsense_ad_id = get_ad_id(get_the_ID());
	$ad_code = ampforwp_incontent_adsense_ads($sticky_adsense_ad_id);
	$amp_sticky = '<amp-sticky-ad layout="nodisplay">'.$ad_code.'</amp-sticky-ad>';
	echo $amp_sticky;
	
}

// adsense dimensions 

function get_adsense_dimensions($id){

$selected_ads_for 	= get_post_meta($id,'select_ads_for',true);
	if('1' === $selected_ads_for){
		$is_link			= get_post_meta($id,'adsense_link',true);
		if('on' == $is_link){
			$dimensions = get_post_meta($id,'link_ads_dimensions',true);
			switch ($dimensions) {
				case '1':
				$dimension = array('width' => '120',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '2':
				$dimension = array('width' => '160',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '3':
				$dimension = array('width' => '180',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '4':
				$dimension = array('width' => '200',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '5':
				$dimension = array('width' => '468',
									'height' => '15'
									 );
				return $dimension;
				break;
			
			case '6':
				$dimension = array('width' => '728',
									'height' => '15'
									 );
				return $dimension;
				break;
			default:
			$dimension = array('width' => '120',
								'height' => '90'
								 );
			break;
			}
		}
		$dimensions = get_post_meta($id,'adsense_dimensions',true);
		switch ($dimensions) {
			case '1':
				$dimension = array('width' => '300',
									'height' => '250'
									 );
				return $dimension;
				break;

			case '2':
				$dimension = array('width' => '336',
									'height' => '280'
									 );
				return $dimension;
				break;

			case '3':
				$dimension = array('width' => '728',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '4':
				$dimension = array('width' => '300',
									'height' => '600'
									 );
				return $dimension;
				break;

			case '5':
				$dimension = array('width' => '320',
									'height' => '100'
									 );
				return $dimension;
				break;
			
			case '6':
				$dimension = array('width' => '200',
									'height' => '50'
									 );
				return $dimension;
				break;

			case '7':
				$dimension = array('width' => '320',
									'height' => '50'
									 );
				return $dimension;
				break;

			case '8':
				$dimension = array();
				$dimension['width'] = get_post_meta($id,'adsense_custom_width',true);
				$dimension['height'] = get_post_meta($id,'adsense_custom_height',true);
				return $dimension;
				break;

			default:
				$dimension = array('width' => '300',
									'height' => '200'
									 );
				break;
		}
	}

	if('2' === $selected_ads_for){
		$is_link			= get_post_meta($id,'_amp_adsense_link',true);
		if('on' == $is_link){
			$dimensions = get_post_meta($id,'_amp_link_ads_dimensions',true);
			switch ($dimensions) {
				case '1':
				$dimension = array('width' => '120',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '2':
				$dimension = array('width' => '160',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '3':
				$dimension = array('width' => '180',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '4':
				$dimension = array('width' => '200',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '5':
				$dimension = array('width' => '468',
									'height' => '15'
									 );
				return $dimension;
				break;
			
			case '6':
				$dimension = array('width' => '728',
									'height' => '15'
									 );
				return $dimension;
				break;
			default:
			$dimension = array('width' => '120',
								'height' => '90'
								 );
			break;
			}
		}
		$dimensions = get_post_meta($id,'_amp_adsense_dimensions',true);
		switch ($dimensions) {
			case '1':
				$dimension = array('width' => '300',
									'height' => '250'
									 );
				return $dimension;
				break;

			case '2':
				$dimension = array('width' => '336',
									'height' => '280'
									 );
				return $dimension;
				break;

			case '3':
				$dimension = array('width' => '728',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '4':
				$dimension = array('width' => '300',
									'height' => '600'
									 );
				return $dimension;
				break;

			case '5':
				$dimension = array('width' => '320',
									'height' => '100'
									 );
				return $dimension;
				break;
			
			case '6':
				$dimension = array('width' => '200',
									'height' => '50'
									 );
				return $dimension;
				break;

			case '7':
				$dimension = array('width' => '320',
									'height' => '50'
									 );
				return $dimension;
				break;

			case '8':
				$dimension = array();
				$dimension['width'] = get_post_meta($id,'_amp_adsense_custom_width',true);
				$dimension['height'] = get_post_meta($id,'_amp_adsense_custom_height',true);
				return $dimension;
				break;

			default:
				$dimension = array('width' => '300',
									'height' => '200'
									 );
				break;
		}
	}
}


// DoubleClick Ad Code generator

function ampforwp_dfp_ads(){

	$post_dfp_ad_id = get_ad_id(get_the_ID());
	$selected_ads_for 	= get_post_meta($post_dfp_ad_id,'select_ads_for',true);
	$dimensions 		= get_dfp_dimensions($post_dfp_ad_id);
	$width				= $dimensions['width'];
	$height				= $dimensions['height'];
	if('1' === $selected_ads_for){
		$ad_slot			= get_post_meta($post_dfp_ad_id,'dfp_ad_slot',true);
		$ad_parallax		= get_post_meta($post_dfp_ad_id,'dfp_parallax',true);
	}
	elseif('2' === $selected_ads_for){
		$ad_slot			= get_post_meta($post_dfp_ad_id,'_amp_dfp_parallax',true);
		$ad_parallax		= get_post_meta($post_dfp_ad_id,'_amp_dfp_parallax',true);
	}
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
	$dimensions 		= get_dfp_dimensions($post_dfp_ad_id);
	$width				= $dimensions['width'];
	$height				= $dimensions['height'];
	if('1' === $selected_ads_for){
		$ad_slot			= get_post_meta($post_dfp_ad_id,'dfp_ad_slot',true);
		$ad_parallax		= get_post_meta($post_dfp_ad_id,'dfp_parallax',true);
	}
	elseif('2' === $selected_ads_for){
		$ad_slot			= get_post_meta($post_dfp_ad_id,'_amp_dfp_parallax',true);
		$ad_parallax		= get_post_meta($post_dfp_ad_id,'_amp_dfp_parallax',true);
	}
	$ad_code		= '<amp-ad class="ampforwp_incontent_dfp_ads"
							type="doubleclick"
							width="'. $width .'"
							height="'. $height .'"
							data-slot="'. $data_slot .'"
						></amp-ad>';
	return $ad_code;
}

function ampforwp_dfp_sticky_ads(){

	$sticky_dfp_ad_id = get_ad_id(get_the_ID());
	$ad_code = ampforwp_incontent_dfp_ads($sticky_dfp_ad_id);
	$amp_sticky = '<amp-sticky-ad layout="nodisplay">'.$ad_code.'</amp-sticky-ad>';
	echo $amp_sticky;
}

// DoubleClick dimensions 

function get_dfp_dimensions($id){
	$selected_ads_for 	= get_post_meta($id,'select_ads_for',true);
	if('1' === $selected_ads_for){
		$dimensions = get_post_meta($id,'dfp_dimensions',true);
		switch ($dimensions) {
			case '1':
				$dimension = array('width' => '300',
									'height' => '250'
									 );
				return $dimension;
				break;

			case '2':
				$dimension = array('width' => '336',
									'height' => '280'
									 );
				return $dimension;
				break;

			case '3':
				$dimension = array('width' => '728',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '4':
				$dimension = array('width' => '300',
									'height' => '600'
									 );
				return $dimension;
				break;

			case '5':
				$dimension = array('width' => '320',
									'height' => '100'
									 );
				return $dimension;
				break;
			
			case '6':
				$dimension = array('width' => '200',
									'height' => '50'
									 );
				return $dimension;
				break;

			case '7':
				$dimension = array('width' => '320',
									'height' => '50'
									 );
				return $dimension;
				break;

			case '8':
				$dimension = array();
				$dimension['width'] = get_post_meta($id,'dfp_custom_width',true);
				$dimension['height'] = get_post_meta($id,'dfp_custom_height',true);
				return $dimension;
				break;

			default:
				$dimension = array('width' => '300',
									'height' => '200'
									 );
				return $dimension;
				break;
		}
	}

	if('2' === $selected_ads_for){
		$dimensions = get_post_meta($id,'_amp_dfp_dimensions',true);
		switch ($dimensions) {
			case '1':
				$dimension = array('width' => '300',
									'height' => '250'
									 );
				return $dimension;
				break;

			case '2':
				$dimension = array('width' => '336',
									'height' => '280'
									 );
				return $dimension;
				break;

			case '3':
				$dimension = array('width' => '728',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '4':
				$dimension = array('width' => '300',
									'height' => '600'
									 );
				return $dimension;
				break;

			case '5':
				$dimension = array('width' => '320',
									'height' => '100'
									 );
				return $dimension;
				break;
			
			case '6':
				$dimension = array('width' => '200',
									'height' => '50'
									 );
				return $dimension;
				break;

			case '7':
				$dimension = array('width' => '320',
									'height' => '50'
									 );
				return $dimension;
				break;

			case '8':
				$dimension = array();
				$dimension['width'] = get_post_meta($id,'_amp_dfp_custom_width',true);
				$dimension['height'] = get_post_meta($id,'_amp_dfp_custom_height',true);
				return $dimension;
				break;

			default:
				$dimension = array('width' => '300',
									'height' => '200'
									 );
				return $dimension;
				break;
		}
	}
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

function ampforwp_custom_sticky_ads(){
	$ad_id 				= get_ad_id(get_the_ID());
	$custom_ad_code	    = get_post_meta($ad_id,'custom_ad',true);
	$sticky_ad_code 	= '<div class="ampforwp-sticky-custom-ad amp-sticky-ads">'.$custom_ad_code.'</div>';
	echo $sticky_ad_code; 
}

// AMP Sticky Add

add_action('ampforwp_body_beginning','ampforwp_amp_sticky_ad');

function ampforwp_amp_sticky_ad(){
	$ad_id 		= get_ad_id(get_the_ID());
	$ad_type 	= get_post_meta($ad_id,'ad_type_format',true);
	$ad_vendor	= get_post_meta($ad_id,'ad_vendor',true);
	if('4' === $ad_type){
		if('1' === $ad_vendor){
		 ampforwp_sticky_adsense_ads();
		}
		elseif('2' === $ad_vendor){
			ampforwp_dfp_sticky_ads();
		}
		else{
			ampforwp_custom_sticky_ads();
		}
	}
}

// AMP Auto Ads Support

add_action('ampforwp_body_beginning','ampforwp_adv_amp_auto_ads');

function ampforwp_adv_amp_auto_ads(){
	$ad_id 		= get_ad_id(get_the_ID());
	$ad_type 	= get_post_meta($ad_id,'ad_type_format',true);

	if('5' === $ad_type){
		$ampauto_ad_code	    = get_post_meta($ad_id,'amp_auto_ad_type',true);
		echo $ampauto_ad_code;
	}
}

// added extra css to improve user experiance for sticky ads
add_action( 'amp_post_template_css', 'ampforwp_extra_sticky_css_styles' );
function ampforwp_extra_sticky_css_styles( $amp_template ) {
 ?>
	amp-sticky-ad {
		z-index: 9999
	}

<?php $ad_id 		= get_ad_id(get_the_ID());
	$ad_type 	= get_post_meta($ad_id,'ad_type_format',true);
	$ad_vendor	= get_post_meta($ad_id,'ad_vendor',true);
	if('4' === $ad_type){
	 if('3' === $ad_vendor){?>
	.ampforwp-sticky-custom-ad{
		position: fixed;
		bottom:0;
		text-align: center;
		left: 0;
		width: 100%;
		z-index: 11;
		max-height: 100px;
		box-sizing: border-box;
		opacity: 1;
		background-image: none;
		background-color: #fff;
		box-shadow: 0 0 5px 0 rgba(0,0,0,.2);
		margin-bottom: 0;
		 }
	body{
		padding-bottom: 40px;
	}	
<?php }
}
}

// Adding the required scripts

add_filter( 'amp_post_template_data', 'ampforwp_adsforwp_scripts', 30 );
function ampforwp_adsforwp_scripts( $data ) {
	$post_ad_id = get_ad_id(get_the_ID());
	$show_ads 	= '';
	$show_ads = 'yes';		
	$show_ads = apply_filters('adsforwp_advert_on_off', $show_ads);

	if ( $show_ads != 'yes' ) {
		return $data ; // Do not show ads and return the data as it is
	}

	$ad_type 	= get_post_meta($post_ad_id,'ad_type_format',true);
	$ad_vendor	= get_post_meta($post_ad_id,'ad_vendor',true);

	$adsense_parallax 	= get_post_meta($post_ad_id,'adsense_parallax',true);
	$dfp_parallax 		= get_post_meta($post_ad_id,'dfp_parallax',true);
	$custom_parallax 	= get_post_meta($post_ad_id,'custom_parallax',true);

	if('1' === $ad_type || '2' === $ad_type || '3' === $ad_type || '4' === $ad_type || '5' === $ad_type ) {

			if('1' === $ad_vendor || '2' === $ad_vendor){
				if ( empty( $data['amp_component_scripts']['amp-ad'] ) ) {
					$data['amp_component_scripts']['amp-ad'] = 'https://cdn.ampproject.org/v0/amp-ad-0.1.js';
				}
			}
		}

		if( '4' === $ad_type ){

			if('1' === $ad_vendor || '2' === $ad_vendor){
				if ( empty( $data['amp_component_scripts']['amp-sticky-ad'] ) ) {
					$data['amp_component_scripts']['amp-auto-ads'] = 'https://cdn.ampproject.org/v0/amp-sticky-ad-1.0.js';
				}
			}
		}

		if(	'5' === $ad_type ) {
						
			if ( empty( $data['amp_component_scripts']['amp-auto-ads'] ) ) {
				$data['amp_component_scripts']['amp-auto-ads'] = 'https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js';
			}
		}


		if(	'on' === $adsense_parallax || 'on' === $dfp_parallax || 'on' === $custom_parallax ) {
						
			if ( empty( $data['amp_component_scripts']['amp-fx-flying-carpet'] ) ) {
				$data['amp_component_scripts']['amp-fx-flying-carpet'] = 'https://cdn.ampproject.org/v0/amp-fx-flying-carpet-0.1.js';
			}
		}


	return $data;
}