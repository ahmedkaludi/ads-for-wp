<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Display the contents of /ads.txt when requested.
 * since v1.9.3
 *
 * @return void
 */
function adsforwp_display_ads_txt() {

		$settings = adsforwp_defaultSettings();

	if ( isset( $settings['adsforwp_ads_txt'] ) ) {

		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) {
			$link = 'https';
		} else {
			$link = 'http';
		}

		$host = '';
		if ( isset( $_SERVER['HTTP_HOST'] ) && ! empty( $_SERVER['HTTP_HOST'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$host = $_SERVER['HTTP_HOST'];
		}

		$link .= '://';
		$link .= $host;
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason Data is not getting stored in DB so unslash is not necessary.
		$link .= isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( $_SERVER['REQUEST_URI'] ) : '';

		if ( trailingslashit( get_site_url() ) . 'ads.txt' === esc_url_raw( $link ) ) {

				$ad_txt = '';

			if ( isset( $settings['adsforwp_adstxt'] ) && $settings['adsforwp_adstxt'] != '' ) {

				$ad_txt = $settings['adsforwp_adstxt'];

			}
			// Will fall through if no option found, likely to a 404.
			if ( $ad_txt ) {

				header( 'Content-Type: text/plain' );
				echo esc_html( $ad_txt );
				die();
			}
		}
	}
}

add_action( 'init', 'adsforwp_display_ads_txt' );

add_action( 'init', 'adsforwp_store_user_info_client_side' );

function adsforwp_store_user_info_client_side() {

	if ( ! is_admin() ) {

		$visitor_obj = new Adsforwp_View_Visitor_Condition();
		$user_ip     = $visitor_obj->adsforwp_get_client_ip();

		$saved_ip      = '';
		$saved_ip_list = array();

		if ( isset( $_COOKIE['adsforwp-user-info'] ) ) {

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason Just fetching the data from cookie
			$saved_ip_list = $_COOKIE['adsforwp-user-info'];
			$saved_ip      = trim( base64_decode( $saved_ip_list[0] ) );

		}

		if ( empty( $saved_ip_list ) && $saved_ip != $user_ip ) {
			$request_day_count = get_option( 'adsforwp_ip_request_' . gmdate( 'Y-m-d' ) );

			if ( $request_day_count ) {

				$request_day_count += 1;

			} else {

				$request_day_count = 1;

			}

				update_option( 'adsforwp_ip_request_' . gmdate( 'Y-m-d' ), $request_day_count );

				$settings = adsforwp_defaultSettings();

			if ( isset( $settings['adsforwp_geolocation_api'] ) && ! empty( trim( $settings['adsforwp_geolocation_api'] ) ) ) {

				$geo_location_data = wp_remote_get( 'https://api.ipgeolocation.io/ipgeo?apiKey=' . $settings['adsforwp_geolocation_api'] . '&ip=' . $user_ip . '&fields=country_code3' );
				if ( ! is_wp_error( $geo_location_data ) && 200 == wp_remote_retrieve_response_code( $geo_location_data ) ) {
					$geo_location_arr = json_decode( $geo_location_data['body'], true );

					if ( isset( $geo_location_arr['ip'] ) && isset( $geo_location_arr['country_code3'] ) ) {

							setcookie( 'adsforwp-user-info[0]', trim( base64_encode( $geo_location_arr['ip'] ) ), time() + ( 86400 * 60 ), '/' );
							setcookie( 'adsforwp-user-info[1]', trim( base64_encode( $geo_location_arr['country_code3'] ) ), time() + ( 86400 * 60 ), '/' );

					}
				}
			}
		}
	}
}

/**
 * Function to reset all the settings and delete the ads and groups list
 *
 * @return type json
 */
function adsforwp_reset_all_settings() {

	if ( ! current_user_can( 'manage_options' ) ) {
				return;
	}

	if ( ! isset( $_POST['adsforwp_security_nonce'] ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
	if ( ! wp_verify_nonce( $_POST['adsforwp_security_nonce'], 'adsforwp_ajax_check_nonce' ) ) {
		return;
	}

		$result = '';

		// Deleting Settings
		delete_option( 'adsforwp_settings' ); // Security: Permission and nonce verified

		// Deleting Ads
		$allposts = get_posts(
			array(
				'post_type'   => 'adsforwp',
				'numberposts' => -1,
			)
		);

	if ( $allposts ) {

		foreach ( $allposts as $eachpost ) {

			$result = wp_delete_post( $eachpost->ID, true );

		}
	}

		// Deleting group Ads

		$allposts = get_posts(
			array(
				'post_type'   => 'adsforwp-groups',
				'numberposts' => -1,
			)
		);

	if ( $allposts ) {

		foreach ( $allposts as $eachpost ) {
			$result = wp_delete_post( $eachpost->ID, true );
		}
	}

	if ( $result ) {
			echo wp_json_encode( array( 'status' => 't' ) );
	} else {
			echo wp_json_encode( array( 'status' => 'f' ) );
	}
			wp_die();
}

add_action( 'wp_ajax_adsforwp_reset_all_settings', 'adsforwp_reset_all_settings' );

function adsforwp_load_plugin_textdomain() {
	//phpcs:ignore WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found -- Reason : 2nd parameter is required to be empty string as per WP Docs.
	load_plugin_textdomain( 'ads-for-wp', '', basename( __DIR__ ) . '/languages/' );
}
add_action( 'plugins_loaded', 'adsforwp_load_plugin_textdomain' );

/**
 * Function to remove warnings for undefined array and string
 *
 * @param  type $data
 * @param  type $index
 * @param  type $type
 * @return string
 */
function adsforwp_rmv_warnings( $data, $index, $type ) {

	if ( $type == 'adsforwp_array' ) {

		if ( isset( $data[ $index ] ) ) {
						return $data[ $index ][0];
		} else {
							return '';
		}
	}

	if ( $type == 'adsforwp_string' ) {

		if ( isset( $data[ $index ] ) ) {
						return $data[ $index ];
		} else {
			return '';
		}
	}
}

/**
 * Filter slugs
 *
 * @global type $typenow
 * @global type $wp_query
 */
function adsforwp_filter_tracked_plugins() {

	global $typenow;
	global $wp_query;
	if ( $typenow == 'adsforwp' ) { // Your custom post type slug
		$plugins = array(
			'ad_shortcode'        => esc_html__( 'Shortcode (Manual)', 'ads-for-wp' ),
			'between_the_content' => esc_html__( 'Between the Content (Automatic)', 'ads-for-wp' ),
			'after_the_content'   => esc_html__( 'After the Content (Automatic)', 'ads-for-wp' ),
			'before_the_content'  => esc_html__( 'Before the Content (Automatic)', 'ads-for-wp' ),
			'custom_target'       => esc_html__( 'Custom Target', 'ads-for-wp' ),
			'sticky'              => esc_html__( 'Sticky', 'ads-for-wp' ),
		); // Options for the filter select field
        //phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason : setting select box value if ad-type-slug is present in url without making any sensitive changes or performing critical actions.
		$current_plugin = isset( $_GET['slug'] ) ? esc_attr( $_GET['slug'] ) : '';
		?>
		<select name="slug" id="slug">
		<option value="all" <?php selected( 'all', $current_plugin ); ?>><?php esc_html_e( 'All', 'ads-for-wp' ); ?></option>
		<?php foreach ( $plugins as $key => $value ) { ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $current_plugin ); ?>><?php echo esc_attr( $value ); ?></option>
		<?php } ?>
		</select>
		<?php
	}
}
add_action( 'restrict_manage_posts', 'adsforwp_filter_tracked_plugins' );


/**
 * Function to add display type filter in ads list dashboard
 *
 * @global type $pagenow
 * @param  type $query
 */
function adsforwp_sort_ads_by_display_type( $query ) {

	global $pagenow;
	// Get the post type
  //phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Reason : Nonce verification is not required here , using post_type for filtering
	$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
  //phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Reason : Nonce verification is not required here , using slug for filtering
	$slug = isset( $_GET['slug'] ) ? sanitize_text_field( $_GET['slug'] ) : '';

	if ( is_admin() && $pagenow == 'edit.php' && $post_type == 'adsforwp' && $slug != 'all' ) {
	
		$query->query_vars['meta_key']     = 'wheretodisplay';//phpcs:ignore -- WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- just using in admin on ads list page
		$query->query_vars['meta_value']   = esc_attr( $slug );//phpcs:ignore -- WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- just using in admin on ads list page
		$query->query_vars['meta_compare'] = '=';

	}
}

add_filter( 'parse_query', 'adsforwp_sort_ads_by_display_type' );


/**
 * Function to add ad type filter in ads list dashboard
 *
 * @global type $pagenow
 * @param  type $query
 */
function adsforwp_filter_by_ad_type() {

	global $typenow;
	global $wp_query;

	if ( $typenow == 'adsforwp' ) { // Your custom post type slug

		$plugins = array(
			'adsense'   => esc_html__( 'AdSense', 'ads-for-wp' ),
			'media_net' => esc_html__( 'Media.net', 'ads-for-wp' ),
			'ad_now'    => esc_html__( 'AdNow', 'ads-for-wp' ),
			'contentad' => esc_html__( 'Content.ad', 'ads-for-wp' ),
			'infolinks' => esc_html__( 'Infolinks', 'ads-for-wp' ),
			'ad_image'  => esc_html__( 'Image Banner Ad', 'ads-for-wp' ),
			'custom'    => esc_html__( 'Custom Code', 'ads-for-wp' ),
		); // Options for the filter select field
        //phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Reason : setting select box value if ad-type-slug is present in url without making any sensitive changes or performing critical actions.
		$current_plugin = isset( $_GET['ad-type-slug'] ) ? sanitize_text_field( $_GET['ad-type-slug'] ) : '';
		?>
		<select name="ad-type-slug" id="ad-type-slug">
		<option value="all" <?php selected( 'all', $current_plugin ); ?>><?php esc_html_e( 'All', 'ads-for-wp' ); ?></option>
		<?php foreach ( $plugins as $key => $value ) { ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $current_plugin ); ?>><?php echo esc_attr( $value ); ?></option>
		<?php } ?>
		</select>
		<?php
	}
}
add_action( 'restrict_manage_posts', 'adsforwp_filter_by_ad_type' );


/**
 * Function to sort by ad type
 *
 * @global type $pagenow
 * @param  type $query
 */
function adsforwp_sort_ads_by_type( $query ) {

	global $pagenow;
	// Get the post type
  //phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Reason : Nonce verification is not required here , using post_type for filtering
	$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
   //phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Reason : Nonce verification is not required here , using ad-type-slug for filtering
	$ad_type_slug = isset( $_GET['ad-type-slug'] ) ? sanitize_text_field( $_GET['ad-type-slug'] ) : '';
	if ( is_admin() && $pagenow == 'edit.php' && $post_type == 'adsforwp' && $ad_type_slug != 'all' ) {
		
		$query->query_vars['meta_key']     = 'select_adtype';//phpcs:ignore -- WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- just using in import button click not all the time
		$query->query_vars['meta_value']   = esc_attr( $ad_type_slug );//phpcs:ignore -- WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- just using in import button click not all the time
		$query->query_vars['meta_compare'] = '=';
	}
}
add_filter( 'parse_query', 'adsforwp_sort_ads_by_type' );


function adsforwp_review_notice_remindme() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_POST['adsforwp_security_nonce'] ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
	if ( ! wp_verify_nonce( $_POST['adsforwp_security_nonce'], 'adsforwp_ajax_check_nonce' ) ) {
		return;
	}
		$result = update_option( 'review_notice_bar_close_date', gmdate( 'Y-m-d' ) );   // Security: Permission and nonce verified

	if ( $result ) {

		echo wp_json_encode( array( 'status' => 't' ) );

	} else {

		echo wp_json_encode( array( 'status' => 'f' ) );

	}
			wp_die();
}

