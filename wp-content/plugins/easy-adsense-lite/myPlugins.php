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

$myPlugins = array();

$needPHP5 = ' <br /> <span style="font-color:#c00;">Note that this plugin requires PHPv5.0+. Please use the Lite version first to ensure that it works before buying the Pro version. If it does not work on your web host, consider the amazing <a href="http://buy.thulasidas.com/easy-adsense/" title="The most popular plugin to insert AdSense on your blog"><em><strong>Easy AdSense Pro</strong></em></a> for all your advertising needs. It can insert non-AdSense blocks as well.</span>';

$myPlugins['ezpaypal'] = array('value' => 'ezPayPal',
            'price' => '9.95',
            'share' => false,
            'long' => false,
            'blurb' => '<em><strong>ezPayPal Pro</strong></em> is the simplest possible way to sell your digital goods online. This standalone PHP package ',
            'desc' => 'helps you quickly set up an online store to sell any downloadable item, where your buyers can pay for it and get an automatic, expiring download link. [ezPayPal is a standalone PHP package, which runs independent of WordPress.]',
            'title' => 'Do you have an application, PHP package, photograph, PDF book (or any other downloadable item) to sell? Find the set up of a shopping cart system too overwhelming? <em>ezPayPal</em> may be the right solution for you.',
            'pro' => 'The Pro version adds a whole slew of features: Data Security, Sandbox Mode, Template Editors, Automatic Handling of returns, refunds, e-chques etc, Sales Editor, Email Tools, Product Version support, Batch Product File Uploads, Data backup/restore/migration tools and so on. It can also be enhanced with optional modules like Affiliate Package, Reporting Tools etc. This powerful and professional package  provides you with a complete and robust solution for your online business.  <em><strong>ezPayPal Pro</strong></em> provides the most robust and feature-complete solution to sell your digital goods online. It helps you quickly set up an online store to sell any downloadable item, where your buyers can pay for it and get an automatic, expiring download link. The whole flow runs fully automated and designed to run unattended. <em><strong>ezPayPal</strong></em> manages all aspects of selling your digital goods.',
    'benefits' => '<li><em>Data Security</em>: The <em>Pro</em> version takes special measures to set up data verification links to ensure your sales data is safe and not susceptible to corruption. In technical terms, it checks for the existence of InnoDB in your MySQL installation, and uses it if found, setting up foreign keys to ensure referential integrity, and indices to guarantee performance. The Lite version uses the default MyISAM engine, fast and simple, but not exactly secure.</li>
<li><em>Sandbox Mode</em>: In the <em>Pro</em> version, you have the option to choose PayPal sandbox mode so that you can check your setup before going live.</li>
<li><em>DB Backup</em>: The <em>Pro</em> version has an option to generate a backup of your sales info to download to a safe location.</li>
<li><em>DB Restore</em>: It also provides a means to restore (of course) a previously backed up data file, overwriting (or appending to, as you wish) the existing sales info.</li>
<li><em>Security Audit</em>: The <em>Pro</em> version provides you with a tool to check your settings and installation for possible security issues.</li>
<li><em>Data Migration</em>: Using this <em>Pro</em> tool, your database tables can be automatically upgraded to the later version without losing your sales info and other settings. You will also get sample PHP files that can be used to migrate your data from text files into the database.</li>
<li><em>Template Editor</em>: The email body, thank you page and download display are all editable in the <em>Pro</em> version.</li>
<li><em>Uninstall Support</em>: In the unlikely event that you want to stop using ezPayPal, this <em>Pro</em> tool can help you clean up your database by deleting all the tables created during ezPayPal installation.</li>
<li><em>Additional Tools</em>: The <em>Pro</em> version also gives you a bunch of tools (php example files) that can help you migrate your existing sales data or product definitions.</li>
<li><em>Email facilities</em>: You can select a number of your buyers to notify, for example, of a critical update of your products, or of a free upgrade opportunity.</li>
<li><em>Upgradeable Products</em>: You can define products that are upgradeable. For instance, you can sell a short eBook at an introductory price. If your buyer likes it, he has the option of buying the full book by paying the difference.</li>
<li>And more!</li>');

$myPlugins['ezpaypal-pro'] =$myPlugins['ezpaypal']  ;

$myPlugins['easy-ads'] = array('value' => 'Easy Ads',
            'price' => '8.95',
            'share' => true,
            'long' => false,
            'blurb' => '<em><strong>Easy Ads</strong></em> is a multi-provider advertising plugin. This streamlined plugin ',
            'desc' => 'showcases AdSense and its alternatives on your blog',
            'title' => '<em><strong>Easy Ads</strong></em> provides a unified and intuitive interface to manage multiple ad providers on your blog. It lets you insert ads provided by <a href="http://www.clicksor.com/pub/index.php?ref=105268" title="Careful, do not double-date with AdSense and Clicksor, they get very jealous of each other!">Clicksor</a>, <a href="http://www.bidvertiser.com/bdv/bidvertiser/bdv_ref_publisher.dbm?Ref_Option=pub&amp;Ref_PID=229404" title="Another fine ad provider">BidVertiser</a> or <a href="http://chitika.com/publishers.php?refid=manojt" title="Compatible with AdSense">Chitika</a> into your existing and future blog posts and pages.',
            'pro' => 'The Lite version of <em><strong>Easy Ads</strong></em> is fully functional.  In the Pro version, you get a filter to minimize the chance of your AdSense and other accounts getting banned. It uses a fast and effective keyword matching algorithm to examine the contents of each page on the fly and determines whether the page content could look offensive to Google and others. If so, it prevents your ads from appearing on those pages. And you can tweak the strength of the algorithm (for each provider). The Pro version also gives you control over other global options like activating and deactivating various ad providers, resetting all options etc. The Pro version also lets you specify a list of computers where your ads will not be shown, in order to prevent accidental clicks on your own ads -- one of the main reasons the ad providers may ban you.' . $needPHP5);

$myPlugins['easy-paypal'] = array('value' => 'Easy PayPal',
            'price' => '6.95',
            'share' => false,
            'long' => false,
            'blurb' => '<em><strong>Easy PayPal</strong></em> is the plugin version of ezPayPal, the simplest possible way to sell your digital goods online. This premium plugin ',
            'desc' => 'helps you quickly set up an online store to sell any downloadable item, where your buyers can pay for it and get an automatic, expiring download link. [Easy PayPal is a Premium WordPress plugin.]',
            'title' => 'Do you have an application, PHP package, photograph, PDF book (or any other downloadable item) to sell from your blog? Find the set up of a shopping cart system too overwhelming? <em>ezPayPal</em> may be the right solution for you.',
            'pro' => 'The Pro version adds a whole slew of features: Data Security, Sandbox Mode, Template Editors, Automatic Handling of returns, refunds, e-chques etc, Sales Editor, Email Tools, Product Version support, Batch Product File Uploads, Data backup/restore/migration tools and so on. It can also be enhanced with optional modules like Affiliate Package, Reporting Tools etc. This powerful and professional package  provides you with a complete and robust solution for your online business.  <em><strong>ezPayPal Pro</strong></em> provides the most robust and feature-complete solution to sell your digital goods online. It helps you quickly set up an online store to sell any downloadable item, where your buyers can pay for it and get an automatic, expiring download link. The whole flow runs fully automated and designed to run unattended. <em><strong>ezPayPal</strong></em> manages all aspects of selling your digital goods.');

$myPlugins['google-adsense'] = array('value' => 'Google AdSense',
            'price' => '5.95',
            'share' => true,
            'long' => false,
            'blurb' => '<em><strong>Google AdSense</strong></em> is a single-provider version of <em><strong>Easy Ads</strong></em> specialized for Google AdSense serving. If you are planning to use more than two providers, it may be easier and more economical to use <em><strong>Easy Ads</strong></em>. <em><strong>Google AdSense</strong></em> ',
            'desc' => 'provides you with a fully streamlined interface to manage Google AdSense on your blog.',
            'title' => '<a href="http://buy.thulasidas.com/plugins/google-adsense/" title="A new plugin to handle Google"><em><strong>Google AdSense</strong></em></a> gives you a specialized and intuitive interface to manage AdSense ads on your blog, with size selectors, widget options, color-picker to customize your colors, etc. It is a new generation plugin with a fancy, tabbed interface.',
            'pro' => 'The Lite version of <em><strong>Google AdSense</strong></em> is fully functional.  But the Pro version gives you more features and control. In the Pro version, you get a filter to minimize the chance of your AdSense account getting banned. It uses a fast and effective keyword matching algorithm to examine the contents of each page on the fly and determines whether the page content could look offensive to Google. If so, it prevents your ads from appearing on those pages. And you can tweak the strength of the algorithm. The Pro version also lets you specify a list of computers where your ads will not be shown, in order to prevent accidental clicks on your own ads -- one of the main reasons AdSense bans you.' . $needPHP5);

$myPlugins['easy-chitika'] = array('value' => 'Easy Chitika',
            'price' => '4.75',
            'share' => true,
            'long' => false,
            'blurb' => '<em><strong>Easy Chitika</strong></em> is a single-provider version of <em><strong>Easy Ads</strong></em> specialized for Chitika serving. If you are planning to use more than two providers, it may be easier and more economical to use <em><strong>Easy Ads</strong></em>. <em><strong>Easy Chitika</strong></em> ',
            'desc' => 'provides you with a fully streamlined interface to manage Chitika ads on your blog.',
            'title' => '<a href="http://buy.thulasidas.com/plugins/easy-chitika/" title="A new plugin to handle Chitika"><em><strong>Easy Chitika</strong></em></a> gives you a specialized and intuitive interface to manage Chitika ads on your blog, with size selectors, widget options, color-picker to customize your colors, etc. It is a new generation plugin with a fancy, tabbed interface.',
            'pro' => 'The Lite version of <em><strong>Easy Chitika</strong></em> is fully functional.  But the Pro version gives you more features and control. In the Pro version, you get a filter to minimize the chance of your Chitka account getting banned. It uses a fast and effective keyword matching algorithm to examine the contents of each page on the fly and determines whether the page content could look offensive to Chitika. If so, it prevents your ads from appearing on those pages. And you can tweak the strength of the algorithm. The Pro version also lets you specify a list of computers where your ads will not be shown, in order to prevent accidental clicks on your own ads.' . $needPHP5);

$myPlugins['easy-adsense'] = array('value' => 'Easy AdSense',
            'price' => '4.95',
            'share' => true,
            'long' => true,
            'blurb' => '<em><strong>Easy AdSense</strong></em> is an updated version of a very popular (downloaded over 600,000 times) WordPress plugin. This premium plugin ',
            'desc' => 'manages all aspects of Google AdSense for your blog. Easy and complete!',
            'title' => '<em><strong>Easy AdSense</strong></em> provides a very easy way to generate revenue from your blog using Google AdSense. It can insert ads into posts and sidebar, and add a Google Search box. With its full set of features, <em><strong>Easy AdSense</strong></em> is perhaps the first plugin to give you a complete solution for everything AdSense-related.',
            'pro' => 'The Lite version of <em><strong>Easy AdSense</strong></em> is fully functional. But the Pro version gives you more features and control. In the Pro version, you get a filter to minimize the chance of your AdSense account getting banned. It uses a fast and effective keyword matching algorithm to examine the contents (including comments that you may have no control over) of each page on the fly and determines whether the page content could look offensive to Google. If so, it prevents your ads from appearing on those pages. And you can tweak the strength of the algorithm. The Pro version also lets you specify a list of computers where your ads will not be shown, in order to prevent accidental clicks on your own ads -- one of the main reasons AdSense bans you.',
            'benefits' => '<li>Safe Content filter: To ensure that your Google AdSense ads show only on those pages that seem to comply with Google AdSense policies, which can be important since some comments may render your pages inconsistent with those policies.</li>
<li>IP filter: Ability to specify a list of computers where your ads will not be shown, in order to prevent accidental clicks on your own ads -- one of the main reasons AdSense bans you. These features will minimize your chance of getting banned.</li>
<li>Compatibility mode: To solve the issue of the ad insertion messing up your page appearances when using some themes.</li>
<li>Shortcode support: Show the ads only on the pages or posts you want, and exactly where you want them.</li>
<li>Mobile support: Ability to suppress ads on mobile devices.</li>
<li>Ability to show a configurable number of ads on Excerpts (which make up the home page in some themes)</li>
<li>Real text-wrapping option in Leadout ad blocks. In the Lite version, text-wrapping in the lead-out ad block may fail in some cases.</li>'
        );

$myPlugins['adsense-now'] = array('value' => 'AdSense Now!',
            'price' => '3.95',
            'share' => true,
            'long' => true,
            'blurb' => '<em><strong>AdSense Now!</strong></em> is an updated version of another popular (downloaded about 150,000 times) WordPress plugin. This premium plugin ',
            'desc' => 'gets you started with Google AdSense. No mess, no fuss.',
            'title' => '<em><strong>AdSense Now!</strong></em> is the simplest possible way to generate revenue from your blog using Google AdSense. Aiming at simplicity, <em><strong>AdSense Now!</strong></em> does only one thing: it puts your AdSense code in up to three spots in your posts and pages (both existing ones and those yet to be written).',
            'pro' => 'The Lite version of <em><strong>AdSense Now!</strong></em> is fully functional. In the Pro version, you get a filter to minimize the chance of your AdSense account getting banned. It uses a fast and effective keyword matching algorithm to examine the contents of each page on the fly and determines whether the page content could look offensive to Google. If so, it prevents your ads from appearing on those pages. And you can tweak the strength of the algorithm. The Pro version also lets you specify a list of computers where your ads will not be shown, in order to prevent accidental clicks on your own ads -- one of the main reasons AdSense bans you.',
            'benefits' => '<li>A safety filter to help you maintain your AdSense account standing. This fast and efficient filter will help keep your AdSense account in good standing by suppressing your ads on pages that may violate Google policies. For instance, if a visitor leaves a comment deemed offensive by Google, this filter will kick in as remove your ads from that page.</li>
<li>Ability to suppress your ads on some IPs to prevent accidental clicks on your own ads -- one of the main reasons for getting your AdSense account banned. It will also help prevent intentional clicks (by your jealous competitor, for instance).</li>
<li>A compatibility mode, if the ad insertion messes up the page layout. Some poorly coded themes may get your pages messed up by ad insertion. The compatibility mode will help prevent it.</li>');

$myPlugins['theme-tweaker'] = array('value' => 'Theme Tweaker',
            'price' => '3.95',
            'share' => false,
            'long' => true,
            'blurb' => '<em><strong>Theme Tweaker</strong></em> is a remarkable plugin that ',
            'desc' => 'lets you modify the colors in your theme with no CSS/PHP editing.',
            'title' => '<em><strong>Theme Tweaker</strong></em> displays the existing colors from your current theme, and gives you a color picker to replace them. It also lets you change them in bulk, like invert all colors, use grey scale etc.',
            'pro' => 'Note that <em><strong>Theme Tweaker</strong></em> may not work with some themes. Please verify its suitability using the Lite version first. The Lite version of the plugin is fully functional. The Pro version lets you create and save your tweaked <code>style.css</code> files, and even generate your own child themes!',
            'benefits' => '<li>Ability to generate and download <code>style.css</code> files with your modified colors.</li>
<li>Ability to create a child theme so that your changes can be applied even when the underlying theme is updated.</li>
<li>Scanning for *all* the style files in your theme directory to find all possible color definitions.</li>');

$myPlugins['easy-quiz'] = array('value' => 'Easy Quiz',
            'price' => '2.95',
            'share' => false,
            'long' => true,
            'blurb' => '<em><strong>Easy Quiz</strong></em> is a jQuery quiz plugin that ',
            'desc' => 'runs a simple but elegant quiz on your posts or pages.',
            'title' => '<em><strong>Easy Quiz</strong></em> displays the statements or questions you type into your post or page (surrounded by <code>[ezquiz][/ezquiz]</code> tag) as a neat quiz for your reader on his/her browser. The answers are not transfered to your server, and there is no extra server load in running the quiz.',
            'pro' => '<em><strong>Easy Quiz</strong></em>, in its lite form, comes with a standard color scheme. If you would like to modify the color scheme, please consider the Pro version. It gives you color pickers for all aspects of the quiz display so that you can perfectly match your theme. It also lets you have different types of quizes (true-or-false, fill-in-the-blanks, multiple-choice, etc.), which you can even mix and match within one test. The Lite version of the plugin is fully functional, but is limited to only true-or-false questions.',
            'benefits' => '<li>Different types of quizes (true-or-false, fill-in-the-blanks, multiple-choice, etc.).</li>
<li>Ability to mix and match different types of questions within one quiz.</li>
<li>Ability to tweak the quiz display colors.</li>
<li>Color pickers for customization with live preview on the admin page.</li>');

$myPlugins['easy-quiz-pro'] = $myPlugins['easy-quiz'];

$myPlugins['easy-text-links'] = array('value' => 'Easy Text Links',
            'price' => '7.95',
            'share' => false,
            'long' => true,
            'blurb' => '<em><strong>Easy Text Links</strong></em> is a robust and modern advertising plugin that ',
            'desc' => 'helps you sell and manage text links on your blog.',
            'title' => '<em><strong>Easy Text Links</strong></em> helps you make extra revenue from your blog by selling text links. Text link advertising can be significantly more lucrative than contextual ads. This plugin automates the insertion and expiration of the links, and helps you with quick reminder emails to your advertisers. If you get a lot of advertising enquiries for text links, this is the right plugin for you.',
            'pro' => '<em><strong>Easy Text Links</strong></em>, in its light form, is already a powerful plugin. The Pro version lets you integrate seamlessly with Easy PayPal and fully automate your link sales, expiration and reminder emails.',
            'benefits' => '<li>Automated link sales and management.</li>
<li>Automated email reminders, and subscription based links.</li>
<li>Choice of several attractive Advertise Here images.</li>
<li>Dedicated dedicated and multi-insertable widget for displaying your links.</li>');

$myPlugins['easy-text-links-pro'] = $myPlugins['easy-text-links'];

$myPlugins['easy-latex'] = array('value' => 'Easy WP LaTeX',
            'price' => '2.95',
            'share' => false,
            'long' => true,
            'blurb' => '<em><strong>Easy WP LaTeX</strong></em> is a premium plugin that ',
            'desc' => 'provides a very easy way to display math and equations in your posts.',
            'title' => '<em><strong>Easy WP LaTeX</strong></em> provides a very easy way to display equations or mathematical formulas (typed in as TeX or LaTeX code) in your posts. It translates LaTeX formulas like this [math](a+b)^2 = a^2 + b^2 + 2ab[/math] into this:<br/>&nbsp;&nbsp;&nbsp;&nbsp;<img src="http://l.wordpress.com/latex.php?latex=(a%2bb)^2%20=%20a^2%20%2b%20b^2%20%2b%202ab&amp;bg=E2E7FF&amp;s=1" style="vertical-align:-70%;" alt="(a+b)^2 = a^2 + b^2 + 2ab" />',
            'pro' => 'The Lite version of the plugin is fully functional. The Pro version gives you options to cache the equation images so that your pages load faster.',
            'benefits' => '<li>Pre-configured cache directory.</li>
<li>Ability to turn control links on formulas.</li>');

$myPlugins['easy-translator'] = array('value' => 'Easy Translator',
            'price' => '3.95',
            'share' => false,
            'long' => true,
            'blurb' => '<em><strong>Easy Translator</strong></em> ',
            'desc' => 'is a blog post translator and a plugin translation tool for authors and translators.',
            'title' => '<em><strong>Easy Translator</strong></em> is a plugin to translate other plugins as well as blog posts. For blog posts and pages, it provides a customizable widget to enable machine translation (from Google or Microsoft). For plugins, it picks up translatable strings (in _[_e]() functions) and presents them and their existing translations (from the MO object of the current text-domain, if loaded) in a user editable form. It can generate a valid PO file that can be emailed to the plugin author directly from the its window, streamlining your work.',
            'pro' => 'The Lite version of Easy Translator is fully functional. The Pro version adds machine translation help from Google, the ability to email the generated PO file directly, without having to save it and attach it to a mail message,  and color pickers to match the widget colors to your blog theme.',
            'benefits' => '<li>Ability to mail translations to the plugin authors</li>
<li>Machine translation for strings that do not have human translations yet.</li>
<li>Blog page translation widget.</li>');

$myPlugins['blackjack'] = array('value' => 'Blackjack',
            'price' => '4.99',
            'share' => false,
            'long' => true,
            'blurb' => '<em><strong>Blackjack</strong></em> ',
            'desc' => 'shows a simple Blackjack game to your readers.',
            'title' => '<em><strong>Blackjack</strong></em>  is a quick and easy plugin to present an attractive Blackjack game to your readers. It is based on HTML5/jQuery, and the whole game is played on the browser. Your server is gets no extra load, nor is anything stored in your database.',
            'pro' => 'The Lite version of Blackjack is fully functional. The Pro version adds color customization, strategy hints and other features related to simulation, winnings accumulation etc. It will also give you revenue generation opportunities by selling these additional features to your readers.');

$myPlugins['unreal-universe'] = array('value' => 'The Unreal Universe - eBook',
            'url' => 'http://www.theunrealuniverse.com',
            'amazon' => 'http://www.amazon.com/exec/obidos/ASIN/9810575947/unrblo-20',
            'price' => '1.49',
            'share' => false,
            'long' => true,
            'blurb' => '<em><strong>The Unreal Universe</strong></em> is a remarkable book on physics and philosophy, science and religion. This compelling read ',
            'desc' => 'will change the way you look at reality and understand the universe around you. Ever wonder why nothing can faster than light? And the Earth was void until God said "Let there be light"? Here are some of the answers.',
            'title' => '<em><strong>The Unreal Universe</strong></em> is a remarkable book on physics, philosophy and surprising interconnections among seemingly disconnected silos of human knowledge.',
            'pro' => '',
            'kind' => 'book');

$myPlugins['pqd'] = array('value' => 'How Does a Bank Work? - eBook',
            'url' => 'http://pqd.thulasidas.com',
            'amazon' => 'http://www.amazon.com/exec/obidos/ASIN/0470745703/unrblo-20',
            'price' => '5.49',
            'share' => false,
            'long' => true,
            'blurb' => 'This eBook companion to <em><strong>Principles of Quantitative Development</strong></em> is a lucid and succinct exposé on the trade life cycle and the business groups involved in managing it, bringing together the big picture of how a trade flows through the systems, and the role of a quantitative professional in the organization. This compelling book ',
            'desc' => 'looks at the need and demand for in-house trading platforms, addressing the current trends in the industry. It then looks at the trade life cycle and its participants, from beginning to end, and then the functions within the front, middle and back office, giving the reader a full understanding and appreciation of the perspectives and needs of each function.',
            'title' => '<em><strong>Principles of Quantitative Development</strong></em> has been enthusiastically endorsed by the leading professionals in the quantitative finance space, including <strong>Paul Wilmott</strong>.',
            'pro' => '',
            'kind' => 'book');

$myPlugins['iphoto-tagger'] = array('value' => 'iPhoto Tagger -- Helper for iPhoto imports',
            'price' => '1.99',
            'share' => false,
            'long' => true,
            'blurb' => '<em><strong>iPhotoTagger</strong></em> is for the budding photographer in you. This Mac program ',
            'desc' => 'will make your life a lot easier if you have to import your existing pohtos into your iPhoto Library. It highlights the import status of your photos using an easy-to-follow color scheme.',
            'title' => '<em><strong>iPhoto Tagger</strong></em> is a native Mac Application to locate and tag your existing photos depending on whether they have been imported into your iPhoto Library. It first goes through your iPhoto library and catalogs what you have there. It then scans the folder you specify and compares the photos in there with those in your library. If a photo is found exactly once, it will get a Green label, so that it stands out when you browse to it in your Finder (which is Mac-talk for Windows Explorer). Similarly, if the photo appears more than once in your iPhoto library, it will be tagged in Yellow. And, going the extra-mile, iPhotoTagger will color your folder Green if all the photos within have been imported into your iPhoto library. Those folders that have been partially imported will be tagged Yellow.',
            'pro' => 'The Lite version of <em><strong>iPhoto Tagger</strong></em> is fully functional and makes the lists of the photos to be tagged. The Pro version adds the ability to automatically label those photos using Spotlight colors so that they stand out while browsing in Finder.',
            'kind' => 'app');

$this->myPlugins = $myPlugins;
