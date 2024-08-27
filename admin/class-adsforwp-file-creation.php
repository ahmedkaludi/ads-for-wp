<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * This class handls all the file creation functions,
 * which we use in different different part in project
 */
class Adsforwp_File_Creation {


		public $ad_support;
		public $wppath;

	public function __construct() {
		$this->wppath     = str_replace( '//', '/', str_replace( '\\', '/', realpath( ABSPATH ) ) . '/' );
		$this->ad_support = $this->wppath . 'front.js';
	}
		/**
		 * Function to create a ad blocker js file
		 *
		 * @return boolean
		 */
	public function adsforwp_create_adblocker_support_js() {

		$writestatus = '';

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			include_once ABSPATH . '/wp-admin/includes/file.php';
		}

		global $wp_filesystem;
		if ( ! $wp_filesystem ) {
			WP_Filesystem();
		}

		if ( $wp_filesystem->exists( $this->ad_support ) ) {
			$wp_filesystem->delete( $this->ad_support );
		}

		if ( ! $wp_filesystem->exists( $this->ad_support ) ) {
			$response = wp_remote_get( ADSFORWP_PLUGIN_DIR_URI . 'public/assets/js/ads-front.js' );

			if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
				$swHtmlContent = wp_remote_retrieve_body( $response );
				$writestatus   = $wp_filesystem->put_contents( $this->ad_support, $swHtmlContent, FS_CHMOD_FILE );
			}
		}

		if ( $writestatus ) {

			return true;

		} else {

			return false;
		}
	}

	/**
	 * Function to delete a ad blocker js file
	 *
	 * @return type
	 */
	public function adsforwp_delete_adblocker_support_js() {

		$result = '';

		if ( file_exists( $this->ad_support ) ) {

			$result = wp_delete_file( $this->ad_support );

		}

		return $result;
	}
}
