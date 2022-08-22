<?php 
$reasons = array(
    		1 => '<li><label><input type="radio" name="adsforwp_disable_reason" value="temporary"/>' . __('It is only temporary', 'ads-for-wp') . '</label></li>',
		2 => '<li><label><input type="radio" name="adsforwp_disable_reason" value="stopped"/>' . __('I stopped using Ads for WP on my site', 'ads-for-wp') . '</label></li>',
		3 => '<li><label><input type="radio" name="adsforwp_disable_reason" value="missing"/>' . __('I miss a feature', 'ads-for-wp') . '</label></li>
		<li><input class="mb-box missing" type="text" name="adsforwp_disable_text[]" value="" placeholder="Please describe the feature"/></li>',
		4 => '<li><label><input type="radio" name="adsforwp_disable_reason" value="technical"/>' . __('Technical Issue', 'ads-for-wp') . '</label></li>
		<li><textarea class="mb-box technical" name="adsforwp_disable_text[]" placeholder="' . __('How Can we help? Please describe your problem', 'ads-for-wp') . '"></textarea></li>',
		5 => '<li><label><input type="radio" name="adsforwp_disable_reason" value="another"/>' . __('I switched to another plugin', 'ads-for-wp') .  '</label></li>
		<li><input class="mb-box another" type="text" name="adsforwp_disable_text[]" value="" placeholder="Name of the plugin"/></li>',
		6 => '<li><label><input type="radio" name="adsforwp_disable_reason" value="other"/>' . __('Other reason', 'ads-for-wp') . '</label></li>
		<li><textarea class="mb-box other" name="adsforwp_disable_text[]" placeholder="' . __('Please specify, if possible', 'ads-for-wp') . '"></textarea></li>',
    );
shuffle($reasons);
?>


<div id="ads-for-wp-reloaded-feedback-overlay" style="display: none;">
    <div id="ads-for-wp-reloaded-feedback-content">
	<form action="" method="post">
	    <h3><strong><?php _e('If you have a moment, please let us know why you are deactivating:', 'ads-for-wp'); ?></strong></h3>
	    <ul>
                <?php 
                foreach ($reasons as $reason){
                    echo $reason;
                }
                ?>
	    </ul>
	    <?php if ($email) : ?>
    	    <input type="hidden" name="adsforwp_disable_from" value="<?php echo $email; ?>"/>
	    <?php endif; ?>
	    <input id="ads-for-wp-reloaded-feedback-submit" class="button button-primary" type="submit" name="ads-for-wp_disable_submit" value="<?php _e('Submit & Deactivate', 'ads-for-wp'); ?>"/>
	    <a class="button"><?php _e('Only Deactivate', 'ads-for-wp'); ?></a>
	    <a class="ads-for-wp-feedback-not-deactivate" href="#"><?php _e('Don\'t deactivate', 'ads-for-wp'); ?></a>
	</form>
    </div>
</div>