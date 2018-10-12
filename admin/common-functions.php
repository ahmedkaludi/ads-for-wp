<?php 
/**
 * This is a common class for all common functions which we will use in different classes in our plugin
 */
class adsforwp_admin_common_functions {   
        
    public function __construct() {        
       
    }   
    /**
     * We are here fetching all groups information from advanced ads plugin
     * @return type array
     */
    public function adsforwp_get_advads_groups(){
        
        $group_list = array();
        
        $terms = get_terms( 'advanced_ads_groups', array(
        'hide_empty' => false,
        ));        
        foreach($terms as $term){
            $ad_ids = array();
            $args = array(
			'post_type' => 'advanced_ads',
			'post_status' => array('publish', 'pending', 'future', 'private'),
			'taxonomy' => $term->taxonomy,
			'term' => $term->slug,
			'posts_per_page' => -1
		);
             $wp = new WP_Query( $args );
             if($wp->post_count){
                 foreach ($wp->posts as $ad_id){
                    $ad_ids[] = $ad_id->ID; 
                 }
             }
             $group_list[$term->term_id] = $ad_ids;
        }    
        return $group_list;
    }
    /**
     * We are here fetching all ads from advanced ads plugin
     * note: Transaction is applied on this function, if any error occure all the data will be rollbacked
     * @global type $wpdb
     * @return boolean
     */
    public function adsforwp_import_all_advanced_ads(){    
        $advads_groups = array();
        $advads_groups = $this->adsforwp_get_advads_groups();        
        $advads_ads_adsense = get_option('advanced-ads-adsense');
        $advads_ads_placement = get_option('advads-ads-placements');        
        $ads_post = array();
        global $wpdb;
        $user_id = get_current_user_id();
        $all_advanced_ads = get_posts(
                    array(
                            'post_type' 	 => 'advanced_ads',                                                                                   
                            'posts_per_page' => -1,   
                            'post_status' => 'any',
                    )
                 );  
       
        
        if($all_advanced_ads){
            // begin transaction
            $wpdb->query('START TRANSACTION');
            foreach($all_advanced_ads as $ads){    
                
                $ads_post = array(
                    'post_author' => $user_id,
                    'post_date' => $ads->post_date,
                    'post_date_gmt' => $ads->post_date_gmt,
                    'post_content' => $ads->post_content,
                    'post_title' => $ads->post_title,
                    'post_excerpt' => $ads->post_excerpt,
                    'post_status' => $ads->post_status,
                    'comment_status' => $ads->comment_status,
                    'ping_status' => $ads->ping_status,
                    'post_password' => $ads->post_password,
                    'post_name' =>  $ads->post_name,
                    'to_ping' => $ads->to_ping,
                    'pinged' => $ads->pinged,
                    'post_modified' => $ads->post_modified,
                    'post_modified_gmt' => $ads->post_modified_gmt,
                    'post_content_filtered' => $ads->post_content_filtered,
                    'post_parent' => $ads->post_parent,                                        
                    'menu_order' => $ads->menu_order,
                    'post_type' => 'adsforwp',
                    'post_mime_type' => $ads->post_mime_type,
                    'comment_count' => $ads->comment_count,
                    'filter' => $ads->filter,                    
                );      
                                
                $post_id = wp_insert_post($ads_post);
                $result = $post_id;
                $guid = get_option('siteurl') .'/?post_type=adsforwp&p='.$post_id;                
                $wpdb->get_results("UPDATE wp_posts SET guid ='".$guid."' WHERE ID ='".$post_id."'");
                $advn_meta_value = array();
                $advn_meta_value  = get_post_meta($ads->ID, $key='advanced_ads_ad_options', true );                  
                
                foreach($advads_groups as $group_id => $ads_id){
                   
                    for($i=0;$i<count($ads_id);$i++){
                    if($ads_id[$i] == $ads->ID){
                      $advads_groups[$group_id][$i] =  $post_id; 
                    }
                    }                                                           
                }
                               
                $post_content = '';               
                $slot_id ='';
                $shortcode = '';
                $adtype ='';                     
                $wheretodisplay ='';
                $adsense_type ='';                
                $adposition = '50_of_the_content';
                $paragraph_number = '';
                foreach($advads_ads_placement as $placement){
                    if($placement['item'] == 'ad_'.$ads->ID){
                        switch($placement['type']){
                            case 'post_top':
                                $wheretodisplay = 'before_the_content';   
                                break;
                            case 'post_bottom':
                                $wheretodisplay = 'after_the_content';
                                break;
                            case 'post_content':                                
                                $wheretodisplay = 'between_the_content';
                                if(isset($placement['item']['options']['index'])){
                                $adposition = 'number_of_paragraph';    
                                $paragraph_number  =$placement['item']['options']['index'];
                                }
                                break;
                            case 'default':
                                $wheretodisplay = 'before_the_content';
                                break;
                        }
                        
                    }
                }                                
                $ad_expire_enable = 0;
                $expire_date = date('Y-m-d');
                if(isset($advn_meta_value['expiry_date'])){                    
                  $ad_expire_enable = 1;  
                  $expire_date = date('Y-m-d', $advn_meta_value['expiry_date']);
                }
                
                if(isset($advn_meta_value['output']['allow_shortcodes'])){
                 $shortcode = '[adsforwp id="'.$post_id.'"]';   
                }
                if(isset($advn_meta_value['type'])){
                switch($advn_meta_value['type']){
                    case 'image':
                        $adtype = 'ad_image';                        
                        break;
                    case 'dummy':
                        $adtype = 'ad_image';                        
                        break;
                    case 'plain':
                        $adtype = 'custom';
                        $post_content = $ads->post_content;
                        break;
                    case 'content':
                        $adtype = 'custom';
                        $post_content = $ads->post_content;
                        break;
                    case 'adsense':     
                        $adsenedata = json_decode($ads->post_content, true);
                        $slot_id = $adsenedata['slotId'];
                        $adtype = 'adsense';
                        $adsense_type = 'normal';
                        break;
                }
              }
                
                $adsense_id = '';
                $ad_width = '';
                $ad_height = '';
                $ad_url = '';
                $ad_redirect_url = '';                
                $ad_img_src = array();
                if(isset($advads_ads_adsense['adsense-id'])){
                  $adsense_id = $advads_ads_adsense['adsense-id'];  
                }
                if(isset($advn_meta_value['width'])){
                 $ad_width =$advn_meta_value['width'];    
                }
                if(isset($advn_meta_value['height'])){
                 $ad_height =$advn_meta_value['height'];    
                }
                if(isset($advn_meta_value['url'])){
                 $ad_redirect_url =$advn_meta_value['url'];    
                }        
                
                if(isset($advn_meta_value['output']['image_id'])){
                $ad_img_src = wp_get_attachment_image_src($advn_meta_value['output']['image_id'], 'full'); 
                $ad_url = $ad_img_src[0];
                }                
                $adforwp_meta_key = array(
                    'select_adtype' => $adtype,  
                    'adsense_type' => $adsense_type,
                    'custom_code' =>$post_content,                     
                    'data_client_id' =>$adsense_id, 
                    'data_ad_slot' =>$slot_id,                     
                    'banner_size' =>$ad_width.'x'.$ad_height, 
                    'adsforwp_ad_image' =>$ad_url, 
                    'adsforwp_ad_redirect_url' => $ad_redirect_url,
                    'adsforwp_ad_img_width' =>$ad_width, 
                    'adsforwp_ad_img_height' =>$ad_height,                                        
                    'wheretodisplay' =>$wheretodisplay,
                    'adposition' =>$adposition, 
                    'paragraph_number' =>$paragraph_number, 
                    'adsforwp_ad_expire_day_enable' =>0,                     
                    'adsforwp_ad_expire_days' =>array(
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ), 
                    'adsforwp_ad_expire_enable' =>$ad_expire_enable, 
                    'adsforwp_ad_expire_from' =>date('Y-m-d', strtotime($ads->post_date)), 
                    'adsforwp_ad_expire_to' =>$expire_date,                     
                    'manual_ads_type' => $shortcode,  
                    'imported_from' => 'advance_ads',
                );
                foreach ($adforwp_meta_key as $key => $val){                     
                    update_post_meta($post_id, $key, $val);  
                }                                                                  
              }   
            $result = $this->adsforwp_import_all_advanced_groups($advads_groups);
            if (is_wp_error($result) ){
              echo $result->get_error_message();              
              $wpdb->query('ROLLBACK');             
            }else{
              $wpdb->query('COMMIT'); 
              return true;
            }            
        }
                             
    }
    
