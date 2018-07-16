<?php
// DoubleClick Ad Code generator

function ampforwp_dfp_ads($args){

	$post_dfp_ad_id 	= $args['id'];
        $post_meta_dataset = get_post_meta($post_dfp_ad_id,$key='',true);
	$selected_ads_for 	= $post_meta_dataset['select_ads_for'][0];
	$dimensions 		= get_dfp_dimensions($post_dfp_ad_id);
	$width				= $dimensions['width'];
	$height				= $dimensions['height'];
	if('1' === $selected_ads_for){
		$ad_slot			= $post_meta_dataset['dfp_ad_slot'][0];
		$ad_parallax		= $post_meta_dataset['dfp_parallax'][0];
		$is_optimize		= $post_meta_dataset['optimize_ads'][0];

	}
	elseif('2' === $selected_ads_for){
		$ad_slot		= $post_meta_dataset['_amp_dfp_ad_slot'][0];
		$ad_parallax		= $post_meta_dataset['_amp_dfp_parallax'][0];
		$is_optimize		= $post_meta_dataset['_amp_optimize_ads'][0];
	}
	if('on' === $is_optimize){
		$optimize =  'data-loading-strategy="prefer-viewability-over-views"';
	}
	else{
		$optimize = '';
	}
	$ad_code		= '<amp-ad data-block-on-consent class="aa_wrp aa_dfp aa_'.$post_dfp_ad_id.'"
							type="doubleclick"'.$optimize.'
							width="'. $width .'"
							height="'. $height .'"
							data-slot="'. $ad_slot .'"
						></amp-ad>';
	echo $ad_code;
}

function ampforwp_incontent_dfp_ads($id){
	
	$post_dfp_ad_id = $id;
	if(NULL != $post_dfp_ad_id){
		// do nothing
	}
	else{
		$post_dfp_ad_id = get_ad_id(get_the_ID());
	}
        $post_meta_dataset = get_post_meta($post_dfp_ad_id,$key='',true);
        $selected_ads_for 	= $post_meta_dataset['select_ads_for'][0];
        
	$dimensions 		= get_dfp_dimensions($post_dfp_ad_id);
	$width				= $dimensions['width'];
	$height				= $dimensions['height'];
	$non_amp_ads 		= $post_meta_dataset['non_amp_ads'][0];
	if('1' === $selected_ads_for){
		$ad_slot			= $post_meta_dataset['dfp_ad_slot'][0];
		$ad_parallax		= $post_meta_dataset['dfp_parallax'][0];
		$is_optimize		= $post_meta_dataset['optimize_ads'][0];
	}
	elseif('2' === $selected_ads_for){
		$ad_slot			= $post_meta_dataset['_amp_dfp_ad_slot'][0];
		$ad_parallax		= $post_meta_dataset['_amp_dfp_parallax'][0];
		$is_optimize		= $post_meta_dataset['_amp_optimize_ads'][0];
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
	$ad_code			.= '<amp-ad data-block-on-consent class="aa_wrp aa_incontent_dfp aa_'.$post_dfp_ad_id.'"
							type="doubleclick"'.$optimize.'
							width="'. $width .'"
							height="'. $height .'"
							data-slot="'. $ad_slot .'"
						></amp-ad>';
	$ad_code 			.= $parallax_container_end;

	if(function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint() || function_exists('is_amp_endpoint') && is_amp_endpoint()){
		return $ad_code;
	}
	else{
		if('on' === $non_amp_ads){ 
		$ad_code = "<!-- Async AdSlot for Ad unit '".$ad_slot."' ### Size: [[".$width.",".$height."]] -->
					<!-- Adslot's refresh function: googletag.pubads().refresh([gptadslots[0]]) -->
					<div id='div-gpt-ad-".$post_dfp_ad_id."'>
					  <script>
					    googletag.cmd.push(function() { googletag.display('div-gpt-ad-".$post_dfp_ad_id."'); });
					  </script>
					</div>
					<!-- End AdSlot -->";
			
		}
		return $ad_code;
	}
	
}
add_action('wp_head','adsforwp_non_amp_dfp_scripts');
function adsforwp_non_amp_dfp_scripts(){ 
	$args = array(
				'post_type'		=>'ads-for-wp-ads',
				'post_status'	=>'publish',
				'posts_per_page'=> -1,
			);
	$query = new WP_Query( $args );
	while ($query->have_posts()) {
	    $query->the_post();
	    $post_dfp_ad_id = get_the_ID();
            $post_meta_dataset = get_post_meta($post_dfp_ad_id,$key='',true);
            
		$selected_ads_for 	= $post_meta_dataset['select_ads_for'][0];
		$dimensions 		= get_dfp_dimensions($post_dfp_ad_id);
		$width				= $dimensions['width'];
		$height				= $dimensions['height'];
		$non_amp_ads 		= $post_meta_dataset['non_amp_ads'][0];
		if('1' === $selected_ads_for){
			$ad_slot			= $post_meta_dataset['dfp_ad_slot'][0];
		}
		elseif('2' === $selected_ads_for){
			$ad_slot			= $post_meta_dataset['_amp_dfp_ad_slot'][0];
		}
			if('on' === $non_amp_ads){ 
				$dfp_wp_script = "<!-- Start GPT Async Tag -->
									<script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>
									<script>
									  var gptadslots = [];
									  var googletag = googletag || {cmd:[]};
									</script>
									<script>
									  googletag.cmd.push(function() {
									    //Adslot declaration
									    gptadslots.push(googletag.defineSlot('".$ad_slot."', [['".$width."','".$height."']], 'div-gpt-ad-".$post_dfp_ad_id."')
									                             .addService(googletag.pubads()));

									    googletag.pubads().enableSingleRequest();
									    googletag.enableServices();
									  });
									</script>
									<!-- End GPT Async Tag -->";
				echo $dfp_wp_script;
			}
	}
	wp_reset_query();
	wp_reset_postdata();
}

function ampforwp_dfp_sticky_ads(){

	$sticky_dfp_ad_id = get_ad_id(get_the_ID());
	$ad_code = ampforwp_incontent_dfp_ads($sticky_dfp_ad_id);
	$amp_sticky = '<amp-sticky-ad layout="nodisplay">'.$ad_code.'</amp-sticky-ad>';
	echo $amp_sticky;
}

// DoubleClick dimensions 

function get_dfp_dimensions($id){
    
        $post_meta_dataset = get_post_meta($id,$key='',true);                
        
	$selected_ads_for 	= $post_meta_dataset['select_ads_for'][0];
	if('1' === $selected_ads_for){
		$dimensions = $post_meta_dataset['dfp_dimensions'][0];
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
				$dimension['width'] = $post_meta_dataset['dfp_custom_width'][0];
				$dimension['height'] = $post_meta_dataset['dfp_custom_height'][0];
				return $dimension;
				break;

			default:
				$dimension = array('width' => '300',
									'height' => '200'
									 );
				return $dimension;
				break;
		}
	}

	if('2' === $selected_ads_for){
		$dimensions = $post_meta_dataset['_amp_dfp_dimensions'][0];
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
				$dimension['width'] = $post_meta_dataset['_amp_dfp_custom_width'][0];
				$dimension['height'] = $post_meta_dataset['_amp_dfp_custom_height'][0];
				return $dimension;
				break;

			default:
				$dimension = array('width' => '300',
									'height' => '200'
									 );
				return $dimension;
				break;
		}
	}
}
