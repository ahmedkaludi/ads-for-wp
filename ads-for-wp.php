<?php
/*
Plugin Name: Easy Google Adsense and Banner Ads Manager - AdsforWP
Plugin URI: https://wordpress.org/plugins/ads-for-wp/
Description: AdsforWP is an Google Ads & Banner ads plugin built for WordPress & AMP. Easy to Use, Unlimited Incontent Ads, Adsense, Premium Features and more
Version: 1.9.33
Author: Magazine3
Author URI: http://adsforwp.com/
Donate link: https://www.paypal.me/Kaludi/25usd
Text Domain: ads-for-wp
Domain Path: /languages
License: GPL2+
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ADSFORWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ADSFORWP_PLUGIN_DIR_URI', plugin_dir_url( __FILE__ ) );
define( 'ADSFORWP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'ADSFORWP_LIB_PATH', __DIR__ . '/admin/inc/' );
if ( ! defined( 'ADSFORWP_VERSION' ) ) {
	define( 'ADSFORWP_VERSION', '1.9.33' );
}

//define( 'ADSFORWP_ENVIRONMENT', 'DEV' );
define( 'ADSFORWP_ENVIRONMENT', 'PRO' );

/* Loading Backend files files*/
require_once ADSFORWP_PLUGIN_DIR . '/admin/ads-setup.php';
require_once ADSFORWP_PLUGIN_DIR . '/admin/control-center.php';
require_once ADSFORWP_PLUGIN_DIR . '/admin/class-adsforwp-ads-newsletter.php';
require_once ADSFORWP_PLUGIN_DIR . '/admin/class-adsforwp-ads-widget.php.php';
require_once ADSFORWP_PLUGIN_DIR . '/admin/class-adsforwp-admin-common-functions.php';
require_once ADSFORWP_PLUGIN_DIR . '/admin/class-adsforwp-admin-settings.php';

require_once ADSFORWP_PLUGIN_DIR . '/admin/class-adsforwp-ajax-selectbox.php';
require_once ADSFORWP_PLUGIN_DIR . '/admin/class-adsforwp-file-creation.php';

require_once ADSFORWP_PLUGIN_DIR . '/admin/class-adsforwp-admin-analytics.php';
require_once ADSFORWP_PLUGIN_DIR . '/admin/inc/gutenberg/class-adsforwp-ads-gutenberg.php';
require_once ADSFORWP_PLUGIN_DIR . '/admin/mb-helper-function.php';

/* Loading view Metaboxes*/
require_once ADSFORWP_PLUGIN_DIR . '/view/class-adsforwp-view-ads-type.php';
require_once ADSFORWP_PLUGIN_DIR . '/view/class-adsforwp-view-display.php';
require_once ADSFORWP_PLUGIN_DIR . '/view/class-adsforwp-view-ads-visibility.php';
require_once ADSFORWP_PLUGIN_DIR . '/view/class-adsforwp-view-ad-groups.php';
require_once ADSFORWP_PLUGIN_DIR . '/view/class-adsforwp-view-expiredate.php';
require_once ADSFORWP_PLUGIN_DIR . '/view/class-adsforwp-view-placement.php';
require_once ADSFORWP_PLUGIN_DIR . '/view/class-adsforwp-view-visitor-condition.php';

/* Loading frontend files*/
require_once ADSFORWP_PLUGIN_DIR . '/output/class-adsforwp-output-functions.php';
require_once ADSFORWP_PLUGIN_DIR . '/output/class-adsforwp-output-service.php';
require_once ADSFORWP_PLUGIN_DIR . '/output/class-adsforwp-output-amp-condition-display.php';


register_activation_hook( __FILE__, 'adsforwp_on_activation' );


/* Function to check other plugin is install or not*/
function adsforwp_check_plugin() {

	if ( get_option( 'adsforwp_do_activation_redirect', false ) ) {

		delete_option( 'adsforwp_do_activation_redirect' );
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason : Using get param as flag.
		if ( ! isset( $_GET['activate-multi'] ) ) {
			$url = esc_url( admin_url( 'edit.php?post_type=adsforwp' ) );
			wp_safe_redirect( $url );
			exit;

		}
	}
	include ADSFORWP_PLUGIN_DIR . '/view/class-adsforwp-amp-compatibility.php';
}

add_action( 'admin_init', 'adsforwp_check_plugin' );


function adsforwp_action_links( $links ) {

		$mylinks = array(
			'<a href="' . esc_url( admin_url( 'edit.php?post_type=adsforwp&page=adsforwp' ) ) . '">' . esc_html__( 'Settings', 'ads-for-wp' ) . '</a>',
		);
		return array_merge( $links, $mylinks );
}