    /**
     * We are here fetching all ads from advanced ads plugin
     * note: Transaction is applied on this function, if any error occure all the data will be rollbacked
     * @global type $wpdb
     * @return boolean
     */
    
    public function adsforwp_migrate_ampforwp_ads(){
        $result ='';
        $adforwp_meta_key = array();
        $amp_options = get_option('redux_builder_amp'); 
        $user_id = get_current_user_id();
               
        $placement = array('1'=> 'Single','2'=> 'Global','3'=> 'Custom Post Types','4'=> 'Pages');
        $wheretodisplayamp = array(                    
                    '1'=>'below_the_header',
                    '2'=>'below_the_footer',                   
                    '3'=>'above_the_post_content',
                    '4'=>'below_the_post_content',
                    '5'=>'below_the_title',
                    '6'=>'above_related_post',                    
                    );
        if($amp_options){
            for($i=1;$i<=6; $i++){
              $amp_options['enable-amp-ads-select-'.$i];   
              $amp_options['enable-amp-ads-text-feild-client-'.$i]; 
              $amp_options['enable-amp-ads-text-feild-slot-'.$i];              
                            
        $ads_post = array(
                    'post_author' => $user_id,                                                            
                    'post_title' => 'Adsense Ad '.$i.' (Migrated from AMP)',                    
                    'post_status' => 'publish',                                                            
                    'post_name' =>  'Adsense Ad '.$i.' (Migrated from AMP)',                    
                    'post_type' => 'adsforwp',
                    
                );  
        if(isset($amp_options['enable-amp-ads-select-'.$i])){
         $post_id = wp_insert_post($ads_post);
                $data_group_array = array();
                $conditions = array();
                if($i==3){
                if(isset($amp_options['made-amp-ad-3-global'])){                
                $conditions = $amp_options['made-amp-ad-3-global'];                
                if(!in_array(4, $conditions)){ 
                   
                for($i = 0; $i <count($conditions); $i++){                    
                    $displayon ='';                    
                    if($conditions[$i] ==1){
                      $displayon = 'post';  
                    }else if($conditions[$i] ==3){
                      $displayon = 'post';  
                    }else if ($conditions[$i] ==2){
                      $displayon = 'page';  
                    }                                        
                    $data_group_array['group-'.$i] =array(
                                    'data_array' => array(
                                                        array(
                                                        'key_1' => 'post_type',
                                                        'key_2' => 'equal',
                                                        'key_3' => $displayon,
                                                        )
                                                     )               
                                                 );                     
                }                 
                }
                }    
                }else{                                                      
                 $data_group_array['group-0'] =array(
                                    'data_array' => array(
                                                        array(
                                                        'key_1' => 'show_globally',
                                                        'key_2' => 'equal',
                                                        'key_3' => 'post',
                                                        )
                                                     )               
                                                 );  
                }                                                
                $adforwp_meta_key = array(
                    'select_adtype' => 'adsense',  
                    'adsense_type' => 'normal',                    
                    'data_client_id' =>$amp_options['enable-amp-ads-text-feild-client-'.$i], 
                    'data_ad_slot' =>$amp_options['enable-amp-ads-text-feild-slot-'.$i],                     
                    'banner_size' =>$placement[$amp_options['enable-amp-ads-select-'.$i]],                     
                    'wheretodisplay' =>'after_the_content',
                    'wheretodisplayamp' =>$wheretodisplayamp[$i],
                    'imported_from' => 'ampforwp_ads',
                    'data_group_array' => $data_group_array
                );
                foreach ($adforwp_meta_key as $key => $val){                     
                $result =  update_post_meta($post_id, $key, $val);  
                }                                     
                 }
        
            }
             
        }
        return $result;
    }
    
