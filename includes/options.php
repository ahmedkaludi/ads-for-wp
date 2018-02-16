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
		'name'    => 'Select Ads for',
		'id'      => 'select_ads_for',
		'type'    => 'select',
		'options' => array(
			'1' => __( 'AMPforWP', 'ampforwp_adsforwp' ),
			'2'   => __( 'AMP by Automattic', 'ampforwp_adsforwp' ),		),
		'default' => '1',
	) );
}
add_action( 'cmb2_admin_init', 'advanced_amp_ampforwp_ads_options' );
// /**
//  * Define the metabox and field configurations.
//  */
function advanced_amp_ampforwp_ads_options() {
	$args = array( 'post_type' => 'ads-for-wp-ads');

	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) : $loop->the_post();
	    $ads_shortcode = get_the_ID();
	endwhile; 
	$prefix = 'ampforwp_adsforwp_';

	$ampforwp_ads_option = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'AMP Ads Settings', 'cmb2' ),
		'object_types'  => array( 'ads-for-wp-ads', ),
		'classes' => 'ads-for ads-for-ampforwp' 
	) );


	$ampforwp_ads_option->add_field( array(
			'name'             => esc_html__( 'Ad Type', 'ampforwp_adsforwp' ),
			'id'               => 'ad_type_heading',
			'type'             => 'title',
			'desc' => 'Select the Ad type to display',
		) );

	$ampforwp_ads_option->add_field( array(
			'name'             => esc_html__( 'Ad Type', 'ampforwp_adsforwp' ),
			'id'               => 'ad_type_format',
			'desc' => 'Tutorial: <a href="http://ampforwp.com/tutorials/">Check all the different types of Ads?</a>',
			'type'             => 'select',
			'show_option_none' => false,
			'options'          => array(
				'1' 		 	=> esc_html__( 'Normal', 'ampforwp_adsforwp' ),
				'2'  	 		=> esc_html__( 'InContent', 'ampforwp_adsforwp' ),
				'3'    			=> esc_html__( 'Manual', 'ampforwp_adsforwp' ),
				'4'    			=> esc_html__( 'Sticky', 'ampforwp_adsforwp' ),
				'5'   			=> esc_html__( 'AMP Auto Ads', 'ampforwp_adsforwp' ),
			),
		) );
	 $ampforwp_ads_option->add_field( array(
			'name'             => esc_html__( 'Placement', 'ampforwp_adsforwp' ),
			'id'               => 'ad_type_position',
			'type'             => 'title',
			'desc' => 'Select the Position for the selected Ad',
		) );
	 $ampforwp_ads_option->add_field( array(
			'name'    			=> 'Ads Visibility',
			'id'      			=> 'ad_visibility_status',
			'type'    			=> 'radio_inline',
			'options'			 => array(
					'show' => __( 'Show', 'ampforwp_adsforwp' ),
					'hide'   => __( 'Hide', 'ampforwp_adsforwp' ),
				),
			'default' => 'show',
		) );
			 // 1. Normal    
			    $ampforwp_ads_option->add_field( array(
					'name' => esc_html__( 'Ad Positions', 'ampforwp_adsforwp' ),
					'id'   => 'normal_ad_type',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'1' 		 	=> esc_html__( 'Above Header', 'ampforwp_adsforwp' ),
						'2'  	 		=> esc_html__( 'Below Header', 'ampforwp_adsforwp' ),
						'3'    			=> esc_html__( 'Before Title', 'ampforwp_adsforwp' ),
						'4'    			=> esc_html__( 'After Title', 'ampforwp_adsforwp' ),
						'5'    			=> esc_html__( 'Before Content', 'ampforwp_adsforwp' ),
						'6'   			=> esc_html__( 'After Featured Image', 'ampforwp_adsforwp' ),
						'7'     		=> esc_html__( 'After Content', 'ampforwp_adsforwp' ),
						'8'     		=> esc_html__( 'Above Related Post', 'ampforwp_adsforwp' ),
						'9'     		=> esc_html__( 'Below Related Post', 'ampforwp_adsforwp' ),
						'10'     		=> esc_html__( 'Before Footer', 'ampforwp_adsforwp' ),
						'11'     		=> esc_html__( 'After Footer', 'ampforwp_adsforwp' ),
						'12'     		=> esc_html__( 'InBetween Loop', 'ampforwp_adsforwp' ),
					),
					'classes' => 'ad-type ad-type-1'
				) );
			   

			// 2. Incontent type        
			    $ampforwp_ads_option->add_field( array(
					'name' 	  => esc_html__( 'Show Ad After', 'ampforwp_adsforwp' ),
					'desc' 	  => 'Paragraphs',
					'id'      => 'incontent_ad_type',
					'type'    => 'text_small',
					'default' => '2',
					'classes' => 'ad-type ad-type-2'
				) );


			// 3. Manual Ads        
			    $ampforwp_ads_option->add_field( array(
					'name' => esc_html__( 'Manual', 'ampforwp_adsforwp' ),
					'desc' => 'place anywhere this shortcode',
					'id'   => 'manual_ad_type',
					'type' => 'text',
					'default' => '[ads-for-wp ads-id="'.$ads_shortcode.'"]',
					'classes' => 'ad-type ad-type-3',
					'save_field'  => false, // Otherwise CMB2 will end up removing the value.
					'attributes'  => array(
						'readonly' => 'readonly',
						'disabled' => 'disabled',
					),
				) );

			// 4. Sticky Ads       
			    $ampforwp_ads_option->add_field( array(
					'name' => esc_html__( 'Sticky Ads', 'ampforwp_adsforwp' ),
					'desc' => 'The sticky Ad will appear at the bottom of the screen.	',
					'id'   => 'sticky_ad_type',
					'type' => 'title',
					'classes' => 'ad-type ad-type-4'
				) );

			// 5. AMP Auto Ads        
			    $ampforwp_ads_option->add_field( array(
					'name' => esc_html__( 'AMP Auto Ads', 'ampforwp_adsforwp' ),
					'desc' => 'Enter your AMP Auto Ad Code',
					'id'   => 'amp_auto_ad_type',
					'type' => 'textarea_code',
					'classes' => 'ad-type ad-type-5'
				) );


	// Vendor
	$ampforwp_ads_option->add_field( array(
			'name'             => esc_html__( 'Company', 'ampforwp_adsforwp' ),
			'id'               => 'ad_company',
			'type'             => 'title',
			'desc' => 'Select the Company',
		) );
	$ampforwp_ads_option->add_field( array(
			'name'             => esc_html__( 'Ad Vendor', 'ampforwp_adsforwp' ),
			'id'               => 'ad_vendor',
			'type'             => 'select',
			'show_option_none' => false,
			'options'          => array(
				'1' 		 	=> esc_html__( 'Adsense', 'ampforwp_adsforwp' ),
				'2'  	 		=> esc_html__( 'DoubleClick', 'ampforwp_adsforwp' ),
				'3'    			=> esc_html__( 'Custom', 'ampforwp_adsforwp' ),
			),
		) );


		// Adsense Options
			$ampforwp_ads_option->add_field( array(
					'name'             => esc_html__( 'Link Ads', 'ampforwp_adsforwp' ),
					'desc'			   => 'Check this if the ad is link ads, Tutorial: <a href="http://ampforwp.com/tutorials/">What are Link Ads and where does it appear?</a>',
					'id'               => 'adsense_link',
					'type'             => 'checkbox',
					'classes'		   => 'vendor-fields adsense-data-1'
				) );
			$ampforwp_ads_option->add_field( array(
					'name'             => esc_html__( 'Link Ads Dimensions', 'ampforwp_adsforwp' ),
					'desc'			   => 'Select the preferred dimensions for your ad',
					'id'               => 'link_ads_dimensions',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'1' 		 	=> esc_html__( '120×90', 'ampforwp_adsforwp' ),
						'2'  	 		=> esc_html__( '160×90', 'ampforwp_adsforwp' ),
						'3'    			=> esc_html__( '180×90', 'ampforwp_adsforwp' ),
						'4'    			=> esc_html__( '200×90', 'ampforwp_adsforwp' ),
						'5'    			=> esc_html__( '468×15', 'ampforwp_adsforwp' ),
						'6'    			=> esc_html__( '728×15', 'ampforwp_adsforwp' ),
					),
					'classes'		   => 'link-ads-dimensions'
				) );
			$ampforwp_ads_option->add_field( array(
					'name'             => esc_html__( 'Dimensions', 'ampforwp_adsforwp' ),
					'desc'			   => 'Select the preferred dimensions for your ad',
					'id'               => 'adsense_dimensions',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'1' 		 	=> esc_html__( '300x250', 'ampforwp_adsforwp' ),
						'2'  	 		=> esc_html__( '336x280', 'ampforwp_adsforwp' ),
						'3'    			=> esc_html__( '728x90', 'ampforwp_adsforwp' ),
						'4'    			=> esc_html__( '300x600', 'ampforwp_adsforwp' ),
						'5'    			=> esc_html__( '320x100', 'ampforwp_adsforwp' ),
						'6'    			=> esc_html__( '200x50', 'ampforwp_adsforwp' ),
						'7'    			=> esc_html__( '320x50', 'ampforwp_adsforwp' ),
						'8'				=> esc_html__( 'Custom', 'ampforwp_adsforwp' ),
					),
					'classes'		   => 'vendor-fields adsense-data-1'
				) );
			$ampforwp_ads_option->add_field( array(
					'name'             => esc_html__( 'Width', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the width',
					'id'               => 'adsense_custom_width',
					'type'             => 'text_small',
					'classes'		   => 'custom-dimensions adsense-custom-data-1'
				) );
			$ampforwp_ads_option->add_field( array(
					'name'             => esc_html__( 'Height', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the height',
					'id'               => 'adsense_custom_height',
					'type'             => 'text_small',
					'classes'		   => 'custom-dimensions adsense-custom-data-1'
				) );
			$ampforwp_ads_option->add_field( array(
					'name'             => esc_html__( 'Data Ad Client', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the Data Ad Client (data-ad-client) from the adsense ad code. e.g. ca-pub-2005XXXXXXXXX342',
					'id'               => 'adsense_ad_client',
					'type'             => 'text_medium',
					'classes'		   => 'vendor-fields adsense-data-1'
				) );
			$ampforwp_ads_option->add_field( array(
					'name'             => esc_html__( 'Data Ad Slot', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the Data Ad Slot (data-ad-slot) from the adsense ad code. e.g. 70XXXXXX12',
					'id'               => 'adsense_ad_slot',
					'type'             => 'text_medium',
					'classes'		   => 'vendor-fields adsense-data-1'
				) );
			$ampforwp_ads_option->add_field( array(
					'name' => 'Parallax Effect',
					'desc' => 'AMP Flying Carpet Ad works only for the incontent Ads',
					'id'   => 'adsense_parallax',
					'type' => 'checkbox',
					'classes'		   => 'vendor-fields adsense-data-1'
				) );

		// DFP Options
			
			$ampforwp_ads_option->add_field( array(
					'name'             => esc_html__( 'Dimensions', 'ampforwp_adsforwp' ),
					'desc'			   => 'Select the preferred dimensions for your ad',
					'id'               => 'dfp_dimensions',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'1' 		 	=> esc_html__( '300x250', 'ampforwp_adsforwp' ),
						'2'  	 		=> esc_html__( '336x280', 'ampforwp_adsforwp' ),
						'3'    			=> esc_html__( '728x90', 'ampforwp_adsforwp' ),
						'4'    			=> esc_html__( '300x600', 'ampforwp_adsforwp' ),
						'5'    			=> esc_html__( '320x100', 'ampforwp_adsforwp' ),
						'6'    			=> esc_html__( '200x50', 'ampforwp_adsforwp' ),
						'7'    			=> esc_html__( '320x50', 'ampforwp_adsforwp' ),
						'8'				=> esc_html__( 'Custom', 'ampforwp_adsforwp' ),
					),
					'classes'		   => 'vendor-fields doubleclick-data-2'
				) );
			$ampforwp_ads_option->add_field( array(
					'name'             => esc_html__( 'Width', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the width',
					'id'               => 'dfp_custom_width',
					'type'             => 'text_small',
					'classes'		   => 'custom-dimensions dfp-custom-data-2'
				) );
			$ampforwp_ads_option->add_field( array(
					'name'             => esc_html__( 'Height', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the height',
					'id'               => 'dfp_custom_height',
					'type'             => 'text_small',
					'classes'		   => 'custom-dimensions dfp-custom-data-2'
				) );
			$ampforwp_ads_option->add_field( array(
					'name'             => esc_html__( 'Data Ad Slot', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the Data Ad Slot. e.g./41****9/mobile_ad_banner',
					'id'               => 'dfp_ad_slot',
					'type'             => 'text_medium',
					'classes'		   => 'vendor-fields doubleclick-data-2'
				) );
			$ampforwp_ads_option->add_field( array(
					'name' => 'Parallax Effect',
					'desc' => 'AMP Flying Carpet Ad works only for the incontent Ads',
					'id'   => 'dfp_parallax',
					'type' => 'checkbox',
					'classes'		   => 'vendor-fields doubleclick-data-2'
				) );

		// Custom Options
			$ampforwp_ads_option->add_field( array(
					'name'             => esc_html__( 'Custom', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the Custom Ad Code',
					'id'               => 'custom_ad',
					'type'             => 'textarea_code',
					'classes'		   => 'vendor-fields custom-data-3'
				) );
			$ampforwp_ads_option->add_field( array(
					'name' => 'Parallax Effect',
					'desc' => 'AMP Flying Carpet Ad works only for the incontent Ads',
					'id'   => 'custom_parallax',
					'type' => 'checkbox',
					'classes'		   => 'vendor-fields custom-data-3'
				) );
	// Optimize Ads
	$ampforwp_ads_option->add_field( array(
					'name' => 'Optimize Ads',
					'desc' => 'Optimize data through data-loading-strategy',
					'id'   => 'optimize_ads',
					'type' => 'checkbox',
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
$amp_ads_option = new_cmb2_box( array(
		'id'            => $prefix2 . 'metabox',
		'title'         => esc_html__( 'AMP Ads Settings', 'cmb2' ),
		'object_types'  => array( 'ads-for-wp-ads', ),
		'classes' => 'ads-for ads-for-amp-by-automattic' 
	) );
// Options ID's Prefix is _amp_

$amp_ads_option->add_field( array(
			'name'             => esc_html__( 'Ad Type', 'ampforwp_adsforwp' ),
			'id'               => '_amp_ad_type_heading',
			'type'             => 'title',
			'desc' => 'Select the Ad type to display',
		) );

	$amp_ads_option->add_field( array(
			'name'             => esc_html__( 'Ad Type', 'ampforwp_adsforwp' ),
			'id'               => '_amp_ad_type_format',
			'desc' => 'Tutorial: <a href="http://ampforwp.com/tutorials/">Check all the different types of Ads?</a>',
			'type'             => 'select',
			'show_option_none' => false,
			'options'          => array(
				'1' 		 	=> esc_html__( 'Normal', 'ampforwp_adsforwp' ),
				'2'  	 		=> esc_html__( 'InContent', 'ampforwp_adsforwp' ),
				'3'    			=> esc_html__( 'Manual', 'ampforwp_adsforwp' ),
				'4'    			=> esc_html__( 'Sticky', 'ampforwp_adsforwp' ),
			),
		) );
	 $amp_ads_option->add_field( array(
			'name'             => esc_html__( 'Placement', 'ampforwp_adsforwp' ),
			'id'               => '_amp_ad_type_position',
			'type'             => 'title',
			'desc' => 'Select the Position for the selected Ad',
		) );
	  $amp_ads_option->add_field( array(
			'name'    			=> 'Ads Visibility',
			'id'      			=> '_amp_ad_visibility_status',
			'type'    			=> 'radio_inline',
			'options'			 => array(
					'show' => __( 'Show', 'ampforwp_adsforwp' ),
					'hide'   => __( 'Hide', 'ampforwp_adsforwp' ),
				),
			'default' => 'show',
		) );
	 		// 1. Normal    
			    $amp_ads_option->add_field( array(
					'name' => esc_html__( 'Ad Positions', 'ampforwp_adsforwp' ),
					'id'   => '_amp_normal_ad_type',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'1'     		=> esc_html__( 'Below Footer', 'ampforwp_adsforwp' ),
					),
					'classes' => 'amp-ad-type amp-ad-type-1'
				) );
			   

			// 2. Incontent type        
			    $amp_ads_option->add_field( array(
					'name' 	  => esc_html__( 'Show Ad After', 'ampforwp_adsforwp' ),
					'desc' 	  => 'Paragraphs',
					'id'   	  => '_amp_incontent_ad_type',
					'type'    => 'text_small',
					'default' => '2',
					'classes' => 'amp-ad-type amp-ad-type-2'
				) );


			// 3. Manual Ads        
			    $amp_ads_option->add_field( array(
					'name' => esc_html__( 'Manual', 'ampforwp_adsforwp' ),
					'desc' => 'place anywhere this shortcode',
					'id'   => '_amp_manual_ad_type',
					'type' => 'text',
					'default' => '[ads-for-wp ads-id="'.$amp_ads_shortcode.'"]',
					'classes' => 'amp-ad-type amp-ad-type-3',
					'save_field'  => false, // Otherwise CMB2 will end up removing the value.
					'attributes'  => array(
						'readonly' => 'readonly',
						'disabled' => 'disabled',
					),
				) );

			// 4. Sticky Ads       
			    $amp_ads_option->add_field( array(
					'name' => esc_html__( 'Sticky Ads', 'ampforwp_adsforwp' ),
					'desc' => 'The sticky Ad will appear at the bottom of the screen.	',
					'id'   => '_amp_sticky_ad_type',
					'type' => 'title',
					'classes' => 'amp-ad-type amp-ad-type-4'
				) );

	// Vendor
	$amp_ads_option->add_field( array(
			'name'             => esc_html__( 'Company', 'ampforwp_adsforwp' ),
			'id'               => '_amp_ad_company',
			'type'             => 'title',
			'desc' => 'Select the Company',
		) );
	$amp_ads_option->add_field( array(
			'name'             => esc_html__( 'Ad Vendor', 'ampforwp_adsforwp' ),
			'id'               => '_amp_ad_vendor',
			'type'             => 'select',
			'show_option_none' => false,
			'options'          => array(
				'1' 		 	=> esc_html__( 'Adsense', 'ampforwp_adsforwp' ),
				'2'  	 		=> esc_html__( 'DoubleClick', 'ampforwp_adsforwp' ),
				'3'    			=> esc_html__( 'Custom', 'ampforwp_adsforwp' ),
			),
		) );


		// Adsense Options
			$amp_ads_option->add_field( array(
					'name'             => esc_html__( 'Link Ads', 'ampforwp_adsforwp' ),
					'desc'			   => 'Check this if the ad is link ads, Tutorial: <a href="http://ampforwp.com/tutorials/">What are Link Ads and where does it appear?</a>',
					'id'               => '_amp_adsense_link',
					'type'             => 'checkbox',
					'classes'		   => 'amp-vendor-fields amp-adsense-data-1'
				) );
			$amp_ads_option->add_field( array(
					'name'             => esc_html__( 'Link Ads Dimensions', 'ampforwp_adsforwp' ),
					'desc'			   => 'Select the preferred dimensions for your ad',
					'id'               => '_amp_link_ads_dimensions',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'1' 		 	=> esc_html__( '120×90', 'ampforwp_adsforwp' ),
						'2'  	 		=> esc_html__( '160×90', 'ampforwp_adsforwp' ),
						'3'    			=> esc_html__( '180×90', 'ampforwp_adsforwp' ),
						'4'    			=> esc_html__( '200×90', 'ampforwp_adsforwp' ),
						'5'    			=> esc_html__( '468×15', 'ampforwp_adsforwp' ),
						'6'    			=> esc_html__( '728×15', 'ampforwp_adsforwp' ),
					),
					'classes'		   => 'amp-link-ads-dimensions'
				) );
			$amp_ads_option->add_field( array(
					'name'             => esc_html__( 'Dimensions', 'ampforwp_adsforwp' ),
					'desc'			   => 'Select the preferred dimensions for your ad',
					'id'               => '_amp_adsense_dimensions',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'1' 		 	=> esc_html__( '300x250', 'ampforwp_adsforwp' ),
						'2'  	 		=> esc_html__( '336x280', 'ampforwp_adsforwp' ),
						'3'    			=> esc_html__( '728x90', 'ampforwp_adsforwp' ),
						'4'    			=> esc_html__( '300x600', 'ampforwp_adsforwp' ),
						'5'    			=> esc_html__( '320x100', 'ampforwp_adsforwp' ),
						'6'    			=> esc_html__( '200x50', 'ampforwp_adsforwp' ),
						'7'    			=> esc_html__( '320x50', 'ampforwp_adsforwp' ),
						'8'				=> esc_html__( 'Custom', 'ampforwp_adsforwp' ),
					),
					'classes'		   => 'amp-vendor-fields amp-adsense-data-1'
				) );
			$amp_ads_option->add_field( array(
					'name'             => esc_html__( 'Width', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the width',
					'id'               => '_amp_adsense_custom_width',
					'type'             => 'text_small',
					'classes'		   => 'amp-custom-dimensions amp-adsense-custom-data-1'
				) );
			$amp_ads_option->add_field( array(
					'name'             => esc_html__( 'Height', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the height',
					'id'               => '_amp_adsense_custom_height',
					'type'             => 'text_small',
					'classes'		   => 'amp-custom-dimensions amp-adsense-custom-data-1'
				) );
			$amp_ads_option->add_field( array(
					'name'             => esc_html__( 'Data Ad Client', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the Data Ad Client (data-ad-client) from the adsense ad code. e.g. ca-pub-2005XXXXXXXXX342',
					'id'               => '_amp_adsense_ad_client',
					'type'             => 'text_medium',
					'classes'		   => 'amp-vendor-fields amp-adsense-data-1'
				) );
			$amp_ads_option->add_field( array(
					'name'             => esc_html__( 'Data Ad Slot', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the Data Ad Slot (data-ad-slot) from the adsense ad code. e.g. 70XXXXXX12',
					'id'               => '_amp_adsense_ad_slot',
					'type'             => 'text_medium',
					'classes'		   => 'amp-vendor-fields amp-adsense-data-1'
				) );
			$amp_ads_option->add_field( array(
					'name' => 'Parallax Effect',
					'desc' => 'AMP Flying Carpet Ad works only for the incontent Ads',
					'id'   => '_amp_adsense_parallax',
					'type' => 'checkbox',
					'classes'		   => 'amp-vendor-fields amp-adsense-data-1'
				) );

		// DFP Options
			$amp_ads_option->add_field( array(
					'name'             => esc_html__( 'Dimensions', 'ampforwp_adsforwp' ),
					'desc'			   => 'Select the preferred dimensions for your ad',
					'id'               => '_amp_dfp_dimensions',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'1' 		 	=> esc_html__( '300x250', 'ampforwp_adsforwp' ),
						'2'  	 		=> esc_html__( '336x280', 'ampforwp_adsforwp' ),
						'3'    			=> esc_html__( '728x90', 'ampforwp_adsforwp' ),
						'4'    			=> esc_html__( '300x600', 'ampforwp_adsforwp' ),
						'5'    			=> esc_html__( '320x100', 'ampforwp_adsforwp' ),
						'6'    			=> esc_html__( '200x50', 'ampforwp_adsforwp' ),
						'7'    			=> esc_html__( '320x50', 'ampforwp_adsforwp' ),
						'8'				=> esc_html__( 'Custom', 'ampforwp_adsforwp' ),
					),
					'classes'		   => 'amp-vendor-fields amp-doubleclick-data-2'
				) );
			$amp_ads_option->add_field( array(
					'name'             => esc_html__( 'Width', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the width',
					'id'               => '_amp_dfp_custom_width',
					'type'             => 'text_small',
					'classes'		   => 'amp-custom-dimensions amp-dfp-custom-data-2'
				) );
			$amp_ads_option->add_field( array(
					'name'             => esc_html__( 'Height', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the height',
					'id'               => '_amp_dfp_custom_height',
					'type'             => 'text_small',
					'classes'		   => 'amp-custom-dimensions amp-dfp-custom-data-2'
				) );
			$amp_ads_option->add_field( array(
					'name'             => esc_html__( 'Data Ad Slot', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the Data Ad Slot. e.g./41****9/mobile_ad_banner',
					'id'               => '_amp_dfp_ad_slot',
					'type'             => 'text_medium',
					'classes'		   => 'amp-vendor-fields amp-doubleclick-data-2'
				) );
			$amp_ads_option->add_field( array(
					'name' => 'Parallax Effect',
					'desc' => 'AMP Flying Carpet Ad works only for the incontent Ads',
					'id'   => '_amp_dfp_parallax',
					'type' => 'checkbox',
					'classes'		   => 'amp-vendor-fields amp-doubleclick-data-2'
				) );

		// Custom Options
			$amp_ads_option->add_field( array(
					'name'             => esc_html__( 'Custom', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the Custom Ad Code',
					'id'               => '_amp_custom_ad',
					'type'             => 'textarea_code',
					'classes'		   => 'amp-vendor-fields amp-custom-data-3'
				) );
			$amp_ads_option->add_field( array(
					'name' => 'Parallax Effect',
					'desc' => 'AMP Flying Carpet Ad works only for the incontent Ads',
					'id'   => '_amp_custom_parallax',
					'type' => 'checkbox',
					'classes'		   => 'amp-vendor-fields amp-custom-data-3'
				) );

	// Optimize Ads
	$amp_ads_option->add_field( array(
					'name' => 'Optimize Ads',
					'desc' => 'Optimize data through data-loading-strategy',
					'id'   => '_amp_optimize_ads',
					'type' => 'checkbox',
				) );
}