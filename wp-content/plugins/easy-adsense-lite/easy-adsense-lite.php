<?php
/*
  Plugin Name: Easy AdSense
  Plugin URI: http://www.thulasidas.com/adsense
  Description: Easiest way to show AdSense and make money from your blog. Configure it at <a href="options-general.php?page=easy-adsense-lite.php">Settings &rarr; Easy AdSense</a>.
  Version: 6.51
  Author: Manoj Thulasidas
  Author URI: http://www.thulasidas.com
*/

/*
  Copyright (C) 2008 www.thulasidas.com

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

if (!class_exists("EzAdSense")) {
  class EzAdSense {
    var $plugindir, $invite, $locale, $defaults, $ezTran,
      $leadin, $leadout, $options, $optionName ;
    var $ezMax, $urMax, $luMax, $urCount, $ezCount ;
    var $adminMsg ;

    function EzAdSense() {
      $this->adminMsg = '' ;
      if (file_exists (dirname (__FILE__).'/defaults.php')) {
        include (dirname (__FILE__).'/defaults.php');
        $this->defaults = $defaults ;
      }
      if (empty($this->defaults))  {
        $this->adminMsg = '<div class="error"><p><b><em>Easy AdSense</em></b>: Error locating or loading the defaults! Ensure <code>defaults.php</code> exists, or reinstall the plugin.</p></div>' ;
      }
      $this->optionName = "ezAdSense" . get_option('stylesheet') ;
      $this->options = get_option($this->optionName) ;
      if (empty($this->options)) {
        $this->options = $this->mkDefaultOptions() ;
      }
      $this->setLang() ;
      $this->plugindir = get_option('siteurl') . '/' . PLUGINDIR .
        '/' . basename(dirname(__FILE__)) ;
      // Counts and limis
      $this->ezMax = 99 ;
      $this->urMax = 99 ;
      $this->luMax = 4 ;
      $this->urCount = 0 ;
      $this->ezCount = 0 ;
    }

    function session_start(){
      if (!session_id()) @session_start() ;
    }

    function handleSubmits() {
      if (empty($_POST)) {
        return ;
      }
      if (!check_admin_referer('EzAdsenseSubmit','EzAdsenseNonce')) {
        return ;
      }
      if ((isset($_POST['ezAds-translate']) && !empty($_POST['ezAds-translate'])) ||
        (isset($_POST['ezAds-make']) && !empty($_POST['ezAds-make'])) ||
        (isset($_POST['ezAds-clear']) && !empty($_POST['ezAds-clear'])) ||
        (isset($_POST['ezAds-savePot']) && !empty($_POST['ezAds-savePot'])) ||
        (isset($_POST['ezAds-mailPot']) && !empty($_POST['ezAds-mailPot'])) ||
        (isset($_POST['ezAds-editMore']) && !empty($_POST['ezAds-editMore']))) {
        if (file_exists (dirname (__FILE__).'/lang/easy-translator.php')){
          include (dirname (__FILE__).'/lang/easy-translator.php');
          $this->ezTran = new ezTran ;
        }
      }
      if (isset($_POST['update_ezAdSenseSettings'])) {
        if (isset($_POST['ezAdSenseShowLeadin']))
          $this->options['show_leadin'] = $_POST['ezAdSenseShowLeadin'];
        if (isset($_POST['ezAdSenseTextLeadin']))
          $this->options['text_leadin'] = $_POST['ezAdSenseTextLeadin'];
        if (isset($_POST['ezLeadInMargin']))
          $this->options['margin_leadin'] = $_POST['ezLeadInMargin'];
        if (isset($_POST['ezLeadInWC']))
          $this->options['wc_leadin'] = $_POST['ezLeadInWC'];
        if (isset($_POST['ezHeaderLeadin']))
          $this->options['header_leadin'] = $_POST['ezHeaderLeadin'];

        if (isset($_POST['ezAdSenseShowMidtext']))
          $this->options['show_midtext'] = $_POST['ezAdSenseShowMidtext'];
        if (isset($_POST['ezAdSenseTextMidtext']))
          $this->options['text_midtext'] = $_POST['ezAdSenseTextMidtext'];
        if (isset($_POST['ezMidTextWC']))
          $this->options['wc_midtext'] = $_POST['ezMidTextWC'];
        if (isset($_POST['ezMidTextMargin']))
          $this->options['margin_midtext'] = $_POST['ezMidTextMargin'];

        if (isset($_POST['ezAdSenseShowLeadout']))
          $this->options['show_leadout'] = $_POST['ezAdSenseShowLeadout'];
        if (isset($_POST['ezAdSenseTextLeadout']))
          $this->options['text_leadout'] = $_POST['ezAdSenseTextLeadout'];
        if (isset($_POST['ezLeadOutWC']))
          $this->options['wc_leadout'] = $_POST['ezLeadOutWC'];
        if (isset($_POST['ezLeadOutMargin']))
          $this->options['margin_leadout'] = $_POST['ezLeadOutMargin'];
        if (isset($_POST['ezFooterLeadout']))
          $this->options['footer_leadout'] = $_POST['ezFooterLeadout'];

        if (isset($_POST['ezAdSenseShowWidget']))
          $this->options['show_widget'] = $_POST['ezAdSenseShowWidget'];
        if (isset($_POST['ezAdWidgetTitle']))
          $this->options['title_widget'] = $_POST['ezAdWidgetTitle'];
        if (isset($_POST['ezAdSenseTextWidget']))
          $this->options['text_widget'] = $_POST['ezAdSenseTextWidget'];
        $this->options['kill_widget_title'] = isset($_POST['ezAdKillWidgetTitle']);
        if (isset($_POST['ezWidgetMargin']))
          $this->options['margin_widget'] = $_POST['ezWidgetMargin'];

        if (isset($_POST['ezAdSenseShowLU']))
          $this->options['show_lu'] = $_POST['ezAdSenseShowLU'];
        if (isset($_POST['ezAdLUTitle']))
          $this->options['title_lu'] = $_POST['ezAdLUTitle'];
        if (isset($_POST['ezAdSenseTextLU']))
          $this->options['text_lu'] = $_POST['ezAdSenseTextLU'];
        $this->options['kill_lu_title'] = isset($_POST['ezAdKillLUTitle']);
        if (isset($_POST['ezLUMargin']))
          $this->options['margin_lu'] = $_POST['ezLUMargin'];

        if (isset($_POST['ezAdSenseShowGSearch'])) {
          $title = $_POST['ezAdSenseShowGSearch']; ;
          if ($title != 'dark' && $title != 'light' && $title != 'no')
            $title = $_POST['ezAdSearchTitle'];
          $this->options['title_gsearch'] = $title;
        }
        if (isset($_POST['killInvites']))
          $this->options['kill_invites'] = $_POST['killInvites'];
        if (isset($_POST['killRating']))
          $this->options['kill_rating'] = $_POST['killRating'];
        $this->options['kill_gsearch_title'] = isset($_POST['ezAdKillSearchTitle']);
        if (isset($_POST['ezAdSenseTextGSearch']))
          $this->options['text_gsearch'] = $_POST['ezAdSenseTextGSearch'];
        if (isset($_POST['ezSearchMargin']))
          $this->options['margin_gsearch'] = $_POST['ezSearchMargin'];

        if (isset($_POST['ezAdSenseMax']))
          $this->options['max_count'] = $_POST['ezAdSenseMax'];
        if (isset($_POST['ezAdSenseLinkMax']))
          $this->options['max_link'] = $_POST['ezAdSenseLinkMax'];

        $this->options['force_midad'] = isset($_POST['ezForceMidAd']);
        $this->options['force_widget'] = isset($_POST['ezForceWidget']);
        $this->options['allow_feeds'] = isset($_POST['ezAllowFeeds']);
        $this->options['kill_pages'] = isset($_POST['ezKillPages']);
        $this->options['kill_home'] = isset($_POST['ezKillHome']);
        $this->options['kill_attach'] = isset($_POST['ezKillAttach']);
        $this->options['kill_front'] = isset($_POST['ezKillFront']);
        $this->options['kill_cat'] = isset($_POST['ezKillCat']);
        $this->options['kill_tag'] = isset($_POST['ezKillTag']);
        $this->options['kill_archive'] = isset($_POST['ezKillArchive']);
        $this->options['kill_inline'] = isset($_POST['ezKillInLine']);
        $this->options['kill_linebreaks'] = isset($_POST['ezKillLineBreaks']);
        $this->options['suppressBoxes'] = isset($_POST['ezSuppressBoxes']);
        $this->options['kill_single'] = isset($_POST['ezKillSingle']);
        $this->options['kill_search'] = isset($_POST['ezKillSearch']);
        $this->options['kill_sticky'] = isset($_POST['ezKillSticky']);

        $this->options['show_borders'] = isset($_POST['ezShowBorders']);
        if (isset($_POST['ezBorderWidth']))
          $this->options['border_width'] = intval($_POST['ezBorderWidth']) ;
        if (isset($_POST['ezBorderNormal']))
          $this->options['border_normal'] = strval($_POST['ezBorderNormal']) ;
        if (isset($_POST['ezBorderColor']))
          $this->options['border_color'] = strval($_POST['ezBorderColor']) ;
        if (isset($_POST['ezBorderWidget']))
          $this->options['border_widget'] = $_POST['ezBorderWidget'];
        if (isset($_POST['ezBorderLU']))
          $this->options['border_lu'] = $_POST['ezBorderLU'];

        if (isset($_POST['ezLimitLU'])) {
          $limit = min(intval($_POST['ezLimitLU']), 3) ;
          $this->options['limit_lu'] = $limit ;
        }
        update_option($this->optionName, $this->options);
        $this->adminMsg = '<div class="updated"><p><strong>' .
          __("Settings Updated.", "easy-adsenser") .
          '</strong></p> </div>' ;
      }
      else if (isset($_POST['reset_ezAdSenseSettings'])) {
        $this->resetOptions();
        $this->adminMsg = '<div class="updated"><p><strong>' .
          __("Ok, all your settings have been discarded!", "easy-adsenser") .
          '</strong></p> </div>' ;
      }
      else if (isset($_POST['english'])) {
        $this->locale = "en_US" ;
        $moFile = dirname(__FILE__) . '/lang/easy-adsenser.mo';
        global $l10n;
        $version = (float)get_bloginfo('version') ;
        if ($version < 2.80)
          $l10n['easy-adsenser']->cache_translations = array() ;
        else
          unset($l10n['easy-adsenser']) ;
        load_textdomain('easy-adsenser', $moFile);
        $this->adminMsg = '<div class="updated"><p><strong>Ok, in English for now. ' .
          '<a href="options-general.php?page=easy-adsense-lite.php">Switch back</a>. ' .
          '</strong></p> </div>' ;
      }
      else if (isset($_POST['clean_db']) || isset($_POST['kill_me'])) {
        $this->resetOptions();
        $this->cleanDB('ezAdSense');
        $this->adminMsg = '<div class="updated"><p><strong>' .
          __("Database has been cleaned. All your options for this plugin (for all themes) have been removed.", "easy-adsenser") .
          '</strong></p> </div>' ;

        if (isset($_POST['kill_me'])) {
          remove_action('admin_menu', 'ezAdSense_ap');
          $me = basename(dirname(__FILE__)) . '/' . basename(__FILE__);
          $this->adminMsg = '<div class="updated"><p><strong>' .
            __("This plugin can be deactivated now. ", "easy-adsenser") .
            '<a href="plugins.php">' .
            __("Go to Plugins", "easy-adsenser") .
            '</a>.</strong></p></div>' ;
        }
      }
    }

    function setLang() {
      $locale = get_locale() ;
      $locale = str_replace('-','_', $locale);
      $this->locale = $locale ;

      $name =  'easy-adsenser' ;

      if(!empty($this->locale) && $this->locale != 'en_US') {
        $this->invite = '<hr /><font color="red"> Would you like to improve this translation of <b>Easy Adsense</b> in your langugage (<b>' . $locale .
          "</b>)?&nbsp; <input type='submit' name='ezAds-translate' onmouseover=\"Tip('If you would like to improve this translation, please use the translation interface. It picks up the translatable strings in &lt;b&gt;Easy AdSense&lt;/b&gt; and presents them and their existing translations in &lt;b&gt;" . $locale .
          "&lt;/b&gt; in an easy-to-edit form. You can then generate a translation file and email it to the author all from the same form. Slick, isn\'t it?  I will include your translation in the next release.', WIDTH, 350, TITLE, 'How to Translate?', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 0, 5])\" onmouseout=\"UnTip()\" value='Improve " . $locale . " translation' /></font>" ;

        $moFile = dirname(__FILE__) . '/lang/' . $this->locale . '/' . $name . '.mo';
        if(@file_exists($moFile) && is_readable($moFile))
          load_textdomain($name, $moFile);
        else {
          // look for any other similar locale with the same first three characters
          $foo = glob(dirname(__FILE__) . '/lang/' . substr($this->locale, 0, 2) .
                 '*/easy-adsenser.mo') ;
          if (!empty($foo)>0) {
            $moFile = $foo[0] ;
            load_textdomain($name, $moFile);
            $this->locale = basename(dirname($moFile)) ;
          }
          $this->invite = '<hr /><font color="red"> Would you like to see ' .
            '<b>Easy Adsense</b> in your langugage (<b>' . $locale .
            "</b>)?&nbsp; <input type='submit' name='ezAds-translate' onmouseover=\"Tip('It is easy to have &lt;b&gt;Easy AdSense&lt;/b&gt; in your language. All you have to do is to translate some strings, and email the file to the author.&lt;br /&gt;&lt;br /&gt;If you would like to help, please use the translation interface. It picks up the translatable strings in &lt;b&gt;Easy AdSense&lt;/b&gt; and presents them (and their existing translations in &lt;b&gt;" . $this->locale .
            "&lt;/b&gt;, if any) in an easy-to-edit form. You can then generate a translation file and email it to the author all from the same form. Slick, isn\'t it?  I will include your translation in the next release.', WIDTH, 350, TITLE, 'How to Translate?', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 0, 5])\" onmouseout=\"UnTip()\" value ='Please help translate ' /></font>" ;
        }
      }
    }

    function mkDefaultOptions(){
      $defaultOptions =
        array('info' => "<!-- Easy AdSense Lite -->",
          'show_leadin' => 'float:right',
          'wc_leadin' => 0,
          'margin_leadin' => 12,
          'text_leadin' => $this->defaults['defaultText'],
          'show_midtext' => 'float:left',
          'header_leadin' => false,
          'wc_midtext' => 0,
          'margin_midtext' => 12,
          'text_midtext' => $this->defaults['defaultText'],
          'show_leadout' => 'no',
          'wc_leadout' => 0,
          'margin_leadout' => 12,
          'text_leadout' => $this->defaults['defaultText'],
          'show_widget' => 'text-align:center',
          'footer_leadout' => false,
          'margin_widget' => 12,
          'text_widget' => $this->defaults['defaultText'],
          'show_lu' => 'text-align:center',
          'margin_lu' => 12,
          'text_lu' => $this->defaults['defaultText'],
          'title_gsearch' => '',
          'kill_gsearch_title' => '',
          'margin_gsearch' => 0,
          'text_gsearch' => $this->defaults['defaultText'],
          'max_count' => 3,
          'max_link' => 0,
          'force_midad' => false,
          'force_widget' => false,
          'allow_feeds' => false,
          'kill_pages' => false,
          'show_borders' => false,
          'border_width' => 1,
          'border_normal' => '00FFFF',
          'border_color' => 'FF0000',
          'border_widget' => false,
          'border_lu' => false,
          'limit_lu' => 1,
          'title_lu' => '',
          'kill_lu_title' => false,
          'kill_invites' => false,
          'kill_rating' => false,
          'kill_attach' => false,
          'kill_home' => false,
          'kill_front' => false,
          'kill_cat' => false,
          'kill_tag' => false,
          'kill_archive' => false,
          'kill_inline' => false,
          'kill_widget_title' => false,
          'kill_linebreaks' => false,
          'kill_single' => false,
          'kill_search' => true,
          'kill_sticky' => false,
          'suppressBoxes' => false,
          'title_widget' => '');
      return $defaultOptions ;
    }

    // Reset all options to defaults
    function resetOptions() {
      $defaultOptions = $this->mkDefaultOptions() ;
      update_option($this->optionName, $defaultOptions);
      $this->options = $defaultOptions ;
    }

    function handleDefaultText($text, $key = '300x250') {
      $ret = $text ;
      if ($ret == $this->defaults['defaultText'] || strlen(trim($ret)) == 0) {
        if ($this->options['suppressBoxes']) {
          $ret = '';
        }
        else {
          $x = strpos($key, 'x') ;
          $w = substr($key, 0, $x);
          $h = substr($key, $x+1);
          $p = (int)(min($w,$h)/6) ;
          $ret = '<div style="width:'.$w.'px;height:'.$h.'px;border:1px solid red;"><div style="padding:'.$p.'px;text-align:center;font-family:arial;font-size:8pt;"><p>Your ads will be inserted here by</p><p><b>Easy AdSense</b>.</p><p>Please go to the plugin admin page to<br /><u title="Generate your ad code from your provider and paste it in the text box for this ad slot">Paste your ad code</u> OR<br /> <u title="Use the dropdown under the text box for this ad slot to suppress it">Suppress this ad slot</u> OR<br /><u title="Use the option to suppress placement boxes">Suppress Placement Boxes</u>.</p></div></div>' ;
        }
      }
      return $ret ;
    }

    function handleDefaults() {
      $texts = array('text_leadin', 'text_midtext', 'text_leadout') ;
      foreach ($texts as $t) {
        $text = $this->options[$t] ;
        $this->options[$t] = $this->handleDefaultText($text) ;
      }
    }

    // Prints out the admin page
    function printAdminPage() {
      // if the defaults are not loaded, send error message
      if (empty($this->defaults)) return ;
      if (file_exists (dirname (__FILE__).'/admin.php')) {
        $this->handleSubmits() ;
        echo $this->adminMsg ;
        include (dirname (__FILE__).'/admin.php');
      }
      else {
        echo '<font size="+1" color="red">' ;
        _e("Error locating the admin page!\nEnsure admin.php exists, or reinstall the plugin.",
          'easy-adsenser') ;
        echo '</font>' ;
      }
    }

    function info($hide=true) {
      if ( ! function_exists( 'get_plugin_data' ) )
      require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
      $plugin_data = get_plugin_data( __FILE__ );
      $version = $plugin_data['Version'];
      $str = "Easy AdSense (WP) V$version" ;
      if ($hide)
        $str = "<!-- $str -->";
      return $str ;
    }

    function cleanDB($prefix){
      global $wpdb ;
      $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '$prefix%'") ;
    }

    function plugin_action($links, $file) {
      if ($file == plugin_basename(dirname(__FILE__).'/easy-adsense-lite.php')) {
        $settings_link = "<a href='options-general.php?page=easy-adsense-lite.php'>" .
          __('Settings', 'easy-adsenser') . "</a>";
        array_unshift( $links, $settings_link );
      }
      return $links;
    }

    function getMetaOptions() {
      global $post;
      $lookup = array('adsense' => 'adsense',
                'adsense-top' =>'show_leadin',
                'adsense-middle' => 'show_midtext',
                'adsense-bottom' => 'show_leadout',
                'adsense-widget' => 'show_widget',
                'adsense-search' => 'title_gsearch',
                'adsense-linkunits' => 'show_lu') ;
      $metaOptions = array() ;
      foreach ($lookup as $metaKey => $optKey) {
        if (!empty($this->options[$optKey])) $metaOptions[$optKey] = $this->options[$optKey] ;
        else $metaOptions[$optKey] = '' ;
        $customStyle = get_post_custom_values($metaKey, $post->ID, true);
        if (is_array($customStyle))
          $metaStyle = strtolower($customStyle[0]) ;
        else
          $metaStyle = strtolower($customStyle) ;
        $style = '' ;
        if ($metaStyle == 'left')
          $style = 'float:left;display:block;' ;
        else if ($metaStyle == 'right')
          $style = 'float:right;display:block;' ;
        else if ($metaStyle == 'center')
          $style = 'text-align:center;display:block;' ;
        else $style = $metaStyle ;
        if (!empty($style)) $metaOptions[$optKey] = $style ;
      }
      return $metaOptions ;
    }

    function ezAdSense_content($content) {
      if (!in_the_loop()) return $content ;
      if (!$this->options['allow_feeds'] && is_feed()) return $content ;
      if ($this->options['kill_pages'] && is_page()) return $content ;
      if ($this->options['kill_attach'] && is_attachment()) return $content ;
      if ($this->options['kill_home'] && is_home()) return $content ;
      if ($this->options['kill_front'] && is_front_page()) return $content ;
      if ($this->options['kill_cat'] && is_category()) return $content ;
      if ($this->options['kill_tag'] && is_tag()) return $content ;
      if ($this->options['kill_archive'] && is_archive()) return $content ;
      if ($this->options['kill_single'] && is_single()) return $content ;
      if ($this->options['kill_search'] && is_search()) return $content ;
      if ($this->options['kill_sticky'] && is_sticky()) return $content ;
      $this->ezMax = $this->options['max_count'] ;
      if ($this->options['force_widget']) $this->ezMax-- ;
      $this->urMax = $this->options['max_link'] ;
      if ($this->ezCount >= $this->ezMax) return $content ;
      if(strpos($content, "<!--noadsense-->") !== false) return $content;
      $metaOptions = $this->getMetaOptions() ;
      if (isset($metaOptions['adsense']) && $metaOptions['adsense'] == 'no')
        return $content;
      $this->handleDefaults() ;
      $this->options['info'] = $this->info() ;

      if ($this->options['kill_linebreaks']) $linebreak = "" ;
      else  $linebreak = "\n" ;

      $wc = str_word_count($content) ;
      $unreal = '' ;
      if ((is_single() || is_page()) && $this->urCount < $this->urMax)
        $unreal = '<div align="center"><font size="-3">' .
          '<a href="http://www.thulasidas.com/adsense/" ' .
          'target="_blank" title="The simplest way to put AdSense to work for you!"> ' .
          'Easy AdSense</a> by <a href="http://www.Thulasidas.com/" ' .
          'target="_blank" title="Unreal Blog proudly brings you Easy AdSense">' .
          'Unreal</a></font></div>';

      $border = '' ;
      if ($this->options['show_borders']) {
        $border='border:#' . $this->options['border_normal'] .
                ' solid ' . $this->options['border_width'] . 'px;" ' .
                ' onmouseover="this.style.border=\'#' . $this->options['border_color'] .
                ' solid ' . $this->options['border_width'] . 'px\'" ' .
                'onmouseout="this.style.border=\'#' . $this->options['border_normal'] .
                ' solid ' . $this->options['border_width'] . 'px\'"' ;
        // $border="border:#{$this->options['border_normal']} solid {$this->options['border_width']}px;\" onmouseover=\"this.style.border='#{$this->options['border_color']} solid {$this->options['border_width']}px'\" onmouseout=\"this.style.border='#{$this->options['border_normal']} solid {$this->options['border_width']}px'" ;
        }
      $show_leadin = $metaOptions['show_leadin'] ;
      $leadin = '' ;
      if ($show_leadin != 'no' && $wc > $this->options['wc_leadin']) {
        if ($this->ezCount < $this->ezMax) {
          $this->ezCount++;
          $margin =  $this->options['margin_leadin'] ;
          if ($this->options['kill_inline'])
            $inline = '' ;
          else
            $inline = 'style="' . $show_leadin .
              ';margin:' . $margin . 'px;' . $border. '"' ;
          $leadin =
            stripslashes($this->options['info'] .
              "$linebreak<!-- Post[count: " . $this->ezCount . "] -->$linebreak" .
              '<div class="ezAdsense adsense adsense-leadin" ' . $inline . '>' .
              $this->options['text_leadin'] .
              ($this->urCount++ < $this->urMax ? $unreal : '') .
              "</div>$linebreak" . $this->options['info'] . "$linebreak") ;
        }
      }

      $show_midtext = $metaOptions['show_midtext'] ;
      if ($show_midtext != 'no' && $wc > $this->options['wc_midtext']) {
        if ($this->ezCount < $this->ezMax) {
          $poses = array();
          $lastpos = -1;
          $repchar = "<p";
          if(strpos($content, "<p") === false)
            $repchar = "<br";

          while(strpos($content, $repchar, $lastpos+1) !== false){
            $lastpos = strpos($content, $repchar, $lastpos+1);
            $poses[] = $lastpos;
          }
          $half = sizeof($poses);
          while(sizeof($poses) > $half)
            array_pop($poses);
          $pickme = 0 ;
          if (!empty($poses)) $pickme = $poses[floor(sizeof($poses)/2)];
          if ($this->options['force_midad'] || $half > 10) {
            $this->ezCount++;
            $margin =  $this->options['margin_midtext'] ;
            if ($this->options['kill_inline'])
              $inline = '' ;
            else
              $inline = 'style="' . $show_midtext .
                ';margin:' . $margin . 'px;' . $border. '"' ;
            $midtext =
              stripslashes($this->options['info'] .
                "$linebreak<!-- Post[count: " . $this->ezCount . "] -->$linebreak" .
                '<div class="ezAdsense adsense adsense-midtext" ' . $inline . '>' .
                $this->options['text_midtext'] .
                ($this->urCount++ < $this->urMax ? $unreal : '') .
                "</div>$linebreak" . $this->options['info'] . "$linebreak") ;
            $content = substr_replace($content, $midtext.$repchar, $pickme, 2);
          }
        }
      }

      $show_leadout = $metaOptions['show_leadout'] ;
      $leadout = '' ;
      if ($show_leadout != 'no' && $wc > $this->options['wc_leadout']) {
        if ($this->ezCount < $this->ezMax) {
          $this->ezCount++;
          $margin =  $this->options['margin_leadout'] ;
          if ($this->options['kill_inline'])
            $inline = '' ;
          else
            $inline = 'style="' . $show_leadout .
              ';margin:' . $margin . 'px;' . $border. '"' ;
          $leadout =
            stripslashes($this->options['info'] .
              "$linebreak<!-- Post[count: " . $this->ezCount . "] -->$linebreak" .
              '<div class="ezAdsense adsense adsense-leadout" ' . $inline . '>' .
              $this->options['text_leadout'] .
              ($this->urCount++ < $this->urMax ? $unreal : '') .
              "</div>$linebreak" . $this->options['info'] . "$linebreak") ;
        }
      }
      if ($this->options['header_leadin']) {
        $this->leadin = $leadin  ;
        $leadin = '' ;
      }
      if ($this->options['footer_leadout']) {
        $this->leadout =  $leadout ;
        $leadout = '' ;
      }
      return $leadin . $content . $leadout ;
    }

    function footer_action(){
      $unreal = '<div align="center"><font size="-3">' .
        '<a href="http://thulasidas.com/adsense" ' .
        'target="_blank" title="The simplest way to put AdSense to work for you!"> ' .
        'Easy AdSense</a> by <a href="http://www.Thulasidas.com/" ' .
        'target="_blank" title="Unreal Blog proudly brings you Easy AdSense">' .
        'Unreal</a></font></div>';
      echo $unreal ;
    }

    function header_leadin() {
      if (is_admin()) return ;
      // is_feed() is not ready, because the WP query hasn't been run yet.
      if (strpos($_SERVER['REQUEST_URI'], 'feed') !== false) return ;
      // This is sad: Need to pre-construct $this->leadin
      $unreal = '' ;
      $border = '' ;
      if ($this->options['show_borders'])
        $border='border:#' . $this->options['border_normal'] .
          ' solid ' . $this->options['border_width'] . 'px;" ' .
          ' onmouseover="this.style.border=\'#' . $this->options['border_color'] .
          ' solid ' . $this->options['border_width'] . 'px\'" ' .
          'onmouseout="this.style.border=\'#' . $this->options['border_normal'] .
          ' solid ' . $this->options['border_width'] . 'px\'"' ;
      $show_leadin = $this->options['show_leadin'] ;

      if ($this->options['kill_linebreaks']) $linebreak = "" ;
      else  $linebreak = "\n" ;

      if ($show_leadin != 'no') {
        $margin =  $this->options['margin_leadin'] ;
        if ($this->options['kill_inline'])
          $inline = '' ;
        else
          $inline = 'style="' . $show_leadin .
            ';margin:' . $margin . 'px;' . $border. '"' ;
        $this->ezCount++ ;
        $this->leadin =
          stripslashes($this->options['info'] .
            "$linebreak<!-- Post[count: " . $this->ezCount . "] -->$linebreak" .
            '<div class="ezAdsense adsense adsense-leadin" ' . $inline . '>' .
            $this->options['text_leadin'] .
            ($this->urCount++ < $this->urMax ? $unreal : '') .
            "</div>$linebreak" . $this->options['info'] . "$linebreak") ;
        echo $this->leadin ;
      }
    }

    function footer_leadout(){
      if (is_admin()) return ;
      echo $this->leadout ;
    }

    // ===== widget functions =====
    function widget_ezAd_ads($args) {
      extract($args);
      $this->options['text_widget'] =
        $this->handleDefaultText($this->options['text_widget'], '160x600') ;
      $this->options['info'] = $this->info() ;
      $metaOptions = $this->getMetaOptions() ;
      if (isset($metaOptions['adsense']) && $metaOptions['adsense'] == 'no') return ;
      $show_widget = $metaOptions['show_widget'] ;
      if ($show_widget == 'no') return ;
      $this->ezMax = $this->options['max_count'] ;
      $this->urMax = $this->options['max_link'] ;
      if (!$this->options['force_widget']) {
        if ($this->ezCount >= $this->ezMax) return ;
        $this->ezCount++;
      }

      if ($this->options['kill_linebreaks']) $linebreak = "" ;
      else  $linebreak = "\n" ;

      $title = empty($this->options['title_widget']) ?
        __('Sponsored Links', 'easy-adsenser') :
        stripslashes(htmlspecialchars($this->options['title_widget'])) ;
      $border = '' ;
      if ($this->options['show_borders'] && $this->options['border_widget'] )
        $border='border:#' . $this->options['border_normal'] .
          ' solid ' . $this->options['border_width'] . 'px ;"' .
          ' onmouseover="this.style.border=\'#' . $this->options['border_color'] .
          ' solid ' . $this->options['border_width'] . 'px\'" ' .
          'onmouseout="this.style.border=\'#' . $this->options['border_normal'] .
          ' solid ' . $this->options['border_width'] . 'px\'"' ;
      $unreal = '<div align="center"><font size="-3">' .
        '<a href="http://thulasidas.com/adsense" ' .
        'target="_blank" title="The simplest way to put AdSense to work for you!"> ' .
        'Easy AdSense</a> by <a href="http://www.Thulasidas.com/" ' .
        'target="_blank" title="Unreal Blog proudly brings you Easy AdSense">' .
        'Unreal</a></font></div>';
      echo $before_widget;
      if (!$this->options['kill_widget_title'])
        echo $before_title . $title . $after_title;
      $margin =  $this->options['margin_widget'] ;
      if ($this->options['kill_inline'])
        $inline = '' ;
      else
        $inline = 'style="' . $show_widget .
          ';margin:' . $margin . 'px;' . $border. '"' ;
      echo stripslashes($this->options['info'] .
        "$linebreak<!-- Widg[count: " . $this->ezCount . "] -->$linebreak" .
        '<div class="ezAdsense adsense adsense-widget"><div ' . $inline. '>' .
        $this->options['text_widget'] .
        ($this->urCount++ < $this->urMax ? $unreal : '') .
        "</div></div>$linebreak" . $this->options['info'] . "$linebreak") ;
      echo $after_widget;
    }

    function widget_ezAd_lu($args) {
      extract($args);
      $this->options['text_lu'] =
        $this->handleDefaultText($this->options['text_lu'], '160x160') ;
      $title = empty($this->options['title_lu']) ? '' :
        $before_title . stripslashes(htmlspecialchars($this->options['title_lu'])) . $after_title ;
      $metaOptions = $this->getMetaOptions() ;
      if (isset($metaOptions['adsense']) && $metaOptions['adsense'] == 'no') return ;
      $show_lu = $metaOptions['show_lu'] ;

      if ($this->options['kill_linebreaks']) $linebreak = "" ;
      else  $linebreak = "\n" ;

      $border = '' ;
      if ($this->options['show_borders'] && $this->options['border_lu'] )
        $border='border:#' . $this->options['border_normal'] .
          ' solid ' . $this->options['border_width'] . 'px;" ' .
          ' onmouseover="this.style.border=\'#' . $this->options['border_color'] .
          ' solid ' . $this->options['border_width'] . 'px\'" ' .
          'onmouseout="this.style.border=\'#' . $this->options['border_normal'] .
          ' solid ' . $this->options['border_width'] . 'px\'"' ;
      if ($show_lu != 'no') {
        echo $before_widget ;
        if (!$this->options['kill_widget_title']) echo $title ;
        $margin =  $this->options['margin_lu'] ;
        if ($this->options['kill_inline'])
          $inline = '' ;
        else
          $inline = 'style="' . $show_widget .
            ';margin:' . $margin . 'px;' . $border. '"' ;
        echo stripslashes('<div class="ezAdsense adsense adsense-lu"><div ' .
          $inline. '>' . "$linebreak" .
          $this->options['text_lu'] . "$linebreak" .
          '</div></div>') ;
        echo $after_widget ;
      }
    }

    function widget_ezAd_search($args) {
      extract($args);
      $this->options['text_gsearch'] =
        $this->handleDefaultText($this->options['text_gsearch'], '160x160') ;
      $metaOptions = $this->getMetaOptions() ;
      if (isset($metaOptions['adsense']) && $metaOptions['adsense'] == 'no') return ;
      $title_gsearch = $metaOptions['title_gsearch'] ;

      if ($this->options['kill_linebreaks']) $linebreak = "" ;
      else  $linebreak = "\n" ;

      if ($title_gsearch != 'no') {
        $title = $before_title . $title_gsearch . $after_title ;
        if ($title_gsearch == 'dark')
          $title = '<img src=" ' . $this->plugindir . '/google-dark.gif" ' .
            ' border="0" alt="[Google]" align="middle" />' ;
        if ($title_gsearch == 'light')
          $title = '<img src=" ' . $this->plugindir . '/google-light.gif" ' .
            ' border="0" alt="[Google]" align="middle" />' ;
        echo $before_widget ;
        if (!$this->options['kill_gsearch_title']) echo $title ;
        $margin =  $this->options['margin_gsearch'] ;
        if ($this->options['kill_inline'])
          $inline = '' ;
        else
          $inline = 'style="margin:' . $margin . 'px; "' ;
        echo stripslashes('<div class="ezAdsense adsense adsense-search"><div ' .
          $inline . '>' . "$linebreak" .
          $this->options['text_gsearch'] . "$linebreak" .
          '</div></div>') ;
        echo $after_widget ;
      }
    }

    function widget_ezAd_control() {
      echo '<p>Configure it at <br />' ;
      echo '<a href="options-general.php?page=easy-adsense-lite.php"> ';
      echo 'Settings &rarr; Easy AdSense</a>' ;
      echo '</p>' ;
    }

    function widget_ezAd_lu_control($widget_args = 1) {
      echo '<p>Configure it at <br />' ;
      echo '<a href="options-general.php?page=easy-adsense-lite.php"> ';
      echo 'Settings &rarr; Easy AdSense</a>' ;
      echo '</p>' ;
    }
  }
} //End Class ezAdSense

