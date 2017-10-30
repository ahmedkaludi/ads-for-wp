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
// require ( ADSFORWP_PLUGIN_DIR.'/admin/settings/ads-settings.php' );
require ( ADSFORWP_PLUGIN_DIR.'/admin/control-center.php' );
require ( ADSFORWP_PLUGIN_DIR.'/admin/global-metaboxes.php' );


// add_filter('adsforwp_advert_on_off', 'modify_code');
function modify_code($show) {
	if ( is_archive() ) {
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