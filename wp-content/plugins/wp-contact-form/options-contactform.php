<?php
/*
Author: Douglas Karr
Author URI: http://www.douglaskarr.com
Description: Administrative options for WP-ContactForm
*/

load_plugin_textdomain('wpcf',$path = 'wp-content/plugins/wp-contact-form');

// Form Action URI
	$location = get_option('siteurl') . '/wp-admin/admin.php?page=wp-contact-form/options-contactform.php'; 

// Default options
	add_option('wpcf_email', __('you@example.com', 'wpcf'));
	add_option('wpcf_subject', __('Contact Form Results', 'wpcf'));
	add_option('wpcf_showsubject', __('TRUE', 'wpcf'));
	add_option('wpcf_success_msg', __('Thanks for your comments!', 'wpcf'));
	add_option('wpcf_error_msg', __('Please fill in the required fields.', 'wpcf'));
	add_option('wpcf_question', __('2 + 2 =', 'wpcf'));
	add_option('wpcf_answer', __('4', 'wpcf'));
	add_option('wpcf_casesensitive', __('FALSE', 'wpcf'));
	add_option('wpcf_copy', __('FALSE', 'wpcf'));

// Check form submission and update options
if ('process' == $_POST['stage']) {
	update_option('wpcf_email', $_POST['wpcf_email']);
	update_option('wpcf_subject', $_POST['wpcf_subject']);
	update_option('wpcf_showsubject', $_POST['wpcf_showsubject']);
	update_option('wpcf_success_msg', $_POST['wpcf_success_msg']);
	update_option('wpcf_error_msg', $_POST['wpcf_error_msg']);
	update_option('wpcf_question', $_POST['wpcf_question']);
	update_option('wpcf_answer', $_POST['wpcf_answer']);
	if ($_POST['wpcf_casesensitive']=='TRUE') {
		update_option('wpcf_casesensitive', 'TRUE');
	} else {
		update_option('wpcf_casesensitive', 'FALSE');
	}
	if ($_POST['wpcf_copy']=='TRUE') {
		update_option('wpcf_copy', 'TRUE');
	} else {
		update_option('wpcf_copy', 'FALSE');
	}
}

// Get options for form fields
	$wpcf_email = stripslashes(get_option('wpcf_email'));
	$wpcf_subject = stripslashes(get_option('wpcf_subject'));
	$wpcf_showsubject = get_option('wpcf_showsubject');
	$wpcf_success_msg = stripslashes(get_option('wpcf_success_msg'));
	$wpcf_error_msg = stripslashes(get_option('wpcf_error_msg'));
	$wpcf_question = stripslashes(get_option('wpcf_question'));
	$wpcf_answer = stripslashes(get_option('wpcf_answer'));
	$wpcf_casesensitive = get_option('wpcf_casesensitive');
	$wpcf_copy = get_option('wpcf_copy');
?>

