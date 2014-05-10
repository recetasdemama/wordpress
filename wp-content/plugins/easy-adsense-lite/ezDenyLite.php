<?php

/*
  Copyright (C) 2008 www.ads-ez.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!function_exists("ezDenyLite")) {
  if (!function_exists('is_plugin_active')) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
  }

  function ezDenyLite($plg, $lite) {
    if (is_plugin_active($lite)) {
      add_action('init', function() {
        global $lite;
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        deactivate_plugins($lite);
      });
      printf("<div class='error'>" . __("%s: Another version of this plugin is active.<br />Please deactivate it before activating %s.", "easy-common") . "</div>", "<strong><em>$plg</em></strong>", "<strong><em>$plg</em></strong>");
      add_action('admin_footer-plugins.php', function() {
        global $plg;
        printf('<script>document.getElementById("message").innerHTML="' . "<span style='font-weight:bold;font-size:1.1em;color:red'>" . $plg . ": " . __("Pro Plugin is activated. Lite version is deactivated.", "easy-common") . "</span>" . '";</script>');
      });
    }
  }

}