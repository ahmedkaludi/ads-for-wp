<?php
   /**
     * This is a ajax handler function for importing plugins data. 
     * @return type json string
     */
function adsforwp_import_plugin_data(){                  
        $plugin_name   = sanitize_text_field($_GET['plugin_name']); 
        $common_function_obj = new adsforwp_admin_common_functions();
        $result = '';
        switch ($plugin_name) {
            case 'advanced_ads':
                if ( is_plugin_active('advanced-ads/advanced-ads.php')) {
                 $result = $common_function_obj->adsforwp_import_all_advanced_ads();      
                }                
                break;
                
            case 'ampforwp_ads':
               
                if ( is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php')) {                     
                 $result = $common_function_obj->adsforwp_import_all_amp_ads();      
                }                
                break;

            default:
                break;
        }                             
        if($result){
        echo json_encode(array('status'=>'t', 'message'=>esc_html__('Data has been imported succeessfully','ads-for-wp')));            
        }else{
        echo json_encode(array('status'=>'f', 'message'=>esc_html__('Plugin data is not available or it is not activated','ads-for-wp')));            
        }        
           wp_die();           
}

add_action('wp_ajax_adsforwp_import_plugin_data', 'adsforwp_import_plugin_data');

/**
* Remove Add new menu
**/
function adsforwp_disable_new_posts() {
	// Hide sidebar link
	global $submenu;
	unset($submenu['edit.php?post_type=adsforwp'][10]);

	// Hide link on listing page
	if (isset($_GET['post_type']) && $_GET['post_type'] == 'adsforwp') {
	    return '<style type="text/css">
	    #favorite-actions, .add-new-h2, .tablenav { display:none; }
	    </style>';
	}
}
add_action('admin_menu', 'adsforwp_disable_new_posts');

   /**
     * This is a ajax handler function for sending email from user admin panel to us. 
     * @return type json string
     */
function adsforwp_send_query_message(){                  
        $message    = sanitize_text_field($_POST['message']); 
        $user       = wp_get_current_user();
        $user_data  = $user->data;        
        $user_email = $user_data->user_email;       
        //php mailer variables
        $to = 'team@magazine3.com';
        $subject = "Customer Query";
        $headers = 'From: '. $user_email . "\r\n" .
        'Reply-To: ' . $user_email . "\r\n";
        // Load WP components, no themes.                      
        $sent = wp_mail($to, $subject, strip_tags($message), $headers);        
        if($sent){
        echo json_encode(array('status'=>'t'));            
        }else{
        echo json_encode(array('status'=>'f'));            
        }        
           wp_die();           
}

add_action('wp_ajax_adsforwp_send_query_message', 'adsforwp_send_query_message');
/*
 * Use of shortcode in php script 
 * Usage : <?php adsforwp_the_ad(3013); ?>
 * Display single ad
 */
function adsforwp_the_ad($ad_id){
   $output_function_obj = new adsforwp_output_functions();
   $ad_code =  $output_function_obj->adsforwp_get_ad_code($ad_id, $type="AD");  
   echo $ad_code;
}
/*
 * Use of shortcode in php script 
 * Usage : <?php adsforwp_the_group(3013); ?>
 * Display group ads
 */
function adsforwp_the_group($group_id){
    
   $output_function_obj = new adsforwp_output_functions();
   $group_code =  $output_function_obj->adsforwp_group_ads($atts=null, $group_id, 'widget');     
   echo $group_code;
}   

/**
 * We are adding extra fields for user profile
 * @param type $user
 */
function adsforwp_extra_user_profile_fields( $user ) {
    
    ?>
    <h3><?php _e("Extra profile information", "ads-for-wp"); ?></h3>

    <table class="form-table">
    <tr>
        <th><label for="afw-data-client-id"><?php _e("AdSense Publisher ID"); ?></label></th>
        <td>
            <input placeholder="ca-pub-13XXXXXXXXXXXX64" type="text" name="adsense_pub_id" id="adsense_pub_id" value="<?php echo esc_attr( get_the_author_meta( 'adsense_pub_id', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your pub ID.", "ads-for-wp"); ?></span>
        </td>
    </tr>
    
    <tr>
        <th><label for="afw-data-ad-slot"><?php _e("Data Ad Slot"); ?></label></th>
        <td>
            <input placeholder="70XXXXXX12" type="text" name="adsense_ad_slot_id" id="adsense_ad_slot_id" value="<?php echo esc_attr( get_the_author_meta( 'adsense_ad_slot_id', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your ad slot ID.", "ads-for-wp"); ?></span>
        </td>
    </tr>
    
    </table>
