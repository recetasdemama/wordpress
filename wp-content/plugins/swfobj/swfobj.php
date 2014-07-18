<?php
/*
Plugin Name: SwfObj
Plugin URI: http://orangesplotch.com/blog/swfobj/
Description: Easily insert Flash media using the media toolbar and shortcode. Uses the SWF Object 2.2 library for greater browser compatability.
Version: 1.0.2
Author: Matt Carpenter
Author URI: http://orangesplotch.com/
*/


	// BEGIN - Pre-2.9 compatibility, set WP_PLUGIN_DIR variable
	if ( ! function_exists( 'is_ssl' ) ) {
		function is_ssl() {
			if ( isset($_SERVER['HTTPS']) ) {
				if ( 'on' == strtolower($_SERVER['HTTPS']) )
					return true;
				if ( '1' == $_SERVER['HTTPS'] )
					return true;
			} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
				return true;
			}
			return false;
		}
	}
	if ( version_compare( get_bloginfo( 'version' ) , '3.0' , '<' ) && is_ssl() ) {
		$wp_content_url = str_replace( 'http://' , 'https://' , get_option( 'siteurl' ) );
	} else {
		$wp_content_url = get_option( 'siteurl' );
	}
	$wp_content_url .= '/wp-content';
	$wp_content_dir = ABSPATH . 'wp-content';
	$wp_plugin_url = $wp_content_url . '/plugins';
	$wp_plugin_dir = $wp_content_dir . '/plugins';
	$wpmu_plugin_url = $wp_content_url . '/mu-plugins';
	$wpmu_plugin_dir = $wp_content_dir . '/mu-plugins';
	// END - Pre-2.9 compatibility, set WP_PLUGIN_DIR variable


