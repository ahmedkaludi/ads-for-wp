<?php 
class adsforwp_admin_common_functions {   
    
        
    public function adsforwp_fetch_all_ads(){
        $all_ads = get_posts(
                    array(
                            'post_type' 	 => 'adsforwp',
                            'posts_per_page' => -1,   
                            'post_status' => 'publish',
                    )
                 ); 
        return $all_ads;
        
    }
    public function adsforwp_fetch_all_ads_post_meta(){
        $all_ads_post_meta = array();
        $all_ads = get_posts(
                    array(
                            'post_type' 	 => 'adsforwp',
                            'posts_per_page' => -1,   
                            'post_status' => 'publish',
                    )
                 );         
        foreach($all_ads as $ad){
                 $all_ads_post_meta[$ad->ID] = get_post_meta( $ad->ID, $key='', true );                                                                           
                }               
        return $all_ads_post_meta;        
    }
    public function adsforwp_fetch_all_groups(){
        $all_groups = get_posts(
                    array(
                            'post_type' 	 => 'adsforwp-groups',
                            'posts_per_page' => -1,   
                            'post_status' => 'publish',
                    )
                 );        
        return $all_groups;
    }
     public function adsforwp_fetch_all_groups_post_meta(){
        $all_groups_post_meta = array();
        $all_groups = get_posts(
                    array(
                            'post_type' 	 => 'adsforwp-groups',
                            'posts_per_page' => -1,   
                            'post_status' => 'publish',
                    )
                 );         
        foreach($all_groups as $group){
                 $all_groups_post_meta[$group->ID] = get_post_meta( $group->ID, $key='', true );                                                                           
                }                    
        return $all_groups_post_meta;        
    }
    public function adsforwp_check_ads_in_group($ad_id){
        $all_groups = get_posts(
                    array(
                            'post_type' 	 => 'adsforwp-groups',
                            'posts_per_page' => -1,   
                            'post_status' => 'publish',
                    )
                 );
                $meta_value = array(); 
                $ad_group_ids = array();
                foreach($all_groups as $groups){
                  $meta_value  = get_post_meta( $groups->ID, $key='adsforwp_ads', true );                   
                    if(in_array($ad_id, array_keys($meta_value))){
                     $ad_group_ids[] = $groups->ID;  
                    }
                    
                                      
                }                
                return $ad_group_ids;
    }

//Function to expand html tags form allowed html tags in wordpress    
public function adsforwp_expanded_allowed_tags() {
            $my_allowed = wp_kses_allowed_html( 'post' );
            // form fields - input
            $my_allowed['input'] = array(
                    'class'        => array(),
                    'id'           => array(),
                    'name'         => array(),
                    'value'        => array(),
                    'type'         => array(),
                    'style'        => array(),
                    'placeholder'  => array(),
                    'maxlength'    => array(),
                    'checked'      => array(),
                    'readonly'     => array(),
                    'disabled'     => array(),
                    'width'        => array(),
                    
            ); 
            //number
            $my_allowed['number'] = array(
                    'class'        => array(),
                    'id'           => array(),
                    'name'         => array(),
                    'value'        => array(),
                    'type'         => array(),
                    'style'        => array(),                    
                    'width'     => array(),
                    
            ); 
            //textarea
             $my_allowed['textarea'] = array(
                    'class' => array(),
                    'id'    => array(),
                    'name'  => array(),
                    'value' => array(),
                    'type'  => array(),
                    'style'  => array(),
                    'rows'  => array(),                                                            
            );       
             //amp tag
             $my_allowed['amp-ad'] = array(
                    'class' => array(),
                    'width'    => array(),
                    'height'  => array(),
                    'type' => array(),
                    'data-slot'  => array(),                 
                    'data-ad-client'  => array(),
                    'data-ad-slot'  => array(),
                    'data-tagtype'  => array(),
                    'data-cid'  => array(),
                    'data-crid'  => array(),
            );
             $my_allowed['amp-img'] = array(
                    'class' => array(),
                    'id' => array(),
                    'width'    => array(),
                    'height'  => array(),
                    'type' => array(),
                    'src'  => array(), 
                    'on'  => array(), 
                    'role'  => array(), 
                    'tabindex'  => array(), 
                    'layout'  => array(), 
            );
             $my_allowed['amp-ad-exit'] = array(
                    'id' => array(),                    
             );
             $my_allowed['amp-auto-ads'] = array(
                    'type' => array(),
                    'id' => array(),
                    'data-ad-client' => array(),
                    'height'  => array(),
                    'width' => array(),             
            );
             $my_allowed['amp-sticky-ad'] = array(
                    'layout' => array(),
                    'id' => array(),                             
            );
             $my_allowed['amp-list'] = array(
                    'width' => array(),
                    'height' => array(),
                    'layout' => array(),
                    'src'  => array(),
                    'width' => array(), 
                    'id' => array(), 
            );
             $my_allowed['amp-live-list'] = array(                    
                    'data-max-items-per-page'  => array(),
                    'data-poll-interval' => array(), 
                    'id' => array(), 
            );
             $my_allowed['amp-app-banner'] = array(                    
                    'layout'  => array(),                    
                    'id' => array(), 
            );
             $my_allowed['amp-carousel'] = array(                    
                    'width'  => array(),                    
                    'height' => array(), 
                    'id' => array(), 
                    'layout' => array(), 
                    'type' => array(), 
                     'data-next-button-aria-label' => array(), 
                     'data-previous-button-aria-label' => array(),
                    'delay' => array(),
                    'loop' => array(),
                    'autoplay' => array(),
                    'controls' => array(),
                 
            );
             $my_allowed['amp-iframe'] = array(                    
                    'width'  => array(), 
                    'height'  => array(), 
                    'sandbox'  => array(), 
                    'layout'  => array(), 
                    'frameborder'  => array(),
                    'src'  => array(),                 
                    'id' => array(), 
            );
             $my_allowed['amp-image-lightbox'] = array(                    
                    'layout'  => array(), 
                    'height'  => array(),                                         
                    'id' => array(), 
            );
             $my_allowed['amp-layout'] = array(                    
                    'layout'  => array(), 
                    'width'  => array(),   
                    'height'  => array(),   
                    'id' => array(), 
            );
             $my_allowed['amp-3d-gltf'] = array(                    
                    'layout'  => array(), 
                    'width'  => array(),   
                    'height'  => array(),   
                    'id' => array(), 
                    'antialiasing' => array(), 
                    'src' => array(),                  
            );
             $my_allowed['amp-anim'] = array(                    
                    'layout'  => array(), 
                    'width'  => array(),   
                    'height'  => array(),   
                    'id' => array(), 
                    'srcset' => array(), 
                    'src' => array(),                  
            );
             $my_allowed['amp-imgur'] = array(                    
                    'data-imgur-id'  => array(), 
                    'layout'  => array(),   
                    'width'  => array(),   
                    'height' => array(), 
                    'id' => array(),                                   
            );
             $my_allowed['amp-animation'] = array(                                        
                    'layout'  => array(),   
                    'duration'  => array(),   
                    'delay' => array(), 
                    'endDelay' => array(),
                    'iterations' => array(),
                    'iterationStart' => array(),
                    'easing' => array(),
                    'direction' => array(),
                    'fill' => array(),   
            );
             
            // select
            $my_allowed['select'] = array(
                    'class'  => array(),
                    'id'     => array(),
                    'name'   => array(),
                    'value'  => array(),
                    'type'   => array(),
                    'required' => array(),
            );
            $my_allowed['tr'] = array(
                    'class'  => array(),
                    'id'     => array(),
                    'name'   => array(),                    
            );
            //  options
            $my_allowed['option'] = array(
                    'selected' => array(),
                    'value' => array(),
            );                       
            // style
            $my_allowed['style'] = array(
                    'types' => array(),
            );
            return $my_allowed;
        }    
}

