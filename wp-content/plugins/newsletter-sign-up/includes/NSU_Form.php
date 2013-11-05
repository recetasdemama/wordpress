<?php

class NSU_Form {
	
	private $validation_errors = array();
	private $number_of_forms = 0;
	private $options = array();

	public function __construct()
	{
		// add hooks
		$options = NSU::instance()->get_options();
		$this->options = $options;

		// register the shortcode which can be used to output sign-up form
		add_shortcode('newsletter-sign-up-form', array($this,'form_shortcode'));
		add_shortcode('nsu-form', array($this,'form_shortcode'));
		add_shortcode('nsu_form', array($this,'form_shortcode'));

		if(isset($_POST['nsu_submit'])) {
			add_action('init', array($this, 'submit'));
		}
	}

	/**
	* Check if ANY Newsletter Sign-Up form has been submitted. 
	*/
	public function submit()
	{

		$opts = $this->options['form'];
		$errors = array();   

		$email = (isset($_POST['nsu_email'])) ? $_POST['nsu_email'] : '';
		$name = (isset($_POST['nsu_name'])) ? $_POST['nsu_name'] : '';

		// has the honeypot been filled?
		if(!empty($_POST['nsu_robocop'])) { return false; }

		if($this->options['mailinglist']['subscribe_with_name'] == 1 && $opts['name_required'] == 1 && empty($name)) {
			$errors['name-field'] = __($opts['text_empty_name']);
		}

		if(empty($email)) { 
			$errors['email-field'] = __($opts['text_empty_email']);
		} elseif(!is_email($email)) {
			$errors['email-field'] = __($opts['text_invalid_email']);
		}

		$this->validation_errors = $errors;

		if(count($this->validation_errors) == 0) {
			NSU::instance()->send_post_data($email, $name, 'form');
		}

		
		return;
	}

	/**
    * The NSU form shortcode function. Calls the output_form method
         * 
         * @param array $atts Not used
         * @param string $content Not used
         * @return string Form HTML-code 
         */
	public function form_shortcode($atts = null,$content = null)
	{ 
		return $this->output_form(false);
	}
	
        /**
         * Generate the HTML for a form
         * @param boolean $echo Should HTML be echo'ed?
         * @return string The generated HTML 
         */
        public function output_form($echo = true)
        {
        	$errors = $this->validation_errors;
        	$opts = NSU::instance()->get_options();
        	
        	$additional_fields = '';
        	$output = "\n<!-- Form by Newsletter Sign-Up v". NSU_VERSION_NUMBER ." - http://wordpress.org/plugins/newsletter-sign-up/ -->\n";

        	$formno = $this->number_of_forms++;

          	/* Set up form variables for API usage or normal form */
        	if($opts['mailinglist']['use_api'] == 1) {

        		/* Using API, send form request to ANY page */
        		$form_action = '';
        		$email_id = 'nsu_email';
        		$name_id = 'nsu_name';

        	} else {

        		/* Using normal form request, set-up using configuration settings */
        		$form_action = $opts['mailinglist']['form_action'];
        		$email_id = $opts['mailinglist']['email_id'];

        		if(!empty($opts['mailinglist']['name_id'])) {
        			$name_id = $opts['mailinglist']['name_id'];
        		}

        	}

        	/* Set up additional fields */
        	if(isset($opts['mailinglist']['extra_data']) && is_array($opts['mailinglist']['extra_data'])) {

        		foreach($opts['mailinglist']['extra_data'] as $ed) {
        			if($ed['value'] == '%%NAME%%') continue;
        			$ed['value'] = str_replace("%%IP%%", $_SERVER['REMOTE_ADDR'], $ed['value']);
        			$additional_fields .= "<input type=\"hidden\" name=\"{$ed['name']}\" value=\"{$ed['value']}\" />";
        		} 
        	} 

        	$email_label = __($opts['form']['email_label'], 'nsu');
        	$name_label = __($opts['form']['name_label'], 'nsu');

        	if($opts['form']['use_html5']) { 
        		$email_type = 'email';
        		$email_atts = 'placeholder="'. __($opts['form']['email_default_value'], 'nsu') .'" required';
        		$name_atts = 'placeholder="'. __($opts['form']['name_default_value'], 'nsu') .'" ';
        		if($opts['form']['name_required']) { $name_atts .= 'required '; }
        	} else {
        		$email_type = 'text';
        		$email_value = __($opts['form']['email_default_value'], 'nsu');
        		$email_atts = 'value="'. $email_value .'"';
        		$name_value = __($opts['form']['name_default_value'], 'nsu');
        		$name_atts = 'value="'. $name_value .'"';
        	}

        	$submit_button = __($opts['form']['submit_button'], 'nsu');      

        	$text_after_signup =  __($opts['form']['text_after_signup'], 'nsu');
        	$text_after_signup = ($opts['form']['wpautop'] == 1) ? wpautop(wptexturize($text_after_signup)) : $text_after_signup;



		 if(!isset($_POST['nsu_submit']) || count($errors) > 0) { //form has not been submitted yet 

		 	$output .= "<form class=\"nsu-form\" id=\"nsu-form-$formno\" action=\"$form_action\" method=\"post\">";	
		 	if($opts['mailinglist']['subscribe_with_name'] == 1) {	
		 		$output .= "<p><label for=\"nsu-name-$formno\">$name_label</label><input class=\"nsu-field\" id=\"nsu-name-$formno\" type=\"text\" name=\"$name_id\" $name_atts ";
		 		if(!$opts['form']['use_html5']) $output .= "onblur=\"if(!this.value) this.value = '$name_value';\" onfocus=\"if(this.value == '$name_value') this.value=''\" ";
		 		$output .= "/>";
		 		if(isset($errors['name-field'])) $output .= '<span class="nsu-error error notice">'.$errors['name-field'].'</span>';
		 		$output .= "</p>";		
		 	} 

		 	$output .= "<p><label for=\"nsu-email-$formno\">$email_label</label><input class=\"nsu-field\" id=\"nsu-email-$formno\" type=\"$email_type\" name=\"$email_id\" $email_atts ";
		 	if(!$opts['form']['use_html5']) $output .= "onblur=\"if(!this.value) this.value = '$email_value';\" onfocus=\"if(this.value == '$email_value') this.value = ''\" ";
		 	$output .= "/>";
		 	if(isset($errors['email-field'])) $output .= '<span class="nsu-error error notice">'.$errors['email-field'].'</span>';
		 	$output .= "</p>";
		 	$output .= $additional_fields;
		 	$output .= '<textarea name="nsu_robocop" style="display: none;"></textarea>';
		 	$output .= "<p><input type=\"submit\" id=\"nsu-submit-$formno\" class=\"nsu-submit\" name=\"nsu_submit\" value=\"$submit_button\" /></p>";
		 	$output .= "</form>";

		} else { // form has been submitted

			$output .= "<p id=\"nsu-signed-up-$formno\" class=\"nsu-signed-up\">". ($text_after_signup) . "</p>";		

		}

		$output .= "\n<!-- / Newsletter Sign-Up -->\n";

		if($echo) { echo $output; } 
		
		return $output;

	}
	
}