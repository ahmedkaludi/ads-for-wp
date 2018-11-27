<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Adds Adsforwp_Ads_Widget widget.
 */
class Adsforwp_Ads_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'adsforwp_ads_widget', // Base ID
			esc_html__( 'Ads For WP Ads', 'ads-for-wp' ), // Name
			array( 'description' => esc_html__( 'Widget to display Ads', 'ads-for-wp' ), ) // Args
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
                          
		echo html_entity_decode(esc_attr($args['before_widget']));
//		if ( ! empty( $instance['title'] ) ) {
//			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
//		}
                
                
                $common_function_obj = new adsforwp_admin_common_functions();
                $all_ads = $common_function_obj->adsforwp_fetch_all_ads();
                $all_groups = $common_function_obj->adsforwp_fetch_all_groups();                                                 
                foreach($all_ads as $ad){
                    if($ad->ID == $instance['ads']){   
                            $output_function_obj = new adsforwp_output_functions();
                            $ad_code =  $output_function_obj->adsforwp_get_ad_code($instance['ads'], $type="AD");          
                            echo $ad_code;                                        
                    }     
                }
                foreach($all_groups as $group){
                 if($group->ID == $instance['ads']){   
                        $output_function_obj = new adsforwp_output_functions();
                        $widget = 'widget';
                        $ad_code =  $output_function_obj->adsforwp_group_ads($atts=null, $instance['ads'], $widget);                   
                        echo $ad_code;                        
                }    
                }
                echo html_entity_decode(esc_attr($args['after_widget']));		
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Ad title or group title', 'ads-for-wp' );
                $ads = ! empty( $instance['ads'] ) ? $instance['ads'] : esc_html__( 'ads list to be display', 'ads-for-wp' );                                
                
		?>

<!--		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                    <?php esc_attr_e( 'Title:', 'ads-for-wp' ); ?></label> 
		<input 
                    class="widefat" 
                    id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
                    name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
                    type="text" 
                    value="<?php echo esc_attr( $title ); ?>">
		</p>-->
                
                <p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'ads' ) ); ?>">
                    <?php esc_attr_e( 'Ads:', 'ads-for-wp' ); ?></label> 
                
                 <?php 
                 $ads_select_html ='';
                 $group_select_html ='';
                 $common_function_obj = new adsforwp_admin_common_functions();
                 $all_ads = $common_function_obj->adsforwp_fetch_all_ads();
                 $all_groups = $common_function_obj->adsforwp_fetch_all_groups();
                 
                 foreach($all_ads as $ad){
                     $ads_select_html .='<option '. esc_attr(selected( $ads, $ad->ID, false)).' value="'.esc_attr($ad->ID).'">'.esc_html__($ad->post_title, 'ads-for-wp').'</option>';
                 }
                 foreach($all_groups as $group){
                     $group_select_html .='<option '. esc_attr(selected( $ads, $group->ID, false)).' value="'.esc_attr($group->ID).'">'.esc_html__($group->post_title, 'ads-for-wp').'</option>';
                 }
                 $allow_html = $common_function_obj->adsforwp_expanded_allowed_tags();
                 echo '<select id="'.esc_attr( $this->get_field_id( 'ads' )).'" name="'.esc_attr( $this->get_field_name( 'ads' )).'">'
                         . '<optgroup label="Groups">'
                         . wp_kses($group_select_html, $allow_html)
                         . '<optgroup label="Ads">'
                         . wp_kses($ads_select_html, $allow_html)
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
		//$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
                $instance['ads'] = ( ! empty( $new_instance['ads'] ) ) ? sanitize_text_field( $new_instance['ads'] ) : '';                                
		return $instance;
	}

} // class Adsforwp_Ads_Widget