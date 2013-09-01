<div class="wrap" id="<?php echo $this->hook; ?>">
    <h2><a href="http://dannyvankooten.com/" target="_blank"><span id="dvk-avatar"></span></a>Newsletter Sign-Up :: Form Settings</h2>
    <div class="postbox-container" style="width:65%;">
        <div class="metabox-holder">	
            <div class="meta-box-sortables">
                <div class="postbox">
                    <div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>
                    <h3 class="hndle" id="nsu-form-settings"><span>Form Settings</span></h3>
                    <div class="inside">
                        
                        <form method="post" action="options.php" id="ns_settings_page">
                            <?php settings_fields('nsu_form_group'); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <td colspan="2"><p>Custome your Sign-up form by providing your own values for the different labels, input fields and buttons of the sign-up form. </p></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">E-mail label</th>
                                <td><input size="50%" type="text" name="nsu_form[email_label]" value="<?php echo $opts['email_label']; ?>" /></td>
                            </tr>
                            <tr valign="top">
                                 <th scope="row">E-mail default value</th>
                                 <td><input size="50%" type="text" name="nsu_form[email_default_value]" value="<?php echo $opts['email_default_value']; ?>" /></td>
                            </tr>
                            <tr valign="top" class="name_dependent" <?php if($opts['mailinglist']['subscribe_with_name'] != 1) echo 'style="display:none;"'; ?>><th scope="row">Name label <span class="ns_small">(if using subscribe with name)</span></th>
                                <td>
                                    <input size="50%" type="text" name="nsu_form[name_label]" value="<?php echo $opts['name_label']; ?>" /><br />
                                    <input type="checkbox" id="name_required" name="nsu_form[name_required]" value="1"<?php if($opts['name_required'] == '1') { echo ' checked'; } ?> />
                                    <label for="name_required">Name is a required field?</label>
                                </td>

                            </tr>
                            <tr valign="top" class="name_dependent" <?php if($opts['mailinglist']['subscribe_with_name'] != 1) echo 'style="display:none;"'; ?>>
                                <th scope="row">Name default value</th>
                                <td><input size="50%" type="text" name="nsu_form[name_default_value]" value="<?php echo $opts['name_default_value']; ?>" /></td>

                            </tr>
                            <tr valign="top"><th scope="row">Submit button value</th>
                                <td><input size="50%" type="text" name="nsu_form[submit_button]" value="<?php echo $opts['submit_button']; ?>" /></td>
                            </tr>
                            <tr valign="top"><th scope="row">Text to replace the form with after a successful sign-up</th>
                                <td>
                                    <textarea style="width:100%;" rows="5" cols="50" name="nsu_form[text_after_signup]"><?php echo $opts['text_after_signup']; ?></textarea>
                                    <p><input id="nsu_form_wpautop" name="nsu_form[wpautop]" type="checkbox" value="1" <?php if($opts['wpautop'] == 1) echo 'checked'; ?> />&nbsp;<label for="nsu_form_wpautop"><?php _e('Automatically add paragraphs'); ?></label></p>
                                </td>
                            </tr>
                            
                            <?php if($opts['mailinglist']['use_api'] == 1) { ?>
                            <tr valign="top"><th scope="row">Redirect to this url after signing up <span class="ns_small">(leave empty for no redirect)</span></th>
                                <td><input size="50%" type="text" name="nsu_form[redirect_to]" value="<?php echo $opts['redirect_to']; ?>" /></td>
                            </tr>
                            <?php } ?>
                            
                            <tr valign="top"><th scope="row"><label for="ns_load_form_styles">Load some default CSS</label><span class="ns_small">(check this for some default styling of the labels and input fields)</span></th>
                                <td><input type="checkbox" id="ns_load_form_styles" name="nsu_form[load_form_css]" value="1" <?php if($opts['load_form_css'] == 1) echo 'checked'; ?> /></td>
                            </tr>
                        </table>
                        <p class="submit">
                            <input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Save Changes') ?>" />
                        </p>
                        
                        <?php 
                        $tips = array(
                            'You can embed a sign-up form in your posts and pages by 
                                using the shortcode <b><em>[newsletter-sign-up-form]</em></b> or by calling <b><em>&lt;?php if(function_exists(\'nsu_signup_form\')) nsu_signup_form(); ?&gt;</em></b> from your template files.',
                            'Using Newsletter Sign-Up Widget? You can alternatively install <a target="_blank" href="http://wordpress.org/extend/plugins/wysiwyg-widgets/">WYSIWYG Widgets</a> and use the NSU form shortcode <strong>[nsu-form]</strong> to render a sign-up form in your widget area\'s. This allows
                            easier customizing',
                            'When testing, make sure to test with an email address that is not already on your e-mail list.',
                            'Using Newsletter Sign-Up with MailChimp? Consider switching to <a href="http://dannyvankooten.com/wordpress-plugins/mailchimp-for-wordpress/">MailChimp for WordPress</a>'
                        ); 
                        $random_key = array_rand($tips); 
                        ?>
                        <p class="nsu-tip">Tip: <?php echo $tips[$random_key]; ?></p>

                        </form>
                        <br style="clear:both;" />
                    </div></div></div></div></div></div>
<div class="postbox-container" style="width:33%; float:right; margin-right:1%;">
    <div class="metabox-holder">	
        <div class="meta-box-sortables">						
<?php
$this->donate_box();
$this->latest_posts();
$this->support_box();
?>				
        </div>
    </div>
</div>
</div>