<?php
/*
Copyright (C) 2008 www.ads-ez.com

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
*/

@include(dirname (__FILE__).'/myPlugins.php');
$ezIsPro = false;

echo '<script type="text/javascript" src="'. get_option('siteurl') . '/' . PLUGINDIR . '/' .  basename(dirname(__FILE__)) . '/wz_tooltip.js"></script>' ;
if (isset($this->ezTran)) {
  echo '<div class="wrap" style="width:900px">' ;
  echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">' ;
  wp_nonce_field('EzAdsenseSubmit','EzAdsenseNonce');
  $this->ezTran->printAdminPage() ;
  echo "</form>\n</div>" ;
}
else {
?>

<div class="wrap" id="wrapper" style="width:1000px">
    <h2>Easy AdSense Setup
</h2>

<form method="post" action="">
<?php
wp_nonce_field('EzAdsenseSubmit','EzAdsenseNonce');
$plgDir = dirname(__FILE__) ;
$plgName = 'easy-adsense' ;
if (empty($this->options['kill_rating']))
  renderRating($myPlugins[$plgName], $plgDir) ;
if (empty($this->options['kill_invites']))
  renderInvite($myPlugins[$plgName], $plgName) ;
?>
<table>
<tr><th scope="row"><h3><?php _e('Instructions', 'easy-adsenser') ; ?></h3></th></tr>
</table>

<table class="form-table" style="width:100%">
<tr style="vertical-align:middle">
<td style="width:40%">

<ul style="padding-left:10px;list-style-type:circle; list-style-position:inside;" >
<li>
<a href="#" title="<?php _e('Click for help', 'easy-adsenser') ; ?>" onclick="TagToTip('help0',WIDTH, 300, TITLE, '<?php _e('How to Set it up', 'easy-adsenser') ; ?>', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 15, 5])">
<?php
printf(__('A few easy steps to setup %s', 'easy-adsenser'),'<em>Easy AdSense</em>') ;
?></a><br />
</li>
<li>
<a href="#" title="<?php _e('Click for help', 'easy-adsenser') ; ?>" onclick="TagToTip('help1',WIDTH, 300, TITLE, '<?php _e('How to Control AdSense on Each Post', 'easy-adsenser') ; ?>', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 15, 5])">
<?php _e('Need to control ad blocks on each post?', 'easy-adsenser') ;?></a><br />
</li>
<li>
<a href="#" title="<?php _e('Click for help', 'easy-adsenser') ; ?>" onclick="TagToTip('help1a',WIDTH, 300, TITLE, '<?php _e('All-in-One AdSense Control', 'easy-adsenser') ; ?>', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 15, 5])">
<?php _e('Sidebar Widgets, Link Units or Google Search', 'easy-adsenser') ;?></a><br />
</li>
</ul>
</td>

<?php @include (dirname (__FILE__).'/head-text.php'); ?>

</tr>
</table>

<br />

<table>
<tr><th scope="row"><h3><?php printf(__('Options (for the %s theme)', 'easy-adsenser'), get_option('stylesheet')); ?></h3></th></tr>
</table>

<table style="width:100%">
<tr>
<td style="width:50%;height:50px">

<table class="form-table">
<tr>
<td style="width:50%;height:40px">
<b><u><?php _e('Ad Blocks in Your Posts', 'easy-adsenser') ; ?></u></b><br />
<?php _e('[Appears in your posts and pages]', 'easy-adsenser') ; ?>
</td>
</tr>
</table>
</td>

<td style="width:50%;height:50px">
<table class="form-table">
<tr>
<td style="width:50%;height:40px">
<b><u><?php _e('Widgets for Your Sidebars', 'easy-adsenser') ; ?></u></b><br />
<?php _e('[See <a href="widgets.php"> Appearance (or Design) &rarr; Widgets</a>]', 'easy-adsenser') ; ?>
</td>
</tr>
</table>
</td>
</tr>
</table>

<table style="width:100%">
<tr style="vertical-align:top">
<td style="width:50%">
<table class="form-table">
<tr style="vertical-align:top">
<td style="width:50%;height:220px;vertical-align:middle">
<b><?php _e('Lead-in AdSense Text', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Appears near the beginning of the post)', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="ezAdSenseTextLeadin" style="width: 95%; height: 130px;"><?php echo(stripslashes(htmlspecialchars($this->options['text_leadin']))) ?></textarea>
<br />
<b><span style="display:inline-block;width:30%"><?php _e('Ad Alignment', 'easy-adsenser') ; ?></b></span>
<span style="display:inline-block;width:40%"  onmouseover="Tip('<?php _e('Suppress this ad block if the post is not at least this many words long. Enter 0 or a small number if you do not want to suppress ads based on the number of words in the page/post.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Min. Word Count', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Min. Word Count', 'easy-adsenser') ; ?>: <input style="width:40px;text-align:center;" id="ezLeadInWC" name="ezLeadInWC" value="<?php echo(stripslashes(htmlspecialchars($this->options['wc_leadin'])));?>" /></span>
<span onmouseover="Tip('<?php _e('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Tweak Margins', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Margin:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezLeadInMargin" name="ezLeadInMargin" value="<?php echo(stripslashes(htmlspecialchars($this->options['margin_leadin'])));?>" />px</span>
<br />

<label for="ezHeaderLeadin" onmouseover="Tip('<?php _e('Select where you would like to show the lead-in ad block. A placement above or below the blog header would be suitable for a wide AdSense block.', 'easy-adsenser') ; echo (htmlspecialchars('<br />Note that <b>Below Header</b> and <b>End of Page</b> options are hacks that may not be compatible with the WordPress default widget for <b>Recent Posts</b> or anything else that may use DB queries or loops. If you have problems with your sidebars and/or font sizes, please choose some other <b>Postion</b> option.')) ; ?>', WIDTH, 240, TITLE, '<?php _e('(Where to show?)', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<?php _e('Position:', 'easy-adsenser') ; ?>&nbsp;
<select style="width:30%;" id="ezHeaderLeadin" name="ezHeaderLeadin">
<option <?php if ($this->options['header_leadin'] == 'send_headers') { echo('selected="selected"'); }?> value ="send_headers"><?php _e('Above Header', 'easy-adsenser') ?></option>
<option <?php if ($this->options['header_leadin'] == 'the_content') { echo('selected="selected"'); }?> value ="the_content"><?php _e('Below Header', 'easy-adsenser') ?></option>
<option <?php if ($this->options['header_leadin'] == '') { echo('selected="selected"'); }?> value =""><?php _e('Beginning of Post', 'easy-adsenser') ?></option>
</select>
</label>
&nbsp;
<label for="ezAdSenseShowLeadin" onmouseover="Tip('<?php _e('Decide whether to show this AdSense block, and specify how to align it.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('(Where to show?)', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<?php _e('Show:', 'easy-adsenser') ; ?>&nbsp;
<select style="width:38%;" id="ezAdSenseShowLeadin" name="ezAdSenseShowLeadin">
<option <?php if ($this->options['show_leadin'] == 'no') { echo('selected="selected"'); }?> value ="no"><?php _e('Suppress Lead-in Ad', 'easy-adsenser') ?></option>
<option <?php if ($this->options['show_leadin'] == 'float:left') { echo('selected="selected"'); }?> value ="float:left"><?php _e('Align Left', 'easy-adsenser'); echo ', ' ; _e('Text-wrapped', 'easy-adsenser'); ?></option>
<option <?php if ($this->options['show_leadin'] == 'text-align:left') { echo('selected="selected"'); }?> value ="text-align:left"><?php _e('Align Left', 'easy-adsenser'); echo ', ' ; _e('No wrap', 'easy-adsenser'); ?></option>
<option <?php if ($this->options['show_leadin'] == 'text-align:center') { echo('selected="selected"'); }?> value ="text-align:center"><?php _e('Center', 'easy-adsenser') ?></option>
<option <?php if ($this->options['show_leadin'] == 'float:right') { echo('selected="selected"'); }?> value ="float:right"><?php _e('Align Right', 'easy-adsenser'); echo ', ' ; _e('Text-wrapped', 'easy-adsenser'); ?></option>
<option <?php if ($this->options['show_leadin'] == 'text-align:right') { echo('selected="selected"'); }?> value ="text-align:right"><?php _e('Align Rigth', 'easy-adsenser'); echo ', ' ; _e('No wrap', 'easy-adsenser'); ?></option>
</select>
</label>
<br />
</td>
</tr>
<tr style="vertical-align:top">
<td style="width:50%;height:220px;vertical-align:middle">
<b><?php _e('Mid-Post AdSense Text', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Appears near the middle of the post)', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="ezAdSenseTextMidtext" style="width: 95%; height: 130px;"><?php echo(stripslashes(htmlspecialchars($this->options['text_midtext']))) ?></textarea>
<br />
<b><span style="display:inline-block;width:30%"><?php _e('Ad Alignment', 'easy-adsenser') ; ?></b></span>
<span style="display:inline-block;width:40%" onmouseover="Tip('<?php _e('Suppress this ad block if the post is not at least this many words long. Enter 0 or a small number if you do not want to suppress ads based on the number of words in the page/post.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Min. Word Count', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Min. Word Count', 'easy-adsenser') ; ?>: <input style="width:40px;text-align:center;" id="ezMidTextWC" name="ezMidTextWC" value="<?php echo(stripslashes(htmlspecialchars($this->options['wc_midtext'])));?>" /></span>
<span onmouseover="Tip('<?php _e('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Tweak Margins', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Margin:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezMidTextMargin" name="ezMidTextMargin" value="<?php echo(stripslashes(htmlspecialchars($this->options['margin_midtext'])));?>" />px</span>
<br />
<label style="display:inline-block;width:45%" for="ezForceMidAd" onmouseover="Tip('<?php _e('Force mid-text ad (if enabled) even in short posts.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Force Mid-post Ad', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<input type="checkbox" id="ezForceMidAd" name="ezForceMidAd"  <?php if ($this->options['force_midad']) { echo('checked="checked"'); }?> /> <?php _e('Force Mid-post Ad', 'easy-adsenser') ; ?></label>
<label for="ezAdSenseShowMidtext" onmouseover="Tip('<?php _e('Decide whether to show this AdSense block, and specify how to align it.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('(Where to show?)', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<?php _e('Show:', 'easy-adsenser') ; ?>
<select style="width:38%;" id="ezAdSenseShowMidtext" name="ezAdSenseShowMidtext">
<option <?php if ($this->options['show_midtext'] == 'no') { echo('selected="selected"'); }?> value ="no"><?php _e('Suppress Mid-post Ad', 'easy-adsenser') ?></option>
<option <?php if ($this->options['show_midtext'] == 'float:left') { echo('selected="selected"'); }?> value ="float:left"><?php _e('Align Left', 'easy-adsenser'); echo ', ' ; _e('Text-wrapped', 'easy-adsenser'); ?></option>
<option <?php if ($this->options['show_midtext'] == 'text-align:left') { echo('selected="selected"'); }?> value ="text-align:left"><?php _e('Align Left', 'easy-adsenser'); echo ', ' ; _e('No wrap', 'easy-adsenser'); ?></option>
<option <?php if ($this->options['show_midtext'] == 'text-align:center') { echo('selected="selected"'); }?> value ="text-align:center"><?php _e('Center', 'easy-adsenser') ?></option>
<option <?php if ($this->options['show_midtext'] == 'float:right') { echo('selected="selected"'); }?> value ="float:right"><?php _e('Align Right', 'easy-adsenser'); echo ', ' ; _e('Text-wrapped', 'easy-adsenser'); ?></option>
<option <?php if ($this->options['show_midtext'] == 'text-align:right') { echo('selected="selected"'); }?> value ="text-align:right"><?php _e('Align Rigth', 'easy-adsenser'); echo ', ' ; _e('No wrap', 'easy-adsenser'); ?></option>
</select>
</label>

</td>
</tr>
<tr style="vertical-align:top">
<td style="width:50%;height:200px;vertical-align:middle">
<b><?php _e('Post Lead-out AdSense Text', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Appears near the end of the post)', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="ezAdSenseTextLeadout" style="width: 95%; height: 112px;"><?php echo(stripslashes(htmlspecialchars($this->options['text_leadout']))) ?></textarea>
<br />
<b><span style="display:inline-block;width:30%"><?php _e('Ad Alignment', 'easy-adsenser') ; ?></b></span>
<span style="display:inline-block;width:40%"  onmouseover="Tip('<?php _e('Suppress this ad block if the post is not at least this many words long. Enter 0 or a small number if you do not want to suppress ads based on the number of words in the page/post.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Min. Word Count', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Min. Word Count', 'easy-adsenser') ; ?>: <input style="width:40px;text-align:center;" id="ezLeadOutWC" name="ezLeadOutWC" value="<?php echo(stripslashes(htmlspecialchars($this->options['wc_leadout'])));?>" /></span>
<span onmouseover="Tip('<?php _e('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Tweak Margins', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Margin:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezLeadOutMargin" name="ezLeadOutMargin" value="<?php echo(stripslashes(htmlspecialchars($this->options['margin_leadout'])));?>" />px</span>
<br />

<label for="ezFooterLeadout" onmouseover="Tip('<?php _e('Select where you would like to show the lead-out ad block. A placement above or below the blog footer would be suitable for a wide AdSense block.', 'easy-adsenser') ; echo (htmlspecialchars('<br />Note that <b>Below Header</b> and <b>End of Page</b> options are hacks that may not be compatible with the WordPress default widget for <b>Recent Posts</b> or anything else that may use DB queries or loops. If you have problems with your sidebars and/or font sizes, please choose some other <b>Position</b> option.')) ;  ?>', WIDTH, 240, TITLE, '<?php _e('(Where to show?)', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<?php _e('Position:', 'easy-adsenser') ; ?>&nbsp;
<select style="width:30%;" id="ezFooterLeadout" name="ezFooterLeadout">
<option <?php if ($this->options['footer_leadout'] == '') { echo('selected="selected"'); }?> value =""><?php _e('End of Post', 'easy-adsenser') ?></option>
<option <?php if ($this->options['footer_leadout'] == 'loop_end') { echo('selected="selected"'); }?> value ="loop_end"><?php _e('End of Page', 'easy-adsenser') ?></option>
<option <?php if ($this->options['footer_leadout'] == 'get_footer') { echo('selected="selected"'); }?> value ="get_footer"><?php _e('Above Footer', 'easy-adsenser') ?></option>
<option <?php if ($this->options['footer_leadout'] == 'wp_footer') { echo('selected="selected"'); }?> value ="wp_footer"><?php _e('Below Footer', 'easy-adsenser') ?></option>
</select>
</label>
&nbsp;
<label for="ezAdSenseShowLeadout" onmouseover="Tip('<?php _e('Decide whether to show this AdSense block, and specify how to align it.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('(Where to show?)', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<?php _e('Show:', 'easy-adsenser') ; ?>
<select style="width:38%;" id="ezAdSenseShowLeadout" name="ezAdSenseShowLeadout">
<option <?php if ($this->options['show_leadout'] == 'no') { echo('selected="selected"'); }?> value ="no"><?php _e('Suppress Lead-out Ad', 'easy-adsenser') ?></option>
<option <?php if ($this->options['show_leadout'] == 'float:left') { echo('selected="selected"'); }?> value ="float:left"><?php _e('Align Left', 'easy-adsenser'); echo ', ' ; _e('Text-wrapped', 'easy-adsenser'); ?></option>
<option <?php if ($this->options['show_leadout'] == 'text-align:left') { echo('selected="selected"'); }?> value ="text-align:left"><?php _e('Align Left', 'easy-adsenser'); echo ', ' ; _e('No wrap', 'easy-adsenser'); ?></option>
<option <?php if ($this->options['show_leadout'] == 'text-align:center') { echo('selected="selected"'); }?> value ="text-align:center"><?php _e('Center', 'easy-adsenser') ?></option>
<option <?php if ($this->options['show_leadout'] == 'float:right') { echo('selected="selected"'); }?> value ="float:right"><?php _e('Align Right', 'easy-adsenser'); echo ', ' ; _e('Text-wrapped', 'easy-adsenser'); ?></option>
<option <?php if ($this->options['show_leadout'] == 'text-align:right') { echo('selected="selected"'); }?> value ="text-align:right"><?php _e('Align Rigth', 'easy-adsenser'); echo ', ' ; _e('No wrap', 'easy-adsenser'); ?></option>
</select>
</label>
<br />
</td>
</tr>
</table>

<table class="form-table">
<tr style="vertical-align:top">
<td style="width:50%;height:250px;vertical-align:middle">
<b title="<?php _e('(Google policy allows no more than three ad blocks and three link units per page)', 'easy-adsenser') ; ?>"><?php _e('Option on Google Policy', 'easy-adsenser') ; ?></b>
<br />
<label for="ezAdSenseMax3">
<input type="radio" id="ezAdSenseMax3" name="ezAdSenseMax" value="3" <?php if ($this->options['max_count'] == 3) { echo('checked="checked"'); }?> /> <?php _e('Three ad blocks (including the side bar widget, if enabled).', 'easy-adsenser') ; ?></label><br />
<label for="ezAdSenseMax2">
<input type="radio" id="ezAdSenseMax2" name="ezAdSenseMax" value="2" <?php if ($this->options['max_count'] == 2) { echo('checked="checked"'); }?> /> <?php _e('Two ad blocks.', 'easy-adsenser') ; ?></label>
<label for="ezAdSenseMax1">
<input type="radio" id="ezAdSenseMax1" name="ezAdSenseMax" value="1" <?php if ($this->options['max_count'] == 1) { echo('checked="checked"'); }?> /> <?php _e('One ad block.', 'easy-adsenser') ; ?></label>
<label for="ezAdSenseMax0">
<input type="radio" id="ezAdSenseMax0" name="ezAdSenseMax" value="0" <?php if ($this->options['max_count'] == 0) { echo('checked="checked"'); }?> /> <?php _e('No ad blocks in posts.', 'easy-adsenser') ; ?></label><br />
<label for="ezAdSenseMax9">
<input type="radio" id="ezAdSenseMax9" name="ezAdSenseMax" value="99" <?php if ($this->options['max_count'] == 99) { echo('checked="checked"'); }?> /> <?php _e('Any number of ad blocks (At your own risk!)', 'easy-adsenser') ; ?></label><br />

<?php if (get_bloginfo('version') < 2.8) {_e('Number of Link Units widgets (&le; 3) [Google serves only three]:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezLimitLU" name="ezLimitLU" value="<?php echo(stripslashes(htmlspecialchars($this->options['limit_lu'])));?>" /><br /><br style="line-height: 3px;" /> <?php } else echo '<br style="line-height: 3px;" />' ;?>

<b><?php _e('Suppress AdSense Ad Blocks on:', 'easy-adsenser') ; ?></b>&nbsp;&nbsp;
<input type="checkbox" id="ezKillPages" name="ezKillPages" value="true" <?php if ($this->options['kill_pages']) { echo('checked="checked"'); }?> /> <a href="http://codex.wordpress.org/Pages" target="_blank" title="<?php _e('Click to see the difference between posts and pages', 'easy-adsenser') ; ?>"><?php _e('Pages (Ads only on Posts)', 'easy-adsenser') ; ?></a><br />
<label style="display:inline-block;width:35%" for="ezKillSticky" title="<?php _e('Sticky front page -- if you use a post as a front page', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillSticky" name="ezKillSticky" <?php if ($this->options['kill_sticky']) { echo('checked="checked"'); }?> /> <?php _e('Sticky Front Page', 'easy-adsenser') ; ?></label>
<label style="display:inline-block;width:25%" for="ezKillHome" title="<?php _e('Home Page and Front Page are the same for most blogs', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillHome" name="ezKillHome" <?php if ($this->options['kill_home']) { echo('checked="checked"'); }?> /> <?php _e('Home Page', 'easy-adsenser') ; ?></label>
<label style="display:inline-block;width:30%" for="ezKillFront" title="<?php _e('Home Page and Front Page are the same for most blogs', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillFront" name="ezKillFront" <?php if ($this->options['kill_front']) { echo('checked="checked"'); }?> /> <?php _e('Front Page', 'easy-adsenser') ; ?></label>
<br />
<label style="display:inline-block;width:35%" for="ezKillCat" title="<?php _e('Pages that come up when you click on category names', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillCat" name="ezKillCat" <?php if ($this->options['kill_cat']) { echo('checked="checked"'); }?> /> <?php _e('Category Pages', 'easy-adsenser') ; ?></label>
<label style="display:inline-block;width:25%" for="ezKillTag" title="<?php _e('Pages that come up when you click on tag names', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillTag" name="ezKillTag" <?php if ($this->options['kill_tag']) { echo('checked="checked"'); }?> /> <?php _e('Tag Pages', 'easy-adsenser') ; ?></label>
<label style="display:inline-block;width:30%" for="ezKillArchive" title="<?php _e('Pages that come up when you click on year/month archives', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillArchive" name="ezKillArchive" <?php if ($this->options['kill_archive']) { echo('checked="checked"'); }?> /> <?php _e('Archive Pages', 'easy-adsenser') ; ?></label>
<label style="display:inline-block;width:35%" for="ezKillSearch" title="<?php _e('Pages showing search results', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillSearch" name="ezKillSearch" <?php if ($this->options['kill_search']) { echo('checked="checked"'); }?> /> <?php _e('Search Results', 'easy-adsenser') ; ?></label>
<label style="display:inline-block;width:25%" for="ezKillSingle" title="<?php _e('Posts (ads will be shown only on other kind of pages as specified in these checkboxes)', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillSingle" name="ezKillSingle" <?php if ($this->options['kill_single']) { echo('checked="checked"'); }?> /> <?php _e('Single Posts', 'easy-adsenser') ; ?></label>
<label style="display:inline-block;width:30%" for="ezKillAttach" title="<?php _e('Pages that show attachments', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillAttach" name="ezKillAttach" <?php if ($this->options['kill_attach']) { echo('checked="checked"'); }?> /> <?php _e('Attachment Page', 'easy-adsenser') ; ?></label>
<br />
<br style="line-height: 15px;" />

<b><?php _e('Other Options', 'easy-adsenser') ; ?></b><br />
<!-- <label for="ezAllowFeeds">
<input type="checkbox" id="ezAllowFeeds" name="ezAllowFeeds"  <?php if ($this->options['allow_feeds']) { echo('checked="checked"'); }?> /> <?php _e('Allow ad blocks in feeds. [Please report any problems with this option.]', 'easy-adsenser') ; ?></label><br /> -->
<label for="ezForceWidget">
<input type="checkbox" id="ezForceWidget" name="ezForceWidget"  <?php if ($this->options['force_widget']) { echo('checked="checked"'); }?> /> <?php _e('Prioritize sidebar widget. (Always shows the widget, if enabled.)', 'easy-adsenser') ; ?></label><br />

<label for="ezShowBorders"  onmouseover="Tip('<?php _e('Google Policy says that you may not direct user attention to the ads via arrows or other graphical gimmicks. Please convince yourself that showing a mouseover decoration does not violate this Google statement before enabling this option.', 'easy-adsenser') ?>',WIDTH, 240, TITLE, 'Your call')" onmouseout="UnTip()" >
<input type="checkbox" id="ezShowBorders" name="ezShowBorders" <?php if ($this->options['show_borders']) { echo('checked="checked"'); }?> /> <?php _e('Show a border around the ads?', 'easy-adsenser') ; ?></label>&nbsp;
<label for="ezBorderWidget" title="<?php _e('Show the same border on the sidebar widget as well?', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezBorderWidget" name="ezBorderWidget" <?php if ($this->options['border_widget']) { echo('checked="checked"'); }?> /> <?php _e('Widget?', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;
<label for="ezBorderLU" title="<?php _e('Show the same border on the link units too?', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezBorderLU" name="ezBorderLU" <?php if ($this->options['border_lu']) { echo('checked="checked"'); }?> /> <?php _e('Link Units?', 'easy-adsenser') ; ?></label><br />&nbsp;&nbsp;&nbsp;&nbsp;
Width: <input style="width:25px;text-align:center;" id="ezBorderWidth" name="ezBorderWidth" value="<?php echo(stripslashes(htmlspecialchars($this->options['border_width'])));?>" />px&nbsp;&nbsp;
Colors:&nbsp; Normal:#<input style="width:65px;text-align:center;" id="ezBorderNormal" name="ezBorderNormal" value="<?php echo(stripslashes(htmlspecialchars($this->options['border_normal'])));?>" />&nbsp;&nbsp; Hover:#<input style="width:65px;text-align:center;" id="ezBorderColor" name="ezBorderColor" value="<?php echo(stripslashes(htmlspecialchars($this->options['border_color'])));?>" /><br />

<label for="ezKillInLine"  onmouseover="Tip('<?php _e('All &lt;code&gt;&amp;lt;div&amp;gt;&lt;/code&gt;s that &lt;em&gt;Easy AdSense&lt;/em&gt; creates have the class attribute &lt;code&gt;adsense&lt;/code&gt;. Furthermore, they have class attributes like &lt;code&gt;adsense-leadin&lt;/code&gt;, &lt;code&gt;adsense-midtext&lt;/code&gt;, &lt;code&gt;adsense-leadout&lt;/code&gt;, &lt;code&gt;adsense-widget&lt;/code&gt; and &lt;code&gt;adsense-lu&lt;/code&gt; depending on the type. You can set the style for these classes in your theme &lt;code&gt;style.css&lt;/code&gt; to control their appearance.&lt;br /&gt;If this is all Greek to you, please leave the option unchecked.', 'easy-adsenser'); ?>',WIDTH, 290, TITLE, 'CSS vs. In-Line')" onmouseout="UnTip()" >
<input type="checkbox" id="ezKillInLine" name="ezKillInLine"  <?php if ($this->options['kill_inline']) { echo('checked="checked"'); }?> /> <?php _e('Suppress in-line styles (Control ad-blocks using style.css)', 'easy-adsenser') ; ?></label><br />

<label for="ezKillLineBreaks"  onmouseover="Tip('<?php _e('If you find that you have extra vertical spaces or if your ad code is messed up with &lt;code&gt;&lt;&lt;/code&gt;&lt;code&gt;p&gt;&lt;/code&gt; or &lt;code&gt;&lt;&lt;/code&gt;&lt;code&gt;br /&gt;&lt;/code&gt; tags, try checking this option.&lt;br /&gt;Under normal cirumstances, this option should be left unchecked.', 'easy-adsenser'); ?>',WIDTH, 290, TITLE, 'Spurious Linebreaks')" onmouseout="UnTip()" >
<input type="checkbox" id="ezKillLineBreaks" name="ezKillLineBreaks"  <?php if ($this->options['kill_linebreaks']) { echo('checked="checked"'); }?> /> <?php _e('Prevent spurious line breaks', 'easy-adsenser') ; ?></label><br />

<label for="ezSuppressBoxes"  onmouseover="Tip('<?php _e('If no ad text is entered for a particular slot, Easy AdSense Pro displays a box with red borders to indicate where an would have been placed. If you would like to suppress them, check this option.', 'easy-adsenser'); ?>',WIDTH, 290, TITLE, 'Suppress Red Boxes')" onmouseout="UnTip()" >
<input type="checkbox" id="ezSuppressBoxes" name="ezSuppressBoxes"  <?php if ($this->options['suppressBoxes']) { echo('checked="checked"'); }?> /> <?php _e('Suppress Placement Boxes', 'easy-adsenser') ; ?></label>

</td>
</tr>
</table>

</td>
<td style="width:50%">

<table class="form-table">
<tr style="vertical-align:top">
<td style="width:50%;height:220px;vertical-align:middle">
<b><?php _e('AdSense Widget Text', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Appears in the Sidebar as a Widget)', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="ezAdSenseTextWidget" style="width: 95%; height: 110px;"><?php echo(stripslashes(htmlspecialchars($this->options['text_widget']))) ?></textarea>
<br />
<span style="display:inline-block;width:70%"><b><?php _e('Ad Alignment', 'easy-adsenser') ; ?></b>&nbsp;<?php _e('(Where to show?)', 'easy-adsenser') ; ?></span>
<span onmouseover="Tip('<?php _e('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Tweak Margins', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Margin:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezWidgetMargin" name="ezWidgetMargin" value="<?php echo(stripslashes(htmlspecialchars($this->options['margin_widget'])));?>" />px</span>
<br />
<label for="ezAdSenseShowWidget_left">
<input type="radio" id="ezAdSenseShowWidget_left" name="ezAdSenseShowWidget" value="text-align:left" <?php if ($this->options['show_widget'] == "text-align:left") { echo('checked="checked"'); }?> /> <?php _e('Align Left', 'easy-adsenser') ; ?> </label>&nbsp;
<label for="ezAdSenseShowWidget_center">
<input type="radio" id="ezAdSenseShowWidget_center" name="ezAdSenseShowWidget" value="text-align:center" <?php if ($this->options['show_widget'] == "text-align:center") { echo('checked="checked"'); }?> /> <?php _e('Center', 'easy-adsenser') ; ?> </label>&nbsp;
<label for="ezAdSenseShowWidget_right">
<input type="radio" id="ezAdSenseShowWidget_right" name="ezAdSenseShowWidget" value="text-align:right" <?php if ($this->options['show_widget'] == "text-align:right") { echo('checked="checked"'); }?> /> <?php _e('Align Right', 'easy-adsenser') ; ?> </label>&nbsp;
<label for="ezAdSenseShowWidget_no">
<input type="radio" id="ezAdSenseShowWidget_no" name="ezAdSenseShowWidget" value="no" <?php if ($this->options['show_widget'] == "no") { echo('checked="checked"'); }?> /> <?php _e('Suppress Widget', 'easy-adsenser') ; ?></label><br />
<label for="ezAdWidgetTitle"><b><?php _e('Widget Title:', 'easy-adsenser') ; ?></b>&nbsp; <input style="width:200px" id="ezAdWidgetTitle" name="ezAdWidgetTitle" type="text" value= "<?php echo(stripslashes(htmlspecialchars($this->options['title_widget']))) ?>" /></label>&nbsp;
<label for="ezAdKillWidgetTitle"><input type="checkbox" id="ezAdKillWidgetTitle" name="ezAdKillWidgetTitle" <?php if ($this->options['kill_widget_title']) { echo('checked="checked"'); }?> /> <?php _e('Hide Title', 'easy-adsenser') ; ?> </label>
</td>
</tr>
<tr style="vertical-align:top">
<td style="width:50%;height:220px;vertical-align:middle">
<b><?php _e('AdSense Link-Units Text', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Appears in the Sidebar as  Widgets)', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="ezAdSenseTextLU" style="width: 95%; height: 110px;"><?php echo(stripslashes(htmlspecialchars($this->options['text_lu']))) ?></textarea>
<br />
<span style="display:inline-block;width:70%"><b><?php _e('Ad Alignment', 'easy-adsenser') ; ?></b>&nbsp;<?php _e('(Where to show?)', 'easy-adsenser') ; ?></span>
<span onmouseover="Tip('<?php _e('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Tweak Margins', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Margin:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezLUMargin" name="ezLUMargin" value="<?php echo(stripslashes(htmlspecialchars($this->options['margin_lu'])));?>" />px</span>
<br />
<label for="ezAdSenseShowLU_left">
<input type="radio" id="ezAdSenseShowLU_left" name="ezAdSenseShowLU" value="text-align:left" <?php if ($this->options['show_lu'] == "text-align:left") { echo('checked="checked"'); }?> /> <?php _e('Align Left', 'easy-adsenser') ; ?> </label>&nbsp;
<label for="ezAdSenseShowLU_center">
<input type="radio" id="ezAdSenseShowLU_center" name="ezAdSenseShowLU" value="text-align:center" <?php if ($this->options['show_lu'] == "text-align:center") { echo('checked="checked"'); }?> /> <?php _e('Center', 'easy-adsenser') ; ?> </label>&nbsp;
<label for="ezAdSenseShowLU_right">
<input type="radio" id="ezAdSenseShowLU_right" name="ezAdSenseShowLU" value="text-align:right" <?php if ($this->options['show_lu'] == "text-align:right") { echo('checked="checked"'); }?> /> <?php _e('Align Right', 'easy-adsenser') ; ?> </label>&nbsp;
<label for="ezAdSenseShowLU_no">
<input type="radio" id="ezAdSenseShowLU_no" name="ezAdSenseShowLU" value="no" <?php if ($this->options['show_lu'] == "no") { echo('checked="checked"'); }?> /> <?php _e('Suppress Link Units', 'easy-adsenser') ; ?></label><br />
<label for="ezAdLUTitle"><b><?php _e('Link Unit Title:', 'easy-adsenser') ; ?></b>&nbsp; <input style="width: 200px;" id="ezAdLUTitle" name="ezAdLUTitle" type="text" value= "<?php echo(stripslashes(htmlspecialchars($this->options['title_lu']))) ?>" /></label>
<label for="ezAdKillLUTitle"><input type="checkbox" id="ezAdKillLUTitle" name="ezAdKillLUTitle" <?php if ($this->options['kill_lu_title']) { echo('checked="checked"'); }?> /> <?php _e('Hide Title', 'easy-adsenser') ; ?> </label>
</td>
</tr>
<tr style="vertical-align:top">
<td style="width:50%;height:250px;vertical-align:middle">
<b><?php _e('Google Search Widget', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Adds a Google Search Box to your sidebar)', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="ezAdSenseTextGSearch" style="width: 95%; height: 110px;"><?php echo(stripslashes(htmlspecialchars($this->options['text_gsearch']))) ?></textarea>
<br />
<span style="display:inline-block;width:70%"><b><?php _e('Search Title', 'easy-adsenser') ; ?></b>&nbsp;<?php _e('(Title of the Google Search Widget)', 'easy-adsenser') ; ?></span>
<span onmouseover="Tip('<?php _e('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Tweak Margins', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Margin:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezSearchMargin" name="ezSearchMargin" value="<?php echo(stripslashes(htmlspecialchars($this->options['margin_gsearch'])));?>" />px</span>
<br />
<label for="ezAdSenseShowGSearch_dark">
<input type="radio" id="ezAdSenseShowGSearch_dark" name="ezAdSenseShowGSearch" value="dark" <?php if ($this->options['title_gsearch'] == "dark") { echo('checked="checked"'); }?> />&nbsp; <?php echo '<img src=" ' . $this->plugindir . '/google-dark.gif" alt="Google (dark)" style="background:black;vertical-align:-40%;"'; ?> /> </label>&nbsp;
<label for="ezAdSenseShowGSearch_light">
<input type="radio" id="ezAdSenseShowGSearch_light" name="ezAdSenseShowGSearch" value="light" <?php if ($this->options['title_gsearch'] == "light") { echo('checked="checked"'); }?> />&nbsp; <?php echo '<img src=" ' . $this->plugindir . '/google-light.gif"  alt="Google (light)" style="background:white;vertical-align:-40%;"'; ?> /> </label>&nbsp;
<label for="ezAdSenseShowGSearch_no">
<input type="radio" id="ezAdSenseShowGSearch_no" name="ezAdSenseShowGSearch" value="no" <?php if ($this->options['title_gsearch'] == "no") { echo('checked="checked"'); }?> /> <?php _e('Suppress Search Box', 'easy-adsenser') ; ?></label><br /><br />
<label for="ezAdSenseShowGSearch_text">
<input type="radio" id="ezAdSenseShowGSearch_text" name="ezAdSenseShowGSearch" value="text" <?php $title = $this->options['title_gsearch'] ; if ($title != 'dark' && $title != 'light' && $title != 'no') { echo('checked="checked"'); }?> /> <b><?php _e('Custom Title:', 'easy-adsenser') ; ?></b></label>&nbsp;
<label for="ezAdSearchTitle">
<input style="width: 200px;" id="ezAdSearchTitle" name="ezAdSearchTitle" type="text" value= "<?php echo(stripslashes(htmlspecialchars($this->options['title_gsearch']))) ?>" /></label>
<label for="ezAdKillSearchTitle"><input type="checkbox" id="ezAdKillSearchTitle" name="ezAdKillSearchTitle" <?php if ($this->options['kill_gsearch_title']) { echo('checked="checked"'); }?> /> <?php _e('Hide Title', 'easy-adsenser') ; ?> </label>
</td>
</tr>
</table>

<table class="form-table">
<tr style="vertical-align:top">
<td style="width:50%;height:250px;vertical-align:middle;color:#d00;">

<?php echo '<b>Support Options<br /> ' ;  ?></b><br />

<b><?php _e('Link-backs to', 'easy-adsenser') ; ?> <a href="http://www.Thulasidas.com" target="_blank">Unreal Blog</a></b>
<?php _e('(Consider showing at least one link.)', 'easy-adsenser') ; ?><br />
<label for="ezAdSenseLinkMax99">
<input type="radio" id="ezAdSenseLinkMax99" name="ezAdSenseLinkMax" value="99" <?php if ($this->options['max_link'] == 99) { echo('checked="checked"'); }?> /> <?php _e('Show a link under every ad block.', 'easy-adsenser') ; ?></label><br />
<label for="ezAdSenseLinkMax1">
<input type="radio" id="ezAdSenseLinkMax1" name="ezAdSenseLinkMax" value="1" <?php if ($this->options['max_link'] == 1) { echo('checked="checked"'); }?> /> <?php _e('Show the link only under the first ad block.', 'easy-adsenser') ; ?></label><br />
<label for="ezAdSenseLinkMax-1">
<input type="radio" id="ezAdSenseLinkMax-1" name="ezAdSenseLinkMax" value="-1" <?php if ($this->options['max_link'] == -1) { echo('checked="checked"'); }?> /> <?php _e('Show the link at the bottom of your blog page.', 'easy-adsenser') ; ?></label><br />
<label for="ezAdSenseLinkMax0">
<input type="radio" id="ezAdSenseLinkMax0" name="ezAdSenseLinkMax" value="0" <?php if ($this->options['max_link'] == 0) { echo('checked="checked"'); }?> /> <?php _e('Show no links to my blog anywhere (Are you sure?!)', 'easy-adsenser') ; echo '</label>' ?>
<br />
<br style="line-height: 12px;" />
<?php echo '<span onmouseover="TagToTip(\'pro\', WIDTH, 350, TITLE, \'Buy the Pro Version\',STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 5, 5])"><b>Buy the <a href="http://buy.thulasidas.com/easy-adsense" target="_blank" ' . "onclick=\"popupwindow('http://buy.thulasidas.com/easy-adsense','Get Easy AdSense Pro', 1024, 768);return false;\"" . '>Pro Version</a></b><br />More features, more power!</span>' ; ?>
</td>
</tr>
</table>

</td>
</tr>
</table>

<div class="submit">
<input type="submit" name="update_ezAdSenseSettings" value="<?php _e('Save Changes', 'easy-adsenser') ?>" title="<?php _e('Save the changes as specified above', 'easy-adsenser') ?>" onmouseover="Tip('<?php _e('Save the changes as specified above', 'easy-adsenser') ?>',WIDTH, 240, TITLE, '<?php _e('Save Changes', 'easy-adsenser') ?>')" onmouseout="UnTip()"/>
<input type="submit" name="reset_ezAdSenseSettings" value="<?php _e('Reset Options', 'easy-adsenser') ?>" title="<?php _e('Discard all your changes and load defaults. (Are you quite sure?)', 'easy-adsenser') ?>"  onmouseover="TagToTip('help3',WIDTH, 240, TITLE, 'DANGER!', BGCOLOR, '#ffcccc', FONTCOLOR, '#800000',BORDERCOLOR, '#c00000')" onmouseout="UnTip()"/>
<input type="submit" name="clean_db"  value="<?php _e('Clean Database', 'easy-adsenser') ?>" onmouseover="TagToTip('help4',WIDTH, 280, TITLE, 'DANGER!', BGCOLOR, '#ffcccc', FONTCOLOR, '#800000',BORDERCOLOR, '#c00000')" onmouseout="UnTip()"/>
<input type="submit" name="kill_me"  value="<?php _e('Uninstall', 'easy-adsenser') ?>" onmouseover="TagToTip('help5',WIDTH, 280, TITLE, 'DANGER!', BGCOLOR, '#ffcccc', FONTCOLOR, '#800000',BORDERCOLOR, '#c00000')" onmouseout="UnTip()"/>
<?php echo $this->invite ;
if ($this->locale != "en_US") {?>
&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" title="Switch to English temporarily" src="<?php echo $this->plugindir ;?>/english.gif" style="vertical-align:-15px;" name="english" value="english" />
<?php } ?>
<hr />
</div>
</form>

<span id="help0">
1.
<?php
_e('Generate AdSense code (from http://adsense.google.com &rarr; AdSense Setup &rarr; Get Ads).', 'easy-adsenser') ;
?>
<br />
2.
<?php
_e('Cut and paste the AdSense code into the boxes below, deleting the existing text.', 'easy-adsenser') ;
?>
<br />
3.
<?php
_e('Decide how to align and show the code in your blog posts.', 'easy-adsenser') ;
?>
<br />
4. <?php
_e('Take a look at the Google policy option, and other options. The defaults should work.', 'easy-adsenser') ;
?>
<br />
5.
<?php
printf(__('If you want to use the widgets, drag and drop them at %s Appearance (or Design) &rarr; Widgets %s', 'easy-adsenser'), '<a href="widgets.php">', '</a>.') ;
?>
<br />
<b>
<?php
_e('Save the options, and you are done!', 'easy-adsenser') ;
?>
</b>
</span>

<span id="help1">
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
no', 'easy-adsenser') ;?>
</span>

<span id="help1a">
<?php _e('<em>Easy AdSense</em> gives you widgets to embelish your sidebars. You can configure them here (on the right hand side of the Options table below) and place them on your page using <a href="widgets.php"> Appearance (or Design) &rarr; Widgets</a>.
<br />
<br />
1. <b>AdSense Widget</b> is an ad block widget that you can place any where on the sidebar. Typically, you would put a skyscraper block (160x600px, for instance) on your sidebar, but you can put anything -- not necessarily AdSense code.<br />
<br />
2. <b>AdSense Link Units</b>, if enabled, give you multiple widgets to put <a href="https://www.google.com/adsense/support/bin/answer.py?hl=en&amp;answer=15817" target="_blank">link units</a> on your sidebars. You can display three of them according to Google AdSense policy, and you can configure the number of widgets you need.<br /><br />
3. <b>Google Search Widget</b> gives you another widget to place a <a href="https://www.google.com/adsense/support/bin/answer.py?hl=en&amp;answer=17960" target="_blank">custom AdSense search box</a> on your sidebar. You can customize the look of the search box and its title by configuring them on this page.', 'easy-adsenser') ;?>
</span>

<span id="help3">
<span style="color:red"><?php _e('This <b>Reset Options</b> button discards all your changes and loads the default options. This is your only warning!', 'easy-adsenser') ; ?></span><br />
<b><?php _e('Discard all your changes and load defaults. (Are you quite sure?)', 'easy-adsenser') ?></b></span>

<span id="help4">
<span style="color:red"><?php _e('The <b>Database Cleanup</b> button discards all your AdSense settings you have saved so far for <b>all</b> the themes, including the current one. Use it only if you know that you won\'t be using these themes. Please be careful with all database operations -- keep a backup.', 'easy-adsenser') ; ?></span><br />
<b><?php _e('Discard all your changes and load defaults. (Are you quite sure?)', 'easy-adsenser') ?></b></span>

<span id="help5">
<span style="color:red"><?php printf(__('The <b>Uninstall</b> button really kills %s after cleaning up all the options it wrote in your database. This is your only warning! Please be careful with all database operations -- keep a backup.', 'easy-adsenser'), '<em>Easy AdSense</em>') ; ?></span><br />
<b><?php _e('Kill this plugin. (Are you quite sure?)', 'easy-adsenser') ?></b></span>

<?php
if (!$this->options['kill_invites'])  {
  echo '<div style="background-color:#cff;padding:5px;border: solid 1px;margin:5px;">' ;
  @include (dirname (__FILE__).'/why-pro.php');
  echo '</div>' ;
}
?>

<div style="background-color:#fcf;padding:5px;border: solid 1px;margin:5px;">
<?php @include (dirname (__FILE__).'/support.php'); ?>
</div>

<?php @include (dirname (__FILE__).'/tail-text.php'); ?>

<table class="form-table" >
<tr><th scope="row"><b><?php _e('Credits', 'easy-adsenser'); ?></b></th></tr>
<tr><td>
<ul style="padding-left:10px;list-style-type:circle; list-style-position:inside;" >
<li>
<?php printf(__('%s uses the excellent Javascript/DHTML tooltips by %s', 'easy-adsenser'), '<b>Easy Adsense</b>', '<a href="http://www.walterzorn.com" target="_blank" title="Javascript, DTML Tooltips"> Walter Zorn</a>.') ;
?>
</li>
</ul>
</td>
</tr>
</table>

</div>
<?php
   }
