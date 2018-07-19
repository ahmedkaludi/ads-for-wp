<?php 
class ads_for_wp_metaboxes_ads_visibility_metabx {
	private $screen = array(
		'post',
	);
	private $meta_fields = array(
		array(
			
			'id' => 'ads-for-wp-visibility',
			'type' => 'radio',
			'options' => array(
				'Show',
				'Hide',
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
				'showadsforcurrentpag',
				__( 'Show Ads for Current Page?', 'ads-for-wp' ),
				array( $this, 'meta_box_callback' ),
				$single_screen,
				'side',
				'low'
			);
		}
	}
	public function meta_box_callback( $post ) {
		wp_nonce_field( 'ads_for_wp_showadscurrent_data', 'ads_for_wp_showadscurrent_nonce' );
		$this->field_generator( $post );
	}
	public function field_generator( $post ) {
		$output = '';
		foreach ( $this->meta_fields as $meta_field ) {
			$label = '';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );                        
			if ( empty( $meta_value ) ) {
				$meta_value = isset($meta_field['default']); 
                                if(empty($meta_value)){
                               $meta_value ='Show';   
                               }
                          }
			switch ( $meta_field['type'] ) {
				case 'radio':
                                        $meta_field_label = isset($meta_field['label']);
					$input = '<fieldset>';
					$input .= '<legend class="screen-reader-text">' . esc_html__($meta_field_label, 'ads-for-wp' ) . '</legend>';
					$i = 0;
					foreach ( $meta_field['options'] as $key => $value ) {
						$meta_field_value = !is_numeric( $key ) ? $key : $value;
                                               
						$input .= sprintf(
							'<label style="padding-right:10px;"><input %s id="% s" name="% s" type="radio" value="% s"> %s</label>%s',
							$meta_value === $meta_field_value ? 'checked' : '',
							$meta_field['id'],
							$meta_field['id'],
							$meta_field_value,
							$value,
							$i < count( $meta_field['options'] ) - 1 ? '' : ''
						);
						$i++;
					}
					$input .= '</fieldset>';
					break;
				default:
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s">',
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						$meta_field['id'],
						$meta_field['id'],
						$meta_field['type'],
						$meta_value
					);
			}
			$output .= $this->format_rows( $label, $input );
		}
		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
	}
	public function format_rows( $label, $input ) {
		return '<tr><td>'.$input.'</td></tr>';
	}
	public function save_fields( $post_id ) {
		if ( ! isset( $_POST['ads_for_wp_showadscurrent_nonce'] ) )
			return $post_id;
		$nonce = $_POST['ads_for_wp_showadscurrent_nonce'];
		if ( !wp_verify_nonce( $nonce, 'ads_for_wp_showadscurrent_data' ) )
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
if (class_exists('ads_for_wp_metaboxes_ads_visibility_metabx')) {
	new ads_for_wp_metaboxes_ads_visibility_metabx;
};