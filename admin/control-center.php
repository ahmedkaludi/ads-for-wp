<?php

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
                'edit_item'             => esc_html__('Edit AD','ads-for-wp')
	    ),
      	'public' 		=> true,
      	'has_archive' 		=> false,
      	'exclude_from_search'	=> true,
    	'publicly_queryable'	=> false,
    );
    register_post_type( 'adsforwp', $args );
}
/*
 *      Hiding WYSIWYG For AMPforWP Ads 2.0, as there is no need for it 
*/
add_action( 'admin_init', 'adsforwp_removing_wysiwig' );

function adsforwp_removing_wysiwig() {
    remove_post_type_support( 'adsforwp', 'editor');   
    
}
/*
 *	Enqueue Javascript and CSS in admin area
 */
add_action('admin_enqueue_scripts','adsforwp_admin_enqueue');

function adsforwp_admin_enqueue() {

	wp_enqueue_style( 'ads-for-wp-admin', adsforwp_PLUGIN_DIR_URI . 'assets/ads.css', false , adsforwp_VERSION );
	wp_register_script( 'ads-for-wp-admin-js', adsforwp_PLUGIN_DIR_URI . 'assets/ads.js', array('jquery'), adsforwp_VERSION , true );
        // Localize the script with new data
	$data = array(
		'ajax_url'  => admin_url( 'admin-ajax.php' ),
		'id'		=> get_the_ID()
	);
	wp_localize_script( 'ads-for-wp-admin-js', 'adsforwp_localize_data', $data );	
	// Enqueued script with localized data.
	wp_enqueue_script( 'ads-for-wp-admin-js' );        
        	
}
/*
 *      storing and updating all ads post ids in transient on different actions 
 *      which we will fetch all ids from here to display our post
 */
    add_action( 'publish_adsforwp', 'adsforwp_published');
    add_action( 'trash_adsforwp', 'adsforwp_update_ids_on_trash');    
    add_action('untrash_adsforwp', 'adsforwp_update_ids_on_untrash');

function adsforwp_published(){        
        $all_ads_post = get_posts(
            array(
                    'post_type' 	 => 'adsforwp',
                    'posts_per_page' => -1,											     
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


