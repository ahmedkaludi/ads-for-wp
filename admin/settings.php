<?php
class adsforwp_admin_settings{
    
public function __construct() {
      add_action( 'admin_menu', array($this, 'adsforwp_add_menu_links'));        
      add_action('admin_init', array($this, 'adsforwp_settings_init'));
      add_action('upload_mimes', array($this, 'adsforwp_custom_upload_mimes'));
    }
public function adsforwp_add_menu_links() {	
	// Settings page - Same as main menu page
	add_submenu_page( 'edit.php?post_type=adsforwp',
                esc_html__( 'Ads for wp', 'ads-for-wp' ),
                esc_html__( 'Settings', 'ads-for-wp' ),
                'manage_options',
                'adsforwp',
                array($this, 'adsforwp_admin_interface_render'));	
}


public function adsforwp_admin_interface_render(){
    
	// Authentication
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}     	       
	// Handing save settings
	if ( isset( $_GET['settings-updated'] ) ) {	
            $settings = adsforwp_defaultSettings();              
            $file_creation = new adsforwp_file_creation();
            if(isset($settings['ad_blocker_support'])){                
                $result = $file_creation->adsforwp_create_adblocker_support_js();                
            }else{
                $result = $file_creation->adsforwp_delete_adblocker_support_js(); 
            }
            
		settings_errors();
	    }
	       $tab = adsforwp_get_tab('general', array('general', 'support', 'tools', 'advance'));
        
	?>
		                            
