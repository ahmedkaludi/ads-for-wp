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
    	'publicly_queryable'	=> false
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
 * Generating Ad ShortCode
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




add_action('admin_footer', function(){ ?>
	<script type="text/javascript">

		jQuery( document ).ready(function($) {


			/* ADS CPT */
			var currentSelectedField 	= $('#adsforwp-current-ad-type').val();
			var adsforwpGlobalCode 		= $('#adsforwp_position_global_code');		
			var adsforwpSpecificCode 	= $('#adsforwp_ads_position_specific_controls');

			if ( currentSelectedField == 'show' ) {
				$(adsforwpGlobalCode).show();
				$(adsforwpSpecificCode).hide();
			} else {
				$(adsforwpGlobalCode).hide();
				$(adsforwpSpecificCode).show();
			}

			$('#adsforwp_ads_position_global').on('click', function() {
				$(adsforwpGlobalCode).show();
				$(adsforwpSpecificCode).hide();
			});

			$('#adsforwp_ads_position_specific').on('click', function() {
				$(adsforwpGlobalCode).hide();
				$(adsforwpSpecificCode).show();
			});


			/* Global */
			var singleAdsStatus = $('#adsforwp-current-ad-status').val(); 
			if ( singleAdsStatus == 'show') {
				$('#adsforwp-all-ads').show();
			} else {
				$('#adsforwp-all-ads').hide();
			}
		
			$('.adsforwp-ads-controls').on('change', '#adsforwp_ads_meta_box_radio_show', function(){
				$('#adsforwp-all-ads').show();
			} );

			$('.adsforwp-ads-controls').on('change', '#adsforwp_ads_meta_box_radio_hide', function(){
				$('#adsforwp-all-ads').hide();
			} );


		});
		
	</script> <?php

});