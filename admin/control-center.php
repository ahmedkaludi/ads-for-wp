<?php
add_action( 'init', 'ads_for_wp_setup_post_type' );

function ads_for_wp_setup_post_type() {
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
    register_post_type( 'ads-for-wp-ads', $args );
}

/*
 * Hiding WYSIWYG For AMPforWP Ads 2.0, as there is no need for it 
*/
add_action( 'admin_init', 'removing_wysiwig_adsforwp' );

function removing_wysiwig_adsforwp() {
    remove_post_type_support( 'ads-for-wp-ads', 'editor' );
}
/*
 *	Enqueue Javascript and CSS in admin area
 */
add_action('admin_enqueue_scripts','ads_for_wp_admin_enqueue');

function ads_for_wp_admin_enqueue() {

	wp_enqueue_style( 'ads-for-wp-admin', ADS_FOR_WP_PLUGIN_DIR_URI . 'assets/ads.css', false , ADS_FOR_WP_VERSION );


	wp_register_script( 'ads-for-wp-admin-js', ADS_FOR_WP_PLUGIN_DIR_URI . 'assets/ads.js', array('jquery'), ADS_FOR_WP_VERSION , true );
        
	// Localize the script with new data
	$data = array(
		'ajax_url'  => admin_url( 'admin-ajax.php' ),
		'id'		=> get_the_ID()
	);
	wp_localize_script( 'ads-for-wp-admin-js', 'ads_for_wp_localize_data', $data );
	
	// Enqueued script with localized data.
	wp_enqueue_script( 'ads-for-wp-admin-js' );

}