<?php
class adsforwp_view_placement {
    
 public function __construct() {                                                                                                     
		add_action( 'add_meta_boxes', array( $this, 'adsforwp_placement_add_meta_box' ) );
		add_action( 'save_post', array( $this, 'adsforwp_placement_save' ) );
	}
        
 public function adsforwp_placement_add_meta_box() {
     
        global $post;
        $in_group = array();
        
        if(is_object($post)){
        
            $common_function_obj = new adsforwp_admin_common_functions();
            $in_group = $common_function_obj->adsforwp_check_ads_in_group($post->ID); 
            
        }
     
        if(empty($in_group)){
            
            add_meta_box(
		'adsforwp_placement_metabox',
		esc_html__( 'Advanced Display Conditions', 'ads-for-wp' ),
		array( $this, 'adsforwp_meta_box_callback' ),
		array('adsforwp', 'adsforwp-groups'),
		'normal',
		'low'
            );
            
        }
	
    }
        
 public function adsforwp_meta_box_callback( $post) {   
     
            $data_group_array =  esc_sql ( get_post_meta($post->ID, 'data_group_array', true)  );              
            $data_group_array = is_array($data_group_array)? array_values($data_group_array): array(); 
            
            if ( empty( $data_group_array ) ) {
                
                       $data_group_array[0] = array(
                           
                           'data_array' => array(
                                    array(
                                    'key_1' => 'post_type',
                                    'key_2' => 'equal',
                                    'key_3' => 'none',
                                    )
                       )      
                           
                   );
                       
            }
    //security check
    wp_nonce_field( 'adsforwp_select_action_nonce', 'adsforwp_select_name_nonce' );?>

    <?php 
    // Type Select    
      $choices = array(
        esc_html__("Basic",'ads-for-wp') => array(                    
          'post_type'       =>  esc_html__("Post Type",'ads-for-wp'),
          'show_globally'   =>  esc_html__("Show Globally",'ads-for-wp'),
          'homepage'        =>  esc_html__("HomePage",'ads-for-wp'),  
          'user_type'       =>  esc_html__("Logged in User Type",'ads-for-wp'),
        ),
        esc_html__("Post",'ads-for-wp') => array(
          'post'            =>  esc_html__("Post",'ads-for-wp'),
          'post_category'   =>  esc_html__("Post Category",'ads-for-wp'),
          'post_format'     =>  esc_html__("Post Format",'ads-for-wp'), 
        ),
        esc_html__("Page",'ads-for-wp') => array(
          'page'            =>  esc_html__("Page",'ads-for-wp'), 
          'page_template'   =>  esc_html__("Page Template",'ads-for-wp'),
        ),
        esc_html__("Other",'ads-for-wp') => array( 
          'ef_taxonomy'     =>  esc_html__("Taxonomy Term",'ads-for-wp'), 
          'User'            =>  esc_html__("User",'ads-for-wp'),   
        )
      ); 

      $comparison = array(
        'equal'         =>  esc_html__( 'Equal to', 'ads-for-wp'), 
        'not_equal'     =>  esc_html__( 'Not Equal to', 'ads-for-wp'),     
      );
      $total_group_fields = count( $data_group_array );       
      ?>
<div class="afw-placement-groups">
      
    <?php for ($j=0; $j < $total_group_fields; $j++) {
        
        $data_array = $data_group_array[$j]['data_array'];        
        $total_fields = count( $data_array );
        
        ?>
    <div class="afw-placement-group" name="data_group_array[<?php echo esc_attr( $j) ?>]" data-id="<?php echo esc_attr($j); ?>">           
     <?php 
        if($j>0){
        echo '<span style="margin-left:10px;font-weight:600">Or</span>';    
        }     
     ?>   
     <table class="widefat afw-widefat">
        <tbody id="afw-repeater-tbody" class="fields-wrapper-1">
        <?php 
        
        for ($i=0; $i < $total_fields; $i++) {  
            
            $selected_val_key_1 = $data_array[$i]['key_1']; 
            $selected_val_key_2 = $data_array[$i]['key_2']; 
            $selected_val_key_3 = $data_array[$i]['key_3'];
            
            $selected_val_key_4 = '';
            
            if(isset($data_array[$i]['key_4'])){
                
                $selected_val_key_4 = $data_array[$i]['key_4'];
              
            }          
            
          ?>
            
          <tr class="toclone">
            <td style="width:31%" class="post_types"> 
              <select class="widefat afw-select-post-type <?php echo esc_attr( $i );?>" name="data_group_array[group-<?php echo esc_attr( $j) ?>][data_array][<?php echo esc_attr( $i) ?>][key_1]">    
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
              <select class="widefat comparison" name="data_group_array[group-<?php echo esc_attr( $j) ?>][data_array][<?php echo esc_attr( $i )?>][key_2]"> <?php
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
              <div class="afw-insert-ajax-select">              
                <?php 
                $ajax_select_box_obj = new adsforwp_ajax_selectbox();
                $ajax_select_box_obj->adsforwp_ajax_select_creator($selected_val_key_1, $selected_val_key_3, $i, $j );
                if($selected_val_key_1 == 'ef_taxonomy'){
                  $ajax_select_box_obj->adsforwp_create_ajax_select_taxonomy($selected_val_key_3, $selected_val_key_4, $i, $j);
                }
                ?>
                  <div style="display:none;" class="spinner"></div>
              </div>
            </td>
            
            <td class="widefat placement-row-clone" style="width:3.5%; <?php if (  $selected_val_key_1 =='show_globally' ) { echo 'display:none;'; }  ?>">
                <span> <button type="button" class="afw-placement-button"> <?php echo esc_html__('And' ,'ads-for-wp');?> </button> </span> 
            </td>
            
            <td class="widefat placement-row-delete" style="width:3.5%; <?php if (  $selected_val_key_1 =='show_globally' ) { echo 'display:none;'; }  ?>">
                <button  type="button"><span class="dashicons dashicons-trash"></span> </button>
            </td>                                           
          </tr>
          
          <?php 
          
        } ?>
        </tbody>
      </table> 
    </div>
    <?php } ?>
    
    <a style="margin-left: 8px; margin-bottom: 8px;" class="button afw-placement-or-group afw-placement-button" href="#"><?php echo esc_html__("Or",'ads-for-wp') ?></a>
</div>    
    <?php                                                      
                                
        }
   
