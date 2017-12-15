<?php
/*
Module Name: Synved Connect
Description: Connect and sync components in a WordPress installation with a remote server
Author: Synved
Version: 1.0.4
Author URI: http://synved.com/
License: GPLv2

LEGAL STATEMENTS

NO WARRANTY
All products, support, services, information and software are provided "as is" without warranty of any kind, express or implied, including, but not limited to, the implied warranties of fitness for a particular purpose, and non-infringement.

NO LIABILITY
In no event shall Synved Ltd. be liable to you or any third party for any direct or indirect, special, incidental, or consequential damages in connection with or arising from errors, omissions, delays or other cause of action that may be attributed to your use of any product, support, services, information or software provided, including, but not limited to, lost profits or lost data, even if Synved Ltd. had been advised of the possibility of such damages.
*/

include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-connect-key.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-connect-component.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-connect-credit.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-connect-support.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-connect-sponsor.php');


define('SYNVED_CONNECT_LOADED', true);
define('SYNVED_CONNECT_VERSION', 100000004);
define('SYNVED_CONNECT_VERSION_STRING', '1.0.4');
define('SYNVED_WP_MODERN_VERSION', '4.1');


$synved_connect = array();


function synved_connect_version()
{
	return SYNVED_CONNECT_VERSION;
}

function synved_connect_version_string()
{
	return SYNVED_CONNECT_VERSION_STRING;
}

function synved_connect_object()
{
	global $synved_connect;
	
	return $synved_connect;
}

function synved_connect_server_get()
{
	global $synved_connect;
	
	if (isset($synved_connect['server']))
	{
		return $synved_connect['server'];
	}
	
	return null;
}

function synved_connect_server_set($server)
{
	global $synved_connect;
	
	$synved_connect['server'] = $server;
}

function synved_connect_path_uri($path = null)
{
	$uri = plugins_url('/synved-wp-connect') . '/synved-connect';
	
	if (function_exists('synved_plugout_module_uri_get'))
	{
		$mod_uri = synved_plugout_module_uri_get('synved-connect');
		
		if ($mod_uri != null)
		{
			$uri = $mod_uri;
		}
	}
	
	if ($path != null)
	{
		if (substr($uri, -1) != '/' && $path[0] != '/')
		{
			$uri .= '/';
		}
		
		$uri .= $path;
	}
	
	return $uri;
}

function synved_connect_id_get($component = null, $part = null)
{
	$option_key = null;
	
	if ($component != null)
	{
		$option_key = 'component_' . $component;
	}
	else
	{
		$option_key = 'default';
	}
	
	$id = get_option('synved_connect_id_' . $option_key);

	return $id;
}

function synved_connect_id_set($component = null, $sponsor_id)
{
	$option_key = null;
	
	if ($component != null)
	{
		$option_key = 'component_' . $component;
	}
	else
	{
		$option_key = 'default';
	}
	
	return update_option('synved_connect_id_' . $option_key, $sponsor_id);
}


function synved_connect_enqueue_scripts()
{
	$uri = synved_connect_path_uri();
	
	wp_register_style('synved-connect-admin', $uri . '/style/admin.css', false, '1.0');
	
	wp_enqueue_style('synved-connect-admin');

	if ( version_compare( get_bloginfo( 'version' ), SYNVED_WP_MODERN_VERSION, 'lt' ) ) {
		wp_register_style( 'synved-connect-old-wp-support-css', $uri . '/style/synved_old_wp_support.css', false,
			null, 'all' );
		wp_enqueue_style( 'synved-connect-old-wp-support-css' );
	}
}

function synved_connect_init() {
	$install_date = get_option( 'synved_connect_install_date' );

	// Fresh install.
	if ( ! $install_date ) {
		update_option( 'synved_connect_install_date', time() );
		update_option( 'synved_version', SYNVED_VERSION );
		synved_option_set( 'synved_social', 'accepted_sharethis_terms', false );

	}

	$version = get_option( 'synved_version' );
	if ( $version !== SYNVED_VERSION ) {
		synved_connect_upgrade( $version );
	}
}

/**
 * Upgrades the plugin.
 * @param string $version The current version.
 * @return void
 */
function synved_connect_upgrade( $version ) {
	// Show the ShareThis notice on version upgrade.
	synved_option_set( 'synved_social', 'accepted_sharethis_terms', false );
	synved_option_set( 'synved_social', 'hide_sharethis_terms', false );

	// Saves the new option in the DB.
	update_option( 'synved_version', SYNVED_VERSION );
}

add_action( 'init', 'synved_connect_init', 9 );
add_action( 'admin_enqueue_scripts', 'synved_connect_enqueue_scripts' );
