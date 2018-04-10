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


// AMP Sticky Add

add_action('amp_post_template_footer','ampforwp_amp_sticky_ad');

function ampforwp_amp_sticky_ad(){
	$ad_id 		= get_ad_id(get_the_ID());
	$ad_type 	= get_post_meta($ad_id,'ad_type_format',true);
	$ad_vendor	= get_post_meta($ad_id,'ad_vendor',true);
	if('4' === $ad_type){
		if('1' === $ad_vendor){
		 ampforwp_sticky_adsense_ads();
		}
		elseif('2' === $ad_vendor){
			ampforwp_dfp_sticky_ads();
		}
		elseif('3' === $ad_vendor){
			ampforwp_custom_sticky_ads();
		}
		else{
			adsforwp_media_net_sticky_ads();
		}
	}
}

// AMP Auto Ads Support

add_action('ampforwp_body_beginning','ampforwp_adv_amp_auto_ads');

function ampforwp_adv_amp_auto_ads(){
	$ad_id 		= get_ad_id(get_the_ID());
	$ad_type 	= get_post_meta($ad_id,'ad_type_format',true);
	$global_visibility  = get_post_meta($ad_id,'ad_visibility_status',true);
	if($global_visibility != 'hide'){
		if('5' === $ad_type){
			$ampauto_ad_code	    = get_post_meta($ad_id,'amp_auto_ad_type',true);
			echo $ampauto_ad_code;
		}
	}
}

// InBetween Loops Ads

add_action('ampforwp_between_loop','ampforwp_inbetween_loop_ads');

