<?php // GA Google Analytics - Show Support Panel

if (!function_exists('add_action')) die();

$plugin_project = 'Google Analytics';

$plugin_url = plugin_dir_url(__FILE__);

$array = array(
			
	0  => '<a target="_blank" href="https://plugin-planet.com/bbq-pro/" title="Premium WP Plugin: BBQ Pro">
				<img width="125" height="125" src="'. $plugin_url .'images/250x250-bbq-pro.jpg" alt="BBQ Pro - Block Bad Queries" />
			</a>',
	1  => '<a target="_blank" href="https://plugin-planet.com/blackhole-pro/" title="Premium WP Plugin: Blackhole Pro">
				<img width="125" height="125" src="'. $plugin_url .'images/250x250-blackhole-pro.jpg" alt="Blackhole Pro - Block Bad Bots" />
			</a>',
	2  => '<a target="_blank" href="https://plugin-planet.com/ses-pro/" title="Premium WP Plugin: SES Pro">
				<img width="125" height="125" src="'. $plugin_url .'images/250x250-ses-pro.jpg" alt="SES Pro - Ajax-Powered Email Signup Forms" />
			</a>',
	3  => '<a target="_blank" href="https://plugin-planet.com/usp-pro/" title="Premium WP Plugin: USP Pro">
				<img width="125" height="125" src="'. $plugin_url .'images/250x250-usp-pro.jpg" alt="USP Pro - Unlimited Front-End Forms" />
			</a>',
		
	4  => '<a target="_blank" href="https://digwp.com/" title="Take your WordPress Skills to the Next Level">
				<img width="125" height="125" src="'. $plugin_url .'images/250x250-digging-into-wordpress.jpg" alt="Digging Into WordPress" />
			</a>',
	5  => '<a target="_blank" href="https://wp-tao.com/" title="Learn the Way of WordPress">
				<img width="125" height="125" src="'. $plugin_url .'images/250x250-tao-of-wordpress.jpg" alt="The Tao of WordPress" />
			</a>',
	6  => '<a target="_blank" href="https://wp-tao.com/wordpress-themes-book/" title="WordPress Themes In Depth">
				<img width="125" height="125" src="'. $plugin_url .'images/250x250-wp-themes-in-depth.jpg" alt="WordPress Themes In Depth" />
			</a>',
	7  => '<a target="_blank" href="https://htaccessbook.com/" title="Optimize and Secure with .htaccess">
				<img width="125" height="125" src="'. $plugin_url .'images/250x250-htaccess-made-easy.jpg" alt=".htaccess made easy" />
			</a>',
	
);
		
$items = array_rand($array, 3);

$item1 = isset($array[$items[0]]) ? $array[$items[0]] : 0;
$item2 = isset($array[$items[1]]) ? $array[$items[1]] : 1;
$item3 = isset($array[$items[2]]) ? $array[$items[2]] : 2;

$message = 'Thank you for using '. $plugin_project .'! Please show support by purchasing one of my 
			<a target="_blank" href="https://wp-tao.com/store/" title="Perishable Press Books">books</a> or 
			<a target="_blank" href="https://plugin-planet.com/store/" title="Plugin Planet">plugins</a>, 
			or by making a <a target="_blank" href="https://m0n.co/donate" title="Donate via PayPal">donation</a>. 
			Your generous support helps to ensure future development of '. $plugin_project .' and is greatly appreciated.';

$donate = 'Any size donation helps me to continue developing this free plugin and other awesome WordPress resources.';

?>

<style type="text/css">
	#project-wrap { width: 100%; overflow: hidden; }
	#project-wrap p { margin-top: 5px; font-size: 12px; }
	#project-wrap .project-support { float: left; max-width: 480px; }
	
	#project-wrap .project-links { width: 100%; overflow: hidden; margin: 15px 0; }
	#project-wrap .project-links img { display: block; width: 125px; height: 125px; margin: 0; padding: 0; border: 0; background-color: #fff; color: #fff; }
	#project-wrap .project-links a { float: left; width: 125px; height: 125px; margin: 0 0 0 15px; padding: 1px; border: 1px solid #ccc; opacity: 0.9; }
	#project-wrap .project-links a:hover { opacity: 1.0; }
	
	#project-wrap .project-blurb { 
		float: left; width: 220px; box-sizing: border-box; margin: 0 0 25px 20px; padding: 15px 20px; border-radius: 5px;
		background-color: #fefefe; border: 1px solid #ccc; box-shadow: 0 20px 25px -20px rgba(0,0,0,0.7);
		}
	#project-wrap .project-blurb a { text-decoration: none; }
	#project-wrap .project-blurb a:hover { text-decoration: underline; }
	#project-wrap .project-blurb p { margin-left: 0; margin-right: 0; }
	#project-wrap .project-blurb p:first-child { margin: 0 0 10px 0; font-size: 13px; }
	#project-wrap .project-blurb ul { margin: 0; padding: 0; font-size: 12px; }
	#project-wrap .project-blurb li { margin: 5px 0; list-style: none; }
	
	@media (max-width: 520px) {
		#project-wrap .project-links a { margin-bottom: 15px; }
	}
</style>
<div id="project-wrap">
	<div class="project-support">
		<div class="project-message">
			<p><?php echo $message; ?></p>
		</div>
		<div class="project-links">
			<?php echo $item1 . $item2 . $item3; ?>
		</div>
	</div>
	<div class="project-blurb">
		<p><strong>Please Donate</strong></p>
		<p><?php echo $donate; ?></p>
		<ul>
			<li><a target="_blank" href="https://m0n.co/donate">Donate via PayPal &raquo;</a></li>
			<li><a target="_blank" href="https://m0n.co/bitcoin">Donate via Bitcoin &raquo;</a></li>
		</ul>
	</div>
</div>