add_action( 'wp_ajax_adsforwp_review_notice_remindme', 'adsforwp_review_notice_remindme' );


function adsforwp_review_notice_close() {

	if ( ! isset( $_POST['adsforwp_security_nonce'] ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
	if ( ! wp_verify_nonce( $_POST['adsforwp_security_nonce'], 'adsforwp_ajax_check_nonce' ) ) {
		return;
	}

		$result = update_option( 'adsforwp_review_never', 'never' );   // Security: Permission and nonce verified

	if ( $result ) {

		echo wp_json_encode( array( 'status' => 't' ) );

	} else {

		echo wp_json_encode( array( 'status' => 'f' ) );

	}
			wp_die();
}

add_action( 'wp_ajax_adsforwp_review_notice_close', 'adsforwp_review_notice_close' );

	/**
	 * This is a ajax handler function for importing plugins data.
	 *
	 * @return type json string
	 */
function adsforwp_import_plugin_data() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_GET['adsforwp_security_nonce'] ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
	if ( ! wp_verify_nonce( $_GET['adsforwp_security_nonce'], 'adsforwp_ajax_check_nonce' ) ) {
		return;
	}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason Data is not getting stored into database, just using this to validate the condition
		$plugin_name         = isset( $_GET['plugin_name'] ) ? sanitize_text_field( $_GET['plugin_name'] ) : '';
		$common_function_obj = new Adsforwp_Admin_Common_Functions();
		$result              = array();

	switch ( $plugin_name ) {

		case 'advanced_ads':
			if ( is_plugin_active( 'advanced-ads/advanced-ads.php' ) ) {
				$result = $common_function_obj->adsforwp_import_all_advanced_ads();
			}
			break;

		case 'ampforwp_ads':
			if ( is_plugin_active( 'accelerated-mobile-pages/accelerated-moblie-pages.php' ) ) {
				$result = $common_function_obj->adsforwp_import_all_amp_ads();

			}
			break;
		case 'ampforwp_advanced_ads':
			if ( is_plugin_active( 'accelerated-mobile-pages/accelerated-moblie-pages.php' ) ) {
				$result = $common_function_obj->adsforwp_import_all_advanced_amp_ads();

			}
			break;
		case 'ad_inserter':
			if ( is_plugin_active( 'ad-inserter/ad-inserter.php' ) ) {
				$result = $common_function_obj->adsforwp_import_all_ad_inserter_ads();

			}
			break;
		case 'quick_adsense':
			if ( is_plugin_active( 'quick-adsense/quick-adsense.php' ) ) {
				$result = $common_function_obj->adsforwp_import_all_quick_adsense_ads();
			}
			break;

		default:
			break;
	}
		$result = array_filter( $result );

	if ( $result ) {

		echo wp_json_encode(
			array(
				'status'  => 't',
				'message' => esc_html__( 'Data has been imported succeessfully', 'ads-for-wp' ),
			)
		);

	} else {

		echo wp_json_encode(
			array(
				'status'  => 'f',
				'message' => esc_html__( 'Plugin data is not available or it is not activated', 'ads-for-wp' ),
			)
		);

	}
			wp_die();
}

add_action( 'wp_ajax_adsforwp_import_plugin_data', 'adsforwp_import_plugin_data' );

/**
 * Remove Add new menu
 **/
function adsforwp_disable_new_posts() {
	// Hide sidebar link
	global $submenu;
	unset( $submenu['edit.php?post_type=adsforwp'][10] );
}
add_action( 'admin_menu', 'adsforwp_disable_new_posts' );

	/**
	 * This is a ajax handler function for sending email from user admin panel to us.
	 *
	 * @return type json string
	 */
function adsforwp_send_query_message() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_POST['adsforwp_security_nonce'] ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
	if ( ! wp_verify_nonce( $_POST['adsforwp_security_nonce'], 'adsforwp_ajax_check_nonce' ) ) {
		return;
	}

	if ( is_user_logged_in() ) {

		include_once ABSPATH . 'wp-includes/pluggable.php';
		$customer_type = 'Are you a premium customer ? No';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason Data is not getting stored into database
		$message       = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason Data is not getting stored into database
		$premium_cus   = isset( $_POST['premium_cus'] ) ? sanitize_textarea_field( $_POST['premium_cus'] ) : '';
		$user          = wp_get_current_user();
		$user_data     = $user->data;
		$user_email    = $user_data->user_email;

		if ( $premium_cus == 'yes' ) {
				$customer_type = 'Are you a premium customer ? Yes';
		}

		$message = '<p>' . $message . '</p><br><br>'
			. $customer_type
			. '<br><br>' . 'Query from plugin support tab';
		if ( $email ) {
			$user_email = $email;
		}
		// php mailer variables
		$to      = 'team@magazine3.in';
		$subject = 'Ads For WP Customer Query';
		$headers = 'From: ' . esc_attr( $user_email ) . "\r\n" .
					'Reply-To: ' . esc_attr( $user_email ) . "\r\n";
		// Load WP components, no themes.
		$sent = wp_mail( $to, $subject, wp_strip_all_tags( $message ), $headers );

		if ( $sent ) {
			echo wp_json_encode(
				array(
					'status' => 't',
					'msg'    => esc_html__( 'Request Submitted succeessfully..', 'ads-for-wp' ),
				)
			);
		} else {
			echo wp_json_encode(
				array(
					'status' => 'f',
					'msg'    => esc_html__( 'Something wrong with this request.', 'ads-for-wp' ),
				)
			);
		}
	}
			wp_die();
}

