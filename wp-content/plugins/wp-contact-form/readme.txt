Plugin Name: WP-ContactForm
Plugin URI: http://www.douglaskarr.com/projects/wp-contactform/
Description: WP Contact Form is a drop in form for users to contact you. It can be implemented on a page or a post. Requires WordPress 2 or higher
Author: Douglas Karr
Author URI: http://www.douglaskarr.com
Version: 2.0.7
--------------------------

Original Author
--------------------------
Author: Ryan Duff
Author URI: http://ryanduff.net

Installation
--------------------------
1. Upload all files to wp-content/plugins/wp-contact-form/
2. Activate the plugin on the plugin screen
3. Go to Options -> Contact Form and update the fields with your information
4. Create a post or a page and press the Contact Form quicktag where you want the form to be. If you don't see the Contact Form quicktag, you can alternatively copy and paste %%wpcontactform%% where you want it to appear.

Upgrading
--------------------------
1. If you are upgrading from an older version, delete your old options-contactform.php in wp-admin/ or wp-content/plugins/ and delete your wp-contactform.php in wp-content/plugins/
2. Upload all files to wp-content/plugins/wp-contact-form/
3. Your old options have been saved in the database and will appear when you visit options > contact form


Frequently Asked Questions
--------------------------
Q. How do I make a multi-select list?
A. For each option, simply put a pipe character in between.  Example: Option 1 | Option 2 | Option 3

Bugs
--------------------------
Please report bugs via:
http://www.douglaskarr.com/contact-me/

History
--------------------------
2.0.7 Uses wp_mail() and modifies it to route mail via SMTP - by Callum Macdonald at http://www.callum-macdonald.com
2.0.6 Added an id and name to the form so the submission does not interfere with other forms
2.0.5 Corrected minor bug
2.0.4 Added feature so the user could copy themselves on the submission
2.0.3 Corrected some minor format issues with the form HTML
2.0.2 Corrected an issue with options not changing on Internet Explorer
2.0.1 Fixed an issue with the list box not populating correctly with the full array of subjects
2.0.0 Rewrote major portions of the plugin and added the ability to have a multi-select list
1.9.0 Secured the posted data to eliminate the opportunity for cross-site scripting
1.8.0 Added option for client subject line concatenated to your default subject line
1.7.0 Added option for case sensitivity in the evaluation of the response
1.6.0 Ryan's site has seem to have disappeared so I'm going to add this to my project page

