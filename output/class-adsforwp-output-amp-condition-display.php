<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * This class handles displaying ads according to amp display conditions
 */
class Adsforwp_Output_Amp_Condition_Display {



	private $output_function = null;

	public function __construct() {

		if ( $this->output_function == null ) {
			$this->output_function = new Adsforwp_Output_Functions();
		}
	}
	/**
	 * List of all hooks which are used in this class
	 */
	public function adsforwp_amp_condition_hooks() {
		// Below the Header
		// Amp custom theme
		add_action( 'ampforwp_add_loop_class', array( $this, 'ampforwp_add_loop_class_above_ad' ) );

		add_action( 'ampforwp_after_header', array( $this, 'adsforwp_display_ads_below_the_header' ) );
		add_action( 'ampforwp_design_1_after_header', array( $this, 'adsforwp_display_ads_below_the_header' ) );

		// Below the Footer
		add_action( 'amp_post_template_footer', array( $this, 'adsforwp_display_ads_below_the_footer' ) );

		// ABove the Footer
		add_action( 'amp_post_template_above_footer', array( $this, 'adsforwp_display_ads_above_the_footer' ) );

		// Above the Post Content
		add_action( 'ampforwp_before_post_content', array( $this, 'adsforwp_display_ads_above_the_post_content' ) );
		add_action( 'ampforwp_inside_post_content_before', array( $this, 'adsforwp_display_ads_above_the_post_content' ) );

		// Below the Post Content
		add_action( 'ampforwp_after_post_content', array( $this, 'adsforwp_display_ads_below_the_post_content' ) );
		add_action( 'ampforwp_inside_post_content_after', array( $this, 'adsforwp_display_ads_below_the_post_content' ) );

		// Below The Title
		add_action( 'ampforwp_below_the_title', array( $this, 'adsforwp_display_ads_below_the_title' ) );

		// Above the Related Post
		add_action( 'ampforwp_above_related_post', array( $this, 'adsforwp_display_ads_above_related_post' ) );

		// Below the Author Box
		add_action( 'ampforwp_below_author_box', array( $this, 'adsforwp_display_ads_below_author_box' ) );
		// In loops
		add_action( 'ampforwp_between_loop', array( $this, 'adsforwp_display_ads_between_loop' ), 10, 1 );
		// Ad After Featured Image #42
		add_action( 'ampforwp_after_featured_image_hook', array( $this, 'adsforwp_display_ads_after_featured_image' ) );
	}

	public function adsforwp_display_ads_after_featured_image() {

			$this->adsforwp_amp_condition_ad_code( 'adsforwp_after_featured_image' );
	}

	public function adsforwp_display_ads_between_loop( $count ) {

		$this->adsforwp_amp_condition_ad_code( 'adsforwp_ads_in_loops', $count );
	}

	public function adsforwp_display_ads_below_author_box() {

			$this->adsforwp_amp_condition_ad_code( 'adsforwp_below_author_box' );
	}
	public function adsforwp_display_ads_above_related_post() {

			$this->adsforwp_amp_condition_ad_code( 'adsforwp_above_related_post' );
	}
	public function adsforwp_display_ads_below_the_title() {

			$this->adsforwp_amp_condition_ad_code( 'adsforwp_below_the_title' );
	}
	public function adsforwp_display_ads_below_the_post_content() {

			$this->adsforwp_amp_condition_ad_code( 'adsforwp_below_the_post_content' );
	}
	public function adsforwp_display_ads_above_the_post_content() {

			$this->adsforwp_amp_condition_ad_code( 'adsforwp_above_the_post_content' );
	}
	public function adsforwp_display_ads_above_the_footer() {

			$this->adsforwp_amp_condition_ad_code( 'adsforwp_above_the_footer' );
	}

	public function adsforwp_display_ads_below_the_footer() {

			$this->adsforwp_amp_condition_ad_code( 'adsforwp_below_the_footer' );
	}