<?php 
}
add_action( 'show_user_profile', 'adsforwp_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'adsforwp_extra_user_profile_fields' );

/**
 * we are saving user extra fields data in database
 * @param type $user_id
 * @return boolean
 */
function adsforwp_save_extra_user_profile_fields( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) { 
        return false; 
    }
    $adsense_pub_id = $_POST['adsense_pub_id'];
    $adsense_ad_slot_id = $_POST['adsense_ad_slot_id'];
    update_user_meta( $user_id, 'adsense_pub_id', $adsense_pub_id ); 
    update_user_meta( $user_id, 'adsense_ad_slot_id', $adsense_ad_slot_id ); 
}
add_action( 'personal_options_update', 'adsforwp_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'adsforwp_save_extra_user_profile_fields' );
/**
 * We are here overriding tile for adsforwp post type
 * @global type $post
 * @param string $title
 * @return string
 */
function adsforwp_modify_title( $title) {
    global $post;
    if($post){
    if($post->post_type =='adsforwp'){
        $adsense_auto = get_post_meta($post->ID, $key='adsense_type', true);
        if($adsense_auto === 'adsense_auto_ads'){
            $title = $title.' (Auto AdSense Ad)';
        }
    }    
}
    return $title;
}
add_filter( 'the_title', 'adsforwp_modify_title', 10, 1 );

/**
* This is a ajax handler function to check adsese auto ads, if it is already added.
* @return type json string
 */
function adsforwp_ajax_check_post_availability(){                                     
                    $cc_args = array(
                        'posts_per_page'   => -1,
                        'post_type'        => 'adsforwp',
                        'meta_key'         => 'adsense_type',
                        'meta_value'       => 'adsense_auto_ads',
                    );
                    $postdata = new WP_Query($cc_args);                      
                    $auto_adsense_post = $postdata->posts;                      
                    if($postdata->post_count >0){                   
                    $ad_sense_type = get_post_meta($auto_adsense_post[0]->ID,$key='adsense_type',true);                     
                    }               
                    if($ad_sense_type){
                    echo json_encode(array('status'=> 't','post_id'=> $auto_adsense_post[0]->ID, 'adsense_type'=> $ad_sense_type));        
                    }else{
                    echo json_encode(array('status'=> 'f','post_id'=> esc_html__('not available', 'ads-for-wp')));                                 
                    }
        
           wp_die();           
}
add_action('wp_ajax_adsforwp_check_meta', 'adsforwp_ajax_check_post_availability');

/**
 * This function gets the link for selected tabs in setting section on ajax request
 * @param type $tab
 * @param type $args
 * @return type
 */
function adsforwp_admin_link($tab = '', $args = array()){	
	$page = 'adsforwp';
	if ( ! is_multisite() ) {
		$link = admin_url( 'admin.php?page=' . $page );
	}
	else {
		$link = network_admin_url( 'admin.php?page=' . $page );
	}

	if ( $tab ) {
		$link .= '&tab=' . $tab;
	}

	if ( $args ) {
		foreach ( $args as $arg => $value ) {
			$link .= '&' . $arg . '=' . urlencode( $value );
		}
	}

	return esc_url($link);
}

/**
 * This function gets the link for selected tabs in setting section on ajax request
 * @param type $tab
 * @param type $args
 * @return type
 */
function adsforwp_analytics_admin_link($tab = '', $args = array()){	
	$page = 'analytics';
	if ( ! is_multisite() ) {
		$link = admin_url( 'edit.php?post_type=adsforwp&page=' . $page );
	}
	else {
		$link = network_admin_url( 'edit.php?post_type=adsforwp&page=' . $page );
	}

	if ( $tab ) {
		$link .= '&tab=' . $tab;
	}

	if ( $args ) {
		foreach ( $args as $arg => $value ) {
			$link .= '&' . $arg . '=' . urlencode( $value );
		}
	}

	return esc_url($link);
}

/**
 * Get the selected tab on page reload
 * @param type $default
 * @param type $available
 * @return type
 */
function adsforwp_get_tab( $default = '', $available = array() ) {

	$tab = isset( $_GET['tab'] ) ? sanitize_text_field($_GET['tab']) : $default;
        
	if ( ! in_array( $tab, $available ) ) {
		$tab = $default;
	}
	return $tab;
}

/**
 * It is default settings value, if value is not set for any option in setting section 
 * @return type
 */
function adsforwp_defaultSettings(){
	$defaults = array(
		'app_blog_name'		=> get_bloginfo( 'name' ),
		'advnc_ads_import_check'	=> 1,
                'ad_blocker_support'	=> 1,
	);        
	$settings = get_option( 'adsforwp_settings', $defaults );         
	return $settings;
}

/**
 * We are here checking expire date of all ads and change status
 */
function adsforwp_update_ads_status(){
        $common_function_obj = new adsforwp_admin_common_functions();
        $all_ads = $common_function_obj->adsforwp_fetch_all_ads();
        $all_ads_post_meta = array();
    
    foreach($all_ads as $ad){
        
        $ads_post_meta = get_post_meta( $ad, $key='', true ); 
        if(isset($ads_post_meta['adsforwp_ad_expire_from'][0]) && isset($ads_post_meta['adsforwp_ad_expire_to'][0]) ){
            $current_date = date("Y-m-d");  
            if($ads_post_meta['adsforwp_ad_expire_to'][0] <$current_date){
             wp_update_post(array(
            'ID'    =>  $ad->ID,
            'post_status'   =>  'draft'
            ));   
         }
        }            
       }     
}
add_action( 'wp_loaded', 'adsforwp_update_ads_status' );


/**
 * We are registering our widget here in wordpress
 */
function register_adsforwp_ads_widget(){
    register_widget('Adsforwp_Ads_Widget');
}
add_action('widgets_init', 'register_adsforwp_ads_widget');

/*
 *      We are registering custom post type adsforwp in wordpress
 */
function adsforwp_setup_post_type() {
    $not_found_button = '<div><p style="float:left;margin-right:5px;">'.esc_html__('Welcome to Ads for WP. It looks like you don\'t have any ads.', 'ads-for-wp').'</p> <a href="'.esc_url( admin_url( 'post-new.php?post_type=adsforwp' ) ).'" class="button button-primary">'.esc_html('Let\'s create a new Ad', 'ads-for-wp').'</a></div>';
    $args = array(
	    'labels' => array(
	        'name' 			=> esc_html__( 'Ads', 'ads-for-wp' ),
	        'singular_name' 	=> esc_html__( 'Ad', 'ads-for-wp' ),
	        'add_new' 		=> esc_html__( 'Add New Ad', 'ads-for-wp' ),
	        'add_new_item'  	=> esc_html__( 'Add New Ad', 'ads-for-wp' ),
                'edit_item'             => esc_html__( 'Edit AD','ads-for-wp'),   
                'not_found'             => $not_found_button,
	    ),
      	'public' 		=> true,
      	'has_archive' 		=> false,
      	'exclude_from_search'	=> true,
    	'publicly_queryable'	=> false,
        'menu_position'         => 100  
    );
    register_post_type( 'adsforwp', $args );
    
    $group_post_type = array(
	    'labels' => array(
	        'name' 			=> esc_html__( 'Groups', 'ads-for-wp' ),	        
	        'add_new' 		=> esc_html__( 'Add New Groups', 'ads-for-wp' ),
	        'add_new_item'  	=> esc_html__( 'Edit Groups', 'ads-for-wp' ),
                'edit_item'             => esc_html__('Edit AD','ads-for-wp'),                
	    ),
      	'public' 		=> true,
      	'has_archive' 		=> false,
      	'exclude_from_search'	=> true,
    	'publicly_queryable'	=> false,
        'show_in_menu'  =>	'edit.php?post_type=adsforwp',                
        'show_ui'           => true,
	'show_in_nav_menus' => false,			
        'show_admin_column' => true,        
	'rewrite'           => false,        
    );
    register_post_type( 'adsforwp-groups', $group_post_type );        
}
add_action( 'init', 'adsforwp_setup_post_type' );

/**
 * Changing the label for ads list table header
 * @param type $views
 * @return type
 */
function adsforwp_modified_views_so( $views ){
    if(isset($views['draft'])){
    $views['draft'] = str_replace('Draft', 'Expire', $views['draft']);    
    }    
    if(isset($views['publish'])){
    $views['publish'] = str_replace('Published', 'Live', $views['publish']);    
    }    
    return $views;
}
add_filter( "views_edit-adsforwp", 'adsforwp_modified_views_so' );

/**
 * Add the custom columns to the adsforwp_groups post type:
 */
function adsforwp_groups_custom_columns($columns) {    
    $new = array();       
    $columns['ads_group_shortcode'] = '<a>'.esc_html__( 'ShortCode', 'ads-for-wp' ).'<a>';        
      foreach($columns as $key=>$value) {
        if($key=='date') {  // when we find the date column
           $new['ads_group_shortcode'] = $columns['ads_group_shortcode'];  // put the tags column before it
        }    
        $new[$key]=$value;
    }           
    return $new;
}
add_filter( 'manage_adsforwp-groups_posts_columns', 'adsforwp_groups_custom_columns' );
/**
 * Add the data to the custom columns for the adsforwp_groups post type:
 * @param type $column
 * @param type $post_id
 */
function adsforwp_group_custom_column_set( $column, $post_id ) {
        
        $common_function_obj = new adsforwp_admin_common_functions();
        $result = $common_function_obj->adsforwp_check_ads_in_group($post_id);
        $post_title ='';        
            foreach($result as $group){
               $group_post = get_post($group);  
               $post_title .= '<a href="'. esc_url(get_admin_url()).'post.php?post='.esc_attr($group).'&action=edit">'.esc_html__($group_post->post_title, 'ads-for-wp').'</a>, ';
            }
            switch ( $column ) {        
                case 'adsforwp_group_column' :
                    echo html_entity_decode(esc_attr($post_title)); 
                    break; 
                case 'adsforwp_ad_image_preview' :
                    $post_meta = get_post_meta($post_id, $key='', true);                    
                    if(isset($post_meta['select_adtype'])){
                        if($post_meta['select_adtype'][0] == 'ad_image'){
                        echo '<div><a href="'. esc_url(get_admin_url()).'post.php?post='.esc_attr($post_id).'&action=edit"><img width="150" src="'.$post_meta['adsforwp_ad_image'][0].'"></a></div>';    
                        }   
                    }                     
                    break;
                case 'adsforwp_ad_impression_column' :
                    $post_meta = get_post_meta($post_id, $key='ad_impression_count', true);                                          
                        echo '<div><span>'.esc_attr($post_meta).'<span></div>';                               
                                         
                    break;
                case 'adsforwp_ad_clicks_column' :
                    $post_meta = get_post_meta($post_id, $key='ad_clicks', true);                                          
                        echo '<div><span>'.esc_attr($post_meta).'<span></div>';                               
                                         
                    break;
                case 'adsforwp_expire_column' :
                    $post_meta = get_post_meta($post_id, $key='', true);
                    $expire_date ='';
                    if(isset($post_meta['adsforwp_ad_expire_to'])){
                    $expire_date = $post_meta['adsforwp_ad_expire_to'][0];    
                    }                    
                    if($expire_date){
                        $current_date = date("Y-m-d");
                        if($current_date>$expire_date){
                         echo esc_html__('Expired on', 'ads-for-wp').' '.date('M d Y', strtotime($expire_date));   
                        }else{
                         echo esc_html__('expires', 'ads-for-wp').' '.date('M d Y', strtotime($expire_date));
                        }
                    } 
                    break;     
            }
}
add_action( 'manage_adsforwp_posts_custom_column' , 'adsforwp_group_custom_column_set', 10, 2 );

/**
 * Add the custom columns to the Ads post type:
 * @param array $columns
 * @return string
 */

function adsforwp_custom_columns($columns) {    
    unset($columns['date']);
    $columns['adsforwp_ad_image_preview'] = '<a>'.esc_html__( 'Preview', 'ads-for-wp' ).'<a>';
    $columns['adsforwp_expire_column'] = '<a>'.esc_html__( 'Expire On', 'ads-for-wp' ).'<a>';
    $columns['adsforwp_group_column'] = '<a>'.esc_html__( 'Groups', 'ads-for-wp' ).'<a>';
    $columns['adsforwp_ad_impression_column'] = '<a>'.esc_html__( 'Ad Impression', 'ads-for-wp' ).'<a>';
    $columns['adsforwp_ad_clicks_column'] = '<a>'.esc_html__( 'Ad Clicks', 'ads-for-wp' ).'<a>';
    
    
    return $columns;
}
add_filter( 'manage_adsforwp_posts_columns', 'adsforwp_custom_columns' );

/**
 * Add the data to the custom columns for the adsforwp_groups post type:
 * @param type $column
 * @param type $post_id
 */
function adsforwp_custom_column_set( $column, $post_id ) {
    
    switch ( $column ) {        
        case 'ads_group_shortcode' :
            echo '<a>[adsforwp-group id="'.esc_attr($post_id).'"]</a>'; 
            break;
    }
}
add_action( 'manage_adsforwp-groups_posts_custom_column' , 'adsforwp_custom_column_set', 10, 2 );

/*
 *      Hiding WYSIWYG For AMPforWP Ads 2.0, as there is no need for it 
*/
function adsforwp_removing_wysiwig() {
         remove_post_type_support( 'adsforwp', 'editor');   
         remove_post_type_support( 'adsforwp-groups', 'editor');   
    
}
add_action( 'admin_init', 'adsforwp_removing_wysiwig' );

/*
 *	 REGISTER ALL NON-ADMIN SCRIPTS
 */
function adsforwp_frontend_enqueue(){
        wp_register_script('adsforwp-ads-front-js', ADSFORWP_PLUGIN_DIR_URI . 'public/ads-front.js', array( 'jquery' ), ADSFORWP_VERSION, true);
        $object_name = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),            
        );
        wp_localize_script('adsforwp-ads-front-js', 'adsforwp_obj', $object_name);
        wp_enqueue_script('adsforwp-ads-front-js');        
                
}
add_action( 'wp_enqueue_scripts', 'adsforwp_frontend_enqueue' );

