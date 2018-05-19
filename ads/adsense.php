<?php
function ampforwp_adsense_ads($args){

	$post_adsense_ad_id = $args['id'];
	$selected_ads_for 	= get_post_meta($post_adsense_ad_id,'select_ads_for',true);
	$dimensions 		= get_adsense_dimensions($post_adsense_ad_id);
	$width				= $dimensions['width'];
	$height				= $dimensions['height'];
	$is_responsive		= '';
	$non_amp_ads 		= get_post_meta($post_adsense_ad_id,'non_amp_ads',true);
	if('ampforwp' === $selected_ads_for){
		$ad_client			= get_post_meta($post_adsense_ad_id,'adsense_ad_client',true);
		$ad_slot			= get_post_meta($post_adsense_ad_id,'adsense_ad_slot',true);
		$ad_parallax		= get_post_meta($post_adsense_ad_id,'adsense_parallax',true);
		$is_optimize		= get_post_meta($post_adsense_ad_id,'optimize_ads',true);
		$is_responsive		= get_post_meta($post_adsense_ad_id,'adsense_responsive',true);
	}
	elseif('amp_by_automattic' === $selected_ads_for){
		$ad_client			= get_post_meta($post_adsense_ad_id,'_amp_adsense_ad_client',true);
		$ad_slot			= get_post_meta($post_adsense_ad_id,'_amp_adsense_ad_slot',true);
		$ad_parallax		= get_post_meta($post_adsense_ad_id,'_amp_adsense_parallax',true);
		$is_optimize		= get_post_meta($post_adsense_ad_id,'_amp_optimize_ads',true);
		$is_responsive		= get_post_meta($post_adsense_ad_id,'_amp_adsense_responsive',true);
	}
	if('on' === $is_optimize){
		$optimize =  'data-loading-strategy="prefer-viewability-over-views"';
	}
	else{
		$optimize = '';
	}

	if('on' === $is_responsive){
		$ad_code 			= '<amp-ad class="aa_wrp aa_adsense aa_'.$post_adsense_ad_id.'"
								width="100vw" height=320
								  type="adsense"
								  data-ad-client="'. $ad_client .'"
								  data-ad-slot="'. $ad_slot .'"
								  data-auto-format="rspv"
								  data-full-width>
							    <div overflow></div>
							</amp-ad>';
	}
	else{

		$ad_code 			= '<amp-ad class="aa_wrp aa_adsense aa_'.$post_adsense_ad_id.'"
								type="adsense"'.$optimize.'
								width="'. $width .'"
								height="'. $height .'"
								data-ad-client="'. $ad_client .'"
								data-ad-slot="'. $ad_slot .'"
							></amp-ad>';
	}
	
	echo $ad_code;	
	
}

