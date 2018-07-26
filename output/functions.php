<?php
/*
 *  Display the ads according to the settings
 */
add_filter('the_content', 'adsforwp_display_ads');
function adsforwp_display_ads($content){

        if ( is_single() ) {                       
        $current_post_data = get_post_meta(get_the_ID(),$key='',true);  
        $visibility ='';
        if(array_key_exists('ads-for-wp-visibility', $current_post_data)){
        $visibility = $current_post_data['ads-for-wp-visibility'][0];    
        }                                
        $is_amp = 'no';
        if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
            $is_amp = 'yes';        
        }                    
        if($visibility == 'show') {
            $all_ads_post = json_decode(get_transient('adsforwp_transient_ads_ids'), true);              
            foreach($all_ads_post as $ads){
            $post_ad_id = $ads;                           
            $where_to_display=""; 
            $adposition="";    
            $post_meta_dataset = get_post_meta($post_ad_id,$key='',true);
            $ad_code =  adsforwp_get_ad_code($post_ad_id); 
            if(array_key_exists('wheretodisplay', $post_meta_dataset)){
            $where_to_display = $post_meta_dataset['wheretodisplay'][0];  
            }
            if(array_key_exists('adposition', $post_meta_dataset)){
            $adposition = $post_meta_dataset['adposition'][0];    
            }
                                                                                                                                             
           //Displays all ads according to their settings paragraphs starts here              
            switch ($where_to_display) {
                
             case 'after_the_content':
              $content = $content.$ad_code;
              break;
             case 'before_the_content':
              $content = $ad_code.$content;
              break;
             case 'between_the_content':        
              if($adposition == 'number_of_paragraph'){
                $paragraph_id = $post_meta_dataset['paragraph_number'][0];   
                $closing_p = '</p>';
                $paragraphs = explode( $closing_p, $content );   
                foreach ($paragraphs as $index => $paragraph) {

                 if ( trim( $paragraph ) ) {
                       $paragraphs[$index] .= $closing_p;
                   }
                   if ( $paragraph_id == $index + 1 ) {
                       $paragraphs[$index] .= $ad_code;
                   }
                 }
                        $content = implode( '', $paragraphs );
                }
        
               if($adposition == '50_of_the_content'){
                 $closing_p = '</p>';
                 $paragraphs = explode( $closing_p, $content );       
                 $total_paragraphs = count($paragraphs);
                 $paragraph_id = round($total_paragraphs /2);       
                 foreach ($paragraphs as $index => $paragraph) {
                    if ( trim( $paragraph ) ) {
                        $paragraphs[$index] .= $closing_p;
                    }
                    if ( $paragraph_id == $index + 1 ) {
                        $paragraphs[$index] .= $ad_code;
                    }
                  }
                   $content = implode( '', $paragraphs ); 
                 }
                break;             
             default:
               break;
          }      
          //Displays all ads according to their settings paragraphs ends here      
         }                          
       }
         }
        return $content;    
}

/*
 *    Create Shortcode adsforwp
 *    Use the shortcode: [adsforwp id=""]
 */
add_shortcode( 'adsforwp', 'adsforwp_manual_ads' );
function adsforwp_manual_ads($atts) {	
        $post_ad_id =   $atts['id'];                  
	$current_post_data = get_post_meta(get_the_ID(),$key='',true);  
        $visibility ='';
        if(array_key_exists('ads-for-wp-visibility', $current_post_data)){
        $visibility = $current_post_data['ads-for-wp-visibility'][0];    
        }                                                            
        if($visibility == 'show') {                                    
        $ad_code =  adsforwp_get_ad_code($post_ad_id);          
        return $ad_code;                           
       }
        
}
/*
 * Generating html for ads to be displayed by post id
 */
