<?php
/**
 * This class handles displaying ads according to amp display conditions
 */
class adsforwp_output_amp_condition_display{
        
    public function __construct() {     
    }
    public function adsforwp_amp_condition_hooks(){
       // Below the Header 
        add_action( 'ampforwp_after_header', array($this, 'adsforwp_display_ads_below_the_header') );
        add_action( 'ampforwp_design_1_after_header', array($this, 'adsforwp_display_ads_below_the_header') ); 
        
        //Below the Footer
        add_action( 'amp_post_template_footer', array($this, 'adsforwp_display_ads_below_the_footer') );
        
        //ABove the Footer
        add_action( 'amp_post_template_above_footer', array($this, 'adsforwp_display_ads_above_the_footer') );
        
        //Above the Post Content
        add_action( 'ampforwp_before_post_content', array($this, 'adsforwp_display_ads_above_the_post_content') );
        add_action( 'ampforwp_inside_post_content_before', array($this, 'adsforwp_display_ads_above_the_post_content') );
        
        // Below the Post Content
        add_action( 'ampforwp_after_post_content', array($this, 'adsforwp_display_ads_below_the_post_content') );
        add_action( 'ampforwp_inside_post_content_after', array($this, 'adsforwp_display_ads_below_the_post_content') );
        
        //Below The Title
        add_action('ampforwp_below_the_title',array($this, 'adsforwp_display_ads_below_the_title'));
        
        //Above the Related Post
        add_action('ampforwp_above_related_post',array($this, 'adsforwp_display_ads_above_related_post'));
        
        // Below the Author Box
        add_action( 'ampforwp_below_author_box', array($this, 'adsforwp_display_ads_below_author_box') );
    }
    
    public function adsforwp_display_ads_below_author_box(){        
            $this->adsforwp_amp_condition_ad_code('below_author_box');                                   
    }
    public function adsforwp_display_ads_above_related_post(){        
            $this->adsforwp_amp_condition_ad_code('above_related_post');                                   
    }
    public function adsforwp_display_ads_below_the_title(){        
            $this->adsforwp_amp_condition_ad_code('below_the_title');                                   
    }
    public function adsforwp_display_ads_below_the_post_content(){        
            $this->adsforwp_amp_condition_ad_code('below_the_post_content');                                   
    }
    public function adsforwp_display_ads_above_the_post_content(){        
            $this->adsforwp_amp_condition_ad_code('above_the_post_content');                                   
    }    
    public function adsforwp_display_ads_above_the_footer(){        
            $this->adsforwp_amp_condition_ad_code('above_the_footer');                                   
    }
    
    public function adsforwp_display_ads_below_the_footer(){        
            $this->adsforwp_amp_condition_ad_code('below_the_footer');                                   
    }
    
    public function adsforwp_display_ads_below_the_header(){                   
            $this->adsforwp_amp_condition_ad_code('below_the_header');                                             
    }
    
    public function adsforwp_amp_condition_ad_code($condition){
        
        
        //For Ads
        $post_ad_id_list = json_decode(get_transient('adsforwp_transient_ads_ids'), true);        
        if($post_ad_id_list){
            $output_function = new adsforwp_output_functions();
            $common_function_obj = new adsforwp_admin_common_functions();
            echo '<div class="amp-ad-wrapper">';
            foreach($post_ad_id_list as $ad_id){      
                    $in_group = $common_function_obj->adsforwp_check_ads_in_group($ad_id);
                      if(empty($in_group)){
                    $amp_display_condition = get_post_meta($ad_id,$key='wheretodisplayamp',true);
                      if($amp_display_condition == $condition){                    
                    $adcode = $output_function->adsforwp_get_ad_code($ad_id, $type="AD");                    
                    echo $adcode;                   
                }    
               }                
            }
            echo '</div>';
        }
        //For Group Ads
        $post_group_id_list = json_decode(get_transient('adsforwp_groups_transient_ids'), true);         
        if($post_group_id_list){
            $output_function = new adsforwp_output_functions();            
            echo '<div class="amp-ad-wrapper">';
            foreach($post_group_id_list as $group_id){      
                    $widget = '';                      
                    $amp_display_condition = get_post_meta($group_id,$key='wheretodisplayamp',true);
                      if($amp_display_condition == $condition){                    
                    $groupcode = $output_function->adsforwp_group_ads($atts=null, $group_id, $widget);                    
                    echo $groupcode;                   
                }                                
            }
            echo '</div>';
        }
                        
    }    
    
}
if (class_exists('adsforwp_output_amp_condition_display')) {        
        add_action('amp_init', 'adsforwp_amp_hooks_call');
        function adsforwp_amp_hooks_call(){
        $adsforwp_condition_obj = new adsforwp_output_amp_condition_display;
        $adsforwp_condition_obj->adsforwp_amp_condition_hooks();   
        }        	
};
