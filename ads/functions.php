<?php
/* Remove unwanted meta boxes from Ads Posts type
 *
*/
add_action('add_meta_boxes', 'adsforwp_wp_seo_meta_box', 100);
function adsforwp_wp_seo_meta_box() {

	$meta_boxes  = array(
		'adsforwp_ads_meta_box' => 'side' , 
		'wpseo_meta' => 'normal' );

	foreach ($meta_boxes as $mb => $position) {
		remove_meta_box( $mb, 'ads-for-wp-ads', $position);
	}
}

/*
 * Ads Between Related Post #12
*/

add_action('ampforwp_between_related_post', 'adsforwp_ads_between_related_posts');
function adsforwp_ads_between_related_posts($r_count){
	global $redux_builder_amp;
	$ID = get_the_ID();
	$number_of_RP = $redux_builder_amp['ampforwp-number-of-related-posts'];
	$in_between = round(abs($number_of_RP / 2));
	if(intval($in_between) === $r_count){
		$ad_position = get_post_meta(get_ad_id($ID),'normal_ad_type',true);
		if('13' === $ad_position){
			$ad_vendor			= get_post_meta(get_ad_id($ID),'ad_vendor',true);
			if('1' === $ad_vendor){
				ampforwp_adsense_ads();
			}elseif('2' === $ad_vendor){
				ampforwp_dfp_ads();
			}elseif('3' === $ad_vendor){
				ampforwp_custom_ads();
			}elseif('4' === $ad_vendor){
				adsforwp_media_net_ads();
			}
		}
			
	}
}
