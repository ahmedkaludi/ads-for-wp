<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
	Metabox to show ads type such as custom and adsense
 */
class Adsforwp_View_Ads_Type {


	private $screen          = array( 'adsforwp' );
	private $common_function = null;
	private $ads_list = array();
	private $meta_fields     = array(
		array(
			'label'      => 'Ad Type',
			'id'         => 'select_adtype',
			'type'       => 'select',
			'options'    => array(
				''              => 'Select Ad Type',
				'adsense'       => 'AdSense',
				'doubleclick'   => 'DoubleClick',
				'media_net'     => 'Media.net',
				'ad_now'        => 'AdNow',
				'mgid'          => 'MGID',
				'contentad'     => 'Content.ad',
				'engageya'      => 'Engageya',
				'ezoic'         => 'Ezoic',
				'infolinks'     => 'Infolinks',
				'mantis'        => 'MANTIS',
				'mediavine'     => 'Mediavine',
				'outbrain'      => 'Outbrain',
				'taboola'       => 'Taboola',
				'ad_image'      => 'Image Banner Ad',
				'ad_background' => 'Background Ad',
				'revcontent'    => 'Revcontent Ad',
				'amp_story_ads' => 'AMP Story Ad',
				'popupad'        => 'Popup Ad',
				'custom'        => 'Custom Code',

			),
			'attributes' => array( 'required' => 'required' ),
			'metaboxes'  => array(
				'doubleclick'   => array( 'all' ),
				'media_net'     => array( 'all' ),
				'ad_now'        => array( 'all' ),
				'mgid'          => array( 'all' ),
				'contentad'     => array( 'all' ),
				'ezoic'         => array( 'display-metabox' ),
				'infolinks'     => array( 'all' ),
				'mantis'        => array( 'all' ),
				'mediavine'     => array( 'all' ),
				'outbrain'      => array( 'all' ),
				'taboola'       => array( 'all' ),
				'ad_image'      => array( 'all' ),
				'ad_background' => array( 'display-metabox', 'adsforwp-location' ),
				'amp_story_ads' => array( 'display-metabox', 'setexpiredate', 'adsforwp-location', 'adsforwp_visitor_condition_metabox', 'adsforwp_placement_metabox' ),
				'engageya'      => array( 'adsforwp-location', 'setexpiredate' ),
				'popupad'    => array( 'display-metabox'),
				'revcontent'    => array( 'all' ),
				'custom'        => array( 'all' ),
			),
		),
		array(
			'label'      => 'Data ID',
			'id'         => 'revcontent_data_id',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '123456',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'revcontent' ),
			),
		),
		array(
			'label'      => 'Data Wrapper',
			'id'         => 'revcontent_data_wrapper',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => 'rcjsload_2ff711',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'revcontent' ),
			),
		),

		array(
			'label'     => 'Ad Type',
			'id'        => 'amp_story_adtypes',
			'type'      => 'select',
			'options'   => array(
				''            => 'Select Ad Type',
				'doubleclick' => 'DoubleClick',
			),
			'required'  => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'amp_story_ads' ),
			),
			'metaboxes' => array(
				''            => array( 'display-metabox', 'setexpiredate', 'adsforwp-location', 'adsforwp_visitor_condition_metabox', 'adsforwp_placement_metabox' ),
				'doubleclick' => array( 'display-metabox', 'setexpiredate', 'adsforwp-location', 'adsforwp_visitor_condition_metabox', 'adsforwp_placement_metabox' ),
			),
		),
		array(
			'label'      => 'Data Slot Id',
			'id'         => 'ampstory_dfp_slot_id',
			'type'       => 'text',
			'attributes' => array(
				'placeholder'   => '/41****9/mobile_ad_banner',
				'maxlength'     => '50',
				'provider_type' => 'adsforwp_dfp',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype'     => 'amp_story_ads',
					'amp_story_adtypes' => 'doubleclick',
				),
			),
		),
		array(
			'label'      => 'Widget Id\'s',
			'id'         => 'engageya_widget_ids',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => 'widget_1,widget_2',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'engageya' ),
			),
		),
		array(
			'label'      => 'Data WebSite Id',
			'id'         => 'engageya_site_id',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '123456',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'engageya' ),
			),
		),
		array(
			'label'      => 'Data Publisher Id',
			'id'         => 'engageya_publisher_id',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '123456',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'engageya' ),
			),
		),
		array(
			'label'      => 'Data Site Id',
			'id'         => 'mediavine_site_id',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '123456',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'mediavine' ),
			),
		),
		array(
			'label'      => 'Data Slot Id',
			'id'         => 'ezoic_slot_id',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '123456',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'ezoic' ),
			),
		),
		array(
			'label'      => 'Data Publisher Id',
			'id'         => 'taboola_publisher_id',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '123456',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'taboola' ),
			),
		),
		array(
			'label'    => 'Outbrain Type',
			'id'       => 'outbrain_type',
			'type'     => 'select',
			'options'  => array(
				'normal'              => 'Normal',
				'outbrain_sticky_ads' => 'Sticky (Only AMP)',
			),
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'outbrain' ),
			),
		),

		array(
			'label'      => 'Widget Id\'s',
			'id'         => 'outbrain_widget_ids',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => 'widget_1,widget_2',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'outbrain' ),
			),
		),
		array(
			'label'    => 'Display Type',
			'id'       => 'mantis_display_type',
			'type'     => 'select',
			'options'  => array(
				'display'   => 'Display',
				'recommend' => 'Recommend',
			),
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'mantis' ),
			),
		),
		array(
			'label'      => 'Data Property Id',
			'id'         => 'mantis_property_id',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '5a5840d00000000000000000',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'mantis' ),
			),
		),
		array(
			'label'      => 'Data Zone Name',
			'id'         => 'mantis_zone_name',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => 'top',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'mantis' ),
			),
		),
		array(
			'label'     => 'AdSense Type',
			'id'        => 'adsense_type',
			'type'      => 'select',
			'options'   => array(
				'normal'              => 'Normal',
				'adsense_auto_ads'    => 'Auto Ads',
				'adsense_sticky_ads'  => 'Sticky (Only AMP)',
				'matched_content_ads' => 'Matched Content Ads',
				'in_article_ads'      => 'In-Article Ads',
				'in_feed_ads'         => 'In-Feed Ads',
			),
			'metaboxes' => array(
				'normal'              => array( 'all' ),
				'matched_content_ads' => array( 'all' ),
				'in_article_ads'      => array( 'all' ),
				'in_feed_ads'         => array( 'all' ),
				'adsense_sticky_ads'  => array( 'display-metabox', 'adsforwp-location' ),
				'adsense_auto_ads'    => array( 'display-metabox', 'adsforwp_visitor_condition_metabox' ),
			),
			'required'  => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'adsense' ),
			),
		),
		array(
			'label'    => 'Custom Code',
			'id'       => 'custom_code',
			'type'     => 'textarea',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'custom' ),
			),
		),
		array(
			'label'      => 'Data Layout Key',
			'id'         => 'data_layout_key',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '-ez+4v+7r-fc+65',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype' => 'adsense',
					'adsense_type'  => array( 'in_feed_ads' ),
				),
			),
		),
		array(
			'label'      => 'Data Client ID',
			'id'         => 'data_client_id',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => 'ca-pub-2005XXXXXXXXX342',
				'maxlength'   => '30',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype' => 'adsense',
					'adsense_type'  => array( 'normal', 'adsense_sticky_ads', 'matched_content_ads', 'adsense_auto_ads', 'in_article_ads', 'in_feed_ads' ),
				),
			),
		),
		array(
			'label'      => 'Data Ad Slot',
			'id'         => 'data_ad_slot',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '70XXXXXX12',
				'maxlength'   => '20',

			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype' => 'adsense',
					'adsense_type'  => array( 'normal', 'adsense_sticky_ads', 'matched_content_ads', 'in_article_ads', 'in_feed_ads' ),
				),
			),
		),
		array(
			'label'    => 'Lazy Load	',
			'id'       => 'adsforwp_adsense_lazy_load_check',
			'type'     => 'checkbox',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'adsense' ),
			),
		),
		array(
			'label'    => 'Matched Content Type',
			'id'       => 'matched_content_type',
			'type'     => 'select',
			'options'  => array(
				'image_sidebyside'      => 'Image Text SideBySide',
				'image_card_sidebyside' => 'Image Text SideBySide With Card',
				'image_stacked'         => 'Image Stacked Above Text',
				'image_card_stacked'    => 'Image Stacked Above Text With Card',
				'text'                  => 'Text Only',
				'text_card'             => 'Text With Card',
			),
			'required' => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype' => 'adsense',
					'adsense_type'  => 'matched_content_ads',
				),
			),
		),
		array(
			'label'      => 'Number of Rows',
			'id'         => 'matched_content_rows',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '3',
				'maxlength'   => '20',

			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype' => 'adsense',
					'adsense_type'  => 'matched_content_ads',
				),
			),
		),
		array(
			'label'      => 'Number of Columns',
			'id'         => 'matched_content_columns',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '3',
				'maxlength'   => '20',

			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype' => 'adsense',
					'adsense_type'  => 'matched_content_ads',
				),
			),
		),
		// Media.net fields starts here
		array(
			'label'      => 'Data CID',
			'id'         => 'data_cid',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '8XXXXX74',
				'maxlength'   => '20',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'media_net' ),
			),
		),
		array(
			'label'      => 'Data CRID',
			'id'         => 'data_crid',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '1XXXXXX82',
				'maxlength'   => '20',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'media_net' ),
			),
		),
		// Media.net fields ends here

			// DoubleClick fields starts here
		array(
			'label'      => 'Slot Id',
			'id'         => 'dfp_slot_id',
			'type'       => 'text',
			'attributes' => array(
				'placeholder'   => '/41****9/mobile_ad_banner',
				'maxlength'     => '50',
				'provider_type' => 'adsforwp_dfp',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'doubleclick' ),
			),
		),

		array(
			'label'      => 'Div Gpt Ad',
			'id'         => 'dfp_div_gpt_ad',
			'type'       => 'text',
			'attributes' => array(
				'placeholder'   => 'div-gpt-ad-*************-*',
				'maxlength'     => '60',
				'provider_type' => 'adsforwp_dfp',
			),
			'required'   => array(
				'type'   => 'and', // 'and'
				'fields' => array( 'select_adtype' => 'doubleclick' ),
			),
		),
		array(
			'label'      => 'Data Publisher',
			'id'         => 'adsforwp_mgid_data_publisher',
			'type'       => 'text',
			'attributes' => array( 'placeholder' => 'site.com' ),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'mgid' ),
			),
		),
		array(
			'label'      => 'Data Widget',
			'id'         => 'adsforwp_mgid_data_widget',
			'type'       => 'text',
			'attributes' => array( 'placeholder' => '123645' ),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'mgid' ),
			),
		),
		array(
			'label'      => 'Data Container',
			'id'         => 'adsforwp_mgid_data_container',
			'type'       => 'text',
			'attributes' => array( 'placeholder' => 'M87ScriptRootC123645' ),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'mgid' ),
			),
		),
		array(
			'label'      => 'Data Js Src',
			'id'         => 'adsforwp_mgid_data_js_src',
			'type'       => 'text',
			'note'       => 'Js is require to work in non AMP',
			'attributes' => array( 'placeholder' => '//jsc.mgid.com/a/m/adsforwp.com.123645.js' ),
			'required'   => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'mgid' ),
			),
		),
		// DoubleClick fields ends here
		array(
			'label'    => 'Size',
			'id'       => 'banner_size',
			'type'     => 'select',
			'options'  => array(
				''         => 'Select Size',
				'728x90'   => 'Leaderboard (728x90)',
				'468x60'   => 'Banner (468x60)',
				'234x60'   => 'Half Banner (234x60)',
				'125x125'  => 'Button (125x125)',
				'120x600'  => 'Skyscraper (120x600)',
				'160x600'  => 'Wide Skyscraper (160x600)',
				'180x150'  => 'Small Rectangle (180x150)',
				'120x240'  => 'Vertical Banner (120x240)',
				'200x200'  => 'Small Square (200x200)',
				'250x250'  => 'Square (250x250)',
				'200x50'   => 'Rectangle (200x50)',
				'300x250'  => 'Medium Rectangle (300x250)',
				'336x280'  => 'Large Rectangle (336x280)',
				'300x600'  => 'Half Page (300x600)',
				'300x1050' => 'Portrait (300x1050)',
				'320x50'   => 'Mobile Banner (320x50)',
				'320x100'  => 'Large Mobile Banner (320x100)',
				'970x90'   => 'Large Leaderboard (970x90)',
				'970x250'  => 'Billboard (970x250)',
				'728x20'   => 'Wide Horizontal (728x20)',
				'600x120'  => 'Horizontal (600x120)',
			),
			'required' => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype' => array( 'adsense', 'doubleclick', 'media_net', 'mgid', 'engageya' ),
					'adsense_type'  => array( 'normal', 'adsense_sticky_ads' ),
				),
			),
		),
		array(
			'label'    => 'Multi-size Ads',
			'id'       => 'dfp_multisize_ads',
			'type'     => 'checkbox',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'doubleclick' ),
			),
		),
		array(
			'label'      => 'Add Sizes',
			'id'         => 'dfp_multisize_ads_sizes',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => '700x90,700x60,500x60....',
			),
			'required'   => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype'     => 'doubleclick',
					'dfp_multisize_ads' => '1',
				),
			),
		),
		array(
			'label'    => 'Multi-size Validation',
			'id'       => 'dfp_multisize_validation',
			'type'     => 'checkbox',
			'required' => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype'     => 'doubleclick',
					'dfp_multisize_ads' => '1',
				),
			),
		),

		array(
			'label'    => 'Upload Ad Image',
			'id'       => 'adsforwp_ad_image',
			'type'     => 'media',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'ad_image' ),
			),
		),
		array(
			'label'    => 'SVG Image Size',
			'id'       => 'adsforwp_svg_sizes',
			'type'     => 'checkbox',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'ad_image' ),
			),
		),
		array(
			'label'    => 'Image Width',
			'id'       => 'adsforwp_svg_width',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype'      => 'ad_image',
					'adsforwp_svg_sizes' => 1,
				),
			),
		),
		array(
			'label'    => 'Image Height',
			'id'       => 'adsforwp_svg_height',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype'      => 'ad_image',
					'adsforwp_svg_sizes' => 1,
				),
			),
		),
		array(
			'label'    => 'Lazy Load	',
			'id'       => 'adsforwp_lazy_load_check',
			'type'     => 'checkbox',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'ad_image' ),
			),
		),
		array(
			'label'    => 'Lazy Load Delay',
			'id'       => 'check_lazy_load_delay',
			'type'     => 'number',
			'required' => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype'            => 'ad_image',
					'adsforwp_lazy_load_check' => 1,
				),
			),
		),
		array(
			'label'    => 'Ad Anchor link',
			'id'       => 'adsforwp_ad_redirect_url',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'ad_image' ),
			),
		),
		array(
			'label'    => 'Ad rel Link Attribute',
			'id'       => 'adsforwp_ad_rel_attr',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'ad_image' ),
			),
		),
		array(
			'label'    => 'AdNow Widget ID',
			'id'       => 'ad_now_widget_id',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'ad_now' ),
			),
		),
		array(
			'label'    => 'ID',
			'id'       => 'contentad_id',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'contentad' ),
			),
		),
		array(
			'label'    => 'D',
			'id'       => 'contentad_id_d',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'contentad' ),
			),
		),
		array(
			'label'    => 'Content Ad Widget ID',
			'id'       => 'contentad_widget_id',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'contentad' ),
			),
		),
		array(
			'label'    => 'Infolinks P ID',
			'id'       => 'infolinks_pid',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'infolinks' ),
			),
		),
		array(
			'label'    => 'Infolinks W S ID',
			'id'       => 'infolinks_wsid',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'infolinks' ),
			),
		),
		array(
			'label'    => 'Select Ad for Popup',
			'id'         => 'select_popupad',
			'type'       => 'select',
			'options'    => array(),
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'popupad' ),
			),
		),
		array(
			'label'    => 'Popup Type',
			'id'         => 'select_popupad_type',
			'type'       => 'select',
			'options'    => array(
				''=>'Select Popup Type',
				'instantly' => 'Load Instantly',
				'specific_time' => 'After Specific Time',
				'on_scroll' => 'On Scroll',
				'on_top' => 'Load on Top',
				'on_bottom' => 'Load on Bottom',
			),
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'popupad' ),
			),
		),
		array(
			'label'    => 'Delay Popup (in miliseconds)',
			'id'       => 'select_popupad_type_time',
			'type'     => 'number',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 
									'select_adtype' => 'popupad',
									'select_popupad_type' => array('specific_time','on_scroll','on_top','on_bottom') 
								),
			),
		),
		array(
			'label'    => 'Cookie Setup',
			'id'         => 'select_popupad_cookie',
			'type'       => 'select',
			'options'    => array(
				'no_expiry' => 'No Expiry',
				'expiry' => 'Expiry',
			),
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'popupad' ),
			),
		),
		array(
			'label'    => 'Cookie Expiry Days',
			'id'       => 'select_popupad_cookie_expiry',
			'type'     => 'number',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 
									'select_adtype' => 'popupad',
									'select_popupad_cookie' => 'exipiry' 
								),
			),
		),
		array(
			'label'    => 'Upload Ad Image',
			'id'       => 'ad_background_image',
			'type'     => 'media',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'ad_background' ),
			),
		),
		array(
			'label'    => 'Ad Anchor link',
			'id'       => 'ad_background_redirect_url',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array( 'select_adtype' => 'ad_background' ),
			),
		),
		array(
			'label'    => 'Responsive',
			'id'       => 'adsforwp_ad_responsive',
			'type'     => 'checkbox',
			'required' => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype' => array( 'adsense', 'engageya', 'outbrain', 'ad_image', 'revcontent' ),
					'adsense_type'  => array( 'normal' ),
				),
			),
		),
		array(
			'label'    => 'Preload ImageAd',
			'id'       => 'adsforwp_ad_preload_image_ad',
			'type'     => 'checkbox',
			'required' => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype' => array( 'ad_image' ),
				),
			),
		),
		array(
			'label'    => 'Max Width',
			'id'       => 'ad_responsive_max_width',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype'          => 'adsense',
					'adsense_type'           => 'normal',
					'adsforwp_ad_responsive' => '1',
				),
			),
		),
		array(
			'label'    => 'Min Width',
			'id'       => 'ad_responsive_min_width',
			'type'     => 'text',
			'required' => array(
				'type'   => 'and',
				'fields' => array(
					'select_adtype'          => 'adsense',
					'adsense_type'           => 'normal',
					'adsforwp_ad_responsive' => '1',
				),
			),
		),
		array(
			'id'   => 'adsforwp_ad_img_height',
			'type' => 'hidden',
		),
		array(
			'id'   => 'adsforwp_ad_img_width',
			'type' => 'hidden',
		),
	);
	public function __construct() {
		$notice_arry                         = array(
			'ezoic'         => '<p class="ezoic_notice"><i>' . esc_html__( 'Note: This Ad type is not implemented in AMP.', 'ads-for-wp' ) . '</i></p>',
			'infolinks'     => '<p class="infolinks_notice"><i>' . esc_html__( 'Note: This Ad type is not implemented in AMP.', 'ads-for-wp' ) . '</i></p>',
			'amp_story_ads' => '<p class="ampstory_notice"><i>' . esc_html__( 'Note: Need AMP Story feature inorder to use this Ad Type', 'ads-for-wp' ) . '</i></p>',
			'ad_now'        => '<p class="adnow_notice"><i>' . esc_html__( 'Note: This Ad type is not implemented in AMP.', 'ads-for-wp' ) . '</i></p>',
			'engageya'      => '<p class="engageya_notice"><i>' . esc_html__( 'Note: This Ad type is not implemented in Non-AMP.', 'ads-for-wp' ) . '</i></p>',
			'revcontent'    => '<p class="revcontent_notice"><i>' . esc_html__( 'Note: This Ad type is not implemented in Non-AMP.', 'ads-for-wp' ) . '</i></p>',
		);
		$adsense_type_notice                 = array(
			'in_article_ads' => '<p class="in_article_notice"><i>' . esc_html__( 'Note: This AdSense type is not implemented in AMP.', 'ads-for-wp' ) . '</i></p>',
			'in_feed_ads'    => '<p class="in_feed_notice"><i>' . esc_html__( 'Note: This AdSense type is not implemented in AMP.', 'ads-for-wp' ) . '</i></p>',
		);
		$this->meta_fields[0]['notice']      = $notice_arry;
			$this->meta_fields[16]['notice'] = $adsense_type_notice;
		if ( $this->common_function == null ) {
			$this->common_function = new Adsforwp_Admin_Common_Functions();
		}
		$this->adsfrowp_get_all_ads($this->meta_fields);
		add_action( 'add_meta_boxes', array( $this, 'adsforwp_add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'adsforwp_save_fields' ) );
	}
	public function adsfrowp_get_all_ads(){
		$post_id = 0;
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if(isset($_GET['post'])){
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post_id = intval($_GET['post']);
		}
		$all_ads_post = get_posts(
			array(
				'post_type'      => 'adsforwp',
				'posts_per_page' => -1,
				//phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
				'post__not_in'=>array($post_id),
				'post_status'    => 'publish',
			)
		);
		$post_data = array(
			''=>'Select Ad for Pupup'
		);
		
		if ( $all_ads_post ) {
			foreach ( $all_ads_post as $ads ) {
				$data_item = array();
				$post_data["'".$ads->ID."'"] = $ads->post_title; 
			}
		}
		
		
		foreach ($this->meta_fields as $key => $value) {
			if($value['id']=='select_popupad'){
				$value['options'] = $post_data;
				$this->meta_fields[$key] =  $value;
				break;
			}
		}
	}
	public function adsforwp_add_meta_boxes() {
		foreach ( $this->screen as $single_screen ) {
			add_meta_box(
				'adtype',
				esc_html__( 'Ad Type', 'ads-for-wp' ),
				array( $this, 'adsforwp_meta_box_callback' ),
				$single_screen,
				'normal',
				'high'
			);
		}
	}
	public function adsforwp_meta_box_callback( $post ) {
		wp_nonce_field( 'adsforwp_adtype_data', 'adsforwp_adtype_nonce' );
		$this->adsforwp_field_generator( $post );
	}
	public function adsforwp_adtype_metabox_fields() {
		$allmetafields = $this->meta_fields;
		return $allmetafields;
	}
	public function adsforwp_field_generator( $post ) {
		$output_escaped = '';
		foreach ( $this->meta_fields as $meta_field ) {

					$attributes = $provider_type = $label = '';

			if ( isset( $meta_field['label'] ) ) {
				$label = $meta_field['label'];
			}
			$label      = '<label for="' . $meta_field['id'] . '">' . esc_html( $label ) . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( empty( $meta_value ) ) {
				$meta_value = isset( $meta_field['default'] );

			}

			if ( isset( $meta_field['attributes'] ) ) {

				if ( array_key_exists( 'provider_type', $meta_field['attributes'] ) ) {

					$provider_type = $meta_field['attributes']['provider_type'];

				}
			}

			switch ( $meta_field['type'] ) {
				case 'select':
					if ( isset( $meta_field['attributes'] ) ) {
						foreach ( $meta_field['attributes'] as $key => $value ) {
							$attributes .= esc_attr( $key ) . '=' . '"' . esc_attr( $value ) . '"' . ' ';
						}
					}
					$input = sprintf(
						'<select class="afw_select" id="%s" name="%s" %s onchange="adsforwp_get_adtype(this);">',
						esc_attr( $meta_field['id'] ),
						esc_attr( $meta_field['id'] ),
						$attributes
					);
					foreach ( $meta_field['options'] as $key => $value ) {
						$meta_field_value = ! is_numeric( $key ) ? $key : $value;

						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$meta_value === $meta_field_value ? 'selected' : '',
							$meta_field_value,
							esc_html( $value )
						);
					}
					switch ( $meta_field['id'] ) {
						case 'select_adtype':
							$input .= '</select><span style="cursor:pointer;float:right;" class="afw_pointer dashicons-before dashicons-editor-help" id="afw_data_cid_pointer"></span>';
							break;
						case 'adsense_type':
							$input .= '</select><p class="afw_adsense_auto_note afw_hide">' . esc_html__( 'You have already added Adsense Auto Ad.', 'ads-for-wp' ) . ' <a class="afw_adsense_auto">' . esc_html__( 'Edit', 'ads-for-wp' ) . '</a></p>';
							break;
						default:
							$input .= '</select>';
							break;
					}

					break;
				case 'textarea':
					$input = sprintf(
						'<textarea class="afw_textarea" id="%s" name="%s" rows="5">%s</textarea>',
						esc_attr( $meta_field['id'] ),
						esc_attr( $meta_field['id'] ),
						esc_textarea( $meta_value )
					);
					break;
				case 'checkbox':
					$adsforwp_lazy_load_check = '';
					if ( ( $meta_field['id'] == 'adsforwp_lazy_load_check' || $meta_field['id'] == 'adsforwp_adsense_lazy_load_check' ) && ! defined( 'ADSFORWP_PRO_VERSION' ) ) {
						$adsforwp_lazy_load_check = "disabled='disabled'";
					}
									$input = sprintf(
										'<input %s id="%s" name="%s" type="checkbox" value="1" onclick="adsforwp_get_adtype(this);" ' . $adsforwp_lazy_load_check . '>',
										$meta_value === '1' ? 'checked' : '',
										esc_attr( $meta_field['id'] ),
										esc_attr( $meta_field['id'] )
									);
					if ( ( $meta_field['id'] == 'adsforwp_lazy_load_check' || $meta_field['id'] == 'adsforwp_adsense_lazy_load_check' ) && ! defined( 'ADSFORWP_PRO_VERSION' ) ) {
							$input .= '<a target="_blank" href="https://www.adsforwp.com/pricing/#pricings" style="text-decoration: none;color: white; font-weight: bold;margin-left: 0px;font-size: 13px !important; padding: 7px 9px;letter-spacing: 0.1px;border-radius: 60px;margin-right: 0px; background: linear-gradient(to right,#eb3349,#f45c43);">' . esc_html__( 'Upgrade to Premium', 'ads-for-wp' ) . '</a>';
					}
					if ( $meta_field['id'] == 'adsforwp_ad_responsive' ) {
						$input .= '  <span class="responsive_advance" style="padding-left:20px;"><a href="#" class="adsforwp_resp_advan">Advance Size Options</a></span>';
					}
					break;
				case 'media':
					if ( $meta_field['id'] == 'adsforwp_ad_image' ) {

									$imageprev = '';
						if ( $meta_value ) {
							$imageprev .= '<br><div class="afw_ad_thumbnail">';
							$imageprev .= '<img class="afw_ad_image_prev" src="' . esc_url( $meta_value ) . '"/>';
							$imageprev .= '<a href="#" class="afw_ad_prev_close">X</a>';
							$imageprev .= '</div>';

						}
								$input = sprintf(
									'<input class="afw_input adsforwp-icon" type="text" name="%s" id="%s" value="%s"/>'
									. '<button type="button" class="button adsforwp-ad-img-upload" data-editor="content">'
									. '<span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> ' . esc_html__( 'Upload Image', 'ads-for-wp' ) . ''
									. '</button>'
									. '<div class="afw_ad_img_div">%s'
									. '</div>',
									esc_attr( $meta_field['id'] ),
									esc_attr( $meta_field['id'] ),
									$meta_value,
									$imageprev
								);

					} else {

											$media_value      = array();
											$media_thumbnail  = '';
											$media_height     = '';
											$media_width      = '';
											$media_key        = esc_attr( $meta_field['id'] ) . '_detail';
											$media_value_meta = get_post_meta( $post->ID, $media_key, true );

						if ( ! empty( $media_value_meta ) ) {
							$media_value = $media_value_meta;
						}
						if ( isset( $media_value['thumbnail'] ) ) {
							$media_thumbnail = $media_value['thumbnail'];
						}
						if ( isset( $media_value['height'] ) ) {
							$media_height = $media_value['height'];
						}
						if ( isset( $media_value['width'] ) ) {
							$media_width = $media_value['width'];
						}
											$imageprev = '';
						if ( isset( $media_value_meta['thumbnail'] ) ) {
							$imageprev .= '<br><div class="afw_ad_thumbnail">';
							$imageprev .= '<img class="afw_ad_image_prev" src="' . esc_url( $media_value_meta['thumbnail'] ) . '"/>';
							$imageprev .= '<a href="#" class="afw_ad_prev_close">X</a>';
							$imageprev .= '</div>';

						}
										$input = sprintf(
											'<fieldset>'
												. '<input class="afw_input" id="%s" name="%s" type="text" value="%s">'
												. '<input media-id="media" style="width: 19%%" class="button" id="%s_button" name="%s_button" type="button" value="Upload" />'
												. '<input type="hidden" data-id="' . esc_attr( $meta_field['id'] ) . '_height" class="upload-height" name="' . esc_attr( $meta_field['id'] ) . '_height" id="' . esc_attr( $meta_field['id'] ) . '_height" value="' . esc_attr( $media_height ) . '">'
												. '<input type="hidden" data-id="' . esc_attr( $meta_field['id'] ) . '_width" class="upload-width" name="' . esc_attr( $meta_field['id'] ) . '_width" id="' . esc_attr( $meta_field['id'] ) . '_width" value="' . esc_attr( $media_width ) . '">'
												. '<input type="hidden" data-id="' . esc_attr( $meta_field['id'] ) . '_thumbnail" class="upload-thumbnail" name="' . esc_attr( $meta_field['id'] ) . '_thumbnail" id="' . esc_attr( $meta_field['id'] ) . '_thumbnail" value="' . esc_attr( $media_thumbnail ) . '">'
												. '</fieldset>'
												. '<div class="afw_ad_img_div">%s'
												. '</div>',
											esc_attr( $meta_field['id'] ),
											esc_attr( $meta_field['id'] ),
											$media_thumbnail,
											esc_attr( $meta_field['id'] ),
											esc_attr( $meta_field['id'] ),
											$imageprev
										);

					}

					break;
				case 'hidden':
					$input = sprintf(
						'<input id="%s" name="%s" type="hidden" value="%s">',
						esc_attr( $meta_field['id'] ),
						esc_attr( $meta_field['id'] ),
						$meta_value
					);
					break;
				default:
					if ( isset( $meta_field['attributes'] ) ) {

						foreach ( $meta_field['attributes'] as $key => $value ) {

							$attributes .= esc_attr( $key ) . '=' . '"' . esc_attr( $value ) . '"' . ' ';

						}
					}

									$input = sprintf(
										'<input class="afw_input" %s id="%s" name="%s" type="%s" onblur="adsforwp_get_adtype(this);" value="%s" %s>',
										$meta_field['type'] !== 'color' ? '' : '',
										esc_attr( $meta_field['id'] ),
										esc_attr( $meta_field['id'] ),
										esc_attr( $meta_field['type'] ),
										esc_attr( $meta_value ),
										$attributes
									);

			}
						$note = '';

			if ( isset( $meta_field['note'] ) ) {
				$note = '<p>' . $meta_field['note'] . '</p>';
			}

						$input = $input . $note;

			$output_escaped .= '<tr class="' . esc_attr( $provider_type ) . '"><th>' . $label . '</th><td>' . $input . '</td></tr>';
		}
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- Reason: already escaped
		echo '<table class="form-table adsforwp-ad-type-table"><tbody>' . $output_escaped . '</tbody></table>';
	}	
	public function adsforwp_save_fields( $post_id ) {

		if ( ! isset( $_POST['adsforwp_adtype_nonce'] ) ) {
			return $post_id;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
		if ( ! wp_verify_nonce( $_POST['adsforwp_adtype_nonce'], 'adsforwp_adtype_data' ) ) {
			return $post_id;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( current_user_can( 'manage_options' ) ) {

			$allowed_html = $this->common_function->adsforwp_expanded_allowed_tags();

			$post_meta = array();
			$post_meta = $_POST; // Sanitized below before saving

			foreach ( $this->meta_fields as $meta_field ) {

				if ( isset( $post_meta[ $meta_field['id'] ] ) ) {
					switch ( $meta_field['type'] ) {
						case 'email':
							$post_meta[ $meta_field['id'] ] = sanitize_email( $post_meta[ $meta_field['id'] ] );
							break;
						case 'text':
							$post_meta[ $meta_field['id'] ] = sanitize_text_field( $post_meta[ $meta_field['id'] ] );
							break;
						case 'textarea':
							$post_meta[ $meta_field['id'] ] = wp_unslash( $post_meta[ $meta_field['id'] ] );
							break;
						default:
							$post_meta[ $meta_field['id'] ] = sanitize_text_field( $post_meta[ $meta_field['id'] ] );
					}
					if ( $meta_field['id'] == 'ad_background_image' ) {

								$media_key       = $meta_field['id'] . '_detail';
								$media_height    = sanitize_text_field( $post_meta[ $meta_field['id'] . '_height' ] );
								$media_width     = sanitize_text_field( $post_meta[ $meta_field['id'] . '_width' ] );
								$media_thumbnail = sanitize_text_field( $post_meta[ $meta_field['id'] . '_thumbnail' ] );

								$media_detail = array(
									'height'    => $media_height,
									'width'     => $media_width,
									'thumbnail' => $media_thumbnail,
								);
								update_post_meta( $post_id, $media_key, $media_detail );
					} else {
						update_post_meta( $post_id, $meta_field['id'], $post_meta[ $meta_field['id'] ] );
					}
				} elseif ( $meta_field['type'] === 'checkbox' ) {
					update_post_meta( $post_id, $meta_field['id'], '0' );
				}
			}
		}
	}
}
if ( class_exists( 'Adsforwp_View_Ads_Type' ) ) {
	new Adsforwp_View_Ads_Type();
}
