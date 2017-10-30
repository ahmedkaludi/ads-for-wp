<?php
function adsforwp_text_field_0_render(  ) { 

	$options = get_option( 'adsforwp_settings' );
	?>
	<input type='text' name='adsforwp_settings[adsforwp_text_field_0]' value='<?php echo $options['adsforwp_text_field_0']; ?>'>
	<?php

}


function adsforwp_checkbox_field_1_render(  ) { 

	$options = get_option( 'adsforwp_settings' );
	?>
	<input type='checkbox' name='adsforwp_settings[adsforwp_checkbox_field_1]' <?php checked( $options['adsforwp_checkbox_field_1'], 1 ); ?> value='1'>
	<?php

}


function adsforwp_radio_field_2_render(  ) { 

	$options = get_option( 'adsforwp_settings' );
	?>
	<input type='radio' name='adsforwp_settings[adsforwp_radio_field_2]' checked="checked" <?php checked( $options['adsforwp_radio_field_2'], 1 ); ?> value='1'>
	<input type='radio' name='adsforwp_settings[adsforwp_radio_field_2]' <?php checked( $options['adsforwp_radio_field_2'], 2 ); ?> value='2'>
	<?php

}


function adsforwp_textarea_field_3_render(  ) { 

	$options = get_option( 'adsforwp_settings' );
	?>
	<textarea cols='40' rows='5' name='adsforwp_settings[adsforwp_textarea_field_3]'><?php echo $options['adsforwp_textarea_field_3']; ?></textarea>
	<?php

}


function adsforwp_select_field_4_render(  ) { 

	$options = get_option( 'adsforwp_settings' );
	?>
	<select name='adsforwp_settings[adsforwp_select_field_4]'>
		<option value='1' <?php selected( $options['adsforwp_select_field_4'], 1 ); ?>>Option 1</option>
		<option value='2' <?php selected( $options['adsforwp_select_field_4'], 2 ); ?>>Option 2</option>
	</select>

<?php

}
