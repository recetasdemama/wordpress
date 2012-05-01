<?php
/*
Plugin Name: WD3K Ajax Sliding Contact Form
Plugin URI: http://www.webdev3000.com
Description: An Ajax powered sliding contact form, based on <a href="http://theodin.co.uk/blog/ajax/contactable-jquery-plugin.html">Contactable</a> (jQuery Plugin) By Philip Beel.
Version: 1.0
Author: Oxy Kay and Csaba Kissi
Author URI: http://www.webdev3000.com

*/


class Jquery_CN_Contact {

	var $options = array();
	
	
	function __construct(){
		add_action('plugin_action_links_' . plugin_basename(__FILE__), array( &$this, 'AddPluginActions' ) );
		add_action('admin_menu', array( &$this, 'mc_admin_actions' ) );
		add_action('template_redirect', array( &$this, 'script_Jquery_CN_Contact' ) );
		add_action('wp_footer', array( &$this, 'div_Jquery_CN_Contact' ) );
		add_action('wp_ajax_Jquery_CN_Contact_ajax', array(&$this,'Jquery_CN_Contact_ajax'));
		add_action('wp_ajax_nopriv_Jquery_CN_Contact_ajax', array(&$this,'Jquery_CN_Contact_ajax'));
		$this->options = get_option( 'Jquery_CN_Contact_options', array() );
		
			//activation/deactivation hooks
		register_activation_hook(__FILE__, array( &$this, 'Jquery_CN_Contact_option_update' ) );
		register_deactivation_hook(__FILE__, array( &$this, 'contactable_form_deactivate' ) );
	}
	


//plugin deactivation	

function contactable_form_deactivate() {
	delete_option('Jquery_CN_Contact_options');
}


    //populate defaults
	function Jquery_CN_Contact_option_update(){
		$defaults = array(
			'recipient_contact'       => get_bloginfo('admin_email'),
			'subject_contact'         => 'Message sent from '.get_bloginfo('name'),
			'label_name_contact'      => 'Name',
			'label_email_contact'     => 'E-Mail',
			'label_feedback_contact'  => 'Your Feedback',
			'label_send_contact'      => 'SEND',
			'recievedMsg_contact'     => 'Thank you for your message',
			'notRecievedMsg_contact'  => 'Sorry, your message could not be sent, try again later',
			'disclaimer_contact'      => 'Please feel free to get in touch, we value your feedback',
			'error_email'      		  => 'Please enter valid email address',
			'error_name'      		  => 'Please enter your Name',
			'error_message'           => 'Please enter your message',
            'show_linkback'           => '1'
		);
		
		if(empty($this->options)){
			foreach($defaults as $key => $value){
				$old = get_option( $key );
				if(!empty($old) && 0 !== strpos($old, 'hide_'))
					$this->options[$key] = $old;
				elseif(!empty($old))
					$this->options[$key] = $old == 'true' ? true : false;
			}
		}
		$this->options = array_merge($this->options, $defaults);
		update_option('Jquery_CN_Contact_options', $this->options);
	}
	
	

	
	
	function AddPluginActions($links) {
		$new_links = array('<a href="options-general.php?page=magic-contact.php">' . __('Settings') . '</a>');
		return array_merge($new_links, $links);
	}
	
	
	function mc_admin_actions(){
		add_options_page("WD3K Contact Form", "WD3K Contact Form", 'manage_options',"magic-contact.php", array(&$this,"Jquery_CN_Contact_menu"));
	}
	
