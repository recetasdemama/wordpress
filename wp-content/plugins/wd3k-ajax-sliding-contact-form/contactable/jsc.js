/*
 * contactable 1.0 - jQuery Ajax contact form
 *
 * Copyright (c) 2009 Philip Beel (http://www.theodin.co.uk/)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) 
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Revision: $Id: jquery.contactable.js 2009-08-24 $
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
			recipient : 'test@test.co.uk',
			subject : 'A contactable message',
			recievedMsg : 'Thank you for your information, we will contact you as soon as possible.',
			notRecievedMsg : 'Sorry but your message could not be sent, try again later',
			disclaimer: 'Please feel free to get in touch,<br/>we value your feedback'
		};

		//call in the default otions
		var options = $.extend(defaults, options);
		var contactShowStatus =1;

		
		//act upon the element that is passed into the design    
		return this.each(function(options) {
			//construct the form
			$(this).html('<div  id="contactable"></div><div id="contactForm" class="feedContainer"><a id="close_feedback" title="Close"><img src="images/btn-close.png" border="0" alt="Close" class="close" /></a><div class="topCrv"></div><div class="leftHand"><div class="leftshad"><div class="rightHand"><div class="rightshad"><div class="gradient"><form id="contactFormId"  method="" action=""><div id="loading">Enviando...<br><img src="images/ajax-loader.gif" border="0"/></div><div class="holder"><input type="hidden" id="recipient" name="recipient" value="'+defaults.recipient+'" /><input type="hidden" id="subject" name="subject" value="'+defaults.subject+'" /><label ><input type="text" id="name" name="name" /></label><label><input type="text" id="email" name="email" /></label><label><textarea id="comment" name="comment"></textarea></label><label><input type="submit" name="Submit" value="SEND" class="btn" /><span style=" padding-top:4px;">'+defaults.disclaimer+'</span></label></div></form><p class="thankNote" id="callback"></p></div></div></div></div></div><div class="botCrv"></div></div>');
			
			$('#contactFormId #name').example('Name');
			$('#contactFormId #email').example('Email');
			$('#contactFormId #comment').example('Message');
			
		/*	
			$(this).html('<div id="contactable"></div><form id="contactForm" method="" action=""><div id="loading"></div><div id="callback"></div><div class="holder"><input type="hidden" id="recipient" name="recipient" value="'+defaults.recipient+'" /><input type="hidden" id="subject" name="subject" value="'+defaults.subject+'" /><p><label for="name">Name <span class="red"> * </span></label><br /><input id="name" class="contact" name="name" /></p><p><label for="email">E-Mail <span class="red"> * </span></label><br /><input id="email" class="contact" name="email" /></p><p><label for="comment">Your Feedback <span class="red"> * </span></label><br /><textarea id="comment" name="comment" class="comment" rows="4" cols="30" ></textarea></p><p><input class="submit" type="submit" value="Send"/></p><p>'+defaults.disclaimer+'</p></div></form>');
		*/	
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
					name: "Please enter your name",
					email: "Please enter your email",
					comment: "Please enter your Message"
				},
				submitHandler: function() {
					$('.holder').hide();
					$('#loading').show();
					$.get('mail.php',{recipient:$('#recipient').val(), subject:$('#subject').val(), name:$('#name').val(), email:$('#email').val(), comment:$('#comment').val()},
					function(data){
						$('#loading').css({display:'none'});
						$('.holder').show();
						//document.forms['contactFormId'].reset()
						name:$('#name').val('');
						email:$('#email').val('');
						comment:$('#comment').val('');
						
						if( data == 'success') {
							$('#callback').show().append(defaults.recievedMsg);
						} else {
							$('#callback').show().append(defaults.notRecievedMsg);
						}
					});		
				}
			});
		});
	};
	//end the plugin call 
})(jQuery);

