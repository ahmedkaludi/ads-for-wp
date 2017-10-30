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
      'exclude_from_search'	=> true,
    //  'publicly_queryable'	=> false
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
add_action( 'add_meta_boxes', 'adsforwp_generate_ads_shortcode' );
function adsforwp_generate_ads_shortcode(){

	add_meta_box(
		'adsforwp_ads_shortcode',
		__( 'Ad Code ', 'ads-for-wp' ),
		'adsforwp_ads_shortcode_html',
		'ads-for-wp-ads',
		'side',
		'default'
	);
}
function adsforwp_ads_shortcode_html(){
	echo '<code> [ads-for-wp ads-id="'.get_the_ID().'"]</code>';
}


/*
 * Generating ShortCode
 */

add_shortcode('ads-for-wp', 'adsforwp_shortcode_generator');
function adsforwp_shortcode_generator( $atts ){

	$content = '';
	$show_ads 	= '';

	$show_ads = 'yes';		
	$show_ads = apply_filters('adsforwp_advert_on_off', $show_ads);

	if ( $show_ads == 'yes' ) {
		$content = get_post_field('post_content', $atts['ads-id']);
	}

	return $content ;
}