/*
 *	Enqueue Javascript and CSS in admin area
 */
function adsforwp_admin_enqueue() {

         wp_enqueue_media(); 
         wp_enqueue_style('thickbox');
         wp_enqueue_script('thickbox'); 
         wp_enqueue_style('wp-pointer');
         wp_enqueue_script('wp-pointer');
         wp_enqueue_script( 'jquery-ui-datepicker' );
         wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
         wp_enqueue_style( 'jquery-ui' );
         add_action('admin_print_footer_scripts', 'adsforwp_print_footer_scripts' );
    
        wp_enqueue_style( 'ads-for-wp-admin', ADSFORWP_PLUGIN_DIR_URI . 'public/adsforwp.css', false , ADSFORWP_VERSION );
        wp_register_script( 'ads-for-wp-admin-js', ADSFORWP_PLUGIN_DIR_URI . 'public/adsforwp.js', array('jquery'), ADSFORWP_VERSION , true );
        wp_register_script( 'ads-for-wp-admin-analytics-js', ADSFORWP_PLUGIN_DIR_URI . 'public/analytics.js', array('jquery'), ADSFORWP_VERSION , true );
            // Localize the script with new data
        $data = array(
            'ajax_url'  => admin_url( 'admin-ajax.php' ),
            'id'		=> get_the_ID(),
            'uploader_title'		=> 'Ad Image',
            'uploader_button'		=> 'Select',
            'post_type'		=> get_post_type()
        );
        wp_localize_script( 'ads-for-wp-admin-js', 'adsforwp_localize_data', $data );	
        // Enqueued script with localized data.
        wp_enqueue_script( 'ads-for-wp-admin-js' );        
        
        //Analytics js
        $analytics_data = array(
            'ajax_url'  => admin_url( 'admin-ajax.php' ),
            'id'		=> get_the_ID(),            
            'post_type'		=> get_post_type()
        );
        wp_localize_script( 'ads-for-wp-admin-analytics-js', 'adsforwp_localize_analytics_data', $analytics_data );
        wp_enqueue_script( 'ads-for-wp-admin-analytics-js' );        
        	
}
add_action('admin_enqueue_scripts','adsforwp_admin_enqueue');

