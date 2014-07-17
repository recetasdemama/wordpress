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

if (!class_exists("PO")) {

  class PO { // an id-str pair with attributes

    var $num, $id, $str, $tranId, $tranVal, $keyId, $keyVal, $tl = '', $domain;

    const MINMATCH = 89;

    function __construct($id, $str) {
      $this->id = (string) $id;
      $this->str = (string) $str;
      $this->tranVal = self::MINMATCH;
      $this->keyVal = self::MINMATCH;
    }

    function PO($id, $str) {
      if (version_compare(PHP_VERSION, "5.0.0", "<")) {
        $this->__construct($id, $str);
        register_shutdown_function(array($this, "__destruct"));
      }
    }

    // Returns a properly escaped string
    static function decorate($str, $esc) {
      if (!get_magic_quotes_gpc()) {
        // $str = addcslashes($str, $esc);
      }
      return $str;
    }

    static function undecorate($str) {
      if (!get_magic_quotes_gpc()) {
        $str = stripslashes($str);
      }
      return $str;
    }

    // Returns a text-area string of the Id
    function getId() {
      $ht = round(strlen($this->id) / 52 + 1) * 25;
      $col = 'background-color:#f5f5f5;';
      $tit = '';
      if ($this->keyVal > self::MINMATCH + 1) {
        $col = "background-color:#ffc;border: solid 1px #f00";
        $tit = 'onmouseover = "Tip(\'Another similar string: ' .
                htmlspecialchars('<br /><em><b>' . addslashes($this->keyId) .
                        '</b></em><br /> ', ENT_QUOTES) .
                'exists. Please alert the author.\',WIDTH, 300)" ' .
                'onmouseout="UnTip()"';
      }
      $s = '<textarea cols="50" rows="15" name="k' . $this->num .
              '" style="width: 45%;height:' . $ht . 'px;' . $col . '" ' .
              $tit . ' readonly="readonly">';
      $s .= htmlspecialchars($this->id, ENT_QUOTES);
      $s .= '</textarea>&nbsp;&nbsp;';
      return $s;
    }

    function getStr() {
      $ht = round(strlen($this->id) / 52 + 1) * 25;
      $col = '';
      $tit = '';
      if ($this->tranVal > self::MINMATCH + 1) {
        $col = "background-color:#fdd;border: solid 1px #f00";
        $tit = 'onmouseover = "Tip(\'Using the translation for a similar string: ' .
                htmlspecialchars('<br /><em><b>' . addslashes($this->tranId) .
                        '</b></em><br />', ENT_QUOTES) .
                'Please check carefully.\',WIDTH, 300)" ' .
                'onmouseout="UnTip()"';
      }
      if (empty($this->str) && !empty($this->tl)) {
        $col = "background-color:#dff;border: solid 1px #0ff";
        $tit = 'onmouseover = "Tip(\'Using Machine Translation from Google.<br />Please check carefully.\',WIDTH, 300)" ' .
                'onmouseout="UnTip()"';
        $this->str = $this->googleTran();
      }
      $s = '<textarea cols="50" rows="15" name="' . $this->num .
              '" style="width: 45%;height:' . $ht . 'px;' . $col . '" ' .
              $tit . '>';
      $s .= htmlspecialchars($this->str, ENT_QUOTES);
      $s .= '</textarea><br />';
      return $s;
    }

    function googleTran1($q) {
      $tl = $this->tl;
      $sl = "auto";
      if (!$q) {
        return;
      }
      $url = 'http://translate.google.com/translate_a/t?client=a&q=' . $q . '&tl=' . $tl . '&sl=' . $sl;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      //must set agent for google to respond with utf-8
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
      $output = curl_exec($ch);
      curl_close($ch);
      if ($output === false) {
        return "No help (output) from Google!";
      }
      $jsonarr = json_decode($output);
      if (!$jsonarr) {
        return "No help (json) from Google!";
      }
      if (isset($jsonarr->results)) {
        $ret = $jsonarr->results[0]->sentences[0]->trans;
      }
      else {
        $ret = $jsonarr->sentences[0]->trans;
      }
      return $ret;
    }

    function googleTran() {
      $sentences = preg_split('/(?<=[.?!;:])\s+/', $this->id, -1, PREG_SPLIT_NO_EMPTY);
      $ret = '';
      foreach ($sentences as $s) {
        $q = urlencode($s);
        $ret .= ' ' . $this->googleTran1($q);
      }
      return trim($ret);
    }

  }

}

