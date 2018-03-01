<?php
add_action( 'init', 'adsforwp_setup_post_type' );

function adsforwp_setup_post_type() {
    $args = array(
      'labels' => array(
        'name' 			=> esc_html__( 'Ads', 'ads-for-wp' ),
        'singular_name' => esc_html__( 'Ad', 'ads-for-wp' ),
        'add_new' 		=> esc_html__( 'Add New Ad', 'ads-for-wp' )
      ),
      	'public' 		=> true,
      	'has_archive' => false,
      	'exclude_from_search'	=> true,
    	'publicly_queryable'	=> false
    );
    register_post_type( 'ads-for-wp-ads', $args );
}


/*
 * Hiding Visaul Editor part, as there is no need for Visual Editor to add Advert Code 
*/

/*
	Not Needed Anymore
 */

// add_filter( 'user_can_richedit', 'adsforwp_hide_visual_editor');

// function adsforwp_hide_visual_editor($content) {
//     global $post_type;

//     if ('ads-for-wp-ads' == $post_type)
//         return false;
//     return $content;
// }

/*
 * Hiding WYSIWYG For AMPforWP Ads 2.0, as there is no need for it 
*/
add_action( 'init', 'removing_wysiwig_adsforwp' );

function removing_wysiwig_adsforwp() {
    remove_post_type_support( 'ads-for-wp-ads', 'editor' );
}

/*
 * Generating Ad ShortCode
 */

add_shortcode('ads-for-wp', 'adsforwp_shortcode_generator');
function adsforwp_shortcode_generator( $atts ){
	$adsPostId = get_ad_id(get_the_ID());
	$selected_ads_for 	= get_post_meta($adsPostId,'select_ads_for',true);
	if('1' === $selected_ads_for){
		$ad_vendor = get_post_meta($adsPostId,'ad_vendor',true);
		$ad_type   = get_post_meta($adsPostId,'ad_type_format',true);
	}
	elseif('2' === $selected_ads_for){
		$ad_vendor = get_post_meta($adsPostId,'_amp_ad_vendor',true);
		$ad_type   = get_post_meta($adsPostId,'_amp_ad_type_format',true);
	}
	
	$content = '';
	$show_ads 	= '';

	$show_ads = 'yes';		
	$show_ads = apply_filters('adsforwp_advert_on_off', $show_ads);
	if('1' === $selected_ads_for){
		$global_visibility  = get_post_meta($adsPostId,'ad_visibility_status',true);
		if($global_visibility != 'show'){
			$show_ads = 'no';
		}
	}
	elseif('2' === $selected_ads_for){
		$global_visibility  = get_post_meta($adsPostId,'_amp_ad_visibility_status',true);
		if($global_visibility != 'show'){
			$show_ads = 'no';
		}
	}	
	if ( $show_ads == 'yes' ) {
		if(function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint()){
			if('1' === $ad_vendor && '3' === $ad_type){
				    $content = ampforwp_incontent_adsense_ads($adsPostId);
				}
				elseif('2' === $ad_vendor && '3' === $ad_type){
					$content = ampforwp_incontent_dfp_ads($adsPostId);
				}
				elseif('3' === $ad_vendor && '3' === $ad_type){
					$content = ampforwp_incontent_custom_ads($adsPostId);
				}
			else{
				if('3' === $ad_type){
					$content = get_post_field('post_content', $atts['ads-id']);
				}
			}
		}
	}

	return $content ;
}

add_action( 'wp_ajax_save_ads_data', 'adsforwp_save_ads_data' );
function adsforwp_save_ads_data() {
    // Handle request then generate response using WP_Ajax_Response
	$data 		= "";
	$save_data 	= array();

	$data 		= $_POST['adsdata'];

	$save_data[ $data['ads_id'] ] = $data;

	$current_post_meta = get_post_meta($data['post_id'], 'adsforwp-advert-data', true);

	if (  $current_post_meta  ) {
		$save_data = array_replace_recursive($current_post_meta, $save_data);
	}

	update_post_meta($data['post_id'], 'adsforwp-advert-data', $save_data);

	// Send the updated and final data back to ajax so it can update the view dynamically
	echo json_encode($save_data);	
    // Don't forget to stop execution afterward.
    wp_die();
}

/*
 * Insert the ad in the Content
*/
add_filter('the_content', 'adsforwp_insert_ads');

