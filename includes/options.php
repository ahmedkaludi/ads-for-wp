<?php

// Options using CMB2


add_action( 'cmb2_admin_init', 'advanced_amp_ads_options' );
// /**
//  * Define the metabox and field configurations.
//  */
function advanced_amp_ads_options() {
	$args = array( 'post_type' => 'ads-for-wp-ads');

	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) : $loop->the_post();
	    $ads_shortcode = get_the_ID();
	endwhile; 
	$prefix = 'ampforwp_adsforwp_';

	$ads_option = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'AMP Ads Settings', 'cmb2' ),
		'object_types'  => array( 'ads-for-wp-ads', ), 
	) );


	$ads_option->add_field( array(
			'name'             => esc_html__( 'Ad Type', 'ampforwp_adsforwp' ),
			'id'               => 'ad_type_heading',
			'type'             => 'title',
			'desc' => 'Select the Ad type to display',
		) );

	$ads_option->add_field( array(
			'name'             => esc_html__( 'Ad Type', 'ampforwp_adsforwp' ),
			'id'               => 'ad_type_format',
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
	 $ads_option->add_field( array(
					'desc' => 'Tutorial: <a href="http://ampforwp.com/tutorials/">What are Normal Ads and where does it appear?</a>',
					'type' => 'title',
					'id'   => 'normal_ads_tutorial',
					'classes' => 'ad-type ad-type-1' 
				) );
	 $ads_option->add_field( array(
					'desc' => 'Tutorial: <a href="http://ampforwp.com/tutorials/">What are Incontent Ads and where does it appear?</a>',
					'type' => 'title',
					'id'   => 'incontent_ads_tutorial',
					'classes' => 'ad-type ad-type-2' 
				) );
	 $ads_option->add_field( array(
					'desc' => 'Tutorial: <a href="http://ampforwp.com/tutorials/">What are Manual Ads and where does it appear?</a>',
					'type' => 'title',
					'id'   => 'manual_ads_tutorial',
					'classes' => 'ad-type ad-type-3' 
				) );
	 $ads_option->add_field( array(
					'desc' => 'Tutorial: <a href="http://ampforwp.com/tutorials/">What are Sticky Ads and where does it appear?</a>',
					'type' => 'title',
					'id'   => 'sticky_ads_tutorial',
					'classes' => 'ad-type ad-type-4' 
				) );
	 $ads_option->add_field( array(
					'desc' => 'Tutorial: <a href="http://ampforwp.com/tutorials/">What are Auto AMP Ads and where does it appear?</a>',
					'type' => 'title',
					'id'   => 'auto_amp_ads_tutorial',
					'classes' => 'ad-type ad-type-5' 
				) );
	 $ads_option->add_field( array(
					'desc' => 'Tutorial: <a href="http://ampforwp.com/tutorials/">What are Link Ads and where does it appear?</a>',
					'type' => 'title',
					'id'   => 'link_ads_tutorial',
					'classes' => 'ad-type ad-type-6' 
				) );
	 $ads_option->add_field( array(
			'name'             => esc_html__( 'Positions', 'ampforwp_adsforwp' ),
			'id'               => 'ad_type_position',
			'type'             => 'title',
			'desc' => 'Select the Position for the selected Ad',
		) );
			 // 1. Normal    
			    $ads_option->add_field( array(
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
						'6'   			=> esc_html__( 'After Content', 'ampforwp_adsforwp' ),
						'7'     		=> esc_html__( 'Before Footer', 'ampforwp_adsforwp' ),
						'8'     		=> esc_html__( 'After Footer', 'ampforwp_adsforwp' ),
					),
					'classes' => 'ad-type ad-type-1'
				) );
			   

			// 2. Incontent type        
			    $ads_option->add_field( array(
					'name' => esc_html__( 'Ad Positions', 'ampforwp_adsforwp' ),
					'id'   => 'incontent_ad_type',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'1' 		 	=> esc_html__( 'Paragraph 1', 'ampforwp_adsforwp' ),
						'2'  	 		=> esc_html__( 'Paragraph 2', 'ampforwp_adsforwp' ),
						'3'    			=> esc_html__( 'Paragraph 3', 'ampforwp_adsforwp' ),
						'4'    			=> esc_html__( 'Paragraph 4', 'ampforwp_adsforwp' ),
						'5'   			=> esc_html__( 'Paragraph 5', 'ampforwp_adsforwp' ),
						'6'     		=> esc_html__( 'Paragraph 6', 'ampforwp_adsforwp' ),
					),
					'classes' => 'ad-type ad-type-2'
				) );


			// 3. Manual Ads        
			    $ads_option->add_field( array(
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
			    $ads_option->add_field( array(
					'name' => esc_html__( 'Sticky Ads', 'ampforwp_adsforwp' ),
					'desc' => 'The sticky Ad will appear at the bottom of the screen.	',
					'id'   => 'sticky_ad_type',
					'type' => 'title',
					'classes' => 'ad-type ad-type-4'
				) );

			// 5. AMP Auto Ads        
			    $ads_option->add_field( array(
					'name' => esc_html__( 'AMP Auto Ads', 'ampforwp_adsforwp' ),
					'desc' => 'Enter your AMP Auto Ad Code',
					'id'   => 'amp_auto_ad_type',
					'type' => 'textarea_code',
					'classes' => 'ad-type ad-type-5'
				) );


	// Vendor
	$ads_option->add_field( array(
			'name'             => esc_html__( 'Company', 'ampforwp_adsforwp' ),
			'id'               => 'ad_company',
			'type'             => 'title',
			'desc' => 'Select the Company',
		) );
	$ads_option->add_field( array(
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
			$ads_option->add_field( array(
					'name'             => esc_html__( 'Link Ads', 'ampforwp_adsforwp' ),
					'desc'			   => 'Check this if the ad is link ads',
					'id'               => 'adsense_link',
					'type'             => 'checkbox',
					'classes'		   => 'vendor-fields adsense-data-1'
				) );
			$ads_option->add_field( array(
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
			$ads_option->add_field( array(
					'name'             => esc_html__( 'Width', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the width',
					'id'               => 'adsense_custom_width',
					'type'             => 'text_small',
					'classes'		   => 'custom-dimensions adsense-custom-data-1'
				) );
			$ads_option->add_field( array(
					'name'             => esc_html__( 'Height', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the height',
					'id'               => 'adsense_custom_height',
					'type'             => 'text_small',
					'classes'		   => 'custom-dimensions adsense-custom-data-1'
				) );
			$ads_option->add_field( array(
					'name'             => esc_html__( 'Data Ad Client', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the Data Ad Client (data-ad-client) from the adsense ad code. e.g. ca-pub-2005XXXXXXXXX342',
					'id'               => 'adsense_ad_client',
					'type'             => 'text_medium',
					'classes'		   => 'vendor-fields adsense-data-1'
				) );
			$ads_option->add_field( array(
					'name'             => esc_html__( 'Data Ad Slot', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the Data Ad Slot (data-ad-slot) from the adsense ad code. e.g. 70XXXXXX12',
					'id'               => 'adsense_ad_slot',
					'type'             => 'text_medium',
					'classes'		   => 'vendor-fields adsense-data-1'
				) );
			$ads_option->add_field( array(
					'name' => 'Parallax Effect',
					'desc' => 'AMP Flying Carpet Ad works only for the incontent Ads',
					'id'   => 'adsense_parallax',
					'type' => 'checkbox',
					'classes'		   => 'vendor-fields adsense-data-1'
				) );

		// DFP Options
			
			$ads_option->add_field( array(
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
			$ads_option->add_field( array(
					'name'             => esc_html__( 'Width', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the width',
					'id'               => 'dfp_custom_width',
					'type'             => 'text_small',
					'classes'		   => 'custom-dimensions dfp-custom-data-2'
				) );
			$ads_option->add_field( array(
					'name'             => esc_html__( 'Height', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the height',
					'id'               => 'dfp_custom_height',
					'type'             => 'text_small',
					'classes'		   => 'custom-dimensions dfp-custom-data-2'
				) );
			$ads_option->add_field( array(
					'name'             => esc_html__( 'Data Ad Slot', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the Data Ad Slot. e.g./41****9/mobile_ad_banner',
					'id'               => 'dfp_ad_slot',
					'type'             => 'text_medium',
					'classes'		   => 'vendor-fields doubleclick-data-2'
				) );
			$ads_option->add_field( array(
					'name' => 'Parallax Effect',
					'desc' => 'AMP Flying Carpet Ad works only for the incontent Ads',
					'id'   => 'dfp_parallax',
					'type' => 'checkbox',
					'classes'		   => 'vendor-fields doubleclick-data-2'
				) );

		// Custom Options
			$ads_option->add_field( array(
					'name'             => esc_html__( 'Custom', 'ampforwp_adsforwp' ),
					'desc'			   => 'Enter the Custom Ad Code',
					'id'               => 'custom_ad',
					'type'             => 'textarea_code',
					'classes'		   => 'vendor-fields custom-data-3'
				) );
			$ads_option->add_field( array(
					'name' => 'Parallax Effect',
					'desc' => 'AMP Flying Carpet Ad',
					'id'   => 'custom_parallax',
					'type' => 'checkbox',
					'classes'		   => 'vendor-fields custom-data-3'
				) );
}