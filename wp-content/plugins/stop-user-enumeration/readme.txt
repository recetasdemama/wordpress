=== Stop User Enumeration ===
Contributors: Locally Digital Ltd
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZEWW5LKK5995J
Tags: User Enumeration, Security, WPSCAN, fail2ban
Requires at least: 3.4
Tested up to: 4.0.1
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

User Enumeration is a method hackers and scanners use to get your username. This plugin stops it.
== Description ==
Even if you are careful and set your blogging nickname differently from your login id, if you are using permalinks it only takes a few seconds
to discover your real user name. This plugin stops user enumeration dead (like in use by WPSCAN), and additionally it will log an event
in your system log so you can use (optionally) fail2ban to block the probing IP.
== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==

= Are there any settings? =
No
= Will it work on Multisite? =
Yes
= Do I need fail2ban for this to work? =
No, but fail2ban will allow you to block IP addresses that attempt user enumeration.
= What do I do with the fail2ban file?=
Place  the file wordpress-userenum.conf in your fail2ban installation's filter.d directory.
edit your jail.local  to include lines like
`[wordpress-userenum]
enabled = true
filter = wordpress-userenumaction   = iptables-allports[name=WORDPRESS-USERENUM]
           sendmail-whois-lines[name=WORDPRESS-USERENUM, dest=youremail@yourdomain, logpath=/var/log/messages]
logpath = /var/log/messages
maxretry = 1
findtime = 600
bantime = 2500000`
Adjusted to your own requirements.

== Changelog ==
= 
= 1.3.0 =

* minor descriptive change
= 1.3.0 =

* code improvement  from Thomas van der Westen

= 1.2.8 =

* bug fix to allow comments to use author in url

= 1.2.8 =

* allow comments to use author in url

= 1.2.7 =

* bug fix to POST protection

= 1.2.6 =

* bug fix to POST protection

= 1.2.5 =

* Added protection against bypass using null bytes  (thanks to vunerbality identification and solution by cvcrcky )
* Added protection angainst POST bypass (thanks to vunerbaility identification by urbanadventurer and solution ideas from Ov3rfly and Malivuk )


= 1.2.4 =

* Added code to check whether not admin (to stop admin features failing) and changed trailing slash code to trap situation where not posts are found and user is displayed in title


= 1.2.3 =


* Fixed bug that stopped export in admin

= 1.2.2 =

* Added code to stop bypassing the check when a trailing slash is added

= 1.2.1 =
* minor change to handle a specific php issue with a certain version



= 1.1 =

* added close log
* corrected call to wp die

= 1.0 =
*  first release

== Upgrade notice ==