    public function adsforwp_migrate_advanced_auto_ads(){
        $result ='';
        $adforwp_meta_key = array();
        $amp_options = get_option('redux_builder_amp');        
        $user_id = get_current_user_id();
        if(isset($amp_options['ampforwp-amp-auto-ads-code'])){
          $explodestr = explode('"', $amp_options['ampforwp-amp-auto-ads-code']);         
          $ads_post = array(
                    'post_author' => $user_id,                                                            
                    'post_title' => 'Adsense Auto Ads '.$i.' (Migrated from Advanced AMP Ads)',                    
                    'post_status' => 'publish',                                                            
                    'post_name' =>  'Adsense Auto Ads '.$i.' (Migrated from Advanced AMP Ads)',                    
                    'post_type' => 'adsforwp',
                    
                );
              $post_id = wp_insert_post($ads_post);
              $data_group_array['group-0'] =array(
                                    'data_array' => array(
                                                        array(
                                                        'key_1' => 'show_globally',
                                                        'key_2' => 'equal',
                                                        'key_3' => 'post',
                                                        )
                                                     )               
                                                 ); 
              $adforwp_meta_key = array(
                    'select_adtype' => 'adsense',  
                    'adsense_type' => 'adsense_auto_ads',                    
                    'data_client_id' =>$explodestr[3],                     
                    'imported_from' => 'ampforwp_ads',
                    'data_group_array' => $data_group_array
                );
                foreach ($adforwp_meta_key as $key => $val){                     
                 $result =  update_post_meta($post_id, $key, $val);  
                }
        }
        return $result;
    }
    
