<?php
class adsforwp_admin_analytics_settings{
            
public function __construct() {
                add_action( 'admin_menu', array($this, 'adsforwp_add_analytics_menu_links'));        
                
                
    }
public function adsforwp_add_analytics_menu_links() {	
	// Settings page - Same as main menu page
                add_submenu_page( 'edit.php?post_type=adsforwp',
                esc_html__( 'Ads for wp', 'ads-for-wp' ),
                esc_html__( 'Analytics', 'ads-for-wp' ),
                'manage_options',
                'analytics',
                array($this, 'adsforwp_admin_analytics_interface_render'));	
}


public function adsforwp_admin_analytics_interface_render(){
    
	// Authentication
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}     	       
	
        
            $all_ads_post = json_decode(get_transient('adsforwp_transient_ads_ids'), true);  
            $total_ads_impression = 0;
            $total_ads_clicks = 0;
            if($all_ads_post){
                
            foreach($all_ads_post as $ad_id){                               
                $ad_impression_count = get_post_meta($ad_id, $key='ad_impression_count', true );
                $ad_clicks_count = get_post_meta($ad_id, $key='ad_clicks', true );   
                $total_ads_impression = ((int)$total_ads_impression+ (int)$ad_impression_count);
                $total_ads_clicks = ($total_ads_clicks+$ad_clicks_count);
            }
            
            }                
        
	       $tab = adsforwp_get_tab('all', array('mobile','desktop', 'amp', 'tablets'));
        
            
	?>
        
<div class="afw-analytic_container">	                            
                <div class="afw-analytics-title"><h1><?php echo esc_html__('Analytics', 'ads-for-wp'); ?></h1></div>
		<div class="nav-tab-wrapper adsforwp-analytics-tabs">
			<?php

			echo '<a href="' . esc_url(adsforwp_analytics_admin_link('all')) . '" class="nav-tab ' . esc_attr( $tab == 'all' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('ALL', 'ads-for-wp') . '</a>';

			echo '<a href="' . esc_url(adsforwp_analytics_admin_link('mobile')) . '" class="nav-tab ' . esc_attr( $tab == 'mobile' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Mobile','ads-for-wp') . '</a>';
                        
                        echo '<a href="' . esc_url(adsforwp_analytics_admin_link('desktop')) . '" class="nav-tab ' . esc_attr( $tab == 'desktop' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Desktop','ads-for-wp') . '</a>';
                        
                        echo '<a href="' . esc_url(adsforwp_analytics_admin_link('amp')) . '" class="nav-tab ' . esc_attr( $tab == 'amp' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('AMP','ads-for-wp') . '</a>';
                        
                        echo '<a href="' . esc_url(adsforwp_analytics_admin_link('tablets')) . '" class="nav-tab ' . esc_attr( $tab == 'tablets' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Tablets','ads-for-wp') . '</a>';
			
			?>
		</div>
                <div class="afw-analytics-days">
                    <select>
                        <option value="last_30_days"> <?php echo esc_html__('Real Time','ads-for-wp'); ?></option>                         
                        <option value="last_30_days"> <?php echo esc_html__('Today','ads-for-wp'); ?></option>
                        <option value="last_30_days"> <?php echo esc_html__('Yesterday','ads-for-wp'); ?></option>
                        <option value="last_30_days"> <?php echo esc_html__('Last 7 days','ads-for-wp'); ?></option> 
                        <option value="last_30_days"> <?php echo esc_html__('Last 14 days','ads-for-wp'); ?></option>
                        <option value="last_30_days"> <?php echo esc_html__('Last 30 days','ads-for-wp'); ?></option>
                        <option value="last_30_days"> <?php echo esc_html__('Last 90 days','ads-for-wp'); ?></option>                       
                    </select>    
                </div>
                
</div>
<div class="afw-analytics_track_report-div">
    <div>
        <h3> <?php echo esc_html__('Visitors','ads-for-wp'); ?></h3>
        <h1>390 <span class="afw-diff-precentage">+13%</span></h1>
    </div>    
    <div>
      <h3> <?php echo esc_html__('Pageview','ads-for-wp'); ?></h3> 
      <h1>500 <span class="afw-diff-precentage">+13%</span></h1>
    </div>    
    <div>
     <h3> <?php echo esc_html__('AD impressions','ads-for-wp'); ?></h3> 
     <h1><?php echo $total_ads_impression; ?><span class="afw-diff-precentage">+13%</span></h1>
    </div>    
    <div>
      <h3> <?php echo esc_html__('Total Clicks','ads-for-wp'); ?></h3> 
      <h1><?php echo $total_ads_clicks; ?> <span class="afw-diff-precentage">+13%</span></h1>
      
    </div> 
    <div>
      <h3> <?php echo esc_html__('CTR','ads-for-wp'); ?></h3> 
      <h1>2.5%</h1>
    </div> 
</div>        
	<?php         
}
/*
	WP Settings API
*/
}
if (class_exists('adsforwp_admin_analytics_settings')) {
	new adsforwp_admin_analytics_settings;
};
/**
 * Enqueue CSS and JS
 */
