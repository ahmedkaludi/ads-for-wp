<?php
class adsforwp_view_ad_groups {
	private $screen = array(
		'adsforwp-groups',
	);
        private $ads_list = array();
        private $added_ad_list = array();
        private $meta_fields = array(	
                array(
			'label' => 'Usage',
			'id'    => 'adsforwp_group_shortcode',
			'type'  => 'text',
                        'attributes' => array(				
                               'readonly' 	=> 'readonly',	
                               'disabled' 	=> 'disabled',
                               'class'          => 'afw_manual_ads_type',
			),
                        
		),
		array(
			'label'   => 'Sorting',
			'id'      => 'adsforwp_group_type',
			'type'    => 'radio',
                        'default' => 'rand',
			'options' => array(
				'rand'    =>'Random ads',
				'ordered' =>'Ordered ads ',
			),
		),		
		array(
			'label'   => 'Refresh Type',
			'id'      => 'adsforwp_refresh_type',
			'type'    => 'select',
                        'options' => array(
                          'on_load'     => 'On Reload',
                          'on_interval' => 'Auto Refresh'  
                        )
		),
		array(			
			'id'   => 'adsforwp_group_ref_interval_sec',
			'type' => 'number',
		),
                 array(
			'label' => 'Ads',     
                        'id'    => 'adsforwp_ads',
                        'class' => 'afw-option afw-option-group-ads',
			'type'  => 'div',
                        
		),
		array(
			'label' => 'Ad New',
			'id'    => 'adsforwp_group_ad_list',
			'type'  => 'select',
                        
		),
                
               
	);
	public function __construct() {
                                                
		add_action( 'add_meta_boxes', array( $this, 'adsforwp_add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'adsforwp_save_fields' ) );                                
                
	}
	public function adsforwp_add_meta_boxes() {
		foreach ( $this->screen as $single_screen ) {
			add_meta_box(
				'adgroup',
				esc_html__( 'Ad Group', 'ads-for-wp' ),
				array( $this, 'adsforwp_meta_box_callback' ),
				$single_screen,
				'advanced',
				'low'
			);
		}
	}
	public function adsforwp_meta_box_callback( $post ) {
		wp_nonce_field( 'adgroup_data', 'adgroup_nonce' );
		$this->adsforwp_field_generator( $post );
	}
	public function adsforwp_field_generator( $post ) {
            
                $common_function_obj = new adsforwp_admin_common_functions();
                $all_ads = $common_function_obj->adsforwp_fetch_all_ads();
		$output = '';    
                
		foreach ( $this->meta_fields as $meta_field ) {
                    
			$id             = ''; 
                        $attributes     = '';
                        $metafieldlabel = '';
                        
                        if(array_key_exists('id', $meta_field)){
                          $id  = $meta_field['id'];
                        }
                        
                        if(array_key_exists('label', $meta_field)){
                          $metafieldlabel  = $meta_field['label'];
                        }
                        
                        $label = '<label for="' . esc_attr($id) . '">' . esc_html__($metafieldlabel, 'ads-for-wp' ) . '</label>';
			$meta_value = get_post_meta( $post->ID, $id, true );   
                        
			if ( empty( $meta_value ) ) {
				$meta_value = isset($meta_field['default']); 
                                
                        }
			switch ( $meta_field['type'] ) {
				case 'checkbox':                                   
                                            $input = sprintf(
						'<input %s id="%s" name="%s" type="checkbox" value="1">',
						$meta_value === '1' ? 'checked' : '',
						esc_attr($id),
						esc_attr($id)
						);                                         					
					break;
                                case 'div':
                                            $this->added_ad_list = $meta_value;                                            
                                            $input  = '<div id="'.esc_attr($meta_field['id']).'" class="'.esc_attr($meta_field['class']).'"> <table class="afw-group-ads">';                                            
                                            $input .= '<tbody>';  
                                            
                                            if($meta_value){
                                              foreach($meta_value as $key => $val){
                                             
                                               if(get_post_status($key) == 'publish'){
                                                  $input .= '<tr class="afw-group-add-ad-list" name="adsforwp_ads['.esc_attr($key).']">'
                                                    . '<td>'.esc_html($val).'<input type="hidden" name="adsforwp_ads['.esc_attr($key).']" value="'.esc_attr($val).'"></td>' 
                                                    . '<td><button type="button" class="afw-remove-ad-from-group button">x</button></td>'                                                        
                                                    . '</tr>';  
                                               }else{
                                                  $input .= '<tr class="afw-group-add-ad-list" name="adsforwp_ads['.esc_attr($key).']">'
                                                    . '<td>'.esc_html($val).'<input type="hidden" name="adsforwp_ads['.esc_attr($key).']" value="'.esc_attr($val).'"><p class="afw-error">'.esc_html__('This ad does not exist. Remove', 'ads-for-wp').'</p></td>' 
                                                    . '<td><button type="button" class="afw-remove-ad-from-group button">x</button></td>'                                                        
                                                    . '</tr>';  
                                               }   
                                                                                                                                                                                          
                                              }                                            
                                              }
                                              
                                            $input .= '</tbody>';
                                            $input .= '</table>';                                            
                                            $input .= '</div>';  
                                            $input .= '<a class="button afw_add_more" style="margin:20px;">'. esc_html__('Add More...', 'ads-for-wp').'</a>';
					
					break;  
                                        
				case 'radio':
                                    
                                        if($meta_value === 'ordered' || $meta_value === 'rand'){
                                        $default_val = $meta_value;                                            
                                        }else{
                                         $default_val = $meta_field['default'];  
                                        }                                        
					$input = '<fieldset>';
					$input .= '<legend class="screen-reader-text">' . esc_html__($meta_field['label'], 'ads-for-wp') . '</legend>';
					$i = 0;
					foreach ( $meta_field['options'] as $key => $value ) {
						$meta_field_value = !is_numeric( $key ) ? $key : $value;
						$input .= sprintf(
							'<label ><input %s id="%s" name="%s" type="radio" value="%s"> %s</label>%s',
							$default_val === $meta_field_value ? 'checked' : '',
							esc_attr($id),
							esc_attr($id),
							esc_attr($meta_field_value),
							esc_html__($value,'ads-for-wp'),
							$i < count( $meta_field['options'] ) - 1 ? '<br>' : ''
						);
						$i++;
					}
					$input .= '</fieldset>';
					break;
                                        
				case 'select':	
                                    
                                        switch ($id) {
                                    
                                            case 'adsforwp_group_ad_list':                                                  
                                                $input = sprintf(
						'<span class="afw-error afw-add-new-note">'. esc_html__('You have added all ads', 'ads-for-wp').'</span><br><select class="afw_select afw_group_ad_list" id="%s" name="%s">',
						esc_attr($id),
						esc_attr($id)
                                                );                                                                                                  

                                                foreach($all_ads as $ad){
                                                    
                                                    $this->ads_list[] =  array(
                                                              'ad_id'   => $ad->ID,
                                                              'ad_name' => $ad->post_title
                                                              );   
                                                    
                                                }                                                  
                                                foreach($this->ads_list as $value){  
                                                    
                                                if($this->added_ad_list){  
                                                    
                                                if(!array_key_exists($value['ad_id'], $this->added_ad_list)) { 
                                                    
                                                $meta_field_value = '['.$value['ad_id'].']';
						$input .= sprintf(
							'<option %s value="adsforwp_ads%s">%s</option>',
							$meta_value === $meta_field_value ? 'selected' : '',
							esc_attr($meta_field_value),
							esc_attr($value['ad_name'])
						);
                                                
                                                }
                                                
                                                }else{
                                                $meta_field_value = '['.$value['ad_id'].']';
						$input .= sprintf(
							'<option %s value="adsforwp_ads%s">%s</option>',
							$meta_value === $meta_field_value ? 'selected' : '',
							esc_attr($meta_field_value),
							esc_html__($value['ad_name'], 'ads-for-wp')
						);  
                                                }
                                                }
                                                $input .= '</select><button type="button" class="button afw-ads-group-button">'.esc_html__('add', 'ads-for-wp').'</button>';
                                                break;

                                            default:
                                                $input = sprintf(
						'<select class="afw_select" id="%s" name="%s">',
						esc_attr($id),
						esc_attr($id)
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
                                    switch ($meta_field['id']) {
                                        case 'adsforwp_group_ref_interval_sec':
                                            $input = sprintf(
						'<input class="afw_input" %s id="%s" name="%s" type="%s" value="%s"> '.esc_html__('milliseconds', 'ads-for-wp')
                                                    . '<p class="description">'. esc_html__('Refresh ads on the same spot', 'ads-for-wp').'</p>'
                                                    . '<p class="description">'.esc_html__('On AMP ads will be shown only on reload.', 'ads-for-wp').' <a href="#">'.esc_html__('why?', 'ads-for-wp').'</a></p>',
						$meta_field['type'] !== 'color' ? '' : '',
						$id,
						$id,
						$meta_field['type'],
						$meta_value
					);
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
						esc_attr($id),
						esc_attr($id),
						esc_attr($meta_field['type']),
						esc_attr($meta_value),
                                                $attributes    
					);
                                            break;
                                    }					
			}
			$output .= $this->adsforwp_format_rows( $label, $input );
		}
                $common_function_obj = new adsforwp_admin_common_functions();
                $allowed_html = $common_function_obj->adsforwp_expanded_allowed_tags();
		echo '<table class="form-table afw-ads-groups-box-table"><tbody>' . wp_kses($output, $allowed_html) . '</tbody></table>';
	}
	public function adsforwp_format_rows( $label, $input ) {
		return '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';
	}
	public function adsforwp_save_fields( $post_id ) {  
            
		if ( ! isset( $_POST['adgroup_nonce'] ) )
			return $post_id;		
		if ( !wp_verify_nonce( $_POST['adgroup_nonce'], 'adgroup_data' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
                  
                if ( current_user_can( 'manage_options' ) ) {
                    
                $post_meta = array();    
                
                $post_meta = $_POST; 
                
                $adsforwp_ads_array = array();    
                
                if(isset($_POST['adsforwp_ads']) && is_array($_POST['adsforwp_ads'])){
                    $adsforwp_ads_array = array_map('sanitize_text_field', $_POST['adsforwp_ads']);                      
                }
                                
                update_post_meta($post_id, 'adsforwp_ads', $adsforwp_ads_array);
                            
		foreach ( $this->meta_fields as $meta_field ) {
                    if($meta_field['id'] != 'adsforwp_ads'){                    
			if ( isset( $post_meta[ $meta_field['id'] ] ) ) {
				switch ( $meta_field['type'] ) {
					case 'email':
						$post_meta[ $meta_field['id'] ] = sanitize_email( $post_meta[ $meta_field['id'] ] );
						break;
					case 'text':
						$post_meta[ $meta_field['id'] ] = sanitize_text_field( $post_meta[ $meta_field['id'] ] );
						break;
				}
				update_post_meta( $post_id, $meta_field['id'], $post_meta[ $meta_field['id'] ] );
			} else if ( $meta_field['type'] === 'checkbox' ) {
				update_post_meta( $post_id, $meta_field['id'], '0' );
			}
                    }
                        
		}
        }
	}
}
if (class_exists('adsforwp_view_ad_groups')) {
	new adsforwp_view_ad_groups;
};