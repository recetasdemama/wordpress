<?php
/*
Plugin Name: Stop User Enumeration
Plugin URI: http://locally.uk/wordpress-plugins/stop-user-enumeration/
Description: User enumeration is a technique used by hackers to get your login name if you are using permalinks. This plugin stops that.
Version: 1.3.1
Author: Locally Digital Ltd
Author URI: http://locally.uk
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( ! is_admin()){
    if ( ! is_admin()){
      if(preg_match('/(wp-comments-post)/', $_SERVER['REQUEST_URI']) === 0 ) {
         if (!empty($_POST['author'])) {
            ll_kill_enumeration();
         }
      }

    if(preg_match('/author=([0-9]*)/', $_SERVER['QUERY_STRING']) === 1)
    ll_kill_enumeration();

    add_filter('redirect_canonical','ll_detect_enumeration', 10,2);
	}
}

add_filter('redirect_canonical','ll_detect_enumeration', 10,2);
function ll_detect_enumeration ($redirect_url, $requested_url) {
if (preg_match('/\?author(%00[0%]*)?=([0-9]*)(\/*)/', $requested_url)===1  | ($_POST['author'])) {
     ll_kill_enumeration();
   } else {
     return $redirect_url;
   }
} 

function ll_kill_enumeration() {
     openlog('wordpress('.$_SERVER['HTTP_HOST'].')',LOG_NDELAY|LOG_PID,LOG_AUTH);
     syslog(LOG_INFO,"Attempted user enumeration from {$_SERVER['REMOTE_ADDR']}");
     closelog();
     wp_die('forbidden');
}

?>