<?php

function ezPluginInfo() {
  $me = basename(dirname(__FILE__));
  $plugins = get_plugins();
  $ret = array('Version' => '', 'Info' => '');
  $break = '';
  foreach ($plugins as $k => $p) {
    $baseDir = dirname($k);
    if ($baseDir == $me) {
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

function renderSupport($name, $plg) {
  $plugindir = get_option('siteurl') . '/' . PLUGINDIR . '/' . basename(dirname(__FILE__));
  $value = $plg['value'];
  $url = 'http://www.thulasidas.com/plugins/' . $name . '#faq';
  $link = '<a href="' . $url . '" target="_blank">' . $value . '</a>';
  echo "&nbsp;<a href='http://support.thulasidas.com' onclick=\"popupwindow('http://support.thulasidas.com','ezSupport for $value', 1024, 768);return false;\" title='";
  _e('Ask a support question (in English or French only) via ezSupport @ $0.95', 'easy-adsenser');
  echo "'><img src='$plugindir/ezsupport.png' class='alignright' alt='ezSupport Portal'/></a>";
  printf(__("If you need help with %s, please read the FAQ section on the $link page. It may answer all your questions.", 'easy-adsenser'), $value, $link);
  echo "<br style='line-height: 20px;'/>";
  _e("Or, if you still need help, you can raise a support ticket.", 'easy-adsenser');
  echo "&nbsp;<a href='http://support.thulasidas.com' onclick=\"popupwindow('http://support.thulasidas.com','ezSupport for $value', 1024, 768);return false;\" title='";
  _e('Ask a support question (in English or French only) via ezSupport @ $0.95', 'easy-adsenser');
  echo "'>";
  _e("[Request Paid Support]", 'easy-adsenser');
  $info = ezPluginInfo();
  echo "</a>&nbsp;<small><em>[";
  _e('Using our ezSupport Ticket System.', 'easy-adsenser');
  echo "]</em></small>";
  echo "<small style='float:right'><em>[";
  printf(__('You are using %s (V%s)', 'easy-adsenser'), $value, $info[0]['Version']);
  echo "]</em></small>";
  $_SESSION['ezSupport'] = $info[0]['Info'];
}

function renderTranslator($plgName) {
  $locale = get_locale();
  if (strncmp($locale, "en", 2) == 0) {
    return;
  }
  if ($plgName == 'adsense-now' ||
          $plgName == 'easy-adsense' ||
          $plgName == 'easy-translator') {
    return;
  }
  echo '<div style="background-color:#ddd;padding:5px;border: solid 1px;margin:5px;">';
  echo "<script type='text/javascript'>
<!--
function hideTranslator(id, btn, translator) {
  var e = document.getElementById(id);
  var eBtn = document.getElementById(btn);
  e.style.display = 'none';
  eBtn.innerHTML = 'Show ' + translator;
}
function showTranslator(id, btn, translator) {
  var e = document.getElementById(id);
  var eBtn = document.getElementById(btn);
  e.style.display = 'block';
  eBtn.innerHTML = 'Hide ' + translator;
}
function toggleVisibility(id, btn, translator) {
  var e = document.getElementById(id);
  if (translator == 'Google') hideTranslator('MicrosoftTranslatorWidget', 'btnMS', 'Microsoft');
  if (translator == 'Microsoft') hideTranslator('GoogleTranslatorWidget', 'btnGG', 'Google');
  if(e.style.display == 'block') {
    hideTranslator(id, btn, translator);
  }
  else {
     showTranslator(id, btn, translator);
  }
}
//-->
</script>";
  $ms = true;
  $google = true;
  if ($ms) {
    $msBtn = " <button id='btnMS' onclick=\"toggleVisibility('MicrosoftTranslatorWidget', 'btnMS', 'Microsoft');\">Show Microsoft</button>";
    $msLink = "<a target=_blank href='http://www.bing.com/translator'>Microsoft<sup>&reg;</sup></a> ";
    $msJS = "<div id='MicrosoftTranslatorWidget' style='margin-left:auto;margin-right:auto;display:none; width: 200px; min-height: 83px; border-color: #404040; background-color: #A0A0A0;'><noscript><a href='http://www.microsofttranslator.com/bv.aspx?a=http%3a%2f%2fwww.thulasidas.com%2fplugins%2f$plgName'>Translate this page</a><br />Powered by <a href='http://www.bing.com/translator'>Microsoft® Translator</a></noscript></div> <script type='text/javascript'> /* <![CDATA[ */ setTimeout(function() { var s = document.createElement('script'); s.type = 'text/javascript'; s.charset = 'UTF-8'; s.src = ((location && location.href && location.href.indexOf('https') == 0) ? 'https://ssl.microsofttranslator.com' : 'http://www.microsofttranslator.com' ) + '/ajax/v2/widget.aspx?mode=manual&from=en&layout=ts'; var p = document.getElementsByTagName('head')[0] || document.documentElement; p.insertBefore(s, p.firstChild); }, 0); /* ]]> */ </script>";
  }
  else {
    $msBtn = $msJs = $msLink = '';
  }
  if ($google) {
    $ggBtn = " <button id='btnGG' onclick=\"toggleVisibility('GoogleTranslatorWidget', 'btnGG', 'Google');\">Show Google</button>";
    $ggLink = "<a target=_blank href='https://translate.google.com/'>Google<sup>&reg;</sup></a>";
    $ggJS = "<div id='GoogleTranslatorWidget' style='text-align:center;display:none;'><div id='google_translate_element'></div><script type='text/javascript'>
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script><script type='text/javascript' src='//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit'></script></div>";
  }
  else {
    $ggBtn = $ggJS = $ggLink = '';
  }
  if ($google && $ms) {
    $or = "or";
  }
  echo "See this page in your language (<code>$locale</code>) using machine translation. $ggLink $or $msLink Translator.";
  echo $ggBtn . $msBtn . $ggJS . $msJS;
  echo '</div>';
}

renderTranslator($plgName);

renderSupport($plgName, $myPlugins[$plgName]);