    public function adsforwp_migrate_advanced_amp_ads_incontent(){
        $result ='';
        $adforwp_meta_key = array();
        $amp_options = get_option('redux_builder_amp');        
        $user_id = get_current_user_id();
         for($i=1;$i<=4;$i++){              
              $adposition = '';
              $paragraph_number ='';
              $post_title ='';
              $data_group_array['group-0'] =array(
                                    'data_array' => array(
                                                        array(
                                                        'key_1' => 'post_type',
                                                        'key_2' => 'equal',
                                                        'key_3' => 'post',
                                                        )
                                                     )               
                                                 );                            
              if(isset($amp_options['ampforwp-advertisement-type-incontent-ad-'.$i])){                  
                switch ($amp_options['ampforwp-advertisement-type-incontent-ad-'.$i]) {
                    case 1: // Adsense
                        $post_title = 'Adsense Normal Ad '.$i. '(Migrated from Advanced AMP Ads)';
                        if($amp_options['ampforwp-adsense-ad-position-incontent-ad-'.$i] =='50-percent'){
                            $adposition ='50_of_the_content';  
                        }else if($amp_options['ampforwp-adsense-ad-position-incontent-ad-'.$i] =='custom'){
                            $adposition ='number_of_paragraph'; 
                            $paragraph_number =$amp_options['ampforwp-custom-adsense-ad-position-incontent-ad-'.$i]; 
                        }else{
                            $adposition ='number_of_paragraph'; 
                            $paragraph_number = $amp_options['ampforwp-adsense-ad-position-incontent-ad-'.$i];
                        }                        
                        $adforwp_meta_key = array(
                            'select_adtype'   => 'adsense',  
                            'adsense_type'    => 'normal',                    
                            'data_client_id'  => $amp_options['ampforwp-adsense-ad-data-ad-client-incontent-ad-'.$i],                     
                            'data_ad_slot'    => $amp_options['ampforwp-adsense-ad-data-ad-slot-incontent-ad-'.$i],                     
                            'banner_size'     => $amp_options['ampforwp-adsense-ad-width-incontent-ad-'.$i].'x'.$amp_options['ampforwp-adsense-ad-height-incontent-ad-'.$i],                     
                            'wheretodisplay'  => 'between_the_content',
                            'adposition'      => $adposition,
                            'paragraph_number'=> $paragraph_number,    
                            'imported_from'   => 'ampforwp_ads',
                            'data_group_array'=> $data_group_array
                        );

                        break;
                    
                    case 3: //Custom Advertisement
                        $post_title = 'Custom Ad '.$i. '(Migrated from Advanced AMP Ads)';
                        if($amp_options['ampforwp-custom-ads-ad-position-incontent-ad-'.$i] =='50-percent'){
                            $adposition ='50_of_the_content';  
                        }else if($amp_options['ampforwp-custom-ads-ad-position-incontent-ad-'.$i] =='custom'){
                            $adposition ='number_of_paragraph'; 
                            $paragraph_number =$amp_options['ampforwp-custom-custom-ads-ad-position-incontent-ad-'.$i]; 
                        }else{
                            $adposition ='number_of_paragraph'; 
                            $paragraph_number = $amp_options['ampforwp-custom-ads-ad-position-incontent-ad-'.$i];
                        }                        
                        $adforwp_meta_key = array(
                            'select_adtype'   => 'custom',                                                                                                                                     
                            'wheretodisplay'  => 'between_the_content',
                            'adposition'      => $adposition,
                            'paragraph_number'=> $paragraph_number,  
                            'custom_code'=> $amp_options['ampforwp-custom-advertisement-incontent-ad-'.$i],  
                            'imported_from'   => 'ampforwp_ads',
                            'data_group_array'=> $data_group_array
                        );
                        break;

                    default:
                        break;
                }              
                $ads_post = array(
                      'post_author' => $user_id,                                                            
                      'post_title' => $post_title,                    
                      'post_status' => 'publish',                                                            
                      'post_name' =>  $post_title,                    
                      'post_type' => 'adsforwp',

                  );     
              }
              if($amp_options['ampforwp-advertisement-type-incontent-ad-'.$i] != 2){
              $post_id = wp_insert_post($ads_post);
              foreach ($adforwp_meta_key as $key => $val){                     
                $result = update_post_meta($post_id, $key, $val);  
                }    
              }                                           
        }
        return $result;
    }

