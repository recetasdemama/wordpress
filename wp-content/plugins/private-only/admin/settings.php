<?php
/*
Private Only 3.3
Website: http://pixert.com
*/
?>
<div style="float:right; width:33%;">

<div class="postbox open">

<h3>About The Author</h3>

<div class="inside">

	<ul>
    
    <li style="margin-bottom: 40px;">
      <h4><?php _e('This is Private Only plugin for WordPress ver 3.3','private-only'); ?></h4>
      <h5>Coded by Kate Mag (Pixel Insert)</h5>
      <p><?php _e('If you disable or enable feed, DO NOT FORGET TO REFRESH YOUR BROWSER CACHE AFTER ACTIVATE PRIVATE ONLY or DISABLE FEED','private-only'); ?></p>
    </li> 
    <li>
    <h4>Thank you for using this plugin on your site</h4>
   <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=L3J4LBDGP533Q">
<img src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" alt="" /></a>
    </li>   
        
		<li><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2FPixelInsert&amp;send=false&amp;layout=standard&amp;width=300&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:35px;" allowTransparency="true"></iframe></li>
		
		<li><a href="https://twitter.com/share" class="twitter-share-button" data-url="http://pixert.com" data-text="Pixel Insert/Pixert" data-related="pixert" data-count="none">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<a href="https://twitter.com/pixert" class="twitter-follow-button" data-show-count="false">Follow @pixert</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></li>
        
		<li>Visit Our <a href="http://pixert.com/blog">Blog</a></li>
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
            	<label for="<?php echo $data['po_logo']; ?>"><?php _e('Logo:','private-only'); ?></label> 
            </th>
            <td>
               <input id="<?php echo $data['po_logo']; ?>" name="<?php echo $data['po_logo']; ?>" value="<?php echo $val['po_logo']; ?>" size="40" /><br />
                <?php _e('Upload an image with Media Library or FTP and put the full path here, http://yourdomainname.com/logo.jpg','private-on;y'); ?><br />
                <?php _e('We do not provide upload tool here, because it is free for you upload it wherever you want to','private-only'); ?><br />
            </td>
   		</tr>
   	   	<tr>
            <th>
            	<label for="<?php echo $data['po_logo_height']; ?>"><?php _e('Logo Height:','private-only'); ?></label> 
            </th>
            <td>
               <input id="<?php echo $data['po_logo_height']; ?>" name="<?php echo $data['po_logo_height']; ?>" value="<?php echo $val['po_logo_height']; ?>" size="40" /><br />
                <?php _e('What is your logo height','private-only'); ?>
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
   		<tr>
            <th>
            	<label for="<?php echo $data['logo_url']; ?>"><?php _e('Change WordPress logo link','private-only'); ?>:</label> 
            </th>
            <td>
               <input id="<?php echo $data['logo_url']; ?>" name="<?php echo $data['logo_url']; ?>" value="<?php echo $val['logo_url']; ?>" size="40" /><br />
                <?php _e('Change WordPress logo link from wordpress.org to your domain, http://yourdomainname.com','private-on;y'); ?><br />
            </td>

   		</tr>
   		<tr>
            <th>
            	<label for="<?php echo $data['remove_lost_password']; ?>"><?php _e('Remove Lost Password Text?','private-only'); ?>:</label> 
            </th>
            <td>
                <input id="<?php echo $data['remove_lost_password']; ?>" name="<?php echo $data['remove_lost_password']; ?>" type="checkbox" <?php if ( $val['remove_lost_password'] ) echo 'checked="checked"'; ?> value="true" /><br />
                <?php _e('Check this box to remove Lost Password text on WP-Admin login, leave unchecked to disable it.','private-only'); ?>
            </td>
   		</tr>
   		<tr>
            <th>
            	<label for="<?php echo $data['remove_backtoblog']; ?>"><?php _e('Remove Back to Blog link?','private-only'); ?>:</label> 
            </th>
            <td>
                <input id="<?php echo $data['remove_backtoblog']; ?>" name="<?php echo $data['remove_backtoblog']; ?>" type="checkbox" <?php if ( $val['remove_backtoblog'] ) echo 'checked="checked"'; ?> value="true" /><br />
                <?php _e('Check this box to remove Back to Blog link on WP-Admin login, leave unchecked to disable it.','private-only'); ?>
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
            	<label for="<?php echo $data['use_custom_css']; ?>"><?php _e('Use Custom CSS','private-only'); ?>:</label> 
            </th>
            <td>
                <input id="<?php echo $data['use_custom_css']; ?>" name="<?php echo $data['use_custom_css']; ?>" type="checkbox" <?php if ( $val['use_custom_css'] ) echo 'checked="checked"'; ?> value="true" /><br />
                <?php _e('Check this box to use Custom CSS, leave unchecked to disable it. You should have custom css in your active theme','private-only'); ?>
            </td>
   		</tr>
    </table>
</div>
</div>


</div> <!-- /float:left -->

<br style="clear:both;" />