add_action( 'wp_ajax_adsforwp_send_query_message', 'adsforwp_send_query_message' );
/*
 * Use of shortcode in php script
 * Usage : <?php adsforwp_the_ad(3013); ?>
 * Display single ad
 */
function adsforwp_the_ad( $ad_id ) {

	$output_function_obj = new Adsforwp_Output_Functions();
	$ad_code_escaped     = $output_function_obj->adsforwp_get_ad_code( $ad_id, $type = 'AD', 'notset' );
   //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- Reason: output is already escaped 
	echo $ad_code_escaped;
}
/*
 * Use of shortcode in php script
 * Usage : <?php adsforwp_the_group(3013); ?>
 * Display group ads
 */
function adsforwp_the_group( $group_id ) {

	$output_function_obj = new Adsforwp_Output_Functions();
	$group_code_escaped  = $output_function_obj->adsforwp_group_ads( $atts = null, $group_id, null, 'notset' );
   //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- Reason: output is already escaped      
	echo $group_code_escaped;
}

/**
 * We are adding extra fields for user profile
 *
 * @param type $user
 */
function adsforwp_extra_user_profile_fields( $user ) {

	?>
	<h3><?php esc_html_e( 'Extra profile information', 'ads-for-wp' ); ?></h3>

	<table class="form-table">
	<tr>
		<th><label for="afw-data-client-id"><?php esc_html_e( 'AdSense Publisher ID', 'ads-for-wp' ); ?></label></th>
		<td>
			<input placeholder="<?php echo esc_attr__("ca-pub-13XXXXXXXXXXXX64","ads-for-wp");?>" type="text" name="adsense_pub_id" id="adsense_pub_id" value="<?php echo esc_attr( get_the_author_meta( 'adsense_pub_id', $user->ID ) ); ?>" class="regular-text" /><br />
			<span class="description"><?php esc_html_e( 'Please enter your pub ID.', 'ads-for-wp' ); ?></span>
		</td>
	</tr>
	
	<tr>
		<th><label for="afw-data-ad-slot"><?php esc_html_e( 'Data Ad Slot', 'ads-for-wp' ); ?></label></th>
		<td>
			<input placeholder="<?php echo esc_attr__("70XXXXXX12","ads-for-wp");?>" type="text" name="adsense_ad_slot_id" id="adsense_ad_slot_id" value="<?php echo esc_attr( get_the_author_meta( 'adsense_ad_slot_id', $user->ID ) ); ?>" class="regular-text" /><br />
			<span class="description"><?php esc_html_e( 'Please enter your ad slot ID.', 'ads-for-wp' ); ?></span>
		</td>
	</tr>
	<?php wp_nonce_field( 'adsforwp_save_adsense_info', '_adsforwp_adsense_nonce', true, false ); ?>
	
	</table>
	<?php
}
add_action( 'show_user_profile', 'adsforwp_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'adsforwp_extra_user_profile_fields' );

/**
 * we are saving user extra fields data in database
 *
 * @param  type $user_id
 * @return boolean
 */
function adsforwp_save_extra_user_profile_fields( $user_id ) {

	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
	if ( ! isset( $_POST['_adsforwp_adsense_nonce'] ) || ! wp_verify_nonce( $_POST['_adsforwp_adsense_nonce'], 'adsforwp_save_adsense_info' ) ) {
		return false;
	}

	$adsense_pub_id     = isset( $_POST['adsense_pub_id'] ) ? sanitize_text_field( wp_unslash( $_POST['adsense_pub_id'] ) ) : '';
	$adsense_ad_slot_id = isset( $_POST['adsense_ad_slot_id'] ) ? sanitize_text_field( wp_unslash( $_POST['adsense_ad_slot_id'] ) ) : '';

	update_user_meta( $user_id, 'adsense_pub_id', $adsense_pub_id );
	update_user_meta( $user_id, 'adsense_ad_slot_id', $adsense_ad_slot_id );
}

add_action( 'personal_options_update', 'adsforwp_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'adsforwp_save_extra_user_profile_fields' );


/**
 * We are here overriding tile for adsforwp post type
 *
 * @global type $post
 * @param  string $title
 * @return string
 */
function adsforwp_modify_title( $title ) {
	global $post;
	if ( is_object( $post ) ) {
		if ( $post->ID ) {
			if ( get_post_type( $post->ID ) == 'adsforwp' ) {
				$adsense_auto = get_post_meta( $post->ID, $key = 'adsense_type', true );
				$ad_type      = get_post_meta( $post->ID, 'select_adtype', true );
				if ( $ad_type == 'adsense' ) {
					if ( $adsense_auto === 'adsense_auto_ads' ) {
						$title = $title . ' (Auto AdSense Ad)';
					}
				} elseif ( $ad_type == 'amp_story_ads' ) {
					$title = $title . ' (AMP Story Ad)';
				}
			}
		}
	}
	return $title;
}
add_filter( 'the_title', 'adsforwp_modify_title', 10, 1 );

/**
 * This is a ajax handler function to check adsese auto ads, if it is already added.
 *
 * @return type json string
 */
function adsforwp_ajax_check_post_availability() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_GET['adsforwp_security_nonce'] ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
	if ( ! wp_verify_nonce( $_GET['adsforwp_security_nonce'], 'adsforwp_ajax_check_nonce' ) ) {
		return;
	}
	   
		$cc_args           = array(
			'posts_per_page' => -1,
			'post_type'      => 'adsforwp',
			'meta_key'       => 'adsense_type',//phpcs:ignore -- WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- just using in adsense ads loading context
			'meta_value'     => 'adsense_auto_ads',//phpcs:ignore -- WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- just using in adsense ads loading context
		);
		
		$postdata          = new WP_Query( $cc_args );
		$auto_adsense_post = $postdata->posts;
		$ad_sense_type     = '';
		if ( $postdata->post_count > 0 ) {
			$ad_sense_type = get_post_meta( $auto_adsense_post[0]->ID, $key = 'adsense_type', true );
		}
		if ( $ad_sense_type ) {
			echo wp_json_encode(
				array(
					'status'       => 't',
					'post_id'      => $auto_adsense_post[0]->ID,
					'adsense_type' => $ad_sense_type,
				)
			);
		} else {
			echo wp_json_encode(
				array(
					'status'  => 'f',
					'post_id' => esc_html__( 'not available', 'ads-for-wp' ),
				)
			);
		}

		wp_die();
}
add_action( 'wp_ajax_adsforwp_check_meta', 'adsforwp_ajax_check_post_availability' );

/**
 * This function gets the link for selected tabs in setting section on ajax request
 *
 * @param  type $tab
 * @param  type $args
 * @return type
 */
function adsforwp_admin_link( $tab = '', $args = array() ) {

	$page = 'adsforwp';
	$link = admin_url( 'admin.php?page=' . $page );
	if ( $tab ) {
		$link .= '&tab=' . $tab;
	}

	if ( $args ) {
		foreach ( $args as $arg => $value ) {
			$link .= '&' . $arg . '=' . urlencode( $value );
		}
	}

	return esc_url( $link );
}

/**
 * This function gets the link for selected tabs in setting section on ajax request
 *
 * @param  type $tab
 * @param  type $args
 * @return type
 */
function adsforwp_analytics_admin_link( $tab = '', $args = array() ) {

    //phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Reason : Nonce verification is not required here , using ad_id for url building
	$ad_id = isset( $_GET['ad_id'] ) ? '&ad_id=' . sanitize_text_field( $_GET['ad_id'] ) : '';

	$page = 'analytics';

	if ( ! is_multisite() ) {
		$link = admin_url( 'edit.php?post_type=adsforwp&page=' . $page . $ad_id );
	} else {
		$link = network_admin_url( 'edit.php?post_type=adsforwp&page=' . $page . $ad_id );
	}

	if ( $tab ) {
		$link .= '&tab=' . $tab;
	}

	if ( $args ) {
		foreach ( $args as $arg => $value ) {
			$link .= '&' . $arg . '=' . urlencode( $value );
		}
	}

	return esc_url( $link );
}

/**
 * Get the selected tab on page reload
 *
 * @param  type $default
 * @param  type $available
 * @return type
 */
function adsforwp_get_tab( $default = '', $available = array() ) {

    //phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Reason : Nonce verification is not required here , using tab for url tab selection
	$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : $default;

	if ( ! in_array( $tab, $available ) ) {
		$tab = $default;
	}
	return $tab;
}

/**
 * It is default settings value, if value is not set for any option in setting section
 *
 * @return type
 */
function adsforwp_defaultSettings() {

	$defaults = array(
		'app_blog_name'             => get_bloginfo( 'name' ),
		'advnc_ads_import_check'    => 1,
		'ad_blocker_support'        => 1,
		'notice_type'               => 'bar',
		'page_redirect'             => 0,
		'allow_cookies'             => 2,
		'notice_title'              => 'Adblock Detected!',
		'notice_description'        => 'Our website is made possible by displaying online advertisements to our visitors. Please consider supporting us by whitelisting our website.',
		'notice_close_btn'          => 1,
		'btn_txt'                   => 'X',
		'notice_txt_color'          => '#ffffff',
		'notice_bg_color'           => '#1e73be',
		'notice_btn_txt_color'      => '#ffffff',
		'notice_btn_bg_color'       => '#f44336',
		'ad_sponsorship_label'      => 0,
		'ad_sponsorship_label_text' => 'Advertisement',
		'ad_label_postion'          => 'above',
		'ad_label_txt_color'        => '#cccccc',
	);

	$settings = get_option( 'adsforwp_settings', $defaults );

	return $settings;
}

/**
 * We are here checking expire date of all ads and change status
 */
function adsforwp_update_ads_status() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

		$common_function_obj = new Adsforwp_Admin_Common_Functions();
		$all_ads             = $common_function_obj->adsforwp_fetch_all_ads();
		$all_ads_post_meta   = array();

	foreach ( $all_ads as $ad ) {

		$ads_post_meta = get_post_meta( $ad, $key = '', true );

		if ( isset( $ads_post_meta['adsforwp_ad_expire_from'][0] ) && isset( $ads_post_meta['adsforwp_ad_expire_to'][0] ) ) {

			$current_date = gmdate( 'Y-m-d' );

			if ( $ads_post_meta['adsforwp_ad_expire_to'][0] < $current_date ) {

				wp_update_post(
					array(
						'ID'          => $ad->ID,
						'post_status' => 'draft',
					)
				);

			}
		}
	}
}
add_action( 'wp_loaded', 'adsforwp_update_ads_status' );