    public function adsforwp_migrate_advanced_amp_ads_after_feature(){
        $result ='';
        $adforwp_meta_key = array();
        $amp_options = get_option('redux_builder_amp');        
        $user_id = get_current_user_id();
         if(isset($amp_options['ampforwp-after-featured-image-ad-type'])){
            
            $ads_post = array(
                    'post_author' => $user_id,                                                            
                    'post_title' => 'After Featured Image (Migrated from Advanced AMP Ads)',                    
                    'post_status' => 'publish',                                                            
                    'post_name' =>  'After Featured Image (Migrated from Advanced AMP Ads)',                    
                    'post_type' => 'adsforwp',
                    
                );
              $post_id = wp_insert_post($ads_post);
              $data_group_array['group-0'] =array(
                                    'data_array' => array(
                                                        array(
                                                        'key_1' => 'post_type',
                                                        'key_2' => 'equal',
                                                        'key_3' => 'post',
                                                        )
                                                     )               
                                                 ); 
              
                            switch ($amp_options['ampforwp-after-featured-image-ad-type']) {
                                case 1:
                                    $adforwp_meta_key = array(
                                        'select_adtype'     => 'adsense',  
                                        'adsense_type'      => 'normal',                    
                                        'data_client_id'    => $amp_options['ampforwp-after-featured-image-ad-type-1-data-ad-client'],                     
                                        'data_ad_slot'      => $amp_options['ampforwp-after-featured-image-ad-type-1-data-ad-slot'],                     
                                        'banner_size'       => $amp_options['ampforwp-after-featured-image-ad-type-1-width'].'x'.$amp_options['ampforwp-after-featured-image-ad-type-1-height'],                     
                                        'wheretodisplay'    => 'between_the_content',
                                        'wheretodisplayamp' => 'after_featured_image',
                                        'imported_from'     => 'ampforwp_ads',
                                        'data_group_array'  => $data_group_array
                                      );                                 
                                    break;
                                case 2:
                                    $adforwp_meta_key = array(
                                        'select_adtype'   => 'custom',                                                                                                                                     
                                        'wheretodisplay'  => 'between_the_content',
                                        'wheretodisplayamp' => 'after_featured_image',                                         
                                        'custom_code'=> $amp_options['ampforwp-after-featured-image-ad-custom-advertisement'],  
                                        'imported_from'   => 'ampforwp_ads',
                                        'data_group_array'=> $data_group_array
                                     );

                                    break;

                                default:
                                break;
                            }
                            
                foreach ($adforwp_meta_key as $key => $val){                     
                $result = update_post_meta($post_id, $key, $val);  
                }            
        }
        return $result;
    }
    public function adsforwp_migrate_advanced_amp_ads_inloop(){
        $result ='';
        $adforwp_meta_key = array();
        $amp_options = get_option('redux_builder_amp');        
        $user_id = get_current_user_id();
         if(isset($amp_options['ampforwp-inbetween-loop'])){
            
            $ads_post = array(
                    'post_author' => $user_id,                                                            
                    'post_title' => 'In Loop Ad (Migrated from Advanced AMP Ads)',                    
                    'post_status' => 'publish',                                                            
                    'post_name' =>  'In Loop Ad (Migrated from Advanced AMP Ads)',                    
                    'post_type' => 'adsforwp',
                    
                );
              $post_id = wp_insert_post($ads_post);
              $data_group_array['group-0'] =array(
                                    'data_array' => array(
                                                        array(
                                                        'key_1' => 'post_type',
                                                        'key_2' => 'equal',
                                                        'key_3' => 'post',
                                                        )
                                                     )               
                                                 ); 
              
                            switch ($amp_options['ampforwp-inbetween-type']) {
                                case 1:
                                    $adforwp_meta_key = array(
                                        'select_adtype'     => 'adsense',  
                                        'adsense_type'      => 'normal',                    
                                        'data_client_id'    => $amp_options['ampforwp-inbetween-adsense-ad-data-ad-client'],                     
                                        'data_ad_slot'      => $amp_options['ampforwp-inbetween-adsense-ad-data-ad-slot'],                     
                                        'banner_size'       => $amp_options['ampforwp-inbetween-adsense-ad-width'].'x'.$amp_options['ampforwp-inbetween-adsense-ad-height'],                     
                                        'wheretodisplay'    => 'between_the_content',
                                        'wheretodisplayamp' => 'ads_in_loops',
                                        'after_how_many_post' => $amp_options['ampforwp-inbetween-loop-post-num'], 
                                        'imported_from'     => 'ampforwp_ads',
                                        'data_group_array'  => $data_group_array
                                      );                                 
                                    break;
                                case 2:
                                    $adforwp_meta_key = array(
                                        'select_adtype'   => 'custom',                                                                                                                                     
                                        'wheretodisplay'  => 'between_the_content',
                                        'wheretodisplayamp' => 'ads_in_loops',                                         
                                        'custom_code'=> $amp_options['ampforwp-inbetween-custom-advertisement'],  
                                        'imported_from'   => 'ampforwp_ads',
                                        'data_group_array'=> $data_group_array
                                     );

                                    break;

                                default:
                                break;
                            }
                            
                foreach ($adforwp_meta_key as $key => $val){                     
                $result = update_post_meta($post_id, $key, $val);  
                }            
        }
        return $result;
    }
    public function adsforwp_migrate_advanced_amp_ads_standard(){
        $result ='';
        $adforwp_meta_key = array();
        $amp_options = get_option('redux_builder_amp');        
        $user_id = get_current_user_id();
        $placement_position = array(
                '1'=> 'below_the_header',
                '2'=> 'below_the_footer',
                '3'=> 'above_the_post_content',
                '4'=> 'below_the_post_content',
                '5'=> 'below_the_title',
                '6'=> 'above_related_post',
                '7'=> 'below_author_box'
                );
            
             for($i=1;$i<=7;$i++){                                                                     
                                                         
              $post_title ='';
              $data_group_array['group-0'] =array(
                                    'data_array' => array(
                                                        array(
                                                        'key_1' => 'post_type',
                                                        'key_2' => 'equal',
                                                        'key_3' => 'post',
                                                        )
                                                     )               
                                                 );                            
              if(isset($amp_options['ampforwp-standard-ads-'.$i])){                  
                switch ($amp_options['ampforwp-advertisement-type-standard-'.$i]) {
                    case 1: // Adsense
                        $post_title = 'Adsense Normal Ad '.$i. '(Migrated from Advanced AMP Ads)';
                                             
                        $adforwp_meta_key = array(
                            'select_adtype'   => 'adsense',  
                            'adsense_type'    => 'normal',                    
                            'data_client_id'  => $amp_options['ampforwp-adsense-ad-data-ad-client-standard-'.$i],                     
                            'data_ad_slot'    => $amp_options['ampforwp-adsense-ad-data-ad-slot-standard-'.$i],                     
                            'banner_size'     => $amp_options['ampforwp-adsense-ad-width-standard-'.$i].'x'.$amp_options['ampforwp-adsense-ad-height-standard-'.$i],                     
                            'wheretodisplay'  => 'between_the_content',                            
                            'wheretodisplayamp'  => $placement_position[$i],    
                            'imported_from'   => 'ampforwp_ads',
                            'data_group_array'=> $data_group_array
                        );

                        break;
                    
                    case 3: //Custom Advertisement
                        $post_title = 'Custom Ad '.$i. '(Migrated from Advanced AMP Ads)';
                                                
                        $adforwp_meta_key = array(
                            'select_adtype'   => 'custom',                                                                                                                                     
                            'wheretodisplay'  => 'between_the_content',
                            'wheretodisplayamp'  => $placement_position[$i],
                            'custom_code'=> $amp_options['ampforwp-custom-advertisement-standard-'.$i],  
                            'imported_from'   => 'ampforwp_ads',
                            'data_group_array'=> $data_group_array
                        );
                        break;
                    default:
                        break;
                }              
                $ads_post = array(
                      'post_author' => $user_id,                                                            
                      'post_title' => $post_title,                    
                      'post_status' => 'publish',                                                            
                      'post_name' =>  $post_title,                    
                      'post_type' => 'adsforwp',

                  );     
              }
              if($amp_options['ampforwp-advertisement-type-standard-'.$i] !=2){
              $post_id = wp_insert_post($ads_post);
              foreach ($adforwp_meta_key as $key => $val){                     
                 $result = update_post_meta($post_id, $key, $val);  
               }    
              }                                           
            }  
            return $result;
    }
    public function adsforwp_import_all_amp_ads(){    
                        
            global $wpdb;
            $wpdb->query('START TRANSACTION');

            $result = $this->adsforwp_migrate_ampforwp_ads();

            $result = $this->adsforwp_migrate_advanced_auto_ads();

            $result = $this->adsforwp_migrate_advanced_amp_ads_incontent();        

            $result = $this->adsforwp_migrate_advanced_amp_ads_after_feature();    

            $result = $this->adsforwp_migrate_advanced_amp_ads_standard();                                 

            $result = $this->adsforwp_migrate_advanced_amp_ads_inloop();  
            
            if (is_wp_error($result) ){
              echo $result->get_error_message();              
              $wpdb->query('ROLLBACK');             
            }else{
              $wpdb->query('COMMIT'); 
             return $result;     
            }                         
    }
    /**
     * We are here importing all fetched groups from advanced ads to adsforwp plugin
     * @param type $advads_groups
     */
    public function adsforwp_import_all_advanced_groups($advads_groups) {
        
            $user_id = get_current_user_id();
            $terms = get_terms( 'advanced_ads_groups', array(
            'hide_empty' => false,
            ));
            $groups_extra_attr = get_option( 'advads-ad-groups', array());            
            foreach($terms as $term){
             $group_post = array(
                    'user_ID' =>$user_id,
                    'post_author' => $user_id,                                                            
                    'post_title' => $term->name,                    
                    'post_status' => 'publish',                    
                    'post_name' =>  $term->name,                                                                                
                    'post_type' => 'adsforwp-groups',                     
                    
                );                         
            $group_post_id = wp_insert_post($group_post);             
            $adforwp_group_meta_key = array(
                'imported_from' => 'advance_ads',
            );                                                                                                                   
            if($groups_extra_attr[$term->term_id]['type'] =='default'){
               $adforwp_group_meta_key['adsforwp_group_type'] = 'rand'; 
            }else{
               $adforwp_group_meta_key['adsforwp_group_type'] = 'ordered';  
            }   
            if($groups_extra_attr[$term->term_id]['options']['refresh']['enabled']){
                $adforwp_group_meta_key['adsforwp_refresh_type'] = 'on_interval';
                $adforwp_group_meta_key['adsforwp_group_ref_interval_sec'] = $groups_extra_attr[$term->term_id]['options']['refresh']['interval'];
            }else{
               $adforwp_group_meta_key['adsforwp_refresh_type'] = 'on_load';  
            }
            $store_ads_id = $advads_groups[$term->term_id];
            $ads_forwp_ads = array();
            foreach($store_ads_id as $id){
              $title = get_the_title($id);
              $ads_forwp_ads[$id] = $title;              
            }    
            $adforwp_group_meta_key['adsforwp_ads']= $ads_forwp_ads;           
            foreach ($adforwp_group_meta_key as $key => $val){                     
                    update_post_meta($group_post_id, $key, $val);  
                }   
            }  
    }

