<?php
// Register ADSFORWP_Widget widget
function adsforwp_register_ads_widget() {
    register_widget( 'ADSFORWP_Widget' );
}
add_action( 'widgets_init', 'adsforwp_register_ads_widget' );

/**
 * Adds ADSFORWP_Widget widget.
 */
class ADSFORWP_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'adsforwp_widget', // Base ID
			esc_html__( 'ADSforWP Advertisement Widget', 'ads-for-wp' ), // Name
			array( 'description' => esc_html__( 'An Advertisement Widget', 'ads-for-wp' ), ) // Args
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

		$check_1 	= '';
		$check_2 	= '';
		$show_ads 	= '';

		$show_ads = 'yes';		
		$show_ads = apply_filters('adsforwp_advert_on_off', $show_ads);

		if ( $instance['ad_position'] === '1' ) {
			$check_1 = '1';
			$check_2 = 'global';
		}
		if ( $instance['ad_position'] === '2') {
			$check_1 = '2';
			$check_2 = is_home(); 
		} 
		if ( $instance['ad_position'] === '3' ) {
			$check_1 = '3';
			$check_2 =  is_single();
		}

		if ( ( $check_1 ) &&  ( $check_2 ) && ( $show_ads == 'yes') ) {

			echo $args['before_widget'];
				if ( ! empty( $instance['title'] ) ) {
					echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
				}

				echo $instance['ad_code'];

			echo $args['after_widget'];
		}
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title 			= ! empty( $instance['title'] ) ? $instance['title'] : '';
		$ad_code 		= ! empty( $instance['ad_code'] ) ? $instance['ad_code'] : '';
		$ad_position	= ! empty( $instance['ad_position'] ) ? $instance['ad_position'] : '';
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'ads-for-wp' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ad_code' ) ); ?>"><?php esc_attr_e( 'Ad Code:', 'ads-for-wp' ); ?></label> 
			<textarea class="widefat"  id="<?php echo esc_attr( $this->get_field_id( 'ad_code' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ad_code' ) ); ?>" cols="30" rows="10"><?php echo esc_attr( $ad_code ); ?></textarea>

		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ad_position' ) ); ?>"><?php esc_attr_e( 'Ad Position', 'ads-for-wp' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'ad_position' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ad_position' ) ); ?>">
				<option value="1" <?php selected( $ad_position, 1 ); ?>><?php esc_attr_e( 'Default', 'ads-for-wp' ); ?>  </option>
				<option value="2" <?php selected( $ad_position, 2 ); ?>><?php esc_attr_e( 'Homepage', 'ads-for-wp' ); ?>  </option>
				<option value="3" <?php selected( $ad_position, 3 ); ?>><?php esc_attr_e( 'Single', 'ads-for-wp' ); ?>  </option>
			</select>
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
		$instance['title'] 			= ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['ad_code'] 		= ( ! empty( $new_instance['ad_code'] ) ) ?  $new_instance['ad_code'] : '';
		$instance['ad_position'] 	= ( ! empty( $new_instance['ad_position'] ) ) ? strip_tags( $new_instance['ad_position'] ) : '';

		return $instance;
	}

} 