/**
 * We are registering our widget here in WordPress
 */
function register_adsforwp_ads_widget() {
	register_widget( 'Adsforwp_Ads_Widget' );
}
add_action( 'widgets_init', 'register_adsforwp_ads_widget' );

/*
 *      We are registering custom post type adsforwp in WordPress
 */
function adsforwp_setup_post_type() {

	$not_found_button = '<div><p style="float:left;margin-right:5px;">' . esc_html__( 'Welcome to Ads for WP. It looks like you don\'t have any ads.', 'ads-for-wp' ) . '</p> <a href="' . esc_url( admin_url( 'post-new.php?post_type=adsforwp' ) ) . '" class="button button-primary">' . esc_html__( 'Let\'s create a new Ad', 'ads-for-wp' ) . '</a></div>';
	$args             = array(
		'labels'              => array(
			'name'          => esc_html__( 'Ads', 'ads-for-wp' ),
			'singular_name' => esc_html__( 'Ad', 'ads-for-wp' ),
			'add_new'       => esc_html__( 'Add New Ad', 'ads-for-wp' ),
			'add_new_item'  => esc_html__( 'Add New Ad', 'ads-for-wp' ),
			'edit_item'     => esc_html__( 'Edit AD', 'ads-for-wp' ),
			'not_found'     => $not_found_button,
		),
		'public'              => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'show_in_admin_bar'   => false,
		'publicly_queryable'  => false,
		'menu_position'       => 100,
	);
	register_post_type( 'adsforwp', $args );
	$common_function_obj = new Adsforwp_Admin_Common_Functions();
	$all_ads             = $common_function_obj->adsforwp_fetch_all_ads();

	$not_found_button_group = '<div><p style="float:left;margin-right:5px;">' . esc_html__( 'Welcome to groups for WP. It looks like you don\'t have any ads.', 'ads-for-wp' ) . '</p> <a href="' . esc_url( admin_url( 'post-new.php?post_type=adsforwp' ) ) . '" class="button button-primary">' . esc_html__( 'Let\'s create a new Ad', 'ads-for-wp' ) . '</a></div>';
	if ( $all_ads ) {
		$not_found_button_group = '<div><p style="float:left;margin-right:5px;">' . esc_html__( 'Welcome to groups for WP. It looks like you don\'t have any group.', 'ads-for-wp' ) . '</p> <a href="' . esc_url( admin_url( 'post-new.php?post_type=adsforwp-groups' ) ) . '" class="button button-primary">' . esc_html__( 'Let\'s create a new Group', 'ads-for-wp' ) . '</a></div>';
	}
	$group_post_type = array(
		'labels'              => array(
			'name'         => esc_html__( 'Groups', 'ads-for-wp' ),
			'add_new'      => esc_html__( 'Add New Groups', 'ads-for-wp' ),
			'add_new_item' => esc_html__( 'Edit Groups', 'ads-for-wp' ),
			'edit_item'    => esc_html__( 'Edit AD', 'ads-for-wp' ),
			'not_found'    => $not_found_button_group,
		),
		'public'              => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'show_in_admin_bar'   => false,
		'show_in_menu'        => 'edit.php?post_type=adsforwp',
		'show_ui'             => true,
		'show_in_nav_menus'   => false,
		'show_admin_column'   => true,
		'rewrite'             => false,
	);
	register_post_type( 'adsforwp-groups', $group_post_type );
}
add_action( 'init', 'adsforwp_setup_post_type' );

