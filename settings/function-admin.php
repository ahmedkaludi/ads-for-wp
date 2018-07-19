<?php 
add_action('admin_menu', 'ads_for_wp_menu');
function ads_for_wp_menu(){
    add_menu_page('My Page Title', 'Ads Settings', 'manage_options', 'ads_for_wp', 'ads_for_wp_settings_page1' );
    add_submenu_page('ads_for_wp', 'Page 1', 'Page1', 'manage_options', 'ads_for_wp', 'ads_for_wp_settings_page1' );
    add_submenu_page('ads_for_wp', 'Page 2', 'Page2', 'manage_options', 'ads_for_wp2', 'ads_for_wp_settings_page1');
    add_submenu_page('ads_for_wp', 'Page 3', 'Page3', 'manage_options', 'ads_for_wp3', 'ads_for_wp_settings_page1');       
}
function ads_for_wp_settings_page1() {
?>
<div class="wrap">
<h1>Your Plugin Name</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'my-cool-plugin-settings-group' ); ?>
    <?php do_settings_sections( 'my-cool-plugin-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">New Option Name</th>
        <td><input type="text" name="new_option_name" value="<?php echo esc_attr( get_option('new_option_name') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Some Other Option</th>
        <td><input type="text" name="some_other_option" value="<?php echo esc_attr( get_option('some_other_option') ); ?>" required="required"/></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Options, Etc.</th>
        <td><input type="text" name="option_etc" value="<?php echo esc_attr( get_option('option_etc') ); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>
