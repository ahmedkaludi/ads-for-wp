<?php
//Metabox to create display areas for ads in ads admin post
class adsforwp_view_display {
    
    private $screen = array(
		'adsforwp',
                'adsforwp-groups'
	);
	private $meta_fields = array(
		array(
			'label'    => 'Display Type',
			'id'       => 'wheretodisplay',
			'type'     => 'select',
            'optgroup' => array(
                'both-options'  => array(
                            'ad_shortcode'        => 'Shortcode (Manual)',
                            'between_the_content' => 'Between the Content (Automatic)',
                            'after_the_content'   => 'After the Content (Automatic)',
                            'before_the_content'  => 'Before the Content (Automatic)',
                            'custom_target'       => 'Custom Target',                                            
                            'sticky'              => 'Sticky',
                ),        
                'amp-options'  => array(
                            'adsforwp_after_featured_image'         => 'Ad after Featured Image',
                            'adsforwp_below_the_header'             => 'Below the Header (SiteWide)',
                            'adsforwp_below_the_footer'             => 'Below the Footer (SiteWide)',
                            'adsforwp_above_the_footer'             => 'Above the Footer (SiteWide)',
                            'adsforwp_above_the_post_content'       => 'Above the Post Content (Single Post)',
                            'adsforwp_below_the_post_content'       => 'Below the Post Content (Single Post)',                                            
                            'adsforwp_below_the_title'              => 'Below the Title (Single Post)',
                            'adsforwp_above_related_post'           => 'Above Related Posts (Single Post)',
                            'adsforwp_below_author_box'             => 'Below the Author Box (Single Post)',
                            'adsforwp_ads_in_loops'                 => 'Ads Inbetween Loop',
                )
            ),
		),                
		array(
			'label' => 'Position',
			'id' => 'adposition',
			'type' => 'select',
			'options' => array(
				'50_of_the_content'=>'Percent of the content',
				'number_of_paragraph'=>'Number of paragraph',
			),
            'required' => array(
                'type' => 'and',
                'fields'=> array('wheretodisplay' => 'between_the_content')
            ),
		),                
        array(
			'label' => 'Count As Per The',
			'id' => 'display_tag_name',
			'type' => 'select',
			'options' => array(                               
				'p_tag'=>'p (default)',
				'div_tag'=>'div', 
                'img_tag'=>'img',
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6',
                'custom_tag'=>'custom',
			),
            'required' => array(
                'type' => 'and',
                'fields'=> array('wheretodisplay' => 'between_the_content','adposition' => 'number_of_paragraph' )
            ),
		),
        array(
			'label' => 'Enter Your Tag',
			'id' => 'entered_tag_name',
			'type' => 'text',
                        'attributes' => array(				
                               'placeholder' 	=> 'div',	                               
			),
            'required' => array(
                'type' => 'and',
                'fields'=> array('wheretodisplay' => 'between_the_content','adposition' => 'number_of_paragraph','display_tag_name' => 'custom_tag')
            ),
		),
        array(
            'label' => 'Percent',
            'id' => 'percent_content',
            'type' => 'number',
            'default' => 50,
            'type' => 'number',
            'required' => array(
                'type' => 'and',
                'fields'=> array('wheretodisplay' => 'between_the_content','adposition' => '50_of_the_content' )
            ),
        ),
		array(
			'label' => 'Paragraph',
			'id' => 'paragraph_number',
			'type' => 'number',
            'required' => array(
                'type' => 'and',
                'fields'=> array('wheretodisplay' => 'between_the_content','adposition' => 'number_of_paragraph' )
            ),
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
            'required' => array(
                'type' => 'and',
                'fields'=> array('wheretodisplay' => 'ad_shortcode' )
            ),
		), 
        array(
			'label' => 'After How Many Posts?',
			'id' => 'adsforwp_after_how_many_post',
			'type' => 'number',
            'required' => array(
                'type' => 'and',
                'fields'=> array('wheretodisplay' => 'adsforwp_ads_in_loops' )
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
            'required' => array(
                'type' => 'and',
                'fields'=> array('wheretodisplay' => array('ad_shortcode','between_the_content','after_the_content','before_the_content','adsforwp_after_featured_image','adsforwp_below_the_header','adsforwp_below_the_footer','adsforwp_above_the_footer','adsforwp_above_the_post_content','adsforwp_below_the_post_content','adsforwp_below_the_title','adsforwp_above_related_post','adsforwp_below_author_box','adsforwp_ads_in_loops') )
            ),
		),
        array(
            'label' => 'Text Wrap Around',
            'id' => 'ads_text_wrap',
            'type' => 'checkbox',
            'required' => array(
                'type' => 'and',
                'fields'=> array('wheretodisplay' => 'between_the_content')
            ),
        ),
        array(
			'label' => 'Position',
			'id' => 'adsforwp_custom_target_position',
			'type' => 'radio',
			'options' => array(
				'existing_element'=>'Existing html element',
                                'new_element'=>'New html element',
                                
			),
            'required' => array(
                'type' => 'and',
                'fields'=> array('wheretodisplay' => 'custom_target')
            ),
		),
        array(
			'label' => 'jQuery Selector',
			'id' => 'adsforwp_jquery_selector',
			'type' => 'text',
			'attributes' => array(				                               	                               
                               'placeholder' => '#container_id or .container_id',
			),
		),
        array(
			'label' => 'New Element',
			'id' => 'adsforwp_new_element',
			'type' => 'text',                        
		),                
                array(
			'label' => 'Action',
			'id' => 'adsforwp_existing_element_action',
			'type' => 'select',
                        'options' => array(
                                'prepend_content' => 'Before Element',
				'append_content'  => 'Inside Element',                                                               
                        )
		),                                
        array(		
            'label'  => 'Margin',
			'id'     => 'adsforwp_ad_margin',                        
			'type'   => 'multiple-text',
                        'fields' => array(
                            array(	
                            'label' => 'Top',    
                            'id'    => 'ad_margin_top',                        
                            'type'  => 'number',
                          ),
                            array(	
                            'label' => 'Bottom',    
                            'id'    => 'ad_margin_bottom',                        
                            'type'  => 'number',
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
		),
        array(
            'label' => 'Floating Ad',
            'id' => 'adsforwp_ad_floating',
            'type' => 'checkbox',
            'required' => array(
                'type' => 'and',
                'fields'=> array('wheretodisplay' => array('ad_shortcode','between_the_content','after_the_content','before_the_content','custom_target','adsforwp_above_the_post_content','adsforwp_below_the_post_content') )
            ),
        ),
        array(
            'label' => 'Hide Ad Label',
            'id' => 'adsforwp_hide_ad_label',
            'type' => 'checkbox',
        )
                                
	);
	public function __construct() {                                                                                                     
		add_action( 'add_meta_boxes', array( $this, 'adsforwp_add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'adsforwp_save_fields' ) );                               
                
	}
	public function adsforwp_add_meta_boxes() {                                              
		foreach ( $this->screen as $single_screen ) {                                                                    
			add_meta_box(
				'display-metabox',
				esc_html__( 'Display', 'ads-for-wp' ),
				array( $this, 'adsforwp_meta_box_callback' ),
				$single_screen,
				'normal',
				'high'
			);
		}
	}
	public function adsforwp_meta_box_callback( $post ) {
		wp_nonce_field( 'adsforwp_display_data', 'adsforwp_display_nonce' );
		$this->adsforwp_field_generator( $post );
	}
    public function adsforwp_display_metabox_fields(){
        $allmetafields = $this->meta_fields;
        return $allmetafields;
    }
	public function adsforwp_field_generator( $post ) {
           
		$output = '';
		foreach ( $this->meta_fields as $meta_field ) {
                        $attributes ='';
			$label = '<label for="' . esc_attr($meta_field['id']) . '">' . esc_html__( $meta_field['label'], 'ads-for-wp' ) . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( empty( $meta_value ) ) {
                if($meta_field['id'] == 'adsforwp_new_element'){
                 $meta_value = esc_html('<div id="'.md5(uniqid(rand(), true)).'"></div>');    
                }else{
                 if(isset($meta_field['default'])){
                  $meta_value = $meta_field['default'];    
                 }                                   
                }				                                                                
            }
			switch ( $meta_field['type'] ) {
				case 'select':      
                switch ($meta_field['id']) {
                    case 'adposition':
                        $input = sprintf(
    						'<select class="afw_select" id="%s" name="%s" onchange="adsforwp_get_display_metafields(this);">',
    						esc_attr($meta_field['id']),
    						esc_attr($meta_field['id'])
    					);
    					foreach ( $meta_field['options'] as $key => $value ) {
    						$meta_field_value = !is_numeric( $key ) ? $key : $value;
    						$input .= sprintf(
    							'<option %s value="%s">%s</option>',
    							$meta_value === $meta_field_value ? 'selected' : '',
    							esc_attr($meta_field_value),
    							esc_html__($value, 'ads-for-wp')                            
    						);
    					}
                        $input .= '</select><a href="#" class="adsforwp-advance-option-click">'. esc_html__('Advance Option', 'ads-for-wp').'</a>';
                    break;              
                    case 'wheretodisplay':
                        if(is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php') || is_plugin_active('amp/amp.php')){
                            $opt_label = 'Full Support ( AMP & NON AMP )';
                        }else{
                            $opt_label = 'Display Type';
                            unset($meta_field['optgroup']['amp-options']);
                        }
                        $input = sprintf('<select class="afw_select" id="%s" name="%s" onchange="adsforwp_get_display_metafields(this);">',
    						esc_attr($meta_field['id']),
    						esc_attr($meta_field['id'])
					       );
                                            
                        foreach($meta_field['optgroup'] as $dtype => $options){
                            if($dtype =='both-options'){
                                $input .='<optgroup label="'.esc_html__($opt_label, 'ads-for-wp').'">';
                            }
                            if($dtype =='amp-options'){
                                $input .='<optgroup label="Partial Support ( AMP Only )">';
                            }                                                
                                               
                            foreach ( $options as $key => $value ) {
                                $meta_field_value = !is_numeric( $key ) ? $key : $value;
                                $input .= sprintf(
                                        '<option %s value="%s">%s</option>',
                                        $meta_value === $meta_field_value ? 'selected' : '',
                                        esc_attr($meta_field_value),
                                        esc_html__($value, 'ads-for-wp')
                                );
                            }
                            $input .='</optgroup>';
                                                
                        }
                        $input .= '</select>';
                        break;
                    default:
                        $input = sprintf('<select class="afw_select" id="%s" name="%s" %s onchange="adsforwp_get_display_metafields(this);">',
        						esc_attr($meta_field['id']),
        						esc_attr($meta_field['id']),
                                esc_attr($attributes)
    					   );
                        foreach ( $meta_field['options'] as $key => $value ) {
                            $meta_field_value = !is_numeric( $key ) ? $key : $value;
                            $input .= sprintf('<option %s value="%s">%s</option>',
                                            $meta_value === $meta_field_value ? 'selected' : '',
                                            $meta_field_value,
                                            esc_html__($value, 'ads-for-wp')
                                        );
                        }
                            $input .= '</select>';
                        break;
                }                                        					
			break;                      
            case 'multiple-text':                                        
                $input ='<div class="afw_ad_img_margin">';
                    foreach($meta_field['fields'] as $field){
                        $margin_value = '';   
                        if(!empty( $meta_value )){
                            $margin_value = $meta_value[$field['id']];                  
                        }                                               
                        $input.= sprintf('<input class="afw_input" %s id="%s" name="adsforwp_ad_margin[%s]" type="%s" placeholder="%s" value="%s">',
            						$meta_field['type'] !== 'color' ? '' : '',
            						esc_attr($field['id']),
            						esc_attr($field['id']),
            						esc_attr($field['type']),
            						esc_attr($field['label']),   
                                    esc_attr($margin_value)
                            );                                        
                    }
				$input .='</div>';
                break;
                case 'checkbox':
                    $input = sprintf(
                        '<input %s id="%s" name="%s" type="checkbox" value="1">',
                        $meta_value === '1' ? 'checked' : '',
                        esc_attr($meta_field['id']),
                        esc_attr($meta_field['id'])
                        );
                    switch($meta_field['id']){
                        case 'adsforwp_ad_floating':
                            $input .= '<span style="cursor:pointer;float:right;" class="afw_display_pointer dashicons-before dashicons-editor-help" id="afw_data_cid_pointer"></span>';
                            break;
                        default:
                           break;
                    }
                break;
                case 'radio':
                    switch ($meta_field['id']) {
                        case 'adsforwp_ad_align':
                            $input = '<fieldset class="afw_ad_align_field">';
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
                        case 'adsforwp_custom_target_position':
                            $input = '<fieldset class="adsforwp-custom-target-fields">';
                            $input .= '<legend class="screen-reader-text">' . esc_html__($meta_field['label'],'ads-for-wp') . '</legend>';
                            $i = 0;
                            foreach ( $meta_field['options'] as $key => $value ) {
                                $meta_field_value = !is_numeric( $key ) ? $key : $value;
                                $input .= sprintf(
                                        '<label style="padding-right:10px;"><input %s id="%s" name="%s" type="radio" value="%s">%s</label>%s',
                                        $meta_value === $meta_field_value ? 'checked' : '',
                                        esc_attr($meta_field['id']),
                                        esc_attr($meta_field['id']),
                                        esc_attr($meta_field_value),
                                        esc_html__($value,'ads-for-wp'),
                                        $i < count( $meta_field['options'] ) - 1 ? '' : ''
                                    );
                                $i++;
                            }
                            $input .= '</fieldset>';
                        break;
                        default:
                        break;
                    } 					
				break;
				default:
                    if(isset($meta_field['attributes'])){
                        foreach ( $meta_field['attributes'] as $key => $value ) {
                            $attributes .= esc_attr($key)."=".'"'.esc_attr($value).'"'.' ';
                        }
                    }
                    switch ($meta_field['id']) {
                       case 'paragraph_number':
                            $paragraphs_checked = '';
                            $paragraphs_number = 0;
                            $paragraphs_number = get_post_meta( $post->ID, 'ads_on_every_paragraphs_number', true );
                            if($paragraphs_number == 1 ){
                                $paragraphs_checked = 'checked';                
                            }
                            $input = sprintf('<input class="afw_input" %s id="%s" name="%s" type="%s" value="%s" %s> <input type="checkbox" id="ads_on_every_paragraphs_number" name="ads_on_every_paragraphs_number" value="1" '.esc_attr($paragraphs_checked).'> <span class="adsforwp-every-paragraphs-text"></span>',
            						$meta_field['type'] !== 'color' ? '' : '',
            						esc_attr($meta_field['id']),
            						esc_attr($meta_field['id']),
            						esc_attr($meta_field['type']),
            						esc_attr($meta_value),
                                    esc_attr($attributes)    
					            );

                        break;
                        default:
                            $input = sprintf('<input class="afw_input" %s id="%s" name="%s" type="%s" value="%s" %s>',
            						$meta_field['type'] !== 'color' ? '' : '',
            						esc_attr($meta_field['id']),
            						esc_attr($meta_field['id']),
            						esc_attr($meta_field['type']),
            						esc_attr($meta_value),
                                    esc_attr($attributes)    
					            );
                        break;
                    }	
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
                $group_links .= '<span style="padding-right:5px;"><a href="?post='.esc_attr($group).'&action=edit"> '.esc_html__($group_post->post_title, 'ads-for-wp').'</a>,</span>';    
                }
                echo '<p>'.esc_html__('This ad is associated with ', 'ads-for-wp').''.html_entity_decode(esc_html($group_links)).'group</p>';   
                echo '<table class="form-table" style="display:none;"><tbody>' . $output . '</tbody></table><div id="afw-embed-code-div"></div>';      
                
        }else{
                echo '<table class="form-table adsforwp-display-table"><tbody>' . $output . '</tbody></table><div style="display:none;" id="afw-embed-code-div"></div>';   
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
        
        if ( current_user_can( 'manage_options' ) ) {
                    
            $post_meta = array();                    
            $post_meta = $_POST; // Sanitized below before saving
            
            $ad_margin = array();    
            
            if(is_array($post_meta['adsforwp_ad_margin'])){
                $ad_margin = array_map('sanitize_text_field', $post_meta['adsforwp_ad_margin']);  
            }
                            
            update_post_meta($post_id, 'adsforwp_ad_margin', $ad_margin);
            
            if(isset($post_meta['ads_on_every_paragraphs_number'])){
                update_post_meta($post_id, 'ads_on_every_paragraphs_number', sanitize_text_field($post_meta['ads_on_every_paragraphs_number']));
            }else{
                update_post_meta($post_id, 'ads_on_every_paragraphs_number', '0');
            }
                
            foreach ( $this->meta_fields as $meta_field ) {
                if($meta_field['id'] != 'adsforwp_ad_margin'){
                    if ( isset( $post_meta[ $meta_field['id'] ] ) ) {
                        switch ( $meta_field['type'] ) {
                            case 'email':
                                $post_meta[ $meta_field['id'] ] = sanitize_email( $post_meta[ $meta_field['id'] ] );
                                break;
                            case 'text':
                                $post_meta[ $meta_field['id'] ] = sanitize_text_field( esc_html($post_meta[ $meta_field['id'] ]));
                                break;
                            default:     
                                $post_meta[ $meta_field['id'] ] = sanitize_text_field( $post_meta[ $meta_field['id'] ] );    
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
if (class_exists('adsforwp_view_display')) {
	new adsforwp_view_display;
};