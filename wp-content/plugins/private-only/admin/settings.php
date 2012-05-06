<?php
/*
Private Only 2.5
Need a WP expert? Hire me : studio[at]pixert.com
*/
?>
<div style="float:right; width:33%;">

<div class="postbox open">

<h3>About The Author</h3>

<div class="inside">

	<ul>
    
    <li>
      <h4><?php _e('This is Private Only plugin for WordPress ver 2.0','private-only'); ?></h4>
      <p><?php _e('If you disable or enable feed, DO NOT FORGET TO REFRESH YOUR BROWSER CACHE AFTER ACTIVATE PRIVATE ONLY or DISABLE FEED','private-only'); ?></p>
    </li>    
        
		<li><a href="http://twitter.com/katemag" title="Kate Mag on Twitter"><?php _e('Follow me on twitter','private-only'); ?></a>.</li>
        
		<li>Need a WP expert? <a href="http://pixert.com/" title="Pixel Insert"><?php _e('Hire me','private-only'); ?></a>.</li>
		
		<li>Contributor : Ivan Ricotti</li>
        
	</ul>
    
</div>
</div>

</div> <!-- /float:right -->

<div style="float:left; width:66%;">

<div class="postbox open">

<h3><?php _e('Custom Login','private-only'); ?></h3>

</div>

<div class="postbox open">
<div class="inside">
	<table class="form-table">
   		<tr>
            <th>
            	<label for="<?php echo $data['public_pages']; ?>"><?php _e('Public Page:','private-only'); ?></label> 
            </th>
            <td>
            	<?php
            		$settings = get_option( 'po_login_settings' );
            		$selected = $settings['public_pages'];
            		$args = array(
            			'id' => $data['public_pages'],
            			'name' => $data['public_pages'],
            			'selected' => $selected,
            			'show_option_none' => '-',
            			'option_none_value' => ''
            		);
            		wp_dropdown_pages( $args ); 
            	?>
                <br /><?php _e('Define the public page','private-only'); ?><br />
            </td>
   		</tr>
    </table>

</div>
</div>

<div class="postbox open">
<div class="inside">
	<table class="form-table">
   		<tr>
            <th>
            	<label for="<?php echo $data['po_logot']; ?>"><?php _e('Logo:','private-only'); ?></label> 
            </th>
            <td>
               <input id="<?php echo $data['po_hlogo']; ?>" name="<?php echo $data['po_logo']; ?>" value="<?php echo $val['po_logo']; ?>" size="40" /><br />
                <?php _e('Upload an image with Media Library or FTP and put the full path here, http://yourdomainname.com/logo.jpg','private-on;y'); ?><br />
                <?php _e('We do not provide upload tool here, because it is free for you upload it wherever you want to','private-only'); ?><br />
            </td>
   		</tr>
    </table>

</div>
</div>

<div class="postbox open">
<div class="inside">
	<table class="form-table">
   		<tr>
            <th>
            	<label for="<?php echo $data['use_wp_logo']; ?>"><?php _e('Use WordPress logo','private-only'); ?>:</label> 
            </th>
            <td>
                <input id="<?php echo $data['use_wp_logo']; ?>" name="<?php echo $data['use_wp_logo']; ?>" type="checkbox" <?php if ( $val['use_wp_logo'] ) echo 'checked="checked"'; ?> value="true" /><br />
                <?php _e('Check this box to use WordPress Logo, leave unchecked to disable it.','private-only'); ?>
            </td>
   		</tr>
    </table>
</div>
</div>

</div> <!-- /float:left -->

<br style="clear:both;" />
