<?php
// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'synved_connect_install_date' );
delete_option( 'synved_version' );

delete_option( 'synved_connect_id_default' );

delete_option( 'widget_synved_social_share' );
delete_option( 'widget_synved_social_follow' );

delete_option( 'synved_option_wp_upgrade_addon_transfer' );

delete_option( 'synved_social_settings' );