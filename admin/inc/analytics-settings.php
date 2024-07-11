<?php
//namespace Adsforwp\analytics;
class Adsforwp_analyticsSettings{
    
	public $ClientID = '615474230703-50bpfep5sehi7aff3721jfcugogj0c8v.apps.googleusercontent.com';
	public $ClientSecret = 'KitbAxJMqJ__wwgKUMICNNGh';
	public $scope = 'https://www.googleapis.com/auth/analytics.readonly' ; // Readonly scope.
	public $redirect = 'http://www.adsforwp.com/analtyics/index.php';

	public $settings;
	protected $client;
	protected $service;
	protected $state_data;
	protected $transient_timeout;
	protected $load_settings;
	protected $plugin_base;
	protected $plugin_settings_base;
        
	function __construct(){
		add_action( 'admin_init', array( $this, 'adsforwp_google_authentication' ) );
		if ( ! class_exists( 'Adsforwp_Google_Client' ) ) {
			require_once ADSFORWP_LIB_PATH . 'Google/Client.php';
			require_once ADSFORWP_LIB_PATH . 'Google/Service/Analytics.php';
		}
		// Setup Settings.
		$this->client = new Adsforwp_Google_Client();
		$this->client->setApprovalPrompt( 'force' );
		$this->client->setAccessType( 'offline' );
		

		$this->client->setClientId( $this->ClientID );
		$this->client->setClientSecret( $this->ClientSecret );
		$this->client->setRedirectUri( $this->redirect );

		$this->client->setScopes( $this->scope );
		
		try {
			$this->service = new \Adsforwp_Google_Service_Analytics( $this->client );
			if( get_option( 'adsforwp_google_token' ) ){
				$this->pa_connect();
			}
			// This function refresh token and use for debugging
			//$this->client->refreshToken( $this->token->refresh_token );
		} catch ( \Adsforwp_Google_Service_Exception $e ) {
			// Show error message only for logged in users.
			if ( current_user_can( 'manage_options' ) ) {
				echo sprintf( esc_html__( '%1$s Oops, Something went wrong. %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s ', 'ads-for-wp' ), '<br /><br />', '<br />', '<i>', '</i>', esc_textarea( $e->getMessage() ) );
			}
		} catch ( \Adsforwp_Google_Auth_Exception $e ) {
			// Show error message only for logged in users.
			if ( current_user_can( 'manage_options' ) ) {
				echo sprintf( esc_html__( '%1$s Oops, Try to %2$s Reset %3$s Authentication. %4$s %7$s %4$s %5$s Don\'t worry, This error message is only visible to Administrators. %6$s %4$s', 'ads-for-wp' ), '<br /><br />', '<a href=' . esc_url( admin_url( 'edit.php?post_type=adsforwp&page=adsforwp-analytics' ) ) . 'title="Reset">', '</a>', '<br />', '<i>', '</i>', esc_textarea( $e->getMessage() ) );
			}
		}

		$this->postCalls();
	}
	function postCalls(){
                                                                            
		if($_SERVER['REQUEST_METHOD']=='POST'){
                        
                        if ( ! isset( $_POST['adsforwp_analytics_nonce'] ) ){
                           return;
                        }
			
                        if ( !wp_verify_nonce( $_POST['adsforwp_analytics_nonce'], 'adsforwp_analytics_data' ) ){
                            return;
                        }
                        
			if(isset($_POST['saveKey'])){ //Need to remove it
				$savedOpt                 = get_option('adsforwp_analytics');
				$adsforwp_google_token    = esc_html($_POST['adsforwp_google_token']);
				$savedOpt['google_token'] = $adsforwp_google_token;
				update_option( 'adsforwp_analytics', $savedOpt ); // Security: Nonce verified
			}
			if(isset($_POST['adsforwp_profile_entry'])){
				$savedOpt                          = get_option('adsforwp_analytics');
				$profile_for_dashboard             = esc_html($_POST['profile_for_dashboard']);
				$savedOpt['profile_for_post']      = $profile_for_dashboard;
				$savedOpt['profile_for_dashboard'] = $profile_for_dashboard;
				update_option( 'adsforwp_analytics', $savedOpt ); // Security: Nonce verified
				wp_redirect(esc_url(admin_url('edit.php?post_type=adsforwp&page=analytics')));
			}
			if(isset($_POST['wp_adsforwp_analytics_log_out'])){
				
				 delete_option( 'adsforwp_google_token' );
				 delete_option( 'adsforwp_analytics' );
			}
		}
	}
	function authentication(){
		?>
		<div class="afw-analytics-title">
			<h1><?php echo esc_html__('Analytics', 'ads-for-wp'); ?></h1>
		</div>
		<?php
		$adsforwp_google_token = get_option( 'adsforwp_google_token' );
		if ( $adsforwp_google_token ) { 
			$analyticsSettings = get_option('adsforwp_analytics');
			$profile_for_post_value = $analyticsSettings['profile_for_dashboard'];
			if($profile_for_post_value){
		?>
				<div style="float:right;padding-right: 2%;">
					<a class="button" href="<?php echo esc_url( admin_url('edit.php?post_type=adsforwp&page=analytics') ); ?>"><?php esc_html_e( 'Go to dashboard.', 'ads-for-wp' ); ?></a>
				</div>
			<?php } ?>
				<form action="" method="post">
                                <?php wp_nonce_field( 'adsforwp_analytics_data', 'adsforwp_analytics_nonce' ); ?>    
				<table>
					<tr>
						<p class="inside"><?php esc_html_e( 'You have allowed your site to access the Analytics data from Google. Logout below to disconnect it.', 'ads-for-wp' ); ?></p>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" class="button-primary" value="<?php esc_html_e('Logout', 'ads-for-wp' ); ?>" name="wp_adsforwp_analytics_log_out" />
						</td>
					</tr>
				</table>
				</form>

				<?php
				$this->siteSettings();
			} else {
                                $auth_url = 'https://accounts.google.com/o/oauth2/auth?'.$this->generate_login_url();                                
				?>
				<p class="inside"><?php esc_html_e( 'For Analytics You need to allow your site to access the Analytics data from Google. Logout below to disconnect it.', 'ads-for-wp' ); ?></p>                                
                                <a title="Log in with your Google Analytics Account" class="button-primary authentication_btn" href="<?php echo esc_url_raw($auth_url); ?>"><?php esc_html_e( 'Log in with Google Analytics Account', 'ads-for-wp' ); ?></a>
				<?php
			}

	}
	private function siteSettings(){
		
			$_profile_otions = $this->fetch_profiles_list_summary();
			$profilePostOpts = '';
			$profileDashboardOpts = '';
			$analyticsSettings = get_option('adsforwp_analytics');
			$profile_for_post_value = $analyticsSettings['profile_for_dashboard'];
			$profile_for_dashboard_value = $analyticsSettings['profile_for_dashboard'];
			if(isset($_profile_otions) && $_profile_otions){
				foreach ($_profile_otions->getItems() as $account) {
					foreach ( $account->getWebProperties() as  $property ) {
						foreach ( $property->getProfiles() as $profile ) {
							$profilePostOpts .= sprintf( '<option value="%1$s" %2$s>%3$s (%4$s)</option>', $profile->getId(), selected( $profile_for_post_value, $profile->getId(), false ), $profile->getName() , $property->getId() );
							$profileDashboardOpts .= sprintf( '<option value="%1$s" %2$s>%3$s (%4$s)</option>', $profile->getId(), selected( $profile_for_dashboard_value, $profile->getId(), false ), $profile->getName() , $property->getId() );

							// Update the UA code in option on setting save for profile_for_posts.
							if (  $profile_for_post_value === $profile->getId()) {
								update_option( 'adsforwp_ua_code', $property->getId() );
							}

						}
					}
				}
			}
		?>
		<form method="post">
                    <?php wp_nonce_field( 'adsforwp_analytics_data', 'adsforwp_analytics_nonce' ); ?>    
			<table class="form-table">
				<tr>
					<td><label><?php esc_html__('Profile for Dashboard', 'ads-for-wp' ); ?></label></td>
					<td>
						<select name="profile_for_dashboard">
							
							<option value=""><?php esc_html__('Select profile for Dashboard', 'ads-for-wp' ); ?></option>
							<?php if(isset($_profile_otions) && $_profile_otions){
								foreach ($_profile_otions->getItems() as $account) {
									foreach ( $account->getWebProperties() as  $property ) {
										foreach ( $property->getProfiles() as $profile ) {
											?>
											<option value="<?php echo esc_attr($profile->getId());?>" <?php echo esc_attr(selected( $profile_for_dashboard_value, $profile->getId(),false ));?>><?php echo esc_html($profile->getName()); ?> (<?php echo esc_html($property->getId()); ?>)</option>
											<?php
										}
									}
								}
							} 
			?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="btn button-primary" name="adsforwp_profile_entry" value="View Report"></td>
				</tr>
			</table>
		</form>
		<?php
	}
	private function generate_login_url(){
		$ADSFORWP = $GLOBALS['ADSFORWP'];
		$url = '';

		$redirect_url   = $this->redirect;
		$client_id      = $this->ClientID;
		
		$url = http_build_query( array(
			'next'            => admin_url( 'edit.php?post_type=adsforwp&page=adsforwp-analytics' ),
			'scope'           => $this->scope,
			'response_type'   => 'code',
			'state'           => get_admin_url() . 'edit.php?post_type=adsforwp&page=adsforwp-analytics',
			'redirect_uri'    => $redirect_url,
			'client_id'       => $client_id,
			'access_type'     => 'offline',
			'approval_prompt' => 'force',
			)
		);

		return $url;
	}
	function fetch_profiles_list_summary() {

		$profiles = get_option( 'ampforwp_profiles_list_summary' );

		if ( ! $profiles && get_option( 'adsforwp_google_token' ) ) {

			try {
				if ( get_option( 'adsforwp_profile_exception' ) ) {
					$this->adsforwp_handle_exceptions( get_option( 'adsforwp_profile_exception' ) );
				} else if ( get_option( 'adsforwp_google_token' ) != '' ) {
					$profiles = $this->service->management_accountSummaries->listManagementAccountSummaries();
				} else {
					echo '<br /><div class="notice notice-warning"><p>' . esc_html__( 'Notice: You must authenticate to access your web profiles.', 'ads-for-wp' ) . '</p></div>';
				}
			} catch (Exception $e) {
				// Show admin notice if some exception occurs.
				$this->adsforwp_handle_exceptions( $e->getErrors() );
				update_option( 'adsforwp_profile_exception', $e->getErrors() );
			}



			update_option( 'ampforwp_profiles_list_summary' , $profiles );
		}

		return $profiles;
	}
	