if (!class_exists("EzTran")) {

  class EzTran {

    // locale is the blog-locale or the language explicity loaded
    // target is the language target for the translation. defaults to locale.

    var $status, $error, $plgName, $plgDir, $plgURL, $domain, $locale, $state,
            $isEmbedded = true, $isPro = false, $target, $POs, $slug;
    var $adminMsg;
    var $helpers = array();
    var $sessionVars = array('POs', 'ezt-locale', 'ezt-target');

    function __construct($plgFile, $plgName, $domain = '') {
      $this->status = '';
      $this->error = '';
      if (empty($plgName)) {
        $plgName = basename($plgFile, '.php');
        $plgName = ucfirst(strtr($plgName, '-_', '  '));
      }
      $this->plgName = $plgName;
      $this->slug = strtolower(strtr($plgName, ' !.,', '----'));
      $this->slug = str_replace("--", "-", $this->slug);
      if (empty($domain)) {
        $domain = $this->slug;
      }
      $this->domain = $domain;
      $this->plgDir = dirname($plgFile);
      $this->plgURL = plugin_dir_url($plgFile);
      $locale = get_locale();
      $this->locale = str_replace('-', '_', $locale);
      $this->target = $this->locale;
      if (!session_id()) {
        session_start();
      }
      if (!empty($_POST['ezt-savePot']) || !empty($_POST['ezt-download'])) {
        // saving cannot be done from handleSubmits
        // because it would be too late for the headers.
        $slug = $_POST['ezt-slug'];
        if ($slug == $this->slug) {
          if (!empty($_POST['ezt-savePot'])) {
            $target = $_POST['ezt-target'];
            $file = "$slug-$target.zip";
            $potArray = unserialize(gzinflate(base64_decode($_POST['potArray'])));
            $poName = $target;
          }
          if (!empty($_POST['ezt-download'])) {
            require_once(ABSPATH . WPINC . '/pluggable.php');
            global $current_user;
            get_currentuserinfo();
            $msg = array();
            $msg['name'] = $current_user->user_firstname . " " .
                    $current_user->user_lastname;
            $msg['email'] = $current_user->user_email;
            $msg['blog'] = get_bloginfo('blog');
            $msg['url'] = get_bloginfo('url');
            $msg['charset'] = get_bloginfo('charset');
            $msg['locale'] = $this->locale;
            $msg['ezt-target'] = "POT File";
            $file = "$slug.zip";
            $s = $this->getFileContents();
            $POs = $this->getTranPOs($s);
            $potArray = $this->mkPotStr($POs, $msg);
            $poName = $slug;
          }
          $zip = new ZipStream($file);
          if ($this->isEmbedded
                  && $slug != 'easy-translator'
                  && $slug != 'easy-translator-lite') {
            $zip->add_file("readme.txt", "Please edit the included PO files using appropriate tools such as poedit (or any text editor) and email them to the plugin author. If you are translating one of my plugins, the email address is manoj@thulasidas.com.");
          }
          foreach ($potArray as $d => $str) {
            if (empty($d)) { // skip strings with no domain -- they are WP core ones
              continue;
            }
            if ($d == $slug) {
              $filePO = "{$poName}.po";
            }
            else { // d should be 'easy-common'
              $filePO = "{$poName}_$d.po";
            }
            $zip->add_file($filePO, $str);
          }
          $zip->finish();
          $this->status .= '<div class="updated">Pot file: ' . $file . ' was saved.</div> ';
          exit(0);
        }
      }
    }

    function EzTran($plgFile, $plgName = '', $domain = '') {
      if (version_compare(PHP_VERSION, "5.0.0", "<")) {
        $this->__construct($plgFile, $plgName, $domain);
        register_shutdown_function(array($this, "__destruct"));
      }
    }

    // Return the contents of all PHP files in the dir specified
    function getFileContents($dir = '') {
      if (empty($dir)) {
        $dir = $this->plgDir;
      }
      $files = glob("$dir/*.php");
      $page = "";
      foreach ($files as $f) {
        $page .= file_get_contents($f, FILE_IGNORE_NEW_LINES);
      }
      // $page = str_replace(array_values($this->helpers), $this->domain, $page);
      return $page;
    }

    // Percentage Levenshtein distance
    function levDist(&$s1, &$s2) {
      similar_text($s1, $s2, $p);
      return round($p);
    }

    // Get the closest existing translation keys, and the recursivley closest in the
    // key set
    function getClosest(&$mo, &$POs) {
      foreach ($POs as $n => $po) {
        $s1 = $po->id;
        if (strlen($po->str) == 0) {
          if (!empty($mo)) {
            foreach ($mo as $mn => $mk) {
              $s2 = $mn;
              $result = $this->levDist($s1, $s2);
              if ($result > $po->tranVal) {
                $po->tranVal = $result;
                $po->tranId = $mn;
                $po->str = $mk->translations[0];
              }
            }
          }
        }
        foreach ($POs as $n2 => $po2) {
          if ($n != $n2) {
            $s2 = $po2->id;
            $result = $this->levDist($s1, $s2);
            if ($result > $po2->keyVal) {
              $po->keyVal = $result;
              $po->keyId = $po2->id;
              $po2->keyVal = $result;
              $po2->keyId = $po->id;
            }
          }
        }
      }
    }

    static function getStrings($contents, &$keys, &$domains) {
      $matches = array();
      $regExp = "#_[_e]\s*\([\'\"](.*)[\'\"]\s*(,\s*[\'\"](.+)[\'\"]|)\s*\)#U";
      // $regExp = "#_[_e]\s*\(\'([^\'\"]*\'|\"[^\'\"])\"s*(,\s*[\'\"](.+)[\'\"]|)\s*\)#U";
      preg_match_all($regExp, $contents, $matches);
      $keys = array_unique($matches[1]);
      $domains = $matches[3];
      foreach ($domains as $k => $d) {
        if (strtr($d, '",\'', '   ') != $d) {
          unset($domains[$k]);
          unset($keys[$k]);
        }
      }
      $keys = str_replace(array("\'", '\"', '\n'), array("'", '"', "\n"), $keys);
    }

    function getTranPOs(&$contents) {
      $keys = $domains = array();
      self::getStrings($contents, $keys, $domains);
      $tl = substr($this->target, 0, 2);
      global $l10n;
      $POs = array();
      foreach ($keys as $n => $k) {
        if (!$this->isEmbedded && $domains[$n] != $this->domain) {
          // consider only the specified domain
          continue;
        }
        if (!empty($l10n[$domains[$n]])) {
          $mo = $l10n[$domains[$n]]->entries;
        }
        if (!empty($mo[$k])) {
          $v = $mo[$k];
          $t = $v->translations[0];
        }
        else {
          $t = '';
        }
        $po = new PO($k, $t);
        $po->num = $n;
        if (isset($_POST['ezt-google']) && ($this->isEmbedded || $this->isPro)) {
          $po->tl = $tl;
        }
        $po->domain = $domains[$n];
        array_push($POs, $po);
      }
      $this->getClosest($mo, $POs);
      return $POs;
    }

    // Make a POT string from ids and msgs
    function mkPotStr(&$POs, $msg) {
      $time = current_time('mysql');
      $potHead = <<<EOF
# This file was generated by EzTran for {$this->plgName}
# Your Name: {$msg["name"]}
# Your Email: {$msg["email"]}
# Your Website: {$msg["blog"]}
# Your URL: {$msg["url"]}
# Your Locale: {$msg["locale"]}
# Your Language: {$msg["ezt-target"]}
#, fuzzy
msgid ""
msgstr ""
"Project-Id-Version: {$this->plgName}\\n"
"PO-Revision-Date: $time\\n"
"Last-Translator: {$msg['name']} <{$msg['email']}>\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset={$msg['charset']}\\n"
"Content-Transfer-Encoding: 8bit\\n"


EOF;
      $pot = array();
      foreach ($POs as $po) {
        if (empty($pot[$po->domain])) {
          $pot[$po->domain] = $potHead;
        }
        $pot[$po->domain] .= 'msgid "' . PO::decorate($po->id, "\n\r\"") . "\"\n";
        if (empty($msg[$po->num])) {
          $t = '';
        }
        else {
          $t = $msg[$po->num];
        }
        $pot[$po->domain] .= 'msgstr "' . PO::decorate($t, "\n\r") . "\"\n\n";
      }
      return $pot;
    }

    // Update the PO objects in $POs with the text box stuff
    function updatePot(&$POs, $msg) {
      foreach ($POs as $po) {
        $t = $msg[$po->num];
        $po->str = PO::undecorate($t);
      }
    }

    function putSessionVars($var = array()) {
      if (empty($var)) {
        $var = $this->sessionVars;
      }
      if (!is_array($var)) {
        $var = array($var);
      }
      if (empty($_SESSION[$this->domain])) {
        $_SESSION[$this->domain] = array();
      }
      foreach ($var as $v) {
        $prop = str_replace("ezt-", "", $v);
        if (isset($this->$prop)) {
          $_SESSION[$this->domain][$v] = $this->$prop;
        }
      }
    }

    function getSessionVars($var = array()) {
      if (empty($var)) {
        $var = $this->sessionVars;
      }
      if (!is_array($var)) {
        $var = array($var);
      }
      foreach ($var as $v) {
        $prop = str_replace("ezt-", "", $v);
        if (isset($_SESSION[$this->domain][$v])) {
          $this->$prop = $_SESSION[$this->domain][$v];
        }
      }
    }

    function rmSessionVars($var = array()) {
      if (empty($var)) {
        $var = $this->sessionVars;
      }
      if (!is_array($var)) {
        $var = array($var);
      }
      foreach ($var as $v) {
        unset($_SESSION[$this->domain][$v]);
        $prop = str_replace("ezt-", "", $v);
        $this->$prop = '';
      }
    }

    function isCached() {
      $cached = !empty($_SESSION[$this->domain]);
      if ($cached) {
        foreach ($this->sessionVars as $v) {
          $cached = $cached && !empty($_SESSION[$this->domain][$v]);
        }
      }
      return $cached;
    }

    function handleSubmits() {
      $adminNeeded = false;
      if (empty($_POST)) {
        return $adminNeeded;
      }
      if (isset($_POST['ezt-english'])) {
        global $l10n;
        unset($l10n[$this->domain]);
        $this->adminMsg = "<div class='updated'><p><strong>Ok, in English for now."
                . " <input type='button' value='Switch Back' onClick='location.reload(true)'></strong></p> </div>";
        return $adminNeeded;
      }
      $adminNeeded = true;
      if (isset($_POST['ezt-translate'])) {
        $_POST['eztran'] = 'eztran';
        if (isset($_POST['ezt-createpo'])) {
          $this->getSessionVars();
          if ($this->target != $_POST['ezt-createpo']) {
            $this->rmSessionVars();
            $this->target = $_POST['ezt-createpo'];
            $this->setLang($this->target);
          }
        }
        return $adminNeeded;
      }
      if (!isset($_POST['eztran'])) {
        return $adminNeeded;
      }
      if (!check_admin_referer('ezTranSubmit', 'ezTranNonce')) {
        return $adminNeeded;
      }
      if (isset($_POST['ezt-clear'])) {
        $this->status = '<div class="updated">Reloaded the translations from PHP files and MO.</div> ';
        $_SESSION[$this->domain] = array();
        $this->target = $_POST['ezt-target'];
        $this->setLang($this->target);
        return $adminNeeded;
      }
      if (!empty($_POST['ezt-mailPot'])) {
        if ($this->isEmbedded || $this->isPro) {
          $locale = $_POST['locale'];
          $file = "{$locale}.po";
          $potArray = unserialize(gzinflate(base64_decode($_POST['potArray'])));

          if (!class_exists("phpmailer")) {
            require_once(ABSPATH . 'wp-includes/class-phpmailer.php');
          }
          $mail = new PHPMailer();
          $mail->From = get_bloginfo('admin_email');
          $mail->FromName = get_bloginfo('name');
          if ($this->isEmbedded) {
            $author = "Manoj Thulasidas";
            $authormail = 'Manoj@Thulasidas.com';
          }
          else {
            $author = $_POST['ezt-author'];
            $authormail = $_POST['ezt-authormail'];
          }
          $mail->AddAddress($authormail, $author);
          $mail->CharSet = get_bloginfo('charset');
          $mail->Mailer = 'php';
          $mail->SMTPAuth = false;
          $mail->Subject = $file;
          foreach ($potArray as $domain => $str) {
            $filePO = "{$locale}_$domain.po";
            $mail->AddStringAttachment($str, $filePO);
          }

          $pos1 = strpos($str, "msgstr");
          $pos2 = strpos($str, "msgid", $pos1);
          $head = substr($str, 0, $pos2);
          $mail->Body = $head;
          if ($mail->Send()) {
            $this->status = "<div class='updated'>Pot file:  $file was sent.</div>";
          }
          else {
            $this->error = "<div class='error'>Error: {$mail->ErrorInfo} Please save the pot file and <a href='mailto:$authormail'>contact $author</a></div>";
          }
        }
        else {
          $this->status = '<div style="background-color:#cff;padding:5px;margin:5px;border:solid 1px;margin-top:10px;font-weight:bold;color:red">In the <a href="http://buy.thulasidas.com/easy-translator">Pro Version</a>, the Pot file would have been sent to the plugin author.<br />In this Lite version, please download the PO file (using the "Display &amp; Save POT File" button above) and email it using your mail client.</div><br />';
        }
        return $adminNeeded;
      }
      return $adminNeeded;
    }

    // Prints out the admin page
    function printAdminPage() {
      echo '<script type="text/javascript">window.onload = function() {jQuery("#loading").fadeOut("slow");};</script>';
      echo "<div id='loading'><p><img src='{$this->plgURL}/loading.gif' alt='loading'/> Please Wait. Loading...</p></div>";
      if ($this->isEmbedded) {
        $this->helpers = array('' => 'easy-common', 'easy-adsense' => 'easy-common');
      }
      $printed = false;
      if (!$this->handleSubmits()) {
        return $printed;
      }
      if (!isset($_POST['eztran'])) {
        return $printed;
      }
      if (isset($_POST['ezt-english'])) {
        return $printed;
      }
      $printed = true;
      if ($this->isEmbedded) {
        $backButtonVal = "Go Back to {$this->plgName} Admin";
      }
      else {
        $backButtonVal = "Go Back to Easy Translator";
      }
      $backButton = "<br /><b>If you are done with translating, <input type='button' value='$backButtonVal' onClick='location.reload(true)'><br />You can continue later, and your translation for the session will be remembered until you close the browser window.</b>";
      echo '<div class="wrap" style="width:1000px">';
      echo '<form method="post" action="#">';
      wp_nonce_field('ezTranSubmit', 'ezTranNonce');
      echo "<input type='hidden' name='eztran' value='eztran'>";
      $locale = $this->locale;
      echo "\n<script type='text/javascript' src='{$this->plgURL}/wz_tooltip.js'></script>\n";
      if ($this->isEmbedded) {
        echo "<h2>Translation Interface for {$this->plgName}</h2>";
      }
      else {
        echo "<h2>Translating {$this->plgName} using Easy Translator</h2>";
      }

      if ($this->isCached()) {
        if (empty($this->status)) {
          $this->status = '<div class="updated">Continuing from your last translation session. Click on "Reload Translation" if you would like to start from scratch.</div> ';
        }
        $this->getSessionVars();
        $POs = $this->POs;
      }
      else {
        $s = $this->getFileContents();
        $POs = $this->getTranPOs($s);

        // cache the POs
        $this->POs = $POs;
        $this->putSessionVars();
      }

      if (isset($_POST['ezt-make'])) {
        $potArray = $this->mkPotStr($POs, $_POST);
        $pot = '';
        foreach ($potArray as $p) {
          $pot .= htmlspecialchars($p, ENT_QUOTES);
        }
        $this->updatePot($POs, $_POST);

        // cache the POs
        $this->POs = $POs;
        $this->putSessionVars();
      }
      else {
        global $current_user;
        $pot = '';
        if (count($POs) > 0) {
          get_currentuserinfo();
          $pot .= '<div style="width: 15%; float:left">Your Name:</div>' .
                  '<input type="text" style="width: 30%" name="name" value="' .
                  $current_user->user_firstname . " " .
                  $current_user->user_lastname . '" /><br />' . "\n";
          $pot .= '<div style="width: 15%; float:left">Your Email:</div>' .
                  '<input type="text" style="width: 30%" name="email" value="' .
                  $current_user->user_email . '" /><br />' . "\n";
          $pot .= '<div style="width: 15%; float:left">Your Website:</div>' .
                  '<input type="text" style="width: 30%" name="blog" value="' .
                  get_bloginfo('blog') . '" />' . "\n<br />";
          $pot .= '<div style="width: 15%; float:left">Your URL:</div>' .
                  '<input type="text" style="width: 30%" name="url" value="' .
                  get_bloginfo('url') . '" />' . "\n<br />";
          $pot .= '<div style="width: 15%; float:left">Your Locale:</div>' .
                  '<input type="text" style="width: 30%" name="locale" value="' .
                  $locale . '" readonly /><br />' . "\n";
          $tip = "Enter the language code for your translation (used for machine translation seed by Google). It should be of the form <code>fr_FR</code>, for instance. The first two letters are for the language (and needed for Google translation), the last two are for the country, and not used by this translator.";
          $pot .= '<div style="width: 15%; float:left">Your Language:</div>' .
                  '<input type="text" style="width: 30%" name="ezt-target" value="' .
                  $this->target . '" onmouseover = "Tip(\'' . $tip . '\',WIDTH, 300)"'
                  . ' onmouseout="UnTip()" /><br />' . "\n";
          $pot .= '<div style="width: 15%; float:left">Character Set:</div>' .
                  '<input type="text" style="width: 30%" name="charset" value="' .
                  get_bloginfo('charset') . '" />' . "\n<br /><br />";

          $pot .= '<div style="width:800px;padding:10px;padding-top:25px"></div>';
          $pot .= '<div style="width:38%px;paddling:10px;padding-left:100px;float:left">' .
                  '<b>English (en_US)</b></div>';
          $pot .= '<div style="width:48%;paddling:10px;padding-left:10px;float:right">' .
                  '<b>Translation</b> (' . $locale . ')</div>';
          $pot .= '<div style="width:100%;padding:15px"></div>';

          if (!class_exists("PO")) {
            die("Class definition error on PO (on Chrome).");
          }
          foreach ($POs as $po) {
            if (!is_object($po) && gettype($po) == 'object') {
              $po = unserialize(serialize($po));
            } // need this only on Chrome!!
            $pot .= $po->getId() . "\n" . $po->getStr() . "\n\n";
          }
        }
        else {
          $pot .= "<div class='error'>No translatable strings found for the plugin {$this->plgName}. Although the translation interface is ready, the plugin author has not yet internationalized this plugin.</div>";
        }
      }
      $tip = "This plugin caches your inputs so that you restart from where you left off. If you would like to discard the cache and start from scratch, please click this button.";
      if ($this->isEmbedded || $this->isPro) {
        $useGoogle = "&nbsp;<span onmouseover=\"Tip('By default, the translator will query Google Translator for each string it cannot find a translation of. This may take a few minutes. If you would rather not wait, please uncheck this option.', WIDTH, 350, TITLE, 'Query Google?', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 0, 5])\" onmouseout=\"UnTip()\"><input type='checkbox' name='ezt-google' checked='checked' />&nbsp;Use Google? </span>&nbsp;";
      }
      else {
        $useGoogle = '';
      }
      $makeStr = '<div class="submit">
<input type="submit" name="ezt-make" value="Display &amp; Save POT File" title="Make a POT file with the translation strings below and display it" />&nbsp;
<input type="submit" name="ezt-clear" value="Reload Translation" title="Discard your changes and reload the translation" onmouseover = "Tip(\'' . $tip . '\',WIDTH, 300)" onmouseout="UnTip()" onclick="return confirm(\'Are you sure you want to discard your changes?\nThe page may take a few minutes to reload because we may be querying Google for translations for each translatable string in the plugin files.\nPlease be patient or uncheck the Use Google option, if available.\');" />&nbsp;'
              . $useGoogle .
              '</div>' . $this->status . $this->error;
      $saveStr = '<div class="submit">
<input type="submit" name="ezt-savePot" value="Save POT file" title="Saves the strings shown below to your PC as a POT file" />&nbsp;
<input type="submit" name="ezt-mailPot" value="Mail POT file" title="Email the translation to the plugin autor" onClick="return confirm(\'Are you sure you want to email the author?\');" />&nbsp;
<input type="submit" name="ezt-editMore" value="Edit More" title="If you are not happy with the strings, edit it further"/>
</div>' . $this->status . $this->error;
      if (isset($_POST['ezt-make'])) {
        echo "<div style='background-color:#eef;border: solid 1px #005;padding:5px'>If you are happy with the POT file as below, please save it or email it to the author. If not, edit it further. $backButton</div>";
        $this->status = '<div class="updated">Ready to email the POT file to the author. Click on "Mail POT file" to send it.</div> ';
        $b64 = base64_encode(gzdeflate(serialize($potArray)));
        echo '<input type="hidden" name="potArray" value="' . $b64 . '" />';
        echo '<input type="hidden" name="locale" value="' . $this->locale . '" />';
        echo '<input type="hidden" name="ezt-target" value="' . $this->target . '" />';
        echo '<input type="hidden" name="ezt-slug" value="' . $this->slug . '" />';
        if (!$this->isEmbedded) {
          $mail = "<br /><span style='width:15%;float:left;'>Plugin Author:</span><input type='text' style='width: 30%' name='ezt-author' value='' /><br />\n";
          $mail .= "<span style='width:15%;float:left'>Author's Email:</span><input type='text' style='width: 30%' name='ezt-authormail' value='' />\n<br />";
          echo $mail;
        }
        echo $saveStr;
        echo "\n" . '<pre>' . $pot . '</pre>';
      }
      else {
        echo <<<EOF1
<div style="background-color:#eef;border: solid 1px #005;padding:5px">
You are editing the <code>$locale</code> translation for <code>{$this->plgName}</code>.
Text Domain is <code>{$this->domain}</code>.
<br />
Enter the translated strings in the text boxes below and hit the "Display POT File" button.
$backButton
</div>
EOF1;
        echo $makeStr;
        echo $pot;
      }
      echo "</form>\n</div>";
      return $printed;
    }

    function getInvite() {
      $locale = $this->locale;
      $plgName = $this->plgName;
      $patience = "I will include your translation in the next release.<br /><br /><span style=\"color:red;font-weight:bold\">Please note that the page may take a while to load because the plugin will query Google Translator for each string. Please be patient! You can make it faster by unchecking the Use Google option.</span>";
      $tipPot = htmlentities("If you would like to use your own tools to translate (such as <code>poedit</code>), please download the POT file here. Once done with the translation, please send the po file to the plugin author: <code>manoj at thulasidas dot com</code>");
      if ($this->state == "Not Translated") {
        $tip = htmlentities("It is easy to have <b>$plgName</b> in your language. All you have to do is to translate some strings, and email the file to the author.<br /><br />If you would like to help, please use the translation interface. It picks up the translatable strings in <b>$plgName</b> and presents them (and their existing translations in <b>$locale</b>, if any) in an easy-to-edit form. You can then generate a translation file and email it to the author all from the same form. $patience");
        $invite = "<span style='color:red'> Would you like to see <b>$plgName</b> in your langugage (<b>$locale</b>)?&nbsp; <input type='submit' name='ezt-translate' onmouseover=\"Tip('$tip', WIDTH, 350, TITLE, 'How to Translate?', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 0, 5])\" onmouseout=\"UnTip()\" value ='Please help translate ' /></span>";
      }
      else if ($this->state == "English") {
        $tip = htmlentities("If you would like to translate this plugin into a language you know or speak, please use the translation interface. It picks up the translatable strings in <b>$plgName</b> and presents them and their existing translations in your chosen language in an easy-to-edit form. You can then generate a translation file and email it to the author all from the same form. $patience");
        $langs = array('af_AF' => 'Afrikaans',
            'sq_SQ' => 'Albanian',
            'ar_AR' => 'Arabic',
            'hy_HY' => 'Armenian',
            'az_AZ' => 'Azerbaijani',
            'eu_EU' => 'Basque',
            'be_BE' => 'Belarusian',
            'bn_BN' => 'Bengali',
            'bs_BS' => 'Bosnian',
            'bg_BG' => 'Bulgarian',
            'ca_CA' => 'Catalan',
            'zh-CN' => 'Chinese',
            'hr_HR' => 'Croatian',
            'cs_CS' => 'Czech',
            'da_DA' => 'Danish',
            'nl_NL' => 'Dutch',
            'eo_EO' => 'Esperanto',
            'et_ET' => 'Estonian',
            'tl_TL' => 'Filipino',
            'fi_FI' => 'Finnish',
            'fr_FR' => 'French',
            'gl_GL' => 'Galician',
            'ka_KA' => 'Georgian',
            'de_DE' => 'German',
            'el_EL' => 'Greek',
            'gu_GU' => 'Gujarati',
            'ht_HT' => 'Haitian Creole',
            'ha_HA' => 'Hausa',
            'iw_IW' => 'Hebrew',
            'hi_HI' => 'Hindi',
            'hu_HU' => 'Hungarian',
            'is_IS' => 'Icelandic',
            'ig_IG' => 'Igbo',
            'id_ID' => 'Indonesian',
            'ga_GA' => 'Irish',
            'it_IT' => 'Italian',
            'ja_JA' => 'Japanese',
            'jw_JW' => 'Javanese',
            'kn_KN' => 'Kannada',
            'km_KM' => 'Khmer',
            'ko_KO' => 'Korean',
            'lo_LO' => 'Lao',
            'la_LA' => 'Latin',
            'lv_LV' => 'Latvian',
            'lt_LT' => 'Lithuanian',
            'mk_MK' => 'Macedonian',
            'ms_MS' => 'Malay',
            'mt_MT' => 'Maltese',
            'mi_MI' => 'Maori',
            'mr_MR' => 'Marathi',
            'mn_MN' => 'Mongolian',
            'ne_NE' => 'Nepali',
            'no_NO' => 'Norwegian',
            'fa_FA' => 'Persian',
            'pl_PL' => 'Polish',
            'pt_PT' => 'Portuguese',
            'pa_PA' => 'Punjabi',
            'ro_RO' => 'Romanian',
            'ru_RU' => 'Russian',
            'sr_SR' => 'Serbian',
            'sk_SK' => 'Slovak',
            'sl_SL' => 'Slovenian',
            'so_SO' => 'Somali',
            'es_ES' => 'Spanish',
            'sw_SW' => 'Swahili',
            'sv_SV' => 'Swedish',
            'ta_TA' => 'Tamil',
            'te_TE' => 'Telugu',
            'th_TH' => 'Thai',
            'tr_TR' => 'Turkish',
            'uk_UK' => 'Ukrainian',
            'ur_UR' => 'Urdu',
            'vi_VI' => 'Vietnamese',
            'cy_CY' => 'Welsh',
            'yi_YI' => 'Yiddish',
            'yo_YO' => 'Yoruba',
            'zu_ZU' => 'Zulu');
        $langOptions = '';
        foreach ($langs as $k => $v) {
          if ($this->target == $k) {
            $selected = "selected='selected'";
          }
          else {
            $selected = '';
          }
          $langOptions .= "<option value='$k' $selected>$v</option>\n";
        }
        $invite = "If you speak another language, please help translate this plugin. You can either use our web interface or download the POT files.<br />"
                . "Select a language: <select name='ezt-createpo'>$langOptions</select>&nbsp;"
                . "&nbsp;<span onmouseover=\"Tip('By default, the translator will query Google Translator for each string it cannot find a translation of. This may take a few minutes. If you would rather not wait, please uncheck this option.', WIDTH, 350, TITLE, 'Query Google?', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 0, 5])\" onmouseout=\"UnTip()\"><input type='checkbox' name='ezt-google' checked='checked' />&nbsp;Use Google? </span>&nbsp;"
                . "&nbsp;<input type='submit' name='ezt-translate' onmouseover=\"Tip('$tip', WIDTH, 350, TITLE, 'How to Translate?', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 0, 5])\" onmouseout=\"UnTip()\" value ='Use Web Interface'/> &nbsp;"
                . "&nbsp;<input type='submit' name='ezt-download' onmouseover=\"Tip('$tipPot', WIDTH, 350, TITLE, 'How to Use POT files?', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 0, 5])\" onmouseout=\"UnTip()\" value ='Download POT files'/> ";
      }
      else {
        $tip = htmlentities("If you would like to improve this translation, please use the translation interface. It picks up the translatable strings in <b>$plgName</b> and presents them and their existing translations in <b>$locale</b> in an easy-to-edit form. You can then generate a translation file and email it to the author all from the same form. $patience");
        $invite = "<span style='color:red'> Would you like to improve this translation of <b>$plgName</b> in your langugage (<b>$locale</b>)?</span><br />"
                . "&nbsp;<span onmouseover=\"Tip('By default, the translator will query Google Translator for each string it cannot find a translation of. This may take a few minutes. If you would rather not wait, please uncheck this option.', WIDTH, 350, TITLE, 'Query Google?', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 0, 5])\" onmouseout=\"UnTip()\"><input type='checkbox' name='ezt-google' checked='checked' />&nbsp;Use Google? </span>&nbsp;"
                . "&nbsp;<input type='submit' name='ezt-translate' onmouseover=\"Tip('$tip', WIDTH, 350, TITLE, 'How to Translate?', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 0, 5])\" onmouseout=\"UnTip()\" value='Improve $locale translation' />&nbsp;"
                . "&nbsp;<input type='submit' name='ezt-download' onmouseover=\"Tip('$tipPot', WIDTH, 350, TITLE, 'How to Use POT files?', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 0, 5])\" onmouseout=\"UnTip()\" value ='Download POT files'/> ";
      }
      $invite .= '<input type="hidden" name="ezt-slug" value="' . $this->slug . '" />';
      return $invite;
    }

    function loadTran($target = '', $helper = '', $domain = '') {
      $locale = '';
      if (empty($target)) {
        $target = $this->target;
      }
      if (empty($target)) {
        $target = $this->locale;
      }
      if (empty($helper)) {
        $moDir = "{$this->plgDir}/lang";
      }
      else {
        $moDir = "{$this->plgDir}/../$helper/lang";
      }
      if (empty($domain)) {
        $domain = $this->domain;
        $moFile = "$moDir/{$target}.mo";
      }
      else {
        $moFile = "$moDir/{$target}_{$domain}.mo";
      }
      $foundMO = false;
      if (file_exists($moFile) && is_readable($moFile)) {
        $foundMO = true;
      }
      else {
        // look for any other similar locale with the same first two characters
        $lo = substr($target, 0, 2);
        $pattern = str_replace($target, "$lo*", $moFile);
        $moFiles = glob($pattern);
        if (!empty($moFiles)) {
          $moFile = $moFiles[0];
          $foundMO = true;
        }
      }
      if ($foundMO) {
        load_textdomain($domain, $moFile);
        if ($this->isEmbedded) {
          $locale = substr(basename($moFile), 0, 5);
        }
      }
      return $locale;
    }

    function setLang($target = '') {
      if (empty($target)) {
        $target = $this->target;
      }
      $this->helpers = array('' => 'easy-common', 'easy-adsense' => 'easy-common');
      $lo = substr($target, 0, 2);
      if ($lo != 'en') {
        $locale = $this->loadTran($target);
        // Append translations in the helpers
        foreach ($this->helpers as $helper => $domain) {
          $this->loadTran($target, $helper, $domain);
        }
        if (empty($locale)) {
          $this->state = "Not Translated";
        }
        else if ($locale == $this->locale) {
          $this->state = "Translated";
        }
        else {
          $this->state = "Alternate MO";
          $this->locale = $locale;
        }
      }
      else {
        // TODO: Ask English speakers to help translate to their second lang
        $this->state = "English";
      }
    }

    function renderTranslator() {
      $this->getSessionVars();
      echo "<br />\n";
      echo "<br />\n";
      echo '<div style="background-color:#ddd;padding:5px;border: solid 1px;margin:5px;">';
      echo $this->getInvite();
      if ($this->state != "Not Translated" && $this->state != "English") {
        echo "<input type='image' title='Switch to English temporarily' onmouseover = 'Tip(\"If you want to temporarily switch to English, please click here.\",WIDTH, 200)' onmouseout='UnTip()' src='{$this->plgURL}/english.gif' style='float:right;padding:0' name='ezt-english' value='english'>";
        echo '</div>';
        echo $this->adminMsg;
        return;
      }
      else if ($this->state != "English") {
        echo "<br />";
        $plgName = strtr(str_replace(' pro', '', strtolower($this->plgName)), ' ', '-');
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
          $msBtn = " <button type='button' id='btnMS' onclick=\"toggleVisibility('MicrosoftTranslatorWidget', 'btnMS', 'Microsoft');\">Show Microsoft</button>";
          $msLink = "<a target=_blank href='http://www.bing.com/translator'>Microsoft<sup>&reg;</sup></a> ";
          $msJS = "<div id='MicrosoftTranslatorWidget' style='margin-left:auto;margin-right:auto;display:none; width: 200px; min-height: 83px; border-color: #404040; background-color: #A0A0A0;'><noscript><a href='http://www.microsofttranslator.com/bv.aspx?a=http%3a%2f%2fwww.thulasidas.com%2fplugins%2f$plgName'>Translate this page</a><br />Powered by <a href='http://www.bing.com/translator'>Microsoft® Translator</a></noscript></div> <script type='text/javascript'> /* <![CDATA[ */ setTimeout(function() { var s = document.createElement('script'); s.type = 'text/javascript'; s.charset = 'UTF-8'; s.src = ((location && location.href && location.href.indexOf('https') == 0) ? 'https://ssl.microsofttranslator.com' : 'http://www.microsofttranslator.com' ) + '/ajax/v2/widget.aspx?mode=manual&from=en&layout=ts'; var p = document.getElementsByTagName('head')[0] || document.documentElement; p.insertBefore(s, p.firstChild); }, 0); /* ]]> */ </script>";
        }
        else {
          $msBtn = $msJs = $msLink = '';
        }
        if ($google) {
          $ggBtn = " <button type='button' id='btnGG' onclick=\"toggleVisibility('GoogleTranslatorWidget', 'btnGG', 'Google');\">Show Google</button>";
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
        echo "See this page in your language (<code>{$this->locale}</code>) using machine translation. $ggLink $or $msLink Translator.";
        echo $ggBtn . $msBtn . $ggJS . $msJS;
        echo '</div>';
      }
      else {
        echo '</div>';
      }
    }

  }

}

