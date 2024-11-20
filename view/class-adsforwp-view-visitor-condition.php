<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Adsforwp_View_Visitor_Condition {

	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'adsforwp_visitor_condition_add_meta_box' ) );
		add_action( 'save_post', array( $this, 'adsforwp_visitor_condition_save' ) );
	}
	public function adsforwp_get_three_letter_country_code_using_two($code){
		$country_codes=array('AF'=>"AFG",'AL'=>"ALB",'DZ'=>"DZA",'AS'=>"ASM",'AD'=>"AND",'AO'=>"AGO",'AI'=>"AIA",'AQ'=>"ATA",'AG'=>"ATG",'AR'=>"ARG",'AM'=>"ARM",'AW'=>"ABW",'AU'=>"AUS",'AT'=>"AUT",'AZ'=>"AZE",'BS'=>"BHS",'BH'=>"BHR",'BD'=>"BGD",'BB'=>"BRB",'BY'=>"BLR",'BE'=>"BEL",'BZ'=>"BLZ",'BJ'=>"BEN",'BM'=>"BMU",'BT'=>"BTN",'BO'=>"BOL",'BA'=>"BIH",'BW'=>"BWA",'BR'=>"BRA",'IO'=>"IOT",'VG'=>"VGB",'BN'=>"BRN" ,'BG'=>"BGR",'BF'=>"BFA",'BI'=>"BDI",'KH'=>"KHM",'CM'=>"CMR",'CA'=>"CAN",'CV'=>"CPV",'KY'=>"CYM",'CF'=>"CAF",'TD'=>"TCD",'CL'=>"CHL",'CN'=>"CHN",'CX'=>"CXR",'CC'=>"CCK",'CO'=>"COL",'KM'=>"COM",'CK'=>"COK",'CR'=>"CRI",'HR'=>"HRV",'CU'=>"CUB",'CW'=>"CUW",'CY'=>"CYP",'CZ'=>"CZE",'CD'=>"COD",'DK'=>"DNK",'DJ'=>"DJI",'DM'=>"DMA",'DO'=>"DOM",'TL'=>"TLS",'EC'=>"ECU",'EG'=>"EGY",'SV'=>"SLV",'GQ'=>"GNQ" ,'ER'=>"ERI",'EE'=>"EST",'ET'=>"ETH",'FK'=>"FLK",'FO'=>"FRO",'FJ'=>"FJI",'FI'=>"FIN",'FR'=>"FRA",'PF'=>"PYF",'GA'=>"GAB",'GM'=>"GMB",'GE'=>"GEO",'DE'=>"DEU",'GH'=>"GHA",'GI'=>"GIB",'GR'=>"GRC",'GL'=>"GRL",'GD'=>"GRD",'GU'=>"GUM",'GT'=>"GTM",'GG'=>"GGY",'GN'=>"GIN",'GW'=>"GNB",'GY'=>"GUY",'HT'=>"HTI",'HN'=>"HND",'HK'=>"HKG",'HU'=>"HUN",'IS'=>"ISL",'IN'=>"IND",'ID'=>"IDN",'IR'=>"IRN",'IQ'=>"IRQ",'IE'=>"IRL",'IM'=>"IMN",'IL'=>"ISR",'IT'=>"ITA",'CI'=>"CIV",'JM'=>"JAM",'JP'=>"JPN",'JE'=>"JEY",'JO'=>"JOR",'KZ'=>"KAZ",'KE'=>"KEN",'KI'=>"KIR",'XK'=>"XKX",'KW'=>"KWT",'KG'=>"KGZ",'LA'=>"LAO",'LV'=>"LVA",'LB'=>"LBN",'LS'=>"LSO",'LR'=>"LBR",'LY'=>"LBY",'LI'=>"LIE",'LT'=>"LTU",'LU'=>"LUX",'MO'=>"MAC",'MK'=>"MKD",'MG'=>"MDG",'MW'=>"MWI",'MY'=>"MYS",'MV'=>"MDV",'ML'=>"MLI",'MT'=>"MLT",'MH'=>"MHL",'MR'=>"MRT",'MU'=>"MUS",'YT'=>"MYT",'MX'=>"MEX",'FM'=>"FSM",'MD'=>"MDA",'MC'=>"MCO",'MN'=>"MNG",'ME'=>"MNE",'MS'=>"MSR",'MA'=>"MAR",'MZ'=>"MOZ",'MM'=>"MMR",'NA'=>"NAM",'NR'=>"NRU",'NP'=>"NPL",'NL'=>"NLD",'AN'=>"ANT",'NC'=>"NCL",'NZ'=>"NZL",'NI'=>"NIC",'NE'=>"NER",'NG'=>"NGA",'NU'=>"NIU",'KP'=>"PRK",'MP'=>"MNP",'NO'=>"NOR",'OM'=>"OMN",'PK'=>"PAK",'PW'=>"PLW",'PS'=>"PSE",'PA'=>"PAN",'PG'=>"PNG",'PY'=>"PRY",'PE'=>"PER",'PH'=>"PHL",'PN'=>"PCN",'PL'=>"POL",'PT'=>"PRT",'PR'=>"PRI",'QA'=>"QAT",'CG'=>"COG",'RE'=>"REU",'RO'=>"ROU",'RU'=>"RUS",'RW'=>"RWA",'BL'=>"BLM",'SH'=>"SHN",'KN'=>"KNA",'LC'=>"LCA",'MF'=>"MAF",'PM'=>"SPM",'VC'=>"VCT",'WS'=>"WSM",'SM'=>"SMR",'ST'=>"STP",'SA'=>"SAU",'SN'=>"SEN",'RS'=>"SRB",'SC'=>"SYC",'SL'=>"SLE",'SG'=>"SGP",'SX'=>"SXM",'SK'=>"SVK",'SI'=>"SVN",'SB'=>"SLB",'SO'=>"SOM",'ZA'=>"ZAF",'KR'=>"KOR",'SS'=>"SSD",'ES'=>"ESP",'LK'=>"LKA",'SD'=>"SDN",'SR'=>"SUR",'SJ'=>"SJM",'SZ'=>"SWZ",'SE'=>"SWE",'CH'=>"CHE",'SY'=>"SYR",'TW'=>"TWN",'TJ'=>"TJK",'TZ'=>"TZA",'TH'=>"THA",'TG'=>"TGO",'TK'=>"TKL",'TO'=>"TON",'TT'=>"TTO",'TN'=>"TUN",'TR'=>"TUR",'TM'=>"TKM",'TC'=>"TCA",'TV'=>"TUV",'VI'=>"VIR",'UG'=>"UGA",'UA'=>"UKR",'AE'=>"ARE",'GB'=>"GB",'US'=>"US",'UY'=>"URY",'UZ'=>"UZB",'VU'=>"VUT",'VA'=>"VAT",'VE'=>"VEN",'VN'=>"VNM",'WF'=>"WLF",'EH'=>"ESH",'YE'=>"YEM",'ZM'=>"ZMB",'ZW'=>"ZWE");
		return isset($country_code[$code])?$country_code[$code]:$code;
	}
	public function adsforwp_visitor_condition_add_meta_box() {

		global $post;
		$in_group = array();
		if ( is_object( $post ) ) {

			$common_function_obj = new Adsforwp_Admin_Common_Functions();
			$in_group            = $common_function_obj->adsforwp_check_ads_in_group( $post->ID );

		}

		if ( empty( $in_group ) ) {
			add_meta_box(
				'adsforwp_visitor_condition_metabox',
				esc_html__( 'User Targeting', 'ads-for-wp' ),
				array( $this, 'adsforwp_visitor_condition_callback' ),
				array( 'adsforwp', 'adsforwp-groups' ),
				'normal',
				'low'
			);

		}
	}

	public function adsforwp_visitor_condition_callback( $post ) {

			$visitor_condition_enable = get_post_meta( $post->ID, $key = 'adsforwp_v_condition_enable', true );
			$visitor_conditions_array = get_post_meta( $post->ID, 'visitor_conditions_array', true ) ;
			$visitor_conditions_array = is_array( $visitor_conditions_array ) ? array_values( $visitor_conditions_array ) : array();

		if ( empty( $visitor_conditions_array ) ) {

					$visitor_conditions_array[0] = array(
						'visitor_conditions' => array(
							array(
								'key_1' => 'device',
								'key_2' => 'equal',
								'key_3' => 'desktop',
							),
						),
					);
		}

		// security check
		wp_nonce_field( 'adsforwp_visitor_condition_action_nonce', 'adsforwp_visitor_condition_name_nonce' );

		// Type Select
		$choices = array(
			esc_html__( 'Basic', 'ads-for-wp' ) => array(
				'device'            => esc_html__( 'Device Type', 'ads-for-wp' ),
				'browser_language'  => esc_html__( 'Language Of The Browser', 'ads-for-wp' ),
				'logged_in_visitor' => esc_html__( 'Logged In', 'ads-for-wp' ),
				'user_agent'        => esc_html__( 'User Agent', 'ads-for-wp' ),
				'user_type'         => esc_html__( 'User Permission', 'ads-for-wp' ),
				'referrer_url'      => esc_html__( 'Referring URL', 'ads-for-wp' ),
				'url_parameter'     => esc_html__( 'URL Parameter', 'ads-for-wp' ),
				'geo_location'      => esc_html__( 'Country', 'ads-for-wp' ),
				'cookie'            => esc_html__( 'Cookie', 'ads-for-wp' ),
				'browser_width'     => esc_html__( 'Browser Width', 'ads-for-wp' ),
			),
		);

		if ( defined( 'PMPRO_BASE_FILE' ) ) {
			$choices['Basic']['membership_level'] = esc_html__( 'Membership Level', 'ads-for-wp' );
		}
		$comparison         = array(
			'equal'     => esc_html__( 'Equal to', 'ads-for-wp' ),
			'not_equal' => esc_html__( 'Not Equal to', 'ads-for-wp' ),
		);
		$total_group_fields = count( $visitor_conditions_array );

		?>
<div class="adsforwp_visitor_condition_group" >   
	<input type="hidden" value="<?php echo ( isset( $visitor_condition_enable ) ? esc_attr( $visitor_condition_enable ) : 'disable' ); ?>" id="adsforwp_v_condition_enable" name="adsforwp_v_condition_enable">    
		<?php
		if ( isset( $visitor_condition_enable ) && $visitor_condition_enable == 'enable' ) {
			echo '<div class="adsforwp-visitor-condition-groups">';
		} else {
			echo '<div class="adsforwp-visitor-condition-div"><a class="adsforwp-enable-click afw-placement-button">' . esc_html__( 'User Targeting', 'ads-for-wp' ) . '</a>';
			echo '<p>' . esc_html__( 'User Targeting conditions to limit the number of users who can see your ad.', 'ads-for-wp' ) . '</p></div>';
			echo '<div class="adsforwp-visitor-condition-groups afw_hide">';
		}
		?>
			<?php
			for ( $j = 0; $j < $total_group_fields; $j++ ) {
				$visitor_conditions = $visitor_conditions_array[ $j ]['visitor_conditions'];

				$total_fields = count( $visitor_conditions );
				?>
	<div class="adsforwp-visitor-condition-group" name="visitor_conditions_array[<?php echo esc_attr( $j ); ?>]" data-id="<?php echo esc_attr( $j ); ?>">           
				<?php
				if ( $j > 0 ) {
					echo '<span style="margin-left:10px;font-weight:600">Or</span>';
				}
				?>
				<table class="widefat adsforwp-visitor-condition-widefat">
		<tbody id="adsforwp-visitor-condition-tbody" class="adsforwp-fields-wrapper-1">
				<?php
				for ( $i = 0; $i < $total_fields; $i++ ) {
					$selected_val_key_1 = $visitor_conditions[ $i ]['key_1'];
					$selected_val_key_2 = $visitor_conditions[ $i ]['key_2'];
					$selected_val_key_3 = $visitor_conditions[ $i ]['key_3'];
					$selected_val_key_4 = '';
					$selected_val_key_5 = '';
					if ( isset( $visitor_conditions[ $i ]['key_4'] ) ) {
						$selected_val_key_4 = $visitor_conditions[ $i ]['key_4'];
					}
					if ( isset( $visitor_conditions[ $i ]['key_5'] ) ) {
						$selected_val_key_5 = $visitor_conditions[ $i ]['key_5'];
					}
					?>
			
			<tr class="adsforwp-toclone">
			<td style="width:31%" class="adsforwp-visitor-condition-type"> 
				<select class="widefat adsforwp-select-visitor-condition-type <?php echo esc_attr( $i ); ?>" name="visitor_conditions_array[group-<?php echo esc_attr( $j ); ?>][visitor_conditions][<?php echo esc_attr( $i ); ?>][key_1]">    
					<?php
					foreach ( $choices as $choice_key => $choice_value ) {
						?>
									<optgroup label="<?php echo esc_attr( $choice_key ); ?>">                      
					</optgroup>
						<?php
						foreach ( $choice_value as $sub_key => $sub_value ) {
							?>
										<option class="pt-child" value="<?php echo esc_attr( $sub_key ); ?>" <?php selected( $selected_val_key_1, $sub_key ); ?> > <?php echo esc_html( $sub_value ); ?> </option>
							<?php
						}
					}
					?>
				</select>
			</td>
			
			<td style="width:31%; 
					<?php
					if ( $selected_val_key_1 == 'show_globally' ) {
						echo 'display:none;'; }
					?>
	">
				<div class="adsforwp-insert-comparision-select">              
					<?php
					$ajax_select_box_obj = new Adsforwp_Ajax_Selectbox();
					$ajax_select_box_obj->adsforwp_comparision_condition_type_values( $selected_val_key_1, $selected_val_key_2, $selected_val_key_4, $selected_val_key_5, $i, $j );
					?>
					<div style="display:none;" class="spinner"></div>                  
				</div>

			</td>
			<td style="width:31%; 
					<?php
					if ( $selected_val_key_1 == 'show_globally' ) {
						echo 'display:none;'; }
					?>
	">
				<div class="adsforwp-insert-condition-select">              
					<?php
					$ajax_select_box_obj = new Adsforwp_Ajax_Selectbox();
					$ajax_select_box_obj->adsforwp_visitor_condition_type_values( $selected_val_key_1, $selected_val_key_3, $selected_val_key_4, $selected_val_key_5, $i, $j );
					?>
					<div style="display:none;" class="spinner"></div>                  
				</div>
			</td>
			
			<td class="widefat adsforwp-visitor-condition-row-clone" style="width:3.5%; 
					<?php
					if ( $selected_val_key_1 == 'show_globally' ) {
						echo 'display:none;'; }
					?>
	">
				<span> <button type="button" class="afw-placement-button"> <?php echo esc_html__( 'And', 'ads-for-wp' ); ?> </button> </span> 
			</td>
			
			<td class="widefat adsforwp-visitor-condition-row-delete" style="width:3.5%; 
					<?php
					if ( $selected_val_key_1 == 'show_globally' ) {
						echo 'display:none;'; }
					?>
	">
				<button  type="button"><span class="dashicons dashicons-trash"></span> </button>
			</td>                                           
			</tr>
		   
					<?php

				}
				?>
		</tbody>
		</table> 
	</div>
			<?php } ?>
	
	<a style="margin-left: 8px; margin-bottom: 8px;" class="button adsforwp-visitor-condition-or-group afw-placement-button" href="#"><?php echo esc_html__( 'Or', 'ads-for-wp' ); ?></a>
	</div>
		<?php
		$user_target_type          = '';
		$afwp_cache_mobile_support = '';
		$visitor_conditions_array  = get_post_meta( $post->ID, 'visitor_conditions_array', true );
		if ( is_array( $visitor_conditions_array ) && ! empty( $visitor_conditions_array ) ) {
			foreach ( $visitor_conditions_array as $key => $value ) {
				foreach ( $value as $gkey => $gvalue ) {
					foreach ( $gvalue as $hkey => $hvalue ) {
						if ( $hvalue['key_1'] == 'device' ) {
							$user_target_type = 1;
							break;
						}
					}
				}
			}
		}
		$message = '';
		if ( function_exists( 'wpsc_init' ) ) {
			global $wp_cache_mobile_enabled;
			if ( ! $wp_cache_mobile_enabled ) {
				$afwp_cache_mobile_support = 1;
				$message                   = "You are using WP Super Cache plugin, if you want User Targeting for 'Device Type' to work perfectly we recommend you to enable Mobile Support of your cache plugin.";
			}
		} elseif ( defined( 'WP_ROCKET_FILE' ) ) {
			$wp_rocket_option = get_option( 'wp_rocket_settings' );
			if ( ! $wp_rocket_option['cache_mobile'] || ! $wp_rocket_option['do_caching_mobile_files'] ) {
				$afwp_cache_mobile_support = 1;
				$message                   = "You are using WP Rocket Cache plugin, if you want User Targeting for 'Device Type' to work perfectly we recommend you to enable Mobile Support of your cache plugin.";
			}
		} elseif ( defined( 'W3TC_FILE' ) ) {
			$afwp_cache_mobile_support = 1;
			$message                   = "You are using WP3 Total Cache plugin, if you want User Targeting for 'Device Type' to work perfectly we recommend you to enable Mobile Support of your cache plugin.";
		}

		$visitor_condition_enable = get_post_meta( $post->ID, $key = 'adsforwp_v_condition_enable', true );
		if ( isset( $visitor_condition_enable ) && $visitor_condition_enable == 'enable' ) {
			if ( $user_target_type == 1 && $afwp_cache_mobile_support == 1 ) {
				?>
	<p class="device-type-notice"><span class="dashicons dashicons-warning"></span><em>Warning: <?php echo esc_html( $message ); ?></em></p>
				<?php
			}
		}
		?>
</div>
		<?php
	}

	public function adsforwp_visitor_condition_save( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
		if ( ! isset( $_POST['adsforwp_visitor_condition_name_nonce'] ) || ! wp_verify_nonce( $_POST['adsforwp_visitor_condition_name_nonce'], 'adsforwp_visitor_condition_action_nonce' ) ) {
			return;
		}

		// if our current user can't edit this post, bail
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

			$post_visitor_conditions_array = array();
			$temp_condition_array          = array();
			$show_globally                 = false;

		if ( isset( $_POST['visitor_conditions_array'] ) ) {

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$post_visitor_conditions_array = (array) $_POST['visitor_conditions_array'];
			/*
			Type casted the $_POST['visitor_conditions_array'] to (array) to make sure
			 * what ever data is sent is should be in array format.
			 * and then we are sanitizing $post_visitor_conditions_array with adsforwp_sanitize_multi_array()
			 * function to make sure we have sanitized keys and its values.
			*/
			foreach ( $post_visitor_conditions_array as $groups ) {

				foreach ( $groups['visitor_conditions'] as $group ) {

					if ( array_search( 'show_globally', $group ) ) {
						$temp_condition_array[0] = $group;
						$show_globally           = true;
					}
				}
			}

			if ( $show_globally ) {

				unset( $post_visitor_conditions_array );
				$post_visitor_conditions_array['group-0']['visitor_conditions'] = $temp_condition_array;
			}
		}
		if ( ! empty( $post_visitor_conditions_array ) ) {

			$post_visitor_conditions_array = adsforwp_sanitize_multi_array( $post_visitor_conditions_array, 'visitor_conditions' );
			update_post_meta(
				$post_id,
				'visitor_conditions_array',
				$post_visitor_conditions_array
			);

		}

		if ( isset( $_POST['adsforwp_v_condition_enable'] ) ) {

			$status = sanitize_text_field( wp_unslash( $_POST['adsforwp_v_condition_enable'] ) );

			update_post_meta(
				$post_id,
				'adsforwp_v_condition_enable',
				$status
			);

		}
	}
	public function adsforwp_visitor_condition_logic_checker( $input ) {
		global $post;

		$type = $comparison = $data = $result = '';

		if ( is_array( $input ) ) {

			$type       = array_key_exists( 'key_1', $input ) ? $input['key_1'] : '';
			$comparison = array_key_exists( 'key_2', $input ) ? $input['key_2'] : '';
			$data       = array_key_exists( 'key_3', $input ) ? $input['key_3'] : '';

		}

		// Get all the users registered
		$user = wp_get_current_user();

		switch ( $type ) {

			case 'device':
				require_once ADSFORWP_PLUGIN_DIR . '/vendor/class-adsforwp-mobile-detect.php';
				$mobile_detect = $isTablet = '';
				// instantiate the Mobile detect class
				$mobile_detect = new Adsforwp_Mobile_Detect();
				$isMobile      = $mobile_detect->isMobile();
				$isTablet      = $mobile_detect->isTablet();

				$device_name = 'desktop';
				if ( $isMobile && $isTablet ) { // Only For tablet
					$device_name = 'mobile';
				} elseif ( $isMobile && ! $isTablet ) { // Only for mobile
					$device_name = 'mobile';
				}

				if ( $comparison == 'equal' ) {
					if ( $device_name == $data ) {
						$result = true;
					}
				}
				if ( $comparison == 'not_equal' ) {
					if ( $device_name != $data ) {
						$result = true;
					}
				}
				break;
			case 'referrer_url':
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason Using it for condition check
					$referrer_url = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_url( $_SERVER['HTTP_REFERER'] ) : '';
				if ( isset( $input['key_4'] ) && $input['key_3'] == 'url_custom' ) {
					$data = $input['key_4'];
				}

				if ( $comparison == 'equal' ) {
					if ( $referrer_url == $data ) {
						$result = true;
					}
				}
				if ( $comparison == 'not_equal' ) {
					if ( $referrer_url != $data ) {
						$result = true;
					}
				}

				break;
			case 'browser_width':
				$result = true;
				break;
			case 'browser_language':
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason Using it for condition check
					$browser_language = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 ) : '';
				if ( $comparison == 'equal' ) {
					if ( $browser_language == $data ) {
						$result = true;
					}
				}
				if ( $comparison == 'not_equal' ) {
					if ( $browser_language != $data ) {
						$result = true;
					}
				}
				break;

			case 'geo_location':
					$country_code = '';
					$saved_ip     = '';
					$user_ip      = $this->adsforwp_get_client_ip();

					$saved_ip_list = array();

				if ( isset( $_COOKIE['adsforwp-user-info'] ) ) {

					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason Using it for condition check
					$saved_ip_list = $_COOKIE['adsforwp-user-info'];
					$saved_ip      = trim( base64_decode( $saved_ip_list[0] ) );

				}

				if ( ! empty( $saved_ip_list ) && $saved_ip == $user_ip ) {

					if ( isset( $saved_ip_list[1] ) ) {

						$country_code = trim( base64_decode( $saved_ip_list[1] ) );

					}
				}
				if ( $comparison == 'equal' ) {
					if($country_code!=""){
						if ( $country_code == $data ) {
							$result = true;
						}
					}else{
						$ip    = $this->adsforwp_get_client_ip();
						$ipdat = wp_remote_get( 'http://www.geoplugin.net/json.gp?ip=' . $ip );
						if ( ! is_wp_error( $ipdat ) && wp_remote_retrieve_response_code( $ipdat ) === 200 ) {
								$body  = wp_remote_retrieve_body( $ipdat );
								$ipdat = json_decode( $body );
								if ( $ipdat !== null ) {
									$country_code = $ipdat->geoplugin_countryCode;
									$country_code = $this->adsforwp_get_three_letter_country_code_using_two($country_code);
								}
						}
						if ( $country_code == $data ) {
								$result = true;
						}
					}
				}
				if ( $comparison == 'not_equal' ) {
					if($country_code!=""){
						if ( $country_code != $data ) {
							$result = true;
						}
					}else{
						$ip    = $this->adsforwp_get_client_ip();
						$ipdat = wp_remote_get( 'http://www.geoplugin.net/json.gp?ip=' . $ip );
						if ( ! is_wp_error( $ipdat ) && wp_remote_retrieve_response_code( $ipdat ) === 200 ) {
								$body  = wp_remote_retrieve_body( $ipdat );
								$ipdat = json_decode( $body );
								if ( $ipdat !== null ) {
									$country_code = $ipdat->geoplugin_countryCode;
									$country_code = $this->adsforwp_get_three_letter_country_code_using_two($country_code);
								}
						}
						if ( $country_code != $data ) {
								$result = true;
						}
					}
				}
				break;

			case 'url_parameter':
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason Using it for condition check
					$url = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_url( $_SERVER['REQUEST_URI'] ) : '';
				if ( $comparison == 'equal' ) {
					if ( strpos( $url, $data ) !== false ) {
						$result = true;
					}
				}
				if ( $comparison == 'not_equal' ) {
					if ( strpos( $url, $data ) == false ) {
						$result = true;
					}
				}
				break;

			case 'cookie':
				$cookie_arr = $_COOKIE;
				if ( $data == '' ) {

					if ( $comparison == 'equal' ) {
						if ( isset( $cookie_arr ) ) {
							$result = true;
						}
					}
					if ( $comparison == 'not_equal' ) {
						if ( ! isset( $cookie_arr ) ) {
							$result = true;
						}
					}
				} else {

					if ( $comparison == 'equal' ) {

						if ( $cookie_arr ) {

							foreach ( $cookie_arr as $arr ) {

								if ( $arr == $data ) {
										$result = true;
										break;
								}
							}
						}
					}
					if ( $comparison == 'not_equal' ) {

						if ( isset( $cookie_arr ) ) {

							foreach ( $cookie_arr as $arr ) {

								if ( $arr != $data ) {
									$result = true;
									break;
								}
							}
						}
					}
				}

				break;

			case 'logged_in_visitor':
				if ( is_user_logged_in() ) {
					$status = 'true';
				} else {
					$status = 'false';
				}

				if ( $comparison == 'equal' ) {
					if ( $status == $data ) {
						$result = true;
					}
				}
				if ( $comparison == 'not_equal' ) {
					if ( $status != $data ) {
						$result = true;
					}
				}

				break;

			case 'user_agent':
				$user_agent_name = $this->adsforwp_detect_user_agent();

				if ( isset( $input['key_5'] ) && $input['key_3'] == 'user_agent_custom' ) {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Using it for condition check
					if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && stripos( $_SERVER['HTTP_USER_AGENT'], $input['key_5'] ) ) {
						$user_agent_name = $input['key_5'];
					}
					$data = $input['key_5'];
				}
				if ( $comparison == 'equal' ) {
					if ( $user_agent_name == $data ) {
						$result = true;
					}
				}
				if ( $comparison == 'not_equal' ) {
					if ( $user_agent_name != $data ) {
						$result = true;
					}
				}
				break;

			case 'user_type':
				if ( $comparison == 'equal' ) {
					if ( in_array( $data, (array) $user->roles ) ) {
						$result = true;
					}
				}

				if ( $comparison == 'not_equal' ) {
					require_once ABSPATH . 'wp-admin/includes/user.php';
					// Get all the registered user roles
					$roles          = get_editable_roles();
					$all_user_types = array();
					foreach ( $roles as $key => $value ) {
						$all_user_types[] = $key;
					}
					// Flip the array so we can remove the user that is selected from the dropdown
					$all_user_types = array_flip( $all_user_types );
					// User Removed
					unset( $all_user_types[ $data ] );
					// Check and make the result true that user is not found
					if ( in_array( $data, (array) $all_user_types ) ) {
						$result = true;
					}
				}

				break;
			case 'membership_level':
				if ( $comparison == 'equal' ) {
					if ( isset( $user->membership_level ) && function_exists( 'pmpro_getAllLevels' ) ) {
						if ( in_array( $data, (array) $user->membership_level->id ) ) {
							$result = true;
						}
					}
				}
				if ( $comparison == 'not_equal' ) {
					require_once ABSPATH . 'wp-admin/includes/user.php';
					// Get all the registered user roles
					if ( function_exists( 'pmpro_getAllLevels' ) ) {
						$all_pmpro_levels = pmpro_getAllLevels( false, true );
						$all_level_types  = array();
						foreach ( $all_pmpro_levels as $key => $value ) {
							$all_level_types[] = $value->id;
						}
						// Flip the array so we can remove the user that is selected from the dropdown
						$all_level_types = array_flip( $all_level_types );
						// User Removed
						unset( $all_level_types[ $data ] );
						// Check and make the result true that user is not found
						if ( in_array( $data, (array) $all_level_types ) ) {
							$result = true;
						}
					}
				}
				break;
			default:
				$result = false;
				break;
		}

		return $result;
	}


	public function adsforwp_detect_user_agent() {
			$user_agent_name = 'others';
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Using it for condition check
			$server_user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		if ( strpos( $server_user_agent, 'Opera' ) || strpos( $user_agent, 'OPR/' ) ) {
			$user_agent_name = 'opera';
		} elseif ( strpos( $server_user_agent, 'Edge' ) ) {
			$user_agent_name = 'edge';
		} elseif ( strpos( $server_user_agent, 'Firefox' ) ) {
			$user_agent_name = 'firefox';
		} elseif ( strpos( $server_user_agent, 'MSIE' ) || strpos( $user_agent, 'Trident/7' ) ) {
			$user_agent_name = 'internet_explorer';
		} elseif ( stripos( $server_user_agent, 'iPod' ) ) {
			$user_agent_name = 'ipod';
		} elseif ( stripos( $server_user_agent, 'iPhone' ) ) {
			$user_agent_name = 'iphone';
		} elseif ( stripos( $server_user_agent, 'iPad' ) ) {
			$user_agent_name = 'ipad';
		} elseif ( stripos( $server_user_agent, 'Android' ) ) {
			$user_agent_name = 'android';
		} elseif ( stripos( $server_user_agent, 'webOS' ) ) {
			$user_agent_name = 'webos';
		} elseif ( strpos( $server_user_agent, 'Chrome' ) ) {
			$user_agent_name = 'chrome';
		} elseif ( strpos( $server_user_agent, 'Safari' ) ) {
			$user_agent_name = 'safari';
		}

			return $user_agent_name;
	}

	public function adsforwp_visitor_condition_field_data( $post_id ) {

		$visitor_conditions_array = get_post_meta( $post_id, 'visitor_conditions_array', true );
		$output                   = array();
		if ( $visitor_conditions_array ) {
			foreach ( $visitor_conditions_array as $gropu ) {
				$output[] = array_map( array( $this, 'adsforwp_visitor_condition_logic_checker' ), $gropu['visitor_conditions'] );
			}
		}

		return $output;
	}

	public function adsforwp_visitor_conditions_status( $post_id ) {

			$unique_checker           = '';
			$visitor_condition_enable = get_post_meta( $post_id, $key = 'adsforwp_v_condition_enable', true );

		if ( isset( $visitor_condition_enable ) && $visitor_condition_enable == 'enable' ) {

			$resultset = $this->adsforwp_visitor_condition_field_data( $post_id );
			if ( $resultset ) {

				$condition_array = array();

				foreach ( $resultset as $result ) {

					$data             = array_filter( $result );
					$number_of_fields = count( $data );
					$checker          = 0;

					if ( $number_of_fields > 0 ) {

						$checker        = count( array_unique( $data ) );
						$array_is_false = in_array( false, $result );

						if ( $array_is_false ) {

									$checker = 0;

						}
					}

					$condition_array[] = $checker;
				}

				$array_is_true = in_array( true, $condition_array );

				if ( $array_is_true ) {
					$unique_checker = 1;
				}
			} else {
				$unique_checker = 'notset';
			}
		} else {
			$unique_checker = 1;
		}
		return $unique_checker;
	}

	// Function to get the client IP address
	public function adsforwp_get_client_ip() {
		$ipaddress = '';
		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Just using it for condition check
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Just using it for condition check
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Just using it for condition check
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Just using it for condition check
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Just using it for condition check
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Just using it for condition check
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		} else {
			$ipaddress = 'UNKNOWN';
		}
		return $ipaddress;
	}
}
if ( class_exists( 'Adsforwp_View_Visitor_Condition' ) ) {
	new Adsforwp_View_Visitor_Condition();
}