    /**
     * We are here fetching all ads post from adsforwp post type
     * @return type
     */
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
    /**
     * we are here fetching all ads post meta for adsforwp post type 
     * @return type
     */
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
    /**
     * We are here fetching all ads groups post from adsforwp-grops post type
     * @return type
     */
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
    /**
     * we are here fetching all ads groups post meta for adsforwp-groups post type 
     * @return type
     */
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
    /**
     * We are checking ad if it is added in group or not
     * @param type $ad_id
     * @return type
     */
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
                    if($meta_value){
                        if(in_array($ad_id, array_keys($meta_value))){
                        $ad_group_ids[] = $groups->ID;  
                    }}                                  
                }                
                return $ad_group_ids;
    }

    /**
     * Function to expand html tags form allowed html tags in wordpress
     * @return array
     */
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
                    'multiple' => array(),
                    'style' => array(),
            );
            
            $my_allowed['iframe'] = array(
                    'class'  => array(),
                    'id'     => array(),
                    'src'   => array(),
                    'height'  => array(),
                    'width'   => array(),                                                            
            );
                        
            $my_allowed['tr'] = array(
                    'class'  => array(),
                    'id'     => array(),
                    'name'   => array(),                    
            );
            $my_allowed['div'] = array(
                    'class'  => array(),
                    'data-id'     => array(),                                    
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
            // allow script
            $my_allowed['script'] = array(
                    'src' => array(),
                    'type' => array(),
                    'data-width' => array(),
                    'data-height' => array(),                                
                    'async' => array(),
                    'crossorigin' => array(),
                    'defer' => array(),
                    'importance' => array(),
                    'integrity' => array(),
                    'nomodule' => array(),
                    'nonce' => array(),                   
                    'text' => array(),
                    'charset' => array(),
                    'language' => array(),                                                
            );
                        
            return $my_allowed;
        }    
}