if (!class_exists('ZipStream')) {
##########################################################################
# ZipStream - Streamed, dynamically generated zip archives.              #
# by Paul Duncan <pabs@pablotron.org>                                    #
#                                                                        #
# Copyright (C) 2007-2009 Paul Duncan <pabs@pablotron.org>               #
#                                                                        #
# Permission is hereby granted, free of charge, to any person obtaining  #
# a copy of this software and associated documentation files (the        #
# "Software"), to deal in the Software without restriction, including    #
# without limitation the rights to use, copy, modify, merge, publish,    #
# distribute, sublicense, and/or sell copies of the Software, and to     #
# permit persons to whom the Software is furnished to do so, subject to  #
# the following conditions:                                              #
#                                                                        #
# The above copyright notice and this permission notice shall be         #
# included in all copies or substantial portions of the of the Software. #
#                                                                        #
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,        #
# EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF     #
# MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. #
# IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR      #
# OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,  #
# ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR  #
# OTHER DEALINGS IN THE SOFTWARE.                                        #
##########################################################################

  class ZipStream {

    const VERSION = '0.2.2';

    var $opt = array(), $files = array(), $cdr_ofs = 0, $ofs = 0;

    function __construct($name = null, $opt = array()) {
      $this->opt = $opt;
      if (empty($this->opt['large_file_size'])) {
        $this->opt['large_file_size'] = 20 * 1024 * 1024;
      }
      if (empty($this->opt['large_file_method'])) {
        $this->opt['large_file_method'] = 'store';
      }
      $this->output_name = $name;
      if ($name || $opt['send_http_headers']) {
        $this->need_headers = true;
      }
    }

    function add_file($name, $data, $opt = array()) {
      $zdata = gzdeflate($data);
      $crc = crc32($data);
      $zlen = strlen($zdata);
      $len = strlen($data);
      $meth = 0x08;
      $this->add_file_header($name, $opt, $meth, $crc, $zlen, $len);
      $this->send($zdata);
    }

    function add_file_from_path($name, $path, $opt = array()) {
      if ($this->is_large_file($path)) {
        $this->add_large_file($name, $path, $opt);
      }
      else {
        $data = file_get_contents($path);
        $this->add_file($name, $data, $opt);
      }
    }

    function finish() {
      $this->add_cdr($this->opt);
      $this->clear();
    }

    private function add_file_header($name, $opt, $meth, $crc, $zlen, $len) {
      $name = preg_replace('/^\\/+/', '', $name);
      $nlen = strlen($name);
      if (empty($opt['time'])) {
        $opt['time'] = time();
      }
      $dts = $this->dostime($opt['time']);
      $fields = array(array('V', 0x04034b50), array('v', (6 << 8) + 3), array('v', 0x00), array('v', $meth), array('V', $dts), array('V', $crc), array('V', $zlen), array('V', $len), array('v', $nlen), array('v', 0),);
      $ret = $this->pack_fields($fields);
      $cdr_len = strlen($ret) + $nlen + $zlen;
      $this->send($ret . $name);
      $this->add_to_cdr($name, $opt, $meth, $crc, $zlen, $len, $cdr_len);
    }

    private function add_large_file($name, $path, $opt = array()) {
      $st = stat($path);
      $block_size = 1048576;
      $algo = 'crc32b';
      $zlen = $len = $st['size'];
      $meth_str = $this->opt['large_file_method'];
      if ($meth_str == 'store') {
        $meth = 0x00;
        $crc = unpack('V', hash_file($algo, $path, true));
        $crc = $crc[1];
      }
      elseif ($meth_str == 'deflate') {
        $meth = 0x08;
        $fh = fopen($path, 'rb');
        $hash_ctx = hash_init($algo);
        $zlen = 0;
        while ($data = fgets($fh, $block_size)) {
          hash_update($hash_ctx, $data);
          $data = gzdeflate($data);
          $zlen += strlen($data);
        } fclose($fh);
        $crc = unpack('V', hash_final($hash_ctx, true));
        $crc = $crc[1];
      }
      else {
        die("unknown large_file_method: $meth_str");
      } $this->add_file_header($name, $opt, $meth, $crc, $zlen, $len);
      $fh = fopen($path, 'rb');
      while ($data = fgets($fh, $block_size)) {
        if ($meth_str == 'deflate') {
          $data = gzdeflate($data);
        }
        $this->send($data);
      } fclose($fh);
    }

    function is_large_file($path) {
      $st = stat($path);
      return ($this->opt['large_file_size'] > 0) && ($st['size'] > $this->opt['large_file_size']);
    }

    private function add_to_cdr($name, $opt, $meth, $crc, $zlen, $len, $rec_len) {
      $this->files[] = array($name, $opt, $meth, $crc, $zlen, $len, $this->ofs);
      $this->ofs += $rec_len;
    }

    private function add_cdr_file($args) {
      list ($name, $opt, $meth, $crc, $zlen, $len, $ofs) = $args;
      if (empty($opt['comment'])) {
        $comment = '';
      }
      else {
        $comment = $opt['comment'];
      }
      $dts = $this->dostime($opt['time']);
      $fields = array(array('V', 0x02014b50), array('v', (6 << 8) + 3), array('v', (6 << 8) + 3), array('v', 0x00), array('v', $meth), array('V', $dts), array('V', $crc), array('V', $zlen), array('V', $len), array('v', strlen($name)), array('v', 0), array('v', strlen($comment)), array('v', 0), array('v', 0), array('V', 32), array('V', $ofs),);
      $ret = $this->pack_fields($fields) . $name . $comment;
      $this->send($ret);
      $this->cdr_ofs += strlen($ret);
    }

    private function add_cdr_eof($opt = null) {
      $num = count($this->files);
      $cdr_len = $this->cdr_ofs;
      $cdr_ofs = $this->ofs;
      $comment = '';
      if (!empty($opt) && !empty($opt['comment'])) {
        $comment = $opt['comment'];
      }
      $fields = array(array('V', 0x06054b50), array('v', 0x00), array('v', 0x00), array('v', $num), array('v', $num), array('V', $cdr_len), array('V', $cdr_ofs), array('v', strlen($comment)),);
      $ret = $this->pack_fields($fields) . $comment;
      $this->send($ret);
    }

    private function add_cdr($opt = null) {
      foreach ($this->files as $file) {
        $this->add_cdr_file($file);
      }
      $this->add_cdr_eof($opt);
    }

    function clear() {
      $this->files = array();
      $this->ofs = 0;
      $this->cdr_ofs = 0;
      $this->opt = array();
    }

    private function send_http_headers() {
      $opt = $this->opt;
      $content_type = 'application/x-zip';
      if (!empty($opt['content_type'])) {
        $content_type = $this->opt['content_type'];
      }
      $disposition = 'attachment';
      if (!empty($opt['content_disposition'])) {
        $disposition = $opt['content_disposition'];
      }
      if ($this->output_name) {
        $disposition .= "; filename=\"{$this->output_name}\"";
      }
      $headers = array('Content-Type' => $content_type, 'Content-Disposition' => $disposition, 'Pragma' => 'public', 'Cache-Control' => 'public, must-revalidate', 'Content-Transfer-Encoding' => 'binary',);
      foreach ($headers as $key => $val) {
        header("$key: $val");
      }
    }

    private function send($str) {
      if ($this->need_headers) {
        $this->send_http_headers();
      }
      $this->need_headers = false;
      echo $str;
    }

    function dostime($when = 0) {
      $d = getdate($when);
      if ($d['year'] < 1980) {
        $d = array('year' => 1980, 'mon' => 1, 'mday' => 1, 'hours' => 0, 'minutes' => 0, 'seconds' => 0);
      } $d['year'] -= 1980;
      return ($d['year'] << 25) | ($d['mon'] << 21) | ($d['mday'] << 16) | ($d['hours'] << 11) | ($d['minutes'] << 5) | ($d['seconds'] >> 1);
    }

    function pack_fields($fields) {
      list ($fmt, $args) = array('', array());
      foreach ($fields as $field) {
        $fmt .= $field[0];
        $args[] = $field[1];
      } array_unshift($args, $fmt);
      return call_user_func_array('pack', $args);
    }

  }

}