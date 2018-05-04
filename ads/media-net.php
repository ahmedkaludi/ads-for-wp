<?php
// media.net code generator 
function adsforwp_media_net_ads($args){
	
	$post_medianet_ad_id = $args['id'];
	$selected_ads_for 	= get_post_meta($post_medianet_ad_id,'select_ads_for',true);
	$dimensions 		= get_medianet_dimensions($post_medianet_ad_id);
	$width				= $dimensions['width'];
	$height				= $dimensions['height'];
	if('1' === $selected_ads_for){
		$ad_client			= get_post_meta($post_medianet_ad_id,'medianet_ad_client',true);
		$ad_slot			= get_post_meta($post_medianet_ad_id,'medianet_ad_slot',true);
		$ad_parallax		= get_post_meta($post_medianet_ad_id,'medianet_parallax',true);
		$is_optimize		= get_post_meta($post_medianet_ad_id,'optimize_ads',true);
	}
	elseif('2' === $selected_ads_for){
		$ad_client			= get_post_meta($post_medianet_ad_id,'_amp_medianet_ad_client',true);
		$ad_slot			= get_post_meta($post_medianet_ad_id,'_amp_medianet_ad_slot',true);
		$ad_parallax		= get_post_meta($post_medianet_ad_id,'_amp_medianet_parallax',true);
		$is_optimize		= get_post_meta($post_medianet_ad_id,'_amp_optimize_ads',true);
	}
	if('on' === $is_optimize){
		$optimize =  'data-loading-strategy="prefer-viewability-over-views"';
	}
	else{
		$optimize = '';
	}

	$ad_code 			= '<amp-ad data-block-on-consent class="aa_wrp aa_medianet aa_'.$post_medianet_ad_id.'"
							width="'. $width .'"
							height="'. $height .'"
							type="medianet"'.$optimize.'
							data-tagtype="cm"
							data-cid="'. $ad_client .'"
							data-crid="'. $ad_slot .'"
						></amp-ad>';
	
	echo $ad_code;	
}
function adsforwp_incontent_media_net_ads($id){
	$post_medianet_ad_id = $id;
	if(NULL != $post_medianet_ad_id){
		// do nothing
	}
	else{
		$post_medianet_ad_id = get_ad_id(get_the_ID());
	}
	$ad_client			= '';
	$ad_slot			= '';
	$ad_parallax		= '';
	$is_optimize		= '';
	$selected_ads_for 	= get_post_meta($post_medianet_ad_id,'select_ads_for',true);
	$dimensions 		= get_medianet_dimensions($post_medianet_ad_id);
	$width				= $dimensions['width'];
	$height				= $dimensions['height'];
	$size 				= $width.'x'.$height;
	$non_amp_ads 		= get_post_meta($post_medianet_ad_id,'non_amp_ads',true);
	if('1' === $selected_ads_for){
		$ad_client			= get_post_meta($post_medianet_ad_id,'medianet_ad_client',true);
		$ad_slot			= get_post_meta($post_medianet_ad_id,'medianet_ad_slot',true);
		$ad_parallax		= get_post_meta($post_medianet_ad_id,'medianet_parallax',true);
		$is_optimize		= get_post_meta($post_medianet_ad_id,'optimize_ads',true);
	}
	elseif('2' === $selected_ads_for){
		$ad_client			= get_post_meta($post_medianet_ad_id,'_amp_medianet_ad_client',true);
		$ad_slot			= get_post_meta($post_medianet_ad_id,'_amp_medianet_ad_slot',true);
		$ad_parallax		= get_post_meta($post_medianet_ad_id,'_amp_medianet_parallax',true);
		$is_optimize		= get_post_meta($post_medianet_ad_id,'_amp_optimize_ads',true);
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

		$ad_code 			= $parallax_container;
		$ad_code 			.= '<amp-ad data-block-on-consent class="aa_wrp aa_incontent_medianet aa_'.$post_medianet_ad_id.'"
				width="'. $width .'"
				height="'. $height .'"
				type="medianet"'.$optimize.'
				data-cid="'. $ad_client .'"
				data-crid="'. $ad_slot .'"
			></amp-ad>';
		$ad_code 			.= $parallax_container_end;
	
	if(function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint() || function_exists('is_amp_endpoint') && is_amp_endpoint()){
		return $ad_code;
	}
	else{
		if('on' === $non_amp_ads){

		$ad_code = '<div class="aa_media_net" style="text-align:center; margin:10px 0">
				<div id="'.$post_medianet_ad_id.'">
		  	<script type="text/javascript">
			  try {
				   window._mNHandle.queue.push(function () {
				   	window._mNDetails.loadTag("'.$post_medianet_ad_id.'", "'.$size.'", "'. $ad_slot .'");
				  	});
				  }
			  catch (error) {}
			</script>
		  </div>
		</div>';
			
		}
		return $ad_code;
	}

}

function adsforwp_medianet_sticky_ads(){
	$selected_ads_for 	= get_post_meta($id,'select_ads_for',true);
	if('1' === $selected_ads_for){
		$global_visibility  = get_post_meta($post_ad_id,'ad_visibility_status',true);
	}
	elseif('2' === $selected_ads_for){
		$global_visibility  = get_post_meta($post_ad_id,'_amp_ad_visibility_status',true);
	}
	if($global_visibility != 'hide'){
		$sticky_medianet_ad_id = get_ad_id(get_the_ID());
		$ad_code = adsforwp_incontent_media_net_ads($sticky_medianet_ad_id);
		$amp_sticky = '<amp-sticky-ad layout="nodisplay">'.$ad_code.'</amp-sticky-ad>';
		echo $amp_sticky;
	}
	
}

// media.net dimensions 

function get_medianet_dimensions($id){

$selected_ads_for 	= get_post_meta($id,'select_ads_for',true);
	if('1' === $selected_ads_for){
		
		$dimensions = get_post_meta($id,'medianet_dimensions',true);
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
				$dimension['width'] = get_post_meta($id,'medianet_custom_width',true);
				$dimension['height'] = get_post_meta($id,'medianet_custom_height',true);
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
		
		$dimensions = get_post_meta($id,'_amp_medianet_dimensions',true);
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
				$dimension['width'] = get_post_meta($id,'_amp_medianet_custom_width',true);
				$dimension['height'] = get_post_meta($id,'_amp_medianet_custom_height',true);
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