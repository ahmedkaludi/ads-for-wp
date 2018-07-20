<?php
/*
 *  Display the ads according to the settings
 */
add_filter('the_content', 'ads_for_wp_display_ads');
function ads_for_wp_display_ads($content){

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
        if($visibility == 'Show') {
            $all_ads_post = json_decode(get_transient('transient_ads_post_ids'), true);  
            
            foreach($all_ads_post as $ads){
            $post_ad_id = $ads;   
            
            $post_meta_dataset="";
            $custom_ad_code="";
            $where_to_display="";
            $adposition="";
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
            if(array_key_exists('adposition', $post_meta_dataset)){
            $adposition = $post_meta_dataset['adposition'][0];    
            }
            if(array_key_exists('select_adtype', $post_meta_dataset)){
            $ad_type = $post_meta_dataset['select_adtype'][0];      
            }
                                                            
            if($ad_type !=""){
            
            if(array_key_exists('ads-for-wp_amp_compatibilty', $post_meta_dataset)){
            $amp_compatibility = $post_meta_dataset['ads-for-wp_amp_compatibilty'][0];    
            }               
            switch ($ad_type) {
                case 'Custom':
                    $ad_code = '<div class="afw afw_custom afw_'.$post_ad_id.'">
							'.$custom_ad_code.'
							</div>';
            break;
           //adsense ads logic code starts here
            case 'AdSense':
                        
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
                if($amp_compatibility == 'Enable'){
                 $ad_code = '<amp-ad 
				type="adsense"
				width="'. $width .'"
				height="'. $height .'"
				data-ad-client="'. $ad_client .'"
				data-ad-slot="'.$ad_slot .'">
			    </amp-ad>';   
                }                             
				                
            }
            if($is_amp == 'no'){                
             $ad_code = '<div class="afw afw-ga afw_'.$post_ad_id.'">
							<script async="" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">
							</script>
							<ins class="adsbygoogle" style="display:inline-block;width:'.$width.'px;height:'.$height.'px" data-ad-client="'.$ad_client.'" data-ad-slot="'.$ad_slot.'">
							</ins>
							<script>
								(adsbygoogle = window.adsbygoogle || []).push({});
							</script>
						</div>';   
            }
            
            
            
            break;
         //adsense ads logic code ends here
            default:
            break;
        }
        //Displays all ads according to their settings paragraphs starts here        
            switch ($where_to_display) {
             case 'After the content':
              $content = $content.$ad_code;
              break;
             case 'Before the content':
              $content = $ad_code.$content;
              break;
             case 'Between the content':        
              if($adposition == 'Number of paragraph'){
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
        
                if($adposition == '50% of the content'){

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
         }
        return $content;    
}
