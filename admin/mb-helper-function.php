<?php


// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Helper method to check if user is in the plugins page.
 *
 * @author 
 * @since  1.4.0
 *
 * @return bool
 */
function adsforwp_is_plugins_page() {
    global $pagenow;

    return ( 'plugins.php' === $pagenow );
}

/**
 * display deactivation logic on plugins page
 * 
 * @since 1.4.0
 */


function adsforwp_add_deactivation_feedback_modal() {
    
  
    if( !is_admin() && !adsforwp_is_plugins_page()) {
        return;
    }

    $current_user = wp_get_current_user();
    if( !($current_user instanceof WP_User) ) {
        $email = '';
    } else {
        $email = trim( $current_user->user_email );
    }

    require_once ADSFORWP_PLUGIN_DIR."admin/deactivate-feedback.php";
    
}

/**
 * send feedback via email
 * 
 * @since 1.4.0
 */
function adsforwp_send_feedback() {

    if( isset( $_POST['data'] ) ) {
        parse_str( $_POST['data'], $form );
    }

    $text = '';
    if( isset( $form['adsforwp_disable_text'] ) ) {
        $text = implode( "\n\r", $form['adsforwp_disable_text'] );
    }

    $headers = array();

    $from = isset( $form['adsforwp_disable_from'] ) ? $form['adsforwp_disable_from'] : '';
    if( $from ) {
        $headers[] = "From: $from";
        $headers[] = "Reply-To: $from";
    }

    $subject = isset( $form['adsforwp_disable_reason'] ) ? $form['adsforwp_disable_reason'] : '(no reason given)';

    $subject = $subject.' - ADS for WP';

    if($subject == 'technical - ADS for WP'){

          $text = trim($text);

          if(!empty($text)){

            $text = 'technical issue description: '.$text;

          }else{

            $text = 'no description: '.$text;
          }
      
    }

    $success = wp_mail( 'team@magazine3.in', $subject, $text, $headers );

    die();
}
add_action( 'wp_ajax_adsforwp_send_feedback', 'adsforwp_send_feedback' );



add_action( 'admin_enqueue_scripts', 'adsforwp_enqueue_makebetter_email_js' );

function adsforwp_enqueue_makebetter_email_js(){
 
    if( !is_admin() && !adsforwp_is_plugins_page()) {
        return;
    }

    wp_enqueue_script( 'ads-for-wp-make-better-js', plugin_dir_url( __DIR__ ). 'admin/make-better-admin.js', array( 'jquery' ), ADSFORWP_VERSION);

    wp_enqueue_style( 'ads-for-wp-make-better-css', plugin_dir_url( __DIR__ ). 'admin/make-better-admin.css', false , ADSFORWP_VERSION);
}

if( is_admin() && adsforwp_is_plugins_page()) {
    add_filter('admin_footer', 'adsforwp_add_deactivation_feedback_modal');
}


add_action( 'wp_ajax_adsforwp_subscribe_for_newsletter', 'adsforwp_subscribe_for_newsletter' );
add_action( 'wp_ajax_nopriv_adsforwp_subscribe_for_newsletter', 'adsforwp_subscribe_for_newsletter' );
function adsforwp_subscribe_for_newsletter(){
    if( !wp_verify_nonce( sanitize_text_field( $_POST['adsforwp_security_nonce'] ), 'adsforwp_ajax_check_admin_news_nonce' ) ) {
        echo 'security_nonce_not_verified';
        die();
    }
    if ( !current_user_can( 'manage_options' ) ) {
        die();
    }
    $api_url = 'http://magazine3.company/wp-json/api/central/email/subscribe';
    $api_params = array(
        'name' => sanitize_text_field($_POST['name']),
        'email'=> sanitize_email($_POST['email']),
        'website'=> sanitize_text_field($_POST['website']),
        'type'=> 'adsforwp'
    );

    $response = wp_remote_post( $api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
    $response = wp_remote_retrieve_body( $response );
    echo $response;
    die;
}