function adsforwp_get_ad_code($post_ad_id){
    
            $is_amp = 'no';
            if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
            $is_amp = 'yes';        
            }	                             
            $custom_ad_code="";
            $where_to_display="";            
            $ad_type="";
            $amp_compatibility =''; 
            $ad_code ='';            
            $post_meta_dataset = get_post_meta($post_ad_id,$key='',true);
            
            if(array_key_exists('custom_code', $post_meta_dataset)){
            $custom_ad_code = $post_meta_dataset['custom_code'][0];    
            }
            if(array_key_exists('wheretodisplay', $post_meta_dataset)){
            $where_to_display = $post_meta_dataset['wheretodisplay'][0];  
            }            
            if(array_key_exists('select_adtype', $post_meta_dataset)){
            $ad_type = $post_meta_dataset['select_adtype'][0];      
            }
                                                            
            if($ad_type !=""){
            
            if(array_key_exists('ads-for-wp_amp_compatibilty', $post_meta_dataset)){
            $amp_compatibility = $post_meta_dataset['ads-for-wp_amp_compatibilty'][0];    
            }                
            switch ($ad_type) {
                case 'custom':
                    if($is_amp== 'yes'){
                     if($amp_compatibility == 'enable'){
                     $ad_code = '<div class="afw afw_custom afw_'.$post_ad_id.'">
							'.$custom_ad_code.'
							</div>';    
                    }   
                    }
                    if($is_amp== 'no'){                     
                     $ad_code = '<div class="afw afw_custom afw_'.$post_ad_id.'">
							'.$custom_ad_code.'
							</div>';    
                      
                    }                    
                                                            
            break;
           //adsense ads logic code starts here
            case 'adsense':
                        
            $ad_client = $post_meta_dataset['data_client_id'][0];
            $ad_slot = $post_meta_dataset['data_ad_slot'][0];    
            $width='200';
            $height='200';
            $banner_size = $post_meta_dataset['banner_size'][0];    
            if($banner_size !=''){
            $explode_size = explode('x', $banner_size);            
            $width = $explode_size[0];            
            $height = $explode_size[1];                               
            }            
            if($is_amp == 'yes'){
                if($amp_compatibility == 'enable'){
                 $ad_code = '<div class="afw afw-ga afw_'.$post_ad_id.'">
                                <amp-ad 
				type="adsense"
				width="'. esc_attr($width) .'"
				height="'. esc_attr($height) .'"
				data-ad-client="'. $ad_client .'"
				data-ad-slot="'.$ad_slot .'">
			    </amp-ad>
                            </div>';
                }                             
				                
            }
            if($is_amp == 'no'){                
             $ad_code = '<div class="afw afw-ga afw_'.$post_ad_id.'">
							<script async="" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">
							</script>
							<ins class="adsbygoogle" style="display:inline-block;width:'.esc_attr($width).'px;height:'.esc_attr($height).'px" data-ad-client="'.$ad_client.'" data-ad-slot="'.$ad_slot.'">
							</ins>
							<script>
								(adsbygoogle = window.adsbygoogle || []).push({});
							</script>
						</div>';   
            }                                    
            break;
            
            case 'media_net':
                        
            $ad_data_cid = $post_meta_dataset['data_cid'][0];
            $ad_data_crid = $post_meta_dataset['data_crid'][0];    
            $width='200';
            $height='200';
            $banner_size = $post_meta_dataset['banner_size'][0];    
            if($banner_size !=''){
            $explode_size = explode('x', $banner_size);            
            $width = $explode_size[0];            
            $height = $explode_size[1];                               
            }            
            if($is_amp == 'yes'){
                if($amp_compatibility == 'enable'){
                 $ad_code = 
                            '<div class="afw afw-md afw_'.$post_ad_id.'">
                            <amp-ad 
				type="medianet"
				width="'. esc_attr($width) .'"
				height="'. esc_attr($height) .'"
                                data-tagtype="cm"    
				data-cid="'. $ad_data_cid.'"
				data-crid="'.$ad_data_crid.'">
			    </amp-ad>;  
                            </div>';    
                }                             
				                
            }
            if($is_amp == 'no'){                
             $ad_code = '<div class="afw afw-md afw_'.$post_ad_id.'">
						<script id="mNCC" language="javascript">
                                                            medianet_width = "'.esc_attr($width).'";
                                                            medianet_height = "'.esc_attr($height).'";
                                                            medianet_crid = "'.$ad_data_crid.'"
                                                            medianet_versionId ="3111299"
                                                   </script>
                                                   <script src="//contextual.media.net/nmedianet.js?cid='.$ad_data_cid.'"></script>		
						</div>';   
            }                                    
            break;
            default:
            break;
        }        
        return $ad_code;
      } 
            
}