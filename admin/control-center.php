<?php
add_action( 'init', 'adsforwp_setup_post_type' );

function adsforwp_setup_post_type() {
    $args = array(
      'labels' => array(
        'name' 			=> esc_html__( 'Ads', 'ads-for-wp' ),
        'singular_name' => esc_html__( 'Ad', 'ads-for-wp' )
      ),
      'public' 		=> true,
      'has_archive' => flase,
      'exclude_from_search'	=> true
    );
    register_post_type( 'ads-for-wp-ads', $args );
}


/*
 * Hiding Visaul Editor part, as there is no need for Visual Editor to add Advert Code 
*/
add_filter( 'user_can_richedit', 'adsforwp_hide_visual_editor');

function adsforwp_hide_visual_editor($content) {
    global $post_type;

    if ('ads-for-wp-ads' == $post_type)
        return false;
    return $content;
}



/*
 * Creating ShortCode meta box for the users to get the ad code.
 */
function adsforwp_generate_ads_shortcode(){

}