<?php
// Custom Ad Code generator
function ampforwp_custom_ads($args){

	$post_custom_ad_id = $args['id'];
        $post_meta_dataset = get_post_meta($post_custom_ad_id,$key='',true);
        
	$selected_ads_for 	= $post_meta_dataset['select_ads_for'][0];
	if('1' === $selected_ads_for){
		$custom_ad_code	   = $post_meta_dataset['custom_ad'][0];
	}
	elseif('2' === $selected_ads_for){
		$custom_ad_code	   = $post_meta_dataset['_amp_custom_ad'][0];
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
        $post_meta_dataset = get_post_meta($post_custom_ad_id,$key='',true);
        
	$selected_ads_for 	= $post_meta_dataset['select_ads_for'][0];
	if('1' === $selected_ads_for){
		$custom_ad_code	   = $post_meta_dataset['custom_ad'][0]; 
		$ad_parallax		=  $post_meta_dataset['custom_parallax'][0]; 
	}
	elseif('2' === $selected_ads_for){
		$custom_ad_code	   = $post_meta_dataset['_amp_custom_ad'][0];
		$ad_parallax		= $post_meta_dataset['_amp_custom_parallax'][0];
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
        $post_meta_dataset = get_post_meta($ad_id,$key='',true);
        
	$selected_ads_for 	= $post_meta_dataset['select_ads_for'][0];
	if('1' === $selected_ads_for){
		$custom_ad_code	   = $post_meta_dataset['custom_ad'][0];
	}
	elseif('2' === $selected_ads_for){
		$custom_ad_code	   = $post_meta_dataset['_amp_custom_ad'][0];
	}
	$sticky_ad_code 	= '<div class="aa_wrp ampforwp-sticky-custom-ad amp-sticky-ads aa_'.$ad_id.'">'.$custom_ad_code.'</div>';
	echo $sticky_ad_code; 
}