<?php
function adsforwp_show_default_overall_dashboard($dashboard_profile_ID,$start_date,$end_date, $compare_start_date, $compare_end_date){
	if(!$dashboard_profile_ID){ return true; }

	$datediff = strtotime($end_date) -strtotime($start_date);
	$date_different     = round($datediff / (60 * 60 * 24));

	$stats = get_transient( md5( 'adsforwp-show-default-overall-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
	if( $stats === false ) {
		$stats = adsforwp_get_analytics_dashboard( 'ga:sessions,ga:users,ga:pageviews,ga:avgSessionDuration,ga:bounceRate,ga:pageviewsPerSession,ga:percentNewSessions,ga:newUsers,ga:sessionDuration', $start_date, $end_date );
		if($date_different && $stats){
			set_transient( md5( 'adsforwp-show-default-overall-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $stats, 60 * 60 * 20 );
		}

	}

	// get prev stats
	$compare_stats =  get_transient( md5( 'adsforwp-show-default-overall-dashboard-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) );
	if ( false === $compare_stats ) {
		$compare_stats = adsforwp_get_analytics_dashboard( 'ga:sessions,ga:users,ga:pageviews,ga:avgSessionDuration,ga:bounceRate,ga:pageviewsPerSession,ga:percentNewSessions,ga:newUsers', $compare_start_date, $compare_end_date );

		if($date_different && $compare_stats){
			set_transient( md5( 'adsforwp-show-default-overall-dashboard-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) , $compare_stats, 60 * 60 * 20 );
		}
	}

	// Device Category Stats
	$device_category_stats = get_transient( md5( 'adsforwp-show-default-overall-device-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
	if ( $device_category_stats === false ) {
		$device_category_stats = adsforwp_get_analytics_dashboard( 'ga:sessions,ga:users,ga:pageviews', $start_date, $end_date, 'ga:deviceCategory', '-ga:sessions' );

		if($date_different && $device_category_stats){
			set_transient( md5( 'adsforwp-show-default-overall-device-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $device_category_stats, 60 * 60 * 20 );
		}
	}
	//compare Device Category Stats
	$compare_device_category_stats =  get_transient( md5( 'adsforwp-show-default-overall-device-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) );
	if ( false === $compare_device_category_stats ) {
		$compare_device_category_stats = adsforwp_get_analytics_dashboard( 'ga:sessions,ga:users,ga:pageviews', $compare_start_date, $compare_end_date,'ga:deviceCategory', '-ga:sessions' );

		if($date_different && $compare_device_category_stats){
			set_transient( md5( 'adsforwp-show-default-overall-device-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) , $compare_device_category_stats, 60 * 60 * 20 );
		}
	}

	// Include Top Pages Statistics
	$top_page_stats =  get_transient( md5( 'adsforwp-show-default-top-pages-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );

	if ( $top_page_stats === false ) {
		$top_page_stats = adsforwp_get_analytics_dashboard( 'ga:pageviews,ga:avgTimeOnPage,ga:bounceRate,ga:users', $start_date, $end_date, 'ga:PageTitle,ga:pagePath', '-ga:pageviews', false, 40 );
		
		if($date_different && $top_page_stats){
			set_transient( md5( 'adsforwp-show-default-top-pages-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $top_page_stats, 60 * 60 * 20 );
		}
	}
	$ampPages = array('visitors'=>0, "pageviews"=>0);
	if ( isset( $top_page_stats['rows'] ) && $top_page_stats['rows'] > 0 ) {
		$i = 1;
		$dashboard_profile_ID = adsforwp_get_analytics_setiings('profile_for_dashboard');
		foreach ( $top_page_stats['rows'] as $top_page ) {
			/*$top_page[2];//ga:pageviews
			$top_page[3];//ga:avgTimeOnPage
			$top_page[4];//ga:bounceRate
			$top_page[5];//ga:users*/
			$toppage = explode('/', $top_page[1]);
			if( in_array("amp", $toppage) ){
				$ampPages['visitors'] += $top_page[5];
				$ampPages['pageviews'] += $top_page[2];	
			}
			
			$i++;
		}
	}	

	$returnstats = array();
	if ( isset( $stats->totalsForAllResults ) ) {
			$compare_results = $compare_stats->totalsForAllResults;
			$results = $stats->totalsForAllResults;

			$device_data = array();
			$compare_device_stats = $compare_device_category_stats->rows;                        
			if(isset($device_category_stats->rows)){
				foreach( $device_category_stats->rows as $key=>$row ){
					$session = $row[1];
					$compare_session = $compare_device_stats[$key][1];
					$vistors = $row[2];
					$compare_vistors = $compare_device_stats[$key][2];
					$pageviews = $row[3];
					$compare_pageviews = $compare_device_stats[$key][3];
					if($row[0]=='mobile'){
                                            
                                                if(isset($device_category_stats->rows[2])){
                                                   $session += $device_category_stats->rows[2][1];
                                                }
						
						    $compare_session += $compare_device_stats[2][1];

                                                if(isset($device_category_stats->rows[2])){
                                                    $vistors += $device_category_stats->rows[2][2];    
                                                }    
						
                                                    $compare_vistors += $compare_device_stats[2][2];
                                                
                                                if(isset($device_category_stats->rows[2])){
                                                    $pageviews += $device_category_stats->rows[2][3];
                                                }     
						
						$compare_pageviews += $compare_device_stats[2][3];

					}

					$device_data[$row[0]] = array(
										'sessions'=>$session,
										'session_cmp'=>adsforwp_get_compare_stats($session, $compare_session, $date_different ),
										'vistors'=>$vistors,
										'vistors_cmp'=>adsforwp_get_compare_stats($vistors, $compare_vistors, $date_different ),
										'pageviews'=>$pageviews,
										'pageviews_cmp'=>adsforwp_get_compare_stats( $pageviews, $compare_pageviews, $date_different ),
									);

				}
			}
			$returnstats = array(
						"sessions" => adsforwp_beautify_number($results['ga:sessions']),
						"sessions_cmp"=>adsforwp_get_compare_stats( $results['ga:sessions'],  $compare_results['ga:sessions'], $date_different ),

						"vistors" => adsforwp_beautify_number($results['ga:users']),
						"vistors_cmp"=>adsforwp_get_compare_stats( $results['ga:users'], $compare_results['ga:users'], $date_different ),

						"pageviews" => adsforwp_beautify_number($results['ga:pageviews']),
						"pageviews_cmp"=>adsforwp_get_compare_stats( $results['ga:pageviews'], $compare_results['ga:pageviews'], $date_different ),

						"avg_session_duration" => adsforwp_beautify_number( $results['ga:avgSessionDuration'] ),
						"avg_session_duration_cmp"=>adsforwp_get_compare_stats( $results['ga:avgSessionDuration'], $compare_results['ga:avgSessionDuration'], $date_different ),


						"bounce_rate" => adsforwp_beautify_number($results['ga:bounceRate']),
						"bounce_rate_cmp"=>adsforwp_get_compare_stats( $results['ga:bounceRate'], $compare_results['ga:bounceRate'], $date_different, 'bounce_rate' ),


						"page_view_per_session" => adsforwp_beautify_number($results['ga:pageviewsPerSession']),
						"page_view_per_session_cmp" => adsforwp_get_compare_stats( $results['ga:pageviewsPerSession'], $compare_results['ga:pageviewsPerSession'], $date_different ),

						"percent_new_session" => adsforwp_beautify_number($results['ga:percentNewSessions']),
						"percent_new_session_cmp"=> adsforwp_get_compare_stats( $results['ga:percentNewSessions'], $compare_results['ga:percentNewSessions'], $date_different ),

						"otherDeviceData"=>$device_data,
						"amp_pages"=>$ampPages,
			);
	}
	return $returnstats;
}

function adsforwp_get_compare_stats( $results, $compare_results, $date_different, $name='' ) {
    
	if ( $date_different == 0 ) { return; }
        $compare='';
        if($compare_results !=0){
        $compare = number_format( ( ( $results - $compare_results ) / $compare_results ) * 100, 2 );    
        }    	
	if ( 'bounce_rate' === $name ) {
		$class   = $compare < 0 ? 'adsforwp_green' : 'adsforwp_red';
	} else {
		$class   = $compare > 0 ? 'adsforwp_green' : 'adsforwp_red';
	}
	$compare = $compare>0 ? '+'.$compare : $compare;
	//' . $date_different . __( ' ago', 'ads-for-wp' ) . '
	return '<div class="adsforwp_general_status_footer_info">
			<span class="' . $class . '  adsforwp_info_value"> ' . $compare . ' %</span> 
	</div>';
        
}
function adsforwp_beautify_number($num){
	if ( is_numeric( $num ) ){

			if( $num > 10000){

				return round( ($num / 1000),2 ) . 'k';

			}else{
				return number_format( $num );
			}

		}else{
			return $num;
		}
} 
function adsforwp_beautify_time($time){
		// Check if numeric.
		if ( is_numeric( $time ) ) {

			$value = array(
				'years'   => '00',
				'days'    => '00',
				'hours'   => '',
				'minutes' => '',
				'seconds' => '',
				);

			if ( $time >= 31556926 ) {
				$value['years'] = floor( $time / 31556926 );
				$time           = ($time % 31556926);
			} //$time >= 31556926

			if ( $time >= 86400 ) {
				$value['days'] = floor( $time / 86400 );
				$time          = ($time % 86400);
			} //$time >= 86400
			if ( $time >= 3600 ) {
				$value['hours'] = str_pad( floor( $time / 3600 ), 1, 0, STR_PAD_LEFT );
				$time           = ($time % 3600);
			} //$time >= 3600
			if ( $time >= 60 ) {
				$value['minutes'] = str_pad( floor( $time / 60 ), 1, 0, STR_PAD_LEFT );
				$time             = ($time % 60);
			} //$time >= 60
					$value['seconds'] = str_pad( floor( $time ), 1, 0, STR_PAD_LEFT );
				// Get the hour:minute:second version.
			if ( '' != $value['hours'] ) {
				$attach_hours = '<span class="adsforwp_xl_f">' . _x( 'h', 'Hour Time', 'ads-for-wp' ) . ' </span> ';
			}
			if ( '' != $value['minutes'] ) {
				$attach_min = '<span class="adsforwp_xl_f">' . _x( 'm', 'Minute Time', 'ads-for-wp' ) . ' </span>';
			}
			if ( '' != $value['seconds'] ) {
				$attach_sec = '<span class="adsforwp_xl_f">' . _x( 's', 'Second Time', 'ads-for-wp' ) . '</span>';
			}
					return $value['hours'] . @$attach_hours . $value['minutes'] . @$attach_min . $value['seconds'] . $attach_sec;
				// return $value['hours'] . ':' . $value['minutes'] . ':' . $value['seconds'];
		} //is_numeric($time)
		else {
			return false;
		}
}

function adsforwp_get_analytics_dashboard( $metrics, $start_date, $end_date, $dimensions = false, $sort = false, $filter = false, $limit = false ) {
			global $GLOBALS;
			$settings = $GLOBALS['ADSFORWP'];
			$service = new Adsforwp_Google_Service_Analytics( $settings->client );
			try {
				$params        = array();

				if ( $dimensions ) {
					$params['dimensions'] = $dimensions;
				}
				if ( $sort ) {
					$params['sort'] = $sort;
				}
				if ( $filter ) {
					$params['filters'] = $filter;
				}
				if ( $limit ) {
					$params['max-results'] = $limit;
				}

				// $profile_id = get_option("pt_webprofile_dashboard");
				$profile_id = adsforwp_get_analytics_setiings('profile_for_dashboard');

				if ( ! $profile_id ) {
					return false;
				}

				return $service->data_ga->get( 'ga:' . $profile_id, $start_date, $end_date, $metrics, $params );

			} catch ( Adsforwp_Google_Service_Exception $e ) {

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

					echo "<div class=\"error-msg\">
				        <div class=\"wpb-error-box\">
					<span class=\"blk\">
						<span class=\"line\"></span>
						<span class=\"dot\"></span>
					</span>
					<span class=\"information-txt\">";
					print_r($e->getMessage());
					echo sprintf( esc_html__( '%1$s Oops, Something went wrong. %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s', 'ads-for-wp' ), '<br /><br />', '<br />', '<i>', '</i>', esc_html( $e->getMessage() ) );
					echo "</span>
				</div>
			</div>";

				}
			} catch ( Adsforwp_Google_Auth_Exception $e ) {

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

					echo sprintf( esc_html__( '%1$s Oops, Try to %3$s Reset %4$s Authentication. %2$s %7$s %2$s %5$s Don\'t worry, This error message is only visible to Administrators. %6$s %2$s', 'ads-for-wp' ), '<br /><br />', '<br />', '<a href=' . esc_url( admin_url( 'edit.php?post_type=adsforwp&page=adsforwp-analytics')), '</a>', '<i>', '</i>', esc_html( $e->getMessage() ) );
				}
			} catch ( Adsforwp_Google_IO_Exception $e ) {

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

					echo sprintf( esc_html__( '%1$s Oops! %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s', 'ads-for-wp' ), '<br /><br />', '<br />', '<i>', '</i>', esc_html( $e->getMessage() ) );
				}
			}
		}

function adsforwp_get_analytics_setiings($settingname){
	$settingOpt = get_option('adsforwp_analytics');
        $dashboard_profile_ID = $settingOpt['profile_for_dashboard'];
    if($settingname!=''){
    	if(isset($settingOpt[$settingname])){
    		return $settingOpt[$settingname];
    	}else{
    		return '';
    	}
    }else{
    	return $settingOpt;
    }
}