<div class="wrap">
  <h2><?php _e('Contact Form Options', 'wpcf') ?></h2>
  	<form name="form1" method="post" action="<?php echo $location ?>&updated=true">
	<input type="hidden" name="stage" value="process" />
    <table width="100%" cellspacing="2" cellpadding="5" class="editform">
		<tr valign="top" width="300px">
			<th scope="row"><?php _e('E-mail Address:') ?></th>
			<td><input name="wpcf_email" type="text" id="wpcf_email" value="<?php echo $wpcf_email; ?>" size="40" /><br />
			<small><?php _e('This address is where the email will be sent to. Multiple recipients can be separated by a comma.', 'wpcf') ?></small></td>
		</tr>
      	<tr valign="top">
        	<th scope="row"><?php _e('Subject Line:') ?></th>
        	<td><input name="wpcf_subject" type="text" id="wpcf_subject" value="<?php echo $wpcf_subject; ?>" size="50" /><br />
			If you would like a list box to select an option from on the form, input your information delimited by a "|" (example: Website | Plugin | Help)<br />
			<input type="checkbox" <?php if ($wpcf_showsubject=="TRUE") { echo "checked=\"checked\""; } ?> name="wpcf_showsubject" id="wpcf_showsubject" value="TRUE" />
			Check this box to allow users to include a Subject line.  It will be concatenated to your default subject line.
			<small><?php _e('This will be the subject of the email.', 'wpcf') ?></small></td>
      	</tr>
		<tr>
			<td colspan="2"><h2><?php _e('Challenge Question', 'wpcf') ?></h2></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('What is your challenge question?', 'wpcf') ?></th>
			<td><input name="wpcf_question" id="wpcf_question" type="text" value="<?php echo $wpcf_question; ?>" size="40" /><br />
			<small><?php _e('This is a question asked to the contact form user to see if they are human.', 'wpcf') ?></small></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Correct response:', 'wpcf') ?></th>
			<td><input name="wpcf_answer" id="wpcf_answer" type="text" value="<?php echo $wpcf_answer; ?>" size="40" /><br />
			<small><?php _e('This is the exact response to the challenge question.', 'wpcf') ?></small><br />
			<input type="checkbox" <?php if ($wpcf_casesensitive=="TRUE") { echo "checked=\"checked\""; } ?> name="wpcf_casesensitive" id="wpcf_casesensitive" value="TRUE" />
			Check this box if you don't care if the user types the response with the correct case sensitivity.
		</tr>
		<tr>
			<td colspan="2"><h2><?php _e('Messages', 'wpcf') ?></h2></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Success Message:', 'wpcf') ?></th>
			<td><textarea name="wpcf_success_msg" id="wpcf_success_msg" style="width: 80%;" rows="4" cols="50"><?php echo $wpcf_success_msg; ?></textarea><br />
			<small><?php _e('When the form is sucessfully submitted, this is the message the user will see.', 'wpcf') ?></small></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Error Message:', 'wpcf') ?></th>
			<td><textarea name="wpcf_error_msg" id="wpcf_error_msg" style="width: 80%;" rows="4" cols="50"><?php echo $wpcf_error_msg; ?></textarea><br />
			<small><?php _e('If the user skips a required field, this is the message he will see.', 'wpcf') ?> <br />
			<?php _e('You can apply CSS to this text by wrapping it in <code>&lt;p style="[your CSS here]"&gt; &lt;/p&gt;</code>.', 'wpcf') ?><br />
			<?php _e('ie. <code>&lt;p style="color:red;"&gt;Please fill in the required fields.&lt;/p&gt;</code>.', 'wpcf') ?></small></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Copy Option:', 'wpcf') ?></th>
			<td><input type="checkbox" <?php if ($wpcf_copy=="TRUE") { echo "checked=\"checked\""; } ?> name="wpcf_copy" id="wpcf_casesensitive" value="TRUE" />Add a checkbox to the form so the user can copy themselves on the email sent.</td>
		</tr>
		</table>
		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Options', 'wpcf') ?> &raquo;" />
		</p>
		</form>
  		<table width="100%" cellspacing="2" cellpadding="5" class="editform">
		<tr>
			<td colspan="2"><h2><?php _e('Instructions', 'wpcf') ?></h2>
		<p>Simply place %%wpcontactform%% where ever you would like your Contact Form to be displayed.</p>
		<p>If you like this plugin, I would appreciate your support.  You can:
				<ul>
				<li><a href="http://www.technorati.com/faves/?add=http://www.douglaskarr.com" title="Add to Technorati Favorites">Add The Marketing Technology Blog</a> to your Technorati Favorites!</li>
				<li><a href="http://feeds.feedburner.com/DouglasKarr" title="My Feed">Add The Marketing Technology Blog</a> to your Feed Reader.</li>
				<li><a href="http://www.douglaskarr.com" title="My Blog">Read The Marketing Technology Blog</a> as often as you can!</li>
				<li>Or you can send me a few bucks via PayPal.<br />
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" style="border:none" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
				<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
				">
				</form></li>
				</ul>
				<p>Thanks for all your support!  Please don't hesitate to <a href="http://www.douglaskarr.com/contact-me/">contact me</a> if you find a problem or bug.</p><p>Regards,<br />Doug Karr</p></td>
		</tr>
	     </table>
</div>