/**
 * Changing the label for ads list table header
 *
 * @param  type $views
 * @return type
 */
function adsforwp_modified_views_so( $views ) {

	if ( isset( $views['draft'] ) ) {

		$views['draft'] = str_replace( 'Draft', 'Expire', $views['draft'] );

	}
	if ( isset( $views['publish'] ) ) {

		$views['publish'] = str_replace( 'Published', 'Live', $views['publish'] );

	}
	return $views;
}

add_filter( 'views_edit-adsforwp', 'adsforwp_modified_views_so' );

/**
 * Add the custom columns to the adsforwp_groups post type:
 */
function adsforwp_groups_custom_columns( $columns ) {

	$new = array();

	$columns['ads_group_shortcode'] = '<a>' . esc_html__( 'ShortCode', 'ads-for-wp' ) . '<a>';

	foreach ( $columns as $key => $value ) {

		if ( $key == 'date' ) {  // when we find the date column

			$new['ads_group_shortcode'] = $columns['ads_group_shortcode'];  // put the tags column before it

		}

		$new[ $key ] = $value;

	}
	return $new;
}
add_filter( 'manage_adsforwp-groups_posts_columns', 'adsforwp_groups_custom_columns' );

/**
 * Add the data to the custom columns for the adsforwp_groups post type:
 *
 * @param type $column
 * @param type $post_id
 */
function adsforwp_group_custom_column_set( $column, $post_id ) {

		global $wpdb;

		$common_function_obj = new Adsforwp_Admin_Common_Functions();
		$result              = $common_function_obj->adsforwp_check_ads_in_group( $post_id );

		$ad_stats   = adsforwp_get_ad_stats( 'sumofstats', $post_id );
		$impression = 0;
		$clicks     = 0;

	if ( $ad_stats ) {

		$impression = $ad_stats['impressions'];
		$clicks     = $ad_stats['clicks'];

	}
			$adsforwp_google_token = get_option( 'adsforwp_google_token' );
			$post_meta             = get_post_meta( $post_id, $key = '', true );
			$all_ads_post          = get_posts(
				array(
					'post_type'      => 'adsforwp',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
				)
			);
				$adsense_types     = array();
	if ( $all_ads_post ) {
		foreach ( $all_ads_post as $ads ) {
			$adsense_types[] = get_post_meta( $ads->ID, 'adsense_type', true );
		}
	}
	switch ( $column ) {
		case 'adsforwp_auto_ads_warning':
			if ( isset( $post_meta['select_adtype'] ) ) {
				if ( $post_meta['select_adtype'][0] == 'adsense' && $post_meta['adsense_type'][0] != 'adsense_auto_ads' ) {
					if ( in_array( 'adsense_auto_ads', $adsense_types ) ) {
						echo '<div class="adsforwp-tooltip"><i class=" dashicons dashicons-warning"></i><span class="adsforwp-tooltiptext" style="padding:5px;">' . esc_html__( 'Cannot use Auto Ads and Normal Ads at a time from same Company.', 'ads-for-wp' ) . '</span></div>';
					}
				}
			}
			break;
		case 'adsforwp_group_column':

			if( ! empty($result) ) {
				foreach ( $result as $group ) {
					$group_post  = get_post( $group );
					echo '<a href="' . esc_url( get_admin_url() ) . 'post.php?post=' . esc_attr( $group ) . '&action=edit">' . esc_html( $group_post->post_title ) . '</a>, ';
				}
			}						

			break;
		case 'adsforwp_ad_image_preview':
			if ( isset( $post_meta['select_adtype'] ) ) {

				if ( $post_meta['select_adtype'][0] == 'ad_image' ) {
							echo '<div><a href="' . esc_url( get_admin_url() ) . 'post.php?post=' . esc_attr( $post_id ) . '&action=edit"><img width="150" src="' . esc_url( $post_meta['adsforwp_ad_image'][0] ) . '"></a></div>';
				}
			}
			break;
		case 'adsforwp_ad_impression_column':
			if ( $adsforwp_google_token ) {

					echo '<div><span><a href="' . esc_url( admin_url( 'edit.php?post_type=adsforwp&page=analytics&ad_id=' . $post_id ) ) . '">' . esc_attr( $impression ) . '</a><span></div>';

			} else {

						echo '<div><span><a href="' . esc_url( admin_url( 'edit.php?post_type=adsforwp&page=adsforwp-analytics&ad_id=' . $post_id ) ) . '">' . esc_attr( $impression ) . '</a><span></div>';
			}

			break;

		case 'adsforwp_ad_clicks_column':
			if ( $adsforwp_google_token ) {

					echo '<div><span><a href="' . esc_url( admin_url( 'edit.php?post_type=adsforwp&page=analytics&ad_id=' . $post_id ) ) . '">' . esc_attr( $clicks ) . '</a><span></div>';

			} else {

						echo '<div><span><a href="' . esc_url( admin_url( 'edit.php?post_type=adsforwp&page=adsforwp-analytics&ad_id=' . $post_id ) ) . '">' . esc_attr( $clicks ) . '</a><span></div>';

			}

			break;
		case 'adsforwp_expire_column':
			$post_meta   = get_post_meta( $post_id, $key = '', true );
			$expire_date = '';

			if ( isset( $post_meta['adsforwp_ad_expire_to'] ) ) {

				$expire_date = $post_meta['adsforwp_ad_expire_to'][0];

			}

			if ( $expire_date ) {

				$current_date = gmdate( 'Y-m-d' );

				if ( $current_date > $expire_date ) {

					echo esc_html__( 'Expired on', 'ads-for-wp' ) . ' ' . esc_html( gmdate( 'M d Y', strtotime( $expire_date ) ) );

				} else {

					echo esc_html__( 'expires', 'ads-for-wp' ) . ' ' . esc_html( gmdate( 'M d Y', strtotime( $expire_date ) ) );

				}
			}
			break;
	}
}
add_action( 'manage_adsforwp_posts_custom_column', 'adsforwp_group_custom_column_set', 10, 2 );

