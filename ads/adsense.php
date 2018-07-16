<?php

function ampforwp_adsense_ads($args){

	$post_adsense_ad_id = $args['id'];
        $post_meta_dataset = get_post_meta($post_adsense_ad_id,$key='',true);  
        
	$selected_ads_for 	= $post_meta_dataset['select_ads_for'][0];         
	$dimensions 		= get_adsense_dimensions($post_adsense_ad_id);
	$width				= $dimensions['width'];
	$height				= $dimensions['height'];

	$is_responsive		= '';
	$non_amp_ads 		= $post_meta_dataset['non_amp_ads'][0];
	if('1' === $selected_ads_for){
            
                $ad_client		= $post_meta_dataset['adsense_ad_client'][0];
		$ad_slot		= $post_meta_dataset['adsense_ad_slot'][0];
		$ad_parallax		= $post_meta_dataset['adsense_parallax'][0];
		$is_optimize		= $post_meta_dataset['optimize_ads'][0];
		$is_responsive		= $post_meta_dataset['adsense_responsive'][0];                
                        		
	}
	elseif('2' === $selected_ads_for){
            
                $ad_client		= $post_meta_dataset['_amp_adsense_ad_client'][0];
		$ad_slot		= $post_meta_dataset['_amp_adsense_ad_slot'][0];
		$ad_parallax		= $post_meta_dataset['_amp_adsense_parallax'][0];
		$is_optimize		= $post_meta_dataset['_amp_optimize_ads'][0];
		$is_responsive		= $post_meta_dataset['_amp_adsense_responsive'][0];                
            		
	}
	if('on' === $is_optimize){
		$optimize =  'data-loading-strategy="prefer-viewability-over-views"';
	}
	else{
		$optimize = '';
	}

	if('on' === $is_responsive){
		$ad_code 			= '<amp-ad data-block-on-consent class="aa_wrp aa_adsense aa_'.$post_adsense_ad_id.'"
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

		$ad_code 			= '<amp-ad data-block-on-consent class="aa_wrp aa_adsense aa_'.$post_adsense_ad_id.'"
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
        
        $post_meta_dataset = get_post_meta($post_adsense_ad_id,$key='',true);        
	$ad_client			= '';
	$ad_slot			= '';
	$ad_parallax		= '';
	$is_optimize		= '';
	$is_responsive		= '';	
        $selected_ads_for 	= $post_meta_dataset['select_ads_for'][0];
        
        
        
	$dimensions 		        = get_adsense_dimensions($post_adsense_ad_id);
	$width				= $dimensions['width'];
	$height				= $dimensions['height'];
	$non_amp_ads 		        = $post_meta_dataset['non_amp_ads'][0];
	if('1' === $selected_ads_for){
            
                $ad_client		= $post_meta_dataset['adsense_ad_client'][0]; 
		$ad_slot		= $post_meta_dataset['adsense_ad_slot'][0];
		$ad_parallax		= $post_meta_dataset['adsense_parallax'][0];
		$is_optimize		= $post_meta_dataset['optimize_ads'][0];
		$is_responsive		= $post_meta_dataset['adsense_responsive'][0];
            		
	}
	elseif('2' === $selected_ads_for){
            
            
                $ad_client		= $post_meta_dataset['_amp_adsense_ad_client'][0]; 
		$ad_slot		= $post_meta_dataset['_amp_adsense_ad_slot'][0]; 
		$ad_parallax		= $post_meta_dataset['_amp_adsense_parallax'][0]; 
		$is_optimize		= $post_meta_dataset['_amp_optimize_ads'][0]; 
		$is_responsive		= $post_meta_dataset['_amp_adsense_responsive'][0]; 
                		
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
		$ad_code 			= '<amp-ad data-block-on-consent class="aa_wrp aa_incontent_adsense aa_'.$post_adsense_ad_id.'"width="100vw" height=320
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
		$ad_code 			.= '<amp-ad data-block-on-consent class="aa_wrp aa_incontent_adsense aa_'.$post_adsense_ad_id.'"
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
	 
	if('1' === $selected_ads_for){
		$global_visibility  = get_post_meta($post_ad_id,'ad_visibility_status',true);
	}
	elseif('2' === $selected_ads_for){
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

$post_meta_dataset        = get_post_meta($id,$key='',true);
$selected_ads_for 	= $post_meta_dataset['select_ads_for'][0];

	if('1' === $selected_ads_for){
		$is_link			= $post_meta_dataset['adsense_link'][0];
		if('on' == $is_link){
			$dimensions = $post_meta_dataset['link_ads_dimensions'][0];
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
				$dimension['width'] = $post_meta_dataset['link_custom_width'][0];
				$dimension['height'] = $post_meta_dataset['link_custom_height'][0];
				return $dimension;
				break;
			default:
			$dimension = array('width' => '120',
								'height' => '90'
								 );
			break;
			}
		}
		$dimensions = $post_meta_dataset['adsense_dimensions'][0];
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
				$dimension['width'] = $post_meta_dataset['adsense_custom_width'][0];
				$dimension['height'] = $post_meta_dataset['adsense_custom_height'][0];
				return $dimension;
				break;

			default:
				$dimension = array('width' => '300',
									'height' => '200'
									 );
				break;
		}
	}

	if('2' === $selected_ads_for){
		$is_link			= $post_meta_dataset['_amp_adsense_link'][0];
		if('on' == $is_link){
			$dimensions = $post_meta_dataset['_amp_link_ads_dimensions'][0];
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
				$dimension['width'] = $post_meta_dataset['_amp_link_custom_width'][0];
				$dimension['height'] = $post_meta_dataset['_amp_link_custom_height'][0];
				return $dimension;
				break;
			default:
			$dimension = array('width' => '120',
								'height' => '90'
								 );
			break;
			}
		}
		$dimensions = $post_meta_dataset['_amp_adsense_dimensions'][0];
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
				$dimension['width'] = $post_meta_dataset['_amp_adsense_custom_width'][0];
				$dimension['height'] = $post_meta_dataset['_amp_adsense_custom_height'][0];
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