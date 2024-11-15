<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Adsforwp_Admin_Analytics {


	public function __construct() {
	}

	/**
	 * This is the list of hooks used in this class
	 */
	public function adsforwp_admin_analytics_hooks() {

		add_action( 'wp_ajax_nopriv_adsforwp_insert_ad_impression', array( $this, 'adsforwp_insert_ad_impression' ) );
		add_action( 'wp_ajax_adsforwp_insert_ad_impression', array( $this, 'adsforwp_insert_ad_impression' ) );

		add_action( 'wp_ajax_nopriv_adsforwp_insert_ad_clicks', array( $this, 'adsforwp_insert_ad_clicks' ) );
		add_action( 'wp_ajax_adsforwp_insert_ad_clicks', array( $this, 'adsforwp_insert_ad_clicks' ) );

		add_action( 'wp_ajax_nopriv_adsforwp_insert_ad_clicks_amp', array( $this, 'adsforwp_insert_ad_clicks_amp' ) );
		add_action( 'wp_ajax_adsforwp_insert_ad_clicks_amp', array( $this, 'adsforwp_insert_ad_clicks_amp' ) );

		add_action( 'wp_ajax_nopriv_adsforwp_insert_ad_impression_amp', array( $this, 'adsforwp_insert_ad_impression_amp' ) );
		add_action( 'wp_ajax_adsforwp_insert_ad_impression_amp', array( $this, 'adsforwp_insert_ad_impression_amp' ) );

		add_filter( 'amp_post_template_data', array( $this, 'adsforwp_enque_analytics_amp_script' ) );
		add_filter( 'amp_post_template_footer', array( $this, 'adsforwp_add_analytics_amp_tags' ) );
	}


	/**
	 * Here, We are enquing amp scripts.
	 *
	 * @param  type $data
	 * @return string
	 */
	public function adsforwp_enque_analytics_amp_script( $data ) {
		if ( empty( $data['amp_component_scripts']['amp-analytics'] ) ) {
				$data['amp_component_scripts']['amp-analytics'] = 'https://cdn.ampproject.org/v0/amp-analytics-latest.js';
		}
		if ( empty( $data['amp_component_scripts']['amp-bind'] ) ) {
				$data['amp_component_scripts']['amp-bind'] = 'https://cdn.ampproject.org/v0/amp-bind-0.1.js';
		}
		if ( empty( $data['amp_component_scripts']['amp-user-notification'] ) ) {
				$data['amp_component_scripts']['amp-user-notification'] = 'https://cdn.ampproject.org/v0/amp-user-notification-0.1.js';
		}
		if ( empty( $data['amp_component_scripts']['amp-ad'] ) ) {
				$data['amp_component_scripts']['amp-ad'] = 'https://cdn.ampproject.org/v0/amp-ad-latest.js';
		}
		if ( empty( $data['amp_component_scripts']['amp-iframe'] ) ) {
				$data['amp_component_scripts']['amp-iframe'] = 'https://cdn.ampproject.org/v0/amp-iframe-latest.js';
		}
		return $data;
	}

	/**
	 * Here, We are adding amp analytics tag for every ad serve on page
	 */
	public function adsforwp_add_analytics_amp_tags() {

		$settings = adsforwp_defaultSettings();

		if ( isset( $settings['ad_performance_tracker'] ) ) {

			if ( ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {

				$amp_ads_id = json_decode( get_transient( 'adsforwp_transient_amp_ids' ), true );

				if ( ! empty( $amp_ads_id ) ) {

					$amp_ads_id = array_unique( $amp_ads_id );

				}

				$ad_impression_script = '';
				$ad_clicks_script     = '';

				$nonce             = wp_create_nonce( 'adsforwp_ajax_check_front_nonce' );
				$ad_impression_url = admin_url( 'admin-ajax.php?action=adsforwp_insert_ad_impression_amp&adsforwp_front_nonce=' . $nonce );
				$ad_clicks_url     = admin_url( 'admin-ajax.php?action=adsforwp_insert_ad_clicks_amp&adsforwp_front_nonce=' . $nonce );

				if ( $amp_ads_id ) {

					$content_post = get_post( get_the_ID() );
					$content      = $content_post->post_content;
					foreach ( $amp_ads_id as $ad_id ) {
						$post_meta_dataset      = array();
						$post_meta_dataset      = get_post_meta( $ad_id, $key = '', true );
						$where_to_display       = adsforwp_rmv_warnings( $post_meta_dataset, 'wheretodisplay', 'adsforwp_array' );
						$jquery_selector        = adsforwp_rmv_warnings( $post_meta_dataset, 'adsforwp_jquery_selector', 'adsforwp_array' );
						$custom_target_position = adsforwp_rmv_warnings( $post_meta_dataset, 'adsforwp_custom_target_position', 'adsforwp_array' );
						$new_element            = adsforwp_rmv_warnings( $post_meta_dataset, 'adsforwp_new_element', 'adsforwp_array' );

						if ( $where_to_display == 'ad_shortcode' ) {
							if ( ! has_shortcode( $content, 'adsforwp' ) ) {
								continue;
							} elseif ( ! preg_match( '/\[adsforwp(.*?)id=\"' . $ad_id . '\"\]/', $content, $matches ) ) {
									continue;
							}
						} elseif ( $where_to_display == 'custom_target' ) {
							if ( $custom_target_position == 'existing_element' && ! empty( $jquery_selector ) ) {
								$idselector    = ltrim( $jquery_selector, '#' );
								$classselector = ltrim( $jquery_selector, '.' );
								if ( strpos( $jquery_selector, '#' ) !== false ) {
									if ( ! preg_match( '/id=\"' . $idselector . '\"/', $content, $matches ) ) {
										continue;
									}
								} elseif ( strpos( $jquery_selector, '.' ) !== false ) {
									if ( ! preg_match( '/class=\"' . $classselector . '\"/', $content, $matches ) ) {
										continue;
									}
								}
							} elseif ( $custom_target_position == 'new_element' && ! empty( $new_element ) ) {
								$new_element = html_entity_decode( $new_element );
								preg_match( '/<div\sid=\"(.*?)\"(.*?)>/', $new_element, $matches );
								if ( $matches ) {
									if ( ! preg_match( '/' . $matches[1] . '/', $content, $match ) ) {
										continue;
									}
								} else {
									continue;
								}
							}
						}
						?>
			<amp-analytics><script type="application/json">
					{
					"requests": {
						"event": "'.esc_url($ad_impression_url).'&event=${eventId}"
					},
					"triggers": {
						"trackPageview": {
						"on": "visible",
						"request": "event",
						"visibilitySpec": {
							"selector": ".afw_ad_amp_'.esc_attr($ad_id).'",
							"visiblePercentageMin": 20,
							"totalTimeMin": 500,
							"continuousTimeMin": 200
						},                                  
						"vars": {
							"eventId": "'.esc_attr($ad_id).'"
						}
						}
					}
					}</script></amp-analytics>                                  
			   
			
			<amp-analytics>
				<script type="application/json">
					{
					"requests": {
						"event": "'.esc_url_raw($ad_clicks_url).'&event=${eventId}"
					},
					"triggers": {
						"trackAnchorClicks": {
						"on": "click",
						"selector": ".afw_ad_amp_anchor_'.esc_attr($ad_id).'",
						"request": "event",
						"vars": {
							"eventId": "'.esc_attr($ad_id).'"
						}
						}
					}
					}
				</script>
				</amp-analytics>

							<?php
					}
				}
			}
		}
	}

	/**
	 * Function to insert ad impression for both (AMP and NON AMP)
	 *
	 * @global type $wpdb
	 * @param  type $ad_id
	 * @param  type $device_name
	 */
	public function adsforwp_insert_impression( $ad_id, $device_name ) {
		global $wpdb;

		$today = adsforwp_get_date( 'day' );

		$stats = wp_cache_get( 'adsforwp_get_stats_impr_' . $ad_id );

		if ( $stats === false ) {
          // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason:working on custom table
			$stats = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT `id` FROM `{$wpdb->prefix}adsforwp_stats` WHERE `ad_id` = %d AND `ad_device_name` = %s AND `ad_thetime` = %d;",
					$ad_id,
					trim( $device_name ),
					$today
				)
			);
			wp_cache_set( 'adsforwp_get_stats_impr_' . $ad_id, $stats );
		}
		if ( $stats > 0 ) {
          // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason:working on custom table
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE `{$wpdb->prefix}adsforwp_stats` SET `ad_impressions` = `ad_impressions` + 1 WHERE `id` = %d;",
					$stats
				)
			);
		} else {
           // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason:working on custom table
			$wpdb->insert(
				$wpdb->prefix . 'adsforwp_stats',
				array(
					'ad_id'          => $ad_id,
					'ad_thetime'     => $today,
					'ad_clicks'      => 0,
					'ad_impressions' => 1,
					'ad_device_name' => trim( $device_name ),
				),
				array(
					'%d', // ad_id
					'%d', // ad_thetime
					'%d', // ad_clicks
					'%d', // ad_impressions
					'%s',  // ad_device_name
				)
			);
		}
	}


	/**
	 * Function to insert ad clicks for both (AMP and NON AMP)
	 *
	 * @global type $wpdb
	 * @param  type $ad_id
	 * @param  type $device_name
	 */
	public function adsforwp_insert_clicks( $ad_id, $device_name ) {
		global $wpdb;

		$today = adsforwp_get_date( 'day' );

		$stats = wp_cache_get( 'adsforwp_get_stats_clicks_' . $ad_id );

		if ( $stats === false ) {
          // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason:working on custom table
			$stats = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT `id` FROM `{$wpdb->prefix}adsforwp_stats` WHERE `ad_id` = %d AND `ad_device_name` = %s AND `ad_thetime` = %d;",
					$ad_id,
					trim( $device_name ),
					$today
				)
			);
			wp_cache_set( 'adsforwp_get_stats_clicks_' . $ad_id, $stats );
		}
		if ( $stats > 0 ) {
          //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: working on custom table
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE `{$wpdb->prefix}adsforwp_stats` SET `ad_clicks` = `ad_clicks` + 1 WHERE `id` = %d;",
					$stats
				)
			);
		} else {
          //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: working on custom table
			$wpdb->insert(
				$wpdb->prefix . 'adsforwp_stats',
				array(
					'ad_id'          => $ad_id,
					'ad_thetime'     => $today,
					'ad_clicks'      => 1, // Initial click count set to 1
					'ad_impressions' => 0, // Initial impressions count set to 0
					'ad_device_name' => trim( $device_name ),
				),
				array(
					'%d', // ad_id
					'%d', // ad_thetime
					'%d', // ad_clicks
					'%d', // ad_impressions
					'%s',  // ad_device_name
				)
			);
		}
	}


	/**
	 * Ajax handler to get ad impression in AMP
	 *
	 * @return type void
	 */
	public function adsforwp_insert_ad_impression_amp() {

		if ( ! isset( $_GET['adsforwp_front_nonce'] ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
		if ( ! wp_verify_nonce( $_GET['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ) {
			return;
		}

			$ad_id       = isset( $_GET['event'] ) ? sanitize_text_field( wp_unslash( $_GET['event'] ) ) : '';
			$device_name = 'amp';

		if ( $ad_id ) {
			$this->adsforwp_insert_impression( $ad_id, $device_name );
		}

			wp_die();
	}

	/**
	 * Ajax handler to get ad impression in NON AMP
	 *
	 * @return type void
	 */
	public function adsforwp_insert_ad_impression() {

		if ( ! isset( $_POST['adsforwp_front_nonce'] ) ) {
			return;
		}
		$settings = adsforwp_defaultSettings();
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
		if ( ! wp_verify_nonce( $_POST['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ) {
			return;
		}

			$ad_ids 		=	array();
			if ( ! empty( $_POST['ad_ids'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$ad_ids 	= 	array_map( 'sanitize_text_field', $_POST['ad_ids'] );		
			} 

			$device_name = isset( $_POST['device_name'] ) ? sanitize_text_field( wp_unslash( $_POST['device_name'] ) ) : '';
			$is_track_impression = false;
			if($ad_ids){
				$is_track_impression = true;
			}
			if($this->is_admin_user()){
				if ( isset( $settings['ad_performance_tracker_for_admin'] ) && $settings['ad_performance_tracker_for_admin']==1) {
					if($ad_ids){
						$is_track_impression = true;
					}
				}else{
					$is_track_impression = false;
				}
			}
			
			if ( $is_track_impression==true) {

			foreach ( $ad_ids as $ad_id ) {

				if ( $ad_id ) {

					$this->adsforwp_insert_impression( $ad_id, $device_name );

				}
			}//Foreach closed
		}
			wp_die();
	}

	public function is_admin_user() {
		$status = false;
		if ( is_user_logged_in() ) {
			if ( current_user_can( 'administrator' ) ) {
				$status = true;
			}
		}
		return $status;
	}

	/**
	 * Ajax handler to get ad clicks in NON AMP
	 *
	 * @return type void
	 */
	public function adsforwp_insert_ad_clicks() {

		if ( ! isset( $_POST['adsforwp_front_nonce'] ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
		if ( ! wp_verify_nonce( $_POST['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ) {
			return;
		}

			$device_name = isset( $_POST['device_name'] ) ? sanitize_text_field( wp_unslash( $_POST['device_name'] ) ) : '';
			$ad_id       = isset( $_POST['ad_id'] ) ? sanitize_text_field( wp_unslash( $_POST['ad_id'] ) ) : '';
		if ( $ad_id && ! $this->is_admin_user() ) {
			$this->adsforwp_insert_clicks( $ad_id, $device_name );
		}
			wp_die();
	}

	/**
	 * Ajax handler to get ad clicks in AMP
	 *
	 * @return type void
	 */
	public function adsforwp_insert_ad_clicks_amp() {

		if ( ! isset( $_GET['adsforwp_front_nonce'] ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
		if ( ! wp_verify_nonce( $_GET['adsforwp_front_nonce'], 'adsforwp_ajax_check_front_nonce' ) ) {
			return;
		}

			$ad_id       = isset( $_GET['event'] ) ? sanitize_text_field( wp_unslash( $_GET['event'] ) ) : '';
			$device_name = 'amp';

		if ( $ad_id ) {

			$this->adsforwp_insert_clicks( $ad_id, $device_name );

		}
			wp_die();
	}
}
if ( class_exists( 'Adsforwp_Admin_Analytics' ) ) {
	$adsforwp_analytics_hooks_obj = new Adsforwp_Admin_Analytics();
		$adsforwp_analytics_hooks_obj->adsforwp_admin_analytics_hooks();
}
