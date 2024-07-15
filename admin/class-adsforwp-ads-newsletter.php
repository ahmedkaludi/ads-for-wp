<?php 
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Adsforwp_Ads_Newsletter {


	public function __construct() {

				add_filter( 'adsforwp_localize_filter', array( $this, 'adsforwp_add_localize_footer_data' ), 10, 2 );
	}

	public function adsforwp_add_localize_footer_data( $object, $object_name ) {

		$dismissed = explode( ',', get_user_meta( wp_get_current_user()->ID, 'dismissed_wp_pointers', true ) );
		$do_tour   = ! in_array( 'adsforwp_subscribe_pointer', $dismissed );

		if ( $do_tour ) {
				wp_enqueue_style( 'wp-pointer' );
				wp_enqueue_script( 'wp-pointer' );
		}

		if ( $object_name == 'adsforwp_localize_data' ) {

				global $current_user;
			$tour = array();
                //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: No form submissions.
				$tab = isset( $_GET['tab'] ) ? esc_attr( wp_unslash($_GET['tab']) ) : '';

			if ( ! array_key_exists( $tab, $tour ) ) {

				$object['do_tour']                = $do_tour;
					$object['get_home_url']       = get_home_url();
					$object['current_user_email'] = $current_user->user_email;
					$object['current_user_name']  = $current_user->display_name;
				$object['displayID']              = '#menu-posts-adsforwp';
					$object['button1']            = esc_html__( 'No Thanks', 'ads-for-wp' );
					$object['button2']            = false;
					$object['function_name']      = '';

			}
		}
		return $object;
	}
}
$adsforwp_ads_newsletter = new Adsforwp_Ads_Newsletter();
