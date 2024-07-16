<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$reasons = array(
	1 => '<li><label><input type="radio" name="adsforwp_disable_reason" value="temporary"/>' . esc_html__( 'It is only temporary', 'ads-for-wp' ) . '</label></li>',
	2 => '<li><label><input type="radio" name="adsforwp_disable_reason" value="stopped"/>' . esc_html__( 'I stopped using Ads for WP on my site', 'ads-for-wp' ) . '</label></li>',
	3 => '<li><label><input type="radio" name="adsforwp_disable_reason" value="missing"/>' . esc_html__( 'I miss a feature', 'ads-for-wp' ) . '</label></li>
		<li><input class="mb-box missing" type="text" name="adsforwp_disable_text[]" value="" placeholder="' .esc_attr__( 'Please describe the feature', 'ads-for-wp' ). '"/></li>',
	4 => '<li><label><input type="radio" name="adsforwp_disable_reason" value="technical"/>' . esc_html__( 'Technical Issue', 'ads-for-wp' ) . '</label></li>
		<li><textarea class="mb-box technical" name="adsforwp_disable_text[]" placeholder="' . esc_attr__( 'How Can we help? Please describe your problem', 'ads-for-wp' ) . '"></textarea></li>',
	5 => '<li><label><input type="radio" name="adsforwp_disable_reason" value="another"/>' . esc_html__( 'I switched to another plugin', 'ads-for-wp' ) . '</label></li>
		<li><input class="mb-box another" type="text" name="adsforwp_disable_text[]" value="" placeholder="Name of the plugin"/></li>',
	6 => '<li><label><input type="radio" name="adsforwp_disable_reason" value="other"/>' . esc_html__( 'Other reason', 'ads-for-wp' ) . '</label></li>
		<li><textarea class="mb-box other" name="adsforwp_disable_text[]" placeholder="' . esc_attr__( 'Please specify, if possible', 'ads-for-wp' ) . '"></textarea></li>',
);
shuffle( $reasons );
?>


<div id="ads-for-wp-reloaded-feedback-overlay" style="display: none;">
	<div id="ads-for-wp-reloaded-feedback-content">
	<form action="" method="post">
		<h3><strong><?php esc_html_e( 'If you have a moment, please let us know why you are deactivating:', 'ads-for-wp' ); ?></strong></h3>
		<ul>
				<?php
				foreach ( $reasons as $reason_escaped ) {
					//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: Output is escaped in the esc_html__ function.
					echo $reason_escaped;
				}
				?>
		</ul>
		<?php if ( $email ) : ?>
			<input type="hidden" name="adsforwp_disable_from" value="<?php echo esc_attr( $email ); ?>"/>
		<?php endif; ?>
		<?php wp_nonce_field( 'adsforwp_deactivate_form', '_adsforwp_deactivate' ); ?>
		<input id="ads-for-wp-reloaded-feedback-submit" class="button button-primary" type="submit" name="ads-for-wp_disable_submit" value="<?php esc_attr_e( 'Submit & Deactivate', 'ads-for-wp' ); ?>"/>
		<a class="button"><?php esc_html_e( 'Only Deactivate', 'ads-for-wp' ); ?></a>
		<a class="ads-for-wp-feedback-not-deactivate" href="#"><?php esc_html_e( 'Don\'t deactivate', 'ads-for-wp' ); ?></a>
	</form>
	</div>
</div>