	function Jquery_CN_Contact_menu(){   
		if ( isset($_POST['submit']) ) {
			if ( !function_exists('current_user_can') || !current_user_can('manage_options') )
				die(__('Cheatin&#8217; uh?'));

			if(!empty($_POST['Jquery_CN_Contact'])){
				$mc = $_POST['Jquery_CN_Contact'];
				$nv = array();
				$nv['recipient_contact'] = isset($mc['recipient_contact']) ? $mc['recipient_contact'] : $this->options['recipient_contact'];
				$nv['subject_contact'] = isset($mc['subject_contact']) ? $mc['subject_contact'] : $this->options['subject_contact'];
				$nv['label_name_contact'] = isset($mc['label_name_contact']) ? $mc['label_name_contact'] : $this->options['label_name_contact'];
				$nv['label_email_contact'] = isset($mc['label_email_contact']) ? $mc['label_email_contact'] : $this->options['label_email_contact'];
				
				$nv['label_feedback_contact'] = isset($mc['label_feedback_contact']) ? $mc['label_feedback_contact'] : $this->options['label_feedback_contact'];
				$nv['label_send_contact'] = isset($mc['label_send_contact']) ? $mc['label_send_contact'] : $this->options['label_send_contact'];
				$nv['recievedMsg_contact'] = isset($mc['recievedMsg_contact']) ? $mc['recievedMsg_contact'] : $this->options['recievedMsg_contact'];
				$nv['notRecievedMsg_contact'] = isset($mc['notRecievedMsg_contact']) ? $mc['notRecievedMsg_contact'] : $this->options['notRecievedMsg_contact'];
				$nv['disclaimer_contact'] = isset($mc['disclaimer_contact']) ? $mc['disclaimer_contact'] : $this->options['disclaimer_contact'];
								
				$nv['error_email'] = isset($mc['error_email']) ? $mc['error_email'] : $this->options['error_email'];
				
				$nv['error_name'] = isset($mc['error_name']) ? $mc['error_name'] : $this->options['error_name'];
				
				$nv['error_message'] = isset($mc['error_message']) ? $mc['error_message'] : $this->options['error_message'];

                $nv['show_linkback'] = isset($mc['show_linkback']) ? $mc['show_linkback'] : 0;

				$this->options = array_merge($this->options, $nv);
				update_option( 'Jquery_CN_Contact_options', $this->options );
			}
			
		}
		include_once(dirname(__FILE__).'/form-admin.php');
	}
	
	
	function script_Jquery_CN_Contact(){
		$base = trailingslashit(plugins_url('',__FILE__)); 
		wp_enqueue_script( 'jquery.contactable', $base . 'contactable/jquery.contactable.js', array('jquery') , 3.1);
		wp_enqueue_script( 'jquery.validate', $base . 'contactable/jquery.validate.pack.js', array('jquery') , 3.1);
		
		wp_enqueue_script( 'jquery.populate', $base . 'contactable/jquery.form.populate.js', array('jquery') , 3.1);
				
		wp_enqueue_script( 'my_contactable', $base . 'my.contactable.js', array('jquery') , 3.1);
		
		$close_btn_url = $base .'contactable/images/btn-close.png';
		$ajax_loader_url = $base .'contactable/images/ajax-loader.gif';

        $poweredby = ($this->options['show_linkback'] == 0 ? '' : '<span style="margin-left: 260px; font-size: 11px">Created by <a href="http://www.webdev3000.com">Web developer resources</a></span>');

		$js_vars = array(
			'name'            => 'Name',
			'email'           => 'E-Mail',
			'message'         => 'Message',
			//'recipient'       => $this->options['recipient_contact'],
			//'subject'         => $this->options['subject_contact'],
			'label_name'      => $this->options['label_name_contact'],
			'label_email'     => $this->options['label_email_contact'],
			'label_feedback'  => $this->options['label_feedback_contact'],
			'label_send'      => $this->options['label_send_contact'],
			'recievedMsg'     => $this->options['recievedMsg_contact'],
			'notRecievedMsg'  => $this->options['notRecievedMsg_contact'],
			'disclaimer'      => $this->options['disclaimer_contact'],
			'error_email'     => $this->options['error_email'],
			'error_name'      => $this->options['error_name'],
			'error_message'   => $this->options['error_message'],
            'show_linkback'   => $this->options['show_linkback'],
            'poweredby'       => $poweredby,
			'fileMail'        => admin_url('admin-ajax.php'),
			'action'          => 'Jquery_CN_Contact_ajax',
			'close_btn_url'   => $close_btn_url,
			'ajax_loader_url'   => $ajax_loader_url,
			
		); 
		if( is_user_logged_in() ){
			$js_vars['hide_email'] = 'true';
		}
		
		wp_localize_script( 'my_contactable', 'CNContact', $js_vars );
		wp_enqueue_style( 'contactable', $base . 'contactable/contactable.css' );
	}
	
	
	function div_Jquery_CN_Contact(){
		echo '<div id="mycontactform"> </div>';
	}
	
	function Jquery_CN_Contact_ajax(){
		
		/*if(is_user_logged_in()){
			$current_user = wp_get_current_user();
			$_POST['name'] = $current_user->display_name;
			$_POST['email'] = $current_user->user_email;
		}*/
		
		$name = esc_attr(trim($_POST['name']));
		$emailAddr = is_email($_POST['email']) ? $_POST['email']: false;
		$comment = nl2br(esc_attr(trim($_POST['comment'])));
		$subject = $this->options['subject_contact'];	

			  $contactMessage .= sprintf("<p><b>Mensaje de</b>: %s</p>",$name);

		if($emailAddr)
			$contactMessage .= sprintf("<p><b>Responder a</b>: %s</p>",$emailAddr);
		
		if(!$emailAddr){
			echo 'An invalid email address was entered';
			die();
		}
		//add referer
		if(isset($_SERVER["HTTP_REFERER"])){
		  $contactMessage .= sprintf("<p><b>P&aacute;gina</b>: %s</p>",$_SERVER["HTTP_REFERER"]);
	  }
	  $contactMessage .= sprintf("<h2>Mensaje</h2><p> %s</p>",$comment);
		
		$contactMessage = sprintf('<p>%s</p>',$contactMessage);
		
		$headers = array(
		  sprintf("From: %s <%s>",$name,$emailAddr),
		  sprintf("Reply-To: %s <%s>",$name,$emailAddr),
    	"Content-Type: text/html"
    );
    $h = implode("\r\n",$headers) . "\r\n";
		
		$send = wp_mail($this->options['recipient_contact'], $subject, $contactMessage,$h);
		if($send)
			echo('success');
		else
			echo('no send');
		die();
	}
}




$CNContact = new Jquery_CN_Contact();


	