	public function adsforwp_display_ads_below_the_header() {

			$this->adsforwp_amp_condition_ad_code( 'adsforwp_below_the_header' );
	}
	/**
	 *  Here, we are fetching ads html markup.
	 *
	 * @param  type $ad_id
	 * @param  type $count
	 * @return type
	 */
	public function adsforwp_in_loop_ads_code( $ad_id, $count ) {

		$ad_code = '';

		$displayed_posts = get_option( 'posts_per_page' );
		$in_between      = round( abs( $displayed_posts / 2 ) );
		$in_between      = get_post_meta( $ad_id, $key = 'adsforwp_after_how_many_post', true );

		if ( intval( $in_between ) == $count ) {

			$ad_code = $this->output_function->adsforwp_get_ad_code( $ad_id, $type = 'AD' );

		}

		return $ad_code;
	}
	/**
	 * Here, we are fetching group ads html markup.
	 *
	 * @param  type $group_id
	 * @param  type $count
	 * @param  type $widget
	 * @return type
	 */
	public function adsforwp_in_loop_group_ads_code( $group_id, $count, $widget ) {

		$displayed_posts = get_option( 'posts_per_page' );
		$in_between      = round( abs( $displayed_posts / 2 ) );
		$in_between      = get_post_meta( $group_id, $key = 'adsforwp_after_how_many_post', true );

		if ( intval( $in_between ) == $count ) {

			$ad_code = $this->output_function->adsforwp_group_ads( $atts = null, $group_id, $widget );

		}

		return $ad_code;
	}

	public function adsforwp_amp_condition_get_ad_code( $condition, $count = null ) {

		$post_ad_id_list = adsforwp_get_ad_ids();

		if ( $post_ad_id_list ) {

			$common_function_obj = new Adsforwp_Admin_Common_Functions();

			foreach ( $post_ad_id_list as $ad_id ) {

						$in_group = $common_function_obj->adsforwp_check_ads_in_group( $ad_id );

				if ( empty( $in_group ) ) {

					$amp_display_condition = get_post_meta( $ad_id, $key = 'wheretodisplay', true );

					if ( $amp_display_condition == $condition ) {

						if ( $amp_display_condition == 'adsforwp_ads_in_loops' ) {

							$amp_ad_code = $this->adsforwp_in_loop_ads_code( $ad_id, $count );

							if ( $amp_ad_code ) {

								return '<div class="amp-ad-wrapper">' . $amp_ad_code . '</div>';

							}
						} else {

							$amp_ad_code = $this->output_function->adsforwp_get_ad_code( $ad_id, $type = 'AD' );

							if ( $amp_ad_code ) {

								return '<div class="amp-ad-wrapper">' . $amp_ad_code . '</div>';

							}
						}
					}
				}
			}
		}
		// For Group Ads
		$post_group_id_list = adsforwp_get_group_ad_ids();

		if ( $post_group_id_list ) {

			foreach ( $post_group_id_list as $group_id ) {

						$widget = '';

						$amp_display_condition = get_post_meta( $group_id, $key = 'wheretodisplay', true );

				if ( $amp_display_condition == $condition ) {

					if ( $amp_display_condition == 'adsforwp_ads_in_loops' ) {

							$amp_group_code = $this->adsforwp_in_loop_group_ads_code( $group_id, $count, $widget );

						if ( $amp_group_code ) {

							return '<div class="amp-ad-wrapper">' . $amp_group_code . '</div>';

						}
					} else {

						$amp_group_code = $this->output_function->adsforwp_group_ads( $atts = null, $group_id, $widget );

						if ( $amp_group_code ) {

							return '<div class="amp-ad-wrapper">' . $amp_group_code . '</div>';

						}
					}
				}
			}
		}
	}

	/**
	 * Here, We are displaying ads or group ads according to amp where to display condition
	 *
	 * @param type $condition
	 * @param type $count
	 */
	public function adsforwp_amp_condition_ad_code( $condition, $count = null ) {

        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- Reason: output is already escaped  
		echo $this->adsforwp_amp_condition_get_ad_code( $condition, $count );
	}

	/**
	 * Here, We are adding a class above ad inside loop ad for amp theme framework
	 *
	 * @param type $i
	 */
	public function ampforwp_add_loop_class_above_ad( $i ) {

		$condition = 'adsforwp_ads_in_loops';

		$result = $this->adsforwp_amp_condition_get_ad_code( $condition, $i );

		if ( $result ) {
			echo 'ampforwp-new-class';
		}
	}
}
if ( class_exists( 'Adsforwp_Output_Amp_Condition_Display' ) ) {

		add_action( 'amp_init', 'adsforwp_amp_hooks_call' );

	function adsforwp_amp_hooks_call() {

		$adsforwp_condition_obj = new Adsforwp_Output_Amp_Condition_Display();
		$adsforwp_condition_obj->adsforwp_amp_condition_hooks();
	}
}
