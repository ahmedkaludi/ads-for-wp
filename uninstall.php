<?php
/**
 * Uninstall AMP For wp
 *
 */// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
delete_option('adsforwp_wisdom_block_notice');