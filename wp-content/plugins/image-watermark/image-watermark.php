<?php
/*
Plugin Name: Image Watermark
Description: Image Watermark allows you to automatically watermark images uploaded to the WordPress Media Library and bulk watermark previously uploaded images.
Version: 1.5.1
Author: dFactory
Author URI: http://www.dfactory.eu/
Plugin URI: http://www.dfactory.eu/plugins/image-watermark/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Text Domain: image-watermark
Domain Path: /languages

Image Watermark
Copyright (C) 2013-2015, Digital Factory - info@digitalfactory.pl

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Image Watermark class.
 *
 * @class Image_Watermark
 * @version	1.5.1
 */
class Image_Watermark {

	private $is_admin = true;
	private $image_sizes = array();
	private $watermark_positions = array(
		'x'	 => array( 'left', 'center', 'right' ),
		'y'	 => array( 'top', 'middle', 'bottom' ),
	);
	private $allowed_mime_types = array(
		'image/jpeg',
		'image/pjpeg',
		'image/png'
	);
	protected $defaults = array(
		'options'	 => array(
			'watermark_on'		 => array(),
			'watermark_cpt_on'	 => array( 'everywhere' ),
			'watermark_image'	 => array(
				'url'					=> 0,
				'width'					=> 80,
				'plugin_off'			=> 0,
				'frontend_active'		=> false,
				'manual_watermarking'	=> 0,
				'position'				=> 'bottom_right',
				'watermark_size_type'	=> 2,
				'offset_width'			=> 0,
				'offset_height'			=> 0,
				'absolute_width'		=> 0,
				'absolute_height'		=> 0,
				'transparent'			=> 50,
				'quality'				=> 90,
				'jpeg_format'			=> 'baseline',
				'deactivation_delete'	=> false,
				'media_library_notice'	=> true
			),
			'image_protection'	 => array(
				'rightclick'	 => 0,
				'draganddrop'	 => 0,
				'forlogged'		 => 0,
			),
		),
		'version'	 => '1.5.1'
	);
	public $options = array();

