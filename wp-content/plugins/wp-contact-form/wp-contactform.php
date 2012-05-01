<?php

/**
* Plugin Name: WP-ContactForm
* Plugin URI: http://www.douglaskarr.com/projects/wp-contactform/
* Description: WP Contact Form is a drop in form for users to contact you. It can be implemented on a page or a post. It currently works with WordPress 2.0+.  The plugin was originally developed by <a href="http://ryanduff.net/projects/wp-contactform/">Ryan Duff</a> but has been modified by <a href="http://www.douglaskarr.com">Doug Karr</a> to require a challenge question and response to fight spam as well as some other features.
* Author: Douglas Karr
* Author URI: http://www.douglaskarr.com
* Version: 2.0.7
*/

	load_plugin_textdomain('wpcf',$path='wp-content/plugins/wp-contact-form');

	// Declare strings that change depending on input. This also resets them so errors clear on resubmission.
	$wpcf_strings=array(
	'name'=>'<input type="text" style="background-color:#FFFF66" name="wpcf_your_name" id="wpcf_your_name" size="30" maxlength="50" tabindex="1" value="'.htmlentities($_POST['wpcf_your_name']).'" />',
	'email'=>'<input type="text" style="background-color:#FFFF66" name="wpcf_email" id="wpcf_email" size="30" maxlength="50" tabindex="2" value="'.htmlentities($_POST['wpcf_email']).'" />',
	'response'=>'<input type="text" style="background-color:#FFFF66" name="wpcf_response" id="wpcf_response" size="30" maxlength="50" tabindex="3" value="'.htmlentities($_POST['wpcf_response']).'" />',	
	'usersubject'=>'<input type="text" style="background-color:#FFFF66" name="wpcf_usersubject" id="wpcf_usersubject" size="30" tabindex="5" maxlength="50" value="'.htmlentities($_POST['wpcf_usersubject']).'" />',
	'msg'=>'<textarea name="wpcf_msg" style="background-color:#FFFF66; width:250px;" id="wpcf_msg" rows="8" cols="8" tabindex="6">'.htmlentities($_POST['wpcf_msg']).'</textarea>',
	'error'=>''
	);

	function wpcf_is_malicious($input) {
		$bad_inputs=array("\r", "\n", "mime-version", "content-type", "cc:", "to:");
		foreach ($bad_inputs as $bad_input) {
			if (strpos(strtolower($input), strtolower($bad_input)) !== false) {
				return true;
			}
		}
		return false;
	}

	function wpcf_is_challenge($input) {
		$answer=get_option('wpcf_answer');
		$answer=stripslashes(trim($answer));
		if (get_option('wpcf_casesensitive')=='TRUE') {
			return (strtoupper($input)== strtoupper($answer));
		} else {
			return ($input== $answer);
		}
		
	}

	// This function checks for errors on input and changes $wpcf_strings if there are any errors. Shortcircuits if there has not been a submission
	function wpcf_check_input() {
		if (isset($_POST['wpcf_stage'])) {
			$_POST['wpcf_your_name']=stripslashes(trim($_POST['wpcf_your_name']));
			$_POST['wpcf_email']=stripslashes(trim($_POST['wpcf_email']));
			$_POST['wpcf_response']=stripslashes(trim($_POST['wpcf_response']));
			$_POST['wpcf_website']=stripslashes(trim($_POST['wpcf_website']));
			$_POST['wpcf_usersubject']=stripslashes(trim($_POST['wpcf_usersubject']));
			$_POST['wpcf_msg']=stripslashes(trim($_POST['wpcf_msg']));
			global $wpcf_strings;
			$reason=false;
			if (empty($_POST['wpcf_your_name'])) {
				$reason='empty';
				$wpcf_strings['name']='<input type="text" name="wpcf_your_name" style="background-color:#FFFF66" id="wpcf_your_name" size="30" maxlength="50" value="'.htmlentities($_POST['wpcf_your_name']).'" class="contacterror" /> ('. __('required','wpcf').')';
			}
			if (!is_email($_POST['wpcf_email'])) {
				$reason='empty';
				$wpcf_strings['email']='<input type="text" name="wpcf_email" style="background-color:#FFFF66" id="wpcf_email" size="30" maxlength="50" value="'.htmlentities($_POST['wpcf_email']).'" class="contacterror" /> ('. __('required','wpcf').')';
			}
			if (empty($_POST['wpcf_response'])) {
				$reason='empty';
				$wpcf_strings['response']='<input type="text" name="wpcf_response" style="background-color:#FFFF66" id="wpcf_response" size="30" maxlength="50" value="'.htmlentities($_POST['wpcf_response']).'" class="contacterror" /> ('. __('required','wpcf').')';
			}
			if (!wpcf_is_challenge($_POST['wpcf_response'])) {
				$reason='wrong';
				$wpcf_strings['response']='<input type="text" name="wpcf_response" style="background-color:#FFFF66" id="wpcf_response" size="30" maxlength="50" value="'.htmlentities($_POST['wpcf_response']).'" class="contacterror" /> ('. __('required','wpcf').')';
			}
			if (empty($_POST['wpcf_usersubject']) && (get_option('wpcf_showsubject')=='TRUE')) {
				$reason='empty';
				$wpcf_strings['usersubject']='<input type="text" name="wpcf_usersubject" style="background-color:#FFFF66" id="wpcf_usersubject" size="30" maxlength="50" value="'.htmlentities($_POST['wpcf_usersubject']).'" class="contacterror" /> ('. __('required','wpcf').')';
			}
			if (empty($_POST['wpcf_msg'])) {
				$reason='empty';
				$wpcf_strings['msg']='<textarea name="wpcf_msg" style="background-color:#FFFF66" id="wpcf_message" cols="35" rows="8" class="contacterror">'.htmlentities($_POST['wpcf_msg']).'</textarea></div>';
			}
			if (wpcf_is_malicious($_POST['wpcf_your_name']) || wpcf_is_malicious($_POST['wpcf_email'])) {
				$reason='malicious';
			}
			if ($reason) {
				if ($reason== 'malicious') {
					$wpcf_strings['error']="<div style='font-weight: bold;'>You can not use any of the following in the Name or Email fields: a linebreak, or the phrases 'mime-version','content-type','cc:' or 'to:'.</div>";
				}
				elseif($reason== 'empty') {
					$wpcf_strings['error']='<div style="font-weight: bold;">'. stripslashes(get_option('wpcf_error_msg')).'</div>';
				}
				elseif($reason== 'wrong') {
					$wpcf_strings['error']="<div style='font-weight: bold;'>You answered the challenge question incorrectly.</div>";
				}
				return false;
			}
			return true;
		}
		return false;
	}

	// Wrapper function which calls the form.
	function wpcf_callback($content) {
		global $wpcf_strings;
		if (strpos($content,'%%wpcontactform%%')!== false) {
			if (wpcf_check_input()) {
				$recipient=get_option('wpcf_email');
				if(strpos(get_option('wpcf_subject'),"|")>0) {
					$subject=$_POST['wpcf_usersubject'];
				} else {
					$subject=get_option('wpcf_subject').': '.$_POST['wpcf_usersubject'];
				}
				$success_msg=get_option('wpcf_success_msg');
				$success_msg=stripslashes($success_msg);
				$name=$_POST['wpcf_your_name'];
				$email=$_POST['wpcf_email'];
				$website=$_POST['wpcf_website'];
				$msg=$_POST['wpcf_msg'];
				$headers="MIME-Version: 1.0\n";
				$headers.= "From: ".$name." <".$email.">\n";
				$headers.= "Content-Type: text/plain; charset=\"".get_settings('blog_charset')."\"\n";
				$fullmsg="$name <$email> wrote:\n";
				$fullmsg.= wordwrap($msg, 80, "\n")."\n\n";
				$fullmsg.= "Website: ".$website."\n";
				$fullmsg.= "IP: ".getip();
				wp_mail($recipient, $subject, $fullmsg, $headers);
				if($_POST['copy']=='true') {
					wp_mail($email, $subject, $fullmsg, $headers);
				}
				$form='<div style="font-weight: bold;">'.$success_msg.'</div>';
				
			} else {
				$question=stripslashes(get_option('wpcf_question'));
				
				$form.='<form action="'.get_permalink().'" name="wpcf_form" method="post">'; 
				$form.='<table align="center" cellspacing="5px" cellpadding="0"><tr><td colspan="2">';
				$form.= $wpcf_strings['error'];
				$form.= '<small>Los campos sombreados son necesarios.</small>';
				$form.= '<input type="hidden" name="wpcf_stage" value="process" /></td></tr>';
				$form.= '<tr><td style="text-align:right">'. __('Nombre: ','wpcf').'</td>';
				$form.= '<td style="text-align:left">'.$wpcf_strings['name'].'</td></tr>';
				$form.= '<tr><td style="text-align:right">'. __('Correo:','wpcf').'</td>';
				$form.= '<td style="text-align:left">'.$wpcf_strings['email'].'</td></tr>';
				$form.= '<tr><td style="text-align:right">'. __($question, 'wpcf').'</td>';
				$form.= '<td style="text-align:left">'.$wpcf_strings['response'].'</td></tr>';
				$form.= '<tr><td style="text-align:right">'. __('Website:','wpcf').'</td>';
				$form.= '<td style="text-align:left">';
				$form.= '<input type="text" name="wpcf_website" id="wpcf_website" size="30" maxlength="100" tabindex="4" value="'.htmlentities($_POST['wpcf_website']).'" /></td></tr>';
				if(strpos(get_option('wpcf_subject'),"|")>0) {
					$subjectarray = array();
					$subjectarray = explode("|",get_option('wpcf_subject'));
						$form.= '<tr><td style="text-align:right">'. __('Subject:','wpcf').'</td><td style="text-align:left">';
						$form .= '<select name="wpcf_usersubject" id="wpcf_usersubject" style="min-width:250px" tabindex="5">';
						for ($i=0; $i<count($subjectarray);$i++ ) {
							$arrayoption = '';
							$arrayoption = trim($subjectarray[$i]);
							if ($arrayoption!='') {
								$form.='<option value="'.trim($subjectarray[$i]).'">'.trim($subjectarray[$i]).'</option>';
							}
						}
						$form .= '</select></td></tr>';
					} else {
					if (get_option('wpcf_showsubject')=="TRUE") {
						$form.= '<tr><td style="text-align:right">'. __('Asunto:','wpcf').'</td>';
						$form.= '<td style="text-align:left">'.$wpcf_strings['usersubject'].'</td></tr>';
					}
				}
				$form.= '<tr><td style="text-align:right">'. __('Mensaje: ','wpcf').'</td>';
				$form.= '<td style="text-align:left">'.$wpcf_strings['msg'].'</td></tr>';
				if (get_option('wpcf_copy')=='TRUE') {
				$form.= '<tr><td style="text-align:right"><input type="checkbox" id="copy" name="copy" value="true" tabindex="7" ></td>';
				$form.= '<td style="text-align:left">Copy yourself on the form submission.</td>';
				}
				$form.= '<tr><td colspan="2" style="text-align:right"><input type="submit" name="Submit" tabindex="8" value="'. __('Submit','wpcf').'" id="contactsubmit" />';
				$form.= '</td></tr></table></form>';
			}
			return str_replace('%%wpcontactform%%', $form, $content);
		}
		return $content;
	}

	// Can't use WP's function here, so lets use our own
	function getip() {
		return (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $_SERVER['REMOTE_ADDR']));
	}

	// Add the options page
	function wpcf_add_options_page() {
		add_options_page('Contact Form Options','Contact Form','manage_options','wp-contact-form/options-contactform.php');
	}

	// Action calls for all functions
	add_action('admin_head','wpcf_add_options_page');
	add_filter('the_content','wpcf_callback', 7);
?>