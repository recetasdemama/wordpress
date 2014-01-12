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

function renderHeadText($name, $plg) {
  $plugindir = get_option('siteurl') . '/' . PLUGINDIR . '/' . basename(dirname(__FILE__));
  $value = '<em><strong>' . $plg['value'] . '</strong></em>';
  $toolTip = $plg['title'];
  $price = $plg['price'];
  $moreInfo = "<b><a href='http://buy.thulasidas.com/$name' title='Buy the Pro version of {$plg['value']} for \$$price. Instant download link.' onclick=\"popupwindow('http://buy.thulasidas.com/$name','Get {$plg['value']}', 1024, 768);return false;\">Pro Version</a></b>";
  $toolTip .= addslashes('<br />' . $moreInfo);
  $why = addslashes($plg['pro']);
  $version = 'Lite';
  echo "<b>Get Pro Version!</b>
<a href='http://buy.thulasidas.com/$name' title='Buy the Pro version of the $name plugin. Instant download link.' onclick=\"popupwindow('http://buy.thulasidas.com/$name','Get {$plg['value']}', 1024, 768);return false;\"><img src='$plugindir/ezpaypal.png' alt='ezPayPal -- Instant PayPal Shop.' class='alignright'/></a>
<br />
You are using the $version version of $value, which is also available as in a Pro version.
<ul><li>
$moreInfo
</li>
<li>$why And it costs only \$$price!</li>
</ul>";
}

function renderProText($name, $plg) {
  $value = '<em><strong>' . $plg['value'] . '</strong></em>';
  $filter = '';
  if (stripos($name, 'adsense') !== FALSE) {
    $filter = " (e.g., a filter to ensure AdSense policy compliance) ";
  }
  $toolTip = $plg['title'];
  $price = $plg['price'];
  $popup = "onclick=\"popupwindow('http://buy.thulasidas.com/$name','Get {$plg['value']}', 1024, 768);return false;\"";
  $moreInfo = "&nbsp; <a href='http://buy.thulasidas.com/lite/$name.zip' title='Download the Lite version of $value'>Lite Version </a>&nbsp; <a href='http://buy.thulasidas.com/$name' title='Buy the Pro version of $value for \$$price' $popup>Pro Version</a>";
  $toolTip .= addslashes('<br />' . $moreInfo);
  echo "<div style='background-color:#ffcccc;padding:5px;border: solid 1px;text-align:center;'>
<span style='font-size:14px;color:#a48;font-variant: small-caps;text-decoration:underline;text-align:center;' $popup onmouseover=\"TagToTip('pro', WIDTH, 300, TITLE, 'Buy the Pro Version',STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 5, 5])\"><b>The Pro Version</b></span><br />";

  $value .= '<b><i> Lite</i></b>';
  echo "Thank you for using $value. The \"Pro\" version gives you more options$filter. Consider <a href='http://buy.thulasidas.com/$name' title='Pro version of this plugin. Instant download link.' $popup>buying it</a>. It costs only \$$price.";

  echo "<div id='pro' style='display:none'>";
  renderHeadText($name, $plg);
  echo "</div></div>";
}

function renderAffiliate() {
  $plugindir = get_option('siteurl') . '/' . PLUGINDIR . '/' . basename(dirname(__FILE__));
  echo "<div style='padding:0px;border:none; width:300px' id='support' onmouseover=\"Tip('<b>ezAffiliates</b>: The most affiliate-centric revenue sharing model on the Web. Finally, you can make some serious returns on your web presence.<br /><b>Generous 50% Commission</b>: perhaps the highest rate of revenue sharing on the web. With just a couple of sales of this plugin, you will have recovered your purchase price!<br /><b>$10 Minimum Payout</b> so that you will not be waiting forever before you qualify for payment.<br /><b>Lifetime Tracking</b>: ezAffiliates uses cookie-less tracking technology to attribute every purchase of your lead to your account. Whatever your leads buy from us, whenever they do, will earn you commission. No cookie expiry!<br /><b>High Quality Products</b> such as this plugin, and other premium plugins and PHP packages.<br /><b>Diverse Markets</b>: Bloggers who blog about plugins, PayPal integration, affiliate marketing, MacOS apps and even eBooks will find ezAffiliates attractive and more effective that their current ad campaigns.', WIDTH, 295, TITLE, 'ezAffiliates', FIX, [this, 0, 0])\" onmouseout=\"UnTip()\" ><a href='http://affiliates.thulasidas.com'><img src='$plugindir/invite.gif' alt='ezAffiliates' onclick=\"popupwindow('http://affiliates.thulasidas.com/','50-50 Revenue Share', 1024, 768);return false;\"/></a></div>";
}

function renderSupportText($name, $plg, $long = true) {
  $value = '<em><strong>' . $plg['value'] . '</strong></em>';
  $supportText = "<div style=\"background-color:#cff;padding:5px;border: solid 1px\" id=\"support\"><b>Support $value. <a href=\"http://buy.thulasidas.com/$name\" title=\"Pro version of this plugin. Instant download link.\" onclick=\"popupwindow('http://buy.thulasidas.com/$name','Get {$plg['value']}', 1024, 768);return false;\">Go Pro!</a></b>";
  if ($long) {
    $longText = "How about ";
  }
  else {
    $longText = '';
  }
  $supportText .= "<br />$longText<span onmouseover=\"TagToTip('dropbox', WIDTH, 440, TITLE, 'What is DropBox?',STICKY, 1, CLOSEBTN, true, FIX, [this, -150, 2])\"><a href='http://db.tt/qsogWB1' title='Sign up for Dropbox -- free 2GB online storage on the cloud!' target='_blank'>2GB of <em>free</em> online storage</a></span>?";
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

function renderTipDivs($name) {
  $plugindir = get_option('siteurl') . '/' . PLUGINDIR . '/' . basename(dirname(__FILE__));
  echo <<<ENDDIVS
<div id="arvixe" style='display:none;'>
  <a href="http://www.arvixe.com/1933-27-1-310.html" target="_blank">Arvixe</a> is my favorite hosting provider. Friendly service, extremely competitive rates, and of course a great affiliate program.
</div>

<span id="dropbox" style='display:none;'>
  Dropbox! gives you 2GB of network (cloud) storage for free, which I find quite adequate for any normal user. (That sounds like the famous last words by Bill Gates, doesn’t it? “64KB of memory should be enough for anyone!”) And, you can get 250MB extra for every successful referral you make. That brings me to my ulterior motive – please use this link to sign up. When you do, I get 250MB extra. Don’t worry, you get 250MB extra as well. So I can grow my online storage up to 8GB, which should keep me happy for a long time. Thank you!
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
<img class="alignright" src="$plugindir/unreal.gif" alt="TheUnrealUniverse" title="Read more about The Unreal Universe" />
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
<img src="$plugindir/pqd-82x126.gif" alt="PQD" title="Buy the companion eBook to Principles of Quantitative Development from the author (only $5.49)" />
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

echo '<td style="width:30%">';

if (rand(0, 2) % 2 || $plgName == "easy-ads" || $plgName == "google-adsense") {
  renderSupportText($plgName, $myPlugins[$plgName], $myPlugins[$plgName]['long']);
  renderTipDivs($name);
}
else {
  renderAffiliate();
}

echo '</td>';
echo '<td style="width:30%">';

renderProText($plgName, $myPlugins[$plgName]);

echo '</td>';
