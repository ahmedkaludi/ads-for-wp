<?php
// Custom Ad Code generator
function ampforwp_custom_ads($args){

	$post_custom_ad_id = $args['id'];
	$selected_ads_for 	= get_post_meta($post_custom_ad_id,'select_ads_for',true);
	if('1' === $selected_ads_for){
		$custom_ad_code	   = get_post_meta($post_custom_ad_id,'custom_ad',true);
	}
	elseif('2' === $selected_ads_for){
		$custom_ad_code	   = get_post_meta($post_custom_ad_id,'_amp_custom_ad',true);
	}
	$ad_code 		   = '<div class="aa_wrp aa_custom aa_'.$post_custom_ad_id.'">
							'.$custom_ad_code.'
							</div>';
	echo $ad_code;
}

function ampforwp_incontent_custom_ads($id){
	$post_custom_ad_id = $id;
	if(NULL != $post_custom_ad_id){
		// do nothing
	}
	else{
		$post_custom_ad_id = get_ad_id(get_the_ID());
	}
	$selected_ads_for 	= get_post_meta($post_custom_ad_id,'select_ads_for',true);
	if('1' === $selected_ads_for){
		$custom_ad_code	   = get_post_meta($post_custom_ad_id,'custom_ad',true);
		$ad_parallax		= get_post_meta($post_custom_ad_id,'custom_parallax',true);
	}
	elseif('2' === $selected_ads_for){
		$custom_ad_code	   = get_post_meta($post_custom_ad_id,'_amp_custom_ad',true);
		$ad_parallax		= get_post_meta($post_custom_ad_id,'_amp_custom_parallax',true);
	}
	if('on' === $ad_parallax){
			$parallax_container = '<amp-fx-flying-carpet height="200px">';
			$parallax_container_end = '</amp-fx-flying-carpet>';
	}
	else{
		$parallax_container = '';
		$parallax_container_end = ''; 
	}

	$ad_code 			= $parallax_container;
	$ad_code 		   .= '<div class="aa_wrp ampforwp_incontent_custom_ads ad-ID-'.$post_custom_ad_id.'">
							'.$custom_ad_code.'
							</div>';
	$ad_code 			.= $parallax_container_end;
	return $ad_code;
}

function ampforwp_custom_sticky_ads(){
	$ad_id 				= get_ad_id(get_the_ID());
	$selected_ads_for 	= get_post_meta($ad_id,'select_ads_for',true);
	if('1' === $selected_ads_for){
		$custom_ad_code	   = get_post_meta($ad_id,'custom_ad',true);
	}
	elseif('2' === $selected_ads_for){
		$custom_ad_code	   = get_post_meta($ad_id,'_amp_custom_ad',true);
	}
	$sticky_ad_code 	= '<div class="aa_wrp ampforwp-sticky-custom-ad amp-sticky-ads aa_'.$ad_id.'">'.$custom_ad_code.'</div>';
	echo $sticky_ad_code; 
}