/*
 *      Storing and updating all ads post ids in transient on different actions 
 *      which we will fetch all ids from here to display our post
 */    
function adsforwp_published(){        
        $all_ads_post = get_posts(
            array(
                    'post_type' 	 => 'adsforwp',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
            )
        ); 
     $ads_post_ids = array();
     foreach($all_ads_post as $ads){
         $ads_post_ids[] = $ads->ID;         
     }
     $ads_post_ids_json = json_encode($ads_post_ids);
     set_transient('adsforwp_transient_ads_ids', $ads_post_ids_json);    
}
function adsforwp_update_ids_on_trash(){
     delete_transient('adsforwp_transient_ads_ids');
     adsforwp_published();         
}

function adsforwp_update_ids_on_untrash(){     
     adsforwp_published();    
}
    add_action( 'publish_adsforwp', 'adsforwp_published');
    add_action( 'trash_adsforwp', 'adsforwp_update_ids_on_trash');    
    add_action('untrash_adsforwp', 'adsforwp_update_ids_on_untrash');
    
/*
 *      Storing and updating all groups post ids in transient on different actions 
 *      which we will fetch all ids from here to display our post
 */    
function adsforwp_groups_published(){        
        $all_group_post = get_posts(
            array(
                    'post_type' 	 => 'adsforwp-groups',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
            )
        );        
     $group_post_ids = array();
     foreach($all_group_post as $group){
         $group_post_ids[] = $group->ID;         
     }
     $group_post_ids_json = json_encode($group_post_ids);
     set_transient('adsforwp_groups_transient_ids', $group_post_ids_json);    
}

