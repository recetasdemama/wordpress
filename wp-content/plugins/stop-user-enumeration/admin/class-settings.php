<?php
/**
 * Created
 * User: alan
 * Date: 09/11/17
 * Time: 15:12
 */

namespace Stop_User_Enumeration\Admin;


use CMB2_Tabs;


class Settings {

	private $options_key = 'stop-user-enumeration';
	private $msg;

	public function __construct( $plugin_name, $version, $freemiusSDK ) {


		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->freemiusSDK = $freemiusSDK;
		// Init CMB2 ( consider if needed elsewhere )
		require_once( STOP_USER_ENUMERATION_PLUGIN_DIR . 'includes/vendor/cmb2/init.php' );
		require_once( STOP_USER_ENUMERATION_PLUGIN_DIR . 'includes/vendor/cmb2-extensions/cmb2-tabs/cmb2-tabs.php' );


	}

	public function register_settings() {

		/**
		 * Registers options page menu item and form.
		 */


		$options = array(
			'id'           => 'stop-user-enumeration_option_metabox',
			'title'        => esc_html__( 'Stop User Enumeration', 'stop-user-enumeration' ),
			'tabs'         => array(
				'info'     => array(
					'label' => __( 'info', 'stop-user-enumeration' ),
					'icon'  => 'dashicons-info',
				),
				'settings' => array(
					'label' => __( 'Settings', 'stop-user-enumeration' ),
					'icon'  => 'dashicons-admin-generic',
				),
			),
			"tab_style"    => "default",
			'object_types' => array( 'options-page' ),
			/*
			 * The following parameters are specific to the options-page box
			 * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
			 */
			'option_key'   => $this->options_key,
			// The option key and admin menu page slug.
			// 'icon_url'     => 'dashicons-shield',
			// Menu icon. Only applicable if 'parent_slug' is left empty.
			// 'menu_title'      => esc_html__( 'Options', 'myprefix' ), // Falls back to 'title' (above).
			'parent_slug'  => 'options-general.php',
			// Make options page a submenu item of the themes menu.
			// 'capability'      => 'manage_options', // Cap required to view options-page.
			// 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
			// 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
			// 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
			// 'save_button'     => esc_html__( 'Save Theme Options', 'myprefix' ), // The text for the options-page save button. Defaults to 'Save'.

		);


		$cmb_options = new_cmb2_box( $options );
		/*
		 * Options fields ids only need
		 * to be unique within this box.
		 * Prefix is not needed.
		 */


		$cmb_options->add_field( array(

			'before'        => '<h2>Information</h2>',
			'after_field'   => __( '<p>Stop User Enumeration detects attempts by malicious scanners to identify your users</p>
<p>If a bot or user is caught scanning for user names they are denied access and their IP is logged</p>
<p>When you are viewing an admin page, the plugin does nothing, this is designed this way as it is assumed admin user have authority, bear this in mind when testing.</p><br>
<p>This plugin is best used in conjunction with a blocking tool to exclude the IP for longer. If you are on a VPS or dedicated server where you have root access you can install and configure <a href="https://www.fail2ban.org" target="_blank">fail2ban</a> or if you are on a shared host you can install <a href="https://wordpress.org/plugins/fullworks-firewall/" target="_blank">Fullworks Firewall</a> which does a very similar job, but requires no configuration to work automatically with Stop User Enumeration</a></p>', 'stop-user-enumeration' ),
			'id'            => 'info_text',
			'type'          => 'title',
			'tab'           => 'info',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		) );

		$cmb_options->add_field( array(
			'name'          => esc_html__( 'Stop REST API User calls', 'stop-user-enumeration' ),
			'desc'          => esc_html__( 'WordPress allows anyone to find users by API call, by checking this box the calls will be restricted to logged in users only. Only untick this box if you need to allow unfettered API access to users', 'stop-user-enumeration' ),
			'id'            => 'stop_rest_user',
			'type'          => 'checkbox',
			'default'       => $this->set_default_checkbox( true ),
			'tab'           => 'settings',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		) );

		$cmb_options->add_field( array(
			'name'          => esc_html__( 'log attempts to AUTH LOG', 'stop-user-enumeration' ),
			'desc'          => sprintf( esc_html__( 'Leave this ticked if you are using %1$sFail2Ban%2$s on your VPS to block attempts at enumeration.%3$s If you are not running Fail2Ban or on a shared host this does not need to be ticked, however it normally will not cause a problem being ticked.'
				, 'stop-user-enumeration' ),
				'<a href="http://www.fail2ban.org/wiki/index.php/Main_Page" target="_blank">', '</a>', '<br>' ),
			'id'            => 'log_auth',
			'type'          => 'checkbox',
			'default'       => $this->set_default_checkbox( true ),
			'tab'           => 'settings',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		) );

		$cmb_options->add_field( array(
			'name'          => esc_html__( 'Remove numbers from comment authors', 'stop-user-enumeration' ),
			'desc'          => esc_html__( 'This plugin uses jQuery to remove any numbers from a comment author name, this is because numbers trigger enumeration checking. You can untick this if you do not use comments on your site or you use a different comment method than standard',
				'stop-user-enumeration' ),
			'id'            => 'comment_jquery',
			'type'          => 'checkbox',
			'default'       => $this->set_default_checkbox( true ),
			'tab'           => 'settings',
			'render_row_cb' => array( 'CMB2_Tabs', 'tabs_render_row_cb' ),
		) );

	}

	private function set_default_checkbox( $default ) {
		// only return true if the option has never been set
		return get_option( $this->options_key, false ) ? '' : ( $default ? (string) $default : '' );
	}


}