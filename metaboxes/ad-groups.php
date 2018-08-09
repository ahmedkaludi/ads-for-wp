<?php
class adsforwp_metaboxes_ad_groups {
	private $screen = array(
		'adsforwp-groups',
	);
        private $ads_list = array();       
        private $meta_fields = array(	
                array(
			'label' => 'Usage',
			'id' => 'adsforwp_group_shortcode',
			'type' => 'text',
                        'attributes' => array(				
                               'readonly' 	=> 'readonly',	
                               'disabled' 	=> 'disabled',
                               'class' => 'afw_manual_ads_type',
			),
                        
		),
		array(
			'label' => 'Sorting',
			'id' => 'adsforwp_group_type',
			'type' => 'radio',
			'options' => array(
				'Random ads',
				'Ordered ads ',
			),
		),		
		array(
			'label' => 'Refresh Interval',
			'id' => 'adsforwp_group_ref_interval',
			'type' => 'checkbox',
		),
		array(			
			'id' => 'adsforwp_group_ref_interval_sec',
			'type' => 'number',
		),
                 array(
			'label' => 'Ads',     
                        'id'    => 'adsforwp_ads',
                        'class' => 'afw-option afw-option-group-ads',
			'type' => 'div',
                        
		),
		array(
			'label' => 'Ad New',
			'id' => 'adsforwp_group_ad_list',
			'type' => 'select',
                        
		),
                
               
	);
	public function __construct() {
                
                $all_ads = get_posts(
                    array(
                            'post_type' 	 => 'adsforwp',
                            'posts_per_page' => -1,
                            'post_status' => 'publish',                              
                    )
                 ); 
                
                 foreach($all_ads as $ad){
                     $this->ads_list[] =  array(
                               'ad_id' => $ad->ID,
                               'ad_name' => $ad->post_title
                               );         
                 }                 
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_fields' ) );                                
                
	}
	public function add_meta_boxes() {
		foreach ( $this->screen as $single_screen ) {
			add_meta_box(
				'adgroup',
				__( 'Ad Group', 'ads-for-wp' ),
				array( $this, 'meta_box_callback' ),
				$single_screen,
				'advanced',
				'low'
			);
		}
	}
	public function meta_box_callback( $post ) {
		wp_nonce_field( 'adgroup_data', 'adgroup_nonce' );
		$this->field_generator( $post );
	}
	public function field_generator( $post ) {
		$output = '';                    
		foreach ( $this->meta_fields as $meta_field ) {
			$id =''; 
                        $attributes ='';
                        $metafieldlabel ='';
                        if(array_key_exists('id', $meta_field)){
                          $id  =$meta_field['id'];
                        }
                        if(array_key_exists('label', $meta_field)){
                          $metafieldlabel  =$meta_field['label'];
                        }
                        $label = '<label for="' . $id . '">' . $metafieldlabel . '</label>';
			$meta_value = get_post_meta( $post->ID, $id, true );                           
			if ( empty( $meta_value ) ) {
				$meta_value = isset($meta_field['default']); }
			switch ( $meta_field['type'] ) {
				case 'checkbox':
                                    switch ($meta_field['id']) {
                                        case 'adsforwp_group_ref_interval':
                                            $input = sprintf(
						'<input %s id="%s" name="%s" type="checkbox" value="1">Enabled',
						$meta_value === '1' ? 'checked' : '',
						$id,
						$id
						);
                                            break;                                        
                                        default:
                                            $input = sprintf(
						'<input %s id="%s" name="%s" type="checkbox" value="1">',
						$meta_value === '1' ? 'checked' : '',
						$id,
						$id
						);
                                            break;
                                        }					
					break;
                                case 'div':
                                            $input = '<div id="'.esc_attr($meta_field['id']).'" class="'.esc_attr($meta_field['class']).'"> <table class="afw-group-ads">';                                            
                                            $input .= '<tbody>';   
                                            if($meta_value){
                                              foreach($meta_value as $key => $val){
                                             $input .= '<tr class="afw-group-add-ad-list" name="adsforwp_ads['.esc_attr($key).']">'
                                                    . '<td>'.esc_html($val).'<input type="hidden" name="adsforwp_ads['.esc_attr($key).']" value="'.esc_attr($val).'"></td>' 
                                                    . '<td><button type="button" class="afw-remove-ad-from-group button">x</button></td>'                                                        
                                                    . '</tr>';  
                                              }                                            
                                              }
                                            $input .= '</tbody>';
                                            $input .= '</table>';
                                            $input .= '</div>';                                              
					
					break;    
				case 'radio':
					$input = '<fieldset>';
					$input .= '<legend class="screen-reader-text">' . $meta_field['label'] . '</legend>';
					$i = 0;
					foreach ( $meta_field['options'] as $key => $value ) {
						$meta_field_value = !is_numeric( $key ) ? $key : $value;
						$input .= sprintf(
							'<label ><input %s id="%s" name="% s" type="radio" value="% s"> %s</label>%s',
							$meta_value === $meta_field_value ? 'checked' : '',
							$id,
							$id,
							$meta_field_value,
							$value,
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
						'<select class="afw_select" id="%s" name="%s">',
						$id,
						$id
                                                );
                                                foreach($this->ads_list as $value){
                                                $meta_field_value = '['.$value['ad_id'].']';
						$input .= sprintf(
							'<option %s value="adsforwp_ads%s">%s</option>',
							$meta_value === $meta_field_value ? 'selected' : '',
							$meta_field_value,
							$value['ad_name']
						);
                                                }
                                                $input .= '</select><button type="button" class="button afw-ads-group-button">add</button>';
                                                break;

                                            default:
                                                $input = sprintf(
						'<select class="afw_select" id="%s" name="%s">',
						$id,
						$id
                                                );
                                                foreach ( $meta_field['options'] as $key => $value ) {
						$meta_field_value = !is_numeric( $key ) ? $key : $value;
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$meta_value === $meta_field_value ? 'selected' : '',
							$meta_field_value,
							$value
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
						'<input class="afw_input" %s id="%s" name="%s" type="%s" value="%s"> milliseconds'
                                                    . '<p class="description">Refresh ads on the same spot. Works when cache-busting is used.</p>',
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
						$id,
						$id,
						$meta_field['type'],
						$meta_value,
                                                $attributes    
					);
                                            break;
                                    }					
			}
			$output .= $this->format_rows( $label, $input );
		}
		echo '<table class="form-table afw-ads-groups-box-table"><tbody>' . $output . '</tbody></table>';
	}
	public function format_rows( $label, $input ) {
		return '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';
	}
	public function save_fields( $post_id ) {                                               
		if ( ! isset( $_POST['adgroup_nonce'] ) )
			return $post_id;
		$nonce = $_POST['adgroup_nonce'];
		if ( !wp_verify_nonce( $nonce, 'adgroup_data' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
                               
                    $adsforwp_ads_array = array();                     
                    $adsforwp_ads_array = array_map('sanitize_text_field', $_POST['adsforwp_ads']);                      
                    update_post_meta($post_id, 'adsforwp_ads', $adsforwp_ads_array);
                            
		foreach ( $this->meta_fields as $meta_field ) {
                    if($meta_field['id'] != 'adsforwp_ads'){                    
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
if (class_exists('adsforwp_metaboxes_ad_groups')) {
	new adsforwp_metaboxes_ad_groups;
};