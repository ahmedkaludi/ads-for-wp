<?php

add_action( 'admin_init', 'adsforwp_create_database_for_existing_users' );

function adsforwp_create_database_for_existing_users() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$status = get_option( 'adsforwp-database-on-first-load' );
	if ( $status != 'enable' ) {
		adsforwp_database_install();
		update_option( 'adsforwp-database-on-first-load', 'enable' );  // Security: Permission verified
	}
}
/**
 * Initial setup on plugin activation
 */
function adsforwp_on_activation() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	add_option( 'adsforwp_do_activation_redirect', true );
	set_transient( 'adsforwp_admin_notice_transient', true, 5 );
	update_option( 'adsforwp_activation_date', gmdate( 'Y-m-d' ) );  // Security: Permission verified

	adsforwp_database_install();
}

/**
 * Here, We create our own database and tables
 *
 * @global type $wpdb
 */
function adsforwp_database_install() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	global $wpdb;
	include_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$charset_collate = $engine = '';

	if ( ! empty( $wpdb->charset ) ) {
		$charset_collate .= " DEFAULT CHARACTER SET {$wpdb->charset}";
	}
	if ( $wpdb->has_cap( 'collation' ) && ! empty( $wpdb->collate ) ) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$DB_NAME    = DB_NAME;
	$table_name = $wpdb->prefix . 'posts';

	$cache_key    = 'adsforwp_found_engine';
	$found_engine = wp_cache_get( $cache_key );

	if ( false === $found_engine ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Need to check the engine of the table
		$found_engine = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT ENGINE FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = %s AND `TABLE_NAME` = %s;',
				$DB_NAME,
				$table_name
			)
		);
		wp_cache_set( $cache_key, $found_engine );
	}

	if ( strtolower( $found_engine ) == 'innodb' ) {
		$engine = ' ENGINE=InnoDB';
	}

	// Use caching for found tables
	$cache_key_tables = 'adsforwp_found_tables';
	$found_tables     = wp_cache_get( $cache_key_tables );

	if ( false === $found_tables ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Check if plugin tables already exist
		$found_tables = $wpdb->get_col( $wpdb->prepare( 'SHOW TABLES LIKE %s;', $wpdb->esc_like( $wpdb->prefix . 'adsforwp%' ) ) );
		wp_cache_set( $cache_key_tables, $found_tables );
	}

	if ( ! in_array( "{$wpdb->prefix}adsforwp_stats", $found_tables ) ) {

		dbDelta(
			"CREATE TABLE `{$wpdb->prefix}adsforwp_stats` (
            `id` bigint(9) unsigned NOT NULL auto_increment,
            `ad_id` int(50) unsigned NOT NULL default '0',            
            `ad_thetime` int(15) unsigned NOT NULL default '0',
            `ad_clicks` int(15) unsigned NOT NULL default '0',
            `ad_impressions` int(15) unsigned NOT NULL default '0',
            `ad_device_name` varchar(20) NOT NULL default '',
            PRIMARY KEY  (`id`),
            INDEX `ad_id` (`ad_id`),
            INDEX `ad_thetime` (`ad_thetime`)
        ) $charset_collate$engine;"
		);
	}
}



function adsforwp_now() {

	return time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
}

/**
 * Here, We get date in unix format as per condition
 *
 * @param  type $type
 * @return type string
 */
function adsforwp_get_date( $type ) {
	switch ( $type ) {
		case 'day':
			$timezone = get_option( 'timezone_string' );
			if ( $timezone ) {
				$result = strtotime( 'today', current_time( 'timestamp' ) );
			} else {
				$result = strtotime( 'today', time() );
			}
			break;
	}

	return $result;
}



/**
 * Here, We fetch ads stats from database table as per condition in query
 *
 * @global type $wpdb
 * @param  type $condition
 * @param  type $ad_id
 * @param  type $date
 * @return type array
 */
function adsforwp_get_ad_stats( $condition, $ad_id, $date = null ) {

	global $wpdb;
	$ad_stats = array();

	switch ( $condition ) {

		case 'sumofstats':
			// Set cache key
			$cache_key = 'adsforwp_stats_' . $ad_id;

			// Try to get cached data
			$result = wp_cache_get( $cache_key );

			if ( $result === false ) {
				// No cached data, perform the database query
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: working on custom table
				$result = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT SUM(`ad_clicks`) as `clicks`, SUM(`ad_impressions`) as `impressions` FROM `{$wpdb->prefix}adsforwp_stats` WHERE `ad_id` = %d;",
						$ad_id
					),
					ARRAY_A
				);

				// Cache the data
				wp_cache_set( $cache_key, $result );
			}

			$ad_stats['impressions'] = $result[0]['impressions'];
			$ad_stats['clicks']      = $result[0]['clicks'];

			break;

		case 'fetchAllBy':
			if ( $ad_id ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: working on custom table
				$result = $wpdb->get_results( $wpdb->prepare( "SELECT *FROM `{$wpdb->prefix}adsforwp_stats` WHERE `ad_thetime` = %d AND `ad_id` = %d;", $date, $ad_id ), ARRAY_A );

			} else {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: working on custom table
				$result = $wpdb->get_results( $wpdb->prepare( "SELECT *FROM `{$wpdb->prefix}adsforwp_stats` WHERE `ad_thetime` = %d;", $date ), ARRAY_A );

			}

			if ( $result ) {

				foreach ( $result as $row ) {

					if ( $row['ad_device_name'] == 'desktop' ) {

						if ( isset( $ad_stats['desktop']['click'] ) ) {
							$ad_stats['desktop']['click'] += $row['ad_clicks'];
						} else {
							$ad_stats['desktop']['click'] = $row['ad_clicks'];
						}

						if ( isset( $ad_stats['desktop']['impression'] ) ) {
							$ad_stats['desktop']['impression'] += $row['ad_impressions'];
						} else {
							$ad_stats['desktop']['impression'] = $row['ad_impressions'];
						}
					}
					if ( $row['ad_device_name'] == 'mobile' ) {

						if ( isset( $ad_stats['mobile']['click'] ) ) {
							$ad_stats['mobile']['click'] += $row['ad_clicks'];
						} else {
							$ad_stats['mobile']['click'] = $row['ad_clicks'];
						}

						if ( isset( $ad_stats['mobile']['impression'] ) ) {
							$ad_stats['mobile']['impression'] += $row['ad_impressions'];
						} else {
							$ad_stats['mobile']['impression'] = $row['ad_impressions'];
						}
					}
					if ( $row['ad_device_name'] == 'amp' ) {

						if ( isset( $ad_stats['amp']['click'] ) ) {
							$ad_stats['amp']['click'] += $row['ad_clicks'];
						} else {
							$ad_stats['amp']['click'] = $row['ad_clicks'];
						}

						if ( isset( $ad_stats['amp']['impression'] ) ) {
							$ad_stats['amp']['impression'] += $row['ad_impressions'];
						} else {
							$ad_stats['amp']['impression'] = $row['ad_impressions'];
						}
					}
				}
			}

			break;

		default:
			break;
	}
	return $ad_stats;
}