function ampforwp_inbetween_loop_ads($count){
	Global $post;
	$displayed_posts = get_option('posts_per_page');
	$in_between = round(abs($displayed_posts / 2));
	$ID = $post->ID;
	if(intval($in_between) === $count){
		$ad_position = get_post_meta(get_ad_id($ID),'normal_ad_type',true);
		if('12' === $ad_position){
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

// added extra css to improve user experiance for sticky ads
add_action( 'amp_post_template_css', 'ampforwp_extra_sticky_css_styles' );
function ampforwp_extra_sticky_css_styles( $amp_template ) {
 ?>
	amp-sticky-ad {
		z-index: 9999
	}
	.aa_wrp, .aa_wrp amp-img, .aa_wrp amp-anim, .aa_wrp amp-ad{
    margin: 0 auto;
    text-align: center;
}

<?php $ad_id 		= get_ad_id(get_the_ID());
	$ad_type 	= get_post_meta($ad_id,'ad_type_format',true);
	$ad_vendor	= get_post_meta($ad_id,'ad_vendor',true);
	if('4' === $ad_type){
	 if('3' === $ad_vendor){?>
	.ampforwp-sticky-custom-ad{
		position: fixed;
		bottom:0;
		text-align: center;
		left: 0;
		width: 100%;
		z-index: 11;
		max-height: 100px;
		box-sizing: border-box;
		opacity: 1;
		background-image: none;
		background-color: #fff;
		box-shadow: 0 0 5px 0 rgba(0,0,0,.2);
		margin-bottom: 0;
		 }
	body{
		padding-bottom: 40px;
	}	
<?php }
}
}

// Adding the required scripts

add_filter( 'amp_post_template_data', 'ampforwp_adsforwp_scripts', 30 );
function ampforwp_adsforwp_scripts( $data ) {
	Global $post;
	$id = $post->ID;
	$post_ad_id = get_ad_id(get_the_ID());
	$show_ads 	= '';
	$show_ads = 'yes';		
	$show_ads = apply_filters('adsforwp_advert_on_off', $show_ads);


	// if ( $show_ads != 'yes' ) {
	// 	return $data ; // Do not show ads and return the data as it is
	// }
	$selected_ads_for 	= get_post_meta($post_ad_id,'select_ads_for',true);
	if('1' === $selected_ads_for){
		$ad_type 	= get_post_meta($post_ad_id,'ad_type_format',true);
		$ad_vendor	= get_post_meta($post_ad_id,'ad_vendor',true);

		$adsense_parallax 	= get_post_meta($post_ad_id,'adsense_parallax',true);
		$dfp_parallax 		= get_post_meta($post_ad_id,'dfp_parallax',true);
		$custom_parallax 	= get_post_meta($post_ad_id,'custom_parallax',true);

		$ad_visibility_status = get_post_meta($post_ad_id,'ad_visibility_status',true);
	}
	elseif('2' === $selected_ads_for){
		$ad_type 	= get_post_meta($post_ad_id,'_amp_ad_type_format',true);
		$ad_vendor	= get_post_meta($post_ad_id,'_amp_ad_vendor',true);

		$adsense_parallax 	= get_post_meta($post_ad_id,'_amp_adsense_parallax',true);
		$dfp_parallax 		= get_post_meta($post_ad_id,'_amp_dfp_parallax',true);
		$custom_parallax 	= get_post_meta($post_ad_id,'_amp_custom_parallax',true);

		$ad_visibility_status = get_post_meta($post_ad_id,'_amp_ad_visibility_status',true);
	}
	
	
		

		if('1' === $ad_type || '2' === $ad_type || '3' === $ad_type || '4' === $ad_type || '5' === $ad_type ) {	
				if('1' === $ad_vendor || '2' === $ad_vendor || '3' === $ad_vendor){
					if ( empty( $data['amp_component_scripts']['amp-ad'] ) ) {
						$data['amp_component_scripts']['amp-ad'] = 'https://cdn.ampproject.org/v0/amp-ad-0.1.js';
					}
					if( '3' !== $ad_vendor) {
						if ( empty( $data['amp_component_scripts']['amp-sticky-ad'] ) ) {
							$data['amp_component_scripts']['amp-sticky-ad'] = 'https://cdn.ampproject.org/v0/amp-sticky-ad-1.0.js';
						}
					}
				}
			}

			if('show' == $ad_visibility_status){
				if(	'5' === $ad_type ) {
								
					if ( empty( $data['amp_component_scripts']['amp-auto-ads'] ) ) {
						$data['amp_component_scripts']['amp-auto-ads'] = 'https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js';
					}
				}
			}


			if(	'on' === $adsense_parallax || 'on' === $dfp_parallax || 'on' === $custom_parallax ) {
							
				if ( empty( $data['amp_component_scripts']['amp-fx-flying-carpet'] ) ) {
					$data['amp_component_scripts']['amp-fx-flying-carpet'] = 'https://cdn.ampproject.org/v0/amp-fx-flying-carpet-0.1.js';
				}
		}
	

	return $data;
}

/*
 * Generate the proper ads id
 */
function get_ad_id($id){
	$meta_details = get_metadata('post',$id,'adsforwp-advert-data',true);
	$post_ad_data = $meta_details;
	$post_id 	  = $id;
	$post_ad_id   = '';
	$all_ads_post = get_posts( array( 'post_type' => 'ads-for-wp-ads','posts_per_page' => -1));
	foreach ($all_ads_post as $ads) {
		$post_ad_id = $ads->ID;
		$ad_position = get_post_meta($post_ad_id,'normal_ad_type',true);
		if('12' === $ad_position){
			$inbetween_id = $post_ad_id;
			return $inbetween_id;
		}
		if('13' === $ad_position){
			$inbetween_id = $post_ad_id;
			return $inbetween_id;
		}
	}
	// foreach ($post_ad_data as $key => $ad_config) {
	// 	$post_ad_id = $ad_config['ads_id'];
	// 	$post_ad_id = (int)$post_ad_id;
	// }
	return $post_ad_id;
}


/*
 *  Insert the ads according to the settings
 */
add_action('pre_amp_render_post','ampforwp_display_amp_ads');
function ampforwp_display_amp_ads(){
	// Declare variables
	$all_ads_post 		= '';
	$post_ad_id 		= ''; 
	$post_data 			= ''; 
	$selected_ads_for 	= ''; 
	$ad_type 			= ''; 
	$ad_vendor 			= ''; 
	$global_visibility 	= ''; 
	$ad_position 		= '';

	// Arrays
	$args = array();

	$all_ads_post = get_posts(
					array(
						'post_type' 	 => 'ads-for-wp-ads',
						'posts_per_page' => -1,
						'meta_query' 	 => array(
					        array(
					            'key'   => 'ad_visibility_status',
					            'value'	=> 'show'
					        ),
					        array(
					            'key'   => '_amp_ad_visibility_status',
					            'value'	=> 'show'
					        )
					    ) 
					)
				);

	foreach ($all_ads_post as $ads) {
		$post_ad_id = $ads->ID;
		$args = array (
	    	'id'        =>  $post_ad_id, // id
	    );


		$post_data = get_post_meta($post_ad_id);

		if ( $post_data['select_ads_for'] ) {
			$selected_ads_for 	= $post_data['select_ads_for'][0];
		}
		if ( $post_data['ad_type_format'] ) {
			$ad_type 			= $post_data['ad_type_format'][0];
		}
		if ( $post_data['ad_vendor'] ) {
			$ad_vendor 			= $post_data['ad_vendor'][0];
		}
		if ( $post_data['ad_visibility_status'] ) {
			$global_visibility 	= $post_data['ad_visibility_status'][0];
		}		
		if ( $post_data['normal_ad_type'] ) {
			$ad_position = $post_data['normal_ad_type'][0];
		}
	
		if('1' === $selected_ads_for) {
			if($global_visibility != 'hide') {
				// Normal Ads
				if('1' === $ad_type){					
					switch ($ad_position) {
						case '1':
							//  "Above Header";
							 if('1' === $ad_vendor){
							 		// $id = get_ad_id(get_the_ID());
									 add_action('ampforwp_header_top_design2','ampforwp_adsense_ads');
								}
								else if('2' === $ad_vendor){
									add_action('ampforwp_header_top_design2','ampforwp_dfp_ads');
								}
								else if('3' === $ad_vendor){
									add_action('ampforwp_header_top_design2','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_header_top_design2',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '2':
							//  "Below Header";
								if('1' === $ad_vendor){
									 add_action('ampforwp_after_header','ampforwp_adsense_ads');
								}
								else if('2' === $ad_vendor){
									add_action('ampforwp_after_header','ampforwp_dfp_ads');
								}
								else if('3' === $ad_vendor){
									add_action('ampforwp_after_header','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_after_header',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '3':
							//  "Before Title";
								//  Adsense Ad
								if('1' === $ad_vendor){
									 add_action('ampforwp_above_the_title','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('ampforwp_above_the_title','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('ampforwp_above_the_title','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_above_the_title',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '4':
							//  "After Title";
							 	if('1' === $ad_vendor){
									 add_action('ampforwp_below_the_title','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('ampforwp_below_the_title','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('ampforwp_below_the_title','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_below_the_title',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '5':
							//  "Before Content";
								if('1' === $ad_vendor){
									 add_action('ampforwp_before_post_content','ampforwp_adsense_ads');
								} 
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('ampforwp_before_post_content','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('ampforwp_before_post_content','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_before_post_content',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '6':
							//  "After Featured Image";
							 	if('1' === $ad_vendor){
									 add_action('ampforwp_after_featured_image_hook','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('ampforwp_after_featured_image_hook','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('ampforwp_after_featured_image_hook','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_after_featured_image_hook',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '7':
							//  "After Content";
							 	if('1' === $ad_vendor){
							 		if(is_single()){
									 add_action('ampforwp_after_post_content','ampforwp_adsense_ads');
							 		}
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									if(is_single()){
										add_action('ampforwp_after_post_content','ampforwp_dfp_ads');
									}
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									if(is_single()){
										add_action('ampforwp_after_post_content','ampforwp_custom_ads');
									}
								}
								else if('4' === $ad_vendor){
									if(is_single()){
										add_action('ampforwp_after_post_content',function() use ( $args ) { 
		              						 adsforwp_media_net_ads( $args ); });
									}
								}
							break;
						case '8':
							//  "Above Related Posts";
							 	if('1' === $ad_vendor){
									 add_action('ampforwp_above_related_post','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('ampforwp_above_related_post','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('ampforwp_above_related_post','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_above_related_post',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '9':
							//  "Below Related Posts";
							 	if('1' === $ad_vendor){
									 add_action('ampforwp_below_related_post','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('ampforwp_below_related_post','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('ampforwp_below_related_post','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_below_related_post',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '10':
							//  "Before Footer";
							 	if('1' === $ad_vendor){
									 add_action('amp_post_template_above_footer','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									add_action('amp_post_template_above_footer','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									add_action('amp_post_template_above_footer','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('amp_post_template_above_footer',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						case '11':
							//  "After Footer";
							 	if('1' === $ad_vendor){
									 add_action('ampforwp_global_after_footer','ampforwp_adsense_ads');
								}
								// DFP Ad
								else if('2' === $ad_vendor){
									 add_action('ampforwp_global_after_footer','ampforwp_dfp_ads');
								}
								// Custom Ad
								else if('3' === $ad_vendor){
									 add_action('ampforwp_global_after_footer','ampforwp_custom_ads');
								}
								else if('4' === $ad_vendor){
									add_action('ampforwp_global_after_footer',function() use ( $args ) { 
		               adsforwp_media_net_ads( $args ); });
								}
							break;
						default:
							//  "please select the postion to display";
							break;
					}

				}
			}
		}


		// FOR AMP BY AUTOMATTIC Normal And Incontent Ads
		if ( $post_data['_amp_ad_type_format'] ) {
			$amp_ad_type = $post_data['_amp_ad_type_format'][0];
		}		
		if ( $post_data['_amp_ad_vendor'] ) {
			$amp_ad_vendor = $post_data['_amp_ad_vendor'][0];
		}		
		if ( $post_data['_amp_ad_visibility_status'] ) {
			$global_visibility = $post_data['_amp_ad_visibility_status'][0];
		}

		elseif('2' === $selected_ads_for) {
			if($global_visibility != 'hide'){
			// Normal
				if('1' === $amp_ad_type){
					switch ($amp_ad_vendor) {
						case '1':
							add_action('amp_post_template_footer','ampforwp_adsense_ads');
							break;
						case '2':
							add_action('amp_post_template_footer','ampforwp_dfp_ads');
							break;
						case '3':
							add_action('amp_post_template_footer','ampforwp_custom_ads');
							break;
						case '4':
							add_action('amp_post_template_footer','adsforwp_media_net_ads');
							break;
						default:
							add_action('amp_post_template_footer','ampforwp_adsense_ads');
							break;
					}
					
				}
			}
		}
	}
}