function adsforwp_groups_update_ids_on_trash(){
     delete_transient('adsforwp_groups_transient_ids');
     adsforwp_groups_published();         
}

function adsforwp_groups_update_ids_on_untrash(){     
     adsforwp_groups_published();    
}
    add_action( 'publish_adsforwp-groups', 'adsforwp_groups_published');
    add_action( 'trash_adsforwp-groups', 'adsforwp_groups_update_ids_on_trash');    
    add_action('untrash_adsforwp-groups', 'adsforwp_groups_update_ids_on_untrash');    

/**
 * Here, We are displaying notice in admin panel on different different actions or conditions
 */    
function adsforwp_general_admin_notice(){
     echo '<div class="message error update-message notice notice-alt notice-error afw-blocker-notice afw_hide">'
                 . '<p>'.esc_html__('Please disable your', 'ads-for-wp').' <strong>'.esc_html__('AdBlocker', 'ads-for-wp').'</strong> '.esc_html('to use adsforwp plugin smoothly', 'ads-for-wp').'</p>'
                 . '</div>'; 
     $post_type = get_post_type();     
     if($post_type == 'adsforwp'){
            ?>
  <script type="text/javascript">  
       jQuery(document).ready( function($) {
           if ($('#adsforwp-hidden-block').length == 0 ) {
                 $(".afw-blocker-notice").show();
           }else{
                 $(".afw-blocker-notice").hide(); 
           }
       });
  </script>                
    <?php
     }
}
add_action('admin_notices', 'adsforwp_general_admin_notice');    

