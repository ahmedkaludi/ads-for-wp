<?php

// Options using CMB2

add_action('cmb2_admin_init','ads_for_options');
function ads_for_options(){
	
	$prefix = 'adsforwp_ads_for';
	$ads_for_option = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Select Ads For', 'ampforwp_adsforwp' ),
		'object_types'  => array( 'ads-for-wp-ads', ), 
	) );

	$ads_for_option->add_field( array(
		'name'    			=> esc_attr__( 'Ads For Non-AMP', 'ads-for-wp' ),
		'desc' 				=> esc_attr__( 'Currently only support for InContent Ads', 'ads-for-wp' ),
		'id'      			=> 'non_amp_ads',
		'type'    			=> 'checkbox',
	) );

	$ads_for_option->add_field( array(
		'name'    			=> esc_attr__( 'AMP Compatibility With', 'ads-for-wp' ),
		'id'      			=> 'select_ads_for',
		'type'    			=> 'select',
		'options' 			=> 
			array(
				'ampforwp' 				=> esc_attr__( 'AMPforWP', 'ads-for-wp' ),
				'amp_by_automattic'  	=> esc_attr__( 'AMP by Automattic', 'ads-for-wp' ),
			),
		'default' 			=> 'ampforwp',
	) );
}
add_action( 'cmb2_admin_init', 'advanced_amp_ampforwp_ads_options' );
function advanced_amp_ampforwp_ads_options() {
	/**
	 * Define the metabox and field configurations.
	 */
	$args = array( 'post_type' => 'ads-for-wp-ads');

	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) : $loop->the_post();
	    $ads_shortcode = get_the_ID();
	endwhile; 
	$prefix = 'ampforwp_adsforwp_';

	$ampforwp_ads_option = new_cmb2_box( array(
		'id'            	=> $prefix . 'metabox',
		'title'         	=> esc_html__( 'AMP Ads Settings', 'ads-for-wp' ),
		'object_types'  	=> array( 'ads-for-wp-ads', ),
		'classes' 			=> 'ads-for ads-for-ampforwp' 
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Ad Type', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'Select the Ad type to display', 'ads-for-wp' ),
		'id'               	=> 'ad_type_heading',
		'type'             	=> 'title',
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Ad Type', 'ads-for-wp' ),
		'desc' 				=> sprintf( __('Tutorial: ','ads-for-wp') . '<a href="%s" target="_blank">' . __( 'Check all the different types of Ads?','ads-for-wp' ) . '</a>',  __( 'https://ampforwp.com/tutorials' ) ),
		'id'               	=> 'ad_type_format',
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> 
			array(
				'1' 		=> esc_html__( 'Normal', 'ads-for-wp' ),
				'2'  	 	=> esc_html__( 'InContent', 'ads-for-wp' ),
				'3'    		=> esc_html__( 'Manual', 'ads-for-wp' ),
				'4'    		=> esc_html__( 'Sticky', 'ads-for-wp' ),
				'5'   		=> esc_html__( 'AMP Auto Ads', 'ads-for-wp' ),
			),
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Placement', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'Select the Position for the selected Ad', 'ads-for-wp' ),
		'id'               	=> 'ad_type_position',
		'type'            	=> 'title',
	) );

	$ampforwp_ads_option->add_field( array(
		'name'    			=> esc_html__( 'Ads Visibility', 'ads-for-wp' ),
		'id'      			=> 'ad_visibility_status',
		'type'    			=> 'radio_inline',
		'options'			=> 
			array(
				'show' 		=> __( 'Show', 'ads-for-wp' ),
				'hide'   	=> __( 'Hide', 'ads-for-wp' ),
			),
		'default' 			=> 'show',
	) );

	// 1. Normal    
	$ampforwp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Ad Positions', 'ads-for-wp' ),
		'id'   				=> 'normal_ad_type',
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> 
			array(
				'1' 		=> esc_html__( 'Above Header', 'ads-for-wp' ),
				'2'  	 	=> esc_html__( 'Below Header', 'ads-for-wp' ),
				'3'    		=> esc_html__( 'Before Title', 'ads-for-wp' ),
				'4'    		=> esc_html__( 'After Title', 'ads-for-wp' ),
				'5'    		=> esc_html__( 'Before Content', 'ads-for-wp' ),
				'6'   		=> esc_html__( 'After Featured Image', 'ads-for-wp' ),
				'7'     	=> esc_html__( 'After Content', 'ads-for-wp' ),
				'8'     	=> esc_html__( 'Above Related Post', 'ads-for-wp' ),
				'9'     	=> esc_html__( 'Below Related Post', 'ads-for-wp' ),
				'10'     	=> esc_html__( 'Before Footer', 'ads-for-wp' ),
				'11'     	=> esc_html__( 'After Footer', 'ads-for-wp' ),
				'12'     	=> esc_html__( 'InBetween Loop', 'ads-for-wp' ),
				'13'     	=> esc_html__( 'InBetween Related Posts', 'ads-for-wp' ),
			),
		'classes' 			=> 'ad-type ad-type-1'
	) );

	// 2. Incontent type        
	$ampforwp_ads_option->add_field( array(
		'name' 	  			=> esc_html__( 'Show Ad After', 'ads-for-wp' ),
		'desc' 	  			=> esc_html__( 'Paragraphs', 'ads-for-wp' ),
		'id'      			=> 'incontent_ad_type',
		'type'    			=> 'text_small',
		'default' 			=> '2',
		'classes' 			=> 'ad-type ad-type-2',	
		'attributes' 		=> array(
			'type' 			=> 'number',
			'min'  			=> '1',
		),


	) );

	// 3. Manual Ads        
	$ampforwp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Manual', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'Place anywhere this shortcode', 'ads-for-wp' ),
		'id'   				=> 'manual_ad_type',
		'type' 				=> 'text',
		'classes'			=> 'ad-type ad-type-3',
		'save_field'  		=> false, // Otherwise CMB2 will end up removing the value.
		'attributes'  		=> 
			array(
				'readonly' 	=> 'readonly',
				'disabled' 	=> 'disabled',
			),
	) );

	// 4. Sticky Ads       
	$ampforwp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Sticky Ads', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'The sticky Ad will appear at the bottom of the screen.', 'ads-for-wp' ),
		'id'   				=> 'sticky_ad_type',
		'type' 				=> 'title',
		'classes' 			=> 'ad-type ad-type-4'
	) );

	// 5. AMP Auto Ads        
	$ampforwp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'AMP Auto Ads', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'Enter your AMP Auto Ad Code', 'ads-for-wp' ),
		'id'   				=> 'amp_auto_ad_type',
		'type' 				=> 'textarea_code',
		'classes' 			=> 'ad-type ad-type-5'
	) );

	// Vendor
	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Company', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'Select the Company', 'ads-for-wp' ),
		'id'               	=> 'ad_company',
		'type'             	=> 'title',
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Ad Vendor', 'ads-for-wp' ),
		'id'               	=> 'ad_vendor',
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> 
			array(
				'1' 		=> esc_html__( 'Adsense', 'ads-for-wp' ),
				'2'  	 	=> esc_html__( 'DoubleClick', 'ads-for-wp' ),
				'3'    		=> esc_html__( 'Custom', 'ads-for-wp' ),
				'4'    		=> esc_html__( 'Medianet', 'ads-for-wp' ),
			),
	) );

	// Adsense Options
	$ampforwp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Responsive Ad', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'This will make your Ad responsive', 'ads-for-wp' ),
		'id'   				=> 'adsense_responsive',
		'type' 				=> 'checkbox',
		'classes'		   	=> 'vendor-fields adsense-data-1'
	) );
	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Link Ads', 'ads-for-wp' ),
		'desc'			   	=> sprintf( __('Check this if the ad is link ads, Tutorial: ','ads-for-wp') . '<a href="%s" target="_blank">' . __( 'What are Link Ads and where does it appear?','ads-for-wp' ) . '</a>',  __( 'https://ampforwp.com/tutorials' ) ),
		'id'               	=> 'adsense_link',
		'type'             	=> 'checkbox',
		'classes'		   	=> 'vendor-fields adsense-data-1'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Link Ads Dimensions', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Select the preferred dimensions for your ad', 'ads-for-wp' ),
		'id'               	=> 'link_ads_dimensions',
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> 
			array(
				'1' 		=> esc_html__( '120×90', 'ads-for-wp' ),
				'2'  	 	=> esc_html__( '160×90', 'ads-for-wp' ),
				'3'    		=> esc_html__( '180×90', 'ads-for-wp' ),
				'4'    		=> esc_html__( '200×90', 'ads-for-wp' ),
				'5'    		=> esc_html__( '468×15', 'ads-for-wp' ),
				'6'    		=> esc_html__( '728×15', 'ads-for-wp' ),
				'7'    		=> esc_html__( 'Custom', 'ads-for-wp' ),
			),
		'classes'		   	=> 'link-ads-dimensions'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Width', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the width', 'ads-for-wp' ),
		'id'               	=> 'link_custom_width',
		'type'             	=> 'text_small',
		'classes'		   	=> 'link-custom-dimensions link-custom-data-1'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'            	=> esc_html__( 'Height', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the height', 'ads-for-wp' ),
		'id'               	=> 'link_custom_height',
		'type'             	=> 'text_small',
		'classes'		   	=> 'link-custom-dimensions link-custom-data-1'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Dimensions', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Select the preferred dimensions for your ad', 'ads-for-wp' ),
		'id'               	=> 'adsense_dimensions',
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> 
			array(
				'1' 		=> esc_html__( '300x250', 'ads-for-wp' ),
				'2'  	 	=> esc_html__( '336x280', 'ads-for-wp' ),
				'3'    		=> esc_html__( '728x90', 'ads-for-wp' ),
				'4'    		=> esc_html__( '300x600', 'ads-for-wp' ),
				'5'    		=> esc_html__( '320x100', 'ads-for-wp' ),
				'6'    		=> esc_html__( '200x50', 'ads-for-wp' ),
				'7'    		=> esc_html__( '320x50', 'ads-for-wp' ),
				'8'			=> esc_html__( 'Custom', 'ads-for-wp' ),
			),
		'classes'		   	=> 'vendor-fields adsense-data-1'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Width', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the width', 'ads-for-wp' ),
		'id'               	=> 'adsense_custom_width',
		'type'             	=> 'text_small',
		'classes'		   	=> 'custom-dimensions adsense-custom-data-1'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Height', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the height', 'ads-for-wp' ),
		'id'               	=> 'adsense_custom_height',
		'type'             	=> 'text_small',
		'classes'		   	=> 'custom-dimensions adsense-custom-data-1'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Data Ad Client', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the Data Ad Client (data-ad-client) from the adsense ad code. e.g. ca-pub-2005XXXXXXXXX342', 'ads-for-wp' ),
		'id'               	=> 'adsense_ad_client',
		'type'             	=> 'text_medium',
		'classes'		   	=> 'vendor-fields adsense-data-1'
	) );

	$ampforwp_ads_option->add_field( array(
	'name'             		=> esc_html__( 'Data Ad Slot', 'ads-for-wp' ),
	'desc'			   		=> esc_html__( 'Enter the Data Ad Slot (data-ad-slot) from the adsense ad code. e.g. 70XXXXXX12', 'ads-for-wp' ),
	'id'               		=> 'adsense_ad_slot',
	'type'             		=> 'text_medium',
	'classes'		   		=> 'vendor-fields adsense-data-1'
	) );

	$ampforwp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Parallax Effect', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'AMP Flying Carpet Ad works only for the incontent Ads', 'ads-for-wp' ),
		'id'   				=> 'adsense_parallax',
		'type' 				=> 'checkbox',
		'classes'		   	=> 'vendor-fields adsense-data-1'
	) );

	// DFP Options
	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Dimensions', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Select the preferred dimensions for your ad', 'ads-for-wp' ),
		'id'               	=> 'dfp_dimensions',
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> 
			array(
				'1' 		=> esc_html__( '300x250', 'ads-for-wp' ),
				'2'  	 	=> esc_html__( '336x280', 'ads-for-wp' ),
				'3'    		=> esc_html__( '728x90', 'ads-for-wp' ),
				'4'    		=> esc_html__( '300x600', 'ads-for-wp' ),
				'5'    		=> esc_html__( '320x100', 'ads-for-wp' ),
				'6'    		=> esc_html__( '200x50', 'ads-for-wp' ),
				'7'    		=> esc_html__( '320x50', 'ads-for-wp' ),
				'8'			=> esc_html__( 'Custom', 'ads-for-wp' ),
			),
		'classes'		   	=> 'vendor-fields doubleclick-data-2'
	) );
	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Width', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the width', 'ads-for-wp' ),
		'id'               	=> 'dfp_custom_width',
		'type'             	=> 'text_small',
		'classes'		   	=> 'custom-dimensions dfp-custom-data-2'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Height', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the height', 'ads-for-wp' ),
		'id'               	=> 'dfp_custom_height',
		'type'            	=> 'text_small',
		'classes'		   	=> 'custom-dimensions dfp-custom-data-2'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Data Ad Slot', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the Data Ad Slot. e.g./41****9/mobile_ad_banner', 'ads-for-wp' ),
		'id'               	=> 'dfp_ad_slot',
		'type'             	=> 'text_medium',
		'classes'		   	=> 'vendor-fields doubleclick-data-2'
	) );

	$ampforwp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Parallax Effect', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'AMP Flying Carpet Ad works only for the incontent Ads', 'ads-for-wp' ),
		'id'   				=> 'dfp_parallax',
		'type' 				=> 'checkbox',
		'classes'		   	=> 'vendor-fields doubleclick-data-2'
	) );

	// Custom Options
	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Custom', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the Custom Ad Code', 'ads-for-wp' ),
		'id'               	=> 'custom_ad',
		'type'             	=> 'textarea_code',
		'classes'		   	=> 'vendor-fields custom-data-3'
	) );

	$ampforwp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Parallax Effect', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'AMP Flying Carpet Ad works only for the incontent Ads', 'ads-for-wp' ),
		'id'   				=> 'custom_parallax',
		'type' 				=> 'checkbox',
		'classes'		    => 'vendor-fields custom-data-3'
	) );

	// Media.net Options
	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Dimensions', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Select the preferred dimensions for your ad', 'ads-for-wp' ),
		'id'               	=> 'medianet_dimensions',
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> 
			array(
				'1' 		=> esc_html__( '300x250', 'ads-for-wp' ),
				'2'  	 	=> esc_html__( '336x280', 'ads-for-wp' ),
				'3'    		=> esc_html__( '728x90', 'ads-for-wp' ),
				'4'    		=> esc_html__( '300x600', 'ads-for-wp' ),
				'5'    		=> esc_html__( '320x100', 'ads-for-wp' ),
				'6'    		=> esc_html__( '200x50', 'ads-for-wp' ),
				'7'    		=> esc_html__( '320x50', 'ads-for-wp' ),
				'8'			=> esc_html__( 'Custom', 'ads-for-wp' ),
			),
		'classes'		   	=> 'vendor-fields medianet-data-4'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Width', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the width', 'ads-for-wp' ),
		'id'               	=> 'medianet_custom_width',
		'type'             	=> 'text_small',
		'classes'		   	=> 'custom-dimensions medianet-custom-data-4'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Height', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the height', 'ads-for-wp' ),
		'id'               	=> 'medianet_custom_height',
		'type'             	=> 'text_small',
		'classes'		   	=> 'custom-dimensions medianet-custom-data-4'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Customer Identifier', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the unique customer identifier (data-cid) from the media.net ad code. e.g. 8CUS8O7EX', 'ads-for-wp' ),
		'id'               	=> 'medianet_ad_client',
		'type'             	=> 'text_medium',
		'classes'		   	=> 'vendor-fields medianet-data-4'
	) );

	$ampforwp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Media.net Ad unit', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the Media.net Ad unit (data-crid) from the media.net ad code. e.g. 112682482', 'ads-for-wp' ),
		'id'               	=> 'medianet_ad_slot',
		'type'             	=> 'text_medium',
		'classes'		   	=> 'vendor-fields medianet-data-4'
	) );

	$ampforwp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Parallax Effect', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'AMP Flying Carpet Ad works only for the incontent Ads', 'ads-for-wp' ),
		'id'   				=> 'medianet_parallax',
		'type' 				=> 'checkbox',
		'classes'		   	=> 'vendor-fields medianet-data-4'
	) );

	// Optimize Ads
	$ampforwp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Optimize Ads', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'Optimize data through data-loading-strategy', 'ads-for-wp' ),
		'id'   				=> 'optimize_ads',
		'type' 				=> 'checkbox',
	) );
}

