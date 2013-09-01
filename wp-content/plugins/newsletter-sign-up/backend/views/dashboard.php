<div class="wrap" id="<?php echo $this->hook; ?>">
    <h2><a href="http://dannyvankooten.com/" target="_blank"><span id="dvk-avatar"></span></a>Newsletter Sign-Up :: Mailinglist Settings</h2>
    <div class="postbox-container" style="width:65%;">
        <div class="metabox-holder">	
            <div class="meta-box-sortables">
                <div class="postbox">
                    <div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>
                    <h3 class="hndle"><span>Mailinglist Settings</span></span></h3>
                    <div class="inside">
                        <form method="post" action="options.php" id="ns_settings_page">
                            <?php settings_fields('nsu_mailinglist_group'); ?>
    
                            <table class="form-table">	
                                <tr valign="top">
                                    <td colspan="2"><p>These settings are the most important since without these Newsletter Sign-Up can't do it's job. Having trouble finding
                                        the right configuration settings? Have a look at <a href="http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/">this post on my blog</a> or try the <a href="admin.php?page=newsletter-sign-up/config-helper">configuration extractor</a>.</p></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">Select your mailinglist provider: </th>
                                    <td>
                                        <select name="nsu_mailinglist[provider]" id="ns_mp_provider" onchange="document.location.href = 'admin.php?page=<?php echo $this->hook; ?>&mp=' + this.value">
                                            <option value="other"<?php if ($viewed_mp == NULL || $viewed_mp == 'other')
                                echo ' SELECTED'; ?>>-- other / advanced</option>
                                            <option value="mailchimp"<?php if ($viewed_mp == 'mailchimp')
                                echo ' SELECTED'; ?> >MailChimp</option>
                                            <option value="ymlp"<?php if ($viewed_mp == 'ymlp')
                                echo ' SELECTED'; ?> >YMLP</option>
                                            <option value="icontact"<?php if ($viewed_mp == 'icontact')
                                echo ' SELECTED'; ?> >iContact</option>
                                            <option value="aweber"<?php if ($viewed_mp == 'aweber')
                                echo ' SELECTED'; ?> >Aweber</option>
                                            <option value="phplist"<?php if ($viewed_mp == 'phplist')
                                echo ' SELECTED'; ?> >PHPList</option>
                                        </select>
                                    </td>
                                </tr>
                                
                                <?php if(isset($viewed_mp) && file_exists(dirname(__FILE__).'/rows-' . $viewed_mp . '.php')) require dirname(__FILE__). '/rows-' . $viewed_mp . '.php'; ?>
                                
                                <tbody class="form_rows"<?php if (isset($viewed_mp) && in_array($viewed_mp, array('mailchimp', 'ymlp')) && isset($opts['use_api']) && $opts['use_api'] == 1)
    echo ' style="display:none" '; ?>>
                                    <tr valign="top"><th scope="row">Newsletter form action</th>
                                        <td><input size="50%" type="text" id="ns_form_action" name="nsu_mailinglist[form_action]" value="<?php echo $opts['form_action']; ?>" /></td>
                                    </tr>
                                    <tr valign="top"><th scope="row">E-mail identifier <span class="ns_small">name attribute of input field that holds the emailadress</span></th>
                                        <td><input size="50%" type="text" name="nsu_mailinglist[email_id]" value="<?php echo $opts['email_id']; ?>"/></td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr valign="top"><th scope="row"><label for="subscribe_with_name">Subscribe with name?</label></th>
                                        <td><input type="checkbox" id="subscribe_with_name" name="nsu_mailinglist[subscribe_with_name]" value="1"<?php if($opts['subscribe_with_name'] == '1') echo ' checked'; ?> /></td>
                                    </tr>
                                    <tr class="name_dependent" valign="top"<?php if($opts['subscribe_with_name'] != 1) echo 'style="display:none;"'; ?>><th scope="row">Name identifier <span class="ns_small">name attribute of input field that holds the name</span></th>
                                        <td><input size="25%" id="ns_name_id" type="text" name="nsu_mailinglist[name_id]" value="<?php echo $opts['name_id']; ?>" /></td>
                                    </tr>
                                </tbody>
                            </table>
                            <p style="margin:10px;">
                                For some newsletter services you need to specify some additional static data, like a list ID or your account name. These fields are usually found as hidden fields in your sign-up form's HTML code.
                                You can specify these additional fields here using name / value pairs so they will be sent along with every sign-up request.
                            </p>
                            <p>If you use <em>%%NAME%%</em> or <em>%%IP%%</em> in the value fields it will be replaced by respectively the actual name or IP address of the subscriber.</p>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="column" style="font-weight:bold;">Name</th>
                                    <th scope="column" style="font-weight:bold;">Value</th>
                                </tr>
<?php
$last_key = 0;

if (isset($opts['extra_data']) && is_array($opts['extra_data'])) :
    foreach ($opts['extra_data'] as $key => $value) :
        ?>
                                        <tr valign="top">
                                            <td><input size="50%" type="text" name="nsu_mailinglist[extra_data][<?php echo $key; ?>][name]" value="<?php echo $value['name']; ?>" /></td>
                                            <td><input size="50%" type="text" name="nsu_mailinglist[extra_data][<?php echo $key; ?>][value]" value="<?php echo $value['value']; ?>" /></td>
                                        </tr>					
        <?php
        $last_key = $key + 1;
    endforeach;
endif;
?>
                                <tr valign="top">
                                    <td><input size="50%" type="text" name="nsu_mailinglist[extra_data][<?php echo $last_key; ?>][name]" value="" /></td>
                                    <td><input size="50%" type="text" name="nsu_mailinglist[extra_data][<?php echo $last_key; ?>][value]" value="" /></td>
                                </tr>
                            </table>
                            <p class="submit">
                                <input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Save Changes') ?>" />
                            </p>
                        </form>
                            <p class="nsu-tip">
                                Having trouble finding the right configuration settings? Try the <a href="admin.php?page=newsletter-sign-up/config-helper">configuration extractor</a>, it's there to help you!
                            </p>
                    </div>
                </div></div></div></div></div>
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