 public function adsforwp_placement_save( $post_id ) {
     
     if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
       
      // if our nonce isn't there, or we can't verify it, bail
      if( !isset( $_POST['adsforwp_select_name_nonce'] ) || !wp_verify_nonce( $_POST['adsforwp_select_name_nonce'], 'adsforwp_select_action_nonce' ) ) return;
      
      // if our current user can't edit this post, bail
      if( !current_user_can( 'edit_post' ) ) return;  
      
            $post_data_group_array = array();  
            $temp_condition_array  = array();
            $show_globally         = false;  
            
            if(isset($_POST['data_group_array'])){
                $post_data_group_array = (array) $_POST['data_group_array']; 
                 /* Type casted the $_POST['data_group_array'] to (array) to make sure 
                 * what ever data is sent is should be in array format.
                 * and then we are sanitizing $post_data_group_array with adsforwp_sanitize_multi_array() 
                 * function to make sure we have sanitized keys and its values.
                */
                
                foreach($post_data_group_array as $groups){
                  foreach($groups['data_array'] as $group ){  
                    if(array_search('show_globally', $group)){
                      $temp_condition_array[0] =  $group;  
                      $show_globally = true;  
                    }
                  }
                }  
                if($show_globally){
                    unset($post_data_group_array);
                    $post_data_group_array['group-0']['data_array'] = $temp_condition_array;
                }
            }

            if(!empty($post_data_group_array)){
                
                $post_data_group_array = adsforwp_sanitize_multi_array($post_data_group_array, 'data_array');
                
                update_post_meta(
                  $post_id, 
                  'data_group_array', 
                  $post_data_group_array 
                );     
                
              }
         }  
            
