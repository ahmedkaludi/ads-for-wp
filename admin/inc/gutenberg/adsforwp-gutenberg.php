<?php 
class Adsforwp_Ads_Gutenberg{
	private static $instance;

	private function __construct() {
		add_action( 'init', array( $this, 'adsforwp_ads_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'adsforwp_register_admin_scripts' ) );
    }

    public function adsforwp_ads_block(){
    	if ( !function_exists( 'register_block_type' ) ) {
			// no Gutenberg, Abort
			return;
		}
		
		register_block_type( 'adsforwp/adsblock', array(
			//'style'         => 'saswp-gutenberg-css-reg',
			'editor_style'  => 'adsforwp-gb-css-editor',
			'editor_script' => 'adsforwp-gb-ad-js',
			'render_callback' => array( $this, 'adsforwp_ads_render_blocks' ),
		) );
    }

    public function adsforwp_register_admin_scripts() {
	    if ( !function_exists( 'register_block_type' ) ) {
	            // no Gutenberg, Abort
	            return;
	    }
	    wp_register_script(
            'adsforwp-gb-ad-js',
            ADSFORWP_PLUGIN_DIR_URI . 'admin/inc/gutenberg/js/adsforwp-blocks.js',
            array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' )
        );                                         
	   
	    $all_ads = adsforwp_get_ad_ids(); 
	    $all_group_ads = adsforwp_get_group_ad_ids();
	    $ads = array();
		$groups = array();
		
		foreach ( $all_ads as $ad_id ) {

			$ads[] = array( 'id' => $ad_id, 'title' => get_the_title( $ad_id ) );
		}
		
		foreach ( $all_group_ads as $gr_ad_id ) {
			$groups[] = array( 'id' => $gr_ad_id, 'name' => get_the_title($gr_ad_id) );
		}
		
		$default = array(
			'--empty--' => esc_html__( '--empty--', 'ads-for-wp' ),
			'adsforwp' => esc_html__( 'Adsforwp Ads', 'ads-for-wp' ),
			'ads' => esc_html__( 'Ads', 'ads-for-wp' ),
			'adGroups' => esc_html__( 'Ad Groups', 'ads-for-wp' ),
		);

		$inline_script = wp_json_encode(
			array(
				'ads' => $ads,
				'groups' => $groups,
				'editLinks' => array(
					'group' => admin_url( 'edit.php?post_type=adsforwp-groups' ),
					'ad' => admin_url( 'post.php?post=%ID%&action=edit' ),
				),
				'default' => $default
			)
		);

	    wp_add_inline_script( 'adsforwp-gb-ad-js', 'var adsforwpGutenberg = '.$inline_script,'before');
        wp_enqueue_script( 'adsforwp-gb-ad-js' );               
	}

    public static function adsforwp_ads_render_blocks($attributes){
    	ob_start();
		if ( !isset( $attributes ) ) {
			ob_end_clean();                                      
			return '';
		}

		// the item is an ad
		if ( 0 === strpos( $attributes['itemID'], 'ad_' ) ) {
			$id = substr( $attributes['itemID'], 3 );
			$output_function_obj = new adsforwp_output_functions();
			$amp_ad_code = $output_function_obj->adsforwp_get_ad_code($id, $type="AD",'notset');
			echo $amp_ad_code;
		} elseif ( 0 === strpos( $attributes['itemID'], 'group_' ) ) {
			$group_id = substr( $attributes['itemID'], 6 );
			$output_function_obj = new adsforwp_output_functions();
   			$group_code =  $output_function_obj->adsforwp_group_ads($atts=null, $group_id, '');     
   			echo $group_code;
		}

		return ob_get_clean();
    }

    public static function get_instance() {
		if( null == self::$instance ){
			self::$instance = new self;
		}
		return self::$instance;
    }
}

if ( class_exists('Adsforwp_Ads_Gutenberg') ){
	Adsforwp_Ads_Gutenberg::get_instance();
}