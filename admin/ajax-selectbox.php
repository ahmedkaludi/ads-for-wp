<?php

class adsforwp_ajax_selectbox{
    
public function __construct() {
      add_action('wp_ajax_adsforwp_create_ajax_select_box',array($this,'adsforwp_ajax_select_creator')); 
      add_action('wp_ajax_adsforwp_ajax_select_taxonomy',array($this,'adsforwp_create_ajax_select_taxonomy'));
}
    

public function adsforwp_post_type_generator(){

    $post_types = '';
    $post_types = get_post_types( array( 'public' => true ), 'names' );
    
    unset($post_types['attachment'], $post_types['amp_acf']);

    return $post_types;
}

public function adsforwp_ajax_select_creator($data = '', $saved_data= '', $current_number = '') {
 
    $response = $data;
    $is_ajax = false;
    if( $_SERVER['REQUEST_METHOD']=='POST'){
        $is_ajax = true;
        if(wp_verify_nonce($_POST["adsforwp_call_nonce"],'adsforwp_select_action_nonce')){
            
            if ( isset( $_POST["id"] ) ) {
              $response = sanitize_text_field(wp_unslash($_POST["id"]));
            }
            if ( isset( $_POST["number"] ) ) {
              $current_number   = intval($_POST["number"]);
            }
        }else{
            exit;
        }
       
    }        
        // send the response back to the front end
       // vars
    $choices = array();    
    
    $options['param'] = $response;
    // some case's have the same outcome
        if($options['param'] == "page_parent")
        {
          $options['param'] = "page";
        }
    
        switch($options['param'])
        {
          case "post_type":

            $choices = $this->adsforwp_post_type_generator();
            
            $choices = apply_filters('adsforwp_modify_select_post_type', $choices );           
            break;

          case "page":

            $post_type = 'page';
            $posts = get_posts(array(
              'posts_per_page'          =>  -1,
              'post_type'               => $post_type,
              'orderby'                 => 'menu_order title',
              'order'                   => 'ASC',
              'post_status'             => 'any',
              'suppress_filters'        => false,
              'update_post_meta_cache'  => false,
            ));

            if( $posts )
            {
              // sort into hierachial order!
              if( is_post_type_hierarchical( $post_type ) )
              {
                $posts = get_page_children( 0, $posts );
              }

              foreach( $posts as $page )
              {
                $title = '';
                $ancestors = get_ancestors($page->ID, 'page');
                if($ancestors)
                {
                  foreach($ancestors as $a)
                  {
                    $title .= '- ';
                  }
                }

                $title .= apply_filters( 'the_title', $page->post_title, $page->ID );                        
                // status
                if($page->post_status != "publish")
                {
                  $title .= " ($page->post_status)";
                }

                $choices[ $page->ID ] = $title;

              }
              // foreach($pages as $page)

            }

            break;

          case "page_template" :

            $choices = array(
              'default' =>  esc_html__('Default Template','ads-for-wp'),
            );

            $templates = get_page_templates();
            foreach($templates as $k => $v)
            {
              $choices[$v] = $k;
            }

            break;

          case "post" :

            $post_types = get_post_types();

            unset( $post_types['page'], $post_types['attachment'], $post_types['revision'] , $post_types['nav_menu_item'], $post_types['acf'] , $post_types['amp_acf']  );

            if( $post_types )
            {
              foreach( $post_types as $post_type )
              {

                $posts = get_posts(array(
                  'numberposts' => '-1',
                  'post_type' => $post_type,
                  'post_status' => array('publish', 'private', 'draft', 'inherit', 'future'),
                  'suppress_filters' => false,
                ));

                if( $posts)
                {
                  $choices[$post_type] = array();

                  foreach($posts as $post)
                  {
                    $title = apply_filters( 'the_title', $post->post_title, $post->ID );

                    // status
                    if($post->post_status != "publish")
                    {
                      $title .= " ($post->post_status)";
                    }

                    $choices[$post_type][$post->ID] = $title;

                  }
                  // foreach($posts as $post)
                }
                // if( $posts )
              }
              // foreach( $post_types as $post_type )
            }
            // if( $post_types )


            break;

          case "post_category" :

            $terms = get_terms( 'category', array( 'hide_empty' => false ) );

            if( !empty($terms) ) {

              foreach( $terms as $term ) {

                $choices[ $term->term_id ] = $term->name;

              }

            }

            break;

          case "post_format" :

            $choices = get_post_format_strings();

            break;

          case "user_type" :
           global $wp_roles;
            $choices = $wp_roles->get_names();

            if( is_multisite() )
            {
              $choices['super_admin'] = esc_html__('Super Admin','ads-for-wp');
            }

            break;

          case "ef_taxonomy" :

            $choices = array('all' => esc_html__('All','ads-for-wp'));
            $taxonomies = $this->adsforwp_post_taxonomy_generator();        
            $choices = array_merge($choices, $taxonomies);                      
            break;

        }        
    // allow custom location rules
    $choices = $choices; 

    // Add None if no elements found in the current selected items
    if ( empty( $choices) ) {
      $choices = array('none' => esc_html__('No Items', 'ads-for-wp') );
    }
     //  echo $current_number;
    // echo $saved_data;

      $output = '<select  class="widefat ajax-output" name="data_array['. esc_attr($current_number) .'][key_3]">'; 

        // Generate Options for Posts
        if ( $options['param'] == 'post' ) {
          foreach ($choices as $choice_post_type) {      
            foreach ($choice_post_type as $key => $value) { 
                if ( $saved_data ==  $key ) {
                    $selected = 'selected="selected"';
                } else {
                  $selected = '';
                }

                $output .= '<option '. esc_attr($selected) .' value="' .  esc_attr($key) .'"> ' .  esc_html__($value, 'ads-for-wp') .'  </option>';            
            }
          }
         // Options for Other then posts
        } else {
          foreach ($choices as $key => $value) { 
                if ( $saved_data ==  $key ) {
                    $selected = 'selected="selected"';
                } else {
                  $selected = '';
                }

            $output .= '<option '. esc_attr($selected) .' value="' . esc_attr($key) .'"> ' .  esc_html__($value, 'ads-for-wp') .'  </option>';            
          } 
        }
    $output .= ' </select> '; 
    $common_function_obj = new adsforwp_admin_common_functions();  
    $allowed_html = $common_function_obj->adsforwp_expanded_allowed_tags();
    echo wp_kses($output, $allowed_html); 
    
    if ( $is_ajax ) {
      die();
    }
// endif;  

}

public function adsforwp_post_taxonomy_generator(){
    $taxonomies = '';  
    $choices    = array();
    $taxonomies = get_taxonomies( array('public' => true), 'objects' );
    

      foreach($taxonomies as $taxonomy) {
        $choices[ $taxonomy->name ] = $taxonomy->labels->name;
      }
      
      // unset post_format (why is this a public taxonomy?)
      if( isset($choices['post_format']) ) {
        unset( $choices['post_format']) ;
      }
      
    return $choices;
}


public function adsforwp_create_ajax_select_taxonomy($selectedParentValue = '',$selectedValue='', $current_number =''){
    $is_ajax = false;
    if( $_SERVER['REQUEST_METHOD']=='POST'){
        $is_ajax = true;
        if(wp_verify_nonce($_POST["adsforwp_call_nonce"],'adsforwp_select_action_nonce')){
              if(isset($_POST['id'])){
                $selectedParentValue = sanitize_text_field(wp_unslash($_POST['id']));
              }
              if(isset($_POST['number'])){
                $current_number = intval($_POST['number']);
              }
        }else{
            exit;
        }       
    }
    $taxonomies = array(); 
    if($selectedParentValue == 'all'){
    $taxonomies =  get_terms( array(
                        'hide_empty' => true,
                    ) );    
    }else{
    $taxonomies =  get_terms($selectedParentValue, array(
                        'hide_empty' => true,
                    ) );    
    }     
    $choices = '<option value="all">'.esc_html__('All','ads-for-wp').'</option>';
    foreach($taxonomies as $taxonomy) {
      $sel="";
      if($selectedValue == $taxonomy->slug){
        $sel = "selected";
      }
      $choices .= '<option value="'.esc_attr($taxonomy->slug).'" '.esc_attr($sel).'>'.esc_html__($taxonomy->name,'ads-for-wp').'</option>';
    }
    $common_function_obj = new adsforwp_admin_common_functions();  
    $allowed_html = $common_function_obj->adsforwp_expanded_allowed_tags();    
    echo '<select  class="widefat afw-ajax-output-child" name="data_array['. esc_attr($current_number) .'][key_4]">'. wp_kses($choices, $allowed_html).'</select>';
    if($is_ajax){
      die;
    }
}

}
if (class_exists('adsforwp_ajax_selectbox')) {
	new adsforwp_ajax_selectbox;
};