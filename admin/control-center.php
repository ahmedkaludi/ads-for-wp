<?php
/**
 * We are here overriding tile for adsforwp post type
 * @global type $post
 * @param string $title
 * @return string
 */
function adsforwp_modify_title( $title) {
    global $post;
    if($post->post_type =='adsforwp'){
        $adsense_auto = get_post_meta($post->ID, $key='adsense_type', true);
        if($adsense_auto === 'adsense_auto_ads'){
            $title = $title.' (Auto AdSense Ad)';
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
 *      We are registering our post type here in wordpress
 */
add_action( 'init', 'adsforwp_setup_post_type' );

function adsforwp_setup_post_type() {
    $args = array(
	    'labels' => array(
	        'name' 			=> esc_html__( 'Ads', 'ads-for-wp' ),
	        'singular_name' 	=> esc_html__( 'Ad', 'ads-for-wp' ),
	        'add_new' 		=> esc_html__( 'Add New Ad', 'ads-for-wp' ),
	        'add_new_item'  	=> esc_html__( 'Add New Ad', 'ads-for-wp' ),
                'edit_item'             => esc_html__( 'Edit AD','ads-for-wp'),                
	    ),
      	'public' 		=> true,
      	'has_archive' 		=> false,
      	'exclude_from_search'	=> true,
    	'publicly_queryable'	=> false,
    );
    register_post_type( 'adsforwp', $args );
    
    $group_post_type = array(
	    'labels' => array(
	        'name' 			=> esc_html__( 'Groups', 'ads-for-wp' ),	        
	        'add_new' 		=> esc_html__( 'Add New Groups', 'ads-for-wp' ),
	        'add_new_item'  	=> esc_html__( 'Edit Groups', 'ads-for-wp' ),
                'edit_item'             => esc_html__('Edit AD','ads-for-wp')
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

function adsforwp_modified_views_so( $views ) 
{
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
add_filter( 'manage_adsforwp-groups_posts_columns', 'adsforwp_groups_custom_columns' );
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
    $columns['adsforwp_group_column'] = '<a>'.esc_html__( 'Groups', 'ads-for-wp' ).'<a>';                            
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
        wp_register_script('adsforwp-ads-front-js', ADSFORWP_PLUGIN_DIR_URI . 'assets/ads-front.js', array( 'jquery' ), ADSFORWP_VERSION, true);
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
         wp_enqueue_style('wp-pointer');
         wp_enqueue_script('wp-pointer');
         wp_enqueue_script( 'jquery-ui-datepicker' );
         wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
         wp_enqueue_style( 'jquery-ui' );
         add_action('admin_print_footer_scripts', 'adsforwp_print_footer_scripts' );
    
        wp_enqueue_style( 'ads-for-wp-admin', ADSFORWP_PLUGIN_DIR_URI . 'assets/ads.css', false , ADSFORWP_VERSION );
        wp_register_script( 'ads-for-wp-admin-js', ADSFORWP_PLUGIN_DIR_URI . 'assets/ads.js', array('jquery'), ADSFORWP_VERSION , true );
            // Localize the script with new data
        $data = array(
            'ajax_url'  => admin_url( 'admin-ajax.php' ),
            'id'		=> get_the_ID(),
            'uploader_title'		=> 'Ad Image',
            'uploader_button'		=> 'Select'
        );
        wp_localize_script( 'ads-for-wp-admin-js', 'adsforwp_localize_data', $data );	
        // Enqueued script with localized data.
        wp_enqueue_script( 'ads-for-wp-admin-js' );        
        	
}
add_action('admin_enqueue_scripts','adsforwp_admin_enqueue');
/*
 *      storing and updating all ads post ids in transient on different actions 
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

/**
 * Showing pointer on mouse movement 
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