/**
 * Add the custom columns to the Ads post type:
 *
 * @param  array $columns
 * @return string
 */
function adsforwp_custom_columns( $columns ) {

	$settings = adsforwp_defaultSettings();

	unset( $columns['date'] );
	$columns['adsforwp_auto_ads_warning'] = '<a>' . esc_html__( 'Warning', 'ads-for-wp' ) . '<a>';
	$columns['adsforwp_ad_image_preview'] = '<a>' . esc_html__( 'Preview', 'ads-for-wp' ) . '<a>';
	$columns['adsforwp_expire_column']    = '<a>' . esc_html__( 'Expire On', 'ads-for-wp' ) . '<a>';
	$columns['adsforwp_group_column']     = '<a>' . esc_html__( 'Groups', 'ads-for-wp' ) . '<a>';

	if ( isset( $settings['ad_performance_tracker'] ) ) {

		$columns['adsforwp_ad_impression_column'] = '<a>' . esc_html__( 'Ad Impression', 'ads-for-wp' ) . '<a>';
		$columns['adsforwp_ad_clicks_column']     = '<a>' . esc_html__( 'Ad Clicks', 'ads-for-wp' ) . '<a>';

	}

	return $columns;
}
add_filter( 'manage_adsforwp_posts_columns', 'adsforwp_custom_columns' );

/**
 * Add the data to the custom columns for the adsforwp_groups post type:
 *
 * @param type $column
 * @param type $post_id
 */
function adsforwp_custom_column_set( $column, $post_id ) {

	switch ( $column ) {
		case 'ads_group_shortcode':
			echo '<a>[adsforwp-group id="' . esc_attr( $post_id ) . '"]</a>';
			break;
	}
}
add_action( 'manage_adsforwp-groups_posts_custom_column', 'adsforwp_custom_column_set', 10, 2 );

/*
 *      Hiding WYSIWYG For AMPforWP Ads 2.0, as there is no need for it
*/
function adsforwp_removing_wysiwig() {

		remove_post_type_support( 'adsforwp', 'editor' );
		remove_post_type_support( 'adsforwp-groups', 'editor' );
}
add_action( 'admin_init', 'adsforwp_removing_wysiwig' );

/*
 *   REGISTER ALL NON-ADMIN SCRIPTS
 */
