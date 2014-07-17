<?php
/*
Plugin Name: Image Watermark
Description: Image Watermark allows you to automatically watermark images uploaded to the WordPress Media Library and bulk watermark previously uploaded images.
Version: 1.3.3
Author: dFactory
Author URI: http://www.dfactory.eu/
Plugin URI: http://www.dfactory.eu/plugins/image-watermark/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Text Domain: image-watermark
Domain Path: /languages

Image Watermark
Copyright (C) 2013, Digital Factory - info@digitalfactory.pl

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/


global $wp_version;

if(version_compare(PHP_VERSION, '5.0', '<') || version_compare($wp_version, '3.5', '<'))
{
	wp_die(__('Sorry, Image Watermark plugin requires at least PHP 5.0 and WP 3.5 or higher.'));
}


class Image_Watermark
{
	private $is_admin = TRUE;
	private $_messages = array();
	private $_image_sizes = array();
	private $_watermark_positions = array (
		'x' => array('left', 'center', 'right'),
		'y' => array('top', 'middle', 'bottom'),
	);
	private $_allowed_mime_types = array(
		'image/jpeg',
		'image/pjpeg',
		'image/png'
	);
	protected $_options = array(
		'df_watermark_on' => array(),
		'df_watermark_cpt_on' => array('everywhere'),
		'df_watermark_image' => array(
			'url' => 0,
			'width' => 80,
			'plugin_off' => 0,
			'frontend_active' => FALSE,
			'manual_watermarking' => 0,
			'position' => 'bottom_right',
			'watermark_size_type' => 2,
			'offset_width' => 0,
			'offset_height' => 0,
			'absolute_width' => 0,
			'absolute_height' => 0,
			'transparent' => 50,
			'quality' => 90,
			'jpeg_format' => 'baseline',
			'deactivation_delete' => FALSE
		),
		'df_image_protection' => array(
			'rightclick' => 0,
			'draganddrop' => 0,
			'forlogged' => 0,
		),
		'version' => '1.3.3'
	);


	public function __construct()
	{
		//register installer function
		register_activation_hook(__FILE__, array(&$this, 'activate_watermark'));
		register_deactivation_hook(__FILE__, array(&$this, 'deactivate_watermark'));

		//update-fix from 1.1.4
		if(version_compare((($db_version = get_option('image_watermark_version')) === FALSE ? '1.0.0' : $db_version), '1.2.0', '<'))
		{
			$options_wi = (($tmp = get_option('df_watermark_image')) !== FALSE ? $tmp : $this->_options['df_watermark_image']);

			//new options
			$options_wi['frontend_active'] = $this->_options['df_watermark_image']['frontend_active'];
			$options_wi['deactivation_delete'] = $this->_options['df_watermark_image']['deactivation_delete'];

			//save new options
			update_option('df_watermark_image', $options_wi);

			//update version
			update_option('image_watermark_version', $this->_options['version']);
		}

		//actions
		add_action('plugins_loaded', array(&$this, 'load_textdomain'));
		add_action('wp_loaded', array(&$this, 'load_image_sizes'));
		add_action('admin_enqueue_scripts', array(&$this, 'admin_watermark_scripts_styles'));
		add_action('wp_enqueue_scripts', array(&$this, 'front_watermark_scripts_styles'));
		add_action('admin_menu', array(&$this, 'watermark_admin_menu'));
		add_action('load-upload.php', array(&$this, 'apply_watermark_bulk_action'));
		add_action('admin_notices', array(&$this, 'bulk_admin_notices'));

		//filters
		add_filter('plugin_row_meta', array(&$this, 'plugin_extend_links'), 10, 2);
		add_filter('plugin_action_links', array(&$this, 'plugin_settings_link'), 10, 2);
		add_filter('wp_handle_upload', array(&$this, 'handle_upload_files'));
	}


	/**
	 * Applies watermark everywhere or for specific (custom) post types
	*/
	public function handle_upload_files($file)
	{
		$opt = get_option('df_watermark_image');

		//admin side, we cant use is_admin() here due to frontend's admin-ajax.php request
		if(strpos(strtolower(wp_get_referer()), strtolower(admin_url()), 0) === 0)
		{
			$this->is_admin = TRUE;

			//apply watermark if backend is active and watermark image is set
			if($opt['plugin_off'] === 0 && $opt['url'] !== 0 && in_array($file['type'], $this->_allowed_mime_types))
				add_filter('wp_generate_attachment_metadata', array(&$this, 'apply_watermark'), 10, 2);
		}
		//front side
		else
		{
			$this->is_admin = FALSE;

			//apply watermark if frontend is active and watermark image is set
			if($opt['frontend_active'] === TRUE && $opt['url'] !== 0 && in_array($file['type'], $this->_allowed_mime_types))
				add_filter('wp_generate_attachment_metadata', array(&$this, 'apply_watermark'), 10, 2);
		}

		return $file;
	}


	/**
	 * Applies watermark for selected images on media page
	 */
	public function apply_watermark_bulk_action()
	{
		global $pagenow;

		if($pagenow == 'upload.php')
		{
			$opt = get_option('df_watermark_image');
			$wp_list_table = _get_list_table('WP_Media_List_Table');

			//update-fix from 1.1.0
			$opt['manual_watermarking'] = (isset($opt['manual_watermarking']) ? $opt['manual_watermarking'] : $this->_options['df_watermark_image']['manual_watermarking']);

			//only if manual watermarking is turned on and image watermark is set
			if($wp_list_table->current_action() === 'applywatermark' && $opt['manual_watermarking'] === 1 && $opt['url'] !== 0)
			{
				//security check
				check_admin_referer('bulk-media');

				$location = remove_query_arg(array('watermarked', 'skipped', 'trashed', 'untrashed', 'deleted', 'message', 'ids', 'posted'), wp_get_referer());

				if(!$location)
				{
					$location = 'upload.php';
				}

				$location = add_query_arg('paged', $wp_list_table->get_pagenum(), $location);

				//do we have selected attachments?
				if(!empty($_REQUEST['media']))
				{
					$watermarked = $skipped = 0;

					foreach($_REQUEST['media'] as $media_id)
					{
						$data = wp_get_attachment_metadata($media_id, FALSE);

						//is this really an image?
						if(in_array(get_post_mime_type($media_id), $this->_allowed_mime_types) && is_array($data))
						{
							$this->apply_watermark($data, 'manual');
							$watermarked++;
						}
						else
							$skipped++;
					}

					$location = add_query_arg(array('watermarked' => $watermarked, 'skipped' => $skipped), $location);
				}

				wp_redirect($location);
				exit();
			}
			else return;
		}
	}
	
	
	/**
	 * Shows admin notices
	 */
	public function bulk_admin_notices()
	{
		global $post_type, $pagenow;

		if($pagenow === 'upload.php' && $post_type === 'attachment' && isset($_REQUEST['watermarked'], $_REQUEST['skipped']))
		{
			$watermarked = (int)$_REQUEST['watermarked'];
			$skipped = (int)$_REQUEST['skipped'];

			if($watermarked === 0)
			{
				echo '<div class="error"><p>'.__('Watermark could not be applied to selected files or no valid images (JPEG, PNG) were selected.', 'image-watermark').($skipped > 0 ? ' '.__('Skipped files', 'image-watermark').': '.$skipped.'.' : '').'</p></div>';
			}
			else
			{
				echo '<div class="updated"><p>'.sprintf(_n('Watermark was succesfully applied to 1 image.', 'Watermark was succesfully applied to %s images.', $watermarked, 'image-watermark'), number_format_i18n($watermarked)).($skipped > 0 ? ' '.__('Skipped files', 'image-watermark').': '.$skipped.'.' : '').'</p></div>';
			}

			$_SERVER['REQUEST_URI'] = remove_query_arg(array('watermarked', 'skipped'), $_SERVER['REQUEST_URI']);
		}
	}


	/**
	 * Loads available image sizes
	*/
	public function load_image_sizes()
	{
		$this->_image_sizes = get_intermediate_image_sizes();
		$this->_image_sizes[] = 'full';

		sort($this->_image_sizes, SORT_STRING);
	}


	/**
	 * Loads textdomain
	*/
	public function load_textdomain()
	{
		load_plugin_textdomain('image-watermark', FALSE, basename(dirname(__FILE__)).'/languages');
	}


	/**
	 * Add links to Support Forum
	*/
	public function plugin_extend_links($links, $file) 
	{
		if(!current_user_can('install_plugins'))
			return $links;

		$plugin = plugin_basename(__FILE__);

		if($file == $plugin) 
		{
			return array_merge(
				$links,
				array(sprintf('<a href="http://www.dfactory.eu/support/forum/image-watermark/" target="_blank">%s</a>', __('Support', 'image-watermark')))
			);
		}

		return $links;
	}


	/**
	 * Add links to Settings page
	*/
	function plugin_settings_link($links, $file) 
	{
		if(!is_admin() || !current_user_can('manage_options'))
			return $links;

		static $plugin;

		$plugin = plugin_basename(__FILE__);

		if($file == $plugin) 
		{
			$settings_link = sprintf('<a href="%s">%s</a>', admin_url('options-general.php').'?page=watermark-options', __('Settings', 'image-watermark'));
			array_unshift($links, $settings_link);
		}

		return $links;
	}


	/**
	 * Enqueues admin-side scripts and styles
	*/
	public function admin_watermark_scripts_styles($page)
	{
		if($page === 'upload.php')
		{
			$opt = get_option('df_watermark_image');

			//update-fix from 1.1.0
			$opt['manual_watermarking'] = (isset($opt['manual_watermarking']) ? $opt['manual_watermarking'] : $this->_options['df_watermark_image']['manual_watermarking']);

			if($opt['manual_watermarking'] === 1)
			{
				wp_enqueue_script(
					'apply-watermark',
					plugins_url('/js/apply-watermark-admin.js', __FILE__)
				);

				wp_localize_script(
					'apply-watermark',
					'watermark_args',
					array(
						'apply_watermark' => __('Apply watermark', 'image-watermark')
					)
				);
			}
		}
		elseif($page === 'settings_page_watermark-options')
		{
			wp_enqueue_media();

			wp_register_script(
				'upload-manager',
				plugins_url('/js/upload-manager-admin.js', __FILE__)
			);

			wp_enqueue_script('upload-manager');

			wp_localize_script(
				'upload-manager',
				'upload_manager_args',
				array(
					'title'			=> __('Select watermark', 'image-watermark'),
					'originalSize'	=> __('Original size', 'image-watermark'),
					'noSelectedImg'	=> __('Watermak has not been selected yet.', 'image-watermark'),
					'notAllowedImg'	=> __('This image is not supported as watermark. Use JPEG, PNG or GIF.', 'image-watermark'),
					'frame'			=> 'select',
					'button'		=> array('text' => __('Add watermark', 'image-watermark')),
					'multiple'		=> FALSE,
				)
			);

			wp_register_script(
				'watermark-admin-script',
				plugins_url('js/image-watermark-admin.js', __FILE__),
				array('jquery', 'jquery-ui-core', 'jquery-ui-button', 'jquery-ui-slider')
			);

			wp_enqueue_script('watermark-admin-script');

			wp_localize_script(
				'watermark-admin-script',
				'iwArgs',
				array(
					'resetToDefaults' => __('Are you sure you want to reset settings to defaults?', 'image-watermark')
				)
			);

			wp_register_style(
				'watermark-style',
				plugins_url('css/image-watermark.css', __FILE__)
			);

			wp_enqueue_style('watermark-style');

			wp_register_style(
				'wp-like-ui-theme',
				plugins_url('css/wp-like-ui-theme.css', __FILE__)
			);

			wp_enqueue_style('wp-like-ui-theme');
		}
	}


	/**
	 * Enqueues front-side script with 'no right click' and 'drag and drop' functions
	*/
	public function front_watermark_scripts_styles()
	{
		$options = get_option('df_image_protection');

		// backward compatibility options
		$options['forlogged'] = (!empty($options['forlogged']) ? 1 : 0);
		$options['draganddrop'] = (!empty($options['draganddrop']) ? 1 : 0);
		$options['rightclick'] = (!empty($options['rightclick']) ? 1 : 0);

		if(($options['forlogged'] == 0 && is_user_logged_in()) || ($options['draganddrop'] == 0 && $options['rightclick'] == 0))
			return;

		wp_enqueue_script(
			'no-right-click',
			plugins_url('js/no-right-click-front.js', __FILE__)
		);

		wp_localize_script(
			'no-right-click',
			'norightclick_args',
			array(
				'rightclick' => ($options['rightclick'] == 1 ? 'Y' : 'N'),
				'draganddrop' => ($options['draganddrop'] == 1 ? 'Y' : 'N')
			)
		);
	}


	/**
	 * Creates options page in menu
	*/
	public function watermark_admin_menu()
	{
		add_options_page(
			__('Image Watermark Options', 'image-watermark'),
			__('Watermark', 'image-watermark'),
			'manage_options',
			'watermark-options',
			array(&$this, 'watermark_options_page')
		);
	}


	/**
	 * Gets custom post types with additional post and page types
	*/
	private function getCustomPostTypes()
	{
		return array_merge(array('post', 'page'), get_post_types(array('_builtin' => FALSE), 'names'));
	}


	/**
	 * Display options page
	 */
	public function watermark_options_page()
	{
		if(!extension_loaded('gd'))
		{
			echo '
			<div class="error">
				<p>'.__('Image Watermark will not work properly without GD PHP extension.', 'image-watermark').'</p>
			</div>';

			return;
		}

		//saves changes
		if(isset($_POST['submit']))
		{
			foreach($this->_options as $option => $value)
			{
				if(array_key_exists($option, $_POST))
				{
					switch($option)
					{
						case 'df_watermark_on':
						{
							$tmp = array();

							foreach($this->_image_sizes as $size)
							{
								if(in_array($size, array_keys($_POST[$option])))
								{
									$tmp[$size] = 1;
								}
							}

							update_option($option, $tmp);
							break;
						}
						case 'df_watermark_cpt_on':
						{
							if($_POST['df_watermark_cpt_on'] === 'everywhere')
							{
								update_option($option, array('everywhere'));
							}
							elseif($_POST['df_watermark_cpt_on'] === 'specific')
							{
								if(isset($_POST['df_watermark_cpt_on_type']))
								{
									$tmp = array();

									foreach($this->getCustomPostTypes() as $cpt)
									{
										if(in_array($cpt, array_keys($_POST['df_watermark_cpt_on_type'])))
										{
											$tmp[$cpt] = 1;
										}
									}

									if(count($tmp) === 0) update_option($option, array('everywhere'));
									else update_option($option, $tmp);
								}
								else update_option($option, array('everywhere'));
							}

							break;
						}
						case 'df_watermark_image':
						{
							$tmp = array();

							foreach($this->_options[$option] as $image_option => $value_i)
							{
								switch($image_option)
								{
									case 'watermark_size_type':
										$tmp[$image_option] = (int)(isset($_POST[$option][$image_option]) && in_array($_POST[$option][$image_option], array(0, 1, 2)) ? $_POST[$option][$image_option] : $this->_options[$option][$image_option]);
										break;

									case 'transparent':
									case 'quality':
									case 'width':
										$tmp[$image_option] = (isset($_POST[$option][$image_option]) ? ($_POST[$option][$image_option] <= 0 ? 0 : ($_POST[$option][$image_option] >= 100 ? 100 : (int)$_POST[$option][$image_option])) : $this->_options[$option][$image_option]);
										break;

									case 'deactivation_delete':
									case 'frontend_active':
										$tmp[$image_option] = (isset($_POST[$option][$image_option]) ? ($_POST[$option][$image_option] == 1 ? TRUE : FALSE) : $this->_options[$option][$image_option]);
										break;

									case 'plugin_off':
									case 'manual_watermarking':
									case 'offset_width':
									case 'offset_height':
									case 'absolute_width':
									case 'absolute_height':
										$tmp[$image_option] = (int)(isset($_POST[$option][$image_option]) ? $_POST[$option][$image_option] : $this->_options[$option][$image_option]);
										break;

									case 'url':
										$tmp[$image_option] = (isset($_POST[$option][$image_option]) ? (int)$_POST[$option][$image_option] : $this->_options[$option][$image_option]);
										break;

									case 'jpeg_format':
										$tmp[$image_option] = (isset($_POST[$option][$image_option]) && in_array($_POST[$option][$image_option], array('baseline', 'progressive')) ? $_POST[$option][$image_option] : $this->_options[$option][$image_option]);
										break;

									case 'position':
										$positions = array();

										foreach($this->_watermark_positions['y'] as $position_y)
										{
											foreach($this->_watermark_positions['x'] as $position_x)
											{
												$positions[] = $position_y.'_'.$position_x;
											}
										}

										$tmp[$image_option] = (isset($_POST[$option][$image_option]) && in_array($_POST[$option][$image_option], $positions) ? $_POST[$option][$image_option] : $this->_options[$option][$image_option]);
										break;
								}
							}

							update_option($option, $tmp);
							break;
						}
						case 'df_image_protection':
						{
							$tmp = array();

							foreach($this->_options[$option] as $protection => $value_p)
							{
								if(in_array($protection, array_keys($_POST[$option])))
									$tmp[$protection] = 1;
								else
									$tmp[$protection] = 0;
							}

							update_option($option, $tmp);
							break;
						}
					}
				}
				else
				{
					update_option($option, $value);
				}
			}

			echo '
			<div class="updated">
				<p>'.__('Settings saved.').'</p>
			</div>';
		}
		//reset to defaults
		elseif(isset($_POST['reset']))
		{
			foreach($this->_options as $option => $value)
			{
				update_option($option, $value);
			}

			echo '
			<div class="updated">
				<p>'.__('Settings restored to defaults.').'</p>
			</div>';
		}

		$watermark_image = get_option('df_watermark_image');
		$image_protection = get_option('df_image_protection');
		$watermark_on = get_option('df_watermark_on');

		//update-fix from 1.1.0
		$watermark_image['manual_watermarking'] = (isset($watermark_image['manual_watermarking']) ? $watermark_image['manual_watermarking'] : $this->_options['df_watermark_image']['manual_watermarking']);

		//update-fix from 1.1.2
		$watermark_image['jpeg_format'] = (isset($watermark_image['jpeg_format']) ? $watermark_image['jpeg_format'] : $this->_options['df_watermark_image']['jpeg_format']);
		$watermark_image['quality'] = (isset($watermark_image['quality']) ? $watermark_image['quality'] : $this->_options['df_watermark_image']['quality']);

		$errors = '';

		if($watermark_image['plugin_off'] === 0 || $watermark_image['manual_watermarking'] === 1)
		{
			if($watermark_image['url'] === 0)
				$errors .= ($errors !== '' ? '<br />' : '').__('Watermark will not be applied when <b>watermark image is not set</b>.', 'image-watermark');

			if(empty($watermark_on))
				$errors .= ($errors !== '' ? '<br />' : '').__('Watermark will not be applied when <b>no image sizes are selected</b>.', 'image-watermark');
		}

		if($watermark_image['frontend_active'] === TRUE)
		{
			if($watermark_image['url'] === 0)
				$errors .= ($errors !== '' ? '<br />' : '').__('Watermark will not be applied while frontend image upload if <b>watermark image is not set</b>.', 'image-watermark');

			if(empty($watermark_on))
				$errors .= ($errors !== '' ? '<br />' : '').__('Watermark will not be applied while frontend image upload if <b>no image sizes are selected</b>.', 'image-watermark');
		}

		echo ($errors !== '' ? sprintf('<div class="error"><p>%s</p></div>', $errors) : '');
	?>
	<div class="wrap">
        <div id="icon-options-general" class="icon32"><br /></div>
        <h2><?php echo __('Image Watermark Settings', 'image-watermark'); ?></h2>
        
	        <div class="image-watermark-settings">
	        	<div class="df-credits">
		        	<h3 class="hndle"><?php _e('Image Watermark', 'image-watermark'); ?></h3>
		            <div class="inside">
		                <h4 class="inner"><?php _e('Need support?', 'image-watermark'); ?></h4>
		                <p class="inner"><?php _e('If you are having problems with this plugin, please talk about them in the', 'image-watermark'); ?> <a href="http://dfactory.eu/support/" target="_blank" title="<?php _e('Support forum', 'image-watermark'); ?>"><?php _e('Support forum', 'image-watermark'); ?></a></p>
		                <hr />
		                <h4 class="inner"><?php _e('Do you like this plugin?', 'image-watermark'); ?></h4>
		                <p class="inner"><a href="http://wordpress.org/support/view/plugin-reviews/image-watermark?filter=5" target="_blank" title="<?php _e('Rate it 5', 'image-watermark'); ?>"><?php _e('Rate it 5', 'image-watermark'); ?></a> <?php _e('on WordPress.org', 'image-watermark'); ?><br />
						<?php _e('Blog about it & link to the','image-watermark'); ?> <a href="http://dfactory.eu/plugins/image-watermark/" target="_blank" title="<?php _e('plugin page', 'image-watermark'); ?>"><?php _e('plugin page', 'image-watermark'); ?></a><br />
		                <?php _e('Check out our other', 'image-watermark'); ?> <a href="http://dfactory.eu/plugins/" target="_blank" title="<?php _e('WordPress plugins', 'image-watermark'); ?>"><?php _e('WordPress plugins', 'image-watermark'); ?></a>
		                </p>
		                <hr />
		                <p class="df-link inner"><?php _e('Created by', 'restrict-widgets'); ?><a href="http://www.dfactory.eu" target="_blank" title="dFactory - Quality plugins for WordPress"><img src="<?php echo plugins_url( 'images/logo-dfactory.png' , __FILE__ ); ?>" title="dFactory - Quality plugins for WordPress" alt="dFactory - Quality plugins for WordPress" /></a></p>
					</div>
				</div>

            <form method="post" action="">
            	<h3><?php echo __('General settings', 'image-watermark'); ?></h3>
                <table id="watermark-general-table" class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo __('Automatic watermarking', 'image-watermark'); ?></th>
                        <td class="wr_width">
                            <fieldset class="wr_width">
                                <div id="run-watermark">
                                    <label for="plugin_on"><?php echo __('on', 'image-watermark'); ?></label>
                                    <input type="radio" id="plugin_on" value="0" name="df_watermark_image[plugin_off]" <?php checked($watermark_image['plugin_off'], 0, TRUE); ?> />
                                    <label for="plugin_off"><?php echo __('off', 'image-watermark'); ?></label>
                                    <input type="radio" id="plugin_off" value="1" name="df_watermark_image[plugin_off]" <?php checked($watermark_image['plugin_off'], 1, TRUE); ?> />
                                </div>
                                <p class="description"><?php echo __('Enable or disable watermark for uploaded images.', 'image-watermark'); ?></p>
                            </fieldset>
                        </td>
                    </tr>
                </table>
				<table id="watermark-manual-table" class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo __('Manual watermarking', 'image-watermark'); ?></th>
                        <td class="wr_width">
                            <fieldset class="wr_width">
                                <div id="run-manual-watermark">
                                    <label for="manual_watermarking_on"><?php echo __('on', 'image-watermark'); ?></label>
                                    <input type="radio" id="manual_watermarking_on" value="1" name="df_watermark_image[manual_watermarking]" <?php checked($watermark_image['manual_watermarking'], 1, TRUE); ?> />
                                    <label for="manual_watermarking_off"><?php echo __('off', 'image-watermark'); ?></label>
                                    <input type="radio" id="manual_watermarking_off" value="0" name="df_watermark_image[manual_watermarking]" <?php checked($watermark_image['manual_watermarking'], 0, TRUE); ?> />
                                </div>
                                <p class="description"><?php echo __('Enable or disable Apply Watermark option for images in Media Library.', 'image-watermark'); ?></p>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                <table id="watermark-for-table" class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo __('Enable watermark for', 'image-watermark'); ?></th>
                        <td class="wr_width">
                            <fieldset class="wr_width">
                                <div id="thumbnail-select">
									<?php foreach($this->_image_sizes as $image_size) : ?>
                                        <input name="df_watermark_on[<?php echo $image_size; ?>]" type="checkbox" id="<?php echo $image_size; ?>" value="1" <?php echo (in_array($image_size, array_keys($watermark_on)) ? ' checked="checked"' : ''); ?> />
                                        <label for="<?php echo $image_size; ?>"><?php echo $image_size; ?></label>
                                    <?php endforeach; ?>
                                </div>
                                <p class="description"><?php echo __('Check image sizes on which watermark should appear.<br /><strong>IMPORTANT:</strong> checking full size is NOT recommended as it\'s the original image. You may need it later - for removing or changing watermark, image sizes regeneration or any other image manipulations. Use it only if you know what you are doing.', 'image-watermark'); ?></p>
                                <?php $watermark_cpt_on = array_keys(get_option('df_watermark_cpt_on'));
								if(in_array('everywhere', $watermark_cpt_on) && count($watermark_cpt_on) === 1)
								{ $first_checked = TRUE; $second_checked = FALSE; $watermark_cpt_on = array(); }
								else { $first_checked = FALSE; $second_checked = TRUE; } ?>
								<div id="cpt-specific">
									<input id="df_option_everywhere" type="radio" name="df_watermark_cpt_on" value="everywhere" <?php echo ($first_checked === TRUE ? 'checked="checked"' : ''); ?>/><label for="df_option_everywhere"><?php _e('everywhere', 'image-watermark'); ?></label>
									<input id="df_option_cpt" type="radio" name="df_watermark_cpt_on" value="specific" <?php echo ($second_checked === TRUE ? 'checked="checked"' : ''); ?> /><label for="df_option_cpt"><?php _e('on selected post types only', 'image-watermark'); ?></label>
								</div>
								<div id="cpt-select" <?php echo ($second_checked === FALSE ? 'style="display: none;"' : ''); ?>>
									<?php foreach($this->getCustomPostTypes() as $cpt) : ?>
                                        <input name="df_watermark_cpt_on_type[<?php echo $cpt; ?>]" type="checkbox" id="<?php echo $cpt; ?>" value="1" <?php echo (in_array($cpt, $watermark_cpt_on) ? ' checked="checked"' : ''); ?> />
                                        <label for="<?php echo $cpt; ?>"><?php echo $cpt; ?></label>
                                    <?php endforeach; ?>
									</div>
                                <p class="description"><?php echo __('Check custom post types on which watermark should be applied to uploaded images.', 'image-watermark'); ?></p>
                            </fieldset>
                        </td>
                    </tr>
                </table>
				<table id="watermark-general-table-front" class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo __('Automatic frontend watermarking', 'image-watermark'); ?></th>
                        <td class="wr_width">
                            <fieldset class="wr_width">
                                <div id="run-watermark-front">
                                    <label for="frontend_active"><?php echo __('on', 'image-watermark'); ?></label>
                                    <input type="radio" id="frontend_active" value="1" name="df_watermark_image[frontend_active]" <?php checked($watermark_image['frontend_active'], TRUE, TRUE); ?> />
                                    <label for="frontend_inactive"><?php echo __('off', 'image-watermark'); ?></label>
                                    <input type="radio" id="frontend_inactive" value="0" name="df_watermark_image[frontend_active]" <?php checked($watermark_image['frontend_active'], FALSE, TRUE); ?> />
                                </div>
                                <p class="description"><?php echo __('Enable or disable watermark for frontend image uploading. (uploading script is not included, but you may use a plugin or custom code). <strong>Notice:</strong> This functionality works only if uploaded images are processed using WordPress native upload methods.', 'image-watermark'); ?></p>
                            </fieldset>
                        </td>
                    </tr>
                </table>
				<table id="watermark-general-table-deactivation" class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo __('Plugin deactivation', 'image-watermark'); ?></th>
                        <td class="wr_width">
                            <fieldset class="wr_width">
                                <div id="deactivation-delete">
                                    <label for="deactivate_on"><?php echo __('on', 'image-watermark'); ?></label>
                                    <input type="radio" id="deactivate_on" value="1" name="df_watermark_image[deactivation_delete]" <?php checked($watermark_image['deactivation_delete'], TRUE, TRUE); ?> />
                                    <label for="deactivate_off"><?php echo __('off', 'image-watermark'); ?></label>
                                    <input type="radio" id="deactivate_off" value="0" name="df_watermark_image[deactivation_delete]" <?php checked($watermark_image['deactivation_delete'], FALSE, TRUE); ?> />
                                </div>
                                <p class="description"><?php echo __('Delete all database settings on plugin deactivation.', 'image-watermark'); ?></p>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                <hr />
                <h3><?php echo __('Watermark position', 'image-watermark'); ?></h3>
                <table id="watermark-position-table" class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo __('Watermark alignment','image-watermark'); ?></th>
                        <td>
                            <fieldset>
                                <table id="watermark_position" border="1">
                                    <?php $watermark_position = $watermark_image['position']; ?>
                                    <?php foreach($this->_watermark_positions['y'] as $y) : ?>
                                    <tr>
                                        <?php foreach($this->_watermark_positions['x'] as $x) : ?>
                                        <td title="<?php echo ucfirst($y . ' ' . $x); ?>">
                                            <input name="df_watermark_image[position]" type="radio" value="<?php echo $y . '_' . $x; ?>"<?php echo ($watermark_position == $y . '_' . $x ? ' checked="checked"' : NULL); ?> />
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </table>
                                <p class="description"><?php echo __('Choose the position of watermark image.','image-watermark'); ?></p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo __('Watermark offset','image-watermark'); ?></th>
                        <td>
                            <fieldset>
                                <?php echo __('x:','image-watermark'); ?> <input type="text" size="5"  name="df_watermark_image[offset_width]" value="<?php echo $watermark_image['offset_width']; ?>"> <?php echo __('px','image-watermark'); ?>
                                <br />
                                <?php echo __('y:','image-watermark'); ?> <input type="text" size="5"  name="df_watermark_image[offset_height]" value="<?php echo $watermark_image['offset_height']; ?>"> <?php echo __('px','image-watermark'); ?>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                <hr />

				<?php
					if($watermark_image['url'] !== NULL && $watermark_image['url'] != 0)
					{
						$image = wp_get_attachment_image_src($watermark_image['url'], array(300, 300), FALSE);
						$imageSelected = TRUE;
					}
					else $imageSelected = FALSE;
				?>

                <h3><?php echo __('Watermark image','image-watermark'); ?></h3>
                <p class="description"><?php echo __('Configure your watermark image. Allowed file formats are: JPEG, PNG, GIF.','image-watermark'); ?></p>
                <table id="watermark-image-table" class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo __('Watermark image','image-watermark'); ?></th>
                        <td>
							<input id="upload_image" type="hidden" name="df_watermark_image[url]" value="<?php echo (int)$watermark_image['url']; ?>" />
                            <input id="upload_image_button" type="button" class="button button-secondary" value="<?php echo __('Select image','image-watermark'); ?>" />
                            <input id="turn_off_image_button" type="button" class="button button-secondary" value="<?php echo __('Turn off image','image-watermark'); ?>" <?php if($imageSelected === FALSE) echo 'disabled="disabled"'; ?>/>
							<p class="description"><?php _e('You have to save changes after the selection or removal of the image.', 'image-watermark'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo __('Watermark preview', 'image-watermark'); ?></th>
                        <td class="wr_width">
                            <fieldset class="wr_width">
                                <div id="previewImg_imageDiv">
                                    <?php if($imageSelected === TRUE) {
									$image = wp_get_attachment_image_src($watermark_image['url'], array(300, 300), FALSE);
									?>
                                    <img id="previewImg_image" src="<?php echo $image[0]; ?>" alt="" width="300" />
                                    <?php } else { ?>
									<img id="previewImg_image" src="" alt="" width="300" style="display: none;" />
									<?php } ?>
                                </div>
                                <p id="previewImageInfo" class="description">
									<?php
									if($imageSelected === FALSE)
									{
										_e('Watermak has not been selected yet.', 'image-watermark');
									}
									else
									{
										$imageFullSize = wp_get_attachment_image_src($watermark_image['url'], 'full', FALSE);

										_e('Original size', 'image-watermark').': '.$imageFullSize[1].' px / '.$imageFullSize[2].' px';
									}
									?>
								</p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Watermark size', 'image-watermark'); ?></th>
                        <td class="wr_width">
                            <fieldset class="wr_width">
                                <div id="watermark-type">
									<label for="type1"><?php _e('original', 'image-watermark'); ?></label>
									<input type="radio" id="type1" value="0" name="df_watermark_image[watermark_size_type]" <?php checked($watermark_image['watermark_size_type'], 0, TRUE); ?> />
									<label for="type2"><?php _e('custom', 'image-watermark'); ?></label>
									<input type="radio" id="type2" value="1" name="df_watermark_image[watermark_size_type]" <?php checked($watermark_image['watermark_size_type'], 1, TRUE); ?> />
									<label for="type3"><?php _e('scaled', 'image-watermark'); ?></label>
									<input type="radio" id="type3" value="2" name="df_watermark_image[watermark_size_type]" <?php checked($watermark_image['watermark_size_type'], 2, TRUE); ?> />
                                </div>
                                <p class="description"><?php _e('Select method of aplying watermark size.', 'image-watermark'); ?></p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr valign="top" id="watermark_size_custom">
                        <th scope="row"><?php _e('Watermark custom size', 'image-watermark'); ?></th>
                        <td class="wr_width">
                            <fieldset class="wr_width">
                                <?php _e('x:', 'image-watermark'); ?> <input type="text" size="5"  name="df_watermark_image[absolute_width]" value="<?php echo $watermark_image['absolute_width']; ?>"> <?php _e('px', 'image-watermark'); ?>
                                <br />
                                <?php _e('y:', 'image-watermark'); ?> <input type="text" size="5"  name="df_watermark_image[absolute_height]" value="<?php echo $watermark_image['absolute_height']; ?>"> <?php _e('px','image-watermark'); ?>
                            </fieldset>
                            <p class="description"><?php _e('Those dimensions will be used if "custom" method is selected above.', 'image-watermark'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top" id="watermark_size_scale">
                        <th scope="row"><?php _e('Scale of watermark in relation to image width', 'image-watermark'); ?></th>
                        <td class="wr_width">
                            <fieldset class="wr_width">
								<div>
									<input type="text" id="iw_size_input" maxlength="3" class="hide-if-js" name="df_watermark_image[width]" value="<?php echo $watermark_image['width']; ?>" />
									<div class="wplike-slider">
										<span class="left hide-if-no-js">0</span><span class="middle" id="iw_size_span" title="<?php echo $watermark_image['width']; ?>"></span><span class="right hide-if-no-js">100</span>
									</div>
								</div>
                            </fieldset>
                            <p class="description"><?php _e('This value will be used if "scaled" method if selected above. <br />Enter a number ranging from 0 to 100. 100 makes width of watermark image equal to width of the image it is applied to.', 'image-watermark'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Watermark transparency / opacity', 'image-watermark'); ?></th>
                        <td class="wr_width">
                            <fieldset class="wr_width">
								<div>
									<input type="text" id="iw_opacity_input" maxlength="3" class="hide-if-js" name="df_watermark_image[transparent]" value="<?php echo $watermark_image['transparent']; ?>" />
									<div class="wplike-slider">
										<span class="left hide-if-no-js">0</span><span class="middle" id="iw_opacity_span" title="<?php echo $watermark_image['transparent']; ?>"></span><span class="right hide-if-no-js">100</span>
									</div>
								</div>
                            </fieldset>
                            <p class="description"><?php _e('Enter a number ranging from 0 to 100. 0 makes watermark image completely transparent, 100 shows it as is.', 'image-watermark'); ?></p>
                        </td>
                    </tr>
					<tr valign="top">
                        <th scope="row"><?php _e('Image quality', 'image-watermark'); ?></th>
                        <td class="wr_width">
                            <fieldset class="wr_width">
								<div>
									<input type="text" id="iw_quality_input" maxlength="3" class="hide-if-js" name="df_watermark_image[quality]" value="<?php echo $watermark_image['quality']; ?>" />
									<div class="wplike-slider">
										<span class="left hide-if-no-js">0</span><span class="middle" id="iw_quality_span" title="<?php echo $watermark_image['quality']; ?>"></span><span class="right hide-if-no-js">100</span>
									</div>
								</div>
                            </fieldset>
                            <p class="description"><?php _e('Set output image quality.', 'image-watermark'); ?></p>
                        </td>
                    </tr>
					<tr valign="top">
                        <th scope="row"><?php _e('Image format', 'image-watermark'); ?></th>
                        <td class="wr_width">
                            <fieldset class="wr_width">
								<div id="jpeg-format">
                                    <label for="baseline"><?php _e('baseline', 'image-watermark'); ?></label>
                                    <input type="radio" id="baseline" value="baseline" name="df_watermark_image[jpeg_format]" <?php checked($watermark_image['jpeg_format'], 'baseline', TRUE); ?> />
                                    <label for="progressive"><?php _e('progressive', 'image-watermark'); ?></label>
                                    <input type="radio" id="progressive" value="progressive" name="df_watermark_image[jpeg_format]" <?php checked($watermark_image['jpeg_format'], 'progressive', TRUE); ?> />
                                </div>
                            </fieldset>
                            <p class="description"><?php _e('Select baseline or progressive image format.', 'image-watermark'); ?></p>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="action" value="update" />
                <hr />
                <h3><?php _e('Image protection', 'image-watermark'); ?></h3>
                <table id="watermark-protection-table" class="form-table">
                    <tr>
                        <th><?php _e('Disable right mouse click on images', 'image-watermark'); ?></th>
                        <td><input type="checkbox" <?php checked((!empty($image_protection['rightclick']) ? 1 : 0), 1, TRUE); ?> value="1" name="df_image_protection[rightclick]"></td>
                    </tr>
                    <tr>
                        <th><?php _e('Prevent drag and drop', 'image-watermark'); ?></th>
                        <td><input type="checkbox" <?php checked((!empty($image_protection['draganddrop']) ? 1 : 0), 1, TRUE); ?> value="1" name="df_image_protection[draganddrop]"></td>
                    </tr>
                    <tr>
                        <th><?php _e('Enable image protection for logged-in users also', 'image-watermark'); ?></th>
                        <td><input type="checkbox" <?php checked((!empty($image_protection['forlogged']) ? 1 : 0), 1, TRUE); ?> value="1" name="df_image_protection[forlogged]"></td>
                    </tr>
                </table>
                <hr />
                <input type="submit" id="watermark-submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'image-watermark'); ?>" />
				<input type="submit" id="watermark-reset" class="button button-secondary" name="reset" value="<?php _e('Reset to defaults', 'image-watermark'); ?>" />
            </form>
        </div>
	</div>
	<?php
	}


	/**
	 * Plugin activation
	 */
	public function activate_watermark()
	{
		//save install time
		add_option('df_watermark_installed', current_time('timestamp'), '', 'no');

		//loop through default options and add them into DB
		foreach($this->_options as $option => $value)
		{
			add_option($option, $value, '', 'no');
		}
	}


	/**
	 * Plugin deactivation
	 */
	public function deactivate_watermark()
	{
		$opt = get_option('df_watermark_image');

		//remove options from database?
		if($opt['deactivation_delete'] === TRUE)
		{
			delete_option('df_image_protection');
			delete_option('df_watermark_cpt_on');
			delete_option('df_watermark_image');
			delete_option('df_watermark_installed');
			delete_option('df_watermark_on');
			delete_option('image_watermark_version');
		}
	}


	/**
	 * Apply watermark to selected image sizes
	 *
	 * @param array $data
	 * @return array
	 */
	public function apply_watermark($data, $attachment_id)
	{
		$opt_img = get_option('df_watermark_image');
		$opt_cpt = get_option('df_watermark_cpt_on');
		$opt_won = get_option('df_watermark_on');

		$post = get_post((int)$attachment_id);
		$post_id = (!empty($post) ? (int)$post->post_parent : 0);

		//something went wrong or is it automatic mode?
		if($attachment_id !== 'manual' && ($this->is_admin === TRUE && !((isset($opt_cpt[0]) && $opt_cpt[0] === 'everywhere') || ($post_id > 0 && in_array(get_post_type($post_id), array_keys($opt_cpt)) === TRUE))))
			return $data;

		if(apply_filters('iw_watermark_display', $attachment_id) === FALSE)
			return $data;

		$upload_dir = wp_upload_dir();

		//is this really an iamge?
		if(getimagesize($upload_dir['basedir'].DIRECTORY_SEPARATOR.$data['file']) !== FALSE)
		{
			//loop through active image sizes
			foreach($opt_won as $image_size => $active_size)
			{
				if($active_size === 1)
				{
					switch($image_size)
					{
						case 'full':
							$filepath = $upload_dir['basedir'].DIRECTORY_SEPARATOR.$data['file'];
							break;

						default:
							if(!empty($data['sizes']) && array_key_exists($image_size, $data['sizes']))
							{
								$filepath = $upload_dir['basedir'].DIRECTORY_SEPARATOR.dirname($data['file']).DIRECTORY_SEPARATOR.$data['sizes'][$image_size]['file'];
							}
							else
							{
								//early getaway
								continue 2;
							}
					}

					do_action('iw_before_apply_watermark');

					//apply watermark
					$this->do_watermark($filepath);

					do_action('iw_after_apply_watermark');
				}
			}
		}

		//pass forward attachment metadata
		return $data;
	}


	/**
	* Apply watermark to certain image
	*
	* @param string $filepath
	*/
	public function do_watermark($filepath)
	{
		$options = array();

		//get watermark settings
		foreach($this->_options as $option => $value)
		{
			$options[$option] = get_option($option);
		}

		//update-fix from 1.1.2
		$options['df_watermark_image']['quality'] = (isset($options['df_watermark_image']['quality']) ? $options['df_watermark_image']['quality'] : $this->_options['df_watermark_image']['quality']);

		$options = apply_filters('iw_watermark_options', $options);

		//get image mime type
		$mime_type = wp_check_filetype($filepath);

		//get image resource
		if(($image = $this->get_image_resource($filepath, $mime_type['type'])) !== FALSE)
		{
			//add watermark image to image
			if($this->add_watermark_image($image, $options) !== FALSE)
			{
				//update-fix from 1.1.2
				$options['df_watermark_image']['jpeg_format'] = (isset($options['df_watermark_image']['jpeg_format']) ? $options['df_watermark_image']['jpeg_format'] : $this->_options['df_watermark_image']['jpeg_format']);

				if($options['df_watermark_image']['jpeg_format'] === 'progressive')
				{
					imageinterlace($image, true);
				}

				//save watermarked image
				$this->save_image_file($image, $mime_type['type'], $filepath, $options['df_watermark_image']['quality']);
			}
		}
	}


	/**
	 * Add watermark image to image
	 *
	 * @param resource $image
	 * @param array $opt
	 * @return resource
	 */
	private function add_watermark_image($image, array $opt)
	{
		//due to allow_url_fopen restrictions on some servers in getimagesize() we need to use server path (not URL)
		$upload_dir = wp_upload_dir();
		$watermark_file = wp_get_attachment_metadata($opt['df_watermark_image']['url'], TRUE);
		$url = $upload_dir['basedir'].DIRECTORY_SEPARATOR.$watermark_file['file'];
		$watermark_file_info = getimagesize($url);

		switch($watermark_file_info['mime'])
		{
			case 'image/jpeg':
			case 'image/pjpeg':
				$watermark = imagecreatefromjpeg($url);
				break;

			case 'image/gif':
				$watermark = imagecreatefromgif($url);
				break;

			case 'image/png':
				$watermark = imagecreatefrompng($url);
				break;

			default:
				return FALSE;
		}

		$watermark_width = imagesx($watermark);
		$watermark_height = imagesy($watermark);
		$img_width = imagesx($image);
		$img_height = imagesy($image);
		$size_type = $opt['df_watermark_image']['watermark_size_type'];

		if($size_type === 1) //custom
		{
			$w = $opt['df_watermark_image']['absolute_width'];
			$h = $opt['df_watermark_image']['absolute_height'];
		}
		elseif($size_type === 2) //scale
		{
			$ratio = $img_width * $opt['df_watermark_image']['width'] / 100 / $watermark_width;

			$w = (int)($watermark_width * $ratio);
			$h = (int)($watermark_height * $ratio);

			//if watermark scaled height is bigger then image watermark
			if($h > $img_height)
			{
				$w = (int)($img_height * $w / $h);
				$h = $img_height;
			}
		}
		else //original
		{
			$w = $watermark_width;
			$h = $watermark_height;
		}

		switch($opt['df_watermark_image']['position'])
		{
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

		$dest_x += $opt['df_watermark_image']['offset_width'];
		$dest_y += $opt['df_watermark_image']['offset_height'];

		$this->imagecopymerge_alpha($image, $this->resize($watermark, $url, $w, $h, $watermark_file_info), $dest_x, $dest_y, 0, 0, $w, $h, $opt['df_watermark_image']['transparent']);

		return $image;
	}


	/**
	 * Creates new image
	*/
	private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
	{
		//creating a cut resource
		$cut = imagecreateTRUEcolor($src_w, $src_h);

		//copying relevant section from background to the cut resource
		imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

		//copying relevant section from watermark to the cut resource
		imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

		//insert cut resource to destination image
		imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
	}


	/**
	 * Resizes image
	*/
	private function resize($im, $path, $nWidth, $nHeight, $imgInfo)
	{
		$newImg = imagecreateTRUEcolor($nWidth, $nHeight);

		//check if this image is PNG, then set if transparent
		if($imgInfo[2] === 3)
		{
			imagealphablending($newImg, FALSE);
			imagesavealpha($newImg, TRUE);
			$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
			imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
		}

		imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);

		return $newImg;
	}


	/**
	* Get image resource accordingly to mimetype
	*
	* @param string $filepath
	* @param string $mime_type
	* @return resource
	*/
	private function get_image_resource($filepath, $mime_type)
	{
		switch($mime_type)
		{
			case 'image/jpeg':
			case 'image/pjpeg':
				return imagecreatefromjpeg($filepath);

			case 'image/png':
				$res = imagecreatefrompng($filepath);
				$transparent = imagecolorallocatealpha($res, 255, 255, 254, 127);
				imagefilledrectangle($res, 0, 0, imagesx($res), imagesy($res), $transparent);
				return $res;

			default:
				return FALSE;
		}
	}


	/**
	 * Save image from image resource
	 *
	 * @param resource $image
	 * @param string $mime_type
	 * @param string $filepath
	 * @return boolean
	 */
	private function save_image_file($image, $mime_type, $filepath, $quality)
	{
		switch($mime_type)
		{
			case 'image/jpeg':
			case 'image/pjpeg':
				imagejpeg($image, $filepath, $quality);
				break;

			case 'image/png':
				imagepng($image, $filepath, (int)round(9 * $quality / 100));
				break;
		}
	}
}

$image_watermark = new Image_Watermark();
?>