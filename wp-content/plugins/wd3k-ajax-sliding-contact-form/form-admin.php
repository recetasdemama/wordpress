<?php
if((!isset($this) || !is_a($this, 'Jquery_CN_Contact')) && (!isset($CNContact) || !is_a($CNContact, 'Jquery_CN_Contact')))
	return;
elseif((!isset($this) || !is_a($this, 'Jquery_CN_Contact')))
	$this &= $CNContact;
?>

<?php if ( !empty($_POST['submit'] ) ) : ?>
  <div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php endif; ?>

<div class="wrap">
  <h2><?php _e('WD3K Ajax Sliding Contact Form Configuration'); ?></h2>
  <div class="narrow Jquery_CN_Contact">
    <form action="" method="post" id="magic-contact-conf" style="margin: auto; width: 600px; ">
      

      <p><h3>Settings for Email</h3></p>
      
      
      <div class="contactleft">
        <label for="recipient_contact"><?php _e('Recipient of the email'); ?></label>
      </div>
      <div class="contactright">
        <input id="recipient_contact" name="Jquery_CN_Contact[recipient_contact]" type="text" class="Jquery_CN_Contact" value="<?php echo $this->options['recipient_contact']; ?>" />
      </div>

      <div class="contactleft">
        <label for="subject_contact"><?php _e('Subject for email'); ?></label>
      </div>
      <div class="contactright">
        <input id="subject_contact" name="Jquery_CN_Contact[subject_contact]" type="text" class="Jquery_CN_Contact" value="<?php echo $this->options['subject_contact']; ?>" />
      </div>
      
      
      <div class="clear"></div>
      <p><h3>Form Labels</h3></p>
      
      
      <div class="contactleft">
        <label for="label_name_contact"><?php _e('Label for name'); ?></label>
      </div>
      
      <div class="contactright">
        <input id="label_name_contact" name="Jquery_CN_Contact[label_name_contact]" type="text" class="Jquery_CN_Contact" value="<?php echo $this->options['label_name_contact']; ?>" />
      </div>
      
      <div class="contactleft">
        <label for="label_email_contact"><?php _e('Label for Email'); ?></label>
      </div>
      
      <div class="contactright">
        <input id="label_email_contact" name="Jquery_CN_Contact[label_email_contact]" type="text" class="Jquery_CN_Contact" value="<?php echo $this->options['label_email_contact']; ?>" />
      </div>
      
      
      <div class="contactleft">
        <label for="label_feedback_contact"><?php _e('Label for Feedback'); ?></label>
      </div>
      
      <div class="contactright">
        <input id="label_feedback_contact" name="Jquery_CN_Contact[label_feedback_contact]" type="text" class="Jquery_CN_Contact" value="<?php echo $this->options['label_feedback_contact']; ?>" />
      </div>
      
      <div class="contactleft">
        <label for="label_send_contact"><?php _e('Label for button (send)'); ?></label>
      </div>
      
      
     
      
      <div class="contactright">
        <input id="label_send_contact" name="Jquery_CN_Contact[label_send_contact]" type="text" class="Jquery_CN_Contact" value="<?php echo $this->options['label_send_contact']; ?>" />
      </div>
      
      
      <div class="clear"></div>
      <p><h3>Status Messages</h3></p>
      
      
      <div class="contactleft">
        <label for="recievedMsg_contact"><?php _e('Recieved message'); ?></label>
      </div>
        
      <div class="contactright">
        <input id="recievedMsg_contact" name="Jquery_CN_Contact[recievedMsg_contact]" type="text" class="Jquery_CN_Contact" value="<?php echo $this->options['recievedMsg_contact']; ?>" />
      </div>

      <div class="contactleft">
        <label for="notRecievedMsg_contact"><?php _e('Not recieved messsage'); ?></label>
      </div>
        
      <div class="contactright">
        <input id="notRecievedMsg_contact" name="Jquery_CN_Contact[notRecievedMsg_contact]" type="text" class="Jquery_CN_Contact" value="<?php echo $this->options['notRecievedMsg_contact']; ?>" />
      </div>

      <div class="contactleft">
        <label for="disclaimer_contact"><?php _e('Disclaimer'); ?></label>
      </div>

      <div class="contactright">
        <input id="disclaimer_contact" name="Jquery_CN_Contact[disclaimer_contact]" type="text" class="Jquery_CN_Contact" value="<?php echo $this->options['disclaimer_contact']; ?>" />
      </div>
      
      <div class="clear"></div>
  
      
      
      
      
      
      
      <div class="clear"></div>
      <p><h3>Error Messages for Fields</h3></p>
      
      
      <div class="contactleft">
        <label for="error_email_contact"><?php _e('Email'); ?></label>
      </div>
        
      <div class="contactright">
        <input id="error_email_contact" name="Jquery_CN_Contact[error_email]" type="text" class="Jquery_CN_Contact" value="<?php echo $this->options['error_email']; ?>" />
      </div>

      <div class="contactleft">
        <label for="error_name_contact"><?php _e('Name'); ?></label>
      </div>
        
      <div class="contactright">
        <input id="error_name_contact" name="Jquery_CN_Contact[error_name]" type="text" class="Jquery_CN_Contact" value="<?php echo $this->options['error_name']; ?>" />
      </div>

      <div class="contactleft">
        <label for="error_message_contact"><?php _e('Feedback'); ?></label>
      </div>

      <div class="contactright">
        <input id="error_message_contact" name="Jquery_CN_Contact[error_message]" type="text" class="Jquery_CN_Contact" value="<?php echo $this->options['error_message']; ?>" />
      </div>
      
      <div class="clear"></div>
      

      <div class="clear"></div>
      <p><h3>Other</h3></p>


      <div class="contactleft">
        <label for="show_linkback"><?php _e('Please support us'); ?></label>
      </div>

      <div class="contactright">
        <input id="show_linkback" name="Jquery_CN_Contact[show_linkback]" type="checkbox" class="Jquery_CN_Contact" value="1" "<?php if($this->options['show_linkback'] == 1) echo "checked=\"1\""; else echo "" ?>"/>
      </div>
      
      
      
      
	    <div class="contactright">
	      <input type="submit" name="submit" value="<?php _e('Update options &raquo;'); ?>" />
	    </div>
    </form>
  </div>
</div>

<style>
input.Jquery_CN_Contact{
width:300px;
}
.contactleft {
clear:both;
display:inline;
float:left;
margin:4px 0;
padding:4px;
text-align:right;
width:25%;
}
.contactright {
display:inline;
float:right;
padding:4px;
text-align:left;
width:70%;
}
</style>
