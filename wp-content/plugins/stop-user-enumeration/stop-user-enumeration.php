<?php
/*
Plugin Name: Stop User Enumeration
Plugin URI: https://fullworks.net/products/stop-user-enumeration/
Description: User enumeration is a technique used by hackers to get your login name if you are using permalinks. This plugin stops that.
Version: 1.3.14
Author: Fullworks Digital Ltd
Text Domain: stop-user-enumeration
Domain Path: /languages
Author URI: https://fullworks.net/
License: GPLv2 or later.
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
// don't allow running on less than php 5.3 but handle gracefully - no alerts
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
if (PHP_VERSION_ID >= 50300 ) {
	require_once(plugin_dir_path( __FILE__ ).'bootstrap.php');
}
