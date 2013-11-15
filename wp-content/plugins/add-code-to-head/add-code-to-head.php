<?php
/*
 * Plugin Name: Add Code to Head
 * Plugin URI: http://hbjitney.com/add-code-to-header.html
 * Description: Adds custom html code (javascript, css, etc.) to each public page's head
 * Version: 1.09
 * Author: HBJitney, LLC
 * Author URI: http://hbjitney.com/
 * License: GPL3

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !class_exists('AddCodeToHead' ) ) {
	/**
 	* Wrapper class to isolate us from the global space in order
 	* to prevent method collision
 	*/
	class AddCodeToHead {
		/**
		 * Set up all actions, instantiate other
		 */
		function __construct() {
			add_action( 'admin_menu', array( $this, 'add_admin' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'wp_head', array( $this, 'display' ) );
		}

		/**
		 * Add our options to the settings menu
		 */
		function add_admin() {
			add_options_page('Add Code to Head', 'Add Code to Head', 'manage_options', 'acth_plugin', array( $this, 'plugin_options_page' ) );
		}

		/**
		 * Callback for options page - set up page title and instantiate fields
		 */
		function plugin_options_page() {
?>
		<div class="plugin-options">
		 <h2><span>Add Code to Head</span></h2>
		 <form action="options.php" method="post">
<?php
		  settings_fields( 'acth_options' );
		  do_settings_sections( 'acth_plugin' );
?>

		  <input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
		 </form>
		</div>
<?php
		}

		/*
		 * Define options section (only one) and fields (also only one!)
		 */
		function admin_init() {
			register_setting( 'acth_options', 'acth_options', array( $this, 'options_validate' ) );
			add_settings_section( 'acth_section', '', array( $this, 'main_section' ), 'acth_plugin' );
			add_settings_field( 'acth_string', 'Code', array( $this, 'text_field'), 'acth_plugin', 'acth_section');
		}

		/*
		 * Static content for options section
		 */
		function main_section() {
			// GNDN
	    }

		/*
		 * Code for field
		 */
		function text_field() {
			$options = get_option( 'acth_options' );
?>
		        <textarea id="acth_options" name="acth_options[text_string]" rows="20" cols="90"><?php _e( $options['text_string'] );?></textarea>
<?php
		}

		/*
		 * No validation, just remove leading and trailing spaces
		 */
		function options_validate($input) {
			$newinput['text_string'] = trim( $input['text_string'] );
			return $newinput;
		}

		/*
		 * Display the code(s) on the public page.
		 * We do an extra check to ensure that the codes don't show up
		 * in the admin tool.
		 */
		function display() {
			if( !is_admin() ) {
				$options = get_option( 'acth_options' );
				_e( $options['text_string'] );
			}
		}
	}
}

/*
 * Sanity - was there a problem setting up the class? If so, bail with error
 * Otherwise, class is now defined; create a new one it to get the ball rolling.
 */
if( class_exists( 'AddCodeToHead' ) ) {
	new AddCodeToHead();
} else {
	$message = "<h2 style='color:red'>Error in plugin</h2>
	<p>Sorry about that! Plugin <span style='color:blue;font-family:monospace'>add-code-to-head</span> reports that it was unable to start.</p>
	<p><a href='mailto:support@hbjitney.com?subject=Add-code-to-head%20error&body=What version of Wordpress are you running? Please paste a list of your current active plugins here:'>Please report this error</a>.
	Meanwhile, here are some things you can try:</p>
	<ul><li>Make sure you are running the latest version of the plugin; update the plugin if not.</li>
	<li>There might be a conflict with other plugins. You can try disabling every other plugin; if the problem goes away, there is a conflict.</li>
	<li>Try a different theme to see if there's a conflict between the theme and the plugin.</li>
	</ul>";
	wp_die( $message );
}
?>
