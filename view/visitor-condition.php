<?php
class adsforwp_view_visitor_condition {
    
 public function __construct() {                                                                                                     
		add_action( 'add_meta_boxes', array( $this, 'adsforwp_visitor_condition_add_meta_box' ) );
		add_action( 'save_post', array( $this, 'adsforwp_visitor_condition_save' ) );
	}
        
 public function adsforwp_visitor_condition_add_meta_box() {
	add_meta_box(
		'adsforwp_visitor_condition_metabox',
		esc_html__( 'Visitor Conditions', 'ads-for-wp' ),
		array( $this, 'adsforwp_visitor_condition_callback' ),
		array('adsforwp', 'adsforwp-groups'),
		'normal',
		'low'
	);
    }
        
 public function adsforwp_visitor_condition_callback( $post) {   
     
            $visitor_condition_enable = get_post_meta($post->ID, $key='adsforwp_v_condition_enable', true);            
            $visitor_conditions_array =  esc_sql ( get_post_meta($post->ID, 'visitor_conditions_array', true)  );                             
            $visitor_conditions_array = is_array($visitor_conditions_array)? array_values($visitor_conditions_array): array();      
            if ( empty( $visitor_conditions_array ) ) {
            
                       $visitor_conditions_array[0] =array(
                           'visitor_conditions' => array(
                                    array(
                                    'key_1' => 'device',
                                    'key_2' => 'equal',
                                    'key_3' => 'desktop',
                                    )
                       )               
                   );
            }            
    //security check
    wp_nonce_field( 'adsforwp_visitor_condition_action_nonce', 'adsforwp_visitor_condition_name_nonce' );?>

    <?php 
    // Type Select    
      $choices = array(
        esc_html__("Basic",'ads-for-wp') => array(                               
          'device'   =>  esc_html__("Device",'ads-for-wp'),           
          'browser_language'   =>  esc_html__("Browser Language",'ads-for-wp'),  
          'logged_in_visitor'   =>  esc_html__("Logged In Visitor",'ads-for-wp'),
          'user_agent'   =>  esc_html__("User Agent",'ads-for-wp'),  
          'user_type'   =>  esc_html__("User Can (Capabilities)",'ads-for-wp'),
          'referrer_url'   =>  esc_html__("Referrer URL",'ads-for-wp')                         
        )        
      ); 

      $comparison = array(
        'equal'   =>  esc_html__( 'Equal to', 'ads-for-wp'), 
        'not_equal' =>  esc_html__( 'Not Equal to', 'ads-for-wp'),     
      );
      $total_group_fields = count( $visitor_conditions_array );       
      ?>
<div>   
    <input type="hidden" value="<?php echo (isset( $visitor_condition_enable )?  $visitor_condition_enable : 'disable'); ?>" id="adsforwp_v_condition_enable" name="adsforwp_v_condition_enable">    
    <?php 
        if(isset($visitor_condition_enable) && $visitor_condition_enable =='enable'){
         echo '<div class="adsforwp-visitor-condition-groups">';
        }else{         
         echo '<div class="adsforwp-visitor-condition-div"><a class="adsforwp-enable-click afw-placement-button">'.esc_html__( 'Enable Visitor Condition', 'ads-for-wp').'</a>'; 
         echo '<p>'.esc_html__( 'Visitor conditions limit the number of users who can see your ad.', 'ads-for-wp').'</p></div>';            
         echo '<div class="adsforwp-visitor-condition-groups afw_hide">';    
        }
    ?>    
    <?php for ($j=0; $j < $total_group_fields; $j++) {
        $visitor_conditions = $visitor_conditions_array[$j]['visitor_conditions'];
        
        $total_fields = count( $visitor_conditions );
        ?>
    <div class="adsforwp-visitor-condition-group" name="visitor_conditions_array[<?php echo esc_attr( $j) ?>]" data-id="<?php echo esc_attr($j); ?>">           
     <?php 
     if($j>0){
     echo '<span style="margin-left:10px;font-weight:600">Or</span>';    
     }     
     ?>   
     <table class="widefat adsforwp-visitor-condition-widefat">
        <tbody id="adsforwp-visitor-condition-tbody" class="adsforwp-fields-wrapper-1">
        <?php  for ($i=0; $i < $total_fields; $i++) {  
          $selected_val_key_1 = $visitor_conditions[$i]['key_1']; 
          $selected_val_key_2 = $visitor_conditions[$i]['key_2'];                     
          $selected_val_key_3 = $visitor_conditions[$i]['key_3'];
          $selected_val_key_4 ='';
          $selected_val_key_5 ='';
          if(isset($visitor_conditions[$i]['key_4'])){
          $selected_val_key_4 = $visitor_conditions[$i]['key_4'];    
          }
          if(isset($visitor_conditions[$i]['key_5'])){
          $selected_val_key_5 = $visitor_conditions[$i]['key_5'];    
          }                    
          ?>
            
          <tr class="adsforwp-toclone">
            <td style="width:31%" class="adsforwp-visitor-condition-type"> 
              <select class="widefat adsforwp-select-visitor-condition-type <?php echo esc_attr( $i );?>" name="visitor_conditions_array[group-<?php echo esc_attr( $j) ?>][visitor_conditions][<?php echo esc_attr( $i) ?>][key_1]">    
                <?php 
                foreach ($choices as $choice_key => $choice_value) { ?>         
                  <optgroup label="<?php echo esc_attr($choice_key);?>">                      
                  </optgroup>
                  <?php
                  foreach ($choice_value as $sub_key => $sub_value) { ?> 
                    <option class="pt-child" value="<?php echo esc_attr( $sub_key );?>" <?php selected( $selected_val_key_1, $sub_key );?> > <?php echo esc_html__($sub_value,'ads-for-wp');?> </option>
                    <?php
                  }
                } ?>
              </select>
            </td>
            
            <td style="width:31%; <?php if (  $selected_val_key_1 =='show_globally' ) { echo 'display:none;'; }  ?>">
              <select class="widefat comparison" name="visitor_conditions_array[group-<?php echo esc_attr( $j) ?>][visitor_conditions][<?php echo esc_attr( $i )?>][key_2]"> <?php
                foreach ($comparison as $key => $value) { 
                  $selcomp = '';
                  if($key == $selected_val_key_2){
                    $selcomp = 'selected';
                  }
                  ?>
                  <option class="pt-child" value="<?php echo esc_attr( $key );?>" <?php echo esc_attr($selcomp); ?> > <?php echo esc_html__($value,'ads-for-wp');?> </option>
                  <?php
                } ?>
              </select>
            </td>
            <td style="width:31%; <?php if (  $selected_val_key_1 =='show_globally' ) { echo 'display:none;'; }  ?>">
              <div class="adsforwp-insert-condition-select">              
                <?php 
                $ajax_select_box_obj = new adsforwp_ajax_selectbox();
                $ajax_select_box_obj->adsforwp_visitor_condition_type_values($selected_val_key_1, $selected_val_key_3,$selected_val_key_4,$selected_val_key_5, $i,$j);                
                ?>
                  <div style="display:none;" class="spinner"></div>
              </div>
            </td>
            
            <td class="widefat adsforwp-visitor-condition-row-clone" style="width:3.5%; <?php if (  $selected_val_key_1 =='show_globally' ) { echo 'display:none;'; }  ?>">
                <span> <button type="button" class="afw-placement-button"> <?php echo esc_html__('And' ,'ads-for-wp');?> </button> </span> 
            </td>
            
            <td class="widefat adsforwp-visitor-condition-row-delete" style="width:3.5%; <?php if (  $selected_val_key_1 =='show_globally' ) { echo 'display:none;'; }  ?>">
                <button  type="button"><span class="dashicons dashicons-trash"></span> </button>
            </td>                                           
          </tr>
          
          <?php 
          
        } ?>
        </tbody>
      </table> 
    </div>
    <?php } ?>
    
    <a style="margin-left: 8px; margin-bottom: 8px;" class="button adsforwp-visitor-condition-or-group afw-placement-button" href="#"><?php echo esc_html__( 'Or', 'ads-for-wp') ?></a>
</div>    
</div>
    <?php                                                                                      
    }
   
