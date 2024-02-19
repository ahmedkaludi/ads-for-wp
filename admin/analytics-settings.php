<?php
class adsforwp_admin_analytics_settings{
            
    public function __construct() {
    
        add_action( 'admin_enqueue_scripts', array($this, 'adsforwp_chart_register_scripts') );
        add_action( 'admin_menu', array($this, 'adsforwp_add_analytics_menu_links'),10);
        add_filter( 'adsforwp_localize_filter',array($this,'adsforwp_add_localize_analytics_data'),10,2);
                
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
                if(!defined('ADSFORWP_PRO_VERSION') && current_user_can('manage_options')){
                global $submenu;
                $permalink = 'javasctipt:void(0);';
                $submenu['edit.php?post_type=adsforwp'][] = array( '<div style="color:#fff176;" onclick="window.open(\'https://adsforwp.com/pricing/\')">'.esc_html__( 'Upgrade To Premium', 'pwa-for-wp' ).'</div>', 'manage_options', $permalink);
            }
    }


    public function adsforwp_admin_analytics_interface_render(){
    
	// Authentication
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }   
                        			            
    $all_ads_post         = adsforwp_get_ad_ids();  
    $total_ads_impression = 0;
    $total_ads_clicks     = 0;
    if($all_ads_post){
            
        foreach($all_ads_post as $ad_id){     
            
            $ad_impression_count  = get_post_meta($ad_id, $key='ad_impression_count', true );
            $ad_clicks_count      = get_post_meta($ad_id, $key='ad_clicks', true );                 
            $total_ads_impression = ((int)$total_ads_impression+ (int)$ad_impression_count);
            $total_ads_clicks     = ((int)$total_ads_clicks+(int)$ad_clicks_count);   
        }    
    }                
    
    $tab = adsforwp_get_tab('all', array('mobile','desktop', 'amp', 'tablets'));
        
        $start_date_val = strtotime( 'now' );//strtotime( '-1 month' );
        $end_date_val   = strtotime( 'now' );
        $start_date     = date( 'Y-m-d', $start_date_val );
        $end_date       = date( 'Y-m-d', $end_date_val );


        $_differ =  'today';
        if(isset($_REQUEST['view_data'])){
            
            if ( ! isset( $_GET['adsforwp_analytics_report_nonce'] ) ){
                return;		
            }

            if ( !wp_verify_nonce( $_GET['adsforwp_analytics_report_nonce'], 'adsforwp_analytics_report_data' ) ){
                return;
            }
                            
            $_differ = $_REQUEST['view_data'];
            
        }
        
        $ad_id_param = null;
                    
