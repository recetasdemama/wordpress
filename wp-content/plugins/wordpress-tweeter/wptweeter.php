<?php
/*
Plugin Name: WordPress Tweeter
Plugin URI: http://www.fusionswift.com/wordpress/wordpress-tweeter/
Description: WordPress Tweeter tweets every time you make a new post on your blog. Make sure you read the <a href="http://www.fusionswift.com/wordpress/wordpress-tweeter/" title="WordPress Tweeter">documentations</a> before using this plugin. The changelog, installation instructions, and any other plugin related information is there.
Version: 0.8.3
Author: Tech163
Author URI: http://www.fusionswift.com/
*/

function wp_tweeter() {
	global $wptweeterservices;
	$wptweeterservices = array(
		'Bit.ly' => 'http://api.bit.ly/api?url=%url%',
		'WP Shortlink' => '',
		'TinyURL' => 'http://tinyurl.com/api-create.php?url=%url%',
		'is.gd' => 'http://is.gd/api.php?longurl=%url%',
		'cli.gs' => 'http://cli.gs/api/v1/cligs/create?url=%url%',
		'short.to' => 'http://short.to/s.txt?url=%url%',
		'chilp.it' => 'http://chilp.it/api.php?url=%url%',
		'th8.us' => 'http://th8.us/api.php?url=%url%',
		'minify.us' => 'http://minify.us/api.php?u=%url%',
		'Metamark' => 'http://metamark.net/api/rest/simple?long_url=%url%'
	);
}

function wp_tweeter_checkstatus($id) {
	global $wptweeterstatus, $wptweeteroptions;
	$wptweeteroptions = get_option('wp_tweeter');
	$wptweeterstatus = get_post_status($id);
}

function wp_tweeter_new($status) {
	global $wptweeteroptions;
	$token = $wptweeteroptions['token'];
	$tokensecret = $wptweeteroptions['tokensecret'];
	$status = substr($status, 0, 140);
	

	$response = wp_remote_get('http://lab.sliceone.com/29308/tweet?oauth_token=' . $token . '&oauth_token_secret=' . $tokensecret . '&status=' . urlencode($status));
	if(!is_wp_error( $response ) ) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.twitter.com/1.1/statuses/update.json");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $response['body']);
		curl_exec($ch);
		curl_close($ch);
	}
	
}

function wp_tweeter_tags() {
	global $wptweeteroptions, $wptweetertags;
	for($i = 0; $i < $wptweeteroptions['tagnumdefault']; $i++) {
		if(!empty($wptweetertags[$i]->slug)) {
			$out .= '#' . str_replace('-', $wptweeteroptions['tagspace'], $wptweetertags[$i]->slug) . ' ';
		}
	}
	return trim($out);
}

