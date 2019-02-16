<?php

add_action('admin_init', 'adsforwp_create_database_for_existing_users');

function adsforwp_create_database_for_existing_users(){
 
		$status = get_option('adsforwp-database-on-first-load');
 
		if($status !='enable'){
                    
			adsforwp_database_install();                        
			update_option('adsforwp-database-on-first-load', 'enable');			
                        
		}
 		   
}
/**
 * Initial setup on plugin activation
 */
function adsforwp_on_activation() { 
    
    add_option('adsforwp_do_activation_redirect', true);       
    set_transient( 'adsforwp_admin_notice_transient', true, 5 );
    update_option( "adsforwp_activation_date", date("Y-m-d"));
            
    adsforwp_database_install();
                    
}

/**
 * Here, We create our own database and tables 
 * @global type $wpdb
 */
function adsforwp_database_install() {
    
	global $wpdb;                

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$charset_collate = $engine = '';	
	

	if(!empty($wpdb->charset)) {
		$charset_collate .= " DEFAULT CHARACTER SET {$wpdb->charset}";
	} 
	if($wpdb->has_cap('collation') AND !empty($wpdb->collate)) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$found_engine = $wpdb->get_var("SELECT ENGINE FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '".DB_NAME."' AND `TABLE_NAME` = '{$wpdb->prefix}posts';");
        
	if(strtolower($found_engine) == 'innodb') {
		$engine = ' ENGINE=InnoDB';
	}

	$found_tables = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}adsforwp%';");	
        
	if(!in_array("{$wpdb->prefix}adsforwp_stats", $found_tables)) {
            
		dbDelta("CREATE TABLE `{$wpdb->prefix}adsforwp_stats` (
			`id` bigint(9) unsigned NOT NULL auto_increment,
			`ad_id` int(50) unsigned NOT NULL default '0',			
			`ad_thetime` int(15) unsigned NOT NULL default '0',
			`ad_clicks` int(15) unsigned NOT NULL default '0',
			`ad_impressions` int(15) unsigned NOT NULL default '0',
                        `ad_device_name` varchar(20) NOT NULL default '',
			PRIMARY KEY  (`id`),
			INDEX `ad_id` (`ad_id`),
			INDEX `ad_thetime` (`ad_thetime`)
		) ".$charset_collate.$engine.";");
                
	}

}

/**
 *  Actions on plugin deactivation
 */
function adsforwp_on_deactivation() {    
    
}

function adsforwp_now() {
    
	return time() + (get_option('gmt_offset') * HOUR_IN_SECONDS);
        
}

/**
 * Here, We get date in unix format as per condition
 * @param type $type
 * @return type string
 */
function adsforwp_get_date($type) {
    	
	switch($type) {
		
		case 'day' :
			$timezone = get_option('timezone_string');
			if($timezone) {
				$server_timezone = date('e');
				date_default_timezone_set($timezone);
				$result = strtotime('00:00:00') + (get_option('gmt_offset') * 3600);
				date_default_timezone_set($server_timezone);
			} else {
				$result = gmdate('U', gmmktime(0, 0, 0, gmdate('n'), gmdate('j')));
			}
		break;
				
	}

	return $result;
}


/**
 * Here, We fetch ads stats from database table as per condition in query
 * @global type $wpdb
 * @param type $condition
 * @param type $ad_id
 * @param type $date
 * @return type array
 */
function adsforwp_get_ad_stats($condition, $ad_id, $date=null) {
    
    global $wpdb;
    $ad_stats = array();
    
    switch ($condition) {
        
        case 'sumofstats':

            $result = $wpdb->get_results($wpdb->prepare("SELECT SUM(`ad_clicks`) as `clicks`, SUM(`ad_impressions`) as `impressions` FROM `{$wpdb->prefix}adsforwp_stats` WHERE `ad_id` = %d;", $ad_id), ARRAY_A);
            
            $ad_stats['impressions'] = $result[0]['impressions'];
            $ad_stats['clicks']      = $result[0]['clicks'];
                        
            
            break;
        
        case 'fetchAllBy':

            
            if($ad_id){
                
                $result = $wpdb->get_results($wpdb->prepare("SELECT *FROM `{$wpdb->prefix}adsforwp_stats` WHERE `ad_thetime` = %d AND `ad_id` = %d;", $date, $ad_id), ARRAY_A);
                
            }else{
            
                $result = $wpdb->get_results($wpdb->prepare("SELECT *FROM `{$wpdb->prefix}adsforwp_stats` WHERE `ad_thetime` = %d;", $date), ARRAY_A);
                
            }                        
                
            if($result){
                                               
                foreach($result as $row){
                     
                    if($row['ad_device_name'] =='desktop'){
                       
                        $ad_stats['desktop']['click']      +=  $row['ad_clicks'];
                        $ad_stats['desktop']['impression'] +=  $row['ad_impressions'];

                    }
                    if($row['ad_device_name'] =='mobile'){
                       
                        $ad_stats['mobile']['click']      +=  $row['ad_clicks'];
                        $ad_stats['mobile']['impression'] +=  $row['ad_impressions'];   
                        
                    }
                    if($row['ad_device_name'] =='amp'){
                        
                        $ad_stats['amp']['click']      +=  $row['ad_clicks'];
                        $ad_stats['amp']['impression'] +=  $row['ad_impressions'];
                     
                    }
                                        
                }
                
            }
            
            break;

        default:
            break;
    }            
    return $ad_stats;
}

?>