<?php
/*
  Copyright (C) 2008 www.thulasidas.com

  This file is part of the programs "Easy AdSense", "AdSense Now!",
  "Theme Tweaker", "Easy LaTeX", "More Money" and "Easy Translator".

  These programs are free software; you can redistribute them and/or
  modify it under the terms of the GNU General Public License as
  published by the Free Software Foundation; either version 3 of the
  License, or (at your option) any later version.

  These programs are distributed in the hope that they will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with the programs.  If not, see <http://www.gnu.org/licenses/>.
 */

function makeTextWithTooltip($text, $tip, $title = '', $width = '') {
  if (!empty($title)) {
    $titleText = "TITLE, '$title',STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true,";
  }
  if (!empty($width)) {
    $widthText = "WIDTH, $width,";
  }
  $return = "<span style='text-decoration:none' " .
          "onmouseover=\"Tip('" . htmlspecialchars($tip) . "', " .
          "$widthText $titleText FIX, [this, 5, 5])\" " .
          "onmouseout=\"UnTip()\">$text</span>";
  return $return;
}

function makeTextWithTooltipTag($plg, $text, $tip, $title = '', $width = '') {
  if (!empty($title)) {
    $titleText = "TITLE, '$title',STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true,";
  }
  if (!empty($width)) {
    $widthText = "WIDTH, $width,";
  }
  $return = "<span style='text-decoration:none' " .
          "onmouseover=\"TagToTip('" . $plg . "', " .
          "$widthText $titleText FIX, [this, 5, 5])\" " .
          "onmouseout=\"UnTip()\">$text</span>";
  return $return;
}

function renderPlg($name, $plg) {
  if (!empty($plg['hide']) && $plg['hide']) {
    return;
  }
  $plugindir = get_option('siteurl') . '/' . PLUGINDIR . '/' . basename(dirname(__FILE__));
  if (!empty($plg['kind']) && $plg['kind'] != 'plugin') {
    return;
  }
  $value = '<em><strong>' . $plg['value'] . '</strong></em>';
  $desc = $plg['desc'];
  $title = $plg['title'];
  $url = 'http://www.thulasidas.com/plugins/' . $name;
  $link = '<b><a href="' . $url . '" target="_blank">' . $value . '</a></b>&nbsp; ';
  $text = $link . $desc;
  $price = $plg['price'];
  $onclick = "onclick=\"popupwindow('http://buy.thulasidas.com/$name','Get {$plg['value']}', 1024, 768);return false;\"";
  $moreInfo = "&nbsp;&nbsp;<a href='http://www.thulasidas.com/plugins/$name' title='More info about $value at Unreal Blog'>More Info</a> ";
  $liteVersion = "&nbsp;&nbsp; <a href='http://buy.thulasidas.com/lite/$name.zip' title='Download the Lite version of $value'>Get Lite Version</a> ";
  $proVersion = "&nbsp;&nbsp; <a href='http://buy.thulasidas.com/$name' title='Buy the Pro version of $value for \$$price' $onclick>Get Pro Version</a><br />";
  $why = "<a href='http://buy.thulasidas.com/$name' title='Buy the Pro version of the $name plugin' $onclick><img src='$plugindir/ezpaypal.png' alt='ezPayPal -- Instant PayPal Shop.' class='alignright' /></a>
<br />" . $plg['pro'];
  echo "<li>" . makeTextWithTooltip($text, $title, $value, 350) .
  "<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
  makeTextWithTooltip($moreInfo, "Read more about $value at its own page.<br />" . $title, "More Information about $value", 300) .
  makeTextWithTooltip($liteVersion, $title, "Download $value - the Lite version", 300) .
  makeTextWithTooltipTag($name, $proVersion, $why, "Get $value Pro!", 300) .
  "<span id=$name> $why </span>" .
  "</li>\n";
}

function renderBook($name, $plg) {
  $plugindir = get_option('siteurl') . '/' . PLUGINDIR . '/' . basename(dirname(__FILE__));
  if (empty($plg['kind']) || $plg['kind'] != 'book') {
    return;
  }
  $value = '<em><strong>' . $plg['value'] . '</strong></em>';
  $desc = $plg['desc'];
  $title = $plg['title'];
  $url = $plg['url'];
  $link = '<b><a href="' . $url . '" target="_blank">' . $value . '</a></b>&nbsp; ';
  $text = $link . $desc;
  $price = $plg['price'];
  $onclick = "onclick=\"popupwindow('http://buy.thulasidas.com/$name','Get {$plg['value']}', 1024, 768);return false;\"";
  $moreInfo = "&nbsp;&nbsp; <a href='$url' title='More info about $value' target=_blank>More Info</a> ";
  $amazon = $plg['amazon'];
  if (!empty($amazon)) {
    $buyAmazon = "&nbsp;&nbsp; <a href='$amazon' title='Get $value from Amazon.com' target=_blank>Get it at Amazon</a> ";
  }
  $buyNow = "&nbsp;&nbsp; <a href='http://buy.thulasidas.com/$name' title='Buy and download $value for \$$price' target=_blank $onclick>Buy and Download now!</a><br />";
  $why = "<a href='http://buy.thulasidas.com/$name' title='$name' $onclick><img src='$plugindir/ezpaypal.png' alt='ezPayPal -- Instant PayPal Shop.' class='alignright' /></a>
<br />" . $title . $desc . " $value costs only \$$price -- direct from the author.";
  echo "<li>" . makeTextWithTooltip($text, $title, $value, 350) .
  "<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
  makeTextWithTooltip($moreInfo, "Read all about $value at its own site.<br />", "$value", 300) .
  makeTextWithTooltip($buyAmazon, $title, "Buy $value from Amazon", 350) .
  makeTextWithTooltipTag("$name-book", $buyNow, $why, "Buy $value!", 300) .
  "<span id=$name-book> $why </span>" .
  "</li>\n";
}
?>

<table class="form-table" >
  <tr>
    <td>
      <ul style="padding-left:10px;list-style-type:circle; list-style-position:inside;" >
        <li>
            <?php _e('Check out my other plugin and PHP efforts:', 'easy-adsenser'); ?>
          <script type = "text/javascript">
            function popupwindow(url, title, w, h) {
              var left = (screen.width / 2) - (w / 2);
              var top = (screen.height / 2) - (h / 2);
              return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
            }
          </script>
          <ul style="margin-left:0px; padding-left:30px;list-style-type:square; list-style-position:inside;" >

<?php
$myPluginsU = array_unique($myPlugins, SORT_REGULAR);
unset($myPluginsU[$plgName]);
foreach ($myPluginsU as $k => $p) {
  if (isset($p['hide']) || isset($p['kind'])) {
    unset($myPluginsU[$k]);
  }
}
$keys = array_rand($myPluginsU, 3);
foreach ($keys as $name) {
  if ($name != $plgName) {
    renderPlg($name, $myPluginsU[$name]);
  }
}
?>

          </ul>
        </li>

        <li>
<?php _e('My Books -- on Physics, Philosophy, making Money etc:', 'easy-adsenser'); ?>

          <ul style="margin-left:0px; padding-left:30px;list-style-type:square; list-style-position:inside;" >

  <?php
  foreach ($myPlugins as $name => $plg) {
    renderBook($name, $plg);
  }
  ?>

          </ul>
        </li>

      </ul>

    </td>
  </tr>

<?php
echo '</table>';

