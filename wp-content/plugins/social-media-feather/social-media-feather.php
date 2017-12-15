<?php
/*
Plugin Name: Social Media Feather
Plugin URI: http://socialmediafeather.com/
Description: Super lightweight social media plugin to add nice and effective social media sharing and following buttons and icons anywhere on your site quickly and easily
Author: socialmediafeather
Version: 1.8.4
Author URI: http://socialmediafeather.com/
*/

define( 'SYNVED_VERSION', '1.8.4' );

if (!function_exists('synved_wp_social_load'))
{
	function synved_wp_social_load()
	{
		global $plugin;

		$path = __FILE__;

		if (defined('SYNVED_SOCIAL_INCLUDE_PATH'))
		{
			$path = SYNVED_SOCIAL_INCLUDE_PATH;
		}
		else if (isset($plugin))
		{
			/* This is mostly for symlink support */
			$real_plugin = realpath($plugin);

			if (strtolower($real_plugin) == strtolower(__FILE__))
			{
				$path = $plugin;
			}
		}

		$dir = dirname($path) . DIRECTORY_SEPARATOR;

		if (!function_exists('synved_plugout_module_import'))
		{
			include($dir . 'synved-plugout' . DIRECTORY_SEPARATOR . 'synved-plugout.php');
		}

		/* Register used modules */
		synved_plugout_module_register('synved-connect');
		synved_plugout_module_path_add('synved-connect', 'core', $dir . 'synved-connect');
		synved_plugout_module_register('synved-option');
		synved_plugout_module_path_add('synved-option', 'core', $dir . 'synved-option');
		synved_plugout_module_register('synved-social');
		synved_plugout_module_path_add('synved-social', 'core', $dir . 'synved-social');
		synved_plugout_module_path_add('synved-social', 'provider', __FILE__);

		/* Import modules */
		synved_plugout_module_import('synved-connect');
		synved_plugout_module_import('synved-option');
		synved_plugout_module_import('synved-social');
	}

	synved_wp_social_load();
}

synved_plugout_module_path_add('synved-connect', 'addon', dirname((defined('SYNVED_SOCIAL_INCLUDE_PATH') ? SYNVED_SOCIAL_INCLUDE_PATH : __FILE__)) . '/synved-connect/addons');
synved_plugout_module_path_add('synved-option', 'addon', dirname((defined('SYNVED_SOCIAL_INCLUDE_PATH') ? SYNVED_SOCIAL_INCLUDE_PATH : __FILE__)) . '/synved-option/addons');
synved_plugout_module_path_add('synved-social', 'addon', dirname((defined('SYNVED_SOCIAL_INCLUDE_PATH') ? SYNVED_SOCIAL_INCLUDE_PATH : __FILE__)) . '/synved-social/addons');

?>