function wp_tweeter_tweet($tpl) {
	global $wptweeteroptions, $wptweeterservices, $wptweeterpost, $wptweetertags;
	$wptweeterpermalink = get_permalink($wptweeterpost->ID);
	$title = $wptweeterpost->post_title;

	$token = $wptweeteroptions['token'];
	$tokensecret = $wptweeteroptions['tokensecret'];
	$parameter = $wptweeteroptions['parameter'];
	if(!empty($parameter)) {
		$parameter = str_replace('%date%', date('Ymd'), $parameter);
		$parameter = str_replace('%time%', date('Gis'), $parameter);
		$parameter = str_replace('%blogtitle%', sanitize_title_with_dashes(get_bloginfo('name')), $parameter);
		$parameter = str_replace('%posttitle%', sanitize_title_with_dashes($title), $parameter);
		if($parameter[0] != '?' && $parameter[0] != '&') {
			$permalink = get_option('permalink_structure');
			if(empty($permalink)) {
				$parameter = '&' . $parameter;
			} else {
				$parameter = '?' . $parameter;
			}
		}
	}
	
	foreach($wptweeterservices as $key => $value) {
		if(preg_replace('/[^a-z0-9]/', '', strtolower(trim($key))) == $wptweeteroptions['service']) {
			$shortener = $value;
		}
	}
	
	if($wptweeteroptions['service'] == 'bitly') {
		if(!empty($wptweeteroptions['bitlyuser']) && !empty($wptweeteroptions['bitlyapi'])) {
			$shortener = 'http://api.bit.ly/v3/shorten/?format=txt&apikey=' . urlencode($wptweeteroptions['bitlyapi']) . '&login=' . urlencode($wptweeteroptions['bitlyuser']) . '&longUrl=%url%';
		} else {
			$shortener = 'http://tinyurl.com/api-create.php?url=%url%';
		}
	}
		
	if($wptweeteroptions['service'] == 'none') {
		$homeurl = get_option('home'); 
		$posturl = $wptweeterpermalink;
	} elseif($wptweeteroptions['service'] == 'wpshortlink') {
		$homeurl = get_option('home');
		$posturl = $homeurl . '/?p=' . $wptweeterpost->ID;
	} else {
		if(preg_match('/%url%/', $tpl)) {
			$exec = wp_remote_get(str_replace('%url%', urlencode(get_option('home') . $parameter), $shortener));
			$homeurl = $exec['body'];
		}
		if(preg_match('/%posturl%/', $tpl)) {
			$exec = wp_remote_get(str_replace('%url%', urlencode($wptweeterpermalink . $parameter), $shortener));
			$posturl = $exec['body'];
		}
	}
	
	$status = str_replace('%url%', $homeurl, $tpl);
	$status = str_replace('%posturl%', $posturl, $status);
	$dateformat = get_option('timezone_string');
	if(!empty($dateformat)) {
		date_default_timezone_set($dateformat);
	}
	$status = str_replace('%date%', date(get_option('date_format')), $status);
	$status = str_replace('%time%', date(get_option('time_format')), $status);
	$status = str_replace('%blogtitle%', get_bloginfo('name'), $status);
	$maxlen = 137 - strlen(preg_replace('/%(.*)%/', '', $status));
	$status = str_replace('%posttitle%', (strlen($title) > $maxlen ? substr($title, 0, $maxlen) . '...' : $title), $status);
	if(preg_match('/%tags%/', $status)) {
		$wptweetertags = get_the_tags($wptweeterpost->ID);
		$tagout = wp_tweeter_tags();
		$newstatus = str_replace('%tags%', $tagout, $status);
		while(strlen($newstatus) > 140) {
			$wptweeteroptions['tagnumdefault']--;
			$tagout = wp_tweeter_tags();
			$newstatus = str_replace('%tags%', $tagout, $status);
		}
		$status = str_replace('%tags%', $tagout, $status);
	}
	wp_tweeter_new($status);
}

function wp_tweeter_update($id) {
	global $wptweeteroptions, $wptweeterstatus, $wptweeterpost;
	$wptweeteroptions = get_option('wp_tweeter');
	
	$wptweeterpost = get_post($id);
	if(!empty($wptweeteroptions['tagblacklist'])) {
		$wptweeteroptions['tagblacklist'] = str_replace("\r", '', $wptweeteroptions['tagblacklist']);
		$tagblacklist = explode("\n", strtolower($wptweeteroptions['tagblacklist']));
		$tags = get_the_tags($id);
		if(is_array($tags)) {
			foreach($tags as $tag) {
				if(in_array(trim($tag->name), $tagblacklist)) {
					return ;
				}
			}
		}
	}
	
	if(!empty($wptweeteroptions['catblacklist'])) {
		$wptweeteroptions['catblacklist'] = str_replace("\r", '', $wptweeteroptions['catblacklist']);
		$catblacklist = explode("\n", strtolower($wptweeteroptions['catblacklist']));
		$categories = get_the_category($id);
		if(is_array($categories)) {
			foreach($categories as $category) {
				if(in_array(trim(strtolower($category->name)), $catblacklist)) {
					return ;
				}
			}
		}
	}

	if(!empty($wptweeterpost->post_password) && $wptweeteroptions['pswdpost'] != 'true') {
		return;
	}
	if(!empty($_POST['customtweettpl'])) {
		$tweettpl = $_POST['customtweettpl'];
	} else {
		if($wptweeterstatus != 'publish') {
			$tweettpl = $wptweeteroptions['tpl'];
		} else {
			$tweettpl = $wptweeteroptions['updatetpl'];
		}
	}
	if($_POST['dotweet'] == 'default' || empty($_POST['dotweet'])) {
		if($wptweeterstatus != 'publish') {
			wp_tweeter_tweet($tweettpl);
		} elseif($wptweeteroptions['postupdate'] == 'true' && $wptweeterstatus == 'publish') {
			wp_tweeter_tweet($tweettpl);
		}
	} elseif($_POST['dotweet'] == 'yes') {
		wp_tweeter_tweet($tweettpl);
	}
}