/**
 *
 *OPTIONS FOR AMP BY AUTOMATTIC
 *
 * 
 */
add_action('cmb2_admin_init','amp_by_automattic_options');
function amp_by_automattic_options(){
		
	$args = array( 'post_type' => 'ads-for-wp-ads');
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) : $loop->the_post();
		$amp_ads_shortcode = get_the_ID();
	endwhile; 
	$prefix2 = 'ampforwp_adsforwp_for_amp_';


	/*
	* Options
	*/
	$amp_ads_option = new_cmb2_box( array(
		'id'            	=> $prefix2 . 'metabox',
		'title'         	=> esc_html__( 'AMP Ads Settings', 'ads-for-wp' ),
		'object_types'  	=> array( 'ads-for-wp-ads', ),
		'classes' 			=> 'ads-for ads-for-amp-by-automattic' 
	) );

	// Options ID's Prefix is _amp_
	$amp_ads_option->add_field( array(
		'name'          	=> esc_html__( 'Ad Type', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'Select the Ad type to display', 'ads-for-wp' ),
		'id'            	=> '_amp_ad_type_heading',
		'type'          	=> 'title',
	) );

	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Ad Type', 'ads-for-wp' ),
		'id'               	=> '_amp_ad_type_format',
		'desc' 				=> sprintf( __('Tutorial: ','ads-for-wp') . '<a href="%s" target="_blank">' . __( 'Check all the different types of Ads?','ads-for-wp' ) . '</a>',  __( 'http://ampforwp.com/tutorials/' ) ),
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> 
			array(
				'1' 		=> esc_html__( 'Normal', 'ads-for-wp' ),
				'2'  		=> esc_html__( 'InContent', 'ads-for-wp' ),
				'3'    		=> esc_html__( 'Manual', 'ads-for-wp' ),
				'4'    		=> esc_html__( 'Sticky', 'ads-for-wp' ),
			),
	) );

	$amp_ads_option->add_field( array(
		'name'          	=> esc_html__( 'Placement', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'Select the Position for the selected Ad', 'ads-for-wp' ),
		'id'            	=> '_amp_ad_type_position',
		'type'          	=> 'title',
	) );

	$amp_ads_option->add_field( array(
		'name'    			=> esc_html__( 'Ads Visibility', 'ads-for-wp' ),
		'id'      			=> '_amp_ad_visibility_status',
		'type'    			=> 'radio_inline',
		'options'			=> 
			array(
				'show' 		=> esc_html__( 'Show', 'ads-for-wp' ),
				'hide' 		=> esc_html__( 'Hide', 'ads-for-wp' ),
			),
		'default' 			=> 'show',
	) );

	// 1. Normal    
	$amp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Ad Positions', 'ads-for-wp' ),
		'id'   				=> '_amp_normal_ad_type',
		'type'             	=> 'select',
		'classes' 			=> 'amp-ad-type amp-ad-type-1',
		'show_option_none' 	=> false,
		'options'          	=> array(
			'1'     		=> esc_html__( 'Below Footer', 'ads-for-wp' ),
		),
	) );

	// 2. Incontent type        
	$amp_ads_option->add_field( array(
		'name' 	  			=> esc_html__( 'Show Ad After', 'ads-for-wp' ),
		'desc' 	  			=> esc_html__( 'Paragraphs', 'ads-for-wp' ),
		'id'   	  			=> '_amp_incontent_ad_type',
		'type'    			=> 'text_small',
		'default' 			=> '2',
		'classes' 			=> 'amp-ad-type amp-ad-type-2',	
		'attributes' 		=> array(
			'type' 			=> 'number',
			'min'  			=> '1',
		),
	) );


	// 3. Manual Ads        
	$amp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Manual', 'ampforwp_adsforwp' ),
		'desc' 				=> esc_html__( 'Place anywhere this shortcode', 'ads-for-wp' ),
		'id'   				=> '_amp_manual_ad_type',
		'type' 				=> 'text',
		'classes' 			=> 'amp-ad-type amp-ad-type-3',
		'save_field'  		=> false, 
		'attributes'  		=> 
			array(
				'readonly' 	=> 'readonly',
				'disabled' 	=> 'disabled',
			),
	) );

	// 4. Sticky Ads       
	$amp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Sticky Ads', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'The sticky Ad will appear at the bottom of the screen.', 'ads-for-wp' ),
		'id'   				=> '_amp_sticky_ad_type',
		'type' 				=> 'title',
		'classes' 			=> 'amp-ad-type amp-ad-type-4'
	) );

	// Vendor
	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Company', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'Select the Company', 'ads-for-wp' ),
		'id'               	=> '_amp_ad_company',
		'type'             	=> 'title',
	) );

	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Ad Vendor', 'ads-for-wp' ),
		'id'               	=> '_amp_ad_vendor',
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> 
			array(
				'1' 		=> esc_html__( 'Adsense', 'ads-for-wp' ),
				'2'  	 	=> esc_html__( 'DoubleClick', 'ads-for-wp' ),
				'3'    		=> esc_html__( 'Custom', 'ads-for-wp' ),
				'4'			=> esc_html__( 'Medianet', 'ads-for-wp'),
			),
	) );

	// Adsense Options			
	$amp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Responsive Ad', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'This will make your Ad responsive', 'ads-for-wp' ),
		'id'   				=> '_amp_adsense_responsive',
		'type' 				=> 'checkbox',
		'classes'			=> 'amp-vendor-fields amp-adsense-data-1'
	) );

	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Link Ads', 'ads-for-wp' ),
		'desc'			   	=> sprintf( __('Check this if the ad is link ads, Tutorial:','ads-for-wp') . '<a href="%s" target="_blank">' . __( 'What are Link Ads and where does it appear?','ads-for-wp' ) . '</a>',  __( 'http://ampforwp.com/tutorials/' ) ),
		'id'               	=> '_amp_adsense_link',
		'type'             	=> 'checkbox',
		'classes'		   	=> 'amp-vendor-fields amp-adsense-data-1'
	) );

	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Link Ads Dimensions', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Select the preferred dimensions for your ad', 'ads-for-wp' ),
		'id'               	=> '_amp_link_ads_dimensions',
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> array(
			'1' 		 	=> esc_html__( '120×90', 'ads-for-wp' ),
			'2'  	 		=> esc_html__( '160×90', 'ads-for-wp' ),
			'3'    			=> esc_html__( '180×90', 'ads-for-wp' ),
			'4'    			=> esc_html__( '200×90', 'ads-for-wp' ),
			'5'    			=> esc_html__( '468×15', 'ads-for-wp' ),
			'6'    			=> esc_html__( '728×15', 'ads-for-wp' ),
			'7'    			=> esc_html__( 'Custom', 'ads-for-wp' ),
		),
		'classes'		   	=> 'amp-link-ads-dimensions'
	) );
	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Width', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the width', 'ads-for-wp' ),
		'id'               	=> '_amp_link_custom_width',
		'type'             	=> 'text_small',
		'classes'		   	=> 'amp-link-custom-dimensions amp-link-custom-data-1'
	) );
	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Height', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the height', 'ads-for-wp' ),
		'id'               	=> '_amp_link_custom_height',
		'type'             	=> 'text_small',
		'classes'		   	=> 'amp-link-custom-dimensions amp-link-custom-data-1'
	) );
	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Dimensions', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Select the preferred dimensions for your ad', 'ads-for-wp' ),
		'id'               	=> '_amp_adsense_dimensions',
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> 
			array(
				'1' 		=> esc_html__( '300x250', 'ads-for-wp' ),
				'2'  	 	=> esc_html__( '336x280', 'ads-for-wp' ),
				'3'    		=> esc_html__( '728x90', 'ads-for-wp' ),
				'4'    		=> esc_html__( '300x600', 'ads-for-wp' ),
				'5'    		=> esc_html__( '320x100', 'ads-for-wp' ),
				'6'    		=> esc_html__( '200x50', 'ads-for-wp' ),
				'7'    		=> esc_html__( '320x50', 'ads-for-wp' ),
				'8'			=> esc_html__( 'Custom', 'ads-for-wp' ),
			),
		'classes'		   	=> 'amp-vendor-fields amp-adsense-data-1'
	) );

	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Width', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the width', 'ads-for-wp' ),
		'id'               	=> '_amp_adsense_custom_width',
		'type'             	=> 'text_small',
		'classes'		   	=> 'amp-custom-dimensions amp-adsense-custom-data-1'
	) );

	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Height', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the height', 'ads-for-wp' ),
		'id'               	=> '_amp_adsense_custom_height',
		'type'             	=> 'text_small',
		'classes'		   	=> 'amp-custom-dimensions amp-adsense-custom-data-1'
	) );

	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Data Ad Client', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the Data Ad Client (data-ad-client) from the adsense ad code. e.g. ca-pub-2005XXXXXXXXX342', 'ads-for-wp' ),
		'id'               	=> '_amp_adsense_ad_client',
		'type'             	=> 'text_medium',
		'classes'		   	=> 'amp-vendor-fields amp-adsense-data-1'
	) );

	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Data Ad Slot', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the Data Ad Slot (data-ad-slot) from the adsense ad code. e.g. 70XXXXXX12', 'ads-for-wp' ),
		'id'               	=> '_amp_adsense_ad_slot',
		'type'             	=> 'text_medium',
		'classes'		   	=> 'amp-vendor-fields amp-adsense-data-1'
	) );

	$amp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Parallax Effect', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'AMP Flying Carpet Ad works only for the incontent Ads', 'ads-for-wp' ),
		'id'   				=> '_amp_adsense_parallax',
		'type' 				=> 'checkbox',
		'classes'		   	=> 'amp-vendor-fields amp-adsense-data-1'
	) );

	// DFP Options
	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Dimensions', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Select the preferred dimensions for your ad', 'ads-for-wp' ),
		'id'               	=> '_amp_dfp_dimensions',
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> 
			array(
				'1' 		=> esc_html__( '300x250', 'ads-for-wp' ),
				'2'  	 	=> esc_html__( '336x280', 'ads-for-wp' ),
				'3'    		=> esc_html__( '728x90', 'ads-for-wp' ),
				'4'    		=> esc_html__( '300x600', 'ads-for-wp' ),
				'5'    		=> esc_html__( '320x100', 'ads-for-wp' ),
				'6'    		=> esc_html__( '200x50', 'ads-for-wp' ),
				'7'    		=> esc_html__( '320x50', 'ads-for-wp' ),
				'8'			=> esc_html__( 'Custom', 'ads-for-wp' ),
			),
		'classes'		   	=> 'amp-vendor-fields amp-doubleclick-data-2'
	) );

	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Width', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the width', 'ads-for-wp' ),
		'id'               	=> '_amp_dfp_custom_width',
		'type'             	=> 'text_small',
		'classes'		   	=> 'amp-custom-dimensions amp-dfp-custom-data-2'
	) );

	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Height', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the height', 'ads-for-wp' ),
		'id'               	=> '_amp_dfp_custom_height',
		'type'            	=> 'text_small',
		'classes'		  	=> 'amp-custom-dimensions amp-dfp-custom-data-2'
	) );

	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Data Ad Slot', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the Data Ad Slot. e.g./41****9/mobile_ad_banner', 'ads-for-wp' ),
		'id'               	=> '_amp_dfp_ad_slot',
		'type'             	=> 'text_medium',
		'classes'		   	=> 'amp-vendor-fields amp-doubleclick-data-2'
	) );
	$amp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Parallax Effect', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'AMP Flying Carpet Ad works only for the incontent Ads', 'ads-for-wp' ),
		'id'   				=> '_amp_dfp_parallax',
		'type' 				=> 'checkbox',
		'classes'		   	=> 'amp-vendor-fields amp-doubleclick-data-2'
	) );

	// Custom Options
	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Custom', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the Custom Ad Code', 'ads-for-wp' ),
		'id'               	=> '_amp_custom_ad',
		'type'             	=> 'textarea_code',
		'classes'		   	=> 'amp-vendor-fields amp-custom-data-3'
	) );
	$amp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Parallax Effect', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'AMP Flying Carpet Ad works only for the incontent Ads', 'ads-for-wp' ),
		'id'   				=> '_amp_custom_parallax',
		'type' 				=> 'checkbox',
		'classes'		   	=> 'amp-vendor-fields amp-custom-data-3'
	) );

	// Media.net Options
	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Dimensions', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Select the preferred dimensions for your ad', 'ads-for-wp' ),
		'id'               	=> '_amp_medianet_dimensions',
		'type'             	=> 'select',
		'show_option_none' 	=> false,
		'options'          	=> 
			array(
				'1' 		=> esc_html__( '300x250', 'ads-for-wp' ),
				'2'  	 	=> esc_html__( '336x280', 'ads-for-wp' ),
				'3'    		=> esc_html__( '728x90', 'ads-for-wp' ),
				'4'    		=> esc_html__( '300x600', 'ads-for-wp' ),
				'5'    		=> esc_html__( '320x100', 'ads-for-wp' ),
				'6'    		=> esc_html__( '200x50', 'ads-for-wp' ),
				'7'    		=> esc_html__( '320x50', 'ads-for-wp' ),
				'8'			=> esc_html__( 'Custom', 'ads-for-wp' ),
			),
		'classes'		   	=> 'amp-vendor-fields amp-medianet-data-4'
	) );
	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Width', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the width', 'ads-for-wp' ),
		'id'               	=> '_amp_medianet_custom_width',
		'type'             	=> 'text_small',
		'classes'		   	=> 'amp-custom-dimensions amp-medianet-custom-data-4'
	) );
	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Height', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the height', 'ads-for-wp' ),
		'id'               	=> '_amp_medianet_custom_height',
		'type'             	=> 'text_small',
		'classes'		   	=> 'amp-custom-dimensions amp-medianet-custom-data-4'
	) );
	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Customer Identifier', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the unique customer identifier (data-cid) from the media.net ad code. e.g. 8CUS8O7EX', 'ads-for-wp' ),
		'id'               	=> '_amp_medianet_ad_client',
		'type'             	=> 'text_medium',
		'classes'		   	=> 'amp-vendor-fields amp-medianet-data-4'
	) );
	$amp_ads_option->add_field( array(
		'name'             	=> esc_html__( 'Media.net Ad unit', 'ads-for-wp' ),
		'desc'			   	=> esc_html__( 'Enter the Media.net Ad unit (data-crid) from the media.net ad code. e.g. 112682482', 'ads-for-wp' ),
		'id'               	=> '_amp_medianet_ad_slot',
		'type'             	=> 'text_medium',
		'classes'		   	=> 'amp-vendor-fields amp-medianet-data-4'
	) );
	$amp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Parallax Effect', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'AMP Flying Carpet Ad works only for the incontent Ads', 'ads-for-wp' ),
		'id'   				=> '_amp_medianet_parallax',
		'type' 				=> 'checkbox',
		'classes'			=> 'amp-vendor-fields amp-medianet-data-4'
	) );

	// Optimize Ads
	$amp_ads_option->add_field( array(
		'name' 				=> esc_html__( 'Optimize Ads', 'ads-for-wp' ),
		'desc' 				=> esc_html__( 'Optimize data through data-loading-strategy', 'ads-for-wp' ),
		'id'   				=> '_amp_optimize_ads',
		'type' 				=> 'checkbox',
	) );
}