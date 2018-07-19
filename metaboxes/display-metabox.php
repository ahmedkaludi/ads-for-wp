<?php

class ads_for_wp_metaboxes_display_metabox {
	private $screen = array(
		'ads-for-wp-ads'
	);
	private $meta_fields = array(
		array(
			'label' => 'Where to display',
			'id' => 'wheretodisplay',
			'type' => 'select',
			'options' => array(
				'Between the content',
				'After the content',
				'Before the content',
			),
		),
		array(
			'label' => 'Position',
			'id' => 'adposition',
			'type' => 'select',
			'options' => array(
				'50% of the content',
				'Number of paragraph',
			),
		),
		array(
			'label' => 'paragraph',
			'id' => 'paragraph_number',
			'type' => 'number',
		),
	);
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_fields' ) );
	}
	public function add_meta_boxes() {
		foreach ( $this->screen as $single_screen ) {
			add_meta_box(
				'display',
				__( 'Display', 'ads-for-wp' ),
				array( $this, 'meta_box_callback' ),
				$single_screen,
				'normal',
				'low'
			);
		}
	}
	public function meta_box_callback( $post ) {
		wp_nonce_field( 'ads_for_wp_display_data', 'ads_for_wp_display_nonce' );
		$this->field_generator( $post );
	}
	public function field_generator( $post ) {
		$output = '';
		foreach ( $this->meta_fields as $meta_field ) {
			$label = '<label for="' . $meta_field['id'] . '">' . esc_html__( $meta_field['label'], 'ads-for-wp' ) . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( empty( $meta_value ) ) {
				$meta_value = isset($meta_field['default']); }
			switch ( $meta_field['type'] ) {
				case 'select':
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
							$value
						);
					}
					$input .= '</select>';
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
		return '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';
	}
	public function save_fields( $post_id ) {
		if ( ! isset( $_POST['ads_for_wp_display_nonce'] ) )
			return $post_id;
		$nonce = $_POST['ads_for_wp_display_nonce'];
		if ( !wp_verify_nonce( $nonce, 'ads_for_wp_display_data' ) )
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
if (class_exists('ads_for_wp_metaboxes_display_metabox')) {
	new ads_for_wp_metaboxes_display_metabox;
};