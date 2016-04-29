<?php
/*
Plugin Name: Stop User Enumeration
Plugin URI: http://fullworks.net/wordpress-plugins/stop-user-enumeration/
Description: User enumeration is a technique used by hackers to get your login name if you are using permalinks. This plugin stops that.
Version: 1.3.4
Author: Fullworks Digital Ltd
Author URI: http://fullworks.net
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

if ( ! is_admin() && isset($_SERVER['REQUEST_URI'])){
      if(preg_match('/(wp-comments-post)/', $_SERVER['REQUEST_URI']) === 0 && !empty($_REQUEST['author']) ) {
            openlog('wordpress('.$_SERVER['HTTP_HOST'].')',LOG_NDELAY|LOG_PID,LOG_AUTH);
     		syslog(LOG_INFO,"Attempted user enumeration from {$_SERVER['REMOTE_ADDR']}");
     		closelog();
     		wp_die('forbidden');
      }
}

add_action('plugin_row_meta', 'sue_plugin_row_meta', 10, 2 );
function sue_plugin_row_meta( $links, $file = '' ){
    if( false !== strpos($file , '/stop-user-enumeration.php') ){
        $links[] = '<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4EMTVFMKXRRYY"><strong>Please Donate (even 50 cents)</strong></a>';
      }
    return $links;
}

?>
