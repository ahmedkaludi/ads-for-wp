<?php


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper method to check if user is in the plugins page.
 *
 * @author
 * @since  1.4.0
 *
 * @return bool
 */
function adsforwp_is_plugins_page() {

	if(function_exists('get_current_screen')){
        $screen = get_current_screen();
            if(is_object($screen)){
                if($screen->id == 'plugins' || $screen->id == 'plugins-network'){
                    return true;
                }
            }
    }
    return false;
}

/**
 * display deactivation logic on plugins page
 *
 * @since 1.4.0
 */
function adsforwp_add_deactivation_feedback_modal() {

	if ( ! is_admin() && ! adsforwp_is_plugins_page() ) {
		return;
	}

	$current_user = wp_get_current_user();
	if ( ! ( $current_user instanceof WP_User ) ) {
		$email = '';
	} else {
		$email = trim( $current_user->user_email );
	}

	include_once ADSFORWP_PLUGIN_DIR . 'admin/deactivate-feedback.php';
}

if( !is_admin() && !superpwa_is_plugins_page()) {
	return;
}

$current_user = wp_get_current_user();
if( !($current_user instanceof WP_User) ) {
	$email = '';
} else {
	$email = trim( $current_user->user_email );
}

require_once SUPERPWA_PATH_ABS."admin/deactivate-feedback.php";

/**
 * send feedback via email
 *
 * @since 1.4.0
 */
function adsforwp_send_feedback() {
    //phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason : Since form is serialised nonce is verified after parsing the recieved data.
	if ( isset( $_POST['data'] ) ) {
		//phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason : Since form is serialised nonce is verified after parsing the recieved data.
		parse_str( $_POST['data'], $form );
	}

	if ( empty( $form ) || ! is_array( $form ) ) {
		wp_die( esc_html__('Invalid Data Received','wpdbbkp') );
	}

	if ( ! isset( $form['_adsforwp_deactivate'] ) || ! wp_verify_nonce( $form['_adsforwp_deactivate'], 'adsforwp_deactivate_form' ) ) {
		wp_die( esc_html__('Security check failed','wpdbbkp') );
	}

	$text = '';
	if ( isset( $form['adsforwp_disable_text'] ) ) {
		$text = implode( "\n\r", $form['adsforwp_disable_text'] );
	}

	$headers = array();

	$from = isset( $form['adsforwp_disable_from'] ) ? $form['adsforwp_disable_from'] : '';
	if ( $from ) {
		$headers[] = "From: $from";
		$headers[] = "Reply-To: $from";
	}

	$subject = isset( $form['adsforwp_disable_reason'] ) ? $form['adsforwp_disable_reason'] : '(no reason given)';

	$subject = $subject . ' - ADS for WP';

	if ( $subject == 'technical - ADS for WP' ) {

			$text = trim( $text );

		if ( ! empty( $text ) ) {

			$text = 'technical issue description: ' . $text;

		} else {

			$text = 'no description: ' . $text;
		}
	}

	$success = wp_mail( 'team@magazine3.in', $subject, $text, $headers );

	die();
}
add_action( 'wp_ajax_adsforwp_send_feedback', 'adsforwp_send_feedback' );



add_action( 'admin_enqueue_scripts', 'adsforwp_enqueue_makebetter_email_js' );

function adsforwp_enqueue_makebetter_email_js() {

	if ( ! is_admin() && ! adsforwp_is_plugins_page() ) {
		return;
	}

	wp_enqueue_script( 'ads-for-wp-make-better-js', plugin_dir_url( __DIR__ ) . 'admin/make-better-admin.js', array( 'jquery' ), ADSFORWP_VERSION, true );

	wp_enqueue_style( 'ads-for-wp-make-better-css', plugin_dir_url( __DIR__ ) . 'admin/make-better-admin.css', false, ADSFORWP_VERSION );
}

	add_filter( 'admin_footer', 'adsforwp_add_deactivation_feedback_modal' );

