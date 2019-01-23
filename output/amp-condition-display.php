<?php
/**
 * This class handles displaying ads according to amp display conditions
 */
class adsforwp_output_amp_condition_display{
        
    public function __construct() {     
    }
    /**
     * List of all hooks which are used in this class
     */
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
        // In loops
        add_action('ampforwp_between_loop', array($this, 'adsforwp_display_ads_between_loop'),10,1);        
        // Ad After Featured Image #42
        add_action('ampforwp_after_featured_image_hook',array($this, 'adsforwp_display_ads_after_featured_image'));
        
    }
    
    public function adsforwp_display_ads_after_featured_image(){   
        
            $this->adsforwp_amp_condition_ad_code('adsforwp_after_featured_image');   
            
    }
    
    public function adsforwp_display_ads_between_loop($count){     
        
            $this->adsforwp_amp_condition_ad_code('adsforwp_ads_in_loops', $count);    
            
    }    
    public function adsforwp_display_ads_below_author_box(){    
        
            $this->adsforwp_amp_condition_ad_code('adsforwp_below_author_box');      
            
    }
    public function adsforwp_display_ads_above_related_post(){ 
        
            $this->adsforwp_amp_condition_ad_code('adsforwp_above_related_post');    
            
    }
    public function adsforwp_display_ads_below_the_title(){ 
        
            $this->adsforwp_amp_condition_ad_code('adsforwp_below_the_title');  
            
    }
    public function adsforwp_display_ads_below_the_post_content(){  
        
            $this->adsforwp_amp_condition_ad_code('adsforwp_below_the_post_content');  
            
    }
    public function adsforwp_display_ads_above_the_post_content(){  
        
            $this->adsforwp_amp_condition_ad_code('adsforwp_above_the_post_content');  
            
    }    
    public function adsforwp_display_ads_above_the_footer(){     
        
            $this->adsforwp_amp_condition_ad_code('adsforwp_above_the_footer');    
            
    }
    
    public function adsforwp_display_ads_below_the_footer(){  
        
            $this->adsforwp_amp_condition_ad_code('adsforwp_below_the_footer');    
            
    }
    
    public function adsforwp_display_ads_below_the_header(){  
        
            $this->adsforwp_amp_condition_ad_code('adsforwp_below_the_header');
            
    }
    /**
     *  Here, we are fetching ads html markup.
     * @param type $ad_id
     * @param type $count
     * @return type
     */
    public function adsforwp_in_loop_ads_code($ad_id, $count){    
        
        $displayed_posts        = get_option('posts_per_page');
        $in_between             = round(abs($displayed_posts / 2));
        $in_between             = get_post_meta($ad_id,$key='adsforwp_after_how_many_post',true);
        
        if(intval($in_between) == $count){
            
         $output_function = new adsforwp_output_functions();
         $ad_code = $output_function->adsforwp_get_ad_code($ad_id, $type="AD");    
         
        }   
        
        return $ad_code;
        
    }
    /**
     * Here, we are fetching group ads html markup.
     * @param type $group_id
     * @param type $count
     * @param type $widget
     * @return type
     */
    public function adsforwp_in_loop_group_ads_code($group_id, $count, $widget){  
        
        $displayed_posts = get_option('posts_per_page');
        $in_between      = round(abs($displayed_posts / 2));
        $in_between      = get_post_meta($group_id,$key='adsforwp_after_how_many_post',true);  
        
        if(intval($in_between) == $count){
            
         $output_function = new adsforwp_output_functions();
         $ad_code = $output_function->adsforwp_group_ads($atts=null, $group_id, $widget);  
         
        }   
        
        return $ad_code;
        
    }
    
    /**
     * Here, We are displaying ads or group ads according to amp where to display condition
     * @param type $condition
     * @param type $count
     */
    public function adsforwp_amp_condition_ad_code($condition, $count=null){               
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
                          
                      if($amp_display_condition =='adsforwp_ads_in_loops'){
                          
                        echo $this->adsforwp_in_loop_ads_code($ad_id, $count);  
                      
                      }else{
                          
                        echo $output_function->adsforwp_get_ad_code($ad_id, $type="AD");  
                      
                      }                              
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
                          
                      if($amp_display_condition =='adsforwp_ads_in_loops'){
                          
                      echo $this->adsforwp_in_loop_group_ads_code($group_id, $count, $widget);
                      
                      }else{
                          
                      echo $output_function->adsforwp_group_ads($atts=null, $group_id, $widget);                                           
                      
                      }                                                                                               
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