 public function adsforwp_comparison_logic_checker($input){
     
        global $post;  
        
        $type = $comparison = $data = $result = '';
        
        if(is_array($input)){
            
        $type       = array_key_exists('key_1', $input) ? $input['key_1'] : '';
        $comparison = array_key_exists('key_2', $input) ? $input['key_2'] : '';
        $data       = array_key_exists('key_3', $input) ? $input['key_3'] : '';
            
        }
        
        // Get all the users registered
        $user               = wp_get_current_user();

        switch ($type) {
        // Basic Controls ------------ 
        
         case 'homepage':    
          
            $homepage ='false';  
          
            if(is_home() || is_front_page() || ( function_exists('ampforwp_is_home') && ampforwp_is_home()) ){
               $homepage = 'true';  
            }
                      
            if ( $comparison == 'equal' ) {
                if ( $homepage == $data ) {
                  $result = true;
                }
            }
            if ( $comparison == 'not_equal') {              
                if ( $homepage != $data ) {
                  $result = true;
                }
            }
        break;   
                        
          // Posts Type
          
          case 'show_globally': 
              
               $result = true;    
              
          break;
          
          case 'post_type':   
                
                $current_post_type ='';
              
                if(is_singular()){
                    
                     $current_post_type  = $post->post_type;  
                     
                }              
                  if ( $comparison == 'equal' ) {
                      
                  if ( $current_post_type == $data ) {
                      
                    $result = true;
                    
                  }
                  
                }
              if ( $comparison == 'not_equal') { 
                  
                  if ( $current_post_type != $data ) {
                      
                    $result = true;
                    
                  }
                  
              }            
          break;

      // Logged in User Type
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

    // Post Controls  ------------ 
      // Posts
      case 'post': 
          
            $current_post ='';              
                if(is_singular()){                    
                    $current_post = $post->ID;                      
                }                      
            if ( $comparison == 'equal' ) {
                if ( $current_post == $data ) {
                  $result = true;
                }
            }
            if ( $comparison == 'not_equal') {              
                if ( $current_post != $data ) {
                  $result = true;
                }
            }

        break;

      // Post Category
      case 'post_category':
          
          $current_category = '';
          $postcat = get_the_category( $post->ID );
          
          if(!empty($postcat)){
              if(is_object($postcat[0])){                 
                $current_category = $postcat[0]->cat_ID;                   
              }               
          }
                   
          if ( $comparison == 'equal') {
              if ( $data == $current_category ) {
                  $result = true;
              }
          }
          if ( $comparison == 'not_equal') {
              if ( $data != $current_category ) {
                  $result = true;
              }
          }
        break;
      // Post Format
      case 'post_format':
          
          $current_post_format = get_post_format( $post->ID );
          
          if ( $current_post_format === false ) {
              $current_post_format = 'standard';
          }
          if ( $comparison == 'equal') {
              
              if ( $data == $current_post_format ) {
                  $result = true;
              }
              
          }
          if ( $comparison == 'not_equal') {
              if ( $data != $current_post_format ) {
                  $result = true;
              }
          }
        break;

    // Page Controls ---------------- 
      // Page
      case 'page': 
        global $redux_builder_amp;
        if(function_exists('ampforwp_is_front_page')){
          if(ampforwp_is_front_page()){
          $current_post = $redux_builder_amp['amp-frontpage-select-option-pages'];    
          } else{
          $current_post = $post->ID;    
          }           
        }else{
          $current_post = $post->ID;
        }
            if ( $comparison == 'equal' ) {
                if ( $current_post == $data ) {
                  $result = true;
                }
            }
            if ( $comparison == 'not_equal') {              
                if ( $current_post != $data ) {
                  $result = true;
                }
            }
        break;

      // Page Template 
      case 'page_template':
        $current_page_template = get_page_template_slug( $post->ID );
            if ( $current_page_template == false ) {
                $current_page_template = 'default';
            }
            if ( $comparison == 'equal' ) {
                if ( $current_page_template == $data ) {
                    $result = true;
                }
            }
            if ( $comparison == 'not_equal') {              
                if ( $current_page_template != $data ) {
                    $result = true;
                }
            }

        break; 

    // Other Controls ---------------
      // Taxonomy Term
      case 'ef_taxonomy':
        // Get all the post registered taxonomies        
        // Get the list of all the taxonomies associated with current post
        $taxonomy_names = get_post_taxonomies( $post->ID );

        $checker    = '';
        $post_terms = '';

          if ( $data != 'all') {
            $post_terms = wp_get_post_terms($post->ID, $data);           

            if ( $comparison == 'equal' ) {
                if ( $post_terms ) {
                    $result = true;
                }
            }

            if ( $comparison == 'not_equal') { 
                $checker =  in_array($data, $taxonomy_names);       
                if ( ! $checker ) {
                    $result = true;
                }
            }
            if($result==true && isset( $input['key_4'] ) && $input['key_4'] !='all'){
                
              $term_data       = $input['key_4'];
              $terms           = wp_get_post_terms( $post->ID ,$data);
              
              if(count($terms)>0){
                  
                $termChoices = array();
                
                foreach ($terms as $key => $termvalue) {
                    
                   $termChoices[] = $termvalue->slug;
                   
                 } 
              }
              $result = false;
              if(in_array($term_data, $termChoices)){
                $result = true;
              }
            }//if closed for key_4

          } else {

            if ( $comparison == 'equal' ) {
              if ( $taxonomy_names ) {                
                  $result = true;
              }
            }

            if ( $comparison == 'not_equal') { 
              if ( ! $taxonomy_names ) {                
                  $result = true;
              }
            }

          }
        break;
      
      default:
        $result = false;
        break;
    }

    return $result;
} 

 public function adsforwp_generate_field_data( $post_id ){
     
      $data_group_array = get_post_meta( $post_id, 'data_group_array', true);  
      $output = array();
      
      if($data_group_array){ 
          
      foreach ($data_group_array as $gropu){
          
         $output[] = array_map(array($this, 'adsforwp_comparison_logic_checker'), $gropu['data_array']); 
         
      }   
      
      }         
      return $output;
}   

 public function adsforwp_get_post_conditions_status($post_id){
       
          $unique_checker   = '';
          $resultset        = $this->adsforwp_generate_field_data( $post_id ); 
          
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
                   
       return $unique_checker;
}         
    
}
if (class_exists('adsforwp_view_placement')) {
	new adsforwp_view_placement;
};
