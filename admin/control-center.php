<?php
add_action( 'init', 'adsforwp_setup_post_type' );

function adsforwp_setup_post_type() {
    $args = array(
	    'labels' => array(
	        'name' 				=> esc_html__( 'Ads', 'ads-for-wp' ),
	        'singular_name' 	=> esc_html__( 'Ad', 'ads-for-wp' ),
	        'add_new' 			=> esc_html__( 'Add New Ad', 'ads-for-wp' ),
	        'add_new_item'  	=> esc_html__( 'Add New Ad', 'ads-for-wp' ),
                'edit_item'             => esc_html__('Edit AD','ads-for-wp')
	    ),
      	'public' 				=> true,
      	'has_archive' 			=> false,
      	'exclude_from_search'	=> true,
    	'publicly_queryable'	=> true,
        
    );
    register_post_type( 'ads-for-wp-ads', $args );
}

/*
 * Hiding WYSIWYG For AMPforWP Ads 2.0, as there is no need for it 
*/
add_action( 'admin_init', 'removing_wysiwig_adsforwp' );

function removing_wysiwig_adsforwp() {
    remove_post_type_support( 'ads-for-wp-ads', 'editor' );
}


/*
 * Generating Ad ShortCode
 */

add_shortcode('ads-for-wp', 'adsforwp_shortcode_generator');
function adsforwp_shortcode_generator( $atts ){
	$adsPostId 			= '';
	$selected_ads_for 	= '';
	$content 			= '';
	$show_ads 			= '';
	$ad_vendor 			= '';
	$ad_type   			= '';
	$global_visibility 	= '';

	$adsPostId 			= $atts["ads-id"];
        
        $post_meta_dataset = get_post_meta($adsPostId,$key='',true);        
        
	$selected_ads_for 	= $post_meta_dataset['select_ads_for'][0];
	$show_ads 			= 'yes';
	$show_ads 			= apply_filters('adsforwp_advert_on_off', $show_ads);

	if('1' === $selected_ads_for){
		$ad_vendor = $post_meta_dataset['ad_vendor'][0];
		$ad_type   = $post_meta_dataset['ad_type_format'][0];
	}
	elseif('2' === $selected_ads_for){
		$ad_vendor = $post_meta_dataset['_amp_ad_vendor'][0];
		$ad_type   = $post_meta_dataset['_amp_ad_type_format'][0];
	}			
	
	if('1' === $selected_ads_for){
		$global_visibility  = $post_meta_dataset['ad_visibility_status'][0];
		if($global_visibility != 'show'){
			$show_ads = 'no';
		}
	}
	elseif('2' === $selected_ads_for){
		$global_visibility  = $post_meta_dataset['_amp_ad_visibility_status'][0];
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
				elseif('4' === $ad_vendor && '3' === $ad_type){
					$content = 	adsforwp_incontent_media_net_ads($adsPostId);
				}
			else{
				if('3' === $ad_type){
					$content = get_post_field('post_content', $atts['ads-id']);
				}
			}
		}
	}
 /* $content = '';
  $show_ads   = '';

  $ad_id = $atts['ads-id'];
  $show_ads = 'yes';    
  $show_ads = apply_filters('adsforwp_advert_on_off', $show_ads, $ad_id);

  if ( $show_ads == 'yes' ) {
    $content = get_post_field('post_content', $atts['ads-id']);
  }*/

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
	$show_ads = apply_filters('adsforwp_advert_on_off',  $show_ads, $currentPostId);

	if ( $show_ads != 'yes' ) {
		return $content ; // Do not show ads and return the content as it is
	}
	if(function_exists('ampforwp_is_front_page')){
		if(!is_singular() && !ampforwp_is_front_page()){
			return $content;
		}
	}

	$post_meta = get_post_meta($currentPostId, 'adsforwp-advert-data', true);
        
	if(empty($post_meta)){
		$post_meta = array('post_id' => '',
				            'ads_id' => '',
				            'visibility' => '',
				            'paragraph' => '',
				            'content'=>'',);

	}
	$selected_ads_for 	= get_post_meta(get_ad_id(get_the_ID()),'select_ads_for',true);
	$incontent_ad_type 	= get_post_meta(get_ad_id(get_the_ID()),'incontent_ad_type',true);
	if('1' === $selected_ads_for){
		if($incontent_ad_type == '1'){
			$closing_p = '</p>';
			$paragraphs = explode( $closing_p, $content );
			$paragraph_id =  count($paragraphs);
			$paragraph_id = $paragraph_id / 2 ;
			$paragraph_id = round($paragraph_id);
			$cmb2_incontent_options = $paragraph_id;
			$incontent_visibility = 'ad_visibility_status';
			$amp_ad_type 			= 'ad_type_format';
		}
		elseif($incontent_ad_type == '2'){
			$cmb2_incontent_options = get_metadata('post',get_ad_id(get_the_ID()), 'incontent_ad_type_paragraph');
			$incontent_visibility = 'ad_visibility_status';
			$amp_ad_type 			= 'ad_type_format';
		}
		
	}
	elseif('2' === $selected_ads_for){  
		$amp_incontent_ad_type =  get_post_meta(get_ad_id(get_the_ID()),'_amp_incontent_ad_type',true);
		  
		if($amp_incontent_ad_type == '1'){
			$closing_p = '</p>';
			$paragraphs = explode( $closing_p, $content );
			$paragraph_id =  count($paragraphs);
			$paragraph_id = $paragraph_id / 2 ;
			$paragraph_id = round($paragraph_id);
			$cmb2_incontent_options = $paragraph_id;
			$incontent_visibility = 'ad_visibility_status';
			$amp_ad_type 			= 'ad_type_format';
		}
		elseif($amp_incontent_ad_type == '2'){
			$cmb2_incontent_options = get_metadata('post',get_ad_id(get_the_ID()), '_amp_incontent_ad_type_paragraph');
			$incontent_visibility = 'ad_visibility_status';
			$amp_ad_type 			= 'ad_type_format';
		}
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
	    $non_amp_ads 		= get_post_meta($adsPostId,'non_amp_ads',true);
	    if($adsType =='hide' ){
	    	continue;
	    }
	    if($ads_format != '2'){
	    	continue;
	    }
 

	    if(isset($cmb2_incontent_options)){
	    	if('1' === $selected_ads_for){
	    	  if('on' === $non_amp_ads || function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint()){	
		    	
			    	$adsVisiblityType 	= get_post_meta($currentPostId,'adsforwp-advert-data',true);

			    	$adsVisiblityType 	= (isset($adsVisiblityType[$adsPostId]['visibility'])
			    		? $adsVisiblityType[$adsPostId]['visibility'] 
			    		: get_post_meta($adsPostId,'ad_visibility_status',true) 
			    							);

				$incontent_ad_type 	= get_post_meta(get_ad_id(get_the_ID()),'incontent_ad_type',true);
		 
				if($incontent_ad_type == '1'){
					$closing_p = '</p>';
					$paragraphs = explode( $closing_p, $content );
					$paragraph_id =  count($paragraphs)-1;
					$paragraph_id = $paragraph_id / 2 ;
					$paragraph_id = round($paragraph_id);

					$adsparagraphs 		= ( isset($post_meta[$adsPostId]['paragraph'])
					    		? $post_meta[$adsPostId]['paragraph'] 
					    		: $paragraph_id );
				}
				elseif($incontent_ad_type == '2'){

			    	$adsparagraphs 		= ( isset($post_meta[$adsPostId]['paragraph'])
			    		? $post_meta[$adsPostId]['paragraph'] 
			    		: get_post_meta($adsPostId,'incontent_ad_type_paragraph',true) );
				    }

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
					elseif('4' === $ad_vendor && '2' === $ad_type){
					$adsContent = 	adsforwp_incontent_media_net_ads($adsPostId);/*var_dump($adsContent);die;*/
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
				if('on' === $non_amp_ads || function_exists('is_amp_endpoint') && is_amp_endpoint()){	
			    	$adsVisiblityType 	= get_post_field('adsforwp_incontent_ads_default', $post_id);
				    $adsVisiblityType 	= (isset($adsVisiblityType[$adsPostId]['visibility'])
			    		? $adsVisiblityType[$adsPostId]['visibility'] 
			    		: get_post_meta($adsPostId,'_amp_ad_visibility_status',true) 
			    							);


				$amp_incontent_ad_type 	= get_post_meta(get_ad_id(get_the_ID()),'_amp_incontent_ad_type',true);
		 	
				if($amp_incontent_ad_type == '1'){
					$closing_p = '</p>';
					$paragraphs = explode( $closing_p, $content );
					$paragraph_id =  count($paragraphs)-1;
					$paragraph_id = $paragraph_id / 2 ;
					$paragraph_id = round($paragraph_id);

					$adsparagraphs 		= ( isset($post_meta[$adsPostId]['paragraph'])
					    		? $post_meta[$adsPostId]['paragraph'] 
					    		: $paragraph_id );
				}
				elseif($amp_incontent_ad_type == '2'){

			    	$adsparagraphs 		= ( isset($post_meta[$adsPostId]['paragraph'])
			    		? $post_meta[$adsPostId]['paragraph'] 
			    		: get_post_meta($adsPostId,'_amp_incontent_ad_type_paragraph',true) );
				    }

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
					elseif('4' === $ad_vendor && '3' === $ad_type){
					$adsContent = 	adsforwp_incontent_media_net_ads($adsPostId);
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
	$isPagesSplits = false;
	if(strpos($content, '<!--nextpage-->')!==false){
		$contents = preg_split("<!--nextpage-->", $content);
		$isPagesSplits = true;
	}else{
		$contents = array($content);
	}
	$completeContents = '';
	foreach ($contents as $pagekey => $content) {
		
		$content = preg_split("/\\r\\n|\\r|\\n/", $content);
		if(count($post_meta)>0){
			foreach ($post_meta as $key => $adsValue) {

				if(!empty($adsValue) && $adsValue['visibility']!="show"){
					continue;
				}
				if(isset($adsValue['paragraph']) && isset($adsValue['content'])){
					 
					if(count($content) > intval($adsValue['paragraph'])){
						$content[$adsValue['paragraph']-1] .= $adsValue['content'];
					} 
				}
				
			}
			
			$completeContents .= implode(' ', $content);
		} 
		//Check for page splits
		if($isPagesSplits){
			$completeContents .= '!--nextpage--';
		}
	}
 	return $completeContents; 
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