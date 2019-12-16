<?php

function synved_option_page_default_name($id)
{
	return 'page_settings';
}

function synved_option_page_default($id)
{
	$page = synved_option_page_default_name($id);
	
	return array('name' => $page, 'type' => 'options-page', 'label' => synved_option_label_from_id($id));
}

function synved_option_page_slug($id, $name, $item = null)
{
	if ($item == null)
	{
		$item = synved_option_item($id, $name);
	}
	
	$type = synved_option_item_type($item);
	$parent = synved_option_item_parent($item);

	if ($type == 'options-page')
	{
		global $synved_option_list;
		
		if (isset($synved_option_list[$id]['pages'][$name]['wp-page-slug']))
		{
			return $synved_option_list[$id]['pages'][$name]['wp-page-slug'];
		}
	}
	
	return null;
}
	
function synved_option_page_link_url($id, $name, $item = null)
{
	if ($item == null)
	{
		$item = synved_option_item($id, $name);
	}
	
	$type = synved_option_item_type($item);
	$parent = synved_option_item_parent($item);
	$slug = synved_option_page_slug($id, $name, $item);

	if ($type == 'options-page')
	{
		if ($slug != null)
		{
			return $parent . '?page=' . $slug;
		}
	}
	
	return null;
}

function synved_option_page_cb($id, $name, $item)
{
	$group = synved_option_group_default($id);
	$label = synved_option_item_label($item);
	$title = synved_option_item_title($item);
	$tip = synved_option_item_tip($item);
	$role = synved_option_item_role($item);
	$style = synved_option_item_style($item);
	
	if (!current_user_can($role))
	{
		wp_die(__('You do not have sufficient permissions to access this page.', 'synved-option'));
	}
	
	if ($title === null)
	{
		$title = $label;
	}
	
	$class = 'wrap';
	
	if ($style != null)
	{
		foreach ($style as $style_name)
		{
			$class .= ' ' . 'synved-option-style-' . $style_name;
		}
	}
?>
	<div class="<?php echo esc_attr($class); ?>">
		<div class="icon32" id="icon-options-general"><br/></div>
		<h2><?php echo $title; ?></h2>
		<p><?php echo $tip; ?></p>
		<form action="options.php" method="post">
		<?php settings_fields($group); ?>
		<?php 
			$page_slug = synved_option_page_slug($id, $name, $item);
			do_settings_sections( $page_slug );
		?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		<?php
			$render_fragment = synved_option_item_render_fragment($item);
			$out = null;
	
			if ($render_fragment != null)
			{
				$error = null;
				$new_out = null;
		
				try
				{
					$params = array();
					$new_out = $render_fragment->Invoke(array('page-submit-tail', '', $params, $name, $id, $item));
				}
				catch (Exception $ex)
				{
					$new_out = null;
			
					$error = $ex->getMessage();
				}
		
				if ($new_out !== null)
				{
					$out = $new_out;
				}
			}
			
			echo $out;
		?>
		</p>
		</form>
	</div>

    <script type="text/javascript">
        const SYNVED_DISABLE_FEATURE_URL = '<?php echo admin_url('options-general.php?page=synved_social_settings&accept-terms=no'); ?>';
        const SYNVED_ENABLE_FEATURE_URL = '<?php echo admin_url('options-general.php?page=synved_social_settings&accept-terms=yes'); ?>';

        jQuery(document).ready(function () {
            synved_switcher.init(<?php echo synved_option_get('synved_social', 'accepted_sharethis_terms'); ?>);
        });
    </script>

<?php
}

function synved_option_page_add($id, $name, $item)
{
	global $synved_option_list;

	define('SYNVEDOPTION', $id);
	define('SYNVEDNAME', $name);

	$type = synved_option_item_type($item);

	if ($type == 'options-page')
	{
		$label = synved_option_item_label($item);
		$tip = synved_option_item_tip($item);
		$parent = synved_option_item_parent($item);
		$role = synved_option_item_role($item);

		if ($label == null)
		{
			$label = $name;
		}
		
		$page_slug = $id . '_' . $name;

		$page = add_submenu_page(
			$parent,
			$label,
			$label,
			$role,
			$page_slug,
			function($name){ return synved_option_page_cb(SYNVEDOPTION, SYNVEDNAME, synved_option_item_find(SYNVEDOPTION, SYNVEDNAME) ); }
			);
		
		$synved_option_list[$id]['pages'][$name]['wp-page-slug'] = $page_slug;
		$synved_option_list[$id]['pages'][$name]['wp-page'] = $page;

		return $page;
	}
	
	return null;
}

function synved_option_page_add_cb()
{
	global $synved_option_list;
	
	if ($synved_option_list != null)
	{
		foreach ($synved_option_list as $id => $list)
		{
			$pages = $list['pages'];
		
			foreach ($pages as $name => $item)
			{
				synved_option_page_add($id, $name, $item);
			}
		}
	}
}

?>
