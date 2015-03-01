=== Goo.gl ===
Contributors: kovshenin
Donate link: http://kovshenin.com/beer/
Tags: links, twitter, short, url, socialmedia, permalinks, redirect, trim, identi.ca, microblogging, shorturl, canonical, analytics
Requires at least: 3.0
Tested up to: 4.1
Stable tag: 1.4.3

Uses Google's URL shortener (Goo.gl) to create short links for your WordPress posts and track analytics.

== Description ==

Google has launched a URL Shortener API (Goo.gl) - one of the fastest and most reliable URL shortners out there. This plugin creates goo.gl short URLs for your posts, which then could be retrieved using the "Get Shortlink" button in your admin UI or the `wp_get_shortlink()` WordPress function. Goo.gl analytics also available!

== Installation ==

1. Upload archive contents to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You're done!

== Screenshots ==

1. Shortened URLs
2. Posts List and Analytics

== Change log ==

= 1.4.3 =
* Actually add the Google API key that was supposed to be added in 1.4.2.

= 1.4.2 =
* Added a Google API key which you can override with the `googl_api_key` filter.

= 1.4.1 =
* Placeholder fix in printf

= 1.4 =
* Using wrapper functions instead of WP_Http class
* Cleanup, coding standards, etc.
* Testing against newer versions of WordPress

= 1.3 =
* Fixing for when WP_DEBUG is true
* Fixing glitch on 404 pages
* Returns goo.gl shortlink for front page too
* Updates goo.gl shortlink on post update (for when permalink is changed)

= 1.2 =
* Minor bug fixing for failback
* WordPress 3.1 compatibility check

= 1.1 =
* Some code refactoring
* Posts list now with short links
* Links to Analytics

= 1.0 =
* Hurray