if (class_exists("EzAdSense")) {
  $ezAdSense = new EzAdSense();
  if (isset($ezAdSense) && !empty($ezAdSense->defaults)) {
    if (!function_exists("ezAdSense_ap")) {
      function ezAdSense_ap() {
        global $ezAdSense ;
        if (function_exists('add_options_page')) {
          add_options_page('Easy AdSense', 'Easy AdSense', 'activate_plugins',
            basename(__FILE__), array($ezAdSense, 'printAdminPage'));
        }
      }
    }

    // sidebar AdSense Widget (skyscraper)
    class ezAdsWidget extends WP_Widget {
      function ezAdsWidget() {
        $widget_ops =
          array('classname' => 'ezAdsWidget',
            'description' =>
            __('Show a Google AdSense block in your sidebar as a widget',
              'easy-adsenser') );
        $this->WP_Widget('ezAdsWidget', 'Easy AdSense: Google Ads',
          $widget_ops);
      }
      function widget($args, $instance) {
        // outputs the content of the widget
        global $ezAdSense ;
        $ezAdSense->widget_ezAd_ads($args) ;
      }

      function update($new_instance, $old_instance) {
        // processes widget options to be saved
        return $new_instance ;
      }

      function form($instance) {
        // outputs the options form on admin
        global $ezAdSense ;
        $ezAdSense->widget_ezAd_control() ;
      }
    }
    add_action('widgets_init',
      create_function('', 'return register_widget("ezAdsWidget");'));

    // sidebar Search Widget
    class ezAdsSearch extends WP_Widget {
      function ezAdsSearch() {
        $widget_ops =
          array('classname' => 'ezAdsSearch',
            'description' =>
            __('Show a Google Search Box in your sidebar as a widget',
              'easy-adsenser') );
        $this->WP_Widget('ezAdsSearch', 'Easy AdSense: Google Search',
          $widget_ops);
      }
      function widget($args, $instance) {
        // outputs the content of the widget
        global $ezAdSense ;
        $ezAdSense->widget_ezAd_search($args) ;
      }

      function update($new_instance, $old_instance) {
        // processes widget options to be saved
        return $new_instance ;
      }

      function form($instance) {
        // outputs the options form on admin
        global $ezAdSense ;
        $ezAdSense->widget_ezAd_control() ;
      }
    }
    add_action('widgets_init',
      create_function('', 'return register_widget("ezAdsSearch");'));

    // sidebar Link Units
    class ezAdsLU extends WP_Widget {
      function ezAdsLU() {
        $widget_ops =
          array('classname' => 'ezAdsLU',
            'description' =>
            __('Show a Google Links Unit in your sidebar as a widget',
              'easy-adsenser') );
        $this->WP_Widget('ezAdsLU', 'Easy AdSense: Google Link Unit',
          $widget_ops);
      }
      function widget($args, $instance) {
        // outputs the content of the widget
        global $ezAdSense ;
        $ezAdSense->widget_ezAd_lu($args) ;
      }

      function update($new_instance, $old_instance) {
        // processes widget options to be saved
        return $new_instance ;
      }

      function form($instance) {
        // outputs the options form on admin
        global $ezAdSense ;
        $ezAdSense->widget_ezAd_control() ;
      }
    }
    add_action('widgets_init',
      create_function('', 'return register_widget("ezAdsLU");'));

    add_filter('the_content', array($ezAdSense, 'ezAdSense_content'));
    $ezAdSense->luMax = $ezAdSense->options['limit_lu'] ;
    add_action('admin_menu', 'ezAdSense_ap');
    add_action('init', array($ezAdSense, 'session_start')) ;
    add_filter('plugin_action_links', array($ezAdSense, 'plugin_action'), -10, 2);
    if ($ezAdSense->options['max_link'] == -1)
      add_action('wp_footer', array($ezAdSense, 'footer_action'));
    else
      remove_action('wp_footer', array($ezAdSense, 'footer_action'));

    if ($ezAdSense->options['header_leadin'])
      add_action($ezAdSense->options['header_leadin'], array($ezAdSense, 'header_leadin'));

    if ($ezAdSense->options['footer_leadout'])
      add_action($ezAdSense->options['footer_leadout'], array($ezAdSense, 'footer_leadout'));
  }
}
