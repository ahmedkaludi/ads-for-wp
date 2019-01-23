<?php 
class adsforwp_view_expiredate {
	private $screen = array(
		'adsforwp',
	);
	private $meta_fields = array(
		array(
			'label' => 'Set Expire Date',
			'id' => 'adsforwp_ad_expire_enable',
			'type' => 'checkbox',
		),
		array(
			'label' => 'From',
			'id' => 'adsforwp_ad_expire_from',
			'type' => 'text',
		),
		array(
			'label' => 'To',
			'id' => 'adsforwp_ad_expire_to',
			'type' => 'text',
		),
              array(
			'label' => 'Set Specific Days',
			'id' => 'adsforwp_ad_expire_day_enable',
			'type' => 'checkbox',
		),
            array(
			'label' => 'Days',
			'id' => 'adsforwp_ad_expire_days',
			'type' => 'select',
                        'options' => array(
                            '0' => 'Monday',
                            '1' => 'Tuesday',
                            '2' => 'Wednesday',
                            '3' => 'Thursday',
                            '4' => 'Friday',
                            '5' => 'Saturday',
                            '6' => 'Sunday'
                        )
		),
//		array(
//			'label' => 'Time',
//			'id' => 'adsforwp_ad_expire_time',
//			'type' => 'select',
//			'options' => array(
//				'',
//			),
//		),
	);
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_fields' ) );                
                
                
                
                
	}
	public function add_meta_boxes() {
		foreach ( $this->screen as $single_screen ) {
			add_meta_box(
				'setexpiredate',
				esc_html__( 'Set Expire Date', 'ads-for-wp' ),
				array( $this, 'meta_box_callback' ),
				$single_screen,
				'side',
				'high'
			);
		}
	}
	public function meta_box_callback( $post ) {
		wp_nonce_field( 'setexpiredate_data', 'setexpiredate_nonce' );
		$this->field_generator( $post );
	}
	public function field_generator( $post ) {
		$output = '';                                 
		foreach ( $this->meta_fields as $meta_field ) {
			$label = '<label for="' . $meta_field['id'] . '">' . esc_html__($meta_field['label'], 'ads-for-wp') . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( empty( $meta_value ) ) {
				$meta_value = isset($meta_field['default']); }
			switch ( $meta_field['type'] ) {
				case 'checkbox':
					$input = sprintf(
						'<input %s id="%s" name="% s" type="checkbox" value="1">',
						$meta_value === '1' ? 'checked' : '',
						$meta_field['id'],
						$meta_field['id']
						);
					break;
				case 'select':
                                    
                                    
                                    switch ($meta_field['id']) {
                                        case 'adsforwp_ad_expire_time':
                                            $input = sprintf(
						'<select id="%s" name="%s">',
						$meta_field['id'],
						$meta_field['id']
					);                                            
                                            $start = "00:00"; //you can write here 00:00:00 but not need to it
                                            $end = "23:30";
                                            $tStart = strtotime($start);
                                            $tEnd = strtotime($end);
                                            $tNow = $tStart;
					    while($tNow <= $tEnd){
                                            if($meta_value == date("H:i:s",$tNow)){
                                            $input.= '<option value="'. esc_attr(date("H:i:s",$tNow)).'" selected>'. esc_attr(date("H:i:s",$tNow)).'</option>';    
                                            }else{
                                            $input.= '<option value="'. esc_attr(date("H:i:s",$tNow)).'">'. esc_attr(date("H:i:s",$tNow)).'</option>';    
                                            }                                            
                                            $tNow = strtotime('+30 minutes',$tNow);
                                        }                                        
					$input .= '</select>';
                                            break;
                                        
                                        case 'adsforwp_ad_expire_days':                                               
                                            $input = sprintf(
						'<select multiple id="%s" name="%s[]" style="height:146px; width:auto;">',
						$meta_field['id'],
						$meta_field['id']
                                                    
					);
                                        $specific_days = array();  
                                        if($meta_value){
                                        $specific_days = $meta_value;    
                                        }                                        
					foreach ( $meta_field['options'] as $key => $value ) {
						$meta_field_value = !is_numeric( $key ) ? $key : $value;                                                
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
                                                        in_array($meta_field_value, $specific_days) ? 'selected' : '',							
							$meta_field_value,
							esc_html__($value, 'ads-for-wp')
						);
					}
                                        
					$input .= '</select>';
                                            break;
                                        default:
                                            $input = sprintf(
						'<select id="%s" name="%s">',
						$meta_field['id'],
						$meta_field['id']
					);
					foreach ( $meta_field['options'] as $key => $value ) {
						$meta_field_value = !is_numeric( $key ) ? $key : $value;
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$meta_value === $meta_field_value ? 'selected' : '',
							$meta_field_value,
							esc_html__($value, 'ads-for-wp')
						);
					}
                                        
					$input .= '</select>';
                                            break;
                                    }
					
					break;
				default:
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s" readonly><span class="dashicons-before dashicons-calendar-alt %s_span"><span>',
						$meta_field['type'] !== 'color' ? 'style="width: 100%; background:#fff"' : '',
						$meta_field['id'],
						$meta_field['id'],
						$meta_field['type'],
						$meta_value,
                                                $meta_field['id']
					);
			}
			$output .= $this->format_rows( $label, $input );
		}
                $common_function_obj = new adsforwp_admin_common_functions();
                $allowed_html = $common_function_obj->adsforwp_expanded_allowed_tags();
		echo '<table class="afw-table-expire-ad form-table"><tbody>' . wp_kses($output, $allowed_html) . '</tbody></table>';
	}
	public function format_rows( $label, $input ) {
		return '<tr><td>'.$label.'</td><td>'.$input.'</td></tr>';
	}
	public function save_fields( $post_id ) {
		if ( ! isset( $_POST['setexpiredate_nonce'] ) )
			return $post_id;		
		if ( !wp_verify_nonce( $_POST['setexpiredate_nonce'], 'setexpiredate_data' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
                
                      
                    $adsforwp_days_array = array();                     
                    $adsforwp_days_array = array_map('sanitize_text_field', $_POST['adsforwp_ad_expire_days']);                      
                    update_post_meta($post_id, 'adsforwp_ad_expire_days', $adsforwp_days_array);
                    
		foreach ( $this->meta_fields as $meta_field ) {
                        if($meta_field['id'] != 'adsforwp_ad_expire_days'){ 
			if ( isset( $_POST[ $meta_field['id'] ] ) ) {
				switch ( $meta_field['type'] ) {
					case 'email':
						$_POST[ $meta_field['id'] ] = sanitize_email( $_POST[ $meta_field['id'] ] );
						break;
					case 'text':
						$_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
						break;
				}
				update_post_meta( $post_id, $meta_field['id'], $_POST[ $meta_field['id'] ] );
			} else if ( $meta_field['type'] === 'checkbox' ) {
				update_post_meta( $post_id, $meta_field['id'], '0' );
			}
                 }
		}
	}
}
if (class_exists('adsforwp_view_expiredate')) {
	new adsforwp_view_expiredate;
};