/**
 * Showing wordpress pointer on mouse movement  
 */
function adsforwp_print_footer_scripts() {               
?>
   <script type="text/javascript">   
   jQuery(document).ready( function($) {       
     function adsforwp_pointer(id,content, status){
       $("#"+id).pointer({
        content: content,
         position: {
            edge: 'top',
        },
        show: function(event, t){
            t.pointer.css({'right':'95px','left':'auto'});
        },
        close: function() {
            // This function is fired when you click the close button
        }
      }).pointer(status); 
   }
   function adsforwp_pointer_hover(id, status){      
       var content ='default';                        
        switch(id){
            case 'afw_adsense_pointer':
                 content = '<?php echo '<h3>'.esc_html__( 'Help', 'ads-for-wp' ).'</h3><p>'.esc_html__( 'You can find Data Client ID and Data Ad Slot from adsense code.', 'ads-for-wp' ).'</p>'; ?>';
                break;
            case 'afw_media_net_pointer':
                content = '<?php echo '<h3>'.esc_html__( 'Help', 'ads-for-wp' ).'</h3><p>'.esc_html__( 'You can find Data CID id and Data CRID from media.net code.', 'ads-for-wp' ).'</p>'; ?>';
                break;           
            default:
                break;
        }         
        adsforwp_pointer(id,content, status);        
   }  
       
    $(".afw_pointer").mouseover(function(){
        var status = 'open';
        var id = $(this).attr('id');         
        adsforwp_pointer_hover(id, status);
    });      
                
   });   
   </script>
<?php
}

