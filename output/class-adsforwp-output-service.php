<?php
class Adsforwp_Output_Service {

	public function adsforwp_enque_amp_floating_ad_css( $ad_id ) {
		$post_meta_dataset = get_post_meta( $ad_id, $key = '', true );
		$floating_ad       = adsforwp_rmv_warnings( $post_meta_dataset, 'adsforwp_ad_floating', 'adsforwp_array' );
		if ( $floating_ad == 1 ) {
			?>
			.afwadid-<?php echo esc_attr( $ad_id ); ?>{
				position:sticky;
				top:60px;
				padding-top:40px;
				display:block;
			}
			<?php
		}
	}
	public function adsforwp_enque_amp_sticky_ad_css( $ad_id ) {

		$post_meta_dataset = get_post_meta( $ad_id, $key = '', true );
		$ad_img_width      = adsforwp_rmv_warnings( $post_meta_dataset, 'adsforwp_ad_img_width', 'adsforwp_array' );
		$ad_img_height     = adsforwp_rmv_warnings( $post_meta_dataset, 'adsforwp_ad_img_height', 'adsforwp_array' );

		?>
				.adsforwp-stick-ad{
			padding-top:0px;
		}               
		.afw_ad_amp_achor{
			text-align:center;
		}
		#afw_ad_amp_anchor_<?php echo esc_attr( $ad_id ); ?> amp-img{
			width:<?php echo esc_attr( $ad_img_width ); ?>px;
			height:<?php echo esc_attr( $ad_img_height ); ?>px;
		}
		.adsforwp-sticky-ad-close {
			position: absolute;
			right: 0px;
			top: 0px;
			padding:2px;
			cursor:pointer;
			color:#000;
			background: transparent;
			border: #fff;
		}
		.adsforwp-sticky-ad-close:after{
			display: inline-block;
			content: "\00d7"; 
		}     
		
		<?php
	}

	public function adsforwp_check_ad_expiry_date( $ad_id ) {

		$post_meta_dataset    = get_post_meta( $ad_id, $key = '', true );
		$ad_expire_days       = get_post_meta( $ad_id, $key = 'adsforwp_ad_expire_days', true );
		$ad_expire_enable     = adsforwp_rmv_warnings( $post_meta_dataset, 'adsforwp_ad_expire_enable', 'adsforwp_array' );
		$ad_expire_from       = adsforwp_rmv_warnings( $post_meta_dataset, 'adsforwp_ad_expire_from', 'adsforwp_array' );
		$ad_expire_to         = adsforwp_rmv_warnings( $post_meta_dataset, 'adsforwp_ad_expire_to', 'adsforwp_array' );
		$ad_days_enable       = adsforwp_rmv_warnings( $post_meta_dataset, 'adsforwp_ad_expire_day_enable', 'adsforwp_array' );
		$ad_impression_enable = adsforwp_rmv_warnings( $post_meta_dataset, 'adsforwp_ad_expire_impression_enable', 'adsforwp_array' );
		$ad_impression_limit  = adsforwp_rmv_warnings( $post_meta_dataset, 'adsforwp_ad_expire_impression_limit', 'adsforwp_array' );

		$impression = 0;

		$current_date = gmdate( 'Y-m-d' );

		if ( $ad_expire_enable ) {

			if ( $ad_expire_from && $ad_expire_to ) {

				if ( $ad_expire_from <= $current_date && $ad_expire_to >= $current_date ) {

					if ( $ad_days_enable ) {

						if ( ! empty( $ad_expire_days ) ) {

							foreach ( $ad_expire_days as $days ) {

								if ( gmdate( 'Y-m-d', strtotime( $days ) ) == $current_date ) {
									if ( $ad_impression_enable ) {
										if ( ! empty( $ad_impression_limit ) ) {
												$ad_stats   = adsforwp_get_ad_stats( 'sumofstats', $ad_id );
												$impression = $ad_stats['impressions'];
											if ( $impression < $ad_impression_limit ) {
												return true;
											}
										}
									} else {
										return true;
									}
								}
							}
						}
					} elseif ( $ad_impression_enable ) {
						if ( ! empty( $ad_impression_limit ) ) {
							$ad_stats   = adsforwp_get_ad_stats( 'sumofstats', $ad_id );
							$impression = $ad_stats['impressions'];
							if ( $impression < $ad_impression_limit ) {
								return true;
							}
						}
					} else {
						return true;
					}
				}
			} else {
				return false;
			}
		} elseif ( $ad_days_enable ) {

			if ( $ad_expire_days ) {
				foreach ( $ad_expire_days as $days ) {
					if ( gmdate( 'Y-m-d', strtotime( $days ) ) == $current_date ) {
						if ( $ad_impression_enable ) {
							if ( ! empty( $ad_impression_limit ) ) {
								$ad_stats   = adsforwp_get_ad_stats( 'sumofstats', $ad_id );
								$impression = $ad_stats['impressions'];
								if ( $impression < $ad_impression_limit ) {

									return true;
								}
							}
						} else {
							return true;
						}
					}
				}
			}
		} elseif ( $ad_impression_enable ) {
			if ( ! empty( $ad_impression_limit ) ) {
				$ad_stats   = adsforwp_get_ad_stats( 'sumofstats', $ad_id );
				$impression = $ad_stats['impressions'];
				if ( $impression < $ad_impression_limit ) {
					return true;
				}
			}
		} else {
			return true;
		}
	}

	public function adsforwp_is_condition( $ad_id ) {

			$status    = false;
			$published = get_post_status( $ad_id );

			$placement_obj       = new Adsforwp_View_Placement();
			$advn_display_status = $placement_obj->adsforwp_get_post_conditions_status( $ad_id );

		if ( ( $advn_display_status === 1 || $advn_display_status === 'notset' ) && $published == 'publish' ) {

			$visitor_condition_obj = new Adsforwp_View_Visitor_Condition();
			$user_targeting_status = $visitor_condition_obj->adsforwp_visitor_conditions_status( $ad_id );

			if ( $user_targeting_status === 1 || $user_targeting_status === 'notset' ) {

					$expiry_status = $this->adsforwp_check_ad_expiry_date( $ad_id );

				if ( $expiry_status ) {

					$ad_post_meta = get_post_meta( $ad_id, $key = '', true );

					$post_visibility    = get_post_meta( get_the_ID(), $key = 'ads-for-wp-visibility', true );
					$non_amp_visibility = adsforwp_rmv_warnings( $ad_post_meta, 'ads_for_wp_non_amp_visibility', 'adsforwp_array' );
					$amp_compatibility  = adsforwp_rmv_warnings( $ad_post_meta, 'ads-for-wp_amp_compatibilty', 'adsforwp_array' );

					if ( $post_visibility != 'hide' ) {

						if ( ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {

							if ( $amp_compatibility != 'disable' ) {
								$status = true;
							}
						} elseif ( $non_amp_visibility != 'hide' ) {

								$status = true;
						}
					}
				}
			}
		}

			return $status;
	}
}