function wp_tweeter_admin() {
	$wptweeteroptions = get_option('wp_tweeter');
	echo '<div class="wrap"><h2>WordPress Tweeter</h2>';
	if(empty($_GET['oauth_token']) && empty($wptweeteroptions)) {
		$response = wp_remote_get('http://lab.sliceone.com/29308/request-token?oauth_callback=' . admin_url('options-general.php?page=wptweeter.php'));
		$response = wp_remote_get('https://api.twitter.com/oauth/request_token?' . $response['body']);
		parse_str($response['body']);
		$link = 'https://api.twitter.com/oauth/authorize?oauth_token=' . $oauth_token;
	?>
	<style type="text/css">
		.twittersignin {
			background: url(//www.fusionswift.com/files/2010/03/twitter_signin.png);
			width: 150px;
			height: 22px;
			display: block;
		}
		.twittersignin:hover {
			background-position: 0 -24px; 
		}
		.twittersignin:active {
			background-position: 0 -48px; 
		}
	</style>
	<?php 
		echo '<a href="' . $link . '"><span class="twittersignin"></span></a>';
	} elseif(isset($_POST['uninstall'])) {
		if($_POST['uninstall'] == 'Uninstall') {
			wp_tweeter_uninstall();
		}
	} elseif(isset($_POST['step']) && $_POST['step'] == 'activate') {
		$token = $wptweeteroptions['token'];
		$tokensecret = $wptweeteroptions['tokensecret'];
		if($_POST['tellworld'] == 'true') {
			$exec = wp_remote_get('http://api.fusionswift.com/29308/tweet.php?oauth_token=' . urlencode($token) . '&oauth_token_secret=' . urlencode($tokensecret) . '&status=I+just+installed+WordPress+Tweeter%2C+which+tweets+every+time+I+make+a+new+post.+You+can+find+it+at+http%3A%2F%2Fbit.ly%2FcHpKR2+%21');
		}
		if($_POST['followdev'] == 'true') {
			$exec = wp_remote_get('http://api.fusionswift.com/29308/follow.php?oauth_token=' . urlencode($token) . '&oauth_token_secret=' . urlencode($tokensecret));
		}
		wp_tweeter_form();
	} else {
		wp_tweeter_form();
	}
	echo '</div>';
}

function wp_tweeter_new_tweet() {
	echo '<div class="wrap"><h2>WordPress Tweeter - New Tweet</h2>';
	if(!empty($_POST['tweet'])) {
		global $wptweeteroptions;
		$wptweeteroptions = get_option('wp_tweeter');
		wp_tweeter_new(stripslashes($_POST['tweet']));
		echo '<div id="message" class="updated fade"><p><strong>The tweet has been tweeted.</strong></p></div>';
	} ?>
	<form action="" method="post">
	<?php wp_nonce_field('update-options'); ?>
	<script type="text/javascript">eval(function(p,a,c,k,e,r){e=function(c){return c.toString(36)};if('0'.replace(0,e)==0){while(c--)r[e(c)]=k[c];k=[function(e){return r[e]||e}];e=function(){return'[1-9a-i]'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('4=140;var bName=navigator.appName;8 taLimit(7){3(7.d.9==4)1 false;1 e}8 taCount(7,f){a=g(f);5=7.d;3(5.9>4)5=5.substring(0,4);3(a){a.innerText=4-5.9}1 e}8 g(6){3(2.h)1 2.h(6);b 3(2.layers)1 c("2."+6);b 3(2.i)1 c("2.i."+6);b 1 c("2."+6)}',[],19,'|return|document|if|maxL|objVal|objId|taObj|function|length|objCnt|else|eval|value|true|Cnt|createObject|getElementById|all'.split('|'),0,{}))</script>
	<p><textarea rows="3" cols="50" name="tweet" onKeyPress="return taLimit(this)" onKeyUp="return taCount(this,'tweetcounter')" ></textarea></p>
	<p><span id="tweetcounter">140</span> characters remaining </p>
	<p><input type="submit" value="TWEET!" /></p>
	</form>
	<p>Read the <a href="http://www.fusionswift.com/wordpress/wordpress-tweeter/" target="_blank">documentation</a> for information regarding this feature.</p>
	<?php echo '</div>';
}

function wp_tweeter_form() {
	global $wptweeterservices;
	if(!empty($_POST['template'])) {
		$wptweeteroptions = get_option('wp_tweeter');
		$postedoptions = array(
			'service' => $_POST['service'],
			'username' => $wptweeteroptions['username'],
			'token' => $wptweeteroptions['token'],
			'tokensecret' => $wptweeteroptions['tokensecret'],
			'parameter' => $_POST['parameter'],
			'tpl' => $_POST['template'],
			'postupdate' => $_POST['postupdate'],
			'pswdpost' => $_POST['pswdpost'],
			'updatetpl' => stripslashes($_POST['updatetpl']),
			'lasttime' => $wptweeteroptions['lasttime'],
			'notice' => $wptweeteroptions['notice'],
			'tagspace' => $_POST['tagspace'], 
			'tagnumdefault' => $_POST['tagnumdefault'],
			'bitlyuser' => $_POST['bitlyuser'],
			'bitlyapi' => $_POST['bitlyapi'],
			'tagblacklist' => $_POST['tagblacklist'],
			'catblacklist' => $_POST['catblacklist'],
			'version' => $wptweeteroptions['version'],
			'autocheck' => $_POST['autocheck']
		);
		foreach($postedoptions as $key=>$theoption) {
			$update[$key] = stripslashes($theoption);
		}
		update_option('wp_tweeter', $update);
		echo '<div id="message" class="updated fade"><p><strong>WordPress Tweeter settings saved.</strong></p></div>';
	}
	$wptweeteroptions = get_option('wp_tweeter');
	if(!empty($_GET['oauth_token']) && !empty($_GET['oauth_verifier'])) {
		$response = wp_remote_get('http://lab.sliceone.com/29308/access-token?oauth_token=' . $_GET['oauth_token'] . '&oauth_verifier='  . $_GET['oauth_verifier']);
		$response = wp_remote_get('https://api.twitter.com/oauth/access_token?' . $response['body']);
		parse_str($response['body'], $access_token_data);
		if(empty($wptweeteroptions)) {
			$default = array(
				'service' => 'tinyurl',
				'username' => $access_token_data['screen_name'],
				'token' => $access_token_data['oauth_token'],
				'tokensecret' => $access_token_data['oauth_token_secret'],
				'parameter' => '',
				'tpl' => '%blogtitle% New Post - %posttitle%. Read it now at %posturl%',
				'postupdate' => '',
				'pswdpost' => '',
				'updatetpl' => 'Post updated %date% - %posttitle%. Read it now at %posturl%',
				'lasttime' => time() - 90000,
				'notice' => '',
				'tagspace' => '_', 
				'tagnumdefault' => 3,
				'bitlyuser' => '',
				'bitlyapi' => '',
				'tagblacklist' => '',
				'catblacklist' => '',
				'autocheck' => 'true'
			);
		} else {
			$default = $wptweeteroptions;
			$default['username'] = $access_token_data['screen_name'];
			$default['token'] = $access_token_data['oauth_token'];
			$default['tokensecret'] = $access_token_data['oauth_token_secret'];
		}
		update_option('wp_tweeter', $default);
		$wptweeteroptions = get_option('wp_tweeter');
		?>
		<form action="<?php echo admin_url('options-general.php?page=wptweeter.php'); ?>" method="post">
			<?php wp_nonce_field('update-options'); ?>
			<p><input type="checkbox" name="tellworld" value="true" checked /> Tell the world you are using WordPress Tweeter. This will create a tweet saying <code>I just installed WordPress Tweeter, which tweets every time I make a new post. You can find it at http://bit.ly/cHpKR2</code>. Any support for the plugin is greatly appreciated :)</p>
			<p><input type="checkbox" name="followdev" value="true" checked /> Follow the plugin developer @tech163_</p>
			<p><input type="submit" value="Continue &gt;&gt;" /></p>
			<input type="hidden" name="step" value="activate" />
		</form>
	<?php } else {
	if(!isset($wptweeteroptions['updatetpl'])) {
		$wptweeteroptions['postupdate'] = '';
		$wptweeteroptions['updatetpl'] = '%blogtitle% Post updated - %posttitle%. Read it now at %posturl%';
		update_option('wp_tweeter', $wptweeteroptions);
	}
	$tagspace = $wptweeteroptions['tagspace'];
	$tagnumdefault = $wptweeteroptions['tagnumdefault'];
	$postupdatecheck = ($wptweeteroptions['postupdate'] == 'true' ? 'checked' : '');
	$pswdpostcheck = ($wptweeteroptions['pswdpost'] == 'true' ? 'checked' : '');
	$bitlyuser = $wptweeteroptions['bitlyuser'];
	$bitlyapi = $wptweeteroptions['bitlyapi'];
	$tpl = $wptweeteroptions['tpl'];
	
	$autocheck = ($wptweeteroptions['autocheck'] == 'true' ? 'checked' : '');

	?>
	<div class="wrap">
	<p>Please use the options below to configure WordPress Tweeter.</p>
	<form action="" method="post">
	<?php wp_nonce_field('update-options'); ?>
	<input type="hidden" name="action" value="update" />
	<style type="text/css">.wptweetercontentleft{padding:10px}.contentright{float:right}.wptweetercontentleft{width:28%;float:left;padding-right:10px;margin-top:10px;position:relative}.wptweetercontentleft h4{font-size:14px;color:#d54e21;letter-spacing:-1px;margin:0 0 5px;padding:0}.wptweetercontentleft p{margin-top:0}.wptweetercontentleft ul{-webkit-border-radius:5px;-moz-border-radius:5px}.wptweetercontentleft li{color:#536e90;margin:0 5px;padding:1px 0;list-style-type:circle;list-style-position:inside}.wptweetercontentright{width:63%;float:left;margin-left:3%;border-left:1px solid #e6e6e6;margin-bottom:-10px;padding-bottom:10px;padding-left:10px;padding-top:10px;min-height:35px}.wptweetercontentright li{list-style-type:none}.fsclear{clear:both}</style>
	<div class="metabox-holder">
		<div class="postbox">
      		<h3>Twitter Account</h3>
				<div class="wptweetercontentleft">
					<p>This is the account that WordPress Tweeter is connected to.</p>
				</div>
				<div class="wptweetercontentright">
					<p><?php echo $wptweeteroptions['username']; ?></p>
					<p><?php
						parse_str(file_get_contents('https://api.twitter.com/oauth/request_token?' . file_get_contents('http://lab.sliceone.com/29308/request-token?oauth_callback=' . admin_url('options-general.php?page=wptweeter.php'))));
						$link = 'https://api.twitter.com/oauth/authorize?oauth_token=' . $oauth_token;
						echo '<a href="' . $link . '">Change Account</a>';
					?></p>
				</div>
				<div class="fsclear"></div>
		</div>
	</div>
	<div class="metabox-holder">
		<div class="postbox">
      		<h3>Parameter</h3>
				<div class="wptweetercontentleft">
					<p>Parameters are useful when tracking where an user comes from. If the parameter is <code>source=twitter</code>, the url will link to <code>http://urltoblog.com/path/to/post/?source=twitter</code>.</p>
				</div>
				<div class="wptweetercontentright">
					<p><textarea rows="1" cols="50" name="parameter"><?php echo $wptweeteroptions['parameter']; ?></textarea></p>
				</div>
				<div class="fsclear"></div>
		</div>
	</div>
	<div class="metabox-holder">
		<div class="postbox">
      		<h3>URL Shortener</h3>
				<div class="wptweetercontentleft">
					<p>Here, you can choose the URL Shortener you wish to use. If you want to use a shortener that's not here, you will have to manually modify the code. <strong>Note that Twitter will often shorten a URL for you if you choose None and have a long URL.</strong></p>
				</div>
				<div class="wptweetercontentright">		
			     	<p><select name="service">
						<option value="none" <?php echo $selected['none']; ?> >None</option>
						<option value="wpshortlink">WP Shortlink</option>
						<?php
						foreach($wptweeterservices as $key => $value) {
							$shortslug = preg_replace('/[^a-z0-9]/', '', strtolower(trim($key)));
							echo '<option value="' . $shortslug . '" ' . ($wptweeteroptions['service'] == $shortslug ? ' selected="selected "' : null) . '>' . $key . '</option>';
						}
						?>
					</select></p>
					<strong>Bit.ly Account</strong>
					<p>If you choose to use bit.ly, you can specify your bit.ly username and API key to enhance tracking.</p>
					<table>
						<tr>
							<td><p>Username:</p></td>
							<td><input type="text" name="bitlyuser" value="<?php echo $bitlyuser; ?>" /></td>
						</tr>
						<tr>
							<td><p>API Key:</p></td>
							<td><input type="text" name="bitlyapi" value="<?php echo $bitlyapi; ?>" /></p></td>
						</tr>
					</table>
				</div><div class="fsclear"></div>
		</div>
	</div>
	<div class="metabox-holder">
		<div class="postbox">
      		<h3>Tweet Templates</h3>
				<div class="wptweetercontentleft">
					<p><strong>New Post Template</strong></p>
					<p><input type="checkbox" name="pswdpost" value="true" <?php echo $pswdpostcheck; ?> /> <strong>Tweet for password protected posts</strong></p>
					<p><textarea rows="3" cols="50" name="template"><?php echo $tpl; ?></textarea></p>
				</div>
				<div class="wptweetercontentright">
					<p><input type="checkbox" name="postupdate" value="true" <?php echo $postupdatecheck; ?> /> <strong>Post Updated Template</strong></p>
					<p><textarea rows="3" cols="50" name="updatetpl"><?php echo $wptweeteroptions['updatetpl']; ?></textarea></p>
				</div><div class="fsclear"></div>
		</div>
	</div>
	<div class="metabox-holder">
		<div class="postbox">
      		<h3>Tags</h3>
				<div class="wptweetercontentleft">
					<p><strong>Replace spaces with:</strong></p>
					<p><input type="text" name="tagspace" value="<?php echo $tagspace; ?>" /></p>
					<p>In the hash tags, this will be the thing that spaces will be replaced with.</p>
				</div>
				<div class="wptweetercontentright">
					<p><strong>Default Number of Tags</strong></p>
					<p><input type="text" name="tagnumdefault" value="<?php echo $tagnumdefault; ?>" /></p>
					<p>This is the number of tags that will be replaced with hashtags.</p>
				</div><div class="fsclear"></div>
		</div>
	</div>
	<div class="metabox-holder">
		<div class="postbox">
      		<h3>Blacklist Tags and Categories</h3>
				<div class="wptweetercontentleft">
					<p><strong>Tags:</strong></p>
					<p>Please enter one tag on each line. Posts with these tags will NOT be tweeted.</p>
					<p><textarea rows="3" cols="50" name="tagblacklist"><?php echo $wptweeteroptions['tagblacklist']; ?></textarea></p>
				</div>
				<div class="wptweetercontentright">
					<p><strong>Categories:</strong></p>
					<p>Please enter one category on each line. Posts with these categories will NOT be tweeted.</p>
					<p><textarea rows="4" cols="50" name="catblacklist"><?php echo $wptweeteroptions['catblacklist']; ?></textarea></p>
				</div><div class="fsclear"></div>
		</div>
	</div>
	<div class="metabox-holder">
		<div class="postbox">
      		<h3>Automatically Check for Updates</h3>
				<div class="wptweetercontentleft">
					<p>If this is enabled, WPTweeter will automatically check for a newer version, on top of the regular checking done by WordPress.</p>
				</div>
				<div class="wptweetercontentright">
					<p><input type="checkbox" name="autocheck" value="true" <?php echo $autocheck; ?> /> <strong>Check for new versions</strong></p>
				</div><div class="fsclear"></div>
		</div>
	</div>
	<p><input type="submit" value="Update Settings" /><input type="submit" name="uninstall" value="Uninstall" /></p>
	</form> 
</div>
<?php }
}

function wp_tweeter_meta_box() {
	echo '<div class="inside">
	<p>Do you want to tweet for this post?</p>
	<select name="dotweet">
		<option value="default" selected>Default</option>
		<option value="yes">Do Tweet</option>
		<option value="no">Don\'t Tweet</option>
	</select>
	<p><strong>Custom Tweet Template</strong> - If this is not empty, this template will be used, instead of the default template.</p>
	<textarea rows="3" cols="25" name="customtweettpl"></textarea>
	</div>';
}

function wp_tweeter_uninstall() {
	delete_option('wp_tweeter');
	echo '<p>WordPress Tweeter options have been successfully uninstalled. Feel free to deactivate the plugin now. We would appreciate it if you <a href="http://www.fusionswift.com/wordpress/wordpress-tweeter/" target="_blank">share the reason by leaving a comment</a> on why you have decided to uninstall WordPress Tweeter. I\'m are hoping to make it better everyday.</p>';
}

function wp_tweeter_menu() {
	add_options_page('WP Tweeter', 'WP Tweeter', 8, basename(__FILE__), 'wp_tweeter_admin');
	add_submenu_page('edit.php', 'New Tweet', 'New Tweet', 8, basename(__FILE__), 'wp_tweeter_new_tweet');
	add_meta_box('wordpress_tweeter', 'WP Tweeter Options', 'wp_tweeter_meta_box', 'post', 'side');
	if(function_exists('add_contextual_help')) {
		$link = '<a href="http://www.fusionswift.com/wordpress/wordpress-tweeter/" target="_blank">View Documentations</a>';
		add_contextual_help($newtweet, '<p>You can use this form to tweet directly from inside WordPress. Tweet messages longer than 140 characters will be truncated. ' . $link . '</p>');
		add_contextual_help($options, '<p>This page allows you to customize the options for WordPress Tweeter. This page allows you to change twitter accounts, modify templates, settings, and much others. ' . $link . '</p>');
	}
}

function wp_tweeter_notice() {
	$wptweeteroptions = get_option('wp_tweeter');
	if(function_exists('admin_url') && !is_array($wptweeteroptions)) {
		echo '<div class="error"><p>WordPress Tweeter is NOT configured yet! Please <a href="' . admin_url('options-general.php?page=wptweeter.php') . '">configure</a> it now! <strong><a href="http://www.fusionswift.com/wordpress/wordpress-tweeter/" target="_blank">Documentations</a></strong></p></div>';
	}
}

add_action('init', 'wp_tweeter');
add_action('pre_post_update', 'wp_tweeter_checkstatus');
add_action('admin_menu', 'wp_tweeter_menu');
add_action('publish_post', 'wp_tweeter_update');
add_action('admin_notices', 'wp_tweeter_notice');
