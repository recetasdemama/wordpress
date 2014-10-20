<?php

/*
  Copyright (C) 2008 www.ads-ez.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or (at
  your option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!class_exists('EzAdmin')) {

  class EzAdmin {

    var $plgURL, $plgFile, $plg, $slug, $name;
    var $killAuthor = false;

    function __construct($plg, $slug, $plgURL) {
      $this->plg = $plg;
      $this->slug = $slug;
      $this->plgURL = $plgURL;
    }

    function __destruct() {

    }

    function EzAdmin($plg, $slug, $plgURL) {
      if (version_compare(PHP_VERSION, "5.0.0", "<")) {
        $this->__construct($plg, $slug, $plgURL);
        register_shutdown_function(array($this, "__destruct"));
      }
    }

    function renderNags(&$options) {
      if (empty($options['kill_rating'])) {
        $this->renderRating();
      }
      else {
        echo "<input type='hidden' name='kill_rating' value='true' />";
      }
//      if (empty($options['kill_invites'])) {
//        $this->renderInvite();
//      }
//      else {
//        echo "<input type='hidden' name='kill_invites' value='true' />";
//      }
    }

    function renderInvite() {
      if ($this->killAuthor) {
        return;
      }
      $plg = $this->plg;
      $slug = $this->slug;
      $plgLongName = $plg['value'];
      $plgPrice = $plg['price'];
      $yesTip = sprintf(__('Buy %s Pro for $%s. PayPal payment. Instant download.', 'easy-common'), $plgLongName, $plgPrice);
      $yesTitle = __('Get the Pro version now!', 'easy-common');
      $noTip = __('Continue using the Lite version, and hide this message. After clicking this button, please remember to save your options to hide this box for good.', 'easy-common');
      $noTitle = __('Stay Lite', 'easy-common');
      $plgKey = $this->getPlgKey();
      $onClick = addslashes("onclick=\"popupwindow('http://www.thulasidas.com/promo.php?key=$plgKey','Get Pro', 1024, 768);return false;\"");
      $hideTip = htmlspecialchars(__('Click the link to hide this box. After clicking this link, please remember to save your options to hide this box for good.', 'easy-common') . "<br /><a style=\"color:red;font-weight:bold\" href=\"http://www.thulasidas.com/promo.php?key=$plgKey\" target=_blank $onClick>" . __("Limited Time Offer. Get the Pro version for less than a dollar!", 'easy-common') . "</a>");
      if (empty($plg['benefits'])) {
        return;
      }
      $benefits = $plg['benefits'];

      $s1 = __("Want More Features?", 'easy-common');
      $s2 = __("The Pro version of this plugin gives you more features and benefits.", 'easy-common');
      $s3 = __("For instance", 'easy-common');
      $s4 = __("And much more.", 'easy-common');
      $s5 = __("New features and bug fixes will first appear in the Pro version before being ported to this freely distributed Lite edition.", 'easy-common');
      $s6 = __("Go Pro!", 'easy-common');
      $s7 = __("No thanks", 'easy-common');
      $s8 = __("Do not show this anymore", 'easy-common');
      $s9 = sprintf(__("Thank you for using %s!", 'easy-common'), $plgLongName);
      $s10 = __("Please Save Options to hide this box forever", 'easy-common');

      echo <<<ENDINVITE
<input type="hidden" id="kill_invites" name="kill_invites" value="" />
<div class="updated" id="tnc">
<p><h3>$s1 <a href="#" onmouseover="Tip('$yesTip', WIDTH, 200, CLICKCLOSE, true, TITLE, '$yesTitle')" onmouseout="UnTip()" onclick = "buttonwhich('Yes')">Go Pro!</a></h3>
$s2 $s3,
<ol>
$benefits
</ol>
$s4 $s5<br />
<input onmouseover="Tip('$yesTip', WIDTH, 200, CLICKCLOSE, true, TITLE, '$yesTitle')" onmouseout="UnTip()" type = "button" id = "ybutton" value = "$s6" onclick = "buttonwhich('Yes')" />
<input onmouseover="Tip('$noTip', WIDTH, 200, CLICKCLOSE, true, TITLE, '$noTitle')" onmouseout="UnTip()" type = "button" id = "nbutton" value = "$s7" onclick = "buttonwhich('No')" />
<small style='font-weight:normal;'><a id='hideInvite' href='#' style='float:right; display:block; border:none;'  onmouseover="Tip('$hideTip', WIDTH, 200, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 5, 5], TITLE, 'Hide this Box')" onclick = "buttonwhich('No')">
$s8</a></small>
<script type = "text/javascript">
function hideInvite() {
  document.getElementById("tnc").style.display = 'none';
}
function buttonwhich(message) {
  document.getElementById("ybutton").style.display = 'none';
  document.getElementById("nbutton").disabled = 'true';
  document.getElementById("kill_invites").value = 'true' ;
  setTimeout('hideInvite()', 5000);
  if (message == 'Yes') popupwindow('http://buy.thulasidas.com/$slug', '$s6', 1024, 768) ;
  if (message == 'No') {
    document.getElementById("nbutton").value = '$s9 $s10';
    document.getElementById("hideInvite").innerHTML = '$s10';
  }
}
</script>
</div>
ENDINVITE;
    }

    function getPlgKey() {
      $plgKey = basename($this->plgFile, '.php');
      if (in_array($plgKey, array('easy-ads', 'google-adsense', 'theme-tweaker'))) {
        $plgKey .= '-lite';
      }
      if ($plgKey == 'easy-paypal-lite') {
        $plgKey = 'easy-paypal-lte';
      }
      return $plgKey;
    }

    function renderRating($killable = true) {
      if ($this->killAuthor) {
        return;
      }
      $plgFile = $this->plgFile;
      $plg = $this->plg;
      $plgCTime = filemtime($plgFile);
      $plgLongName = $plg['value'];
      $plgKey = $this->getPlgKey();
      $onClick = addslashes("onclick=\"popupwindow('http://www.thulasidas.com/promo.php?key=$plgKey','Get Pro', 1024, 768);return false;\"");
      $hideTip = htmlspecialchars(__('Click the link to hide this box. After clicking this link, please remember to save your options to hide this box for good.', 'easy-common') . "<br /><a style=\"color:red;font-weight:bold\" href=\"http://www.thulasidas.com/promo.php?key=$plgKey\" target=_blank $onClick>" . __("Limited Time Offer. Get the Pro version for less than a dollar!", 'easy-common') . "</a>");

      if (time() > $plgCTime + (60 * 60 * 24 * 30)) {
        $msg = __("You've installed this plugin over a month ago.", 'easy-common');
      }
      else {
        $msg = __("You will find it feature-rich and robust.", 'easy-common');
      }
      $display = '';
      if (!$killable) {
        $display = "style='display:none'";
      }

      $s1 = __("If you are satisfied with how well it works, why not rate it and recommend it to others?", 'easy-common');
      $s2 = __("Click here", 'easy-common');
      $s3 = __("Do not show this anymore", 'easy-common');
      $s4 = __("Please Save Options to hide this box forever", 'easy-common');

      echo <<<ENDRATING
<div class='updated' id='rating'>
<p>Thanks for using <i><b>$plgLongName</b></i>! $msg <br />
$s1 <a href='http://wordpress.org/extend/plugins/$plgKey/' onclick="popupwindow('http://wordpress.org/extend/plugins/$plgKey/','$s2', 1024, 768);return false;">$s2</a>
<small style='font-weight:normal;'><a id='hideRating' $display href='#' style='float:right; display:block; border:none;'  onmouseover="Tip('$hideTip', WIDTH, 200, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 5, 5], TITLE, 'Hide this Box')" onclick = "hideme()">
$s3</a></small></p></div>
<input type="hidden" id="kill_rating" name="kill_rating" value="" />
<script type = "text/javascript">
function hideRating() {
  document.getElementById("rating").style.display = 'none';
}
function hideme() {
  document.getElementById("kill_rating").value = 'true' ;
  document.getElementById("hideRating").innerHTML = '$s4';
  setTimeout('hideRating()', 4000);
}
</script>
ENDRATING;
    }

    function renderHeadText() {
      $plgURL = $this->plgURL;
      $plg = $this->plg;
      $slug = $this->slug;
      $value = '<em><strong>' . $plg['value'] . '</strong></em>';
      $toolTip = $plg['title'];
      $price = $plg['price'];
      $onclick = "onclick=\"popupwindow('http://buy.thulasidas.com/$slug','Get Pro}', 1024, 768);return false;\"";

      $s2 = __('Lite Version', 'easy-common');
      $s3 = sprintf(__('Buy the Pro version of %s for $%.2f', 'easy-common'), $plg['value'], $price);
      $s4 = __('Instant download link.', 'easy-common');
      $s5 = __('Pro Version', 'easy-common');
      $moreInfo = "$s2 and <b><a href='http://buy.thulasidas.com/$slug' title='$s3. $s4' $onclick>$s5</a></b>";
      $toolTip .= addslashes('<br />' . $moreInfo);
      $why = addslashes($plg['pro']);
      $version = 'Lite';

      $s6 = sprintf(__('You are using the %s version of %s, which is available in two versions:', 'easy-common'), $version, $value);
      $s7 = sprintf(__('And it costs only $%.2f!', 'easy-common'), $price);
      $s8 = __('Get the Pro version now!', 'easy-common');

      $plgKey = $this->getPlgKey();
      $promoClick = "onclick=\"popupwindow('http://www.thulasidas.com/promo.php?key=$plgKey','Get Pro', 1024, 768);return false;\"";
      $promoTip = "<a style=\"color:red;font-weight:bold\" href=\"http://www.thulasidas.com/promo.php?key=$plgKey\" target=_blank $promoClick>" . __("Limited Time Offer. Get the Pro version for less than a dollar!", 'easy-common') . "</a>";

      echo "<b>$s8</b>
<a href='http://buy.thulasidas.com/$slug' title='$s3. $s4' $onclick><img src='$plgURL/ezpaypal.png' alt='ezPayPal' class='alignright'/></a>
<br />
$s6
<ul><li>
$moreInfo
</li>
<li>$why $s7</li>
</ul>$promoTip";
    }

    function renderProText() {
      echo "<div id='pro' style='display:none'>";
      $this->renderHeadText();
      echo "</div>";
      if ($this->killAuthor) {
        return;
      }
      $plg = $this->plg;
      $slug = $this->slug;
      $value = '<em><strong>' . $plg['value'] . '</strong></em>';
      $filter = '';
      if (stripos($slug, 'adsense') !== FALSE) {
        $filter = __("e.g., a filter to ensure AdSense policy compliance.", 'easy-common');
      }
      $toolTip = $plg['title'];
      $price = $plg['price'];
      $onclick = "onclick=\"popupwindow('http://buy.thulasidas.com/$slug','Get Pro', 1024, 768);return false;\"";

      $s1 = sprintf(__('Download the Lite version of %s', 'easy-common'), $plg['value']);
      $s2 = __('Lite Version', 'easy-common');
      $s3 = sprintf(__('Buy the Pro version of %s for $%.2f', 'easy-common'), $plg['value'], $price);
      $s4 = __('Pro Version', 'easy-common');
      $s5 = __('Buy the Pro Version', 'easy-common');

      $moreInfo = "&nbsp; <a href='http://buy.thulasidas.com/lite/$slug.zip' title='$s1'>$s2 </a>&nbsp; <a href='http://buy.thulasidas.com/$slug' title='$s3' $onclick>$s4</a>";
      $toolTip .= addslashes('<br />' . $moreInfo);
      echo "<div style='background-color:#ffcccc;padding:5px;border: solid 1px;'>
<div style='font-size:14px;color:#a48;font-variant: small-caps;text-decoration:underline;text-align:center;' $onclick onmouseover=\"TagToTip('pro', WIDTH, 300, TITLE, '$s5',STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 5, 5])\"><b>$s4</b></div>";

      $s8 = sprintf(__('It costs only $%.2f!', 'easy-common'), $price);
      $s9 = __('Instant download link.', 'easy-common');
      $value .= '<b><i> Lite</i></b>';
      $s10 = sprintf(__('Thank you for using %s. The "Pro" version gives you more options.', 'easy-common'), $value);
      $s11 = __("Consider buying it.", 'easy-common');

      $plgKey = $this->getPlgKey();
      $promoClick = addslashes("onclick=\"popupwindow('http://www.thulasidas.com/promo.php?key=$plgKey','Get Pro', 1024, 768);return false;\"");
      $promoTip = htmlspecialchars("$s3. $s9<br /><a style=\"color:red;font-weight:bold\" href=\"http://www.thulasidas.com/promo.php?key=$plgKey\" target=_blank $promoClick>" . __("Limited Time Offer. Get the Pro version for less than a dollar!", 'easy-common') . "</a>");

      echo "$s10 $filter $s11 <br /><a $onclick href='http://buy.thulasidas.com/$slug' title='$s3. $s9' onmouseover=\"Tip('$promoTip', WIDTH, 200, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 5, 5], TITLE, 'Limited Time Offer')\" target=_blank>$s8</a>";
      echo "</div>";
    }

    function renderAffiliate() {
      if ($this->killAuthor) {
        return;
      }
      $plgURL = $this->plgURL;

      $select = rand(0,4);
      echo "<div style='padding:0px;border:none;text-align:center' id='support' onmouseover=\"TagToTip('proservices', WIDTH, 295, TITLE, 'Professional Services', FIX, [this, 25, 0], CLICKCLOSE, true, CLOSEBTN, true)\" ><a href='http://www.thulasidas.com/professional-php-services/' target='_blank'><img src='$plgURL/300x250-0$select.jpg' border='0' alt='Professional Services from the Plugin Author' /></a></div>";
    }

    function renderSupportText() {
      if ($this->killAuthor) {
        return;
      }
      $plg = $this->plg;
      $slug = $this->slug;
      $long = $plg['long'];
      $value = '<em><strong>' . $plg['value'] . '</strong></em>';
      $supportText = "<div style=\"background-color:#cff;padding:5px;border: solid 1px\" id=\"support\"><b>Support $value. <a href=\"http://buy.thulasidas.com/$slug\" title=\"Pro version of this plugin. Instant download link.\" onclick=\"popupwindow('http://buy.thulasidas.com/$slug','Get {$plg['value']}', 1024, 768);return false;\">Go Pro!</a></b>";
      if ($long) {
        $longText = "Speed up your blog: ";
      }
      else {
        $longText = '';
      }
      $supportText .= "<br />$longText<span onmouseover=\"TagToTip('maxcdn', WIDTH, 300, TITLE, 'What is MaxCDN?',STICKY, 1, CLOSEBTN, true, FIX, [this, -150, 2])\"><a href='http://tracking.maxcdn.com/c/95082/3964/378' target='_blank'>Use MaxCDN!</a></span>";
      if ($long) {
        $longText = "WordPress Hosting for ";
      }
      else {
        $longText = 'Hosting for ';
      }
      $supportText .= "<br />$longText<span onmouseover=\"TagToTip('arvixe', WIDTH, 300, TITLE, 'Arvixe - My favorite provider!',STICKY, 1, CLOSEBTN, true, FIX, [this, -200, 2])\"><a href='http://www.arvixe.com/1933.html' target='_blank'>just $4/month</a></span>. ";
      if ($long) {
        $longText = "My books on ";
      }
      else {
        $longText = 'Books: ';
      }
      $supportText .= "<br />$longText<span style=\"text-decoration:underline\" onmouseover=\"TagToTip('unreal', WIDTH, 205, TITLE, 'Buy &lt;em&gt;The Unreal Universe&lt;/em&gt;',STICKY, 1, CLOSEBTN, true, FIX, [this, 5, 2])\"><b><a href='http://buy.thulasidas.com/unreal' target='_blank'>Physics &amp; Philosophy</a></b></span> or ";
      $supportText .= "<span style=\"text-decoration:underline\" onmouseover=\"TagToTip('pqd', WIDTH, 205, TITLE, '&lt;em&gt;Principles of Quant. Devel.&lt;/em&gt;',STICKY, 1, CLOSEBTN, true, FIX, [this, 5, 2])\"><b><a href='http://buy.thulasidas.com/pqd' target='_blank'>Money &amp; Finance</a></b></span>.</div>";
      echo $supportText;
    }

    function renderTipDivs() {
      $plgURL = $this->plgURL;
      echo <<<ENDDIVS
<div id="arvixe" style='display:none;'>
  <a href="http://www.arvixe.com/1933-27-1-310.html" target="_blank"><b>Arvixe</b></a> is my favorite hosting provider. Friendly service, extremely competitive rates, and of course a great affiliate program. My own WordPress blog and a dozen websites are hosted on their VPS. If you are looking for a new hosting provider, do check them out!
</div>

 <span id="proservices" style='display:none;'>
      The author of this plugin may be able to help you with your WordPress or plugin customization needs and other PHP related development. <a href='http://www.thulasidas.com/professional-php-services/' target='_blank'>Contact me</a> if you find a plugin that almost, but not quite, does what you are looking for, or if you need any other professional services. If you would like to see my credentials, take a look at <a href='http://www.thulasidas.com/col/Manoj-CV.pdf' target='_blank'>my CV</a>.
</span>

 <span id="superlinks" style='display:none;'>
  <a href='http://superlinks.com' target='_blank'><b>Superlinks</b></a> is an advertising network that can give AdSense a run for its money. It gives you access to Google AdExchange program, normally reserved for very high volume publishers. I have partnered with Superlinks to give you access to their offerings. If your site qualifies for Google AdX, you stand to make significantly higher returns than AdSense. Give it a shot!
</span>

 <span id="maxcdn" style='display:none;'>
  <a href='http://tracking.maxcdn.com/c/95082/3964/378' target='_blank'><b>MaxCDN</b></a> is a professional content delivery network. Easiest and most effective way to optimize your blog performance. Compatible with caching plugins, 24x7 professional support. Faster than most CDN providers in the continental US. Cheaper and better than most. Check it out now!
</span>

<span id="dropbox" style='display:none;'>
  Dropbox! gives you 2GB of network (cloud) storage for free, which I find quite adequate for any normal user. (That sounds like the famous last words by Bill Gates, doesn’t it? “64KB of memory should be enough for anyone!”) And, you can get 250MB extra for every successful referral you make. That brings me to my ulterior motive – please use this link to sign up. When you do, I get 500MB extra. Don’t worry, you get 500MB extra as well. So I can grow my online storage up to 18GB, which should keep me happy for a long time. Thank you!
</span>

<div id="unreal" style="margin-left:auto;margin-right:auto;width:200px;display:none;">
<div style="text-align:center;width:200px;padding:1px;background:#aad;margin:2px;">
<div style="text-align:center;width:192px;height:180px;padding:2px;border:solid 1px #000;background:#ccf;margin:1px;">
<a style="text-decoration:none;" href="http://buy.thulasidas.com/unreal-universe" title="Find out more about The Unreal Universe and buy it ($1.49 for eBook, $15.95 for paperback). It will change the way you view life and reality!">
<span style="font-size:14px;font-family:arial;color:#a48;font-variant: small-caps;"><b>The Unreal Universe</b></span><br />
<small style="font-size:12px;font-family:arial;color:#000;">
A Book on Physics and Philosophy
</small>
</a>
<hr />
<table style="border-width:0;padding:2px;width:100%;margin-left:auto;margin-right:auto;border-spacing:0;border-collapse:collapse;">
<tr><td style="width:65%">
<a style="text-decoration:none;" href="http://buy.thulasidas.com/unreal-universe" title="Find out more about The Unreal Universe and buy it ($1.49 for eBook or Kindle, $15.95 for paperback). It will change the way you view life and reality!">
<small style="font-size:10px;font-family:arial;color:#000;">
Pages: 292<br />
(282 in eBook)<br />
Trimsize: 6" x 9" <br />
Illustrations: 34<br />
(9 in color in eBook)<br />
Tables: 8 <br />
Bibliography: Yes<br />
Index: Yes<br />
ISBN: 9789810575946&nbsp;<br />
<span style="font-color=#ff0000;"><b>Only $1.49!</b></span>
</small>
</a>
</td>
<td>
<a style="text-decoration:none;" href="http://buy.thulasidas.com/unreal-universe" title="Find out more about The Unreal Universe and buy it ($1.49 for eBook or Kindle, $15.95 for paperback). It will change the way you view life and reality!">
<img class="alignright" src="$plgURL/unreal.gif" alt="TheUnrealUniverse" title="Read more about The Unreal Universe" />
</a>
</td>
</tr>
</table>
</div>
</div>
</div>

<div id="pqd" style="margin-left:auto;margin-right:auto;width:200px;display:none;">
<div style="text-align:center;width:200px;padding:1px;background:#000;margin:2px;">
<div style="text-align:center;width:190px;height:185px;padding:2px;padding-top:1px;padding-left:4px;border:solid 1px #fff;background:#411;margin:1px;">
<a style="text-decoration:none;" href="http://buy.thulasidas.com/pqd" title="Buy the companion eBook to Principles of Quantitative Development from the author (only $5.49)">
<span style="font-size:14px;font-family:arial;color:#fff;font-variant: small-caps;">A Remarkable Book from Wiley-Finance</span>
</a>
<hr />
<table style="border-width:0;padding:2px;width:100%;margin-left:auto;margin-right:auto;border-spacing:0;border-collapse:collapse;">
<tr><td style="padding:0px">
<div style="border:solid 1px #faa;height:126px;width:82px;">
<a style="text-decoration:none;" href="http://buy.thulasidas.com/pqd" title="Buy the companion eBook to Principles of Quantitative Development from the author (only $5.49)">
<img src="$plgURL/pqd-82x126.gif" alt="PQD" title="Buy the companion eBook to Principles of Quantitative Development from the author (only $5.49)" />
</a>
</div>
</td>
<td style="padding:3px">
<a style="text-decoration:none;" href="http://buy.thulasidas.com/pqd" title="Buy the companion eBook to Principles of Quantitative Development from the author (only $5.49)">
<em style="font-size:14px;font-family:arial;color:#fff;">"An excellent book!"</em><br />
<small style="font-size:13px;font-family:arial;color:#faa;">&nbsp;&nbsp;&#8212; Paul Wilmott</small>
<br />
<small style="font-size:11px;font-family:arial;color:#fff;">
Want to break into the lucrative world of trading and quantitative finance? You <b>need </b> this book!
</small>
</a>
</td>
</tr>
</table>
</div>
</div>
</div>
ENDDIVS;
    }

    static function makeTextWithTooltip($text, $tip, $title = '', $width = '') {
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

    static function makeTextWithTooltipTag($plg, $text, $tip, $title = '', $width = '') {
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

    function renderPlg($slug, $plg) {
      if ($this->killAuthor) {
        return;
      }
      $plgURL = $this->plgURL;
      if (!empty($plg['hide']) && $plg['hide']) {
        return;
      }
      if (!empty($plg['kind']) && $plg['kind'] != 'plugin') {
        return;
      }
      $value = '<em><strong>' . $plg['value'] . '</strong></em>';
      $desc = $plg['desc'];
      $title = $plg['title'];
      $url = 'http://www.thulasidas.com/plugins/' . $slug;
      $link = '<b><a href="' . $url . '" target="_blank">' . $value . '</a></b>&nbsp; ';
      $text = $link . $desc;
      $price = $plg['price'];
      $onclick = "onclick=\"popupwindow('http://buy.thulasidas.com/$slug','Get {$plg['value']}', 1024, 768);return false;\"";
      $moreInfo = "&nbsp;&nbsp;<a href='http://www.thulasidas.com/plugins/$slug' title='More info about $value at Unreal Blog'>More Info</a> ";
      $liteVersion = "&nbsp;&nbsp; <a href='http://buy.thulasidas.com/lite/$slug.zip' title='Download the Lite version of $value'>Get Lite Version</a> ";
      $proVersion = "&nbsp;&nbsp; <a href='http://buy.thulasidas.com/$slug' title='Buy the Pro version of $value for \$$price' $onclick>Get Pro Version</a><br />";
      $why = "<a href='http://buy.thulasidas.com/$slug' title='Buy the Pro version of the $slug plugin' $onclick><img src='$plgURL/ezpaypal.png' alt='ezPayPal -- Instant PayPal Shop.' class='alignright' /></a>
<br />" . $plg['pro'];
      echo "<li>" . self::makeTextWithTooltip($text, $title, $value, 350) .
      "<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
      self::makeTextWithTooltip($moreInfo, "Read more about $value at its own page.<br />" . $title, "More Information about $value", 300) .
      self::makeTextWithTooltip($liteVersion, $title, "Download $value - the Lite version", 300) .
      self::makeTextWithTooltipTag($slug, $proVersion, $why, "Get $value Pro!", 300) .
      "<span id=$slug> $why </span>" .
      "</li>\n";
    }

    function renderBook($slug, $plg) {
      if ($this->killAuthor) {
        return;
      }
      $plgURL = $this->plgURL;
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
      $onclick = "onclick=\"popupwindow('http://buy.thulasidas.com/$slug','Get {$plg['value']}', 1024, 768);return false;\"";
      $moreInfo = "&nbsp;&nbsp; <a href='$url' title='More info about $value' target=_blank>More Info</a> ";
      $amazon = $plg['amazon'];
      if (!empty($amazon)) {
        $buyAmazon = "&nbsp;&nbsp; <a href='$amazon' title='Get $value from Amazon.com' target=_blank>Get it at Amazon</a> ";
      }
      $buyNow = "&nbsp;&nbsp; <a href='http://buy.thulasidas.com/$slug' title='Buy and download $value for \$$price' target=_blank $onclick>Buy and Download now!</a><br />";
      $why = "<a href='http://buy.thulasidas.com/$slug' title='$slug' $onclick><img src='$plgURL/ezpaypal.png' alt='ezPayPal -- Instant PayPal Shop.' class='alignright' /></a>
<br />" . $title . $desc . " $value costs only \$$price -- direct from the author.";
      echo "<li>" . self::makeTextWithTooltip($text, $title, $value, 350) .
      "<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
      self::makeTextWithTooltip($moreInfo, "Read all about $value at its own site.<br />", "$value", 300) .
      self::makeTextWithTooltip($buyAmazon, $title, "Buy $value from Amazon", 350) .
      self::makeTextWithTooltipTag("$slug-book", $buyNow, $why, "Buy $value!", 300) .
      "<span id=$slug-book> $why </span>" .
      "</li>\n";
    }

    function getPlgInfo() {
      $me = $this->slug;
      $plugins = get_plugins();
      $ret = array('Version' => '', 'Info' => '');
      $break = '';
      foreach ($plugins as $k => $p) {
        $baseDir = dirname($k);
        $baseDirSmall = str_replace("-lite", "", $baseDir);
        if ($baseDir == $me || $baseDirSmall == $me) {
          $version = $p['Version'];
          if (!empty($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
          }
          else {
            $referer = 'Unknown';
          }
          $info = "$break{$p['Title']} V{$p['Version']} (Referer: $referer)";
          $ret[] = array('Version' => $version, 'Info' => $info);
        }
      }
      return $ret;
    }

    function renderSupport() {
      echo '<div style="background-color:#fcf;padding:5px;border: solid 1px;margin:5px;">
';
      $plgURL = $this->plgURL;
      $plg = $this->plg;
      $slug = $this->slug;
      $value = $plg['value'];
      $url = 'http://www.thulasidas.com/plugins/' . $slug . '#faq';
      $link = '<a href="' . $url . '" target="_blank">' . $value . '</a>';
      echo "&nbsp;<a href='http://support.thulasidas.com' onclick=\"popupwindow('http://support.thulasidas.com','ezSupport for $value', 1024, 768);return false;\" title='";
      _e('Ask a support question (in English or French only) via ezSupport @ $0.95', 'easy-common');
      echo "'><img src='$plgURL/ezsupport.png' class='alignright' alt='ezSupport Portal'/></a>";
      printf(__("If you need help with %s, please read the FAQ section on the %s page. It may answer all your questions.", 'easy-common'), $value, $link);
      echo "<br style='line-height: 20px;'/>";
      _e("Or, if you still need help, you can raise a support ticket.", 'easy-common');
      echo "&nbsp;<a href='http://support.thulasidas.com' onclick=\"popupwindow('http://support.thulasidas.com','ezSupport', 1024, 768);return false;\" title='";
      _e('Ask a support question (in English or French only) via ezSupport @ $0.95', 'easy-common');
      echo "'>";
      _e("[Request Paid Support]", 'easy-common');
      $info = $this->getPlgInfo();
      echo "</a>&nbsp;<small><em>[";
      _e('Using our ezSupport Ticket System.', 'easy-common');
      echo "]</em></small>";
      echo "<small style='float:right'><em>[";
      printf(__('You are using %s (V%s)', 'easy-common'), $value, $info[0]['Version']);
      echo "]</em></small>";
      $_SESSION['ezSupport'] = $info[0]['Info'];
      echo "</div>";
    }

    function renderWhyPro($short = false) {
      if ($this->killAuthor && !$short) {
        return;
      }
      $plgURL = $this->plgURL;
      $plg = $this->plg;
      $slug = $this->slug;
      $value = $plg['value'];
      $toolTip = $plg['title'];
      $price = $plg['price'];
      $buyURL = "http://buy.thulasidas.com/$slug";
      $infoURL = "http://www.thulasidas.com/plugins/$slug";
      $s1 = sprintf(__('Get %s', 'easy-common'), $value);
      $s2 = sprintf(__('More info about %s', 'easy-common'), $value);
      $s3 = sprintf(__('Buy the Pro version of %s for $%.2f', 'easy-common'), $value, $price);
      $s4 = __('More Info', 'easy-common');

      $onclick = "onclick=\"popupwindow('$buyURL','$s1', 1024, 768);return false;\"";
      $moreInfo = " &nbsp;  &nbsp; <a href='$infoURL' title='$s2' target=_blank> $s4 </a>&nbsp; <a href='$buyURL' $onclick title='$s3'>Get Pro Version</a>";
      $toolTip .= addslashes('<br />' . $moreInfo);
      $why = addslashes($plg['pro']);
      echo '<div style="background-color:#cff;padding:5px;border: solid 1px;margin:5px;padding-bottom:15px;">';
      if ($short) {
        $s5 = __('Buy the Pro Version', 'easy-common');
        $s6 = __('More features, more power!', 'easy-common');

        echo "<span onmouseover=\"TagToTip('pro', WIDTH, 350, TITLE, 'Buy the Pro Version',STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 5, 5])\"><b><a href='$buyURL' $onclick target='_blank'>$s5</a></b>&nbsp; $s6</span>";
      }
      else {
        $s7 = sprintf(__('You are using the Lite version of %s, which is available in two versions: <b>Lite</b> and <b>Pro</b>.', 'easy-common'), $value);
        echo "<b>Get Pro Version!</b>
<a href='http://buy.thulasidas.com/$slug' title='$s3'><img src='$plgURL/ezpaypal.png' alt='ezPayPal' class='alignright' $onclick /></a>
<br />
$s7
<ul><li>
$moreInfo
<li>$why</li>
</ul>";
      }
      echo '</div>';
    }

  }

}