function adsforwp_insert_ads( $content ){
	global $post,$post_id;
	// $post_id = $post->ID;
	// $cmb2_incontent_options = '';
	$currentPostId = $post->ID;

	$show_ads 	= '';

	$show_ads = 'yes';		
	$show_ads = apply_filters('adsforwp_advert_on_off', $show_ads);

	if ( $show_ads != 'yes' ) {
		return $content ; // Do not show ads and return the content as it is
	}

	if(!is_singular()){
		return $content;
	}

	$post_meta = get_post_meta($currentPostId, 'adsforwp-advert-data', true);
	$selected_ads_for 	= get_post_meta(get_ad_id(get_the_ID()),'select_ads_for',true);
	if('1' === $selected_ads_for){
		$cmb2_incontent_options = get_metadata('post',get_ad_id(get_the_ID()), 'incontent_ad_type');
		$incontent_visibility = 'ad_visibility_status';
		$amp_ad_type 			= 'ad_type_format';
		
	}
	elseif('2' === $selected_ads_for){
		$cmb2_incontent_options = get_metadata('post',get_ad_id(get_the_ID()), '_amp_incontent_ad_type');
		$incontent_visibility 	= '_amp_ad_visibility_status';
		$amp_ad_type 			= '_amp_ad_type_format';
	}
	
	//Get all other adds which are set to inline
	$args = array(
				'post_type'		=>'ads-for-wp-ads',
				'post_status'	=>'publish',
				'posts_per_page'=> -1,
				'meta_query'	=>array(
					'adsforwp_ads_position'=>'hide',
				array(
					'key' 	=> $incontent_visibility,
					'value' => 'show',
				)
		
				)
			);
	$query = new WP_Query( $args );
	while ($query->have_posts()) {
	    $query->the_post();
	    $adsPostId = get_the_ID();
	    $adsType = get_post_meta($adsPostId, $incontent_visibility,true);
	    $ads_format = get_post_meta($adsPostId, $amp_ad_type,true);
	    
	    if($adsType =='hide' ){
	    	continue;
	    }
	    if($ads_format != '2'){
	    	continue;
	    }


	    if(isset($cmb2_incontent_options)){
	    	if('1' === $selected_ads_for){
		    	if(function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint()){
			    	$adsVisiblityType 	= get_post_meta($currentPostId,'adsforwp-advert-data',true);

			    	$adsVisiblityType 	= (isset($adsVisiblityType[$adsPostId]['visibility'])
			    		? $adsVisiblityType[$adsPostId]['visibility'] 
			    		: get_post_meta($adsPostId,'ad_visibility_status',true) 
			    							);
			    	$adsparagraphs 		= ( isset($post_meta[$adsPostId]['paragraph'])
			    		? $post_meta[$adsPostId]['paragraph'] 
			    		: get_post_meta($adsPostId,'incontent_ad_type',true) );


				    $ad_vendor 			= get_post_meta($adsPostId,'ad_vendor',true);
				    $ad_type 			= get_post_meta($adsPostId,'ad_type_format',true);
			    	if('1' === $ad_vendor && '2' === $ad_type){
					    $adsContent = ampforwp_incontent_adsense_ads($adsPostId);
					    $post_meta[$adsPostId] = array(				
										            'post_id' => $currentPostId,
										            'ads_id' => $adsPostId,
										            'visibility' => $adsVisiblityType,
										            'paragraph' => $adsparagraphs,
										            'content'=>$adsContent,
				    							);
					}
					elseif('2' === $ad_vendor && '2' === $ad_type){
						$adsContent = ampforwp_incontent_dfp_ads($adsPostId);
					    $post_meta[$adsPostId] = array(				
										            'post_id' => $currentPostId,
										            'ads_id' => $adsPostId,
										            'visibility' => $adsVisiblityType,
										            'paragraph' => $adsparagraphs,
										            'content'=>$adsContent,
				    							);
					}
					elseif('3' === $ad_vendor && '2' === $ad_type){
						$adsContent = ampforwp_incontent_custom_ads($adsPostId);
					    $post_meta[$adsPostId] = array(				
										            'post_id' => $currentPostId,
										            'ads_id' => $adsPostId,
										            'visibility' => $adsVisiblityType,
										            'paragraph' => $adsparagraphs,
										            'content'=>$adsContent,
				    							);
					}
				}
			}
			if('2' === $selected_ads_for){
			    	$adsVisiblityType 	= get_post_field('adsforwp_incontent_ads_default', $post_id);
				    $adsVisiblityType 	= (isset($adsVisiblityType[$adsPostId]['visibility'])
			    		? $adsVisiblityType[$adsPostId]['visibility'] 
			    		: get_post_meta($adsPostId,'_amp_ad_visibility_status',true) 
			    							);
			    	$adsparagraphs 		= ( isset($post_meta[$adsPostId]['paragraph'])
			    		? $post_meta[$adsPostId]['paragraph'] 
			    		: get_post_meta($adsPostId,'_amp_incontent_ad_type',true) );
				    $ad_vendor 			= get_post_meta($adsPostId,'_amp_ad_vendor',true);
				    $ad_type 			= get_post_meta($adsPostId,'_amp_ad_type_format',true);
			    	if('1' === $ad_vendor && '2' === $ad_type){
					    $adsContent = ampforwp_incontent_adsense_ads($adsPostId);
					    $post_meta[$adsPostId] = array(				
										            'post_id' => $currentPostId,
										            'ads_id' => $adsPostId,
										            'visibility' => $adsVisiblityType,
										            'paragraph' => $adsparagraphs,
										            'content'=>$adsContent,
				    							);

					}
					elseif('2' === $ad_vendor && '2' === $ad_type){
						$adsContent = ampforwp_incontent_dfp_ads($adsPostId);
					    $post_meta[$adsPostId] = array(				
										            'post_id' => $currentPostId,
										            'ads_id' => $adsPostId,
										            'visibility' => $adsVisiblityType,
										            'paragraph' => $adsparagraphs,
										            'content'=>$adsContent,
				    							);
					}
					elseif('3' === $ad_vendor && '2' === $ad_type){
						$adsContent = ampforwp_incontent_custom_ads($adsPostId);
					    $post_meta[$adsPostId] = array(				
										            'post_id' => $currentPostId,
										            'ads_id' => $adsPostId,
										            'visibility' => $adsVisiblityType,
										            'paragraph' => $adsparagraphs,
										            'content'=>$adsContent,
				    							);
					}
				
			}
	    }
	    elseif(!isset($post_meta[$adsPostId])){

	    	$adsVisiblityType = get_post_field('adsforwp_incontent_ads_default', $post_id);
		    $adsparagraphs = get_post_field('adsforwp_incontent_ads_paragraphs', $post_id);
		    $adsContent = ampforwp_incontent_adsense_ads($adsPostId);
		    $post_meta[$adsPostId] = array(				
							            'post_id' => $currentPostId,
							            'ads_id' => $adsPostId,
							            'visibility' => $adsVisiblityType,
							            'paragraph' => $adsparagraphs,
							            'content'=>$adsContent,
	    							);
	    }
	    else{
	    	$adsContent = get_post_field('post_content', $adsPostId);
	    	$ad_vendor = get_post_meta($adsPostId,'ad_vendor',true);
	    	if('1' === $ad_vendor){
	    		 $post_meta[$adsPostId]['content'] = ampforwp_incontent_adsense_ads($adsPostId);

	    	}
	    	elseif('2' === $ad_vendor){
	    		 $post_meta[$adsPostId]['content'] = ampforwp_incontent_dfp_ads($adsPostId);

	    	}
	    	elseif('3' === $ad_vendor){
	    		 $post_meta[$adsPostId]['content'] = ampforwp_incontent_custom_ads($adsPostId);

	    	}else{
	    		$post_meta[$adsPostId]['content'] = $adsContent;
	    	}
	    }
	    
	}
	wp_reset_query();
	ksort($post_meta);
	$content = preg_split("/\\r\\n|\\r|\\n/", $content);
	if(count($post_meta)>0){
		foreach ($post_meta as $key => $adsValue) {
			if($adsValue['visibility']!="show"){
				continue;
			}
			if(isset($adsValue['paragraph']) && isset($adsValue['content'])){
			
				if(count($content) > $adsValue['paragraph']){

					$content[$adsValue['paragraph']] .= $adsValue['content'];
				}

				//array_splice( $content, $adsValue['paragraph'], 0, $adsValue['content'] );
			}
			
		}
		$content = implode(' ', $content);
	}

	return $content; 
}

/*
 *	Enqueue Javascript and CSS in admin area
 */
add_action('admin_enqueue_scripts','adsforwp_admin_enqueue');

function adsforwp_admin_enqueue() {

	wp_enqueue_style( 'adsforwp-admin', ADSFORWP_PLUGIN_DIR_URI . 'assets/ads.css', false , ADSFORWP_VERSION );


	wp_register_script( 'adsforwp-admin-js', ADSFORWP_PLUGIN_DIR_URI . 'assets/ads.js', array('jquery'), ADSFORWP_VERSION , true );

	// Localize the script with new data
	$data = array(
		'ajax_url'  => admin_url( 'admin-ajax.php' ),
		'id'		=> get_the_ID()
	);
	wp_localize_script( 'adsforwp-admin-js', 'adsforwp_localize_data', $data );
	
	// Enqueued script with localized data.
	wp_enqueue_script( 'adsforwp-admin-js' );

}
