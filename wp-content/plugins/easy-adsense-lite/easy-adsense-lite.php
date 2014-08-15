<?php

/*
  Plugin Name: Easy AdSense
  Plugin URI: http://www.thulasidas.com/adsense
  Description: Easiest way to show AdSense and make money from your blog. Configure it at <a href="options-general.php?page=easy-adsense-lite.php">Settings &rarr; Easy AdSense</a>.
  Version: 7.31
  Author: Manoj Thulasidas
  Author URI: http://www.thulasidas.com
 */

/*
  Copyright (C) 2008 www.ads-ez.com

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License as
  published by the Free Software Foundation; either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (class_exists("EzAdSensePro")) {
  $plg = "Easy AdSense Lite";
  $lite = plugin_basename(__FILE__);
  include_once('ezDenyLite.php');
  ezDenyLite($plg, $lite);
}

if (!class_exists("EzAdSense")) {

  require_once('EzOptions.php');

  class EzAdSense extends EzBasePlugin {

    var $invite, $locale, $defaults, $helpTags,
            $leadin, $leadout, $options, $optionName, $metaOptions;
    var $ezMax, $urMax, $urCount, $ezCount;
    var $adminMsg, $border;
    var $kills = array('page', 'sticky', 'home', 'front_page', 'category',
        'tag', 'archive', 'search', 'single', 'attachment');
    var $ezOptions = array();

    function EzAdSense() {
      parent::__construct("easy-adsense", "Easy AdSense", __FILE__);
      $this->prefix = 'ezAdSense';
      $this->adminMsg = '';
      $this->defaults = array('defaultText' => 'Please generate and paste your ad code here. If left empty, the ad location will be highlighted on your blog pages with a reminder to enter your code.');
      $defaultOptions = $this->mkDefaultOptions();
      $this->optionName = $this->prefix . get_option('stylesheet');
      $this->options = get_option($this->optionName);
      if (empty($this->options)) {
        $this->options = $defaultOptions;
      }
      else {
        $this->options = array_merge($defaultOptions, $this->options);
      }
      // Counts and limis
      $this->ezMax = $this->options['max_count'];
      $this->urMax = $this->options['max_link'];
      $this->urCount = 0;
      $this->ezCount = 0;
      $this->metaOptions = array();
      $this->border = '';
    }

    static function showUnreal($print = true) {
      $unreal = "<div style='text-align:center;margin-left:auto;margin-right:auto;font-size:0.6em'><a href='http://www.thulasidas.com/adsense/' target='_blank' title='The simplest way to put AdSense to work for you!'> Easy AdSense</a> by <a href='http://www.Thulasidas.com/' target='_blank' title='Unreal Blog proudly brings you Easy AdSense'>Unreal</a></div>";
      if ($print) {
        echo $unreal;
      }
      else {
        return $unreal;
      }
    }

    function mkHelpTags() {
      $this->helpTags = array();
      $o = new EzHelpTag('help0');
      $o->title = __('Click for help', 'easy-adsenser');
      $o->tipTitle = __('How to Set it up', 'easy-adsenser');
      $o->desc = sprintf(__('A few easy steps to setup %s', 'easy-adsenser'), "<em>Easy AdSense</em>");
      $this->helpTags[] = $o;

      $o = new EzHelpTag('help1');
      $o->title = __('Click for help', 'easy-adsenser');
      $o->tipTitle = __('How to Control AdSense on Each Post', 'easy-adsenser');
      $o->desc = __('Need to control ad blocks on each post?', 'easy-adsenser');
      $this->helpTags[] = $o;

      $o = new EzHelpTag('help2');
      $o->title = __('Click for help', 'easy-adsenser');
      $o->tipTitle = __('All-in-One AdSense Control', 'easy-adsenser');
      $o->desc = __('Sidebar Widgets, Link Units or Google Search', 'easy-adsenser');
      $this->helpTags[] = $o;

      $o = new EzHelpPopUp('http://wordpress.org/extend/plugins/easy-adsense-lite/');
      $o->title = __('Click for help', 'easy-adsenser');
      $o->desc = __('Check out the FAQ and rate this plugin.', 'easy-adsenser');
      $this->helpTags[] = $o;
    }

    function mkEzOptions() {
      if (!empty($this->ezOptions)) {
        return;
      }

      parent::mkEzOptions();

      $o = new EzTextArea('text_leadin');
      $o->before = "<b>" . __('Lead-in AdSense Text', 'easy-adsenser') .
              "</b>&nbsp;";
      $o->desc = __('(Appears near the beginning of the post)', 'easy-adsenser')
              . '<br />';
      $o->style = "width: 95%; height: 130px;";
      $o->width = "50";
      $o->height = "15";
      $o->after = "<br />";
      $this->ezOptions['text_leadin'] = clone $o;
      $o->name = 'text_midtext';
      $o->before = "<b>" . __('Mid-Post AdSense Text', 'easy-adsenser') .
              "</b>&nbsp;";
      $o->desc = __('(Appears near the middle of the post)', 'easy-adsenser')
              . '<br />';
      $this->ezOptions['text_midtext'] = clone $o;
      $o->name = 'text_leadout';
      $o->before = "<b>" . __('Post Lead-out AdSense Text', 'easy-adsenser') .
              "</b>&nbsp;";
      $o->desc = __('(Appears near the end of the post)', 'easy-adsenser')
              . '<br />';
      $this->ezOptions['text_leadout'] = clone $o;
      $o->name = 'text_widget';
      $o->before = "<b>" . __('AdSense Widget Text', 'easy-adsenser') .
              "</b>&nbsp;";
      $o->desc = __('(Appears in the Sidebar as a Widget)', 'easy-adsenser')
              . '<br />';
      $o->style = "width: 95%; height: 110px;";
      $this->ezOptions['text_widget'] = clone $o;
      $o->name = 'text_lu';
      $o->before = "<b>" . __('AdSense Link-Units Text', 'easy-adsenser') .
              "</b>&nbsp;";
      $o->desc = __('(Appears in the Sidebar as a Widget)', 'easy-adsenser')
              . '<br />';
      $this->ezOptions['text_lu'] = clone $o;
      $o->name = 'text_gsearch';
      $o->before = "<b>" . __('Google Search Widget', 'easy-adsenser') .
              "</b>&nbsp;";
      $o->desc = __('(Adds a Google Search Box to your sidebar)', 'easy-adsenser')
              . '<br />';
      $this->ezOptions['text_gsearch'] = clone $o;

      $o = new EzText('wc_leadin');
      $o->title = __('Suppress this ad block if the post is not at least this many words long. Enter 0 or a small number if you do not want to suppress ads based on the number of words in the page/post.', 'easy-adsenser');
      $o->desc = __('Min. Word Count', 'easy-adsenser') . ':';
      $o->style = "width:40px;text-align:center;";
      $this->ezOptions['wc_leadin'] = clone $o;
      $o->name = 'wc_midtext';
      $this->ezOptions['wc_midtext'] = clone $o;
      $o->name = 'wc_leadout';
      $this->ezOptions['wc_leadout'] = clone $o;

      $o = new EzText('margin_leadin');
      $o->title = __('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser');
      $o->desc = __('Margin:', 'easy-adsenser');
      $o->style = "width:30px;text-align:center;";
      $o->after = ' px<br />';
      $this->ezOptions['margin_leadin'] = clone $o;
      $o->name = 'margin_midtext';
      $this->ezOptions['margin_midtext'] = clone $o;
      $o->name = 'margin_leadout';
      $this->ezOptions['margin_leadout'] = clone $o;
      $o->name = 'margin_widget';
      $this->ezOptions['margin_widget'] = clone $o;
      $o->name = 'margin_lu';
      $this->ezOptions['margin_lu'] = clone $o;
      $o->name = 'margin_gsearch';
      $this->ezOptions['margin_gsearch'] = clone $o;

      $o = new EzCheckBox('force_midad');
      $o->title = __('Force mid-text ad (if enabled) even in short posts.', 'easy-adsenser');
      $o->desc = __('Force Mid-post Ad', 'easy-adsenser');
      $o->labelWidth = "43%";
      $this->ezOptions['force_midad'] = clone $o;

      $o = new EzSelect('header_leadin');
      $o->title = __('Select where you would like to show the lead-in ad block. A placement above or below the blog header would be suitable for a wide AdSense block.', 'easy-adsenser') . "<br />" . __('Note that <b>Below Header</b> and <b>End of Page</b> options are hacks that may not be compatible with the WordPress default widget for <b>Recent Posts</b> or anything else that may use DB queries or loops. If you have problems with your sidebars and/or font sizes, please choose some other <b>Postion</b> option.', 'easy-adsenser');
      $o->desc = __('Position:', 'easy-adsenser');
      $o->style = "width:30%;";
      $o->addChoice('send_headers', 'send_headers', __('Above Header', 'easy-adsenser'));
      $o->addChoice('loop_start', 'loop_start', __('Above Post Title', 'easy-adsenser'));
      $o->addChoice('the_content', 'the_content', __('Below Header', 'easy-adsenser'));
      $o->addChoice('default', '', __('Beginning of Post', 'easy-adsenser'));
      $this->ezOptions['header_leadin'] = clone $o;

      $o = new EzSelect('show_leadin');
      $o->title = __('Decide whether to show this AdSense block, and specify how to align it.', 'easy-adsenser');
      $o->desc = __('Show:', 'easy-adsenser');
      $o->style = "width:38%;";
      $o->addChoice('no', 'no', __('Suppress Lead-in Ad', 'easy-adsenser'));
      $o->addChoice('float:left', 'float:left', __('Align Left', 'easy-adsenser') . ', ' .
              __('Text-wrapped', 'easy-adsenser'));
      $o->addChoice('text-align:left', 'text-align:left', __('Align Left', 'easy-adsenser') . ', ' .
              __('No wrap', 'easy-adsenser'));
      $o->addChoice('text-align:center', 'text-align:center', __('Center', 'easy-adsenser'));
      $o->addChoice('float:right', 'float:right', __('Align Right', 'easy-adsenser') . ', ' .
              __('Text-wrapped', 'easy-adsenser'));
      $o->addChoice('text-align:right', 'text-align:right', __('Align Right', 'easy-adsenser') . ', ' .
              __('No wrap', 'easy-adsenser'));
      $this->ezOptions['show_leadin'] = clone $o;

      $o->name = 'show_midtext';
      $o->addChoice('no', 'no', __('Suppress Mid-post Ad', 'easy-adsenser'));
      $choice = array_pop($o->choices);
      array_shift($o->choices);
      array_unshift($o->choices, $choice);
      $this->ezOptions['show_midtext'] = clone $o;

      $o->name = 'show_leadout';
      $o->addChoice('no', 'no', __('Suppress Lead-out Ad', 'easy-adsenser'));
      $choice = array_pop($o->choices);
      array_shift($o->choices);
      array_unshift($o->choices, $choice);
      $this->ezOptions['show_leadout'] = clone $o;

      $o = new EzSelect('footer_leadout');
      $o->title = __('Select where you would like to show the lead-out ad block. A placement above or below the blog footer would be suitable for a wide AdSense block.', 'easy-adsenser') . __('<br />Note that <b>Below Header</b> and <b>End of Page</b> options are hacks that may not be compatible with the WordPress default widget for <b>Recent Posts</b> or anything else that may use DB queries or loops. If you have problems with your sidebars and/or font sizes, please choose some other <b>Position</b> option.' . 'easy-adsenser');
      $o->desc = __('Position:', 'easy-adsenser');
      $o->style = "width:30%;";
      $o->addChoice('default', '', __('End of Post', 'easy-adsenser'));
      $o->addChoice('loop_end', 'loop_end', __('End of Page', 'easy-adsenser'));
      $o->addChoice('get_footer', 'get_footer', __('Above Footer', 'easy-adsenser'));
      $o->addChoice('wp_footer', 'wp_footer', __('Below Footer', 'easy-adsenser'));
      $this->ezOptions['footer_leadout'] = clone $o;

      $o = new EzRadioBox('max_count');
      $o->desc = "<b>" . __('Option on Google Policy', 'easy-adsenser') . "</b>";
      $o->title = __('Google policy allows no more than three ad blocks and three link units per page', 'easy-adsenser');
      $o->addChoice('3', '3', __('Three ad blocks (including the side bar widget, if enabled).', 'easy-adsenser'))->after = "<br />";
      $o->addChoice('2', '2', __('Two ad blocks.', 'easy-adsenser'));
      $o->addChoice('1', '1', __('One ad block.', 'easy-adsenser'));
      $o->addChoice('0', '0', __('No ad blocks in posts.', 'easy-adsenser'))->after = "<br />";
      $o->addChoice('99', '99', __('Any number of ad blocks (At your own risk!)', 'easy-adsenser'));
      $o->after = "<br />";
      $this->ezOptions['max_count'] = clone $o;

      $o = new EzCheckBox('kill_page');
      $o->title = __('Do not show ads on pages. Ad will appear on posts. Please see the differece at http://support.wordpress.com/post-vs-page/', 'easy-adsenser');
      $o->desc = __('Pages (Ads only on Posts)', 'easy-adsenser');
      $o->before = "&nbsp;";
      $o->after = "<br />";
      $this->ezOptions['kill_page'] = clone $o;

      $o = new EzCheckBox('kill_sticky');
      $o->title = __('Suppress ads on sticky front page. Sticky front page is a post used as the front page of the blog.', 'easy-adsenser');
      $o->desc = __('Sticky Front Page', 'easy-adsenser');
      $o->labelWidth = "35%";
      $this->ezOptions['kill_sticky'] = clone $o;

      $o = new EzCheckBox('kill_home');
      $o->title = __('Home Page and Front Page are the same for most blogs', 'easy-adsenser');
      $o->desc = __('Home Page', 'easy-adsenser');
      $o->labelWidth = "25%";
      $this->ezOptions['kill_home'] = clone $o;

      $o = new EzCheckBox('kill_front_page');
      $o->title = __('Home Page and Front Page are the same for most blogs', 'easy-adsenser');
      $o->desc = __('Front Page', 'easy-adsenser');
      $o->labelWidth = "30%";
      $o->after = "<br />";
      $this->ezOptions['kill_front_page'] = clone $o;

      $o = new EzCheckBox('kill_category');
      $o->title = __('Pages that come up when you click on category names', 'easy-adsenser');
      $o->desc = __('Category Pages', 'easy-adsenser');
      $o->labelWidth = "35%";
      $this->ezOptions['kill_category'] = clone $o;

      $o = new EzCheckBox('kill_tag');
      $o->title = __('Pages that come up when you click on tag names', 'easy-adsenser');
      $o->desc = __('Tag Pages', 'easy-adsenser');
      $o->labelWidth = "25%";
      $this->ezOptions['kill_tag'] = clone $o;

      $o = new EzCheckBox('kill_archive');
      $o->title = __('Pages that come up when you click on year/month archives', 'easy-adsenser');
      $o->desc = __('Archive Pages', 'easy-adsenser');
      $o->labelWidth = "30%";
      $o->after = "<br />";
      $this->ezOptions['kill_archive'] = clone $o;

      $o = new EzCheckBox('kill_search');
      $o->title = __('Pages showing search results', 'easy-adsenser');
      $o->desc = __('Search Results', 'easy-adsenser');
      $o->labelWidth = "35%";
      $this->ezOptions['kill_search'] = clone $o;

      $o = new EzCheckBox('kill_single');
      $o->title = __('Posts (ads will be shown only on other kind of pages as specified in these checkboxes)', 'easy-adsenser');
      $o->desc = __('Single Posts', 'easy-adsenser');
      $o->labelWidth = "25%";
      $this->ezOptions['kill_single'] = clone $o;

      $o = new EzCheckBox('kill_attachment');
      $o->title = __('Pages that show attachments', 'easy-adsenser');
      $o->desc = __('Attachment Page', 'easy-adsenser');
      $o->labelWidth = "30%";
      $o->after = "<br />";
      $this->ezOptions['kill_attachment'] = clone $o;

      $o = new EzCheckBox('force_widget');
      $o->title = __('If the sidebar widget is enabled, it will be displayed in preference to the ad blocks in the text. If this option is not checked, it may happen that the number of ad blocks you selected above may get used up by the ones in the post body.', 'easy-adsenser');
      $o->desc = __('Prioritize sidebar widget. (Always shows the widget, if enabled.)', 'easy-adsenser');
      $o->after = "<br />";
      $this->ezOptions['force_widget'] = clone $o;

      $o = new EzCheckBox('show_borders');
      $o->title = __('Google Policy says that you may not direct user attention to the ads via arrows or other graphical gimmicks. Please convince yourself that showing a mouseover decoration does not violate this Google statement before enabling this option.', 'easy-adsenser');
      $o->desc = __('Show a border around the ads?', 'easy-adsenser');
      $o->after = "&nbsp;";
      $this->ezOptions['show_borders'] = clone $o;

      $o = new EzCheckBox('border_widget');
      $o->title = __('Show the same border on the sidebar widget as well?', 'easy-adsenser');
      $o->desc = __('Widget?', 'easy-adsenser');
      $o->before = "&nbsp;";
      $o->after = "&nbsp;";
      $this->ezOptions['border_widget'] = clone $o;

      $o = new EzCheckBox('border_lu');
      $o->title = __('Show the same border on the link units too?', 'easy-adsenser');
      $o->desc = __('Link Units?', 'easy-adsenser');
      $o->before = "&nbsp;";
      $o->after = "<br />";
      $this->ezOptions['border_lu'] = clone $o;

      $o = new EzText('border_width');
      $o->title = __('Specify the border width.', 'easy-adsenser');
      $o->desc = __('Width', 'easy-adsenser') . ':&nbsp;';
      $o->style = "width:25px;text-align:center;";
      $this->ezOptions['border_width'] = clone $o;

      $o = new EzText('border_normal');
      $o->title = __('Specify the border colors.', 'easy-adsenser');
      $o->desc = __('Colors:&nbsp; Normal', 'easy-adsenser') . ':#';
      $o->style = "width:65px;text-align:center;";
      $o->after = "&nbsp;";
      $this->ezOptions['border_normal'] = clone $o;

      $o = new EzText('border_color');
      $o->title = __('Specify the border colors.', 'easy-adsenser');
      $o->desc = __('Hover', 'easy-adsenser') . ':#';
      $o->style = "width:65px;text-align:center;";
      $o->after = "<br />";
      $this->ezOptions['border_color'] = clone $o;

      $o = new EzCheckBox('kill_inline');
      $o->title = __('All <code>&lt;div&gt;</code>s that <em>Easy AdSense</em> creates have the class attribute <code>adsense</code>. Furthermore, they have class attributes like <code>adsense-leadin</code>, <code>adsense-midtext</code>, <code>adsense-leadout</code>, <code>adsense-widget</code> and <code>adsense-lu</code> depending on the type. You can set the style for these classes in your theme <code>style.css</code> to control their appearance.<br />If this is all Greek to you, please leave the option unchecked.', 'easy-adsenser');
      $o->desc = __('Suppress in-line styles (Control ad-blocks using style.css)', 'easy-adsenser');
      $o->after = "<br />";
      $o->tipWidth = 350;
      $this->ezOptions['kill_inline'] = clone $o;

      $o = new EzCheckBox('kill_linebreaks');
      $o->title = __('If you find that you have extra vertical spaces or if your ad code is messed up with <code><</code><code>p></code> or <code><</code><code>br /></code> tags, try checking this option.<br />Under normal cirumstances, this option should be left unchecked.', 'easy-adsenser');
      $o->desc = __('Prevent spurious line breaks', 'easy-adsenser');
      $o->after = "<br />";
      $this->ezOptions['kill_linebreaks'] = clone $o;

      $o = new EzRadioBox('show_widget');
      $o->title = __('Decide where (or whether) to show this widget and how to align it.', 'easy-adsenser');
      $o->addChoice('text-align:left', 'text-align:left', __('Align Left', 'easy-adsenser'));
      $o->addChoice('text-align:center', 'text-align:center', __('Center', 'easy-adsenser'));
      $o->addChoice('text-align:right', 'text-align:right', __('Align Right', 'easy-adsenser'));
      $o->addChoice('no', 'no', __('Suppress Widget', 'easy-adsenser'));
      $o->after = "<br />";
      $this->ezOptions['show_widget'] = clone $o;

      $o = new EzRadioBox('show_lu');
      $o->title = __('Decide where (or whether) to show this widget and how to align it.', 'easy-adsenser');
      $o->addChoice('text-align:left', 'text-align:left', __('Align Left', 'easy-adsenser'));
      $o->addChoice('text-align:center', 'text-align:center', __('Center', 'easy-adsenser'));
      $o->addChoice('text-align:right', 'text-align:right', __('Align Right', 'easy-adsenser'));
      $o->addChoice('no', 'no', __('Suppress Widget', 'easy-adsenser'));
      $o->after = "<br />";
      $this->ezOptions['show_lu'] = clone $o;

      $o = new EzText('title_widget');
      $o->title = __('Give a title to your widget -- something like Sponsored Links or Advertisements would be good. You can also suppress the title by checking the box to the right.', 'easy-adsenser');
      $o->desc = __('Widget Title:', 'easy-adsenser') . "&nbsp;";
      $o->style = "width:220px";
      $o->after = "&nbsp;";
      $this->ezOptions['title_widget'] = clone $o;
      $o->name = 'title_lu';
      $this->ezOptions['title_lu'] = clone $o;

      $o = new EzCheckBox('kill_widget_title');
      $o->title = __('Check this box to suppress the title for this widget.', 'easy-adsenser');
      $o->desc = __('Hide Title', 'easy-adsenser');
      $o->after = "<br />";
      $this->ezOptions['kill_widget_title'] = clone $o;
      $o->name = 'kill_lu_title';
      $this->ezOptions['kill_lu_title'] = clone $o;
      $o->name = 'kill_gsearch_title';
      $this->ezOptions['kill_gsearch_title'] = clone $o;

      $o = new EzRadioBox('title_gsearch');
      $o->title = __('Choose a title for your Google Search Widget. Depending on your theme background, you can choose a dark or light image, or a custom text title. You can also suppress the widget altogether.', 'easy-adsenser');
      $o->addChoice('dark', 'dark', "<img src='{$this->plgURL}/google-dark.gif' alt='Google (dark)' style='background:black;vertical-align:-40%;' />")->after = "&nbsp;";
      $o->addChoice('light', 'light', "<img src='{$this->plgURL}/google-light.gif' alt='Google (light)' style='background:white;vertical-align:-40%;' />")->after = "&nbsp;";
      $o->addChoice('no', 'no', __('Suppress Search Box', 'easy-adsenser'))->after = "<br />";
      $o->addChoice('customized', 'customized', __('Custom Title:', 'easy-adsenser'));
      $this->ezOptions['title_gsearch'] = clone $o;

      $o = new EzText('title_gsearch_custom');
      $o->title = __('Enter a custom title for your Google Search Widget. Remember to include styling tags (such as <code>&lt;h3&gt;</code> etc.) as needed.', 'easy-adsenser');
      $this->ezOptions['title_gsearch_custom'] = clone $o;

      $o = new EzRadioBox('max_link');
      $o->before = "<b>" . __('Link-backs to', 'easy-adsenser') . " <a href='http://www.Thulasidas.com' target='_blank'>Unreal Blog</a></b>";
      $o->desc = __('(Consider showing at least one link.)', 'easy-adsenser') . "<br />";
      $o->title = __('If you would like to show a discreet link to the developer site, customize it here.', 'easy-adsenser');
      $o->addChoice('99', '99', __('Show a link under every ad block.', 'easy-adsenser'))->after = "<br />";
      $o->addChoice('1', '1', __('Show the link only under the first ad block.', 'easy-adsenser'))->after = "<br />";
      $o->addChoice('-1', '-1', __('Show the link at the bottom of your blog page.', 'easy-adsenser'))->after = "<br />";
      $o->addChoice('0', '0', __('Suppress links', 'easy-adsenser'))->after = "<br />";
      $this->ezOptions['max_link'] = clone $o;

      $o = new EzCheckBox('suppressBoxes');
      $o->title = __('Easy AdSense displays a box with red borders to indicate where an ad would have been placed, but has been suppressed by one of the filters above. If you would like to suppress the boxes, check this option.', 'easy-adsenser');
      $o->desc = __('Suppress Placement Boxes?', 'easy-adsenser');
      $o->between = "&nbsp;";
      $o->after = "<br /><br />";
      $this->ezOptions['suppressBoxes'] = clone $o;
    }

    function migrateOptions() {
      $lookup = array('info' => '',
          'limit_lu' => '',
          'allow_exitjunction' => '',
          'policy' => '',
          'mc' => '',
          'allow_feeds' => '',
          'suspend_ads' => '',
          'gFilter' => '',
          'kill_mobile' => '',
          'filterValue' => '',
          'bannedIPs' => '',
          'compatMode' => '',
          'excerptNumber' => '',
          'shortCodeMode' => '',
          'kill_pages' => 'kill_page',
          'kill_attach' => 'kill_attachment',
          'kill_front' => 'kill_front_page',
          'kill_cat' => 'kill_category');
      foreach ($lookup as $k => $v) {
        if (isset($this->options[$k])) {
          if (!empty($v)) {
            $this->options[$v] = $this->options[$k];
          }
          unset($this->options[$k]);
        }
      }
      $this->options['kill_author'] = false;
      update_option($this->optionName, $this->options);
    }

    function mkDefaultOptions() { // TODO: Merge this with mkEzOptions
      $defaultOptions = array('show_leadin' => 'float:right',
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
          'title_gsearch_custom' => '',
          'kill_gsearch_title' => '',
          'margin_gsearch' => 0,
          'text_gsearch' => $this->defaults['defaultText'],
          'max_count' => 3,
          'max_link' => 0,
          'force_midad' => false,
          'force_widget' => false,
          'kill_page' => false,
          'show_borders' => false,
          'border_width' => 1,
          'border_normal' => '00FFFF',
          'border_color' => 'FF0000',
          'border_widget' => false,
          'border_lu' => false,
          'title_lu' => '',
          'kill_lu_title' => false,
          'kill_attachment' => false,
          'kill_home' => false,
          'kill_front_page' => false,
          'kill_category' => false,
          'kill_tag' => false,
          'kill_archive' => false,
          'kill_inline' => false,
          'kill_widget_title' => false,
          'kill_linebreaks' => false,
          'kill_single' => false,
          'kill_search' => true,
          'kill_sticky' => false,
          'title_widget' => '',
          'suppressBoxes' => false) +
              parent::mkDefaultOptions();
      return $defaultOptions;
    }

    function handleDefaultText($text, $key = '300x250') {
      $ret = $text;
      if ($ret == $this->defaults['defaultText'] || strlen(trim($ret)) == 0) {
        if ($this->options['suppressBoxes']) {
          $ret = '';
        }
        else {
          $x = strpos($key, 'x');
          $w = substr($key, 0, $x);
          $h = substr($key, $x + 1);
          $p = (int) (min($w, $h) / 6);
          $ret = '<div style="width:' . $w . 'px;height:' . $h . 'px;border:1px solid red;"><div style="padding:' . $p . 'px;text-align:center;font-family:arial;font-size:8pt;"><p>Your ads will be inserted here by</p><p><b>Easy AdSense</b>.</p><p>Please go to the plugin admin page to<br /><u title="Generate your ad code from your provider and paste it in the text box for this ad slot">Paste your ad code</u> OR<br /> <u title="Use the dropdown under the text box for this ad slot to suppress it">Suppress this ad slot</u>.</p></div></div>';
        }
      }
      return $ret;
    }

    function handleDefaults() {
      $texts = array('text_leadin', 'text_midtext', 'text_leadout');
      foreach ($texts as $t) {
        $text = $this->options[$t];
        $this->options[$t] = $this->handleDefaultText($text);
      }
    }

    // Prints out the admin page
    function printAdminPage() {
      $ez = parent::printAdminPage();
      if (empty($ez)) {
        return;
      }
      $this->handleSubmits();
      // if the defaults are not loaded, send error message
      if (empty($this->defaults)) {
        return;
      }
      if (file_exists($this->plgDir . '/admin.php')) {
        echo $this->adminMsg;
        include ($this->plgDir . '/admin.php');
      }
      else {
        echo '<font size="+1" color="red">';
        _e("Error locating the admin page!\nEnsure admin.php exists, or reinstall the plugin.", 'easy-adsenser');
        echo '</font>';
      }
    }

    function plugin_action($links, $file) {
      if ($file == plugin_basename(__FILE__)) {
        $settings_link = "<a href='options-general.php?page=easy-adsense-lite.php'>" .
                __('Settings', 'easy-adsenser') . "</a>";
        array_unshift($links, $settings_link);
      }
      return $links;
    }

    function getMetaOptions() {
      if (empty($this->metaOptions) || $this->mayBeExcerpt()) {
        global $post;
        if (is_object($post)) {
          $postID = $post->ID;
        }
        else {
          global $wp;
          $url = home_url(add_query_arg(array(), $wp->request));
          $postID = url_to_postid($url);
        }
        $metaOptions = array();
        if (!empty($postID)) {
          $lookup = array('adsense' => 'adsense',
              'adsense-top' => 'show_leadin',
              'adsense-middle' => 'show_midtext',
              'adsense-bottom' => 'show_leadout',
              'adsense-widget' => 'show_widget',
              'adsense-search' => 'title_gsearch',
              'adsense-linkunits' => 'show_lu');
          foreach ($lookup as $metaKey => $optKey) {
            if (!empty($this->options[$optKey])) {
              $metaOptions[$optKey] = $this->options[$optKey];
            }
            else {
              $metaOptions[$optKey] = '';
            }
            $customStyle = get_post_custom_values($metaKey, $postID, true);
            if (is_array($customStyle)) {
              $metaStyle = strtolower($customStyle[0]);
            }
            else {
              $metaStyle = strtolower($customStyle);
            }
            $style = '';
            if ($metaStyle == 'left') {
              $style = 'float:left;display:block;';
            }
            else if ($metaStyle == 'right') {
              $style = 'float:right;display:block;';
            }
            else if ($metaStyle == 'center') {
              $style = 'text-align:center;display:block;';
            }
            else {
              $style = $metaStyle;
            }
            if (!empty($style)) {
              $metaOptions[$optKey] = $style;
            }
          }
        }
        $this->metaOptions = $metaOptions;
      }
      return $this->metaOptions;
    }

    function findParas($content) {
      $content = strtolower($content);  // not using stripos() for PHP4 compatibility
      $paras = array();
      $lastpos = -1;
      $paraMarker = "<p";
      if (strpos($content, "<p") === false) {
        $paraMarker = "<br";
      }

      while (strpos($content, $paraMarker, $lastpos + 1) !== false) {
        $lastpos = strpos($content, $paraMarker, $lastpos + 1);
        $paras[] = $lastpos;
      }
      return $paras;
    }

    function mayBeExcerpt() {
      return is_home() || is_category() || is_tag() || is_archive();
    }

    function mkBorder() {
      if ($this->options['show_borders'] && empty($this->border)) {
        $this->border = 'border:#' . $this->options['border_normal'] .
                ' solid ' . $this->options['border_width'] . 'px;" ' .
                ' onmouseover="this.style.border=\'#' . $this->options['border_color'] .
                ' solid ' . $this->options['border_width'] . 'px\'" ' .
                'onmouseout="this.style.border=\'#' . $this->options['border_normal'] .
                ' solid ' . $this->options['border_width'] . 'px\'';
      }
      return $this->border;
    }

    function mkAdBlock($slot) {
      $border = $this->mkBorder();
      $show = $this->metaOptions["show_$slot"];
      $margin = $this->options["margin_$slot"];
      if ($this->options['kill_linebreaks']) {
        $linebreak = "";
      }
      else {
        $linebreak = "\n";
      }
      if ($this->options['kill_inline']) {
        $inline = '';
      }
      else {
        $inline = 'style="' . $show . ';margin:' .
                $margin . 'px;' . $border . '"';
      }
      $unreal = self::showUnreal(false);
      $info = $this->info();
      $adBlock = stripslashes($linebreak . $info . $linebreak .
              "<!-- [$slot: {$this->ezCount} urCount: {$this->urCount} urMax: {$this->urMax}] -->$linebreak" .
              '<div class="ezAdsense adsense adsense-' . $slot . '" ' . $inline . '>' .
              $this->options["text_$slot"] .
              ($this->urCount++ < $this->urMax ? $unreal : '') .
              "</div>" . $linebreak . $info . $linebreak);
      $this->ezCount++;
      return $adBlock;
    }

    function isKilled() {
      $killed = false;
      foreach ($this->kills as $k) {
        $fn = "is_$k";
        if ($this->options["kill_$k"] && $fn()) {
          $killed = true;
        }
      }
      return $killed;
    }

    function filterContent($content) {
      if ($this->isKilled()) {
        return $content;
      }
      $this->ezMax = $this->options['max_count'];
      if ($this->options['force_widget']) {
        $this->ezMax--;
      }
      if ($this->ezCount >= $this->ezMax) {
        return "$content <!-- Easy AdSense Unfiltered [count: {$this->ezCount} "
                . "is not less than {$this->ezMax}] -->";
      }
      if (strpos($content, "<!--noadsense-->") !== false) {
        $this->metaOptions['adsense'] = 'no';
        return "$content <!-- Easy AdSense Unfiltered [suppressed by noadsense comment] -->";
      }
      $metaOptions = $this->getMetaOptions();
      if (isset($metaOptions['adsense']) && $metaOptions['adsense'] == 'no') {
        return "$content <!-- Easy AdSense Unfiltered [suppressed by meta option adsense = no] -->";
      }

      if (!in_the_loop()) {
        return $content;
      }
      $this->handleDefaults();

      $wc = str_word_count($content);

      $show_leadin = $metaOptions['show_leadin'];
      $leadin = '';
      if ($show_leadin != 'no' && empty($this->options['header_leadin']) && $wc > $this->options['wc_leadin']) {
        if ($this->ezCount < $this->ezMax) {
          $leadin = $this->mkAdBlock("leadin");
        }
      }

      $show_midtext = $metaOptions['show_midtext'];
      if ($show_midtext != 'no' && $wc > $this->options['wc_midtext']) {
        if ($this->ezCount < $this->ezMax) {
          $paras = $this->findParas($content);

          $half = sizeof($paras);
          while (sizeof($paras) > $half) {
            array_pop($paras);
          }
          $split = 0;
          if (!empty($paras)) {
            $split = $paras[floor(sizeof($paras) / 2)];
          }
          if ($this->options['force_midad'] || $half > 10) {
            $midtext = $this->mkAdBlock("midtext");
            $content = substr($content, 0, $split) . $midtext . substr($content, $split);
          }
        }
      }

      $show_leadout = $metaOptions['show_leadout'];
      $leadout = '';
      if ($show_leadout != 'no' && $wc > $this->options['wc_leadout']) {
        if ($this->ezCount < $this->ezMax) {
          if (strpos($show_leadout, "float") !== false) {
            $paras = $this->findParas($content);
            $split = array_pop($paras);
            if (!empty($split)) {
              $content1 = substr($content, 0, $split);
              $content2 = substr($content, $split);
            }
          }
          $leadout = $this->mkAdBlock("leadout");
        }
      }
      if (!empty($this->options['header_leadin'])) {
        $this->leadin = $leadin;
        $leadin = '';
      }
      if ($this->options['footer_leadout']) {
        $this->leadout = $leadout;
        $leadout = '';
      }
      if (empty($content1)) {
        $content = $leadin . $content . $leadout;
      }
      else {
        $content = $leadin . $content1 . $leadout . $content2;
      }
      return $content;
    }

    // This is add_action target to either the_content, loop_start or send_headers.
    function filterHeader($arg) {
      if (is_admin()) {
        return $arg;
      }
      // is_feed() is not ready, because the WP query may not be run yet.
      if (strpos($_SERVER['REQUEST_URI'], 'feed') !== false) {
        return $arg;
      }
      if ($this->isKilled()) {
        return $arg;
      }
      $show_leadin = $this->options['show_leadin'];
      if ($show_leadin != 'no') {
        $metaOptions = $this->getMetaOptions();
        if (empty($metaOptions['adsense']) ||
                (!empty($metaOptions['adsense']) && $metaOptions['adsense'] != 'no')) {
          $this->metaOptions['show_leadin'] = '';
          echo $this->mkAdBlock("leadin");
          unset($this->metaOptions);
        }
      }
      return $arg;
    }

    function filterFooter($arg) {
      if (is_admin()) {
        return $arg;
      }
      echo $this->leadout;
      return $arg;
    }

  }

} //End Class EzAdSense

if (class_exists("EzAdSense")) {
  $ezAdSense = new EzAdSense();
  if (isset($ezAdSense) && !empty($ezAdSense->defaults)) {
    if (!function_exists("ezAdSense_ap")) {

      function ezAdSense_ap() {
        global $ezAdSense;
        if (function_exists('add_options_page')) {
          $mName = 'Easy AdSense';
          add_options_page($mName, $mName, 'activate_plugins', basename(__FILE__), array($ezAdSense, 'printAdminPage'));
        }
      }

    }

    // sidebar AdSense Widget (skyscraper)
    class EzAdsWidget extends WP_Widget {

      function EzAdsWidget() {
        $widget_ops = array('classname' => 'EzAdsWidget',
            'description' =>
            __('Show a Google AdSense block in your sidebar as a widget', 'easy-adsenser'));
        $this->WP_Widget('EzAdsWidget', 'Easy AdSense: Google Ads', $widget_ops);
      }

      function widget($args, $instance) {
        // outputs the content of the widget
        global $ezAdSense;
        if ($ezAdSense->isKilled()) {
          return;
        }
        extract($args);
        $ezAdSense->options['text_widget'] = $ezAdSense->handleDefaultText($ezAdSense->options['text_widget'], '160x600');
        $metaOptions = $ezAdSense->getMetaOptions();
        if (isset($metaOptions['adsense']) && $metaOptions['adsense'] == 'no') {
          return;
        }
        $show_widget = $metaOptions['show_widget'];
        if ($show_widget == 'no') {
          return;
        }
        $ezAdSense->ezMax = $ezAdSense->options['max_count'];
        $ezAdSense->urMax = $ezAdSense->options['max_link'];
        if (!$ezAdSense->options['force_widget']) {
          if ($ezAdSense->ezCount >= $ezAdSense->ezMax) {
            return;
          }
        }

        $title = empty($ezAdSense->options['title_widget']) ?
                __('Sponsored Links', 'easy-adsenser') :
                stripslashes(htmlspecialchars($ezAdSense->options['title_widget']));
        echo $before_widget;
        if (!$ezAdSense->options['kill_widget_title']) {
          echo $before_title . $title . $after_title;
        }
        echo $ezAdSense->mkAdBlock("widget");
        echo $after_widget;
      }

      function update($new_instance, $old_instance) {
        // processes widget options to be saved
        return $new_instance;
      }

      function form($instance) {
        // outputs the options form on admin
        echo '<p>Configure it at <br />';
        echo '<a href="options-general.php?page=easy-adsense-lite.php"> ';
        echo 'Settings &rarr; Easy AdSense</a>';
        echo '</p>';
      }

    }

    add_action('widgets_init', create_function('', 'return register_widget("EzAdsWidget");'));

    // sidebar Search Widget
    class EzAdsSearch extends WP_Widget {

      function EzAdsSearch() {
        $widget_ops = array('classname' => 'EzAdsSearch',
            'description' =>
            __('Show a Google Search Box in your sidebar as a widget', 'easy-adsenser'));
        $this->WP_Widget('EzAdsSearch', 'Easy AdSense: Google Search', $widget_ops);
      }

      function widget($args, $instance) {
        // outputs the content of the widget
        global $ezAdSense;
        extract($args);
        $ezAdSense->options['text_gsearch'] = $ezAdSense->handleDefaultText($ezAdSense->options['text_gsearch'], '160x160');
        $metaOptions = $ezAdSense->getMetaOptions();
        if (isset($metaOptions['adsense']) && $metaOptions['adsense'] == 'no') {
          return;
        }
        $title_gsearch = $metaOptions['title_gsearch'];
        if ($title_gsearch != 'no') {
          if ($ezAdSense->options['kill_linebreaks']) {
            $linebreak = "";
          }
          else {
            $linebreak = "\n";
          }
          $title = $before_title . $title_gsearch . $after_title;
          if ($title_gsearch == 'dark') {
            $title = '<img src=" ' . $ezAdSense->plgURL . '/google-dark.gif" ' .
                    ' border="0" alt="[Google]" align="middle" />';
          }
          else if ($title_gsearch == 'light') {
            $title = '<img src=" ' . $ezAdSense->plgURL . '/google-light.gif" ' .
                    ' border="0" alt="[Google]" align="middle" />';
          }
          else if ($title_gsearch == 'customized') {
            $title = $ezAdSense->options['title_gsearch_custom'];
          }
          echo $before_widget;
          if (!$ezAdSense->options['kill_gsearch_title']) {
            echo $title;
          }
          $margin = $ezAdSense->options['margin_gsearch'];
          if ($ezAdSense->options['kill_inline']) {
            $inline = '';
          }
          else {
            $inline = 'style="margin:' . $margin . 'px; "';
          }
          echo stripslashes('<div class="ezAdsense adsense adsense-search"><div '
                  . $inline . '>' . "$linebreak" .
                  $ezAdSense->options['text_gsearch'] . "$linebreak" .
                  '</div></div>');
          echo $after_widget;
        }
      }

      function update($new_instance, $old_instance) {
        // processes widget options to be saved
        return $new_instance;
      }

      function form($instance) {
        // outputs the options form on admin
        echo '<p>Configure it at <br />';
        echo '<a href="options-general.php?page=easy-adsense-lite.php"> ';
        echo 'Settings &rarr; Easy AdSense</a>';
        echo '</p>';
      }

    }

    add_action('widgets_init', create_function('', 'return register_widget("EzAdsSearch");'));

    // sidebar Link Units
    class EzAdsLU extends WP_Widget {

      function EzAdsLU() {
        $widget_ops = array('classname' => 'EzAdsLU',
            'description' =>
            __('Show a Google Links Unit in your sidebar as a widget', 'easy-adsenser'));
        $this->WP_Widget('EzAdsLU', 'Easy AdSense: Google Link Unit', $widget_ops);
      }

      function widget($args, $instance) {
        // outputs the content of the widget
        global $ezAdSense;
        if ($ezAdSense->isKilled()) {
          return;
        }
        extract($args);
        $ezAdSense->options['text_lu'] = $ezAdSense->handleDefaultText($ezAdSense->options['text_lu'], '160x160');
        $title = empty($ezAdSense->options['title_lu']) ? '' :
                $before_title .
                stripslashes(htmlspecialchars($ezAdSense->options['title_lu'])) .
                $after_title;
        $metaOptions = $ezAdSense->getMetaOptions();
        if (isset($metaOptions['adsense']) && $metaOptions['adsense'] == 'no') {
          return;
        }
        $show_lu = $metaOptions['show_lu'];
        if ($show_lu != 'no') {
          echo $before_widget;
          if (!$ezAdSense->options['kill_widget_title']) {
            echo $title;
          }
          echo $ezAdSense->mkAdBlock("lu");
          echo $after_widget;
        }
      }

      function update($new_instance, $old_instance) {
        // processes widget options to be saved
        return $new_instance;
      }

      function form($instance) {
        // outputs the options form on admin
        echo '<p>Configure it at <br />';
        echo '<a href="options-general.php?page=easy-adsense-lite.php"> ';
        echo 'Settings &rarr; Easy AdSense</a>';
        echo '</p>';
      }

    }

    add_action('widgets_init', create_function('', 'return register_widget("EzAdsLU");'));
    add_action('admin_menu', 'ezAdSense_ap');
    add_filter('plugin_action_links', array($ezAdSense, 'plugin_action'), -10, 2);

    add_filter('the_content', array($ezAdSense, 'filterContent'));
    if ($ezAdSense->options['max_link'] === -1) {
      add_action('wp_footer', array($ezAdSense, 'showUnreal', 1));
    }
    else {
      remove_action('wp_footer', array($ezAdSense, 'showUnreal'));
    }

    if (!empty($ezAdSense->options['header_leadin'])) {
      add_action($ezAdSense->options['header_leadin'], array($ezAdSense, 'filterHeader'));
    }

    if ($ezAdSense->options['footer_leadout']) {
      add_action($ezAdSense->options['footer_leadout'], array($ezAdSense, 'filterFooter'));
    }
    register_activation_hook(__FILE__, array($ezAdSense, 'migrateOptions'));
  }
}

