<?php
/*
  Copyright (C) 2008 www.ads-ez.com

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License as
  published by the Free Software Foundation; either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that they will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with the programs.  If not, see <http://www.gnu.org/licenses/>.
 */

echo '<script type="text/javascript" src="' . $this->plgURL . '/wz_tooltip.js"></script>';

$this->mkEzOptions();
$this->setOptionValues();
$this->mkHelpTags();
?>
<div class="wrap" id="wrapper" style="width:1000px">
  <h2>Easy AdSense Setup</h2>
  <table class="form-table">
    <tr style="vertical-align:middle">
      <td style="width:40%">
        <h3>
          <?php
          _e('Instructions', 'easy-adsenser');
          echo "</h3>\n<ul style='padding-left:10px;list-style-type:circle; list-style-position:inside;'>\n";
          foreach ($this->helpTags as $help) {
            echo "<li>";
            $help->render();
            echo "</li>\n";
          }
          ?>
          </ul>
      </td>
      <?php
      include ($this->plgDir . '/head-text.php');
      ?>
    </tr></table>
  <form method='post' action='#'>
    <?php
    $this->renderNonce();
    $ez->renderNags($this->options);
    ?>
    <br />
    <table>
      <tr><td><h3>
            <?php
            printf(__('Options (for the %s theme)', 'easy-adsenser'), get_option('stylesheet'));
            ?>
          </h3></td></tr>
    </table>
    <table style='width:100%'>
      <tr>
        <td style='width:50%;height:50px'>
          <table class='form-table'>
            <tr>
              <td style='width:50%;height:40px'>
                <?php
                echo "<b><u>";
                _e('Ad Blocks in Your Posts', 'easy-adsenser');
                echo "</u></b><br />";
                _e('[Appears in your posts and pages]', 'easy-adsenser');
                ?>
              </td>
            </tr>
          </table>
        </td>
        <td style='width:50%;height:50px'>
          <table class='form-table'>
            <tr>
              <td style='width:50%;height:40px'>
                <?php
                echo "<b><u>";
                _e('Widgets for Your Sidebars', 'easy-adsenser');
                ?>
                </u></b><br />
                <?php
                _e('[See <a href="widgets.php"> Appearance (or Design) &rarr; Widgets</a>]', 'easy-adsenser');
                ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <table style='width:100%'>
      <tr style='vertical-align:top'>
        <td style='width:50%'>
          <table class='form-table'>
            <tr style='vertical-align:top'>
              <td style='width:50%;height:220px;vertical-align:middle'>
                <?php
                $this->ezOptions['text_leadin']->render();
                echo "<b><span style='display:inline-block;width:30%'>";
                _e('Ad Alignment', 'easy-adsenser');
                echo "</span></b>"
                . "<span style='display:inline-block;width:40%'>";
                $this->ezOptions['wc_leadin']->render();
                echo "</span>";
                $this->ezOptions['margin_leadin']->render();
                $this->ezOptions['header_leadin']->render();
                echo "&nbsp;";
                $this->ezOptions['show_leadin']->render();
                ?>
                <br />
              </td>
            </tr>
            <tr style='vertical-align:top'>
              <td style='width:50%;height:220px;vertical-align:middle'>
                <?php
                $this->ezOptions['text_midtext']->render();
                echo "<b><span style='display:inline-block;width:30%'>";
                _e('Ad Alignment', 'easy-adsenser');
                echo "</span></b>"
                . "<span style='display:inline-block;width:40%'>";
                $this->ezOptions['wc_midtext']->render();
                echo "</span>";
                $this->ezOptions['margin_midtext']->render();
                $this->ezOptions['force_midad']->render();
                echo "&nbsp;";
                $this->ezOptions['show_midtext']->render();
                ?>
                <br />
              </td>
            </tr>
            <tr style='vertical-align:top'>
              <td style='width:50%;height:250px;vertical-align:middle'>
                <?php
                $this->ezOptions['text_leadout']->render();
                echo "<b><span style='display:inline-block;width:30%'>";
                _e('Ad Alignment', 'easy-adsenser');
                echo "</span></b>"
                . "<span style='display:inline-block;width:40%'>";
                $this->ezOptions['wc_leadout']->render();
                echo "</span>";
                $this->ezOptions['margin_leadout']->render();
                $this->ezOptions['footer_leadout']->render();
                echo "&nbsp;";
                $this->ezOptions['show_leadout']->render();
                ?>
              </td>
            </tr>
          </table>

          <table class='form-table'>
            <tr style='vertical-align:top'>
              <td style='width:50%;height:250px;vertical-align:middle'>
                <?php
                $this->ezOptions['max_count']->render();
                echo "<br style='line-height: 5px;' /><b>";
                _e('Suppress AdSense Ad Blocks on:', 'easy-adsenser');
                echo "</b>";
                foreach ($this->kills as $k) {
                  $this->ezOptions["kill_$k"]->render();
                }
                ?>
                <br style='line-height: 5px;' />

                <b><?php _e('Other Options', 'easy-adsenser'); ?></b><br />

                <?php
                $this->ezOptions['force_widget']->render();
                $this->ezOptions['show_borders']->render();
                $this->ezOptions['border_widget']->render();
                $this->ezOptions['border_lu']->render();
                echo "<span style='display:inline-block;width:20px'> </span>";
                $this->ezOptions['border_width']->render();
                $this->ezOptions['border_normal']->render();
                $this->ezOptions['border_color']->render();
                $this->ezOptions['kill_inline']->render();
                $this->ezOptions['kill_linebreaks']->render();
                ?>
              </td>
            </tr>
          </table>

        </td>
        <td style='width:50%'>

          <table class='form-table'>
            <tr style='vertical-align:top'>
              <td style='width:50%;height:220px;vertical-align:middle'>
                <?php
                $this->ezOptions['text_widget']->render();
                ?>
                <span style='display:inline-block;width:70%'><b><?php _e('Ad Alignment', 'easy-adsenser'); ?></b>&nbsp;<?php _e('(Where to show?)', 'easy-adsenser'); ?></span>
                <?php
                $this->ezOptions['margin_widget']->render();
                $this->ezOptions['show_widget']->render();
                $this->ezOptions['title_widget']->render();
                $this->ezOptions['kill_widget_title']->render();
                ?>
              </td>
            </tr>
            <tr style='vertical-align:top'>
              <td style='width:50%;height:220px;vertical-align:middle'>
                <?php
                $this->ezOptions['text_lu']->render();
                ?>
                <span style='display:inline-block;width:70%'><b><?php _e('Ad Alignment', 'easy-adsenser'); ?></b>&nbsp;<?php _e('(Where to show?)', 'easy-adsenser'); ?></span>
                <?php
                $this->ezOptions['margin_lu']->render();
                $this->ezOptions['show_lu']->render();
                $this->ezOptions['title_lu']->render();
                $this->ezOptions['kill_lu_title']->render();
                ?>
              </td>
            </tr>
            <tr style='vertical-align:top'>
              <td style='width:50%;height:250px;vertical-align:middle'>
                <?php
                $this->ezOptions['text_gsearch']->render();
                ?>
                <span style='display:inline-block;width:70%'><b><?php _e('Search Title', 'easy-adsenser'); ?></b>&nbsp;<?php _e('(Title of the Google Search Widget)', 'easy-adsenser'); ?></span>
                <?php
                $this->ezOptions['margin_gsearch']->render();
                $this->ezOptions['title_gsearch']->render();
                $this->ezOptions['title_gsearch_custom']->render();
                $this->ezOptions['kill_gsearch_title']->render();
                ?>
              </td>
            </tr>
          </table>

          <table class='form-table'>
            <tr style='vertical-align:top'>
              <td style='width:50%;height:250px;vertical-align:middle'>
                <br style='line-height: 12px;' />
                <?php
                $this->ezOptions['max_link']->render();
                $this->ezOptions['kill_author']->render();
                $this->ezOptions['suppressBoxes']->render();
                $ez->renderWhyPro($short = true);
                ?>
              </td>
            </tr>
          </table>

        </td>
      </tr>
    </table>

    <div class="submit">
      <?php
      $this->renderSubmitButtons();
      $this->ezTran->renderTranslator();
      ?>
    </div>
  </form>

  <span id="help0" style='display:none'>
    <?php
    echo "1. ";
    _e('Generate AdSense code (from http://adsense.google.com &rarr; AdSense Setup &rarr; Get Ads).', 'easy-adsenser');
    echo "<br />\n2. ";
    _e('Cut and paste the AdSense code into the boxes below, deleting the existing text.', 'easy-adsenser');
    echo "<br />\n3. ";
    _e('Decide how to align and show the code in your blog posts.', 'easy-adsenser');
    echo "<br />\n4. ";
    _e('Take a look at the Google policy option, and other options. The defaults should work.', 'easy-adsenser');
    echo "<br />\n5. ";
    printf(__('If you want to use the widgets, drag and drop them at %s Appearance (or Design) &rarr; Widgets %s', 'easy-adsenser'), '<a href="widgets.php">', '</a>.');
    echo "<br />\n<b>";
    _e('Save the options, and you are done!', 'easy-adsenser');
    echo "</b>";
    ?>
  </span>

  <span id="help1" style='display:none'>
    <?php _e('If you want to suppress AdSense in a particular post or page, give the <b><em>comment </em></b> "&lt;!--noadsense--&gt;" somewhere in its text.
<br />
<br />
Or, insert a <b><em>Custom Field</em></b> with a <b>key</b> "adsense" and give it a <b>value</b> "no".<br />
<br />
Other <b><em>Custom Fields</em></b> you can use to fine-tune how a post or page displays AdSense blocks:<br />
<b>Keys</b>:<br />
adsense-top,
adsense-middle,
adsense-bottom,
adsense-widget,
adsense-search<br />
<b>Values</b>:<br />
left,
right,
center,
no', 'easy-adsenser'); ?>
  </span>

  <span id="help2" style='display:none'>
    <?php _e('<em>Easy AdSense</em> gives you widgets to embelish your sidebars. You can configure them here (on the right hand side of the Options table below) and place them on your page using <a href="widgets.php"> Appearance (or Design) &rarr; Widgets</a>.
<br />
<br />
1. <b>AdSense Widget</b> is an ad block widget that you can place any where on the sidebar. Typically, you would put a skyscraper block (160x600px, for instance) on your sidebar, but you can put anything -- not necessarily AdSense code.<br />
<br />
2. <b>AdSense Link Units</b>, if enabled, give you multiple widgets to put <a href="https://www.google.com/adsense/support/bin/answer.py?hl=en&amp;answer=15817" target="_blank">link units</a> on your sidebars. You can display three of them according to Google AdSense policy, and you can configure the number of widgets you need.<br /><br />
3. <b>Google Search Widget</b> gives you another widget to place a <a href="https://www.google.com/adsense/support/bin/answer.py?hl=en&amp;answer=17960" target="_blank">custom AdSense search box</a> on your sidebar. You can customize the look of the search box and its title by configuring them on this page.', 'easy-adsenser'); ?>
  </span>

  <?php
  if (!$this->isPro) {
    $ez->renderWhyPro();
  }
  $ez->renderSupport();
  include ($this->plgDir . '/tail-text.php');
  ?>

  <table class="form-table" >
    <tr><th scope="row"><b><?php _e('Credits', 'easy-adsenser'); ?></b></th></tr>
    <tr><td>
        <ul style="padding-left:10px;list-style-type:circle; list-style-position:inside;" >
          <li>
            <?php printf(__('%s uses the excellent Javascript/DHTML tooltips by %s', 'easy-adsenser'), '<b>Easy Adsense</b>', '<a href="http://www.walterzorn.com" target="_blank" title="Javascript, DTML Tooltips"> Walter Zorn</a>.');
            ?>
          </li>
        </ul>
      </td>
    </tr>
  </table>

</div>
