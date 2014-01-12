<?php

function renderWhyPro($name, $plg) {
  $plugindir = get_option('siteurl') . '/' . PLUGINDIR . '/' . basename(dirname(__FILE__));
  $value = $plg['value'];
  $toolTip = $plg['title'];
  $price = $plg['price'];
  $moreInfo = " &nbsp;  &nbsp; <a href='http://www.thulasidas.com/plugins/$name' title='More info about $value'> More Info </a>&nbsp; <a href='http://buy.thulasidas.com/$name' onclick=\"popupwindow('http://buy.thulasidas.com/$name','Get $value', 1024, 768);return false;\" title='Buy the Pro version of $value for \$$price'>Get Pro Version</a>";
  $toolTip .= addslashes('<br />' . $moreInfo);
  $why = addslashes($plg['pro']);
  echo "<b>Get Pro Version!</b>
<a href='http://buy.thulasidas.com/$name' title='Buy the Pro version of the $value plugin for \$$price'><img src='$plugindir/ezpaypal.png' alt='ezPayPal -- Your Instant PayPal Shop.' class='alignright' onclick=\"popupwindow('http://buy.thulasidas.com/$name','Get $value', 1024, 768);return false;\"/></a>
<br />
You are using the Lite version of $value, which is available in two versions: <b>Lite</b> and <b>Pro</b>.
<ul><li>
$moreInfo
<li>$why</li>
</ul>";
}

renderWhyPro($plgName, $myPlugins[$plgName]);