 public function adsforwp_visitor_condition_save( $post_id ) {     
     
     if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
       
      // if our nonce isn't there, or we can't verify it, bail
      if( !isset( $_POST['adsforwp_visitor_condition_name_nonce'] ) || !wp_verify_nonce( $_POST['adsforwp_visitor_condition_name_nonce'], 'adsforwp_visitor_condition_action_nonce' ) ) return;
      
      // if our current user can't edit this post, bail
      if( !current_user_can( 'edit_post' ) ) return;  
            $post_visitor_conditions_array = array();  
            $temp_condition_array  = array();
            $show_globally =false;             
            if(isset($_POST['visitor_conditions_array'])){        
            $post_visitor_conditions_array = $_POST['visitor_conditions_array'];    
                foreach($post_visitor_conditions_array as $groups){        
                      foreach($groups['visitor_conditions'] as $group ){              
                        if(array_search('show_globally', $group))
                        {
                          $temp_condition_array[0] =  $group;  
                          $show_globally = true;              
                        }
                      }
                   }    
                if($show_globally){
                unset($post_visitor_conditions_array);
                $post_visitor_conditions_array['group-0']['visitor_conditions'] = $temp_condition_array;       
                }      
            }
            if(isset($_POST['visitor_conditions_array'])){
                update_post_meta(
                  $post_id, 
                  'visitor_conditions_array', 
                  $post_visitor_conditions_array 
                );     
              }
              
              if(isset($_POST['adsforwp_v_condition_enable'])){
                $status = $_POST['adsforwp_v_condition_enable'];                  
                update_post_meta(
                  $post_id, 
                  'adsforwp_v_condition_enable', 
                  $status 
                );     
              }
         }              
 public function adsforwp_visitor_condition_logic_checker($input){
        global $post;        
        $type               = $input['key_1'];
        $comparison         = $input['key_2'];
        $data               = $input['key_3'];       
        
        
        $result             = ''; 
       
        // Get all the users registered
        $user               = wp_get_current_user();

        switch ($type) {
                   
          case 'device':                      
                    $device_name  = 'desktop'; 
                    if(wp_is_mobile()){
                    $device_name  = 'mobile';                
                    }                  
                  if ( $comparison == 'equal' ) {
                        if ( $device_name == $data ) {
                          $result = true;
                        }
                  }
                    if ( $comparison == 'not_equal') {              
                        if ( $device_name != $data ) {
                          $result = true;
                        }
                    }            
          break;
          case 'referrer_url':    
                                        
                    $referrer_url  = esc_url($_SERVER['HTTP_REFERER']);                      
                    if(isset($input['key_4']) && $input['key_3']=='url_custom'){
                    $data = $input['key_4'];   
                    }
                    
                    if ( $comparison == 'equal' ) {
                        if ( $referrer_url == $data ) {
                          $result = true;
                        }
                    }
                    if ( $comparison == 'not_equal') {              
                        if ( $referrer_url != $data ) {
                          $result = true;
                        }
                    }    
                    
                    
                              
          break;
                   
          case 'browser_language':                      
                   $browser_language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);                                                                     
                  if ( $comparison == 'equal' ) {
                        if ( $browser_language == $data ) {
                          $result = true;
                        }
                  }
                    if ( $comparison == 'not_equal') {              
                        if ( $browser_language != $data ) {
                          $result = true;
                        }
                    }            
          break;
          
           case 'logged_in_visitor': 
            
             if ( is_user_logged_in() ) {
                $status = 'true';
             } else {
                $status = 'false';
             }
                          
            if ( $comparison == 'equal' ) {
                if ( $status == $data ) {
                  $result = true;
                }
            }
            if ( $comparison == 'not_equal') {              
                if ( $status != $data ) {
                  $result = true;
                }
            }

        break;
        
        case 'user_agent':            
                $user_agent_name =$this->adsforwp_detect_user_agent();                               
                if(isset($input['key_5']) && $input['key_3']=='user_agent_custom'){
                    $data = strtolower($input['key_5']);
                }
                if ( $comparison == 'equal' ) {
                if ( $user_agent_name == $data ) {
                  $result = true;
                }
               }
                if ( $comparison == 'not_equal') {              
                if ( $user_agent_name != $data ) {
                  $result = true;
                }
               }                         
        break;
        
        case 'user_type':            
            if ( $comparison == 'equal') {
                if ( in_array( $data, (array) $user->roles ) ) {
                    $result = true;
                }
            }            
            if ( $comparison == 'not_equal') {
                require_once ABSPATH . 'wp-admin/includes/user.php';
                // Get all the registered user roles
                $roles = get_editable_roles();                
                $all_user_types = array();
                foreach ($roles as $key => $value) {
                  $all_user_types[] = $key;
                }
                // Flip the array so we can remove the user that is selected from the dropdown
                $all_user_types = array_flip( $all_user_types );
                // User Removed
                unset( $all_user_types[$data] );

                // Check and make the result true that user is not found 
                if ( in_array( $data, (array) $all_user_types ) ) {
                    $result = true;
                }
            }
            
           break;
         
      default:
        $result = false;
        break;
    }