function ampforwp_incontent_adsense_ads($id){
	$post_adsense_ad_id = $id;

	if ( empty( $post_adsense_ad_id ) || null == $post_adsense_ad_id ) {
		$post_adsense_ad_id = get_ad_id(get_the_ID());
	}

	$ad_client			= '';
	$ad_slot			= '';
	$ad_parallax		= '';
	$is_optimize		= '';
	$is_responsive		= '';
	$selected_ads_for 	= get_post_meta($post_adsense_ad_id,'select_ads_for',true);
	$dimensions 		= get_adsense_dimensions($post_adsense_ad_id);
	$width				= $dimensions['width'];
	$height				= $dimensions['height'];
	$non_amp_ads 		= get_post_meta($post_adsense_ad_id,'non_amp_ads',true);
	if('ampforwp' === $selected_ads_for){
		$ad_client			= get_post_meta($post_adsense_ad_id,'adsense_ad_client',true);
		$ad_slot			= get_post_meta($post_adsense_ad_id,'adsense_ad_slot',true);
		$ad_parallax		= get_post_meta($post_adsense_ad_id,'adsense_parallax',true);
		$is_optimize		= get_post_meta($post_adsense_ad_id,'optimize_ads',true);
		$is_responsive		= get_post_meta($post_adsense_ad_id,'adsense_responsive',true);
	}
	elseif('amp_by_automattic' === $selected_ads_for){
		$ad_client			= get_post_meta($post_adsense_ad_id,'_amp_adsense_ad_client',true);
		$ad_slot			= get_post_meta($post_adsense_ad_id,'_amp_adsense_ad_slot',true);
		$ad_parallax		= get_post_meta($post_adsense_ad_id,'_amp_adsense_parallax',true);
		$is_optimize		= get_post_meta($post_adsense_ad_id,'_amp_optimize_ads',true);
		$is_responsive		= get_post_meta($post_adsense_ad_id,'_amp_adsense_responsive',true);
	}
	if('on' === $is_optimize){
		$optimize =  'data-loading-strategy="prefer-viewability-over-views"';
	}
	else{
		$optimize = '';
	}
	if('on' === $ad_parallax){
			$parallax_container = '<amp-fx-flying-carpet height="200px">';
			$parallax_container_end = '</amp-fx-flying-carpet>';
	}
	else{
		$parallax_container = '';
		$parallax_container_end = ''; 
	}

	if('on' === $is_responsive){
		$ad_code 			= '<amp-ad class="aa_wrp aa_incontent_adsense aa_'.$post_adsense_ad_id.'"width="100vw" height=320
			  type="adsense"
			  data-ad-client="'. $ad_client .'"
			  data-ad-slot="'. $ad_slot .'"
			  data-auto-format="rspv"
			  data-full-width>
		    <div overflow></div>
		</amp-ad>';
	}
	else{
		$ad_code 			= $parallax_container;
		$ad_code 			.= '<amp-ad class="aa_wrp aa_incontent_adsense aa_'.$post_adsense_ad_id.'"
				type="adsense"'.$optimize.'
				width="'. $width .'"
				height="'. $height .'"
				data-ad-client="'. $ad_client .'"
				data-ad-slot="'. $ad_slot .'"
			></amp-ad>';
		$ad_code 			.= $parallax_container_end;
	}
	if(function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint() || function_exists('is_amp_endpoint') && is_amp_endpoint()){
		return $ad_code;
	}
	else{
		if('on' === $non_amp_ads){
		$ad_code = '	<div class="add-wrapper" style="text-align:center;">
							<script async="" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">
							</script>
							<ins class="adsbygoogle" style="display:inline-block;width:'.$width.';height:'.$height.'" data-ad-client="'.$ad_client.'" data-ad-slot="'.$ad_slot.'">
							</ins>
							<script>
								(adsbygoogle = window.adsbygoogle || []).push({});
							</script>
						</div>';
		}
		return $ad_code;
	}
	
}

function ampforwp_sticky_adsense_ads(){
	$selected_ads_for 	= get_post_meta($id,'select_ads_for',true);
	if('ampforwp' === $selected_ads_for){
		$global_visibility  = get_post_meta($post_ad_id,'ad_visibility_status',true);
	}
	elseif('amp_by_automattic' === $selected_ads_for){
		$global_visibility  = get_post_meta($post_ad_id,'_amp_ad_visibility_status',true);
	}
	if($global_visibility != 'hide'){
		$sticky_adsense_ad_id = get_ad_id(get_the_ID());
		$ad_code = ampforwp_incontent_adsense_ads($sticky_adsense_ad_id);
		$amp_sticky = '<amp-sticky-ad layout="nodisplay">'.$ad_code.'</amp-sticky-ad>';
		echo $amp_sticky;
	}
	
}

// adsense dimensions 