if (!class_exists("SwfObj")) {

class SwfObj {

	var $registered_objects; // holds all swf objects in the content
	var $flag_static_embed   = true; // whether to embed swf using dynamic or static method
	var $admin_options_saved = 'SwfObjAdminOptions';

	// Constructor
	function SWFObj() {
		load_plugin_textdomain( 'swfobj' );

		global $registered_objects;
		global $flag_static_embed;
		$registered_objects = array();
	}

	// Return an array of options
	function get_options() {
		$admin_options = array( 'height' => '300',
		                        'width' => '400',
		                        'alt' => '<p>'.__('The Flash plugin is required to view this object.', 'swfobj').'</p>',
		                        'required_player_version' => '8.0.0',
	 	                        'express_install_swf' => plugins_url().'/swfobj/expressInstall.swf',
		                        'allowfullscreen' => 'false',
		                        'wmode' => 'window',
	 	                        'dynamic_embed' => 'false' );
		$saved_options = get_option($this->admin_options_saved);
		if (!empty($saved_options)) {
			foreach($saved_options as $key => $val) {
				$admin_options[$key] = $val;
			}
		}
		update_option($this->admin_options_saved, $admin_options);
		return $admin_options;
	}

	function init() {
		// Wordpress 3.0 uses version 2.2 of swfobject.js
		if ( version_compare( get_bloginfo( 'version' ) , '3.0' , '<' ) ) {
			wp_enqueue_script('swfobject');
		}
		else {
			// Wordpress < 3.0 uses the older swfobject.js version (2.1)
			// Use the plugin version of swfobject as it is current
			wp_deregister_script('swfobject');
			wp_register_script('swfobject', plugins_url().'/swfobj/swfobject.js', false, NULL, true);
			wp_enqueue_script('swfobject');
		}
	}

	function activate() {
		$this->get_options();
	}

	// Add Javascript to end of page to register all swf objects.
	function swfobj_footer() {
		global $registered_objects;
		global $flag_static_embed;

		// register any swf files on the page
		if (count($registered_objects) > 0) {
			echo '
			<!-- SwfObj: Register SWFs on this page. -->
			<script type="text/javascript">';

			foreach ($registered_objects as $swf) {
				if ($swf['embed'] == 'static') {
					// This swfobj is statically embedded. Simply register the div.
					echo '
					swfobject.registerObject("'.$swf['id'].'", "'.$swf['required_player_version'].'", "'.$swf['express_install_swf'].'");';
				}
				elseif ($swf['embed'] == 'dynamic') {
					// This swfobj is dynamically embedded. Register and set all swfobj properties.
 					$flashvars  = '';
					$params     = '';
					$attributes = '';

					if ( $swf['flashvars'] && count($swf['flashvars']) > 0 ){
						foreach ($swf['flashvars'] as $key => $var) {
							$flashvars .= "\n					  $key: '".str_replace("\n",'\n',$var)."',";
						}
						$flashvars = substr($flashvars, 0, -1); // take off the last comma
					}
					if ( $swf['params'] && count($swf['params']) > 0 ){
						foreach ($swf['params'] as $key => $var) {
							$params .= "\n					  $key: '$var',";
						}
						$params = substr($params, 0, -1); // take off the last comma
					}
					if ( $swf['attributes'] && count($swf['attributes']) > 0 ){
						foreach ($swf['attributes'] as $key => $var) {
							$attributes .= "\n					  $key: '$var',";
						}
						$attributes = substr($attributes, 0, -1); // take off the last comma
					}

					echo '
					'.$swf['id'].'_flashvars  = {'.$flashvars.' };
					'.$swf['id'].'_params     = {'.$params.' };
					'.$swf['id'].'_attributes = {'.$attributes.' };

					swfobject.embedSWF("'.$swf['swfUrl'].'", "'.$swf['id'].'", "'.$swf['width'].'", "'.$swf['height'].'", "'.$swf['version'].'", "'.$swf['expressInstallSwfurl'].'", '.$swf['id'].'_flashvars, '.$swf['id'].'_params, '.$swf['id'].'_attributes);';
				}
			}
			echo '
			</script>'."\n";
		}
	}

	// [swfobj] shortcode handler
	function swfobj_func($atts, $content=null) {
		global $registered_objects;
		global $flag_static_embed;
		$defaults = $this->get_options();

		extract(shortcode_atts(array( 'src' => '',
		                              'width' => $defaults['width'],
		                              'height' => $defaults['height'],
		                              'id' => 'swfobj_'.count($registered_objects),
																	'alt' => $defaults['alt'],
		                              'name' => false,
		                              'class' => false,
		                              'align' => false,
		                              'required_player_version' => $defaults['required_player_version'],
		                              'express_install_swf' => $defaults['express_install_swf'],
		                              'quality' => false,
		                              'bgcolor' => false,
		                              'getvars' => false,
		                              'scale' => false,
		                              'salign' => false,
		                              'wmode' => $defaults['wmode'],
		                              'base' => false,
		                              'allownetworking' => false,
		                              'allowscriptaccess' => false,
		                              'callbackFn' => false,
		                              // The following parameters are true/false only
		                              // TODO: Check if they are set to true or false, if not, ignore them?
		                              'allowfullscreen' => $defaults['allowfullscreen'],
		                              'flashvars' => false,
		                              'loop' => false,
		                              'menu' => false,
		                              'play' => false,
		                              'swliveconnect' => false,
		                              'seamlesstabbing' => false,
		                              'devicefont' => false,
		                              'dynamic_embed' => $defaults['dynamic_embed'] ), $atts));

		// pull alt from inner content if it exists
		if ($content !== null) {
				 $alt = $content;
		}

		$extraparams = array( 'align' => false,
		                      'allowfullscreen' => 'false',
		                      'bgcolor' => false,
		                      'getvars' => false,
		                      'quality' => false,
		                      'flashvars' => false,
		                      'name' => false,
		                      'scale' => false,
		                      'salign' => false,
		                      'loop' => false,
		                      'menu' => false,
		                      'play' => false,
		                      'wmode' => 'window',
		                      'base' => false,
		                      'swliveconnect' => false,
		                      'seamlesstabbing' => false,
		                      'allownetworking' => false,
		                      'allowscriptaccess' => false,
		                      'devicefont' => false,
		                      'callbackFn' => false );
		$params     = '';
		$attributes = '';
    
		$flag_static_embed = !($dynamic_embed == 'true');
		if ($flag_static_embed) {
			// SWF is embedded statically (directly in the HTML)
    
			// loop through all params and get value
			foreach( $extraparams as $param => $default ){
				if( ${$param} !== false && ${$param} != $default ){
					$params     .= "\n      ".'<param name="'.$param.'" value="'.${$param}.'" />';
					$attributes .= ' '.$param.'="'.${$param}.'"';
				}
			}

			// Add this object to the array so it will be registered in the header
			$registered_objects[]  = array('id' => $id, 'required_player_version' => $required_player_version, 'express_install_swf' => $express_install_swf, 'embed' => 'static');

			$swfobj = '
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="'.$id.'" width="'.$width.'" height="'.$height.'"'.($class?' class="'.$class.'"':'').($align?' align="'.$align.'"':'').($name?' name="'.$name.'"':'').'>
      <param name="movie" value="'.$src.(($getvars)?'?'.$getvars.'"':'').'" />'.$params.'
      <!--[if !IE]>-->
      <object type="application/x-shockwave-flash" data="'.$src.(($getvars)?'?'.$getvars.'"':'').'" width="'.$width.'" height="'.$height.'"'.$attributes.'>
      <!--<![endif]-->
        '.$alt.'
      <!--[if !IE]>-->
      </object>
      <!--<![endif]-->
    </object>
';
		}
		else {
			// SWF is embedded dynamically (using Javascript)
			// Only place the alternative content on the page

			// loop through all params and get values
			$params     = array();
			$flash_vars = array();
			foreach( $extraparams as $param => $default ){
				// make sure you aren't adding anything you don't need here
				// params are different in the dynamic version
				if( ${$param} !== false && ${$param} != $default ){
					// extract flashvars out separately
					if ( $param == 'flashvars' ) {
	 					$tmpvars = explode('&#038;', ${$param});
						if ( count($tmpvars) < 2 ) {
							// make sure they didn't separate vars with &amp; instead of $#038;
							$tmpvars = explode('&amp;', ${$param});
						}
	 					foreach ($tmpvars as $tmpvar) {
	 						$tmpvararray = explode('=', $tmpvar);
							$flash_vars[ $tmpvararray[0] ] = $tmpvararray[1];
	 					}
					}
					else {
						$params[$param] = ${$param};
					}
				}
			}

			// Get all attribute values
			$attributes = array();
			if ($name) {
				$attributes['name'] = $name;
			}

			// Add this object to the array so it will be registered
			// (swfUrl, id, width, height, version, expressInstallSwfurl, flashvars, params, attributes, callbackFn)
			$registered_objects[]  = array( 'swfUrl' => $src,
			                                'id' => $id,
			                                'width' => $width,
			                                'height' => $height,
			                                'version' => $required_player_version,
			                                'expressInstallSwfurl' => $express_install_swf,
			                                'flashvars' => $flash_vars,
			                                'params' => $params,
			                                'attributes' => $attribute,
			                                'callbackFn' => $callbackFn,
			                                'embed' => 'dynamic' );

			$swfobj = '
    <div id="'.$id.'">
      '.$alt.'
    </div>
';
		}

		return $swfobj;
	}

	function swfobj_options_page() {
		$options = $this->get_options();
		if ($_POST['options_update']) {
			// Update current options to the values submitted
			foreach ($options as $option => $value) {
				if (isset($_POST[$option])) {
					$options[$option] = $_POST[$option];
				}
			}
			update_option($this->admin_options_saved, $options);
			?>
			<div class="updated"><p><strong><?php _e('Options Updated', 'swfobj'); ?></strong></p></div>
			<?php
		}

		?>
<style type="text/css">
.form-table tr.odd { background: #f3F9Ff; }
</style>
<div class="wrap">
<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<h2><?php _e('SwfObj Default Settings', 'swfobj'); ?></h2>
      <input type="hidden" name="options_update" value="1" />

      <table class="form-table">
      <tr class="odd">
        <th scope="row" valign="top"><?php _e('Default Embedding Mode', 'swfobj'); ?></th>
        <td>
	    <label for="dynamic_embed_no"><input type="radio" id="dynamic_embed_no" name="dynamic_embed" value="false"<?php if ($options['dynamic_embed'] == 'false'): ?> checked="checked"<?php endif; ?> /> <?php _e('Static Publishing', 'swfobj'); ?></label><br />
	    <label for="dynamic_embed_yes"><input type="radio" id="dynamic_embed_yes" name="dynamic_embed" value="true"<?php if ($options['dynamic_embed'] == 'true'): ?> checked="checked"<?php endif; ?> /> <?php _e('Dynamic Publishing', 'swfobj'); ?></label>
        <td>
          <ol>
            <li><strong>Static publishing: </strong><em><?php _e('embeds both Flash content and alternative content using standards compliant markup, and uses JavaScript to resolve the issues that markup alone cannot solve', 'swfobj'); ?></em></li>
            <li><strong>Dynamic publishing: </strong><em><?php _e('is based on marked up alternative content and uses JavaScript to replace this content with Flash content if the minimal Flash Player version is installed and enough JavaScript support is available (similar like previous versions of SWFObject and UFO) ', 'swfobj'); ?></em></li>
          </ol>
        <!-- http://code.google.com/p/swfobject/wiki/documentation#Should_I_use_the_static_or_dynamic_publishing_method? --></td>
      </tr>
      <tr>
        <th scope="row" valign="top"><?php _e('Default Width', 'swfobj'); ?></th>
        <td><input type="text" name="width" value="<?php echo $options['width']; ?>" size="40" /></td>
        <td><em><?php _e('Default width of embedded Flash content.', 'swfobj'); ?></em></td>
      </tr>
      <tr class="odd">
        <th scope="row" valign="top"><?php _e('Default Height', 'swfobj'); ?></th>
        <td><input type="text" name="height" value="<?php echo $options['height']; ?>" size="40" /></td>
        <td><em><?php _e('Default height of embedded Flash content.', 'swfobj'); ?></em></td>
      </tr>
      <tr>
        <th scope="row" valign="top"><?php _e('Alternative Content', 'swfobj'); ?></th>
        <td><input type="text" name="alt" value="<?php echo $options['alt']; ?>" size="40" /></td>
        <td><em><?php _e('Alternative HTML content to display if Flash plugin is not installed in the browser.', 'swfobj'); ?></em></td>
      </tr>
      <tr class="odd">
        <th scope="row" valign="top"><?php _e('Required Flash Player', 'swfobj'); ?></th>
        <td><input type="text" name="required_player_version" value="<?php echo $options['required_player_version']; ?>" size="40" /></td>
        <td><em><?php _e('Default minimum Flash player required by the browser.', 'swfobj'); ?></em></td>
      </tr>
      <tr>
        <th scope="row" valign="top"><?php _e('Object CSS Class', 'swfobj'); ?></th>
        <td><input type="text" name="class" value="<?php echo $options['class']; ?>" size="40" /></td>
        <td><em><?php _e('The CSS class to apply to the embedded Flash object.', 'swfobj'); ?></em></td>
      </tr>
      <tr class="odd">
        <th scope="row" valign="top"><?php _e('Express Install Swf', 'swfobj'); ?></th>
        <td><input type="text" name="express_install_swf" value="<?php echo $options['express_install_swf']; ?>" size="40" /></td>
        <td><em><?php _e('Swf shown when viewer needs to upgrade their player.', 'swfobj'); ?></em></td>
      </tr>
      <tr>
        <th scope="row" valign="top"><?php _e('Allow Fullscreen Mode', 'swfobj'); ?></th>
        <td>
	    <label for="allowfullscreen_yes"><input type="radio" id="allowfullscreen_yes" name="allowfullscreen" value="true"<?php if ($options['allowfullscreen'] == 'true'): ?> checked="checked"<?php endif; ?> /> <?php _e('Yes', 'swfobj'); ?></label> &nbsp; &nbsp; &nbsp;
	    <label for="allowfullscreen_no"><input type="radio" id="allowfullscreen_no" name="allowfullscreen" value="false"<?php if ($options['allowfullscreen'] == 'false'): ?> checked="checked"<?php endif; ?> /> <?php _e('No', 'swfobj'); ?></label>
        <td><em><?php _e('Allow fullscreen mode by default.', 'swfobj'); ?></em></td>
      </tr>
      <tr class="odd">
        <th scope="row" valign="top"><?php _e('Default wmode', 'swfobj'); ?></th>
        <td>
          <select name="wmode">
            <option value="window"<?php if($options['wmode'] == 'window'): ?> selected="selected"<?php endif; ?>>window</option>
            <option value="opaque"<?php if($options['wmode'] == 'opaque'): ?> selected="selected"<?php endif; ?>>opaque</option>
            <option value="transparent"<?php if($options['wmode'] == 'transparent'): ?> selected="selected"<?php endif; ?>>transparent</option>
          </select>
        </td>
        <td><em><?php _e('If you are experiencing trouble with Flash content overlapping HTML layers, set this to "opaque".', 'swfobj'); ?></em></td>
      </tr>
      </table>

      <p class="submit">
      	 <input type="submit" name="Submit" value="<?php _e('Update Defaults', 'swfobj'); ?>" />
      </p>
</form>
</div>
		<?php
	}

	function addMediaButton() {
 		global $post_ID, $temp_ID;
		$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);
		$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";

		$media_swfobj_iframe_src = apply_filters('media_swfobj_iframe_src', "$media_upload_iframe_src&amp;type=flash");
		$media_swfobj_title = __('Add Flash content', 'swfobj');

		echo '<a href="'.$media_swfobj_iframe_src.'&amp;TB_iframe=true&amp;height=500&amp;width=640" class="thickbox" title="'.$media_swfobj_title.'"><img src="'.plugins_url().'/swfobj/media-button-flash.gif" alt="'.$media_swfobj_title.'"></a>';
	}

	function media_upload_flash() {
		if ( isset($_POST['html-upload']) && !empty($_FILES) ) {
			// Upload File button was clicked
			$id = media_handle_upload('async-upload', $_REQUEST['post_id']);
			unset($_FILES);
			if ( is_wp_error($id) ) {
				$errors['upload_error'] = $id;
				$id = false;
			}
		}

		if ( !empty($_POST['insertonlybutton']) ) {

			$src      = $_POST['insertonly']['src'];
			$title    = stripslashes( htmlspecialchars ($_POST['insertonly']['post_title'], ENT_QUOTES));
			$alt      = $_POST['insertonly']['post_content'];

			if ( !empty($src) && !strpos($src, '://') ) {
				$src = "http://$src";
			}

			// append any additional properties passed to the object.
			// I don't like that I'm doing the same thing here in two places
			// TODO: Need to make this so it only happens in one location.
			$extras   = '';
			if ( !empty($_POST['insertonly']['width']) && intval($_POST['insertonly']['width']) ) {
				$extras .= ' width="'.stripslashes( htmlspecialchars ($_POST['insertonly']['width'], ENT_QUOTES)).'"';
			}
			if ( !empty($_POST['insertonly']['height']) && intval($_POST['insertonly']['height']) ) {
				$extras .= ' height="'.stripslashes( htmlspecialchars ($_POST['insertonly']['height'], ENT_QUOTES)).'"';
			}
			if ( !empty($_POST['insertonly']['id']) ) {
				$extras .= ' id="'.stripslashes( htmlspecialchars ($_POST['insertonly']['id'], ENT_QUOTES)).'"';
			}
			if ( !empty($_POST['insertonly']['name']) ) {
				$extras .= ' name="'.stripslashes( htmlspecialchars ($_POST['insertonly']['name'], ENT_QUOTES)).'"';
			}
			if ( !empty($_POST['insertonly']['class']) ) {
				$extras .= ' class="'.stripslashes( htmlspecialchars ($_POST['insertonly']['class'], ENT_QUOTES)).'"';
			}
			if ( isset($_POST['insertonly']['align']) ) {
				$extras .= ' align="'.$_POST['insertonly']['align'].'"';
			}
			if ( isset($_POST['insertonly']['allowfullscreen']) ) {
				$extras .= ' allowfullscreen="'.$_POST['insertonly']['allowfullscreen'].'"';
			}
			if ( !empty($_POST['insertonly']['required_player_version']) ) {
				$extras .= ' required_player_version="'.stripslashes( htmlspecialchars ($_POST['insertonly']['required_player_version'], ENT_QUOTES)).'"';
			}

			if ( !empty($src) ) {
				$html  = '[swfobj src="'.$src.'"'.$extras.( ($alt != '') ? ']'.$alt.'[/swfobj]' : '] ');
			}

			return media_send_to_editor($html);
		}

		if ( !empty($_POST) ) {
			$return = media_upload_form_handler();

			if ( is_string($return) )
				return $return;
			if ( is_array($return) )
				$errors = $return;
		}

		if ( isset($_POST['save']) ) {
			$errors['upload_notice'] = __('Saved.');
		}

		if ( isset($_GET['tab']) && $_GET['tab'] == 'type_url' ) {
			return wp_iframe( 'media_upload_type_url_form', 'flash', $errors, $id );
		}

		return wp_iframe( 'media_upload_type_form', 'flash', $errors, $id );
	}

	function modify_post_mime_types($post_mime_types) {
		if ( function_exists( '_n_noop' ) ) :
			$post_mime_types['application/x-shockwave-flash'] = array(__('Flash', 'swfobj'), __('Manage Flash', 'swfobj'), _n_noop('Flash (%s)', 'Flash (%s)', 'swfobj'));
		else :
			$post_mime_types['application/x-shockwave-flash'] = array(__('Flash', 'swfobj'), __('Manage Flash', 'swfobj'), __ngettext_noop('Flash (%s)', 'Flash (%s)', 'swfobj'));
		endif;
		return $post_mime_types;
	}

	function flash_media_send_to_editor($html) {
		if ( isset($_POST['send']) ) {

			$keys     = array_keys($_POST['send']);
			$send_id  = (int) array_shift($keys);
			$flashobj = $_POST['attachments'][$send_id];

			// only process Flash objects here
			if ( isset($flashobj['media_type']) && $flashobj['media_type'] == 'application/x-shockwave-flash' ) {
			
			$src      = $flashobj['src'];
			$title    = stripslashes( htmlspecialchars ($flashobj['post_title'], ENT_QUOTES));
			$alt      = $flashobj['post_content'];

			// append any additional properties passed to the object.
			$extras   = '';
			if ( !empty($flashobj['width']) && intval($flashobj['width']) ) {
				$extras .= ' width="'.stripslashes( htmlspecialchars ($flashobj['width'], ENT_QUOTES)).'"';
			}
			if ( !empty($flashobj['height']) && intval($flashobj['height']) ) {
				$extras .= ' height="'.stripslashes( htmlspecialchars ($flashobj['height'], ENT_QUOTES)).'"';
			}
			if ( !empty($flashobj['id']) ) {
				$extras .= ' id="'.stripslashes( htmlspecialchars ($flashobj['id'], ENT_QUOTES)).'"';
			}
			if ( !empty($flashobj['name']) ) {
				$extras .= ' name="'.stripslashes( htmlspecialchars ($flashobj['name'], ENT_QUOTES)).'"';
			}
			if ( !empty($flashobj['class']) ) {
				$extras .= ' class="'.stripslashes( htmlspecialchars ($flashobj['class'], ENT_QUOTES)).'"';
			}
			if ( isset($flashobj['align']) ) {
				$extras .= ' align="'.$flashobj['align'].'"';
			}
			if ( isset($flashobj['allowfullscreen']) ) {
				$extras .= ' allowfullscreen="'.$flashobj['allowfullscreen'].'"';
			}
			if ( !empty($flashobj['required_player_version']) ) {
				$extras .= ' required_player_version="'.stripslashes( htmlspecialchars ($flashobj['required_player_version'], ENT_QUOTES)).'"';
			}

			$html  = '[swfobj src="'.$src.'"'.$extras.( ($alt != '') ? ']'.$alt.'[/swfobj]' : '] ');
			}
		}
		return $html;
	}

	function flash_attachment_fields_to_edit($form_fields, $post) {
		if ( substr($post->post_mime_type, -5) == 'flash' ) {
			$form_fields['post_title']['required'] = true;
			unset( $form_fields['post_excerpt'] );
			unset( $form_fields['url'] );

			$form_fields['post_content']['label']   = __('Alternate html');
			$form_fields['post_content']['helps'][] = __('Displayed when Flash is unavailable, e.g. "&lt;p&gt;Cool Flash game.&lt;/p&gt;"');

			$form_fields['size'] = array( 'label' => __('Size').' <em>'.__('width/height').'</em>',
			                              'input' => 'html',
						      'html'  => '<input id="attachments['.$post->ID.'][width]" name="attachments['.$post->ID.'][width]" value="" type="text" class="halfpint">
			                                         <input id="attachments['.$post->ID.'][height]" name="attachments['.$post->ID.'][height]" value="" type="text" class="halfpint">' );

			// Advanced options
			//   sneaking in a hidden media_type input so only flash items are processed 
			//   by this plugin when they are inserted in the post.
			$form_fields['advanced_open'] = array( 'label' => __('Advanced Options'),
			                                       'input' => 'html',
							       'html'  => '<div id="advanced-'.$post->ID.'" class="toggle-advanced">'.__('Advanced Options').'</div></td></tr></tbody><tbody id="tbody-advanced-'.$post->ID.'" class="swfobj-advanced-options"><tr class="hidden"><td colspan="2"><input type="hidden" name="attachments['.$post->ID.'][media_type]" value="application/x-shockwave-flash" /><input type="hidden" name="attachments['.$post->ID.'][src]" value="'.$post->guid.'" />' );
			$form_fields['align'] = array(
				'label' => __('Alignment'),
				'input' => 'html',
				'html'  => "
					<input type='radio' name='attachments[$post->ID][align]' id='swfobj-align-none-$post->ID' value='none' />
					<label for='swfobj-align-none-$post->ID' class='align image-align-none-label'>" . __('None') . "</label>
					<input type='radio' name='attachments[$post->ID][align]' id='swfobj-align-left-$post->ID' value='left' />
					<label for='swfobj-align-left-$post->ID' class='align image-align-left-label'>" . __('Left') . "</label>
					<input type='radio' name='attachments[$post->ID][align]' id='swfobj-align-center-$post->ID' value='center' />
					<label for='swfobj-align-center-$post->ID' class='align image-align-center-label'>" . __('Center') . "</label>
					<input type='radio' name='attachments[$post->ID][align]' id='swfobj-align-right-$post->ID' value='right' />
					<label for='swfobj-align-right-$post->ID' class='align image-align-right-label'>" . __('Right') . "</label>\n",
			);

			$form_fields['id']    = array( 'label' => __('ID') );
			$form_fields['name']  = array( 'label' => __('Name') );
			$form_fields['class'] = array( 'label' => __('Class') );

			$form_fields['required_player_version']['label']   = __('Required Player');
			$form_fields['required_player_version']['helps'][] = __('Minimum Flash player required to play this object.');

			$form_fields['allowfullscreen'] = array ( 'label' => __('Allow Fullscreen Mode'),
							  	  'input' => 'html',
								  'html'  => '
								  	     <label for="attachments-allowfullscreen-'.$post->ID.'-true">'.__('Yes').'</label>
									     <input type="radio" id="attachments-allowfullscreen-'.$post->ID.'-true" name="attachments['.$post->ID.'][allowfullscreen]" value="true" />
								  	     <label for="attachments-allowfullscreen-'.$post->ID.'-false">'.__('No').'</label>
									     <input type="radio" id="attachments-allowfullscreen-'.$post->ID.'-false" name="attachments['.$post->ID.'][allowfullscreen]" value="false" />' );

			$form_fields['advanced_close'] = array( 'label' => __('Advanced Options'),
			                                       'input' => 'html',
							       'html'  => '</tbody><tbody><tr class="hidden"><td colspan="2">' );
		}
		return $form_fields;
	}

	function swfobj_upload_header () { ?>
		
<!-- SwfObj Plugin -->
<style type="text/css">
.swfobj-advanced-options {
border-top: 1px solid #B6B6B6; }

.swfobj-advanced-options th {
color: #444470; }

.swfobj-advanced-options .hidden,
.advanced_open th span,
tr.advanced_close {
display: none; }

.advanced_open td {
font-style: italic;
font-weight: bold;
color: #1D3C7B;
cursor: pointer; }

input.halfpint {
width: 222px !important; }
</style>
<script type="text/javascript">
<!--
jQuery(function($){
	$('.swfobj-advanced-options').hide();
	$('.toggle-advanced').html('<?php _e('Show Advanced Options'); ?>');
	$('.toggle-advanced').click(function() {
		postID = $(this).attr('id');
		if ( '<?php _e('Show Advanced Options'); ?>' == $(this).text() ) {
			$('#tbody-'+postID).show();
			// $('#tbody-'+postID).slideDown('fast');
			$(this).html('<?php _e('Hide Advanced Options'); ?>');
		}
		else {
			$('#tbody-'+postID).hide();
			// $('#tbody-'+postID).slideUp('fast');
			$(this).html('<?php _e('Show Advanced Options'); ?>');
		}
	});
});
-->
</script>
<?php
	}

} // end SwfObj class

}

if (class_exists("SwfObj")) {
	$swfobj = new SwfObj();
}

function type_form_flash() {
	return '

	<table class="describe"><tbody>
		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="insertonly[src]">' . __('Flash URL') . '</label></span>
				<span class="alignright"><abbr title="required" class="required">*</abbr></span>
			</th>
			<td class="field"><input id="insertonly[src]" name="insertonly[src]" value="" type="text"></td>
		</tr>
		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="insertonly[post_title]">' . __('Title') . '</label></span>
			</th>
			<td class="field"><input id="insertonly[post_title]" name="insertonly[post_title]" value="" type="text"></td>
		</tr>
		<tr><td></td><td class="help">' . __('Link text, e.g. "Lucy on YouTube"') . '</td></tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="button" name="insertonlybutton" value="' . esc_attr__('Insert into Post') . '" />
			</td>
		</tr>
	</tbody></table>
';
}

// Function changed in WP 2.7
function type_url_form_flash() {
    //return type_form_flash();

return '
	<table class="describe"><tbody>
		<tr>
			<th valign="top" scope="row" class="label">
				<span class="alignleft"><label for="insertonly[src]">' . __('Flash URL') . '</label></span>
				<span class="alignright"><abbr title="required" class="required">*</abbr></span>
			</th>
			<td class="field"><input id="insertonly[src]" name="insertonly[src]" value="" type="text"></td>
		</tr>

		<tr class="post_title form-required">
			<th valign="top" scope="row" class="label"><label for="insertonly[post_title]"><span class="alignleft">Title</span><span class="alignright"><abbr title="required" class="required">*</abbr></span><br class="clear" /></label></th>
			<td class="field"><input type="text" id="insertonly[post_title]" name="insertonly[post_title]" value="" aria-required="true" /></td>
		</tr>
		<tr class="post_content">
			<th valign="top" scope="row" class="label"><label for="insertonly[post_content]"><span class="alignleft">Alternate html</span><span class="alignright"></span><br class="clear" /></label></th>

			<td class="field"><textarea type="text" id="insertonly[post_content]" name="insertonly[post_content]"></textarea><p class="help">Displayed when Flash is unavailable, e.g. "&lt;p&gt;Cool Flash game.&lt;/p&gt;"</p></td>
		</tr>
		<tr class="size">
			<th valign="top" scope="row" class="label"><label for="insertonly[size]"><span class="alignleft">Size <em>width/height</em></span><span class="alignright"></span><br class="clear" /></label></th>
			<td class="field"><input id="insertonly[width]" name="insertonly[width]" value="" type="text" class="halfpint">

			                                         <input id="insertonly[height]" name="insertonly[height]" value="" type="text" class="halfpint"></td>
		</tr>
		<tr class="advanced_open">
			<th valign="top" scope="row" class="label"><label for="insertonly[advanced_open]"><span class="alignleft">Advanced Options</span><span class="alignright"></span><br class="clear" /></label></th>
			<td class="field"><div id="advanced-insertonly" class="toggle-advanced">Advanced Options</div></td></tr></tbody><tbody id="tbody-advanced-insertonly" class="swfobj-advanced-options"><tr class="hidden"><td colspan="2"><input type="hidden" name="insertonly[media_type]" value="application/x-shockwave-flash" /></td>
		</tr>
		<tr class="align">
			<th valign="top" scope="row" class="label"><label for="insertonly[align]"><span class="alignleft">Alignment</span><span class="alignright"></span><br class="clear" /></label></th>

			<td class="field">
					<input type="radio" name="insertonly[align]" id="swfobj-align-none-insertonly" value="none" />
					<label for="swfobj-align-none-insertonly" class="align image-align-none-label">None</label>
					<input type="radio" name="insertonly[align]" id="swfobj-align-left-insertonly" value="left" />

					<label for="swfobj-align-left-insertonly" class="align image-align-left-label">Left</label>
					<input type="radio" name="insertonly[align]" id="swfobj-align-center-insertonly" value="center" />
					<label for="swfobj-align-center-insertonly" class="align image-align-center-label">Center</label>
					<input type="radio" name="insertonly[align]" id="swfobj-align-right-insertonly" value="right" />
					<label for="swfobj-align-right-insertonly" class="align image-align-right-label">Right</label>
</td>
		</tr>
		<tr class="id">
			<th valign="top" scope="row" class="label"><label for="insertonly[id]"><span class="alignleft">ID</span><span class="alignright"></span><br class="clear" /></label></th>
			<td class="field"><input type="text" id="insertonly[id]" name="insertonly[id]" value=""/></td>
		</tr>

		<tr class="name">
			<th valign="top" scope="row" class="label"><label for="insertonly[name]"><span class="alignleft">Name</span><span class="alignright"></span><br class="clear" /></label></th>
			<td class="field"><input type="text" id="insertonly[name]" name="insertonly[name]" value=""/></td>
		</tr>
		<tr class="class">
			<th valign="top" scope="row" class="label"><label for="insertonly[class]"><span class="alignleft">Class</span><span class="alignright"></span><br class="clear" /></label></th>
			<td class="field"><input type="text" id="insertonly[class]" name="insertonly[class]" value=""/></td>
		</tr>

		<tr class="required_player_version">
			<th valign="top" scope="row" class="label"><label for="insertonly[required_player_version]"><span class="alignleft">Required Player</span><span class="alignright"></span><br class="clear" /></label></th>
			<td class="field"><input type="text" id="insertonly[required_player_version]" name="insertonly[required_player_version]" value=""/><p class="help">Minimum Flash player required to play this object.</p></td>
		</tr>
		<tr class="allowfullscreen">
			<th valign="top" scope="row" class="label"><label for="insertonly[allowfullscreen]"><span class="alignleft">Allow Fullscreen Mode</span><span class="alignright"></span><br class="clear" /></label></th>
			<td class="field">

								  	     <label for="attachments-allowfullscreen-insertonly-true">Yes</label>
									     <input type="radio" id="attachments-allowfullscreen-insertonly-true" name="insertonly[allowfullscreen]" value="true" />
								  	     <label for="attachments-allowfullscreen-insertonly-false">No</label>
									     <input type="radio" id="attachments-allowfullscreen-insertonly-false" name="insertonly[allowfullscreen]" value="false" /></td>
		</tr>
		<tr class="advanced_close">
			<th valign="top" scope="row" class="label"><label for="insertonly[advanced_close]"><span class="alignleft">Advanced Options</span><span class="alignright"></span><br class="clear" /></label></th>

			<td class="field"></tbody><tbody><tr class="hidden"><td colspan="2"></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="button" name="insertonlybutton" value="' . esc_attr__('Insert into Post') . '" />
			</td>
		</tr>
	</tbody></table>
';

}

// Initialize the admin panel
if (!function_exists("swfobj_ap")) {
	function swfobj_ap() {
		global $swfobj;
		if (!isset($swfobj)) {
			return;
		}
		if (function_exists('add_options_page')) {
			add_options_page(__('SwfObj Default Settings', 'swfobj'), 'SwfObj', 8, basename(__FILE__), array(&$swfobj, 'swfobj_options_page'));
		}
	}
}

// Actions and Filters
if (isset($swfobj)) {

	// Actions
	add_action('wp_footer', array(&$swfobj, 'swfobj_footer'), 100);
	add_action('admin_menu', 'swfobj_ap', 100);
	add_action('init', array(&$swfobj, 'init'));
	add_action('activate_swfobj/swfobj.php',  array(&$swfobj, 'activate'));
	add_action('media_buttons', array(&$swfobj, 'addMediaButton'), 20);
	add_action('media_upload_flash', array(&$swfobj, 'media_upload_flash'));

	add_action("admin_head_media_upload_type_form", array(&$swfobj, 'swfobj_upload_header'), 50);
	add_action("admin_head", array(&$swfobj, 'swfobj_upload_header'), 50);

	// Filters
	add_filter('post_mime_types', array(&$swfobj, 'modify_post_mime_types'));
	add_filter('async_upload_flash', 'get_media_item', 10, 2);
	add_filter('media_send_to_editor', array(&$swfobj, 'flash_media_send_to_editor'));
	add_filter('attachment_fields_to_edit', array(&$swfobj, 'flash_attachment_fields_to_edit'), 10, 2);

	// Shortcodes
	// check if shortcodes exist just so this plugin doesn't kill WordPress on versions < 2.5
	if ( function_exists('add_shortcode') ) {
		add_shortcode('swfobj', array(&$swfobj, 'swfobj_func'));
		add_shortcode('flash', array(&$swfobj, 'swfobj_func'));
	}

}

?>