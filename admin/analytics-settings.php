<?php
class adsforwp_admin_analytics_settings{
            
public function __construct() {
        add_action( 'admin_enqueue_scripts', array($this, 'adsforwp_chart_register_scripts') );
        add_action( 'admin_menu', array($this, 'adsforwp_add_analytics_menu_links'),true);
                
    }
public function adsforwp_add_analytics_menu_links() {	
	// Settings page - Same as main menu page
               
        $settingsLink = null;
        $adsforwp_google_token = get_option( 'adsforwp_google_token' );
        if (! $adsforwp_google_token ) {
            $settingsLink = 'edit.php?post_type=adsforwp';    
        }else{
             add_submenu_page( 'edit.php?post_type=adsforwp',
                esc_html__( 'Ads for wp', 'ads-for-wp' ),
                esc_html__( 'Analytics', 'ads-for-wp' ),
                'manage_options',
                'analytics',
                array($this, 'adsforwp_admin_analytics_interface_render')); 
        }
                add_submenu_page( $settingsLink,
                    esc_html__( 'Ads for wp Analytics', 'ads-for-wp' ),
                    esc_html__( 'Analytics', 'ads-for-wp' ),
                    'manage_options',
                    'adsforwp-analytics',
                    array($this , 'adsforwp_admin_analytics_render')
                );
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
            
            $start_date_val = strtotime( 'now' );//strtotime( '-1 month' );
            $end_date_val   = strtotime( 'now' );
            $start_date     = date( 'Y-m-d', $start_date_val );
            $end_date       = date( 'Y-m-d', $end_date_val );


            //$_differ = get_option( 'adsforwp_date_differ' );
            $_differ =  'today';
            if(isset($_REQUEST['view_data'])){
                $_differ = $_REQUEST['view_data'];
            }
            if ( $_differ ) {
                if ( $_differ == 'last_7_days' ) {
                    $start_date = date( 'Y-m-d', strtotime( '-7 days' ) );
                }elseif ( $_differ == 'last_14_days' ) {
                    $start_date = date( 'Y-m-d', strtotime( '-14 days' ) );
                }elseif ( $_differ == 'last_30_days' ) {
                    $start_date = date( 'Y-m-d', strtotime( '-1 month' ) );
                }elseif (  $_differ == 'this_month' ) {
                    $start_date =  date('Y-m-01') ;
                }elseif ( $_differ == 'last_month' ) {
                    $start_date =  date('Y-m-01', strtotime('-1 month') );
                    $end_date =  date('Y-m-t', strtotime('-1 month') );
                }elseif ( $_differ == 'last_3_months' ) {
                    $start_date =  date('Y-m-01', strtotime('-3 month') );
                    $end_date =  date('Y-m-t', strtotime('-1 month') );
                }elseif ( $_differ == 'last_6_months' ) {
                    $start_date =  date('Y-m-01', strtotime('-6 month') );
                    $end_date =  date('Y-m-t', strtotime('-1 month') );
                }elseif ( $_differ == 'last_year' ) {
                    $start_date =  date('Y-m-01', strtotime('-1 year') );
                    $end_date =  date('Y-m-t', strtotime('-1 month') );
                }

            }
            /*if ( isset( $_POST['view_data'] ) ) {

            $s_date   = sanitize_text_field( wp_unslash( $_POST['st_date'] ) );
            $ed_date  = sanitize_text_field( wp_unslash( $_POST['ed_date'] ) );
        }

        if ( isset( $s_date ) ) {
            $start_date = $s_date ;
        }

        if ( isset( $ed_date ) ) {
            $end_date = $ed_date;
        }*/
        $date1 = date_create( $start_date );
        $date2 = date_create( $end_date );
        $diff  = date_diff( $date2, $date1 );

        $compare_start_date = strtotime( $start_date . $diff->format( '%R%a days' ) );
        $compare_start_date = date( 'Y-m-d', $compare_start_date );
        $compare_end_date   = $start_date;

        $settingOpt = get_option('adsforwp_analytics');
        $dashboard_profile_ID = $settingOpt['profile_for_dashboard'];
        if($dashboard_profile_ID!=''){
            $allinfo =  adsforwp_show_default_overall_dashboard($dashboard_profile_ID,$start_date,$end_date, $compare_start_date, $compare_end_date);
        }

    //ADS Click & impressions
        $overallStats = array();
        $datediff = strtotime($end_date) - strtotime($start_date);
        $date_different     = round($datediff / (60 * 60 * 24));
        if($date_different == 0){    
            $optionDetails = get_option("adsforwp_ads-".date('Y-m-d'));
            $overallStats = $optionDetails['complete'];
        }else{
            $periods = new DatePeriod(
                             new DateTime($start_date),
                             new DateInterval('P1D'),
                             new DateTime($end_date)
                        );
            foreach ($periods as $key => $value) {
                $optionDetails = get_option("adsforwp_ads-".$value->format('Y-m-d'));
                if($optionDetails){
                    foreach ($optionDetails['complete'] as $key => $value) {
                        if(isset($overallStats[$key]['impression'])){
                            $overallStats[$key]['impression'] = $value['impression'];
                        }else{
                            @$overallStats[$key]['impression'] += $value['impression'];
                        }
                        if(isset($overallStats[$key]['click'])){
                            $overallStats[$key]['click'] += $value['click'];
                        }else{
                            @$overallStats[$key]['click'] += $value['click'];
                        }
                    }//Device foreach closed

                }//If closed
            }//Foreach closed
        }

    //ALL
    $allDeviceAds = array("impression" => 0, "click"=>0);
    if($overallStats){
        foreach ($overallStats as $key => $value) {
            $allDeviceAds['click'] +=  $value['click'];
            $allDeviceAds['impression'] +=  $value['impression'];
        }
    }
    $overallStats['all'] = $allDeviceAds;
	?>
        
<div class="afw-analytic_container">	                            
                <div class="afw-analytics-title"><h1><?php echo esc_html__('Analytics', 'ads-for-wp'); ?></h1></div>
		<div class="nav-tab-wrapper adsforwp-analytics-tabs">
            <?php

            echo '<a href="' . esc_url(adsforwp_analytics_admin_link('all')) . '" class="nav-tab ' . esc_attr( $tab == 'all' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('ALL', 'ads-for-wp') . '</a>';

            echo '<a href="' . esc_url(adsforwp_analytics_admin_link('mobile')) . '" class="nav-tab ' . esc_attr( $tab == 'mobile' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Mobile','ads-for-wp') . '</a>';
                        
            echo '<a href="' . esc_url(adsforwp_analytics_admin_link('desktop')) . '" class="nav-tab ' . esc_attr( $tab == 'desktop' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Desktop','ads-for-wp') . '</a>';
            
            echo '<a href="' . esc_url(adsforwp_analytics_admin_link('amp')) . '" class="nav-tab ' . esc_attr( $tab == 'amp' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('AMP','ads-for-wp') . '</a>';
            
            
            ?>
        </div>
        <div class="view_old_data" style="display: inline-block;">
            <form method="get">
                <div class="afw-analytics-days" style="display: inline-block;">
                    <input type="hidden" name="post_type" value="adsforwp">
                    <input type="hidden" name="page" value="analytics">
                    <select name="view_data">
                        <option value="today" <?php if($_differ=='today'){echo "selected"; } ?>> <?php echo esc_html__('Today','ads-for-wp'); ?></option>
                        <option value="last_7_days" <?php if($_differ=='last_7_days'){echo "selected"; } ?> > <?php echo esc_html__('Last 7 days','ads-for-wp'); ?></option>
                        <option value="last_14_days" <?php if($_differ=='last_14_days'){echo "selected"; } ?>> <?php echo esc_html__('Last 14 days','ads-for-wp'); ?></option> 
                        <option value="last_30_days" <?php if($_differ=='last_30_days'){echo "selected"; } ?>> <?php echo esc_html__('Last 30 days','ads-for-wp'); ?></option>
                        <option value="this_month" <?php if($_differ=='this_month'){echo "selected"; } ?>> <?php echo esc_html__('This month','ads-for-wp'); ?></option>
                        <option value="last_month" <?php if($_differ=='last_month'){echo "selected"; } ?>> <?php echo esc_html__('Last month','ads-for-wp'); ?></option>                       
                        <option value="last_3_months" <?php if($_differ=='last_3_months'){echo "selected"; } ?>> <?php echo esc_html__('Last 3 month','ads-for-wp'); ?></option>                       
                        <option value="last_6_months" <?php if($_differ=='last_6_months'){echo "selected"; } ?>> <?php echo esc_html__('Last 6 month','ads-for-wp'); ?></option>                       
                        <option value="last_year" <?php if($_differ=='last_year'){echo "selected"; } ?>> <?php echo esc_html__('Last year','ads-for-wp'); ?></option>                       
                    </select>    
                    <button type="submit" class="btn btn-success button-primary" style="display: inline-block;
                        background: #444444;
                        border-radius: 5px;
                        border: 0;
                        color: #fff;
                        font-size: 14px;
                        padding: 0 13px;
                        height: 30px;
                        cursor: pointer;"> <?php echo esc_html__('View Report','ads-for-wp'); ?>  </button>
                </div>
            </form>
        </div>
        <div class="view_settings_option" style="display: inline-block;">
            <a href="<?php echo esc_url( admin_url('edit.php?post_type=adsforwp&page=adsforwp-analytics') ); ?>"><i class="dashicons-before dashicons-admin-generic"></i> Settings</a></div>

</div>
<div class="form-wrap">
    <div class="adsforwp-all afw-analytics_track_report-div">
        <div>
            <h3> <?php echo esc_html__('Visitors','ads-for-wp'); ?></h3>
            <h1><?php echo @$allinfo['vistors'] ?><span class="afw-diff-precentage"><?php echo @$allinfo['vistors_cmp']; ?> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Pageview','ads-for-wp'); ?></h3> 
          <h1><?php echo @$allinfo['pageviews']; ?> <span class="afw-diff-precentage"><?php echo @$allinfo['pageviews_cmp']; ?> </span></h1>
        </div>    
        <div>
         <h3> <?php echo esc_html__('AD impressions','ads-for-wp'); ?></h3> 
         <h1><?php echo (isset($overallStats['all']['impression'])? $overallStats['all']['impression']: 0); ?><span class="afw-diff-precentage"> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Total Clicks','ads-for-wp'); ?></h3> 
          <h1><?php echo (isset($overallStats['all']['click'])? $overallStats['all']['click']: 0); ?> <span class="afw-diff-precentage"> </span></h1>
          
        </div> 
        <div>
          <h3> <?php echo esc_html__('CTR','ads-for-wp'); ?></h3> 
          <h1><?php
          $all_impression = (isset($overallStats['all']['impression'])? $overallStats['all']['impression']: 0);
            if($all_impression){
                echo $this->two_decimal_places(((isset($overallStats['all']['click'])? $overallStats['all']['click']: 0)/$all_impression)*100);
            }else{echo "0"; } ?>%</h1>
        </div> 
    </div>

    <div class="adsforwp-mobile afw-analytics_track_report-div" style="display: none;">
        <div>
            <h3> <?php echo esc_html__('Visitors','ads-for-wp'); ?></h3>
            <h1><?php echo @$allinfo['otherDeviceData']['mobile']['vistors'] ?><span class="afw-diff-precentage"><?php echo @$allinfo['otherDeviceData']['mobile']['vistors_cmp']; ?> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Pageview','ads-for-wp'); ?></h3> 
          <h1><?php echo @$allinfo['otherDeviceData']['mobile']['pageviews']; ?> <span class="afw-diff-precentage"><?php echo @$allinfo['otherDeviceData']['mobile']['pageviews_cmp']; ?> </span></h1>
        </div>    
        <div>
         <h3> <?php echo esc_html__('AD impressions','ads-for-wp'); ?></h3> 
         <h1><?php echo (isset($overallStats['mobile']['impression'])? $overallStats['mobile']['impression']: 0); ?><span class="afw-diff-precentage"> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Total Clicks','ads-for-wp'); ?></h3> 
          <h1><?php echo (isset($overallStats['mobile']['click'])? $overallStats['mobile']['click']: 0); ?> <span class="afw-diff-precentage"> </span></h1>
          
        </div> 
        <div>
          <h3> <?php echo esc_html__('CTR','ads-for-wp'); ?></h3> 
          <h1><?php 
          $mobile_impression = (isset($overallStats['mobile']['impression'])? $overallStats['mobile']['impression']: 0);
          if($mobile_impression){
            echo $this->two_decimal_places(((isset($overallStats['mobile']['click'])? $overallStats['mobile']['click']: 0)/$mobile_impression)*100);
          }else{echo "0"; } ?>%</h1>
        </div>
    </div>

    <div class="adsforwp-desktop afw-analytics_track_report-div" style="display: none;">
        <div>
            <h3> <?php echo esc_html__('Visitors','ads-for-wp'); ?></h3>
            <h1><?php echo @$allinfo['otherDeviceData']['desktop']['vistors'] ?><span class="afw-diff-precentage"><?php echo @$allinfo['otherDeviceData']['desktop']['vistors_cmp']; ?> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Pageview','ads-for-wp'); ?></h3> 
          <h1><?php echo @$allinfo['otherDeviceData']['desktop']['pageviews']; ?> <span class="afw-diff-precentage"><?php echo @$allinfo['otherDeviceData']['desktop']['pageviews_cmp']; ?> </span></h1>
        </div>    
        <div>
         <h3> <?php echo esc_html__('AD impressions','ads-for-wp'); ?></h3> 
         <h1><?php echo (isset($overallStats['desktop']['impression'])? $overallStats['desktop']['impression']: 0); ?><span class="afw-diff-precentage"> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Total Clicks','ads-for-wp'); ?></h3> 
          <h1><?php echo (isset($overallStats['desktop']['click'])? $overallStats['desktop']['click']: 0); ?> <span class="afw-diff-precentage"> </span></h1>
          
        </div> 
        <div>
          <h3> <?php echo esc_html__('CTR','ads-for-wp'); ?></h3> 
          <h1><?php
          $desktop_impression = (isset($overallStats['desktop']['impression'])? $overallStats['desktop']['impression']: 0);
          if($desktop_impression){
           echo $this->two_decimal_places(((isset($overallStats['desktop']['click'])? $overallStats['desktop']['click']: 0)/$desktop_impression)*100);
           }else{echo "0"; } ?>%</h1>
        </div>
    </div>

    <div class="adsforwp-amp afw-analytics_track_report-div" style="display: none;">
        <div>
            <h3> <?php echo esc_html__('Visitors','ads-for-wp'); ?></h3>
            <h1><?php echo @$allinfo['amp_pages']['visitors'] ?><span class="afw-diff-precentage"><?php echo @$allinfo['amp_pages']['vistors_cmp']; ?> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Pageview','ads-for-wp'); ?></h3> 
          <h1><?php echo @$allinfo['amp_pages']['pageviews']; ?> <span class="afw-diff-precentage"><?php echo @$allinfo['amp_pages']['pageviews_cmp']; ?> </span></h1>
        </div>    
        <div>
         <h3> <?php echo esc_html__('AD impressions','ads-for-wp'); ?></h3> 
         <h1><?php echo (isset($overallStats['amp']['impression'])? $overallStats['amp']['impression']: 0); ?> <span class="afw-diff-precentage"> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Total Clicks','ads-for-wp'); ?></h3> 
          <h1><?php echo  (isset($overallStats['amp']['click'])? $overallStats['amp']['click']: 0); ?> <span class="afw-diff-precentage"> </span></h1>
          
        </div> 
        <div>
          <h3> <?php echo esc_html__('CTR','ads-for-wp'); ?></h3> 
          <h1><?php
          $amp_impression = (isset($overallStats['amp']['impression'])? $overallStats['amp']['impression']: 0); 
          if($amp_impression){
          echo $this->two_decimal_places(((isset($overallStats['amp']['impression'])? $overallStats['amp']['click']: 0)/$amp_impression)*100); }else{echo "0"; } ?>%</h1>
        </div>
    </div>
    

    
</div>  
<section style="margin-top:30px; background: #fff;padding:10px;">
    <div id="canvas-holder" style="width:40%;padding:10px;display: inline-block;">
        <h3>Mobile vs Desktop</h3>
        <canvas  id="chart-stats" ></canvas>      
    </div>
    <div id="canvas-holder" style="width:40%;padding:10px;display: inline-block;">
        <h3>AMP vs Non AMP</h3>
        <canvas  id="chart-amp-mobile" ></canvas>      
    </div>
</section>
<script>
   var piechart = <?php echo json_encode(array("mobile"=>(isset($overallStats['mobile']['impression'])? $overallStats['mobile']['impression']: 0),
         "desktop"=> (isset($overallStats['desktop']['impression'])? $overallStats['desktop']['impression']: 0),
        "AMP"=>(isset($overallStats['amp']['impression'])? $overallStats['amp']['impression']: 0) ,
     ));?>
</script>
	<?php         
}
/*
	WP Settings API
*/
	function adsforwp_admin_analytics_render(){
       global $GLOBALS;
        $GLOBALS['ADSFORWP']->authentication();
    }
    function two_decimal_places($num){
        return number_format((float)$num, 2, '.', '');
    }

    function adsforwp_chart_register_scripts($hook){
        if("adsforwp_page_analytics"==$hook){
            wp_register_script(
                    'highCharts',
                    ADSFORWP_PLUGIN_DIR_URI . 'public/Chart.bundle.js',
                    array( 'jquery' ),
                    '3.0',
                    true
                );
                wp_register_script(
                    'adminCharts',
                    ADSFORWP_PLUGIN_DIR_URI . 'public/admin_charts.js',
                    array( 'highCharts' ),
                    '1.0',
                    true
                );
               /*wp_register_style(
                    'adminChartsStyles',
                    ADSFORWP_PLUGIN_DIR_URI . 'css/admin_chart.css'
                );*/
                wp_enqueue_script( 'highCharts' );
                wp_enqueue_script( 'adminCharts' );
       }
    }

}
if (class_exists('adsforwp_admin_analytics_settings')) {
	new adsforwp_admin_analytics_settings;
};
/**
 * Enqueue CSS and JS
 */
