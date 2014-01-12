<?php
/*
Copyright (C) 2008 www.thulasidas.com

This file is part of the program "Easy AdSense."

Easy AdSense is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or (at
your option) any later version.

Easy AdSense is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

Easy AdSense is supported by ad space sharing. Unless you configure
the program (following the instructions on its admin page) and
explicitly turn off the sharing, you agree to run its developer's ads
on your site(s). By using the program, you are agreeing to this
condition, and confirming that your sites abide by Google's policies
and terms of service.
*/

define('MINMATCH', 89) ;

if (!class_exists("ezTran") && !class_exists("PO")) {
  class PO { // an id-str pair with attributes
    var $num, $id, $str, $tranId, $tranVal, $keyId, $keyVal ;

    function PO($id, $str) {
      $this->id = (string) $id ;
      $this->str = (string) $str ;
      $this->tranVal = MINMATCH ;
      $this->keyVal = MINMATCH ;
    }

    // Returns a properly escaped string
    static function decorate($str, $esc) {
      if (!get_magic_quotes_gpc()) $str = addcslashes($str, $esc) ;
      return $str ;
    }
    static function undecorate($str) {
      if (!get_magic_quotes_gpc()) $str = stripslashes($str) ;
      return $str ;
    }

    // Returns a text-area string of the Id
    function textId() {
      $ht = round(strlen($this->id)/52 + 1) * 25 ;
      $col = 'background-color:#f5f5f5;' ;
      $col = $tit = '' ;
      if ($this->keyVal > MINMATCH+1) {
        $col = "background-color:#ffc;border: solid 1px #f00" ;
        $tit = 'onmouseover = "Tip(\'Another similar string: ' .
          htmlspecialchars('<br /><em><b>' . $this->keyId .
                           '</b></em><br /> ', ENT_QUOTES) .
          'exists. Please alert the author.\',WIDTH, 300)" ' .
          'onmouseout="UnTip()"';
      }
      $s = '<textarea cols="50" rows="15" name="k' . $this->num .
        '" style="width: 45%;height:' . $ht . 'px;' . $col . '" ' .
        $tit . ' readonly="readonly">';
      $s .= htmlspecialchars($this->id, ENT_QUOTES) ;
      $s .= '</textarea>&nbsp;&nbsp;' ;
      return $s ;
    }

    function textStr() {
      $ht = round(strlen($this->id)/52 + 1) * 25 ;
      $col = $tit = '' ;
      if ($this->tranVal > MINMATCH+1){
        $col = "background-color:#fdd;border: solid 1px #f00" ;
        $tit = 'onmouseover = "Tip(\'Using the translation for a similar string: ' .
          htmlspecialchars('<br /><em><b>' . $this->tranId .
                           '</b></em><br />', ENT_QUOTES) .
          'Please check carefully.\',WIDTH, 300)" ' .
          'onmouseout="UnTip()"';
      }
      $s =  '<textarea cols="50" rows="15" name="' . $this->num .
        '" style="width: 45%;height:' . $ht . 'px;' . $col . '" ' .
        $tit . '>';
      $s .=  htmlspecialchars($this->str, ENT_QUOTES) ;
      $s .= '</textarea><br />' ;
      return $s ;
    }
  }

  class ezTran {
    var $status, $error ;
    function ezTran()
    {
      $this->status = '' ;
      $this->error = '' ;
      if (!empty($_POST['ezAds-savePot'])) {
        $file = $_POST['potFile'] ;
        $str = $_POST['potStr'] ;
        header('Content-Disposition: attachment; filename="' . $file .'"');
        header("Content-Transfer-Encoding: ascii");
        header('Expires: 0');
        header('Pragma: no-cache');
        ob_start() ;
        print htmlspecialchars_decode($str, ENT_QUOTES) ;
        ob_end_flush() ;
        $this->status = '<div class="updated">Pot file: ' . $file . ' was saved.</div> ' ;
        exit(0) ;
      }
      if (!empty($_POST['ezAds-clear'])) {
        $this->status =
          '<div class="updated">Reloaded the translations from PHP files and MO.</div> ' ;
        unset($_SESSION['ezAds-POs']) ;
      }
      if (!empty($_POST['ezAds-mailPot'])) {
        $file = $_POST['potFile'] ;
        $str = stripslashes($_POST['potStr']) ;
        $str = str_replace("\'", "'", $str) ;

        if (!class_exists("phpmailer")) {
          require_once(ABSPATH.'wp-includes/class-phpmailer.php');
        }
        $mail = new PHPMailer();
        $mail->From = get_bloginfo('admin_email') ;
        $mail->FromName = get_bloginfo('name') ;
        $mail->AddAddress('Manoj@Thulasidas.com', "Manoj Thulasidas") ;
        $mail->CharSet = get_bloginfo('charset');
        $mail->Mailer = 'php';
        $mail->SMTPAuth = false;
        $mail->Subject = $file;
        $mail->AddStringAttachment($str,$file) ;
        $pos1 = strpos($str, "msgstr") ;
        $pos2 = strpos($str, "msgid", $pos1) ;
        $head = substr($str, 0, $pos2) ;
        $mail->Body = $head ;
        if ($mail->Send())
          $this->status = '<div class="updated">Pot file: ' . $file . ' was sent.</div> ' ;
        else
          $this->error = '<div class="error">Error: ' . $mail->ErrorInfo .
            ' Please save the pot file and <a href="http://manoj.thulasidas.com/mail.shtml" target=_blank>contact the author</a></div>' ;
      }
    }

    // Return the contents of all PHP files in the dir specified
    function getFileContents($dir="") {
      if ($dir == "") $dir = dirname(__FILE__) ;
      $files = glob($dir . '/*.php') ;
      $page = "" ;
      foreach ($files as $f) {
        $page .= file_get_contents($f, FILE_IGNORE_NEW_LINES) ;
      }
      return $page ;
    }

    // Percentage Levenshtein distance
    function levDist(&$s1, &$s2) {
      similar_text($s1, $s2, $p) ;
      return round($p) ;
    }

    // Get the closest existing translation keys, and the recursivley closest in the
    // key set
    function getClose(&$mo, &$POs){
      foreach ($POs as $n => $po){
        $s1 = $po->id ;
        $l1 = strlen($s1);
        if (strlen($po->str) == 0) {
          if (!empty($mo)) foreach ($mo as $mn => $mk) {
            $s2 = $mn ;
            $result = $this->levDist($s1, $s2) ;
            if ($result > $po->tranVal) {
              $po->tranVal = $result ;
              $po->tranId = $mn ;
              $po->str = $mk->translations[0] ;
            }
          }
        }
        foreach ($POs as $n2 => $po2){
          if ($n != $n2){
            $s2 = $po2->id ;
            $result = $this->levDist($s1, $s2) ;
            if ($result > $po2->keyVal) {
              $po->keyVal = $result ;
              $po->keyId = $po2->id ;
              $po2->keyVal = $result ;
              $po2->keyId = $po->id ;
            }
          }
        }
      }
    }

    // Get the strings that look like translation keys
    function getTranPOs(&$contents, &$mo, $domain, &$POs) {
      preg_match_all("#_[_e].*\([\'\"](.+)[\'\"]\s*,\s*[\'\"]" . $domain ."[\'\"]#",
                     $contents, $matches) ;
      $keys = array_unique($matches[1]) ;
      $keys = str_replace(array("\'", '\"', '\n'), array("'", '"', "\n"), $keys) ;
      foreach ($keys as $n => $k) {
        if (!empty($mo[$k])) {
          $v = $mo[$k] ;
          $t = $v->translations[0] ;
        }
        else {
          $t = '' ;
        }
        $po = new PO($k, $t) ;
        $po->num = $n ;
        array_push($POs, $po) ;
      }
      $this->getClose($mo, $POs) ;
    }

    // Make a POT string from ids and msgs
    function mkPot(&$POs, $msg){
      $pot = '' ;
      $pot .=
'# This file was generated by Easy Translator for Easy AdSense -- a WordPress plugin translator
# Your Name: ' . $msg["name"] . '
# Your Email: ' . $msg["email"] . '
# Your Website: ' . $msg["blog"] . '
# Your URL: ' . $msg["url"] . '
# Your Locale: ' . $msg["locale"] . '
# Your Language: ' . $msg["lang"] . '
#, fuzzy
msgid ""
msgstr ""
"Project-Id-Version: Easy AdSenser\n"
"PO-Revision-Date: ' . current_time('mysql') . '\n"
"Last-Translator: ' . $msg['name'] . ' <' . $msg['email'] . '>\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=' . $msg['charset'] . '\n"
"Content-Transfer-Encoding: 8bit\n"

' ;
      foreach ($POs as $n => $po) {
        $pot .= "msgid " . '"' . PO::decorate($po->id, "\n\r\"") . '"' . "\n" ;
        $t = $msg[$po->num] ;
        $pot .= "msgstr " . '"' . PO::decorate($t, "\n\r") . '"' . "\n\n" ;
      }
      return $pot ;
    }

    // Update the PO objects in $POs with the text box stuff
    function updatePot(&$POs, $msg){
      foreach ($POs as $n => $po) {
        $t = $msg[$po->num] ;
        $po->str = PO::undecorate($t) ;
      }
    }

    // Prints out the admin page
    function printAdminPage() {
      $locale = get_locale();
      $made = isset($_POST['ezAds-make']) ;
      $saving = isset($_POST['ezAds-savePot']) ;

?>
<h2>Easy Translator</h2>
<p>
<strong><a href="options-general.php?page=easy-adsense.php"><em>Easy AdSense</em></a> Translation Interface</strong>
</p>
<?php

      $domain = 'easy-adsenser' ;
      $version = (float)get_bloginfo('version') ;
      if ($version < 2.80) {
        echo '<div class="error"><p><b>Error</b>:' .
          'Sorry, Easy Translator works only on WP2.8+</p></div>' ;
        return ;
      }

      if (isset($_SESSION['ezAds-POs'])){
        $POs = $_SESSION['ezAds-POs'];
      }
      else {
        global $l10n;
        $mo = array($l10n[$domain]->entries) ;

        $s = $this->getFileContents() ;
        $parent = dirname(__FILE__) . '/..' ;
        $s .= $this->getFileContents($parent) ;

        $POs = array() ;

        $this->getTranPOs($s, $mo[0], $domain, $POs) ;
      }

      if ($made) {
        $pot = htmlspecialchars($this->mkPot($POs, $_POST), ENT_QUOTES) ;
        $this->updatePot($POs, $_POST) ;
        $_SESSION['ezAds-POs'] = $POs ;
      }
      else {
        global $current_user;
        get_currentuserinfo();
        $pot = '' ;
        $pot .= '<div style="width: 15%; float:left">Your Name:</div>' .
          '<input type="text" style="width: 30%" name="name" value="' .
          $current_user->user_firstname . " " .
          $current_user->user_lastname . '" /><br />' . "\n" ;
        $pot .= '<div style="width: 15%; float:left">Your Email:</div>' .
          '<input type="text" style="width: 30%" name="email" value="' .
          $current_user->user_email . '" /><br />' . "\n" ;
        $pot .= '<div style="width: 15%; float:left">Your Website:</div>' .
          '<input type="text" style="width: 30%" name="blog" value="' .
          get_bloginfo('blog') . '" />' . "\n<br />" ;
        $pot .= '<div style="width: 15%; float:left">Your URL:</div>' .
          '<input type="text" style="width: 30%" name="url" value="' .
          get_bloginfo('url') . '" />' . "\n<br />" ;
        $pot .= '<div style="width: 15%; float:left">Your Locale:</div>' .
          '<input type="text" style="width: 30%" name="locale" value="' .
          $locale . '" /><br />' . "\n" ;
        $pot .= '<div style="width: 15%; float:left">Your Language:</div>' .
          '<input type="text" style="width: 30%" name="lang" value="' .
          get_bloginfo('language') . '" /><br />' . "\n" ;
        $pot .= '<div style="width: 15%; float:left">Character Set:</div>' .
          '<input type="text" style="width: 30%" name="charset" value="' .
          get_bloginfo('charset') . '" />' . "\n<br /><br />" ;

        $pot .= '<div style="width:800px;padding:10px;padding-top:25px"></div>' ;
        $pot .= '<div style="width:38%px;paddling:10px;padding-left:100px;float:left">' .
          '<b>English (en_US)</b></div>' ;
        $pot .= '<div style="width:38%;paddling:10px;padding-left:80px;float:right">' .
          '<b>Your Language (' . $locale . ')</b></div>' ;
        $pot .= '<div style="width:100%;padding:15px"></div>' ;

        foreach ($POs as $n => $po) {
          if (!is_object ($po) && gettype ($po) == 'object')
            $po = unserialize (serialize ($po)); // need this only on Chrome!!
          $pot .= $po->textId() . "\n" . $po->textStr() . "\n\n" ;
        }
      }
      $makeStr =
'<div class="submit">
<input type="submit" name="ezAds-make" value="Display &amp; Save POT File" title="Make a POT file with the translation strings below and display it" />&nbsp;
<input type="submit" name="ezAds-clear" value="Reload Translation" title="Discard your changes and reload the translation" onClick="return confirm(\'Are you sure you want to discard your changes?\');" />&nbsp;
</div>' . $this->status . $this->error ;
      $saveStr =
'<div class="submit">
<input type="submit" name="ezAds-savePot" value="Save POT file" title="Saves the strings shown below to your PC as a POT file" />&nbsp;
<input type="submit" name="ezAds-mailPot" value="Mail POT file" title="Email the translation to the plugin autor" onClick="return confirm(\'Are you sure you want to email the author?\');" />&nbsp;
<input type="submit" name="ezAds-editMore" value="Edit More" title="If you are not happy with the strings, edit it further"/>
</div>' . $this->status . $this->error  ;
      if ($made) {
?>
<div style="background-color:#eef;border: solid 1px #005;padding:5px">
If you are happy with the POT file as below, please save it or email it to the author.
If not, edit it further.
</div>
<?php
        echo '<input type="hidden" name="potFile" value="' .
              $domain . "-" . $locale . '.po" />' ;
        echo '<input type="hidden" name="potStr" value="' . $pot . '" />' ;
        echo $saveStr ;
        echo  "\n" . '<pre>' . $pot . '</pre>' ;
      }
      else
      {
?>
<div style="background-color:#eef;border: solid 1px #005;padding:5px">
Add or modify translation for your language <b><?php echo $locale ?></b>.
<br />
Enter the translated strings in the text boxes below and hit the "Display POT File" button.
</div>
<?php
         echo $makeStr ;
         echo $pot ;
       }
    } // End function printAdminPage()
  }
} // End Class ezTran

?>
