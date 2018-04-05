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