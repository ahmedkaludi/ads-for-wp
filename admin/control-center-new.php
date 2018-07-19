<?php
add_action( 'init', 'adsforwp_setup_post_type' );

function adsforwp_setup_post_type() {
    $args = array(
	    'labels' => array(
	        'name' 				=> esc_html__( 'Ads', 'ads-for-wp' ),
	        'singular_name' 	=> esc_html__( 'Ad', 'ads-for-wp' ),
	        'add_new' 			=> esc_html__( 'Add New Ad', 'ads-for-wp' ),
	        'add_new_item'  	=> esc_html__( 'Add New Ad', 'ads-for-wp' ),
                'edit_item'             => esc_html__('Edit AD','ads-for-wp')
	    ),
      	'public' 				=> true,
      	'has_archive' 			=> false,
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
add_action('admin_enqueue_scripts','adsforwp_admin_enqueue');

function adsforwp_admin_enqueue() {

	wp_enqueue_style( 'adsforwp-admin', ADSFORWP_PLUGIN_DIR_URI . 'assets/ads.css', false , ADSFORWP_VERSION );


	wp_register_script( 'adsforwp-admin-js', ADSFORWP_PLUGIN_DIR_URI . 'assets/ads.js', array('jquery'), ADSFORWP_VERSION , true );
        
	// Localize the script with new data
	$data = array(
		'ajax_url'  => admin_url( 'admin-ajax.php' ),
		'id'		=> get_the_ID()
	);
	wp_localize_script( 'adsforwp-admin-js', 'adsforwp_localize_data', $data );
	
	// Enqueued script with localized data.
	wp_enqueue_script( 'adsforwp-admin-js' );

}