	public function adsforwp_google_authentication(){
            
                        if ( ! current_user_can( 'manage_options' ) ) {
                            return;
                        }
                
                        if ( isset( $_GET['code'] ) && 'adsforwp-analytics' === $_GET['page'] ) {
				$key_google_token = sanitize_text_field( wp_unslash( $_GET['code'] ) );
				try {
					update_option( 'adsforwp_post_analytics_token', $key_google_token ); // Security: Permission verified
					if ( $this->pa_connect() ) { wp_redirect(  esc_url(admin_url( 'edit.php?post_type=adsforwp&page=adsforwp-analytics' ))); }
				} catch (Exception $e) {
					echo esc_html($e->getMessage());
				}				
			}
	}

	public function pa_connect() {
			$ga_google_authtoken = get_option( 'adsforwp_google_token' );
			if ( ! empty( $ga_google_authtoken ) ) {
				$this->client->setAccessToken( $ga_google_authtoken );
			} else {
				$auth_code = get_option( 'adsforwp_post_analytics_token' );
				if ( empty( $auth_code ) ) { return false; }
					try {
						$access_token = $this->client->authenticate( $auth_code );
					} catch ( Exception $e ) {
						echo 'Adsforwp (Bug): ' . esc_textarea( $e->getMessage() );
						return false;
					}
					if ( $access_token ) {
						$this->client->setAccessToken( $access_token );
						update_option( 'adsforwp_google_token', $access_token );
						return true;
					} else {
						return false;
					}
			}

			$this->token = json_decode( $this->client->getAccessToken() );

			return true;
		}
	function adsforwp_handle_exceptions( $_exception_errors ) {
		if ( $_exception_errors[0]['reason'] == 'dailyLimitExceeded' ) {
			add_action( 'admin_notices', array( $this,'daily_limit_exceed_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'insufficientPermissions' ) {
			add_action( 'admin_notices', array( $this,'insufficent_permissions_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'usageLimits.userRateLimitExceededUnreg' ) {
			add_action( 'admin_notices', array( $this,'user_rate_limit_unreg_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'userRateLimitExceeded' ) {
			add_action( 'admin_notices', array( $this,'user_rate_limit_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'rateLimitExceeded' ) {
			add_action( 'admin_notices', array( $this,'rate_limit_exceeded_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'quotaExceeded' ) {
			add_action( 'admin_notices', array( $this,'quota_exceeded_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'accessNotConfigured' ) {
			add_action( 'admin_notices', array( $this,'accessNotConfigured' ), 9 );
		}
	}

	function show_notice_messages(){
		$this->admin_notice_messages;
	}
}

add_action( 'plugins_loaded', 'adsforwp_instantiate_analytics_class' );
function adsforwp_instantiate_analytics_class(){
if(is_admin() && current_user_can( 'manage_options' ) ){	
	$GLOBALS['ADSFORWP'] = new Adsforwp_analyticsSettings();
}    
}
