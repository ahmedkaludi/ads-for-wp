<?php
/*
  Metabox to show ads type such as custom and adsense 
 */
class adsforwp_view_ads_type {
        
	private $screen = array(		
            'adsforwp'                                                      
	);
        private $common_function = null;
        
	private $meta_fields = array(
		array(
			'label'   => 'Ad Type',
			'id'      => 'select_adtype',
			'type'    => 'select',                        
			'options' => array(
				''              => 'Select Ad Type',
				'adsense'       => 'AdSense',
                                'doubleclick'   => 'DoubleClick',
                                'media_net'     => 'Media.net',
                                'ad_now'        => 'AdNow',
                                'mgid'          => 'MGID',
                                'contentad'     => 'Content.ad',
                                'infolinks'     => 'Infolinks',
                                'mantis'     => 'MANTIS',
                                'outbrain'     => 'Outbrain',
                                'ad_image'      => 'Image Banner Ad',
                                'ad_background' => 'Background Ad',                                
                                'custom'        => 'Custom Code',
                            
			),
                                'attributes' => array(				
                                'required' => 'required',                                
				
			),
		),
		array(
			'label'     => 'Outbrain Type',
			'id'        => 'outbrain_type',
			'type'      => 'select',                        
			'options'   => array(				
				'normal'             => 'Normal',
                'outbrain_sticky_ads' => 'Sticky (Only AMP)'
			)
        ),
		array(
			'label'     => 'Widget Id\'s',
			'id'        => 'outbrain_widget_ids',
			'type'      => 'text',
			'attributes'=> array(
				'placeholder'   => 'widget_1,widget_2',
                'maxlength'     => '30',
			),
		),
		array(
			'label'     => 'MANTIS Type',
			'id'        => 'mantis_type',
			'type'      => 'select',
			'options'   => array(				
                'display'   => 'Display',
                'recommend' => 'Recommend'
			)
		),

        array(
			'label'     => 'AdSense Type',
			'id'        => 'adsense_type',
			'type'      => 'select',                        
			'options'   => array(				
				'normal'             => 'Normal',
                                'adsense_auto_ads'   => 'Auto Ads',
                                'adsense_sticky_ads' => 'Sticky (Only AMP)'
			)
                      ),
		array(
			'label'     => 'Custom Code',
			'id'        => 'custom_code',
			'type'      => 'textarea',
		),
               
		array(
			'label'     => 'Data Client ID',
			'id'        => 'data_client_id',
			'type'      => 'text',
                        'attributes'=> array(
				'placeholder'   => 'ca-pub-2005XXXXXXXXX342',
                                'maxlength'     => '30',                                
				
			),
		),
		array(
			'label'     => 'Data Ad Slot',
			'id'        => 'data_ad_slot',
			'type'      => 'text',
                        'attributes'=> array(
				'placeholder'   => '70XXXXXX12',
                                'maxlength'     => '20',
				
			),
		),
            //Media.net fields starts here
                array(
			'label'     => 'Data CID',
			'id'        => 'data_cid',
			'type'      => 'text',
                        'attributes'=> array(
				'placeholder'   => '8XXXXX74',
                                'maxlength'     => '20',
				
			),
		),
                array(
			'label'      => 'Data CRID',
			'id'         => 'data_crid',
			'type'       => 'text',
                        'attributes' => array(
				'placeholder'   => '1XXXXXX82',
                                'maxlength'     => '20',
				
			),
		),
            //Media.net fields ends here  
            
            //DoubleClick fields starts here
                array(
			'label'     => 'Slot Id',
			'id'        => 'dfp_slot_id',
			'type'      => 'text',
                        'attributes'=> array(
				'placeholder'   => '/41****9/mobile_ad_banner',
                                'maxlength'     => '50',
                                'provider_type' => 'adsforwp_dfp',
				
			),
		),
                array(
			'label'      => 'Div Gpt Ad',
			'id'         => 'dfp_div_gpt_ad',
			'type'       => 'text',
                        'attributes' => array(
				'placeholder'   => 'div-gpt-ad-*************-*',
                                'maxlength'     => '60',
                                'provider_type' => 'adsforwp_dfp',
				
			),
		),
                array(
			'label'     => 'Data Publisher',
			'id'        => 'adsforwp_mgid_data_publisher',                        
			'type'      => 'text',
                        'attributes' => array(
				'placeholder'   => 'site.com',                                                                
				
			),
		),
                array(
			'label'     => 'Data Widget',
			'id'        => 'adsforwp_mgid_data_widget',                        
			'type'      => 'text',
                        'attributes' => array(
				'placeholder'   => '123645',                                                                				
			),
		),
                array(
			'label'     => 'Data Container',
			'id'        => 'adsforwp_mgid_data_container',                        
			'type'      => 'text',
                        'attributes' => array(
				'placeholder'   => 'M87ScriptRootC123645',                                                                				
			),
		),
            
                array(
			'label'     => 'Data Js Src',
			'id'        => 'adsforwp_mgid_data_js_src',                        
			'type'      => 'text',
                        'note'      => 'Js is require to work in non AMP',
                        'attributes' => array(
				'placeholder'   => '//jsc.mgid.com/a/m/adsforwp.com.123645.js',                                                                				
			),
		),
            //DoubleClick fields ends here  
		array(
			'label'     => 'Size',
			'id'        => 'banner_size',
			'type'      => 'select',
			'options'   => array(
                                ''          =>  'Select Size',
				'728x90'    =>  'Leaderboard (728x90)',
				'468x60'    =>  'Banner (468x60)',
				'234x60'    =>  'Half Banner (234x60)' ,
				'125x125'   =>  'Button (125x125)',
				'120x600'   =>  'Skyscraper (120x600)',
				'160x600'   =>  'Wide Skyscraper (160x600)',
				'180x150'   =>  'Small Rectangle (180x150)',
				'120x240'   =>  'Vertical Banner (120x240)',
				'200x200'   =>  'Small Square (200x200)',
				'250x250'   =>  'Square (250x250)',
                                '200x50'    =>  'Rectangle (200x50)',
				'300x250'   =>  'Medium Rectangle (300x250)',
				'336x280'   =>  'Large Rectangle (336x280)',
				'300x600'   =>  'Half Page (300x600)',
				'300x1050'  =>  'Portrait (300x1050)',
				'320x50'    =>  'Mobile Banner (320x50)',
                                '320x100'   =>  'Large Mobile Banner (320x100)',
				'970x90'    =>  'Large Leaderboard (970x90)',
				'970x250'   =>  'Billboard (970x250)',
                                '728x20'    =>  'Wide Horizontal (728x20)',
                                '600x120'   =>  'Horizontal (600x120)',
			),
		),
            
                 array(
			'label'     => 'Upload Ad Image',
			'id'        => 'adsforwp_ad_image',                        
			'type'      => 'media',
		),
                array(
			'label'     => 'Ad Anchor link',
			'id'        => 'adsforwp_ad_redirect_url',                        
			'type'      => 'text',
		),
              
                array(
			'label'     => 'AdNow Widget ID',
			'id'        => 'ad_now_widget_id',                        
			'type'      => 'text',
		),            
                array(
			'label'     => 'ID',
			'id'        => 'contentad_id',                        
			'type'      => 'text',
		),
                array(
			'label'     => 'D',
			'id'        => 'contentad_id_d',                        
			'type'      => 'text',
		),
                array(
			'label'     => 'Content Ad Widget ID',
			'id'        => 'contentad_widget_id',                        
			'type'      => 'text',
		),             
                array(
			'label'     => 'Infolinks P ID',
			'id'        => 'infolinks_pid',                        
			'type'      => 'text',
		),
                array(
			'label'     => 'Infolinks W S ID',
			'id'        => 'infolinks_wsid',                        
			'type'      => 'text',
		),
                array(
			'label'     => 'Upload Ad Image',
			'id'        => 'ad_background_image',                        
			'type'      => 'media',
		),
                array(
			'label'     => 'Ad Anchor link',
			'id'        => 'ad_background_redirect_url',                        
			'type'      => 'text',
		),
                array(
			'label'     => 'Responsive',
			'id'        => 'adsforwp_ad_responsive',                        
			'type'      => 'checkbox',
		),
                array(			
                        'id'        => 'adsforwp_ad_img_height',                        
                        'type'      => 'hidden',
                    ),
                array(			
			'id'        => 'adsforwp_ad_img_width',                        
			'type'      => 'hidden',
		),
	);
	public function __construct() {
                
                if($this->common_function == null){
                    $this->common_function = new adsforwp_admin_common_functions();
                }
            
		add_action( 'add_meta_boxes', array( $this, 'adsforwp_add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'adsforwp_save_fields' ) );
	}
	public function adsforwp_add_meta_boxes() {
		foreach ( $this->screen as $single_screen ) {
			add_meta_box(
				'adtype',
				esc_html__( 'Ad Type', 'ads-for-wp' ),
				array( $this, 'adsforwp_meta_box_callback' ),
				$single_screen,
				'normal',
				'high'
			);
		}
                
	}
	public function adsforwp_meta_box_callback( $post ) {
		wp_nonce_field( 'adsforwp_adtype_data', 'adsforwp_adtype_nonce' );
		$this->adsforwp_field_generator( $post );
	}
	public function adsforwp_field_generator( $post ) {
		$output = '';                     
		foreach ( $this->meta_fields as $meta_field ) {
                    
                    $attributes = $provider_type = $label = '';
                    
                    if(isset($meta_field['label'])){
                      $label =  $meta_field['label']; 
                    }
			$label = '<label for="' . $meta_field['id'] . '">' . esc_html__( $label, 'ads-for-wp' ) . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( empty( $meta_value ) ) {
				$meta_value = isset($meta_field['default']); 
                                
                        }
                        
                        if(isset($meta_field['attributes'])){
                            
                            if(array_key_exists('provider_type', $meta_field['attributes'])){
                                
                               $provider_type = $meta_field['attributes']['provider_type']; 
                                
                            }
                            
                            
                        }
                        
			switch ( $meta_field['type'] ) {
				case 'select':                                                                        
                    
                                        if(isset($meta_field['attributes'])){
                                            
                                                foreach ( $meta_field['attributes'] as $key => $value ) {
                                                        $attributes .= esc_attr($key)."=".'"'.esc_attr($value).'"'.' ';
                                                }
                                                
                                        }
                                    
					$input = sprintf(
						'<select class="afw_select" id="%s" name="%s" %s>',
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id']),
                                                $attributes    
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
                                        switch($meta_field['id']){
                                            case 'select_adtype':
                                                $input .= '</select><span style="cursor:pointer;float:right;" class="afw_pointer dashicons-before dashicons-editor-help" id="afw_data_cid_pointer"></span>';
                                                break; 
                                            case 'adsense_type':
                                                $input .= '</select><p class="afw_adsense_auto_note afw_hide">'.esc_html__('You have already added Adsense Auto Ad.', 'ads-for-wp').' <a class="afw_adsense_auto">'.esc_html__('Edit' ,'ads-for-wp').'</a></p>';
                                                break; 
                                            default:
                                                $input .= '</select>';
                                               break;
                                        }
					
					break;
				case 'textarea':
					$input = sprintf(
						'<textarea class="afw_textarea" id="%s" name="%s" rows="5">%s</textarea>',
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id']),
						esc_textarea($meta_value)
					); 
                                    break;
                                case 'checkbox':
					$input = sprintf(
						'<input %s id="%s" name="%s" type="checkbox" value="1">',
						$meta_value === '1' ? 'checked' : '',
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id'])
						);
					break;
                                case 'media':
                                                                            
                                            if($meta_field['id'] == 'adsforwp_ad_image'){
                                                
                                                $imageprev ='';
                                                if($meta_value){
                                                 $imageprev .='<br><div class="afw_ad_thumbnail">';
                                                 $imageprev .='<img class="afw_ad_image_prev" src="'.esc_url($meta_value).'"/>';
                                                 $imageprev .='<a href="#" class="afw_ad_prev_close">X</a>';
                                                 $imageprev .='</div>';
                                                 
                                                }
                                                $input = sprintf(
						'<input class="afw_input adsforwp-icon" type="text" name="%s" id="%s" value="%s"/>'
                                                . '<button type="button" class="button adsforwp-ad-img-upload" data-editor="content">'
                                                . '<span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> '.esc_html__('Upload Image' ,'ads-for-wp').''
                                                . '</button>'
                                                . '<div class="afw_ad_img_div">%s'
                                                . '</div>',
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id']),
						$meta_value,
                                                $imageprev        
                                             );
                                                
                                            }else{
                                               
                                                $media_value      = array();
                                                $media_thumbnail  ='';
                                                $media_height     ='';
                                                $media_width      = '';
                                                $media_key        = esc_attr($meta_field['id']).'_detail';                                                 
                                                $media_value_meta = get_post_meta( $post->ID, $media_key, true );   
                                                
                                                if(!empty($media_value_meta)){
                                                $media_value =$media_value_meta;  
                                                }                                                                                                
                                                if(isset($media_value['thumbnail'])){
                                                     $media_thumbnail =$media_value['thumbnail'];
                                                }
                                                if(isset($media_value['height'])){
                                                     $media_height =$media_value['height']; 
                                                }
                                                if(isset($media_value['width'])){
                                                     $media_width =$media_value['width'];
                                                }                                                
                                                $imageprev ='';
                                                if(isset($media_value_meta['thumbnail'])){
                                                 $imageprev .='<br><div class="afw_ad_thumbnail">';
                                                 $imageprev .='<img class="afw_ad_image_prev" src="'.esc_url($media_value_meta['thumbnail']).'"/>';
                                                 $imageprev .='<a href="#" class="afw_ad_prev_close">X</a>';
                                                 $imageprev .='</div>';
                                                 
                                                }                                                
                                                $input = sprintf(
						'<fieldset>'
                                                . '<input class="afw_input" id="%s" name="%s" type="text" value="%s">'
                                                . '<input media-id="media" style="width: 19%%" class="button" id="%s_button" name="%s_button" type="button" value="Upload" />'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_height" class="upload-height" name="'.esc_attr($meta_field['id']).'_height" id="'.esc_attr($meta_field['id']).'_height" value="'.esc_attr($media_height).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_width" class="upload-width" name="'.esc_attr($meta_field['id']).'_width" id="'.esc_attr($meta_field['id']).'_width" value="'.esc_attr($media_width).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_thumbnail" class="upload-thumbnail" name="'.esc_attr($meta_field['id']).'_thumbnail" id="'.esc_attr($meta_field['id']).'_thumbnail" value="'.esc_attr($media_thumbnail).'">'                                                
                                                .'</fieldset>'
                                                . '<div class="afw_ad_img_div">%s'
                                                . '</div>',
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id']),
						$media_thumbnail,
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id']),
                                                $imageprev        
                                            );
                                                
                                            }
                                                                                                                        
                                                break;
                                case 'hidden':                                                    
                                                $input = sprintf(
						'<input id="%s" name="%s" type="hidden" value="%s">',                                                
						esc_attr($meta_field['id']),	
                                                esc_attr($meta_field['id']),    
						$meta_value                                                     
                                                );
                                                break;            
				default:
                                      
                                    if(isset($meta_field['attributes'])){
                                        
                                      foreach ( $meta_field['attributes'] as $key => $value ) {
                                    
					$attributes .= esc_attr($key)."=".'"'.esc_attr($value).'"'.' ';                                        
                                        
					}
                                        
                                    }
    
                                     $input = sprintf(
						'<input class="afw_input" %s id="%s" name="%s" type="%s" value="%s" %s>',
						$meta_field['type'] !== 'color' ? '' : '',
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id']),
						esc_attr($meta_field['type']),
						esc_attr($meta_value),
                                                $attributes
                                             );
                                        
					
			}
                        $note = '';
                        
                        if(isset($meta_field['note'])){
                            $note = '<p>'.$meta_field['note'].'</p>';
                        }
                        
                        $input = $input.$note;
                        
			$output .= $this->adsforwp_format_rows( $label, $input, $provider_type );
		}
                //$output variable's html is already sanitized in above loop.                                                                                		                                
		echo '<table class="form-table adsforwp-ad-type-table"><tbody>' . $output. '</tbody></table>';
	}
	public function adsforwp_format_rows( $label, $input, $provider_type ) {
                                                        
		return '<tr class="'.esc_attr($provider_type).'"><th>'.$label.'</th><td>'.$input.'</td></tr>';
	}                
	public function adsforwp_save_fields( $post_id ) { 
            
		if ( ! isset( $_POST['adsforwp_adtype_nonce'] ) )
			return $post_id;		
		if ( !wp_verify_nonce( $_POST['adsforwp_adtype_nonce'], 'adsforwp_adtype_data' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
                    
                if ( current_user_can( 'manage_options' ) ) {
                                        
                    $allowed_html = $this->common_function->adsforwp_expanded_allowed_tags(); 
                
			$post_meta = array();                    
			$post_meta = $_POST; // Sanitized below before saving
                
			foreach ( $this->meta_fields as $meta_field ) {

				if ( isset( $post_meta[ $meta_field['id'] ] ) ) {
					switch ( $meta_field['type'] ) {
						case 'email':
							$post_meta[ $meta_field['id'] ] = sanitize_email( $post_meta[ $meta_field['id'] ] );
							break;
						case 'text':
							$post_meta[ $meta_field['id'] ] = sanitize_text_field( $post_meta[ $meta_field['id'] ] );
							break;
                                                case 'textarea':                                                    
							$post_meta[ $meta_field['id'] ] = wp_unslash($post_meta[ $meta_field['id'] ]) ;
							break;    
						default:     
							$post_meta[ $meta_field['id'] ] = sanitize_text_field( $post_meta[ $meta_field['id'] ] );
					}
					if($meta_field['id'] == 'ad_background_image'){

						$media_key       = $meta_field['id'].'_detail';          
						$media_height    = sanitize_text_field( $post_meta[ $meta_field['id'].'_height' ] );
						$media_width     = sanitize_text_field( $post_meta[ $meta_field['id'].'_width' ] );
						$media_thumbnail = sanitize_text_field( $post_meta[ $meta_field['id'].'_thumbnail' ] );

						$media_detail = array(                                                    
						'height'    => $media_height,
						'width'     => $media_width,
						'thumbnail' => $media_thumbnail,
						);                                                    
						update_post_meta( $post_id, $media_key, $media_detail);
					} else {
						update_post_meta( $post_id, $meta_field['id'], $post_meta[ $meta_field['id'] ] );
					}
				} else if ( $meta_field['type'] === 'checkbox' ) {
					update_post_meta( $post_id, $meta_field['id'], '0' );
				}
			}
       	}
	}
}
if (class_exists('adsforwp_view_ads_type')) {
	new adsforwp_view_ads_type;
};