add_filter( 'plugin_action_links_' . ADSFORWP_PLUGIN_BASENAME, 'adsforwp_action_links' );

/**
 * Set user defined message on plugin activate
 */
function adsforwp_admin_notice() {

	/* Check transient, if available display notice */
	if ( get_transient( 'adsforwp_admin_notice_transient' ) ) {

		echo '<div class="updated notice is-dismissible message notice notice-alt adsforwp-setup-notice">
             <p><span class="dashicons dashicons-thumbs-up"></span>' . esc_html__( 'Thank you for using Ads For WP plugin!', 'ads-for-wp' ) . '               
             <a href="' . esc_url( admin_url( 'edit.php?post_type=adsforwp' ) ) . '"> ' . esc_html__( 'Start adding ads', 'ads-for-wp' ) . '</a>
             </p></div>';

		/* Delete transient, only display this notice once. */
		delete_transient( 'adsforwp_admin_notice_transient' );

	}
	// Feedback notice
		$activation_date = get_option( 'adsforwp_activation_date' );

		$one_day    = gmdate( 'Y-m-d', strtotime( '+1 day', strtotime( $activation_date ) ) );
		$seven_days = gmdate( 'Y-m-d', strtotime( '+7 day', strtotime( $activation_date ) ) );
		$one_month  = gmdate( 'Y-m-d', strtotime( '+30 day', strtotime( $activation_date ) ) );
		$sixty_days = gmdate( 'Y-m-d', strtotime( '+60 day', strtotime( $activation_date ) ) );
		$six_month  = gmdate( 'Y-m-d', strtotime( '+180 day', strtotime( $activation_date ) ) );
		$one_year   = gmdate( 'Y-m-d', strtotime( '+365 day', strtotime( $activation_date ) ) );

		$current_date                  = gmdate( 'Y-m-d' );
		$list_of_date                  = array( $one_day, $seven_days, $one_month, $sixty_days, $six_month, $one_year );
		$review_notice_bar_status_date = get_option( 'review_notice_bar_close_date' );
		$review_notice_bar_never       = get_option( 'adsforwp_review_never' );

	if ( in_array( $current_date, $list_of_date ) && $review_notice_bar_status_date != $current_date && $review_notice_bar_never != 'never' ) {

		echo '<div class="updated notice is-dismissible message notice notice-alt adsforwp-feedback-notice">
                <p><span class="dashicons dashicons-thumbs-up"></span> 
                ' . esc_html__( 'You have been using the Ads For WP plugin for some time now, do you like it?, If so,', 'ads-for-wp' ) . '						
                <a target="_blank" href="https://wordpress.org/plugins/ads-for-wp/#reviews">				
                ' . esc_html__( 'please write us a review', 'ads-for-wp' ) . '              
                </a> 
                <button style="margin-left:10px;" class="button button-primary adsforwp-feedback-notice-remindme">' . esc_html__( 'Remind Me Later', 'ads-for-wp' ) . '</button>
                <button style="margin-left:10px;" class="button button-primary adsforwp-feedback-notice-close">' . esc_html__( 'No Thanks', 'ads-for-wp' ) . '</button> '
				. ' </p> </div>';

	}
}

add_action( 'admin_notices', 'adsforwp_admin_notice' );

/**
 * Here, We are adding support forum links, hire us links and review links for our plugin inside plugins list
 *
 * @param  type $meta_fields
 * @param  type $file
 * @return string
 */
function adsforwp_add_plugin_meta_links( $meta_fields, $file ) {

	if ( ADSFORWP_PLUGIN_BASENAME == $file ) {
		$plugin_url    = 'https://wordpress.org/support/plugin/ads-for-wp';
		$hire_url      = 'https://ampforwp.com/hire/';
		$meta_fields[] = "<a href='" . esc_url( $plugin_url ) . "' target='_blank'>" . esc_html__( 'Support Forum', 'ads-for-wp' ) . '</a>';
		$meta_fields[] = "<a href='" . esc_url( $hire_url ) . "' target='_blank'>" . esc_html__( 'Hire Us', 'ads-for-wp' ) . '</a>';
		$meta_fields[] = "<a href='" . esc_url( $plugin_url ) . "/reviews#new-post' target='_blank' title='" . esc_attr__( 'Rate', 'ads-for-wp' ) . "'>
            <i class='adsforwp-wdi-rate-stars'>"
		. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
		. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
		. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
		. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
		. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
		. '</i></a>';
	}

	return $meta_fields;
}

add_filter( 'plugin_row_meta', 'adsforwp_add_plugin_meta_links', 10, 2 );
