<?php
//Metabox to create display areas for ads in ads admin post
class adsforwp_view_display {
    
    private $screen = array(
		'adsforwp',
                'adsforwp-groups'
	);
	private $meta_fields = array(
		array(
			'label' => 'Where to display',
			'id' => 'wheretodisplay',
			'type' => 'select',
			'options' => array(
				'between_the_content' =>'Between the content',
				'after_the_content'   => 'After the content',
				'before_the_content'  => 'Before the content',
                                'ad_shortcode'  => 'Ad Shortcode',
			),
		),
		array(
			'label' => 'Position',
			'id' => 'adposition',
			'type' => 'select',
			'options' => array(
				'50_of_the_content'=>'50% of the content',
				'number_of_paragraph'=>'Number of paragraph',
			),
		),
		array(
			'label' => 'Paragraph',
			'id' => 'paragraph_number',
			'type' => 'number',
		),
               array(
			'label' => 'Manual Ad',
			'id' => 'manual_ads_type',
			'type' => 'text',                                
                        'attributes' => array(				
                               'readonly' 	=> 'readonly',	
                               'disabled' 	=> 'disabled',
                               'class' => 'afw_manual_ads_type',
			),
		),
            
                array(
			'label' => 'Alignment',
			'id' => 'adsforwp_ad_align',
			'type' => 'radio',
			'options' => array(
				'left'=>'Left',
				'center'=>'Center',
                                'right' => 'Right'
			),
		),
                array(		
                        'label' => 'Margin',
			'id' => 'adsforwp_ad_margin',                        
			'type' => 'multiple-text',
                        'fields'=> array(
                            array(	
                            'label' => 'Top',    
                            'id' => 'ad_margin_top',                        
                            'type' => 'number',
                          ),
                            array(	
                            'label' => 'Bottom',    
                            'id' => 'ad_margin_bottom',                        
                            'type' => 'number',
                          ),
                            array(	
                            'label' => 'Left',    
                            'id' => 'ad_margin_left',                        
                            'type' => 'number',
                          ),
                            array(	
                            'label' => 'Right',    
                            'id' => 'ad_margin_right',                        
                            'type' => 'number',
                          ),
                        )
		)
                                
	);
	public function __construct() {                                                                                                     
		add_action( 'add_meta_boxes', array( $this, 'adsforwp_add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'adsforwp_save_fields' ) );                               
                
	}
	public function adsforwp_add_meta_boxes() {                
                $post_type = get_post_type();                
		foreach ( $this->screen as $single_screen ) {                                                                    
			add_meta_box(
				'display-metabox',
				esc_html__( 'Display', 'ads-for-wp' ),
				array( $this, 'adsforwp_meta_box_callback' ),
				$single_screen,
				'normal',
				'low'
			);
		}
	}
	public function adsforwp_meta_box_callback( $post ) {
		wp_nonce_field( 'adsforwp_display_data', 'adsforwp_display_nonce' );
		$this->adsforwp_field_generator( $post );
	}
	public function adsforwp_field_generator( $post ) {
           
		$output = '';
		foreach ( $this->meta_fields as $meta_field ) {
                        $attributes ='';
			$label = '<label for="' . $meta_field['id'] . '">' . esc_html__( $meta_field['label'], 'ads-for-wp' ) . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( empty( $meta_value ) ) {
				$meta_value = isset($meta_field['default']); }
			switch ( $meta_field['type'] ) {
				case 'select':
					$input = sprintf(
						'<select class="afw_select" id="%s" name="%s">',
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
                                        case 'multiple-text':                                        
                                        $input ='<div class="afw_ad_img_margin">';                                       
                                        foreach($meta_field['fields'] as $field){
                                        $input.= sprintf(
						'<input class="afw_input" %s id="%s" name="adsforwp_ad_margin[%s]" type="%s" placeholder="%s" value="%s">',
						$meta_field['type'] !== 'color' ? '' : '',
						$field['id'],
						$field['id'],
						$field['type'],
						$field['label'],
                                                $meta_value[$field['id']]                                                 
                                             );                                        
                                        }
					$input .='</div>';
                                    break;
                                    case 'radio':
					$input = '<fieldset>';
					$input .= '<legend class="screen-reader-text">' . isset($meta_field['label']) . '</legend>';
					$i = 0;
					foreach ( $meta_field['options'] as $key => $value ) {
						$meta_field_value = !is_numeric( $key ) ? $key : $value;
                                                $checked ='';
                                             
                                                if($meta_value==''){
                                                    
                                                    if($key == 'left'){
                                                     $checked = 'checked';   
                                                    }
                                                }else{
                                                     $checked = $meta_value === $meta_field_value ? 'checked' : '';
                                                }
						$input .= sprintf(
							'<label style="padding-right:10px;"><input %s id="%s" name="% s" type="radio" value="% s"> %s</label>%s',
							$checked,
							$meta_field['id'],
							$meta_field['id'],
							$meta_field_value,
							esc_html__($value, 'ads-for-wp'),
							$i < count( $meta_field['options'] ) - 1 ? '' : ''
						);
						$i++;
					}
					$input .= '</fieldset>';
					break;
				default:
                                    
                                         if(isset($meta_field['attributes'])){
                                      foreach ( $meta_field['attributes'] as $key => $value ) {
                                    
					$attributes .=  $key."=".'"'.$value.'"'.' ';                                        
					}
                                       }
    
					$input = sprintf(
						'<input class="afw_input" %s id="%s" name="%s" type="%s" value="%s" %s>',
						$meta_field['type'] !== 'color' ? '' : '',
						$meta_field['id'],
						$meta_field['id'],
						$meta_field['type'],
						$meta_value,
                                                $attributes    
					);
			}
			$output .= $this->adsforwp_format_rows( $label, $input );
		}
                        $common_function_obj = new adsforwp_admin_common_functions();
                        $allowed_html = $common_function_obj->adsforwp_expanded_allowed_tags();                
                        $in_group = $common_function_obj->adsforwp_check_ads_in_group($post->ID);                               
                if(!empty($in_group)){
                        $group_links = '';
                        foreach($in_group as $group){                       
                        $group_post = get_post($group);                        
                        $group_links .= '<span style="padding-right:5px;"><a href="?post='.esc_attr($group).'&action=edit"> '.esc_html__($group_post->post_title, 'ads-for-wp').'</a>,</span> ';    
                        }
                        echo '<p>'.esc_html__('This ad is associated with groups. Go to the groups', 'ads-for-wp').' '.html_entity_decode(esc_html($group_links)).'</p>';   
                        echo '<table class="form-table" style="display:none;"><tbody>' . wp_kses($output, $allowed_html) . '</tbody></table>';      
                }else{
                        echo '<table class="form-table"><tbody>' . wp_kses($output, $allowed_html) . '</tbody></table>';   
                }
		                
	}
	public function adsforwp_format_rows( $label, $input ) {                                    
		return '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';                
	}
	public function adsforwp_save_fields( $post_id ) {
                            
		if ( ! isset( $_POST['adsforwp_display_nonce'] ) )
			return $post_id;		
		if ( !wp_verify_nonce( $_POST['adsforwp_display_nonce'], 'adsforwp_display_data' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
                
                $ad_margin = array();                     
                $ad_margin = array_map('sanitize_text_field', $_POST['adsforwp_ad_margin']);                      
                update_post_meta($post_id, 'adsforwp_ad_margin', $ad_margin);
                
		foreach ( $this->meta_fields as $meta_field ) {
                    if($meta_field['id'] != 'adsforwp_ad_margin'){ 
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
if (class_exists('adsforwp_view_display')) {
	new adsforwp_view_display;
};