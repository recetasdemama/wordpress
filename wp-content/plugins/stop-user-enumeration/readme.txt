=== Stop User Enumeration ===
Contributors: fullworks
Tags: User Enumeration, Security, WPSCAN, fail2ban,
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4EMTVFMKXRRYY
Requires at least: 3.4
Requires PHP: 5.3
Tested up to: 4.9.8
Stable tag: 1.3.16
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Helps secure your site against hacking attacks through detecting  User Enumeration

== Description ==

Stop User Enumeration is a security plugin designed to detect and prevent hackers scanning your site for user names.

User Enumeration is a type of attack where nefarious parties can probe your website to discover your login name. This is often a pre-cursor to brute-force password attacks. Stop User Enumeration helps block this attack and even allows you to log IPs launching these attacks to block further attacks in the future.

If you are on a VPS or dedicated server, as the attack IP is logged, you can use (optional additional configuration) fail2ban to block the attack directly at your server's firewall, a very powerful solution for VPS owners to stop brute force attacks as well as DDoS attacks.

If you don't have access to install fail2ban ( e.g. on a Shared Host ) you can still use this plugin. To make it more effective, you can also install [Fullworks Firewall](https://en-gb.wordpress.org/plugins/fullworks-firewall/), which will work in a similar way to fail2ban but on your WordPress site.

Since WordPress 4.5 user data can also be obtained by API calls without logging in, this is a WordPress feature, but if you don't need it to get user data, this
plugin will restrict and log that too.




== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. If needed to change defaults settings, visit the settings page

== Frequently asked questions ==

= It doesn't seem to work! ==
Are you logged in?  This plugin won't do anything for logged in users, it only works when you are logged out. This is the way it is designed. A common mistake is to install the plugin and test it, while still logged in as admin.
= Are there any settings? =
Yes, but the default ones are fine for most cases
= This doesn't work with PHP 5.2! =
This plugin does not support PHP 5.2. PHP 5.2 is very old and you really need to sort out your hosting, running version of software way past its supported end of life is a security risk.
= Will it work on Multisite? =
Yes
= Why don't I just block with .htaccess =
A .htaccess solution is insufficient for sevaral reasons, but most published posts on the subject do not cover POST blocking, REST API blocking and inadvertently block admin users access. And don't log the IP to a firewall, the major benefit!
= Does it break anything? =
If a comment is left by someone just giving a number that comment would be forbidden, as it is assume a hack attempt, but the plugin has a bit of code that strips out numbers from comment author names
= Do I need fail2ban for this to work? =
No, but fail2ban will allow you to block IP addresses at your VPS / Dedicated server firewall that attempt user enumeration.
If you don't have root access ( e.g. on shared hosting ) so can't install fail2ban you can install and use [Fullworks Firewall](https://wordpress.org/plugins/fullworks-firewall/)
Stop User Enumeration will automatically detect this and will report malicious IPs.
= What is the fail2ban config?=
An fail2ban config file, wordpress-userenum.conf is found in the plugin directory stop-user-enumeration/fail2ban/filter.d
= What needs to go in the fail2ban jail.local?=
An example jail.local is found in plugin directory stop-user-enumeration/fail2ban
= If I have Fullworks Firewall installed, is there anything I need to do? =
No, the plugin automatically detects [Fullworks Firewall](https://wordpress.org/plugins/fullworks-firewall/) the plugin and sends the suspect IPs directly


== Changelog ==
= 1.3.17 =
* changed settings page to stop random metaboxes

= 1.3.16 =
* Reworked settings page

= 1.3.15 =
* fix to ensure scripts not enqueued unless required

= 1.3.14 =
* fix double plugin header

= 1.3.13 =
* ability to link to shared host firewall ( fullworks-firewall )

= 1.3.12 =

* Resolve some missing files

= 1.3.11 =

* Added language localisation for translations
* Added Spanish translation

= 1.3.10 =

Fixed unused javascript & css in settings page

= 1.3.9 =

Added language settings to allow translation.

Sanitized text being written to syslog

Closed potential REST API bypass

= 1.3.8 =

Security fix to stop XSS exploit

Also coded so should work with PHP 5.3 - although PHP 5.3. has been end of life for over two years it seems some hosts still use this. This is a security risk in its own right and
sites using PHP 5.3 should try to upgrade to a supported version of PHP, but this change is for backward compatibility.

= 1.3.7 =

Fix to allow deprecated PHP Version 5.4 to work, as 5.4 seems to still be in common use despite end of life

Note this code wont work on PHP 5.3

= 1.3.6 =

Fix PHP error

= 1.3.5 =

* full rewrite
* Changed detection rules to stop a reported bypass
* Added detection and suppression of REST API calls to user data
* Added settings page to allow REST API calls or stop system logging as required
* Added code to remove numbers from comment authors, and setting to turn that off

[](http://coderisk.com/wp/plugin/stop-user-enumeration/RIPS-1o0cni0Kbq)