		<h1><?php echo esc_html__('Ads for WP Settings', 'ads-for-wp'); ?></h1>
		<h2 class="nav-tab-wrapper adsforwp-tabs">
			<?php	
			echo '<a href="' . esc_url(adsforwp_admin_link('general')) . '" class="nav-tab ' . esc_attr( $tab == 'general' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('General','ads-for-wp') . '</a>';
                        
                        echo '<a href="' . esc_url(adsforwp_admin_link('tools')) . '" class="nav-tab ' . esc_attr( $tab == 'tools' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Tools', 'ads-for-wp') . '</a>';    
                        
                        echo '<a href="' . esc_url(adsforwp_admin_link('advance')) . '" class="nav-tab ' . esc_attr( $tab == 'advance' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Advanced', 'ads-for-wp') . '</a>';    
                        
                        echo '<a href="' . esc_url(adsforwp_admin_link('support')) . '" class="nav-tab ' . esc_attr( $tab == 'support' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Support','ads-for-wp') . '</a>';
                                                                                                                    			
			?>
		</h2>
                <form action="options.php" method="post" enctype="multipart/form-data" class="adsforwp-settings-form">		
			<div class="form-wrap">
			<?php
			// Output nonce, action, and option_page fields for a settings page.
			settings_fields( 'adsforwp_setting_dashboard_group' );												

			echo "<div class='adsforwp-general' ".( $tab != 'general' ? 'style="display:none;"' : '').">";
				// general Application Settings
		        do_settings_sections( 'adsforwp_general_section' );	// Page slug
			echo "</div>";                                                
                        
                        echo "<div class='adsforwp-tools' ".( $tab != 'tools' ? 'style="display:none;"' : '').">";
			// Status
			do_settings_sections( 'adsforwp_tools_section' );	// Page slug
			echo "</div>";
                        
                        echo "<div class='adsforwp-advance' ".( $tab != 'advance' ? 'style="display:none;"' : '').">";
			// Status
			do_settings_sections( 'adsforwp_advance_section' );	// Page slug
			echo "</div>";
                        
                        echo "<div class='adsforwp-support' ".( $tab != 'support' ? 'style="display:none;"' : '').">";
				// general Application Settings
		        do_settings_sections( 'adsforwp_support_section' );	// Page slug
			echo "</div>";

			?>
                        </div>
			<div class="button-wrapper">                            
				<?php
				// Output save settings button
			submit_button( esc_html__('Save', 'ads-for-wp') );
				?>
			</div>
		</form>
	</div>
        
	<?php
           
}
/*
	WP Settings API
*/
public function adsforwp_settings_init(){
	register_setting( 'adsforwp_setting_dashboard_group', 'adsforwp_settings', array($this, 'adsforwp_handle_file_upload'));
        
	        add_settings_section('adsforwp_tools_section',  'Migration', '__return_false', 'adsforwp_tools_section');		                                                                                                  
                    
                    add_settings_field(
                            'adsforwp_import_status',								// ID
                            '',			// Title
                             array($this, 'adsforwp_import_callback'),					// Callback
                            'adsforwp_tools_section',							// Page slug
                            'adsforwp_tools_section'							// Settings Section ID
                    );        
                    
                add_settings_section('adsforwp_advance_section',  'Advance Settings', '__return_false', 'adsforwp_advance_section');		                                                                                                  
                    
                    add_settings_field(
                            'adsforwp_advance_status',								// ID
                            '',			// Title
                             array($this, 'adsforwp_advance_callback'),					// Callback
                            'adsforwp_advance_section',							// Page slug
                            'adsforwp_advance_section'							// Settings Section ID
                    );    
                                                                    
                add_settings_section('adsforwp_general_section', 'Settings', '__return_false', 'adsforwp_general_section');		              
                    add_settings_field(
                            'adsforwp_ad_blocker_support',								// ID
                            'Ad Blocker Support',			// Title
                             array($this, 'adsforwp_ad_blocker_support_callback'),					// Callback
                            'adsforwp_general_section',							// Page slug
                            'adsforwp_general_section'							// Settings Section ID
                    );
                    
                add_settings_section('adsforwp_general_section', 'Settings', '__return_false', 'adsforwp_general_section');		              
                    add_settings_field(
                            'adsforwp_ad_revenue_sharing',								// ID
                            'Revenue Sharing',			// Title
                             array($this, 'adsforwp_ad_revenue_sharing_callback'),					// Callback
                            'adsforwp_general_section',							// Page slug
                            'adsforwp_general_section'							// Settings Section ID
                    );   
                    
                 add_settings_section('adsforwp_support_section', 'Contact Us', '__return_false', 'adsforwp_support_section');		              
                    add_settings_field(
                            'adsforwp_contact_us_form',								// ID
                            '',			// Title
                             array($this, 'adsforwp_contact_us_form_callback'),					// Callback
                            'adsforwp_support_section',							// Page slug
                            'adsforwp_support_section'							// Settings Section ID
                    );       
                          		               
}


public function adsforwp_custom_upload_mimes($mimes = array()) {
	
	$mimes['json'] = "application/json";

	return $mimes;
}
public function adsforwp_handle_file_upload($option)
{
  if(!empty($_FILES["adsforwp_import_backup"]["tmp_name"]))
  {
    $urls = wp_handle_upload($_FILES["adsforwp_import_backup"], array('test_form' => FALSE));      
    $url = $urls["url"];
    update_option('adsforwp-file-upload_url',esc_url($url));
  }
  
  return $option;
}

public function adsforwp_check_data_imported_from($plugin_post_type_name){
       $cc_args = array(
                        'posts_per_page'   => -1,
                        'post_type'        => 'adsforwp',
                        'meta_key'         => 'imported_from',
                        'meta_value'         => $plugin_post_type_name,
                    );					
	$imported_from = new WP_Query( $cc_args ); 
        return $imported_from;
}

public function adsforwp_advance_callback(){
    $settings = adsforwp_defaultSettings();
    
    ?>
            <ul>
                <li><div class="adsforwp-tools-field-title">
                        <div class="adsforwp-tooltip"><strong><?php echo esc_html__('IP Geolocation API','ads-for-wp'); ?></strong>
                        </div>
                        <input type="text" value="<?php if(isset($settings['adsforwp_geolocation_api'])){ echo $settings['adsforwp_geolocation_api']; } ?>" id="adsforwp-geolocation-api" name="adsforwp_settings[adsforwp_geolocation_api]">
                        <p><?php echo esc_html__('Note : They have free plan which gives you 50K requests per month. For all that you need to singup','ads-for-wp'); ?> <a href="https://ipgeolocation.io" target="_blank"><?php echo esc_html__('Links','ads-for-wp'); ?></a></p>
                        
                    </div>
                </li> 
                
            </ul>
        
    <?php    
    
}

public function adsforwp_import_callback(){
	$message = '<p>'.esc_html__('This plugin\'s data already has been imported. Do you want to import again?. click on button above button.','ads-for-wp').'</p>';
        $schema_message = '';
        $ampforwp_ads_message = '';
        $ampforwp_advanced_ads_message = '';
        $schema_plugin = $this->adsforwp_check_data_imported_from('advance_ads'); 
        $ampforwp_ads = $this->adsforwp_check_data_imported_from('ampforwp_ads'); 
        $ampforwp_advanced_ads = $this->adsforwp_check_data_imported_from('ampforwp_advanced_ads'); 
	if($schema_plugin->post_count !=0){
         $schema_message =$message;
        }
        if($ampforwp_ads->post_count !=0){
         $ampforwp_ads_message =$message;   
        }
        if($ampforwp_advanced_ads->post_count !=0){
         $ampforwp_advanced_ads_message =$message;   
        }
        ?>	
            <ul>
                <li><div class="adsforwp-tools-field-title"><div class="adsforwp-tooltip"><strong><?php echo esc_html__('Advanced Ads Plugin','ads-for-wp'); ?></strong></div><button data-id="advanced_ads" class="button adsforwp-import-plugins"><?php echo esc_html__('Start Importing','ads-for-wp'); ?></button>
                        <p class="adsforwp-imported-message"></p>
                        <?php echo $schema_message; ?>    
                    </div>
                </li> 
                <li><div class="adsforwp-tools-field-title"><div class="adsforwp-tooltip"><strong><?php echo esc_html__('AMP for WP Ads','ads-for-wp'); ?></strong></div><button data-id="ampforwp_ads" class="button adsforwp-import-plugins"><?php echo esc_html__('Start Importing','ads-for-wp'); ?></button>
                        <p class="adsforwp-imported-message"></p>
                        <?php echo $ampforwp_ads_message; ?>    
                    </div>
                </li>
                <li><div class="adsforwp-tools-field-title"><div class="adsforwp-tooltip"><strong><?php echo esc_html__('AMP for WP Advanced Ads','ads-for-wp'); ?></strong></div><button data-id="ampforwp_advanced_ads" class="button adsforwp-import-plugins"><?php echo esc_html__('Start Importing','ads-for-wp'); ?></button>
                        <p class="adsforwp-imported-message"></p>
                        <?php echo $ampforwp_advanced_ads_message; ?>    
                    </div>
                </li>
            </ul>                   
	<?php  
        echo '<h2>'.esc_html__('Import / Export','ads-for-wp').'</h2>'; 
        $url =  admin_url('admin-ajax.php?action=adsforwp_export_all_settings');
        ?>
        <ul>
                <li>
                    <div class="adsforwp-tools-field-title"><div class="adsforwp-tooltip"><strong><?php echo esc_html__('Export All Ads For WP Data','ads-for-wp'); ?></strong></div><a href="<?php echo esc_url($url); ?>"class="button adsforwp-export-data"><?php echo esc_html__('Export','ads-for-wp'); ?></a>                         
                    </div>
                </li> 
                <li>
                    <div class="adsforwp-tools-field-title"><div class="adsforwp-tooltip"><strong><?php echo esc_html__('Import All Ads For WP Data','ads-for-wp'); ?></strong></div><input type="file" name="adsforwp_import_backup" id="adsforwp_import_backup">                         
                    </div>
                </li> 
        </ul>
        <?php
        echo '<h2>'.esc_html__('Delete All Settings and Data','ads-for-wp').'</h2>';         
        ?>
        <ul>
                
                <li>
                   <div class="adsforwp-tools-field-title">
                        <div class="adsforwp-tooltip">
                            <strong>
                                <?php echo esc_html__('Delete','ads-for-wp'); ?>
                            </strong>
                        </div>
                       <a href="#"class="button adsforwp-reset-data">
                                    <?php echo esc_html__('Delete','ads-for-wp'); ?>
                       </a>                         
                        <p>This will delete your settings and all ads</p>
                    </div>
                </li> 
        </ul>
        <?php
        
}
public function adsforwp_ad_blocker_support_callback(){
	        
	$settings = adsforwp_defaultSettings();           
        ?>	
	<fieldset>
            <?php
          
            if(isset($settings['ad_blocker_support'])){
                echo '<input type="checkbox" name="adsforwp_settings[ad_blocker_support]" class="regular-text afw_advnc_ad_blocker_support" value="1" checked> ';
            }else{
                echo '<input type="checkbox" name="adsforwp_settings[ad_blocker_support]" class="regular-text afw_advnc_ad_blocker_support" value="1" >';
            }
           
            ?>
		
	</fieldset>

	<?php
        
}
public function adsforwp_ad_revenue_sharing_callback(){	        
	$settings = adsforwp_defaultSettings();   
        
        ?>	
	<fieldset>
            <?php
           
            if(isset($settings['ad_revenue_sharing'])){
                echo '<input type="checkbox" name="adsforwp_settings[ad_revenue_sharing]" class="regular-text afw_ad_revenue_sharing" value="1" checked> ';
            }else{
                echo '<input type="checkbox" name="adsforwp_settings[ad_revenue_sharing]" class="regular-text afw_ad_revenue_sharing" value="1" >';
            }
            
            ?>		
	</fieldset>
        <div class="afw_revenue_divider"><p><?php echo esc_html__('Enter the percentage of revenue that you would like to share', 'ads-for-wp') ?></p>
            <strong><?php echo esc_html__('Owner', 'ads-for-wp') ?></strong> <input type="number" placeholder="percentage" id="adsforwp_owner_revenue_per" name="adsforwp_settings[ad_owner_revenue_per]" value="<?php echo isset( $settings['ad_owner_revenue_per'] ) ? esc_attr( $settings['ad_owner_revenue_per']) : ''; ?>">
            <strong><?php echo esc_html__('Author', 'ads-for-wp') ?></strong> <input type="number"  placeholder="percentage" id="adsforwp_author_revenue_per" name="adsforwp_settings[ad_author_revenue_per]" value="<?php echo isset( $settings['ad_author_revenue_per'] ) ? esc_attr( $settings['ad_author_revenue_per']) : ''; ?>">
        </div>
	<?php        
}

public function adsforwp_contact_us_form_callback(){	        	        
        ?>		
        
        <div class="afw_contact_us_div">
            <strong><?php echo esc_html__('If you have any query, please write the query in below box or email us at', 'ads-for-wp') ?> <a href="mailto:team@magazine3.com">team@magazine3.com</a>. <?php echo esc_html__('We will reply to your email address shortly', 'ads-for-wp') ?></strong>
       
            <ul>
                <li>
                    <textarea rows="5" cols="60" id="adsforwp_query_message" name="adsforwp_query_message"> </textarea>
                    <br>
                    <span class="afw-query-success afw_hide"><?php echo esc_html__('Message sent successfully, Please wait we will get back to you shortly', 'ads-for-wp'); ?></span>
                    <span class="afw-query-error afw_hide"><?php echo esc_html__('Message not sent. please check your network connection', 'ads-for-wp'); ?></span>
                </li> 
                <li><button class="button afw-send-query"><?php echo esc_html__('Send Message', 'ads-for-wp'); ?></button></li>
            </ul>            
                   
        </div>
	<?php        
}
    
}
if (class_exists('adsforwp_admin_settings')) {
	new adsforwp_admin_settings;
};