function get_adsense_dimensions($id){

$selected_ads_for 	= get_post_meta($id,'select_ads_for',true);
	if('ampforwp' === $selected_ads_for){
		$is_link			= get_post_meta($id,'adsense_link',true);
		if('on' == $is_link){
			$dimensions = get_post_meta($id,'link_ads_dimensions',true);
			switch ($dimensions) {
				case '1':
				$dimension = array('width' => '120',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '2':
				$dimension = array('width' => '160',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '3':
				$dimension = array('width' => '180',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '4':
				$dimension = array('width' => '200',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '5':
				$dimension = array('width' => '468',
									'height' => '15'
									 );
				return $dimension;
				break;
			
			case '6':
				$dimension = array('width' => '728',
									'height' => '15'
									 );
				return $dimension;
				break;

			case '7':
				$dimension = array();
				$dimension['width'] = get_post_meta($id,'link_custom_width',true);
				$dimension['height'] = get_post_meta($id,'link_custom_height',true);
				return $dimension;
				break;
			default:
			$dimension = array('width' => '120',
								'height' => '90'
								 );
			break;
			}
		}
		$dimensions = get_post_meta($id,'adsense_dimensions',true);
		switch ($dimensions) {
			case '1':
				$dimension = array('width' => '300',
									'height' => '250'
									 );
				return $dimension;
				break;

			case '2':
				$dimension = array('width' => '336',
									'height' => '280'
									 );
				return $dimension;
				break;

			case '3':
				$dimension = array('width' => '728',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '4':
				$dimension = array('width' => '300',
									'height' => '600'
									 );
				return $dimension;
				break;

			case '5':
				$dimension = array('width' => '320',
									'height' => '100'
									 );
				return $dimension;
				break;
			
			case '6':
				$dimension = array('width' => '200',
									'height' => '50'
									 );
				return $dimension;
				break;

			case '7':
				$dimension = array('width' => '320',
									'height' => '50'
									 );
				return $dimension;
				break;

			case '8':
				$dimension = array();
				$dimension['width'] = get_post_meta($id,'adsense_custom_width',true);
				$dimension['height'] = get_post_meta($id,'adsense_custom_height',true);
				return $dimension;
				break;

			default:
				$dimension = array('width' => '300',
									'height' => '200'
									 );
				break;
		}
	}

	if('amp_by_automattic' === $selected_ads_for){
		$is_link			= get_post_meta($id,'_amp_adsense_link',true);
		if('on' == $is_link){
			$dimensions = get_post_meta($id,'_amp_link_ads_dimensions',true);
			switch ($dimensions) {
				case '1':
				$dimension = array('width' => '120',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '2':
				$dimension = array('width' => '160',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '3':
				$dimension = array('width' => '180',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '4':
				$dimension = array('width' => '200',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '5':
				$dimension = array('width' => '468',
									'height' => '15'
									 );
				return $dimension;
				break;
			
			case '6':
				$dimension = array('width' => '728',
									'height' => '15'
									 );
				return $dimension;
				break;

			case '7':
				$dimension = array();
				$dimension['width'] = get_post_meta($id,'_amp_link_custom_width',true);
				$dimension['height'] = get_post_meta($id,'_amp_link_custom_height',true);
				return $dimension;
				break;
			default:
			$dimension = array('width' => '120',
								'height' => '90'
								 );
			break;
			}
		}
		$dimensions = get_post_meta($id,'_amp_adsense_dimensions',true);
		switch ($dimensions) {
			case '1':
				$dimension = array('width' => '300',
									'height' => '250'
									 );
				return $dimension;
				break;

			case '2':
				$dimension = array('width' => '336',
									'height' => '280'
									 );
				return $dimension;
				break;

			case '3':
				$dimension = array('width' => '728',
									'height' => '90'
									 );
				return $dimension;
				break;

			case '4':
				$dimension = array('width' => '300',
									'height' => '600'
									 );
				return $dimension;
				break;

			case '5':
				$dimension = array('width' => '320',
									'height' => '100'
									 );
				return $dimension;
				break;
			
			case '6':
				$dimension = array('width' => '200',
									'height' => '50'
									 );
				return $dimension;
				break;

			case '7':
				$dimension = array('width' => '320',
									'height' => '50'
									 );
				return $dimension;
				break;

			case '8':
				$dimension = array();
				$dimension['width'] = get_post_meta($id,'_amp_adsense_custom_width',true);
				$dimension['height'] = get_post_meta($id,'_amp_adsense_custom_height',true);
				return $dimension;
				break;

			default:
				$dimension = array('width' => '300',
									'height' => '200'
									 );
				break;
		}
	}
}