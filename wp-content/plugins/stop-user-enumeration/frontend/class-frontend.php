<?php


/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, hooks & filters
 *
 */

namespace Stop_User_Enumeration\FrontEnd;

use Stop_User_Enumeration\Includes\Core;

use WP_Error;

class FrontEnd {

	/**
	 * The ID of this plugin.
	 *
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 */
	private $version;

	private $option_key = 'stop-user-enumeration';

	/**
	 * Initialize the class and set its properties.
	 *
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/frontend.js', array( 'jquery' ), $this->version, false );

	}


	public function check_request() {
		/*
		* Validate incoming request
		 *
		 */
		if ( ! is_user_logged_in() && isset( $_REQUEST['author'] ) ) {
			if ( $this->ContainsNumbers( $_REQUEST['author'] ) ) {
				$this->sue_log();
				wp_die( esc_html__( 'forbidden - number in author name not allowed = ', 'stop-user-enumeration' ) . esc_html( $_REQUEST['author'] ) );
			}
		}
	}

	public function only_allow_logged_in_rest_access_to_users( $access ) {
		if ( 'on' === Core::sue_get_option( 'stop_rest_user', 'off' ) ) {
			if ( ( preg_match( '/users/', $_SERVER['REQUEST_URI'] ) !== 0 ) || ( isset( $_REQUEST['rest_route'] ) && ( preg_match( '/users/', $_REQUEST['rest_route'] ) !== 0 ) ) ) {
				if ( ! is_user_logged_in() ) {
					$this->sue_log();

					return new WP_Error( 'rest_cannot_access', esc_html__( 'Only authenticated users can access the User endpoint REST API.', 'stop-user-enumeration' ), array( 'status' => rest_authorization_required_code() ) );
				}
			}
		}

		return $access;
	}

	private function ContainsNumbers( $String ) {
		return preg_match( '/\\d/', $String ) > 0;
	}

	private function sue_log() {
		$ip = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
		if ( is_plugin_active( 'fullworks-firewall/fullworks-firewall.php' ) ) {
			do_action( 'fullworks_security_block_ip', $ip, 1, 'stop-user-enumeration' );
		}
		if ( 'on' === Core::sue_get_option( 'log_auth', 'off' ) ) {
			openlog( 'wordpress(' . sanitize_text_field( $_SERVER['HTTP_HOST'] ) . ')', LOG_NDELAY | LOG_PID, LOG_AUTH );
			syslog( LOG_INFO, "Attempted user enumeration from " . $ip );
			closelog();
		}
	}

}
