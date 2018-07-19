<?php

class ads_for_wp_metaboxes_ads_type_metabox {
	private $screen = array(		
            'ads-for-wp-ads'                                                      
	);
	private $meta_fields = array(
		array(
			'label' => 'Ad Type',
			'id' => 'select_adtype',
			'type' => 'select',                        
			'options' => array(
				'Select Ad Type',
				'AdSense',
				'Custom',
			),
                                'attributes' => array(				
                                'required' => 'required',                                
				
			),
		),
		array(
			'label' => 'Custom Code',
			'id' => 'custom_code',
			'type' => 'textarea',
		),
		array(
			'label' => 'Data Client ID',
			'id' => 'data_client_id',
			'type' => 'text',
                        'attributes' => array(
				'placeholder' => 'ca-pub-2005XXXXXXXXX342',
                                'maxlength' => '30',                                
				
			),
		),
		array(
			'label' => 'Data Ad Slot',
			'id' => 'data_ad_slot',
			'type' => 'text',
                        'attributes' => array(
				'placeholder' => '70XXXXXX12',
                                'maxlength' => '20',
				
			),
		),
		array(
			'label' => 'Size',
			'id' => 'banner_size',
			'type' => 'select',
			'options' => array(
                                '' => 'Select Size',
				'728x90' => 'leaderboard (728x90)',
				'468x60' =>'banner (468x60)',
				'234x60'=>'half banner (234x60)' ,
				'125x125'=>'button (125x125)',
				'120x600'=>'skyscraper (120x600)',
				'160x600'=>'wide skyscraper (160x600)',
				'180x150'=>'small rectangle (180x150)',
				'120x240'=>'vertical banner (120x240)',
				'200x200'=>'small square (200x200)',
				'250x250'=>'square (250x250)',
				'300x250'=>'medium rectangle (300x250)',
				'336x280'=>'large rectangle (336x280)',
				'300x600'=>'half page (300x600)',
				'300x1050'=>'portrait (300x1050)',
				'320x50'=>'mobile banner (320x50)',
				'970x90'=>'large leaderboard (970x90)',
				'970x250'=>'billboard (970x250)',
			),
		),
	);
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_fields' ) );
	}
	public function add_meta_boxes() {
		foreach ( $this->screen as $single_screen ) {
			add_meta_box(
				'adtype',
				__( 'Ad Type', 'ads-for-wp' ),
				array( $this, 'meta_box_callback' ),
				$single_screen,
				'normal',
				'default'
			);
		}
                
	}
	public function meta_box_callback( $post ) {
		wp_nonce_field( 'ads_for_wp_adtype_data', 'ads_for_wp_adtype_nonce' );
		$this->field_generator( $post );
	}
	public function field_generator( $post ) {
		$output = '';                
		foreach ( $this->meta_fields as $meta_field ) {
                    $attributes ='';
			$label = '<label for="' . $meta_field['id'] . '">' . esc_html__($meta_field['label'], 'ads-for-wp' ) . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( empty( $meta_value ) ) {
				$meta_value = isset($meta_field['default']); }
			switch ( $meta_field['type'] ) {
				case 'select':
                                    
                                    
                                        if(isset($meta_field['attributes'])){
                                      foreach ( $meta_field['attributes'] as $key => $value ) {
                                    
					$attributes .=  $key."=".'"'.$value.'"'.' ';                                        
					}
                                    }
                                    
					$input = sprintf(
						'<select id="%s" name="%s" %s>',
						$meta_field['id'],
						$meta_field['id'],
                                                $attributes    
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
				case 'textarea':
					$input = sprintf(
						'<textarea style="width: 100%%" id="%s" name="%s" rows="5">%s</textarea>',
						$meta_field['id'],
						$meta_field['id'],
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
						'<input %s id="%s" name="%s" type="%s" value="%s" %s>',
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						$meta_field['id'],
						$meta_field['id'],
						$meta_field['type'],
						$meta_value,
                                                $attributes
					);
			}
			$output .= $this->format_rows( $label, $input );
		}
		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
	}
	public function format_rows( $label, $input ) {
		return '<tr class=""><th>'.$label.'</th><td>'.$input.'</td></tr>';
	}
	public function save_fields( $post_id ) {
		if ( ! isset( $_POST['ads_for_wp_adtype_nonce'] ) )
			return $post_id;
		$nonce = $_POST['ads_for_wp_adtype_nonce'];
		if ( !wp_verify_nonce( $nonce, 'ads_for_wp_adtype_data' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		foreach ( $this->meta_fields as $meta_field ) {
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
if (class_exists('ads_for_wp_metaboxes_ads_type_metabox')) {
	new ads_for_wp_metaboxes_ads_type_metabox;
};