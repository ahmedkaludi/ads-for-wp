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

/*
 * Advertisement Controller
 * 
 * Want to hide add the ads in the current page?
 * Pass 'no' adsforwp_advert_on_off filter 
*/
add_filter('adsforwp_advert_on_off', 'adsforwp_hide_ads_controller',10,2);
function adsforwp_hide_ads_controller($show,$id) {
  global $post;

  if ( is_singular()  ) { 
    $content     = get_post_field('post_content', $post->ID );
    $content_count   = str_word_count($content);
  }

  // Hide ads on the page where content is less then 150 words
   if ( $content_count && $content_count < 150 ) {
     return $show = 'no';
   }

  $current = adsforwp_get_meta_post( 'adsforwp_ads_meta_box_ads_on_off' );

  if ( $current === 'hide' ) {
    $show = 'no';
  }

       $post_meta         = array();
    $current_ads_status = '';

    $post_meta[] = get_post_meta($id, 'adsforwp_ads_controller_default', true);
     
    foreach ($post_meta as $current_ads_status ) {
      if ( 'hide' === $current_ads_status ) {
        $show = 'no';
        return $show;
      }
      elseif('show' === $current_ads_status){
        $show = 'yes';
        return $show;
      }

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
	$post_types 	= "";

	$args = array(
	   'public'   => true,
	);

	$get_post_types = get_post_types( $args, 'objects');

	// Remove the unwanted post types 
	unset($get_post_types['attachment']);
	unset($get_post_types['ads-for-wp-ads']);

	foreach ( $get_post_types  as $post_type ) {
		$post_types[$post_type->name] = $post_type->label;
	}

	$post_types = apply_filters( 'adsforwp_modify_post_types', $post_types );

	return $post_types;
}

function adsforwp_global_checker($id){
	$atts['ads-id']= $id;
	$current_stat = adsforwp_shortcode_generator($atts);
	if ( empty($current_stat) ) {
		return false;
	}

	return true;
}