        if(isset($_REQUEST['ad_id'])){
            
            $ad_id_param = $_REQUEST['ad_id'];
            
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
        $overallStats       = array();
        $datediff           = strtotime($end_date) - strtotime($start_date);
        $date_different     = round($datediff / (60 * 60 * 24));
        if($date_different == 0){    
            
            $overallStats = adsforwp_get_ad_stats('fetchAllBy', $ad_id_param ,strtotime(date('y-m-d')));
                        
        }else{
            $periods = new DatePeriod(
                             new DateTime($start_date),
                             new DateInterval('P1D'),
                             new DateTime($end_date)
                        );
            foreach ($periods as $key => $value) {
               
                $optionDetails = adsforwp_get_ad_stats('fetchAllBy', $ad_id_param ,strtotime($value->format('Y-m-d')));
                 
                if($optionDetails){
                    foreach ($optionDetails as $key => $value) {
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
                <?php if(isset($_GET['ad_id'])) { ?>
                
                <div class="afw-analytics-title"><h3><?php echo esc_html__('Analytics of Single AD', 'ads-for-wp'); ?></h3></div>
    
                <?php } else { ?>
    
                <div class="afw-analytics-title"><h3><?php echo esc_html__('Analytics of All ADs', 'ads-for-wp'); ?></h3></div>
                
                <?php } ?>
                    
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
                    
                    <?php
                    if(isset($_GET['ad_id'])){
                        echo ' <input type="hidden" name="ad_id" value="'.esc_attr($_GET['ad_id']).'">';
                    }
                    ?>
                                        
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
                    <input type="hidden" name="adsforwp_analytics_report_nonce" value="<?php echo wp_create_nonce('adsforwp_analytics_report_data');     ?>">
                    <button type="submit" class="btn btn-success adsforwp_view_report_btn"> <?php echo esc_html__('View Report','ads-for-wp'); ?>  </button>
                </div>
            </form>
        </div>
        <div class="afw_view_settings_option" style="display: inline-block;">
            <a href="<?php echo esc_url( admin_url('edit.php?post_type=adsforwp&page=adsforwp-analytics') ); ?>"><i class="dashicons-before dashicons-admin-generic"></i> <?php echo esc_html__('Settings', 'ads-for-wp'); ?></a></div>

</div>
<div class="form-wrap">
    <div class="adsforwp-all afw-analytics_track_report-div">
        <div>
            <h3> <?php echo esc_html__('Visitors','ads-for-wp'); ?></h3>
            <h1><?php echo (isset($allinfo['vistors'])? esc_attr($allinfo['vistors']):0); ?>
                <!-- Below variable returns html which is already escaped function name adsforwp_get_compare_stats-->
                <span class="afw-diff-precentage"><?php echo @$allinfo['vistors_cmp']; ?> </span> 
            </h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Pageview','ads-for-wp'); ?></h3> 
          <h1><?php echo (isset($allinfo['pageviews'])? esc_attr($allinfo['pageviews']):0); ?> 
              <!-- Below variable returns html which is already escaped: function name adsforwp_get_compare_stats -->
              <span class="afw-diff-precentage"><?php echo @$allinfo['pageviews_cmp']; ?> </span>
          </h1>
        </div>    
        <div>
         <h3> <?php echo esc_html__('AD impressions','ads-for-wp'); ?></h3> 
         <h1><?php echo (isset($overallStats['all']['impression'])? esc_attr($overallStats['all']['impression']): 0); ?><span class="afw-diff-precentage"> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Total Clicks','ads-for-wp'); ?></h3> 
          <h1><?php echo (isset($overallStats['all']['click'])? esc_attr($overallStats['all']['click']): 0); ?> <span class="afw-diff-precentage"> </span></h1>
          
        </div> 
        <div>
          <h3> <?php echo esc_html__('CTR','ads-for-wp'); ?></h3> 
          <h1><?php
          $all_impression = (isset($overallStats['all']['impression'])? $overallStats['all']['impression']: 0);
            if($all_impression){
                $all_impression = $this->two_decimal_places(((isset($overallStats['all']['click'])? $overallStats['all']['click']: 0)/$all_impression)*100);
                echo esc_attr($all_impression);
            }else{echo "0"; } ?>%</h1>
        </div> 
    </div>

    <div class="adsforwp-mobile afw-analytics_track_report-div" style="display: none;">
        <div>
            <h3> <?php echo esc_html__('Visitors','ads-for-wp'); ?></h3>
            <h1>
                <?php
                $mobile_visitor = @$allinfo['otherDeviceData']['mobile']['vistors'];
                if(isset($mobile_visitor)){
                  echo esc_attr($mobile_visitor);  
                }else{
                    echo "0";
                }
                 ?>
             <!-- Below variable returns html which is already escaped: function name adsforwp_get_compare_stats -->   
            <span class="afw-diff-precentage"><?php echo @$allinfo['otherDeviceData']['mobile']['vistors_cmp']; ?> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Pageview','ads-for-wp'); ?></h3> 
          <h1><?php 
               $mobile_page_views = @$allinfo['otherDeviceData']['mobile']['pageviews'];
               if(isset($mobile_page_views)){
                   echo esc_attr($mobile_page_views);
               }else{
                   echo "0";
               }
          ?>    
              <!-- Below variable returns html which is already escaped: function name adsforwp_get_compare_stats -->              
              <span class="afw-diff-precentage"><?php echo @$allinfo['otherDeviceData']['mobile']['pageviews_cmp']; ?> </span></h1>
        </div>    
        <div>
         <h3> <?php echo esc_html__('AD impressions','ads-for-wp'); ?></h3> 
         <h1><?php echo (isset($overallStats['mobile']['impression'])? esc_attr($overallStats['mobile']['impression']): 0); ?><span class="afw-diff-precentage"> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Total Clicks','ads-for-wp'); ?></h3> 
          <h1><?php echo (isset($overallStats['mobile']['click'])? esc_attr($overallStats['mobile']['click']): 0); ?> <span class="afw-diff-precentage"> </span></h1>
          
        </div> 
        <div>
          <h3> <?php echo esc_html__('CTR','ads-for-wp'); ?></h3> 
          <h1><?php 
          $mobile_impression = (isset($overallStats['mobile']['impression'])? $overallStats['mobile']['impression']: 0);
          if($mobile_impression){
              $mobile_impression = $this->two_decimal_places(((isset($overallStats['mobile']['click'])? $overallStats['mobile']['click']: 0)/$mobile_impression)*100);
            echo esc_attr($mobile_impression);
          }else{echo "0"; } ?>%</h1>
        </div>
    </div>

    <div class="adsforwp-desktop afw-analytics_track_report-div" style="display: none;">
        <div>
            <h3> <?php echo esc_html__('Visitors','ads-for-wp'); ?></h3>
            <h1><?php 
                $desktop_vistors =  @$allinfo['otherDeviceData']['desktop']['vistors'];
                if(isset($desktop_vistors)){
                    echo esc_attr($desktop_vistors);
                }else{
                    echo "0";
                }
                    ?>
                <!-- Below variable returns html which is already escaped: function name adsforwp_get_compare_stats -->
                <span class="afw-diff-precentage"><?php echo @$allinfo['otherDeviceData']['desktop']['vistors_cmp']; ?> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Pageview','ads-for-wp'); ?></h3> 
          <h1><?php
          $desktop_page_view = @$allinfo['otherDeviceData']['desktop']['pageviews'];
          if(isset($desktop_page_view)){
              echo esc_attr($desktop_page_view);
          }else{
              echo "0";
          }
          ?> 
              <!-- Below variable returns html which is already escaped: function name adsforwp_get_compare_stats -->
              <span class="afw-diff-precentage"><?php echo @$allinfo['otherDeviceData']['desktop']['pageviews_cmp']; ?> </span></h1>
        </div>    
        <div>
         <h3> <?php echo esc_html__('AD impressions','ads-for-wp'); ?></h3> 
         <h1><?php echo (isset($overallStats['desktop']['impression'])? esc_attr($overallStats['desktop']['impression']): 0); ?><span class="afw-diff-precentage"> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Total Clicks','ads-for-wp'); ?></h3> 
          <h1><?php echo (isset($overallStats['desktop']['click'])? esc_attr($overallStats['desktop']['click']): 0); ?> <span class="afw-diff-precentage"> </span></h1>
          
        </div> 
        <div>
          <h3> <?php echo esc_html__('CTR','ads-for-wp'); ?></h3> 
          <h1><?php
          $desktop_impression = (isset($overallStats['desktop']['impression'])? $overallStats['desktop']['impression']: 0);
          if($desktop_impression){
              $desktop_impression = $this->two_decimal_places(((isset($overallStats['desktop']['click'])? $overallStats['desktop']['click']: 0)/$desktop_impression)*100);
           echo esc_attr($desktop_impression);
           }else{echo "0"; } ?>%</h1>
        </div>
    </div>

    <div class="adsforwp-amp afw-analytics_track_report-div" style="display: none;">
        <div>
            <h3> <?php echo esc_html__('Visitors','ads-for-wp'); ?></h3>
            <h1><?php echo (isset($allinfo['amp_pages']['visitors'])? esc_attr($allinfo['amp_pages']['visitors']):0); ?>
                <!-- Below variable returns html which is already escaped: function name adsforwp_get_compare_stats -->
                <span class="afw-diff-precentage"><?php echo @$allinfo['amp_pages']['vistors_cmp']; ?> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Pageview','ads-for-wp'); ?></h3> 
          <h1><?php echo (isset($allinfo['amp_pages']['pageviews'])? esc_attr($allinfo['amp_pages']['pageviews']):0); ?> 
              <!-- Below variable returns html which is already escaped: function name adsforwp_get_compare_stats -->
              <span class="afw-diff-precentage"><?php echo @$allinfo['amp_pages']['pageviews_cmp']; ?> </span></h1>
        </div>    
        <div>
         <h3> <?php echo esc_html__('AD impressions','ads-for-wp'); ?></h3> 
         <h1><?php echo (isset($overallStats['amp']['impression'])? esc_attr($overallStats['amp']['impression']): 0); ?> <span class="afw-diff-precentage"> </span></h1>
        </div>    
        <div>
          <h3> <?php echo esc_html__('Total Clicks','ads-for-wp'); ?></h3> 
          <h1><?php echo  (isset($overallStats['amp']['click'])? esc_attr($overallStats['amp']['click']): 0); ?> <span class="afw-diff-precentage"> </span></h1>          
        </div> 
        <div>
          <h3> <?php echo esc_html__('CTR','ads-for-wp'); ?></h3> 
          <h1><?php
          $amp_impression = (isset($overallStats['amp']['impression'])? $overallStats['amp']['impression']: 0); 
          if($amp_impression){
              $amp_impression = $this->two_decimal_places(((isset($overallStats['amp']['impression'])? $overallStats['amp']['click']: 0)/$amp_impression)*100);
              echo esc_attr($amp_impression);          
          }else{
              echo "0"; 
              
          } ?>%</h1>
        </div>
    </div>
    

    
</div>  
<section style="margin-top:30px; background: #fff;padding:10px;margin-right: 20px;">
    <h2><?php echo esc_html__('Ad performance','ads-for-wp'); ?></h2><hr/>
    <div id="canvas-holder" style="width:40%;padding:10px;display: inline-block;">
        <h3><?php echo esc_html__('Mobile vs Desktop', 'ads-for-wp'); ?></h3>
        <?php
        if(isset($overallStats['mobile']['impression']) && $overallStats['mobile']['impression']!=0 && isset($overallStats['desktop']['impression']) && $overallStats['desktop']['impression']!=0){
            echo '<canvas  id="chart-stats" ></canvas>';      
        }else{
            echo esc_html__("Not enough data at the moment, Please check back soon", 'ads-for-wp');
            
        }
        ?>
    </div>
    <div id="canvas-holder" style="width:40%;padding:10px;display: inline-block;min-height: 350px;">
        <h3><?php echo esc_html__('AMP vs Non AMP', 'ads-for-wp'); ?></h3>
        <?php
        if(isset($overallStats['amp']['impression']) && $overallStats['amp']['impression']!=0 && isset($overallStats['desktop']['impression']) && $overallStats['desktop']['impression']!=0){
            echo '<canvas  id="chart-amp-mobile" ></canvas>';      
        }else{
            echo esc_html__("Not enough data at the moment, Please check back soon", 'ads-for-wp');
            
        }
         ?>
              
    </div>
</section>
	<?php         
}

    public function adsforwp_add_localize_analytics_data($object, $object_name){
        
        $ad_id_param = null;
                    
        if(isset($_REQUEST['ad_id'])){
            
            $ad_id_param = esc_attr($_REQUEST['ad_id']);
            
        }
        
        if($object_name=='adsforwp_localize_data'){
            
               $overallStats = adsforwp_get_ad_stats('fetchAllBy', $ad_id_param ,strtotime(date('y-m-d')));
                                       
               $object['mobile']  =(isset($overallStats['mobile']['impression'])? $overallStats['mobile']['impression']: 0);
               $object['desktop'] =(isset($overallStats['desktop']['impression'])? esc_attr($overallStats['desktop']['impression']): 0);
               $object['AMP']     =(isset($overallStats['amp']['impression'])? esc_attr($overallStats['amp']['impression']): 0); 
                          
        }
        return $object;
         
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
                ADSFORWP_PLUGIN_DIR_URI . 'public/assets/vendor/js/Chart.bundle.min.js',
                array( 'jquery' ),
                '3.0',
                true
            );
            wp_register_script(
                'adminCharts',
                ADSFORWP_PLUGIN_DIR_URI . 'public/assets/vendor/js/admin_charts.js',
                array( 'highCharts' ),
                '1.0',
                true
            );
            wp_enqueue_script( 'highCharts' );
            wp_enqueue_script( 'adminCharts' );
       }
    }
}
if (class_exists('adsforwp_admin_analytics_settings')) {
	new adsforwp_admin_analytics_settings;
};
