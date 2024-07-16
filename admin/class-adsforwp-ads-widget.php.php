<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Adds Adsforwp_Ads_Widget widget.
 */
class Adsforwp_Ads_Widget extends WP_Widget {



		private $common_function = null;
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		if ( $this->common_function == null ) {
			$this->common_function = new Adsforwp_Admin_Common_Functions();
		}

		parent::__construct(
			'adsforwp_ads_widget', // Base ID
			esc_html__( 'Ads For WP Ads', 'ads-for-wp' ), // Name
			array( 'description' => esc_html__( 'Widget to display Ads', 'ads-for-wp' ) ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		$allow_html = $this->common_function->adsforwp_expanded_allowed_tags();
		echo wp_kses( $args['before_widget'], $allow_html );

		$all_ads    = $this->common_function->adsforwp_fetch_all_ads();
		$all_groups = $this->common_function->adsforwp_fetch_all_groups();

		foreach ( $all_ads as $ad ) {

			if ( $ad->ID == $instance['ads'] ) {

					$output_function_obj = new Adsforwp_Output_Functions();
					$ad_code_escaped     = $output_function_obj->adsforwp_get_ad_code( $instance['ads'], $type = 'AD', 'notset' );
					//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- Reason: output is already escaped          
					echo $ad_code_escaped;

			}
		}
		foreach ( $all_groups as $group ) {

			if ( $group->ID == $instance['ads'] ) {

				$output_function_obj = new Adsforwp_Output_Functions();
				$widget              = 'widget';
				$ad_code_escaped     = $output_function_obj->adsforwp_group_ads( $atts = null, $instance['ads'], $widget, 'notset' );
       //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- Reason: output is already escaped                       
				echo $ad_code_escaped;

			}
		}
		echo wp_kses( $args['after_widget'], $allow_html );
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$title       = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Ad title or group title', 'ads-for-wp' );
				$ads = ! empty( $instance['ads'] ) ? $instance['ads'] : esc_html__( 'ads list to be display', 'ads-for-wp' );?>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'ads' ) ); ?>"><?php esc_attr_e( 'Ads:', 'ads-for-wp' ); ?></label>
									<?php

									$ads_select_html = $group_select_html = '';

									$all_ads    = $this->common_function->adsforwp_fetch_all_ads();
									$all_groups = $this->common_function->adsforwp_fetch_all_groups();

									foreach ( $all_ads as $ad ) {

										$ads_select_html .= '<option ' . esc_attr( selected( $ads, $ad->ID, false ) ) . ' value="' . esc_attr( $ad->ID ) . '">' . esc_html( $ad->post_title ) . '</option>';

									}
									foreach ( $all_groups as $group ) {

										$group_select_html .= '<option ' . esc_attr( selected( $ads, $group->ID, false ) ) . ' value="' . esc_attr( $group->ID ) . '">' . esc_html( $group->post_title ) . '</option>';
									}
									$allow_html = $this->common_function->adsforwp_expanded_allowed_tags();

									echo '<select id="' . esc_attr( $this->get_field_id( 'ads' ) ) . '" name="' . esc_attr( $this->get_field_name( 'ads' ) ) . '">'
									. '<optgroup label="Groups">'
									. wp_kses( $group_select_html, $allow_html )
									. '<optgroup label="Ads">'
									. wp_kses( $ads_select_html, $allow_html )
									. '</select>';
									?>
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['ads'] = ( ! empty( $new_instance['ads'] ) ) ? sanitize_text_field( $new_instance['ads'] ) : '';
		return $instance;
	}
} // class Adsforwp_Ads_Widget