function adsforwp_frontend_enqueue() {
	$settings     = adsforwp_defaultSettings();
	$all_ads_post = adsforwp_get_ad_ids();
	if ( $all_ads_post ) {
		$need_to_display = false;
		foreach ( $all_ads_post as $post_ad_id ) {
			$service   = new Adsforwp_Output_Service();
			$ad_status = $service->adsforwp_is_condition( $post_ad_id );
			if ( $ad_status ) {
				$need_to_display = true;
				break;
			}
		}
		if ( $need_to_display ) {
			if ( ADSFORWP_ENVIRONMENT == 'DEV' ) {
				wp_register_script( 'adsforwp-ads-frontend-js', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/js/ads-frontend.js', array( 'jquery' ), ADSFORWP_VERSION, true );//phpcs:ignore EnqueuedScriptsScope -- if you look at above we have added manual condion to load the script
				wp_register_script( 'adsforwp-ads-front-js', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/js/ads-front.js', array( 'jquery' ), ADSFORWP_VERSION, true );//phpcs:ignore EnqueuedScriptsScope -- if you look at above we have added manual condion to load the script
			} else {
				wp_register_script( 'adsforwp-ads-frontend-js', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/js/ads-frontend.min.js', array( 'jquery' ), ADSFORWP_VERSION, true );//phpcs:ignore EnqueuedScriptsScope -- if you look at above we have added manual condion to load the script
				wp_register_script( 'adsforwp-ads-front-js', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/js/ads-front.min.js', array( 'jquery' ), ADSFORWP_VERSION, true );//phpcs:ignore EnqueuedScriptsScope -- if you look at above we have added manual condion to load the script
			}
			$browserdata = array();
			$object_name = array(
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'adsforwp_front_nonce' => wp_create_nonce( 'adsforwp_ajax_check_front_nonce' ),
			);

			$object_browser = apply_filters( 'adsforwp_localize_browser_filter', $browserdata, 'adsforwp_localize_data' );

			if ( isset( $settings['ad_performance_tracker'] ) ) {
				$object_name['ad_performance_tracker'] = $settings['ad_performance_tracker'];
			}

			    wp_localize_script( 'adsforwp-ads-front-js', 'adsforwp_obj', $object_name );
			    wp_localize_script( 'adsforwp-ads-frontend-js', 'adsforwp_browser_obj', $object_browser );
				wp_enqueue_script( 'adsforwp-ads-front-js' );
				wp_enqueue_script( 'adsforwp-ads-frontend-js' );
			if ( ADSFORWP_ENVIRONMENT == 'DEV' ) {
				wp_enqueue_style( 'ads-for-wp-front-css', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/css/adsforwp-front.css', false, ADSFORWP_VERSION );
			} else {
				wp_enqueue_style( 'ads-for-wp-front-css', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/css/adsforwp-front.min.css', false, ADSFORWP_VERSION );
			}
		}
	}
}
add_action( 'wp_enqueue_scripts', 'adsforwp_frontend_enqueue' );

add_filter( 'adsforwp_localize_browser_filter', 'adsforwp_browser_width_conditoinal' );
function adsforwp_browser_width_conditoinal( $data ) {
	$conditions        = array();
	$all_ads_post      = adsforwp_get_ad_ids();
	$and_or_conditions = array();
	if ( $all_ads_post ) {
		$i = 0;
		foreach ( $all_ads_post as $ads ) {
			$post_ad_id               = $ads;
			$visitor_condition_enable = get_post_meta( $post_ad_id, $key = 'adsforwp_v_condition_enable', true );
			$visitor_conditions_array = get_post_meta( $post_ad_id, 'visitor_conditions_array', true ) ;
			if ( isset( $visitor_condition_enable ) && $visitor_condition_enable == 'enable' ) {
				for ( $j = 0;$j < count( $visitor_conditions_array );$j++ ) {
					$conditions = $visitor_conditions_array[ 'group-' . $j ]['visitor_conditions'];
					foreach ( $conditions as $key => $value ) {
						if ( is_array( $value ) ) {
							if ( in_array( 'browser_width', $value ) ) {
								if ( count( $conditions ) > 1 ) {
									$and_or_conditions[ $post_ad_id ]['and'][] = $conditions[ $key ];
								} else {
									$and_or_conditions[ $post_ad_id ]['or'][] = $conditions[ $key ];
								}
							}
						}
					}
				}
			}

			++$i;
		}
		$data = $and_or_conditions;
	}
	return $data;
}


/*
 *  Enqueue Javascript and CSS in admin area
 */
function adsforwp_admin_enqueue( $hook ) {

		wp_enqueue_media();
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui' );

		wp_enqueue_style( 'jquery-ui', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/vendor/css/jquery-ui.css', false, ADSFORWP_VERSION );
	if ( ADSFORWP_ENVIRONMENT == 'DEV' ) {
		wp_enqueue_style( 'ads-for-wp-admin', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/css/adsforwp.css', false, ADSFORWP_VERSION );
		wp_register_script( 'ads-for-wp-admin-js', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/js/adsforwp.js', array( 'jquery', 'wp-color-picker' ), ADSFORWP_VERSION, true );
	} else {
		wp_enqueue_style( 'ads-for-wp-admin', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/css/adsforwp.min.css', false, ADSFORWP_VERSION );
		wp_register_script( 'ads-for-wp-admin-js', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/js/adsforwp.min.js', array( 'jquery', 'wp-color-picker' ), ADSFORWP_VERSION, true );
	}

		$ad_type_obj   = new Adsforwp_View_Ads_Type();
		$ad_type_array = $ad_type_obj->adsforwp_adtype_metabox_fields();

			$display_metabox_obj   = new Adsforwp_View_Display();
			$display_metabox       = $display_metabox_obj->adsforwp_display_metabox_fields();
			$amp_story_ads_feature = array( 'amp_story_ad' => false );
			$experiences           = array();
	if ( class_exists( 'AMP_Options_Manager' ) ) {
		$experiences = AMP_Options_Manager::get_option( 'experiences' );

		if ( ! empty( $experiences ) ) {
			if ( in_array( 'stories', $experiences ) || class_exists( 'Ampforwp_Stories_Post_Type' ) ) {
				$amp_story_ads_feature = array( 'amp_story_ad' => true );
			}
		}
	}

		wp_localize_script( 'ads-for-wp-admin-js', 'adtype_metafields', $ad_type_array );
		wp_localize_script( 'ads-for-wp-admin-js', 'display_metafields', $display_metabox );
		wp_localize_script( 'ads-for-wp-admin-js', 'amp_story_ads_feature', $amp_story_ads_feature );
	if ( ADSFORWP_ENVIRONMENT == 'DEV' ) {
		wp_register_script( 'ads-for-wp-admin-analytics-js', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/js/analytics.js', array( 'jquery' ), ADSFORWP_VERSION, true );
	} else {
		wp_register_script( 'ads-for-wp-admin-analytics-js', ADSFORWP_PLUGIN_DIR_URI . 'public/assets/js/analytics.min.js', array( 'jquery' ), ADSFORWP_VERSION, true );
	}
		wp_enqueue_style( 'wp-color-picker' );

		$data = array(
			'ajax_url'                => admin_url( 'admin-ajax.php' ),
			'id'                      => get_the_ID(),
			'uploader_title'          => esc_html__( 'Ad Image', 'ads-for-wp' ),
			'uploader_button'         => esc_html__( 'Select', 'ads-for-wp' ),
			'post_type'               => get_post_type(),
			'current_page'            => $hook,
			'adnow_note'              => esc_html__( 'Adnow does not support AMP, Once Adnow starts supporting, we will also start.', 'ads-for-wp' ),
			'infolinks_note'          => esc_html__( 'Infolinks does not support AMP, Once Infolinks starts supporting, we will also start.', 'ads-for-wp' ),
			'embed_code_button_text'  => esc_html__( 'Embed Shortcode', 'ads-for-wp' ),
			'adsforwp_security_nonce' => wp_create_nonce( 'adsforwp_ajax_check_nonce' ),

		);

		$data = apply_filters( 'adsforwp_localize_filter', $data, 'adsforwp_localize_data' );

		wp_localize_script( 'ads-for-wp-admin-js', 'adsforwp_localize_data', $data );

		wp_enqueue_script( 'ads-for-wp-admin-js' );
		wp_enqueue_script( 'ads-for-wp-admin-analytics-js' );
}
add_action( 'admin_enqueue_scripts', 'adsforwp_admin_enqueue' );

function adsforwp_get_ad_ids() {

	$all_ads_id = json_decode( get_transient( 'adsforwp_transient_ads_ids' ), true );
	if ( ! $all_ads_id ) {
		$all_ads_post = get_posts(
			array(
				'post_type'      => 'adsforwp',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			)
		);
		$ads_post_ids = array();
		if ( $all_ads_post ) {

			foreach ( $all_ads_post as $ads ) {
				$ads_post_ids[] = $ads->ID;
			}
		}

		if ( ! empty( $ads_post_ids ) ) {
			return $ads_post_ids;
		} else {
			return false;
		}
	}
	return $all_ads_id;
}

/*
 *      Storing and updating all ads post ids in transient on different actions
 *      which we will fetch all ids from here to display our post
 */
function adsforwp_published() {

		$ads_post_ids = array();

		$all_ads_post = get_posts(
			array(
				'post_type'      => 'adsforwp',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			)
		);

	if ( $all_ads_post ) {

		foreach ( $all_ads_post as $ads ) {
			$ads_post_ids[] = $ads->ID;
		}
	}

	if ( $ads_post_ids ) {
		$ads_post_ids_json = wp_json_encode( $ads_post_ids );
		set_transient( 'adsforwp_transient_ads_ids', $ads_post_ids_json );
	}
}

function adsforwp_update_ids_on_trash() {

	delete_transient( 'adsforwp_transient_ads_ids' );
	adsforwp_published();
}

function adsforwp_update_ids_on_untrash() {
	adsforwp_published();
}
	add_action( 'save_post_adsforwp', 'adsforwp_published' );
	add_action( 'publish_adsforwp', 'adsforwp_published' );
	add_action( 'trash_adsforwp', 'adsforwp_update_ids_on_trash' );
	add_action( 'untrash_adsforwp', 'adsforwp_update_ids_on_untrash' );


function adsforwp_get_group_ad_ids() {

	$all_ads_id = json_decode( get_transient( 'adsforwp_groups_transient_ids' ), true );
	if ( ! $all_ads_id ) {
		$all_group_post = get_posts(
			array(
				'post_type'      => 'adsforwp-groups',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			)
		);
		$group_post_ids = array();
		if ( $all_group_post ) {
			foreach ( $all_group_post as $group ) {
				$group_post_ids[] = $group->ID;
			}
		}
		if ( ! empty( $group_post_ids ) ) {
			return $group_post_ids;
		} else {
			return false;
		}
	}
	return $all_ads_id;
}

/*
 *      Storing and updating all groups post ids in transient on different actions
 *      which we will fetch all ids from here to display our post
 */
function adsforwp_groups_published() {

	$all_group_post = get_posts(
		array(
			'post_type'      => 'adsforwp-groups',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		)
	);

	$group_post_ids = array();

	if ( $all_group_post ) {

		foreach ( $all_group_post as $group ) {
			$group_post_ids[] = $group->ID;
		}
	}

	if ( $group_post_ids ) {

		$group_post_ids_json = wp_json_encode( $group_post_ids );
		set_transient( 'adsforwp_groups_transient_ids', $group_post_ids_json );

	}
}

function adsforwp_groups_update_ids_on_trash() {

	delete_transient( 'adsforwp_groups_transient_ids' );
	adsforwp_groups_published();
}

function adsforwp_groups_update_ids_on_untrash() {
	adsforwp_groups_published();
}
	add_action( 'save_post_adsforwp-groups', 'adsforwp_groups_published' );
	add_action( 'publish_adsforwp-groups', 'adsforwp_groups_published' );
	add_action( 'trash_adsforwp-groups', 'adsforwp_groups_update_ids_on_trash' );
	add_action( 'untrash_adsforwp-groups', 'adsforwp_groups_update_ids_on_untrash' );

/**
 * Here, We are displaying notice in admin panel on different different actions or conditions
 */
function adsforwp_general_admin_notice() {

	echo '<div class="message error update-message notice notice-alt notice-error afw-blocker-notice afw_hide">'
		. '<p>' . esc_html__( 'Please disable your', 'ads-for-wp' ) . ' <strong>' . esc_html__( 'AdBlocker', 'ads-for-wp' ) . '</strong> ' . esc_html__( 'to use adsforwp plugin smoothly.', 'ads-for-wp' ) . ' <a target="_blank" href="http://adsforwp.com/docs/article/what-is-ad-blocker-and-how-to-disable-it/">' . esc_html__( 'Learn More', 'ads-for-wp' ) . '</a></p>'
		. '</div>';
}
add_action( 'admin_notices', 'adsforwp_general_admin_notice' );
/**
 * Showing WordPress pointer on mouse movement
 */

add_filter( 'adsforwp_localize_filter', 'adsforwp_add_localize_data', 10, 2 );

function adsforwp_add_localize_data( $object, $object_name ) {

	if ( $object_name == 'adsforwp_localize_data' ) {

			$object['pointer_help']          = esc_html__( 'Help', 'ads-for-wp' );
			$object['adsense_pointer']       = esc_html__( 'You can find Data Client ID and Data Ad Slot from adsense code. Please <a href="https://adsforwp.com/docs/article/how-to-add-adsense-ads-in-wordpress-amp/" target="_blank">Click Here</a> for more info.', 'ads-for-wp' );
			$object['media_net_pointer']     = esc_html__( 'You can find Data CID id and Data CRID from media.net code. Please <a href="https://adsforwp.com/docs/article/how-to-add-amp-ad-for-media-net/" target="_blank">Click Here</a> for more info.', 'ads-for-wp' );
			$object['ad_now_pointer']        = esc_html__( 'You can find Widget ID from adnow code.', 'ads-for-wp' );
			$object['revcontent_pointer']    = esc_html__( 'You can find Data ID and Data Wrapper from revcontent code.', 'ads-for-wp' );
			$object['mgid_pointer']          = esc_html__( 'You can find Publisher and Widget Data from MGID ad code. Please <a href="https://adsforwp.com/docs/article/how-to-add-mgid-ads-in-wordpress-amp/" target="_blank">Click Here</a> for more info.', 'ads-for-wp' );
			$object['contentad_pointer']     = esc_html__( 'You can find ID id, D and Ad Widget ID from content.ad code.', 'ads-for-wp' );
			$object['infolinks_pointer']     = esc_html__( 'You can find P ID and W S ID from infolinks code. Please <a href="https://adsforwp.com/docs/article/how-to-add-infolinks-ads-in-wordpress-amp/" target="_blank">Click Here</a> for more info.', 'ads-for-wp' );
			$object['ad_image_pointer']      = esc_html__( 'Upload a banner which you want to display as an ad and anchor link which will redirect users to that link on click. Please <a href="https://adsforwp.com/docs/article/how-to-add-custom-banner-ads-in-amp-wordpress/" target="_blank">Click Here</a> for more info.', 'ads-for-wp' );
			$object['custom_pointer']        = esc_html__( 'Insert the ad code or script. Please <a href="https://adsforwp.com/docs/article/how-to-add-ads-in-iframe/" target="_blank">Click Here</a> for more info.', 'ads-for-wp' );
			$object['doubleclick_pointer']   = esc_html__( 'Insert the Slot Id and Div Gpt Ad. Please <a href="https://adsforwp.com/docs/article/how-to-add-dfp-ads-in-wordpress-amp/" target="_blank">Click Here</a> for more info.', 'ads-for-wp' );
			$object['ad_background_pointer'] = esc_html__( 'Insert the background banner.', 'ads-for-wp' );
			$object['ezoic_pointer']         = esc_html__( 'You can find Data Ezoic ID from ezoic code. Please <a href="https://adsforwp.com/docs/article/how-to-add-ezoic-ads-in-wordpress-amp/" target="_blank">Click Here</a> for more info.', 'ads-for-wp' );
			$object['engageya_pointer']      = esc_html__( 'You can find Data Widget Id\'s, Website Id and Publisher Id from engageya ad network provider.', 'ads-for-wp' );
			$object['mantis_pointer']        = esc_html__( 'You can find Data Mantis ID from mantis ads code. Please <a href="https://adsforwp.com/docs/article/how-to-add-mantis-ads-in-wordpress-amp/" target="_blank">Click Here</a> for more info.', 'ads-for-wp' );
			$object['outbrain_pointer']      = esc_html__( 'You can find Data Outbrain ID from outbrain ads code. Please <a href="https://adsforwp.com/docs/article/how-to-add-outbrain-ads-in-wordpress-amp/" target="_blank">Click Here</a> for more info.', 'ads-for-wp' );
			$object['mediavine_pointer']     = esc_html__( 'You can find Data Mediavine ID from mediavine ads code. Please <a href="https://adsforwp.com/docs/article/how-to-add-mediavine-ads-in-wordpress-amp/" target="_blank">Click Here</a> for more info.', 'ads-for-wp' );
			$object['taboola_pointer']       = esc_html__( 'You can find Data Taboola ID from taboola ads code. Please <a href="https://adsforwp.com/docs/article/how-to-add-taboola-ads-in-wordpress-amp/" target="_blank">Click Here</a> for more info.', 'ads-for-wp' );

	}

		return $object;
}
/**
 * Function to sanitize display condition and user targeting
 *
 * @param  type $array
 * @param  type $type
 * @return type array
 */
function adsforwp_sanitize_multi_array( $array, $type ) {

	if ( $array ) {

		foreach ( $array as $group => $condition ) {

			$group_condition = $condition[ $type ];

			foreach ( $group_condition as $con_key => $con_val ) {

				foreach ( $con_val as $key => $val ) {

						$con_val[ $key ] = sanitize_text_field( $val );

				}

				$group_condition[ $con_key ] = $con_val;
			}

			$array[ $group ] = $condition;

		}
	}

	return $array;
}
