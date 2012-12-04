<?php
/*
Plugin Name: Private Only
Plugin URI: http://www.pixert.com/
Description: Redirects all non-logged in users to login form with custom login capability
Version: 3.0
Author: Kate Mag (Pixel Insert)
Author URI: http://www.pixert.com
*/
/*
Copyright 2009 Kate Mag-Pixel Insert (email:studio[at]pixert dot com)
This program is free softwate; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
Public License for more detail

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software Fondation, Inc.,
51 Franklin St, Fifth Floor, Boston, MA 02110-1301 US
*/
?>
<?php
/* Localization */
load_plugin_textdomain('private-only', false, basename( dirname( __FILE__ ) ) . '/languages' );
//login page - sweet!
//define constant folder to the plugin
$wp_content_dir = ABSPATH . 'wp-content';
$wp_plugin_dir = $wp_content_dir . '/plugins';
define( 'PO_LOGIN', $wp_plugin_dir . '/private-only' );
define( 'PO_LOGIN_URL', $wp_plugin_dir . '/private-only' );
define( 'PO_LOGIN_ADMIN', $wp_plugin_dir . '/private-only/admin' );
define( 'PO_LOGIN_CSS', $wp_plugin_dir . '/private-only/css' );
	
//load settings from database 
if ( is_admin() )
	require_once( PO_LOGIN_ADMIN . '/settings-admin.php' );
	$po_login = get_option( 'po_login_settings' );
  	function po_login_add_pages() {
		if ( function_exists( 'add_options_page' ) ) 
			$page=add_options_page( 'Private Only Custom Login', 'Private Only Custom Login', 'update_plugins' , 'privateonly.php', 'po_login_page');
  	}
  //main function
  function po_custom_login() {
	global $po_login;
		echo '<!-- Private Only -->' . "\n\n";
?>
<?php if (isset($po_login[ 'use_wp_logo' ]) && $po_login[ 'use_wp_logo' ] == true ) {} else { ?> 
<?php if (isset($po_login[ 'po_logo' ]) && !empty($po_login[ 'po_logo']) ) { ?>
<style>
#login h1 a{ background: url(<?php echo $po_login[ 'po_logo' ]; ?>) no-repeat top center !important; }
</style>
<?php } else { ?>
<style>
#login h1 a{ display: none !important; }
</style>
<?php } } ?>
<?php if (isset($po_login['use_custom_css'])) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo get_bloginfo('stylesheet_directory') ?>/custom.css" />
<?php } ?>
<?php
		echo '<!-- Private Only by Kate Mag - @link: http://pixert.com-->' . "\n\n";
  }
  function po_login_plugin_actions($links, $file ) {
 	  if( $file == 'private-only/privateonly.php' && function_exists( "admin_url" ) ) {
		  $settings_link = '<a href="' . admin_url( 'options-general.php?page=privateonly.php' ) . '">' . __('Private Only Custom Login') . '</a>';
		  array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
  }
  //add a settings page to menu
	add_action( 'admin_menu', 'po_login_add_pages' );
	add_action( 'login_head', 'po_custom_login' );
	//Add a settings page to the plugin menu
	add_filter( 'plugin_action_links', 'po_login_plugin_actions', 10, 2 );

//Main Private Only features
function private_only () {
	$settings = get_option( 'po_login_settings' );
	/* New Feature, code added by Ivan Ricotti. Thanks */
	if (!is_user_logged_in() && !is_feed() && isset($settings['public_pages']) && $settings['public_pages'] && is_page($settings['public_pages'])) {
		return;
	} 
	if (!is_user_logged_in() && !is_feed() && (!is_page($settings['public_pages']) || empty($settings['public_pages']))) {
		auth_redirect();
  } 
}
function no_index () {
	echo "<meta name='robots' content='noindex,nofollow' />\n";
}
/* Fix Login Page Message */
function custom_login_message() {
$settings = get_option( 'po_login_settings' );
$pagetitle = get_the_title($settings['public_pages']);
$pagelink = get_permalink($settings['public_pages']);
$message = '<p class="message">'.__('Only registered and logged in users are allowed to view this site. Please login now','private-only').'<br />';
if (isset($settings['public_pages']) && $settings['public_pages']) {
$message .= __('Visit our public page:','private-only') .' <a href='.$pagelink.'>'.$pagetitle.'</a></p>';
} else {
$message .= '</p>';	
}
return $message;
}
add_filter('login_message', 'custom_login_message');
function custom_register_message() {
$message ='<p class="message">'.printf(__('Welcome,you need to be registered to see content','private-only')).'</p><br />';
return $message;
}
add_filter('register_message', 'custom_register_message');
function log_in_message ($error) {
	global $error;
	$error="";
	return $error;
}
/* Fix Logo Title */;
function change_login_headertitle(){
	return get_bloginfo('title', 'display' );
}
add_filter( 'login_headertitle', 'change_login_headertitle');
add_action('template_redirect','private_only');
add_action('login_head','no_index');
add_filter('login_headertitle','log_in_message');
?>