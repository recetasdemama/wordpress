/*
 * contactable 1.2.1 - jQuery Ajax contact form
 *
 * Copyright (c) 2009 Philip Beel (http://www.theodin.co.uk/)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) 
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Revision: $Id: jquery.contactable.js 2010-01-18 $
 *
 */
 
//extend the plugin
(function($){

  
	//define the new for the plugin ans how to call it	
	$.fn.contactable = function(options) { 
		//set default options  
		var defaults = {
			name: 'Name',
			email: 'Email',
			message : 'Message',
			label_name: 'Name',
			label_email: 'E-Mail',
			label_website: 'Website',
			label_feedback: 'Your Feedback',
			label_send: 'SEND',
			recievedMsg : 'Thankyou for your message',
			notRecievedMsg : 'Sorry but your message could not be sent, try again later',
			disclaimer: 'Please feel free to get in touch, we value your feedback',
			fileMail: 'mail.php',
			hideOnSubmit: false,
			error_email: 'Please enter valid email address',
			error_name: 'Please enter your Name',
			error_message: 'Please enter your message',
			close_btn_url: '',
			ajax_loader_url: '',
            poweredby: '<span style="margin-left: 260px; font-size: 11px">Created by <a href="http://www.webdev3000.com">Web developer resources</a></span>'
		};

		//call in the default otions
		var options = $.extend(defaults, options);
		var contactShowStatus =1;
		
		//act upon the element that is passed into the design    
		return this.each(function(options) {
			//construct the form
	
			$(this).html('<div id="contactable"></div><div id="contactForm" class="feedContainer"><a id="close_feedback" title="Close" style="position: static;"><img src="'+defaults.close_btn_url+'" border="0" alt="Close" class="close" /></a><div class="topCrv"></div><div class="leftHand"><div class="leftshad"><div class="rightHand"><div class="rightshad"><div class="gradient"><form id="contactFormId"  method="" action=""><div id="loading">Enviando...<br><img src="'+defaults.ajax_loader_url+'" border="0"/></div><div class="holder"><label ><input type="text" id="name" name="name" /></label><label><input type="text" id="email" name="email" /></label><label><textarea id="comment" name="comment"></textarea></label><label><input type="submit" name="Submit" value="'+defaults.label_send+'" class="btn" /><span style=" padding-top:4px;">'+defaults.disclaimer+'</span></label></div></form>'+convertEntities(defaults.poweredby)+'<p class="thankNote" id="callback"></p></div></div></div></div></div><div class="botCrv"></div></div>');
			
			$('#contactFormId #name').example(defaults.label_name);
			$('#contactFormId #email').example(defaults.label_email);
			$('#contactFormId #comment').example(defaults.label_feedback);
			

			
			//show / hide function
			function contactShow() {
				$('#overlay').css({display: 'block'});
				$('div#contactable').animate({"marginLeft": "-=5px"}, "fast"); 
				$('#contactForm').animate({"marginLeft": "-=0px"}, "fast");
				$('div#contactable').animate({"marginLeft": "+=523px"}, "slow"); 
				$('#contactForm').animate({"marginLeft": "+=520px"}, "slow");
				$('.feedContainer .close').css({right: '-4px'});
				contactShowStatus = 0;
			}
		function contactHide() {
				$('#contactForm').animate({"marginLeft": "-=520px"}, "slow");
				$('div#contactable').animate({"marginLeft": "-=523px"}, "slow").animate({"marginLeft": "+=5px"}, "fast"); 
				$('#overlay').css({display: 'none'});
				$('.feedContainer .close').css({right: '0px'});
				contactShowStatus =1;
			}
			
			$('div#contactable').click(
				function() {
				if(contactShowStatus==1)
					contactShow();
				else
					contactHide();
			
			});
			
			$('a#close_feedback').click(function() {
				contactHide();
			
			}
			);
			
			
			
			//validate the form 
			$("#contactFormId").validate({
				//set the rules for the fild names
				rules: {
					name: {
						required: true,
						minlength: 2
					},
					email: {
						required: true,
						email: true
					},
					comment: {
						required: true
					}
				},
				//set messages to appear inline
				messages: {
					name: defaults.error_name,
					email: defaults.error_email,
					comment: defaults.error_message
				},

				submitHandler: function() {
					$('.holder').hide();
					$('#loading').show();
					name_val = $('#name').val();
					email_val = $('#email').val();
					comment_val = $('#comment').val();
					
					$.post(defaults.fileMail,{
						subject:	defaults.subject, 
						name: 		name_val, 
						email: 		email_val,  
						comment:	comment_val, 
						action:		defaults.action
						},
					function(data){
						$('#loading').css({display:'none'}); 
						$('.holder').show();
						data = jQuery.trim(data);
						if( data == 'success') {
							$('#callback').show().append(defaults.recievedMsg);
							if(defaults.hideOnSubmit == true) {
								//hide the tab after successful submition if requested
								setTimeout(function(){$('div#contactable').click();},1200);
								$('#comment').val('');
								$('#overlay').css({display: 'none'});	
							}
						} else {
							$('#callback').show().append(defaults.notRecievedMsg);
							setTimeout(function(){$('div#contactable').click();},1500);
						}
					});		
				}
			});
		});
	};
	$(document).ready(function(){
    $('a[href=#contact]').click(function(){
        $('div#contactable').click();
    });
  });
})(jQuery);