	public function __construct() {
		// installer
		register_activation_hook( __FILE__, array( &$this, 'activate_watermark' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate_watermark' ) );

		// settings
		$this->options = array_merge( $this->defaults['options'], (array) get_option( 'image_watermark_options' ) );

		// actions
		add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ) );
		add_action( 'wp_loaded', array( &$this, 'load_image_sizes' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_print_scripts', array( &$this, 'admin_print_scripts' ), 20 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts' ) );
		add_action( 'admin_menu', array( &$this, 'options_page' ) );
		add_action( 'load-upload.php', array( &$this, 'apply_watermark_bulk_action' ) );
		add_action( 'admin_init', array( &$this, 'update_watermark' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_notices', array( &$this, 'bulk_admin_notices' ) );

		// filters
		add_filter( 'plugin_row_meta', array( &$this, 'plugin_extend_links' ), 10, 2 );
		add_filter( 'plugin_action_links', array( &$this, 'plugin_settings_link' ), 10, 2 );
		add_filter( 'wp_handle_upload', array( &$this, 'handle_upload_files' ) );
	}

	/**
	 * Plugin activation.
	 */
	public function activate_watermark() {
		add_option( 'image_watermark_options', $this->defaults['options'], '', 'no' );
		add_option( 'image_watermark_version', $this->defaults['version'], '', 'no' );
	}

	/**
	 * Plugin deactivation.
	 */
	public function deactivate_watermark() {
		// remove options from database?
		if ( $this->options['image_watermark_image']['deactivation_delete'] === true ) {
			delete_option( 'image_watermark_options' );
			delete_option( 'image_watermark_version' );
		}
	}

	/**
	 * Plugin update, fix for version < 1.5.0
	 */
	public function update_watermark() {
		if ( ! current_user_can( 'install_plugins' ) )
			return;

		$db_version = get_option( 'image_watermark_version' );
		$db_version = ! ( $db_version ) && ( get_option( 'df_watermark_installed' ) != false ) ? get_option( 'version' ) : $db_version;

		if ( $db_version != false ) {
			if ( version_compare( $db_version, '1.5.0', '<' ) ) {
				$options = array();

				$old_new = array(
					'df_watermark_on'			 => 'watermark_on',
					'df_watermark_cpt_on'		 => 'watermark_cpt_on',
					'df_watermark_image'		 => 'watermark_image',
					'df_image_protection'		 => 'image_protection',
					'df_watermark_installed'	 => '',
					'version'					 => '',
					'image_watermark_version'	 => '',
				);

				foreach ( $old_new as $old => $new ) {
					if ( $new ) {
						$options[$new] = get_option( $old );
					}
					delete_option( $old );
				}

				add_option( 'image_watermark_options', $options, '', 'no' );
				add_option( 'image_watermark_version', $this->defaults['version'], '', 'no' );
			}
		}
	}

	/**
	 * Load textdomain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'image-watermark', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Load available image sizes.
	 */
	public function load_image_sizes() {
		$this->image_sizes = get_intermediate_image_sizes();
		$this->image_sizes[] = 'full';

		sort( $this->image_sizes, SORT_STRING );
	}

	/**
	 * Get post types.
	 */
	private function get_post_types() {
		return array_merge( array( 'post', 'page' ), get_post_types( array( '_builtin' => false ), 'names' ) );
	}

	/**
	 * Admin inline scripts.
	 */
	public function admin_print_scripts() {
		global $pagenow;

		if ( $pagenow === 'upload.php' ) {
			if ( $this->options['watermark_image']['manual_watermarking'] == 1 ) {
				?>
				<script type="text/javascript">
					jQuery( function( $ ) {
						$( document ).ready( function() {
							$( "<option>" ).val( "applywatermark" ).text( "<?php _e( 'Apply watermark', 'image-watermark' ); ?>" ).appendTo( "select[name='action']" );
							$( "<option>" ).val( "applywatermark" ).text( "<?php _e( 'Apply watermark', 'image-watermark' ); ?>" ).appendTo( "select[name='action2']" );
						});
					});
				</script>
				<?php
			}
		}
	}

	/**
	 * Enqueue admin scripts and styles.
	 */
	public function admin_enqueue_scripts( $page ) {
		if ( $page === 'settings_page_watermark-options' ) {
			wp_enqueue_media();

			wp_register_script(
				'upload-manager', plugins_url( '/js/admin-upload.js', __FILE__ ), array(), $this->defaults['version']
			);

			wp_enqueue_script( 'upload-manager' );

			wp_localize_script(
				'upload-manager', 'upload_manager_args', array(
				'title'			 => __( 'Select watermark', 'image-watermark' ),
				'originalSize'	 => __( 'Original size', 'image-watermark' ),
				'noSelectedImg'	 => __( 'Watermak has not been selected yet.', 'image-watermark' ),
				'notAllowedImg'	 => __( 'This image is not supported as watermark. Use JPEG, PNG or GIF.', 'image-watermark' ),
				'frame'			 => 'select',
				'button'		 => array( 'text' => __( 'Add watermark', 'image-watermark' ) ),
				'multiple'		 => false,
				)
			);

			wp_register_script(
				'watermark-admin-script', plugins_url( 'js/admin-settings.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-button', 'jquery-ui-slider' ), $this->defaults['version']
			);

			wp_enqueue_script( 'watermark-admin-script' );

			wp_localize_script(
				'watermark-admin-script', 'iwArgs', array(
				'resetToDefaults' => __( 'Are you sure you want to reset settings to defaults?', 'image-watermark' )
				)
			);

			wp_register_style(
				'watermark-style', plugins_url( 'css/image-watermark.css', __FILE__ ), array(), $this->defaults['version']
			);

			wp_enqueue_style( 'watermark-style' );

			wp_register_style(
				'wp-like-ui-theme', plugins_url( 'css/wp-like-ui-theme.css', __FILE__ ), array(), $this->defaults['version']
			);

			wp_enqueue_style( 'wp-like-ui-theme' );
		}
	}

	/**
	 * Enqueue frontend script with 'no right click' and 'drag and drop' functions.
	 */
	public function wp_enqueue_scripts() {
		if ( ($this->options['image_protection']['forlogged'] == 0 && is_user_logged_in()) || ($this->options['image_protection']['draganddrop'] == 0 && $this->options['image_protection']['rightclick'] == 0) )
			return;

		wp_enqueue_script(
			'no-right-click', plugins_url( 'js/no-right-click.js', __FILE__ ), array(), $this->defaults['version']
		);

		wp_localize_script(
			'no-right-click', 'norightclick_args', array(
			'rightclick'	 => ($this->options['image_protection']['rightclick'] == 1 ? 'Y' : 'N'),
			'draganddrop'	 => ($this->options['image_protection']['draganddrop'] == 1 ? 'Y' : 'N')
			)
		);
	}

	/**
	 * Apply watermark everywhere or for specific post types.
	 * 
	 * @param	resource $file
	 * @return	resource
	 */
	public function handle_upload_files( $file ) {

		// admin, we cant use is_admin() here due to frontend's admin-ajax.php request
		if ( strpos( strtolower( wp_get_referer() ), strtolower( admin_url() ), 0 ) === 0 ) {

			$this->is_admin = true;

			// apply watermark if backend is active and watermark image is set
			if ( $this->options['watermark_image']['plugin_off'] == 1 && $this->options['watermark_image']['url'] != 0 && in_array( $file['type'], $this->allowed_mime_types ) ) {
				add_filter( 'wp_generate_attachment_metadata', array( &$this, 'apply_watermark' ), 10, 2 );
			}	
		} else {

			// frontend
			$this->is_admin = false;

			// apply watermark if frontend is active and watermark image is set
			if ( $this->options['watermark_image']['frontend_active'] == 1 && $this->options['watermark_image']['url'] != 0 && in_array( $file['type'], $this->allowed_mime_types ) ) {
				add_filter( 'wp_generate_attachment_metadata', array( &$this, 'apply_watermark' ), 10, 2 );
			}
		}

		return $file;
	}

	/**
	 * Apply watermark for selected images on media page.
	 */
	public function apply_watermark_bulk_action() {
		global $pagenow;

		if ( $pagenow == 'upload.php' ) {

			$wp_list_table = _get_list_table( 'WP_Media_List_Table' );

			// only if manual watermarking is turned on and image watermark is set
			if ( $wp_list_table->current_action() === 'applywatermark' && $this->options['watermark_image']['manual_watermarking'] == 1 && $this->options['watermark_image']['url'] != 0 ) {
				// security check
				check_admin_referer( 'bulk-media' );

				$location = esc_url( remove_query_arg( array( 'watermarked', 'skipped', 'trashed', 'untrashed', 'deleted', 'message', 'ids', 'posted' ), wp_get_referer() ) );

				if ( ! $location ) {
					$location = 'upload.php';
				}

				$location = esc_url( add_query_arg( 'paged', $wp_list_table->get_pagenum(), $location ) );
				
				// make sure ids are submitted.  depending on the resource type, this may be 'media' or 'ids'
			    if ( isset( $_REQUEST['media'] ) ) {
			      $post_ids = array_map( 'intval', $_REQUEST['media'] );
			    }

				// do we have selected attachments?
				if ( $post_ids ) {
					$watermarked = $skipped = 0;

					foreach ( $post_ids as $post_id ) {
						$data = wp_get_attachment_metadata( $post_id, false );

						// is this really an image?
						if ( in_array( get_post_mime_type( $post_id ), $this->allowed_mime_types ) && is_array( $data ) ) {
							$this->apply_watermark( $data, 'manual' );
							$watermarked ++;
						} else
							$skipped ++;
					}

					$location = esc_url( add_query_arg( array( 'watermarked' => $watermarked, 'skipped' => $skipped ), $location ), null, '' );
				}

				wp_redirect( $location );
				exit();
			} else
				return;
		}
	}

	/**
	 * Display admin notices.
	 */
	public function bulk_admin_notices() {
		global $post_type, $pagenow;

		if ( $pagenow === 'upload.php' ) {
			
			if ( ! current_user_can( 'upload_files' ) )
				return;
			
			// hide media library notice
			if ( isset( $_GET['iw_action'] ) && $_GET['iw_action'] == 'hide_library_notice' ) {
				$this->options['watermark_image']['media_library_notice'] = false;
				update_option( 'image_watermark_options', $this->options );
			}
			
			// check if manual watermarking is enabled
			if ( ! empty( $this->options['watermark_image']['manual_watermarking'] ) && ( ! isset( $this->options['watermark_image']['media_library_notice']) || $this->options['watermark_image']['media_library_notice'] === true ) ) {
				$mode = get_user_option( 'media_library_mode', get_current_user_id() ) ? get_user_option( 'media_library_mode', get_current_user_id() ) : 'grid';
				
				if ( isset( $_GET['mode'] ) && in_array( $_GET['mode'], array( 'grid', 'list' ) ) ) {
					$mode = $_GET['mode'];
				}
				
				// display notice in grid mode only
				if ( $mode === 'grid' ) {
					// get current admin url
					$query_string = array();
					parse_str( $_SERVER['QUERY_STRING'], $query_string );
					$current_url = esc_url( add_query_arg( array_merge( (array) $query_string, array( 'iw_action' => 'hide_library_notice' ) ), '', admin_url( trailingslashit( $pagenow ) ) ) );

					echo '<div class="error notice"><p>' . sprintf( __( '<strong>Image Watermark:</strong> Bulk watermarking is available in list mode only, under <em>Bulk Actions</em> dropdown. <a href="%1$s">Got to List Mode</a> or <a href="%2$s">Hide this notice</a>', 'image-watermark' ), esc_url( admin_url( 'upload.php?mode=list' ) ), esc_url( $current_url ) ) . '</p></div>';
				}
			}
			
			if ( isset( $_REQUEST['watermarked'], $_REQUEST['skipped'] ) && $post_type === 'attachment' ) {
				$watermarked = (int) $_REQUEST['watermarked'];
				$skipped = (int) $_REQUEST['skipped'];
	
				if ( $watermarked === 0 ) {
					echo '<div class="error"><p>' . __( 'Watermark could not be applied to selected files or no valid images (JPEG, PNG) were selected.', 'image-watermark' ) . ($skipped > 0 ? ' ' . __( 'Images skipped', 'image-watermark' ) . ': ' . $skipped . '.' : '') . '</p></div>';
				} else {
					echo '<div class="updated"><p>' . sprintf( _n( 'Watermark was succesfully applied to 1 image.', 'Watermark was succesfully applied to %s images.', $watermarked, 'image-watermark' ), number_format_i18n( $watermarked ) ) . ($skipped > 0 ? ' ' . __( 'Skipped files', 'image-watermark' ) . ': ' . $skipped . '.' : '') . '</p></div>';
				}
	
				$_SERVER['REQUEST_URI'] = esc_url( remove_query_arg( array( 'watermarked', 'skipped' ), $_SERVER['REQUEST_URI'] ) );
			}
		}
	}

	/**
	 * Create options page in menu.
	 */
	public function options_page() {
		add_options_page(
			__( 'Image Watermark Options', 'image-watermark' ), __( 'Watermark', 'image-watermark' ), 'manage_options', 'watermark-options', array( &$this, 'options_page_output' )
		);
	}

	/**
	 * Options page output.
	 */
	public function options_page_output() {

		if ( ! current_user_can( 'manage_options' ) )
			return;

		echo '
		<div class="wrap">
			<h2>' . __( 'Image Watermark', 'image-watermark' ) . '</h2>';

		echo '
			<div class="image-watermark-settings">
				<div class="df-sidebar">
					<div class="df-credits">
						<h3 class="hndle">' . __( 'Image Watermark', 'image-watermark' ) . ' ' . $this->defaults['version'] . '</h3>
						<div class="inside">
							<h4 class="inner">' . __( 'Need support?', 'image-watermark' ) . '</h4>
							<p class="inner">' . __( 'If you are having problems with this plugin, checkout plugin', 'image-watermark' ) . '  <a href="http://www.dfactory.eu/docs/image-watermark-plugin/?utm_source=image-watermark-settings&utm_medium=link&utm_campaign=documentation" target="_blank" title="' . __( 'Documentation', 'image-watermark' ) . '">' . __( 'Documentation', 'image-watermark' ) . '</a> ' . __( 'or talk about them in the', 'image-watermark' ) . ' <a href="http://www.dfactory.eu/support/?utm_source=image-watermark-settings&utm_medium=link&utm_campaign=support" target="_blank" title="' . __( 'Support forum', 'image-watermark' ) . '">' . __( 'Support forum', 'image-watermark' ) . '</a></p>
							<hr />
							<h4 class="inner">' . __( 'Do you like this plugin?', 'image-watermark' ) . '</h4>
							<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" class="inner">
								<input type="hidden" name="cmd" value="_s-xclick">
								<input type="hidden" name="hosted_button_id" value="DCF3AXC9A5A88">
								<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
								<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
							</form>
							<p class="inner"><a href="http://wordpress.org/support/view/plugin-reviews/image-watermark" target="_blank" title="' . __( 'Rate it 5', 'image-watermark' ) . '">' . __( 'Rate it 5', 'image-watermark' ) . '</a> ' . __( 'on WordPress.org', 'image-watermark' ) . '<br />' .
		__( 'Blog about it & link to the', 'image-watermark' ) . ' <a href="http://www.dfactory.eu/plugins/image-watermark/?utm_source=image-watermark-settings&utm_medium=link&utm_campaign=blog-about" target="_blank" title="' . __( 'plugin page', 'image-watermark' ) . '">' . __( 'plugin page', 'image-watermark' ) . '</a><br />' .
		__( 'Check out our other', 'image-watermark' ) . ' <a href="http://www.dfactory.eu/plugins/?utm_source=image-watermark-settings&utm_medium=link&utm_campaign=other-plugins" target="_blank" title="' . __( 'WordPress plugins', 'image-watermark' ) . '">' . __( 'WordPress plugins', 'image-watermark' ) . '</a>
							</p>     
							<hr />
							<p class="df-link inner">' . __( 'Created by', 'image-watermark' ) . ' <a href="http://www.dfactory.eu/?utm_source=image-watermark-settings&utm_medium=link&utm_campaign=created-by" target="_blank" title="dFactory - Quality plugins for WordPress"><img src="' . EVENTS_MAKER_URL . '/images/logo-dfactory.png' . '" title="dFactory - Quality plugins for WordPress" alt="dFactory - Quality plugins for WordPress" /></a></p>
						</div>
					</div>
				<form action="options.php" method="post">';

		settings_fields( 'image_watermark_options' );
		do_settings_sections( 'image_watermark_options' );

		echo '
					<p class="submit">';
		submit_button( '', 'primary', 'save_image_watermark_options', false );
		echo ' ';
		submit_button( __( 'Reset to defaults', 'image-watermark' ), 'secondary', 'reset_image_watermark_options', false );

		echo '		</p>
				</form>
			</div>
			<div class="clear"></div>
		</div>';
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		register_setting( 'image_watermark_options', 'image_watermark_options', array( $this, 'validate_options' ) );

		add_settings_section( 'image_watermark_general', __( 'General settings', 'image-watermark' ), '', 'image_watermark_options' );
		add_settings_field( 'iw_automatic_watermarking', __( 'Automatic watermarking', 'image-watermark' ), array( $this, 'iw_automatic_watermarking' ), 'image_watermark_options', 'image_watermark_general' );
		add_settings_field( 'iw_manual_watermarking', __( 'Manual watermarking', 'image-watermark' ), array( $this, 'iw_manual_watermarking' ), 'image_watermark_options', 'image_watermark_general' );
		add_settings_field( 'iw_enable_for', __( 'Enable watermark for', 'image-watermark' ), array( $this, 'iw_enable_for' ), 'image_watermark_options', 'image_watermark_general' );
		add_settings_field( 'iw_frontend_watermarking', __( 'Frontend watermarking', 'image-watermark' ), array( $this, 'iw_frontend_watermarking' ), 'image_watermark_options', 'image_watermark_general' );
		add_settings_field( 'iw_deactivation', __( 'Deactivation', 'image-watermark' ), array( $this, 'iw_deactivation' ), 'image_watermark_options', 'image_watermark_general' );

		add_settings_section( 'image_watermark_position', __( 'Watermark position', 'image-watermark' ), '', 'image_watermark_options' );
		add_settings_field( 'iw_alignment', __( 'Watermark alignment', 'image-watermark' ), array( $this, 'iw_alignment' ), 'image_watermark_options', 'image_watermark_position' );
		add_settings_field( 'iw_offset', __( 'Watermark offset', 'image-watermark' ), array( $this, 'iw_offset' ), 'image_watermark_options', 'image_watermark_position' );

		add_settings_section( 'image_watermark_image', __( 'Watermark image', 'image-watermark' ), '', 'image_watermark_options' );
		add_settings_field( 'iw_watermark_image', __( 'Watermark image', 'image-watermark' ), array( $this, 'iw_watermark_image' ), 'image_watermark_options', 'image_watermark_image' );
		add_settings_field( 'iw_watermark_preview', __( 'Watermark preview', 'image-watermark' ), array( $this, 'iw_watermark_preview' ), 'image_watermark_options', 'image_watermark_image' );
		add_settings_field( 'iw_watermark_size', __( 'Watermark size', 'image-watermark' ), array( $this, 'iw_watermark_size' ), 'image_watermark_options', 'image_watermark_image' );
		add_settings_field( 'iw_watermark_size_custom', __( 'Watermark custom size', 'image-watermark' ), array( $this, 'iw_watermark_size_custom' ), 'image_watermark_options', 'image_watermark_image' );
		add_settings_field( 'iw_watermark_size_scaled', __( 'Scale of watermark in relation to image width', 'image-watermark' ), array( $this, 'iw_watermark_size_scaled' ), 'image_watermark_options', 'image_watermark_image' );
		add_settings_field( 'iw_watermark_opacity', __( 'Watermark transparency / opacity', 'image-watermark' ), array( $this, 'iw_watermark_opacity' ), 'image_watermark_options', 'image_watermark_image' );
		add_settings_field( 'iw_image_quality', __( 'Image quality', 'image-watermark' ), array( $this, 'iw_image_quality' ), 'image_watermark_options', 'image_watermark_image' );
		add_settings_field( 'iw_image_format', __( 'Image format', 'image-watermark' ), array( $this, 'iw_image_format' ), 'image_watermark_options', 'image_watermark_image' );

		add_settings_section( 'image_watermark_protection', __( 'Image protection', 'image-watermark' ), '', 'image_watermark_options' );
		add_settings_field( 'iw_protection_right_click', __( 'Right click', 'image-watermark' ), array( $this, 'iw_protection_right_click' ), 'image_watermark_options', 'image_watermark_protection' );
		add_settings_field( 'iw_protection_drag_drop', __( 'Drag and drop', 'image-watermark' ), array( $this, 'iw_protection_drag_drop' ), 'image_watermark_options', 'image_watermark_protection' );
		add_settings_field( 'iw_protection_logged', __( 'Logged-in users', 'image-watermark' ), array( $this, 'iw_protection_logged' ), 'image_watermark_options', 'image_watermark_protection' );
	}

	/**
	 * Automatic watermarking option.
	 */
	public function iw_automatic_watermarking() {
		?>
		<label for="iw_automatic_watermarking">
			<input id="iw_automatic_watermarking" type="checkbox" <?php checked( ( ! empty( $this->options['watermark_image']['plugin_off'] ) ? 1 : 0 ), 1, true ); ?> value="1" name="iw_options[watermark_image][plugin_off]">
<?php echo __( 'Enable watermark for uploaded images.', 'image-watermark' ); ?>
		</label>
		<?php
	}

	/**
	 * Manual watermarking option.
	 */
	public function iw_manual_watermarking() {
		?>
		<label for="iw_manual_watermarking">
			<input id="iw_manual_watermarking" type="checkbox" <?php checked( ( ! empty( $this->options['watermark_image']['manual_watermarking'] ) ? 1 : 0 ), 1, true ); ?> value="1" name="iw_options[watermark_image][manual_watermarking]">
<?php echo __( 'Enable Apply Watermark option for images in Media Library.', 'image-watermark' ); ?>
		</label>
		<?php
	}

	/**
	 * Enable watermark for option.
	 */
	public function iw_enable_for() {
		?>
		<fieldset id="iw_enable_for">
			<div id="thumbnail-select">
				<?php
				foreach ( $this->image_sizes as $image_size ) {
					?>
					<input name="iw_options[watermark_on][<?php echo $image_size; ?>]" type="checkbox" id="<?php echo $image_size; ?>" value="1" <?php echo (in_array( $image_size, array_keys( $this->options['watermark_on'] ) ) ? ' checked="checked"' : ''); ?> />
					<label for="<?php echo $image_size; ?>"><?php echo $image_size; ?></label>
					<?php
				}
				?>
			</div>
			<p class="description">
<?php echo __( 'Check image sizes on which watermark should appear.<br /><strong>IMPORTANT:</strong> checking full size is NOT recommended as it\'s the original image. You may need it later - for removing or changing watermark, image sizes regeneration or any other image manipulations. Use it only if you know what you are doing.', 'image-watermark' ); ?>
			</p>
			
			<?php
			$watermark_cpt_on = array_keys( $this->options['watermark_cpt_on'] );

			if ( in_array( 'everywhere', $watermark_cpt_on ) && count( $watermark_cpt_on ) === 1 ) {
				$first_checked = true;
				$second_checked = false;
				$watermark_cpt_on = array();
			} else {
				$first_checked = false;
				$second_checked = true;
			}
			?>
			
			<div id="cpt-specific">
				<input id="df_option_everywhere" type="radio" name="iw_options[watermark_cpt_on]" value="everywhere" <?php echo ($first_checked === true ? 'checked="checked"' : ''); ?>/><label for="df_option_everywhere"><?php _e( 'everywhere', 'image-watermark' ); ?></label>
				<input id="df_option_cpt" type="radio" name="iw_options[watermark_cpt_on]" value="specific" <?php echo ($second_checked === true ? 'checked="checked"' : ''); ?> /><label for="df_option_cpt"><?php _e( 'on selected post types only', 'image-watermark' ); ?></label>
			</div>
			
			<div id="cpt-select" <?php echo ($second_checked === false ? 'style="display: none;"' : ''); ?>>
			<?php
			foreach ( $this->get_post_types() as $cpt ) {
				?>
				<input name="iw_options[watermark_cpt_on_type][<?php echo $cpt; ?>]" type="checkbox" id="<?php echo $cpt; ?>" value="1" <?php echo (in_array( $cpt, $watermark_cpt_on ) ? ' checked="checked"' : ''); ?> />
				<label for="<?php echo $cpt; ?>"><?php echo $cpt; ?></label>
				<?php
			}
				?>
			</div>
			
			<p class="description"><?php echo __( 'Check custom post types on which watermark should be applied to uploaded images.', 'image-watermark' ); ?></p>
		</fieldset>
		<?php
	}

	/**
	 * Frontend watermarking option.
	 */
	public function iw_frontend_watermarking() {
		?>
		<label for="iw_frontend_watermarking">
			<input id="iw_frontend_watermarking" type="checkbox" <?php checked( ( ! empty( $this->options['watermark_image']['frontend_active'] ) ? 1 : 0 ), 1, true ); ?> value="1" name="iw_options[watermark_image][frontend_active]">
<?php echo __( 'Enable frontend image uploading. (uploading script is not included, but you may use a plugin or custom code).', 'image-watermark' ); ?>
		</label>
		<span class="description"><?php echo __( '<br /><strong>Notice:</strong> This functionality works only if uploaded images are processed using WordPress native upload methods.', 'image-watermark' ); ?></span>
		<?php
	}

	/**
	 * Remove data on deactivation option.
	 */
	public function iw_deactivation() {
		?>
		<label for="iw_deactivation">
			<input id="iw_deactivation" type="checkbox" <?php checked( ( ! empty( $this->options['watermark_image']['deactivation_delete'] ) ? 1 : 0 ), 1, true ); ?> value="1" name="iw_options[watermark_image][deactivation_delete]">
<?php echo __( 'Delete all database settings on plugin deactivation.', 'image-watermark' ); ?>
		</label>
		<?php
	}

	/**
	 * Watermark alignment option.
	 */
	public function iw_alignment() {
		?>
		<fieldset id="iw_alignment">
			<table id="watermark_position" border="1">
			<?php
			$watermark_position = $this->options['watermark_image']['position'];

			foreach ( $this->watermark_positions['y'] as $y ) {
			?>
				<tr>
				<?php
				foreach ( $this->watermark_positions['x'] as $x ) {
				?>
					<td title="<?php echo ucfirst( $y . ' ' . $x ); ?>">
						<input name="iw_options[watermark_image][position]" type="radio" value="<?php echo $y . '_' . $x; ?>"<?php echo ($watermark_position == $y . '_' . $x ? ' checked="checked"' : NULL); ?> />
					</td>
					<?php }
					?>
				</tr>
				<?php
			}
		?>
			</table>
			<p class="description"><?php echo __( 'Choose the position of watermark image.', 'image-watermark' ); ?></p>
		</fieldset>
		<?php
	}

	/**
	 * Watermark offset option.
	 */
	public function iw_offset() {
		?>
		<fieldset id="iw_offset">
			<?php echo __( 'x:', 'image-watermark' ); ?> <input type="text" size="5"  name="iw_options[watermark_image][offset_width]" value="<?php echo $this->options['watermark_image']['offset_width']; ?>"> <?php echo __( 'px', 'image-watermark' ); ?>
			<br />
			<?php echo __( 'y:', 'image-watermark' ); ?> <input type="text" size="5"  name="iw_options[watermark_image][offset_height]" value="<?php echo $this->options['watermark_image']['offset_height']; ?>"> <?php echo __( 'px', 'image-watermark' ); ?>
		</fieldset>
		<?php
	}

	/**
	 * Watermark image option.
	 */
	public function iw_watermark_image() {
		if ( $this->options['watermark_image']['url'] !== NULL && $this->options['watermark_image']['url'] != 0 ) {
			$image = wp_get_attachment_image_src( $this->options['watermark_image']['url'], array( 300, 300 ), false );
			$image_selected = true;
		} else {
			$image_selected = false;
		}
		?>
		<div class="iw_watermark_image">
			<input id="upload_image" type="hidden" name="iw_options[watermark_image][url]" value="<?php echo (int) $this->options['watermark_image']['url']; ?>" />
			<input id="upload_image_button" type="button" class="button button-secondary" value="<?php echo __( 'Select image', 'image-watermark' ); ?>" />
			<input id="turn_off_image_button" type="button" class="button button-secondary" value="<?php echo __( 'Remove image', 'image-watermark' ); ?>" <?php if ( $image_selected === false ) echo 'disabled="disabled"'; ?>/>
			<p class="description"><?php _e( 'You have to save changes after the selection or removal of the image.', 'image-watermark' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Watermark image preview.
	 */
	public function iw_watermark_preview() {
		if ( $this->options['watermark_image']['url'] !== NULL && $this->options['watermark_image']['url'] != 0 ) {
			$image = wp_get_attachment_image_src( $this->options['watermark_image']['url'], array( 300, 300 ), false );
			$image_selected = true;
		} else {
			$image_selected = false;
		}
		?>
		<fieldset id="iw_watermark_preview">
			<div id="previewImg_imageDiv">
			<?php
				if ( $image_selected === true ) {
					$image = wp_get_attachment_image_src( $this->options['watermark_image']['url'], array( 300, 300 ), false );
					?>
					<img id="previewImg_image" src="<?php echo $image[0]; ?>" alt="" width="300" />
				<?php } else { ?>
					<img id="previewImg_image" src="" alt="" width="300" style="display: none;" />
				<?php }
			?>
			</div>
			<p id="previewImageInfo" class="description">
			<?php
			if ( $image_selected === false ) {
				_e( 'Watermak has not been selected yet.', 'image-watermark' );
			} else {
				$image_full_size = wp_get_attachment_image_src( $this->options['watermark_image']['url'], 'full', false );

				_e( 'Original size', 'image-watermark' ) . ': ' . $image_full_size[1] . ' px / ' . $image_full_size[2] . ' px';
			}
		?>
			</p>
		</fieldset>
		<?php
	}

	/**
	 * Watermark size option.
	 */
	public function iw_watermark_size() {
		?>
		<fieldset id="iw_watermark_size">
			<div id="watermark-type">
				<label for="type1"><?php _e( 'original', 'image-watermark' ); ?></label>
				<input type="radio" id="type1" value="0" name="iw_options[watermark_image][watermark_size_type]" <?php checked( $this->options['watermark_image']['watermark_size_type'], 0, true ); ?> />
				<label for="type2"><?php _e( 'custom', 'image-watermark' ); ?></label>
				<input type="radio" id="type2" value="1" name="iw_options[watermark_image][watermark_size_type]" <?php checked( $this->options['watermark_image']['watermark_size_type'], 1, true ); ?> />
				<label for="type3"><?php _e( 'scaled', 'image-watermark' ); ?></label>
				<input type="radio" id="type3" value="2" name="iw_options[watermark_image][watermark_size_type]" <?php checked( $this->options['watermark_image']['watermark_size_type'], 2, true ); ?> />
			</div>
			<p class="description"><?php _e( 'Select method of aplying watermark size.', 'image-watermark' ); ?></p>
		</fieldset>
		<?php
	}

	/**
	 * Watermark custom size option.
	 */
	public function iw_watermark_size_custom() {
		?>
		<fieldset id="iw_watermark_size_custom">
			<?php _e( 'x:', 'image-watermark' ); ?> <input type="text" size="5"  name="iw_options[watermark_image][absolute_width]" value="<?php echo $this->options['watermark_image']['absolute_width']; ?>"> <?php _e( 'px', 'image-watermark' ); ?>
			<br />
			<?php _e( 'y:', 'image-watermark' ); ?> <input type="text" size="5"  name="iw_options[watermark_image][absolute_height]" value="<?php echo $this->options['watermark_image']['absolute_height']; ?>"> <?php _e( 'px', 'image-watermark' ); ?>
		</fieldset>
		<p class="description"><?php _e( 'Those dimensions will be used if "custom" method is selected above.', 'image-watermark' ); ?></p>
		<?php
	}

	/**
	 * Watermark scaled size option.
	 */
	public function iw_watermark_size_scaled() {
		?>
		<fieldset id="iw_watermark_size_scaled">
			<div>
				<input type="text" id="iw_size_input" maxlength="3" class="hide-if-js" name="iw_options[watermark_image][width]" value="<?php echo $this->options['watermark_image']['width']; ?>" />
				<div class="wplike-slider">
					<span class="left hide-if-no-js">0</span><span class="middle" id="iw_size_span" title="<?php echo $this->options['watermark_image']['width']; ?>"></span><span class="right hide-if-no-js">100</span>
				</div>
			</div>
		</fieldset>
		<p class="description"><?php _e( 'This value will be used if "scaled" method if selected above. <br />Enter a number ranging from 0 to 100. 100 makes width of watermark image equal to width of the image it is applied to.', 'image-watermark' ); ?></p>
		<?php
	}

	/**
	 * Watermark custom size option.
	 */
	public function iw_watermark_opacity() {
		?>
		<fieldset id="iw_watermark_opacity">
			<div>
				<input type="text" id="iw_opacity_input" maxlength="3" class="hide-if-js" name="iw_options[watermark_image][transparent]" value="<?php echo $this->options['watermark_image']['transparent']; ?>" />
				<div class="wplike-slider">
					<span class="left hide-if-no-js">0</span><span class="middle" id="iw_opacity_span" title="<?php echo $this->options['watermark_image']['transparent']; ?>"></span><span class="right hide-if-no-js">100</span>
				</div>
			</div>
		</fieldset>
		<p class="description"><?php _e( 'Enter a number ranging from 0 to 100. 0 makes watermark image completely transparent, 100 shows it as is.', 'image-watermark' ); ?></p>
		<?php
	}

	/**
	 * Image quality option.
	 */
	public function iw_image_quality() {
		?>
		<fieldset id="iw_image_quality">
			<div>
				<input type="text" id="iw_quality_input" maxlength="3" class="hide-if-js" name="iw_options[watermark_image][quality]" value="<?php echo $this->options['watermark_image']['quality']; ?>" />
				<div class="wplike-slider">
					<span class="left hide-if-no-js">0</span><span class="middle" id="iw_quality_span" title="<?php echo $this->options['watermark_image']['quality']; ?>"></span><span class="right hide-if-no-js">100</span>
				</div>
			</div>
		</fieldset>
		<p class="description"><?php _e( 'Set output image quality.', 'image-watermark' ); ?></p>
		<?php
	}

	/**
	 * Image format option.
	 */
	public function iw_image_format() {
		?>
		<fieldset id="iw_image_format">
			<div id="jpeg-format">
				<label for="baseline"><?php _e( 'baseline', 'image-watermark' ); ?></label>
				<input type="radio" id="baseline" value="baseline" name="iw_options[watermark_image][jpeg_format]" <?php checked( $this->options['watermark_image']['jpeg_format'], 'baseline', true ); ?> />
				<label for="progressive"><?php _e( 'progressive', 'image-watermark' ); ?></label>
				<input type="radio" id="progressive" value="progressive" name="iw_options[watermark_image][jpeg_format]" <?php checked( $this->options['watermark_image']['jpeg_format'], 'progressive', true ); ?> />
			</div>
		</fieldset>
		<p class="description"><?php _e( 'Select baseline or progressive image format.', 'image-watermark' ); ?></p>
		<?php
	}

	/**
	 * Right click image protection option.
	 */
	public function iw_protection_right_click() {
		?>
		<label for="iw_protection_right_click">
			<input id="iw_protection_right_click" type="checkbox" <?php checked( ( ! empty( $this->options['image_protection']['rightclick'] ) ? 1 : 0 ), 1, true ); ?> value="1" name="iw_options[image_protection][rightclick]">
<?php _e( 'Disable right mouse click on images', 'image-watermark' ); ?>
		</label>
		<?php
	}

	/**
	 * Drag and drop image protection option.
	 */
	public function iw_protection_drag_drop() {
		?>
		<label for="iw_protection_drag_drop">
			<input id="iw_protection_drag_drop" type="checkbox" <?php checked( ( ! empty( $this->options['image_protection']['draganddrop'] ) ? 1 : 0 ), 1, true ); ?> value="1" name="iw_options[image_protection][draganddrop]">
<?php _e( 'Prevent drag and drop', 'image-watermark' ); ?>
		</label>
		<?php
	}

	/**
	 * Logged-in users image protection option.
	 */
	public function iw_protection_logged() {
		?>
		<label for="iw_protection_logged">
			<input id="iw_protection_logged" type="checkbox" <?php checked( ( ! empty( $this->options['image_protection']['forlogged'] ) ? 1 : 0 ), 1, true ); ?> value="1" name="iw_options[image_protection][forlogged]">
<?php _e( 'Enable image protection for logged-in users also', 'image-watermark' ); ?>
		</label>
		<?php
	}

	/**
	 * Validate options.
	 * 
	 * @param 	array $input
	 * @return 	array
	 */
	public function validate_options( $input ) {

		if ( ! current_user_can( 'manage_options' ) )
			return $input;

		if ( isset( $_POST['save_image_watermark_options'] ) ) {

			$input['watermark_image']['plugin_off'] = isset( $_POST['iw_options']['watermark_image']['plugin_off'] ) ? ((bool) $_POST['iw_options']['watermark_image']['plugin_off'] == 1 ? true : false) : $this->defaults['options']['watermark_image']['plugin_off'];
			$input['watermark_image']['manual_watermarking'] = isset( $_POST['iw_options']['watermark_image']['manual_watermarking'] ) ? ((bool) $_POST['iw_options']['watermark_image']['manual_watermarking'] == 1 ? true : false) : $this->defaults['options']['watermark_image']['manual_watermarking'];

			$watermark_on = array();

			if ( isset( $_POST['iw_options']['watermark_on'] ) && is_array( $_POST['iw_options']['watermark_on'] ) ) {
				foreach ( $this->image_sizes as $size ) {
					if ( in_array( $size, array_keys( $_POST['iw_options']['watermark_on'] ) ) ) {
						$watermark_on[$size] = 1;
					}
				}
			}
			$input['watermark_on'] = $watermark_on;

			$input['watermark_cpt_on'] = $this->defaults['options']['watermark_cpt_on'];

			if ( isset( $_POST['iw_options']['watermark_cpt_on'] ) && in_array( esc_attr( $_POST['iw_options']['watermark_cpt_on'] ), array( 'everywhere', 'specific' ) ) ) {
				if ( $_POST['iw_options']['watermark_cpt_on'] === 'specific' ) {
					if ( isset( $_POST['iw_options']['watermark_cpt_on_type'] ) ) {
						$tmp = array();

						foreach ( $this->get_post_types() as $cpt ) {
							if ( in_array( $cpt, array_keys( $_POST['iw_options']['watermark_cpt_on_type'] ) ) ) {
								$tmp[$cpt] = 1;
							}
						}

						if ( count( $tmp ) > 0 ) {
							$input['watermark_cpt_on'] = $tmp;
						}
					}
				}
			}

			$input['watermark_image']['frontend_active'] = isset( $_POST['iw_options']['watermark_image']['frontend_active'] ) ? ((bool) $_POST['iw_options']['watermark_image']['frontend_active'] == 1 ? true : false) : $this->defaults['options']['watermark_image']['frontend_active'];
			$input['watermark_image']['deactivation_delete'] = isset( $_POST['iw_options']['watermark_image']['deactivation_delete'] ) ? ((bool) $_POST['iw_options']['watermark_image']['deactivation_delete'] == 1 ? true : false) : $this->defaults['options']['watermark_image']['deactivation_delete'];

			$positions = array();

			foreach ( $this->watermark_positions['y'] as $position_y ) {
				foreach ( $this->watermark_positions['x'] as $position_x ) {
					$positions[] = $position_y . '_' . $position_x;
				}
			}
			$input['watermark_image']['position'] = isset( $_POST['iw_options']['watermark_image']['position'] ) && in_array( esc_attr( $_POST['iw_options']['watermark_image']['position'] ), $positions ) ? esc_attr( $_POST['iw_options']['watermark_image']['position'] ) : $this->defaults['options']['watermark_image']['position'];

			$input['watermark_image']['offset_width'] = isset( $_POST['iw_options']['watermark_image']['offset_width'] ) ? (int) $_POST['iw_options']['watermark_image']['offset_width'] : $this->defaults['options']['watermark_image']['offset_width'];
			$input['watermark_image']['offset_height'] = isset( $_POST['iw_options']['watermark_image']['offset_height'] ) ? (int) $_POST['iw_options']['watermark_image']['offset_height'] : $this->defaults['options']['watermark_image']['offset_height'];
			$input['watermark_image']['url'] = isset( $_POST['iw_options']['watermark_image']['url'] ) ? (int) $_POST['iw_options']['watermark_image']['url'] : $this->defaults['options']['watermark_image']['url'];
			$input['watermark_image']['watermark_size_type'] = isset( $_POST['iw_options']['watermark_image']['watermark_size_type'] ) ? (int) $_POST['iw_options']['watermark_image']['watermark_size_type'] : $this->defaults['options']['watermark_image']['watermark_size_type'];
			$input['watermark_image']['absolute_width'] = isset( $_POST['iw_options']['watermark_image']['absolute_width'] ) ? (int) $_POST['iw_options']['watermark_image']['absolute_width'] : $this->defaults['options']['watermark_image']['absolute_width'];
			$input['watermark_image']['absolute_height'] = isset( $_POST['iw_options']['watermark_image']['absolute_height'] ) ? (int) $_POST['iw_options']['watermark_image']['absolute_height'] : $this->defaults['options']['watermark_image']['absolute_height'];
			$input['watermark_image']['width'] = isset( $_POST['iw_options']['watermark_image']['width'] ) ? (int) $_POST['iw_options']['watermark_image']['width'] : $this->defaults['options']['watermark_image']['width'];
			$input['watermark_image']['transparent'] = isset( $_POST['iw_options']['watermark_image']['transparent'] ) ? (int) $_POST['iw_options']['watermark_image']['transparent'] : $this->defaults['options']['watermark_image']['transparent'];
			$input['watermark_image']['quality'] = isset( $_POST['iw_options']['watermark_image']['quality'] ) ? (int) $_POST['iw_options']['watermark_image']['quality'] : $this->defaults['options']['watermark_image']['quality'];
			$input['watermark_image']['jpeg_format'] = isset( $_POST['iw_options']['watermark_image']['jpeg_format'] ) && in_array( esc_attr( $_POST['iw_options']['watermark_image']['jpeg_format'] ), array( 'baseline', 'progressive' ) ) ? esc_attr( $_POST['iw_options']['watermark_image']['jpeg_format'] ) : $this->defaults['options']['watermark_image']['jpeg_format'];

			$input['image_protection']['rightclick'] = isset( $_POST['iw_options']['image_protection']['rightclick'] ) ? ((bool) $_POST['iw_options']['image_protection']['rightclick'] == 1 ? true : false) : $this->defaults['options']['image_protection']['rightclick'];
			$input['image_protection']['draganddrop'] = isset( $_POST['iw_options']['image_protection']['draganddrop'] ) ? ((bool) $_POST['iw_options']['image_protection']['draganddrop'] == 1 ? true : false) : $this->defaults['options']['image_protection']['draganddrop'];
			$input['image_protection']['forlogged'] = isset( $_POST['iw_options']['image_protection']['forlogged'] ) ? ((bool) $_POST['iw_options']['image_protection']['forlogged'] == 1 ? true : false) : $this->defaults['options']['image_protection']['forlogged'];

			add_settings_error( 'iw_settings_errors', 'iw_settings_saved', __( 'Settings saved.', 'image-watermark' ), 'updated' );
		} elseif ( isset( $_POST['reset_image_watermark_options'] ) ) {

			$input = $this->defaults['options'];

			add_settings_error( 'iw_settings_errors', 'iw_settings_reset', __( 'Settings restored to defaults.', 'image-watermark' ), 'updated' );
		}

		if ( $input['watermark_image']['plugin_off'] != 0 || $input['watermark_image']['manual_watermarking'] != 0 ) {
			if ( empty( $input['watermark_image']['url'] ) )
				add_settings_error( 'iw_settings_errors', 'iw_image_not_set', __( 'Watermark will not be applied when watermark image is not set.', 'image-watermark' ), 'error' );

			if ( empty( $input['watermark_on'] ) )
				add_settings_error( 'iw_settings_errors', 'iw_sizes_not_set', __( 'Watermark will not be applied when no image sizes are selected.', 'image-watermark' ), 'error' );
		}

		return $input;
	}

	/**
	 * Apply watermark to selected image sizes.
	 *
	 * @param	array $data
	 * @param	int $attachment_id
	 * @return	array
	 */
	public function apply_watermark( $data, $attachment_id ) {

		$post = get_post( (int) $attachment_id );
		$post_id = ( ! empty( $post ) ? (int) $post->post_parent : 0);

		// something went wrong or is it automatic mode?
		if ( $attachment_id !== 'manual' && ($this->is_admin === true && ! ((isset( $this->options['watermark_cpt_on'][0] ) && $this->options['watermark_cpt_on'][0] === 'everywhere') || ($post_id > 0 && in_array( get_post_type( $post_id ), array_keys( $this->options['watermark_cpt_on'] ) ) === true))) )
			return $data;

		if ( apply_filters( 'iw_watermark_display', $attachment_id ) === false )
			return $data;

		$upload_dir = wp_upload_dir();

		// is this really an iamge?
		if ( getimagesize( $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $data['file'] ) !== false ) {
			// loop through active image sizes
			foreach ( $this->options['watermark_on'] as $image_size => $active_size ) {
				if ( $active_size === 1 ) {
					switch ( $image_size ) {
						case 'full':
							$filepath = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $data['file'];
							break;

						default:
							if ( ! empty( $data['sizes'] ) && array_key_exists( $image_size, $data['sizes'] ) ) {
								$filepath = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . dirname( $data['file'] ) . DIRECTORY_SEPARATOR . $data['sizes'][$image_size]['file'];
							} else {
								// early getaway
								continue 2;
							}
					}

					do_action( 'iw_before_apply_watermark' );

					// apply watermark
					$this->do_watermark( $filepath );

					do_action( 'iw_after_apply_watermark' );
				}
			}
		}

		// pass forward attachment metadata
		return $data;
	}

	/**
	 * Apply watermark to image.
	 *
	 * @param	string $filepath
	 */
	public function do_watermark( $filepath ) {

		$options = apply_filters( 'iw_watermark_options', $this->options );

		// get image mime type
		$mime_type = wp_check_filetype( $filepath );

		// get image resource
		if ( ($image = $this->get_image_resource( $filepath, $mime_type['type'] )) !== false ) {
			// add watermark image to image
			if ( $this->add_watermark_image( $image, $options ) !== false ) {

				if ( $options['watermark_image']['jpeg_format'] === 'progressive' ) {
					imageinterlace( $image, true );
				}

				// save watermarked image
				$this->save_image_file( $image, $mime_type['type'], $filepath, $options['watermark_image']['quality'] );
			}
		}
	}

	/**
	 * Add watermark image to image.
	 *
	 * @param	resource $image
	 * @param	array $opt
	 * @return	resource
	 */
	private function add_watermark_image( $image, $opt ) {
		// due to allow_url_fopen restrictions on some servers in getimagesize() we need to use server path (not URL)
		$upload_dir = wp_upload_dir();
		$watermark_file = wp_get_attachment_metadata( $opt['watermark_image']['url'], true );
		$url = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $watermark_file['file'];
		$watermark_file_info = getimagesize( $url );

		switch ( $watermark_file_info['mime'] ) {
			case 'image/jpeg':
			case 'image/pjpeg':
				$watermark = imagecreatefromjpeg( $url );
				break;

			case 'image/gif':
				$watermark = imagecreatefromgif( $url );
				break;

			case 'image/png':
				$watermark = imagecreatefrompng( $url );
				break;

			default:
				return false;
		}

		$watermark_width = imagesx( $watermark );
		$watermark_height = imagesy( $watermark );
		$img_width = imagesx( $image );
		$img_height = imagesy( $image );
		$size_type = $opt['watermark_image']['watermark_size_type'];

		if ( $size_type === 1 ) { // custom
			$w = $opt['watermark_image']['absolute_width'];
			$h = $opt['watermark_image']['absolute_height'];
		} elseif ( $size_type === 2 ) { // scale
			$ratio = $img_width * $opt['watermark_image']['width'] / 100 / $watermark_width;

			$w = (int) ($watermark_width * $ratio);
			$h = (int) ($watermark_height * $ratio);

			// if watermark scaled height is bigger then image watermark
			if ( $h > $img_height ) {
				$w = (int) ($img_height * $w / $h);
				$h = $img_height;
			}
		} else { // original
			$w = $watermark_width;
			$h = $watermark_height;
		}

		switch ( $opt['watermark_image']['position'] ) {
			case 'top_left':
				$dest_x = $dest_y = 0;
				break;

			case 'top_center':
				$dest_x = ($img_width / 2) - ($w / 2);
				$dest_y = 0;
				break;

			case 'top_right':
				$dest_x = $img_width - $w;
				$dest_y = 0;
				break;

			case 'middle_left':
				$dest_x = 0;
				$dest_y = ($img_height / 2) - ($h / 2);
				break;

			case 'middle_right':
				$dest_x = $img_width - $w;
				$dest_y = ($img_height / 2) - ($h / 2);
				break;

			case 'bottom_left':
				$dest_x = 0;
				$dest_y = $img_height - $h;
				break;

			case 'bottom_center':
				$dest_x = ($img_width / 2) - ($w / 2);
				$dest_y = $img_height - $h;
				break;

			case 'bottom_right':
				$dest_x = $img_width - $w;
				$dest_y = $img_height - $h;
				break;

			default:
				$dest_x = ($img_width / 2) - ($w / 2);
				$dest_y = ($img_height / 2) - ($h / 2);
		}

		$dest_x += $opt['watermark_image']['offset_width'];
		$dest_y += $opt['watermark_image']['offset_height'];

		$this->imagecopymerge_alpha( $image, $this->resize( $watermark, $url, $w, $h, $watermark_file_info ), $dest_x, $dest_y, 0, 0, $w, $h, $opt['watermark_image']['transparent'] );

		return $image;
	}

	/**
	 * Create new image function.
	 * 
	 * @param	resource $dst_im
	 * @param	resource $src_im
	 * @param	int $dst_x
	 * @param	int $dst_y
	 * @param	int $src_x
	 * @param	int $src_y
	 * @param	int $src_w
	 * @param	int $src_h
	 * @param	int $pct
	 */
	private function imagecopymerge_alpha( $dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct ) {
		// create a cut resource
		$cut = imagecreatetruecolor( $src_w, $src_h );

		// copy relevant section from background to the cut resource
		imagecopy( $cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h );

		// copy relevant section from watermark to the cut resource
		imagecopy( $cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h );

		// insert cut resource to destination image
		imagecopymerge( $dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct );
	}

	/**
	 * Resize image function.
	 * 
	 * @param	resource $Im
	 * @param	string $path
	 * @param	int $nWidth
	 * @param	int $nHeight
	 * @param	array $imgInfo
	 * @return	resource
	 */
	private function resize( $im, $path, $nWidth, $nHeight, $imgInfo ) {
		$newImg = imagecreatetruecolor( $nWidth, $nHeight );

		// check if this image is PNG, then set if transparent
		if ( $imgInfo[2] === 3 ) {
			imagealphablending( $newImg, false );
			imagesavealpha( $newImg, true );
			$transparent = imagecolorallocatealpha( $newImg, 255, 255, 255, 127 );
			imagefilledrectangle( $newImg, 0, 0, $nWidth, $nHeight, $transparent );
		}

		imagecopyresampled( $newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1] );

		return $newImg;
	}

	/**
	 * Get image resource accordingly to mimetype.
	 *
	 * @param	string $filepath
	 * @param	string $mime_type
	 * @return	resource
	 */
	private function get_image_resource( $filepath, $mime_type ) {
		switch ( $mime_type ) {
			case 'image/jpeg':
			case 'image/pjpeg':
				return imagecreatefromjpeg( $filepath );

			case 'image/png':
				$res = imagecreatefrompng( $filepath );
				imagesavealpha( $res, true );
				$transparent = imagecolorallocatealpha( $res, 255, 255, 255, 127 );
				imagefilledrectangle( $res, 0, 0, imagesx( $res ), imagesy( $res ), $transparent );

				return $res;

			default:
				return false;
		}
	}

	/**
	 * Save image from image resource.
	 *
	 * @param	resource $image
	 * @param	string $mime_type
	 * @param	string $filepath
	 * @return	boolean
	 */
	private function save_image_file( $image, $mime_type, $filepath, $quality ) {
		switch ( $mime_type ) {
			case 'image/jpeg':
			case 'image/pjpeg':
				imagejpeg( $image, $filepath, $quality );
				break;

			case 'image/png':
				imagepng( $image, $filepath, (int) round( 9 * $quality / 100 ) );
				break;
		}
	}

	/**
	 * Add links to Support Forum.
	 * 
	 * @param 	array $links
	 * @param 	string $file
	 * @return 	array
	 */
	public function plugin_extend_links( $links, $file ) {
		if ( ! current_user_can( 'install_plugins' ) )
			return $links;

		$plugin = plugin_basename( __FILE__ );

		if ( $file == $plugin ) {
			return array_merge(
				$links, array( sprintf( '<a href="http://www.dfactory.eu/support/forum/image-watermark/" target="_blank">%s</a>', __( 'Support', 'image-watermark' ) ) )
			);
		}

		return $links;
	}

	/**
	 * Add links to Settings page.
	 * 
	 * @param 	array $links
	 * @param 	string $file
	 * @return 	array
	 */
	function plugin_settings_link( $links, $file ) {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) )
			return $links;

		static $plugin;

		$plugin = plugin_basename( __FILE__ );

		if ( $file == $plugin ) {
			$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php' ) . '?page=watermark-options', __( 'Settings', 'image-watermark' ) );
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

}

$image_watermark = new Image_Watermark();