    return $result;
} 


 public function adsforwp_detect_user_agent( ){
            $user_agent_name ='others';           
            if     (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') || strpos($user_agent, 'OPR/')) $user_agent_name = 'opera';
            elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Edge'))    $user_agent_name = 'edge';            
            elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) $user_agent_name ='firefox';
            elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') || strpos($user_agent, 'Trident/7')) $user_agent_name = 'internet_explorer';                        
            elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'iPod')) $user_agent_name = 'ipod';
            elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'iPhone')) $user_agent_name = 'iphone';
            elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'iPad')) $user_agent_name = 'ipad';
            elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'Android')) $user_agent_name = 'android';
            elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'webOS')) $user_agent_name = 'webos';
            elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome'))  $user_agent_name = 'chrome';
            elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari'))  $user_agent_name = 'safari';
            
            return $user_agent_name;
}   

 public function adsforwp_visitor_condition_field_data( $post_id ){
      $visitor_conditions_array = get_post_meta( $post_id, 'visitor_conditions_array', true);  
      $output = array();
      if($visitor_conditions_array){          
      foreach ($visitor_conditions_array as $gropu){
         $output[] = array_map(array($this, 'adsforwp_visitor_condition_logic_checker'), $gropu['visitor_conditions']);     
      }   
      
      }         
      return $output;
}   

 public function adsforwp_visitor_conditions_status($post_id){
       
          $unique_checker ='';
          $visitor_condition_enable = get_post_meta($post_id, $key='adsforwp_v_condition_enable', true);
                    
          if(isset($visitor_condition_enable) && $visitor_condition_enable =='enable'){
          
          $resultset = $this->adsforwp_visitor_condition_field_data( $post_id ); 
          if($resultset){
              
          $condition_array = array(); 
          
          foreach ($resultset as $result){
          
             $data = array_filter($result);          
             $number_of_fields = count($data);
             $checker = 0;
             
             if ( $number_of_fields > 0 ) {                    
                $checker = count( array_unique($data) );             
                $array_is_false =  in_array(false, $result);           
            if (  $array_is_false ) {
                $checker = 0;
            }
           }
             
          $condition_array[] = $checker;    
          }
          
          $array_is_true = in_array(true,$condition_array);
          if($array_is_true){
          $unique_checker = 1;    
          }          
          }else{
           $unique_checker ='notset';   
          }
        }else{
           $unique_checker = 1;   
        }           
       return $unique_checker;
}         
    
}
if (class_exists('adsforwp_view_visitor_condition')) {
	new adsforwp_view_visitor_condition;
};
