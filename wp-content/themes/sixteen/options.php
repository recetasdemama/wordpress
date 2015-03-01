<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet
	$themename = wp_get_theme();
	$themename = preg_replace("/\W/", "_", strtolower($themename) );

	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'sixteen'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {

	$options = array();
	$imagepath =  get_template_directory_uri() . '/images/';
		
	
	//Basic Settings
	
	$options[] = array(
		'name' => __('Basic Settings', 'sixteen'),
		'type' => 'heading');
				
		
	$options[] = array(
		'name' => __('Copyright Text', 'sixteen'),
		'desc' => __('Some Text regarding copyright of your site, you would like to display in the footer.', 'sixteen'),
		'id' => 'footertext2',
		'std' => '',
		'type' => 'text');
		
		$options[] = array(
		'desc' => __('To have more customization options including Analytics, Custom Header/Footer Scripts <a href="http://inkhive.com/product/sixteen-plus" target="_blank">Upgrade to Pro</a> at Just $24.95'),
		'std' => '',
		'type' => 'info');
		
	//Layout Settings
		
	$options[] = array(
		'name' => __('Layout Settings', 'sixteen'),
		'type' => 'heading');	
	
	$options[] = array(
		'name' => "Sidebar Layout",
		'desc' => "Select Layout for Posts & Pages.",
		'id' => "sidebar-layout",
		'std' => "right",
		'type' => "images",
		'options' => array(
			'left' => $imagepath . '2cl.png',
			'right' => $imagepath . '2cr.png')
	);
	
	$options[] = array(
		'desc' => __('<a href="http://inkhive.com/product/sixteen-plus" target="_blank">Pro Version</a> supports the option to add custom themes, styles & Layouts. Upgrade at Just $24.95.'),
		'std' => '',
		'type' => 'info');
	
	$options[] = array(
		'name' => __('Custom CSS', 'sixteen'),
		'desc' => __('Some Custom Styling for your site. Place any css codes here instead of the style.css file.', 'sixteen'),
		'id' => 'style2',
		'std' => '',
		'type' => 'textarea');
	
	//SLIDER SETTINGS

	$options[] = array(
		'name' => __('Slider Settings', 'sixteen'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Enable Slider', 'sixteen'),
		'desc' => __('Check this to Enable Slider.', 'sixteen'),
		'id' => 'slider_enabled',
		'type' => 'checkbox',
		'std' => '0' );
		
	$options[] = array(
		'desc' => __('This Slider supports upto 5 Images. To show only 3 Slides in the slider, upload only 3 images. Leave the rest Blank. For best results, upload images of width 1180px.', 'sixteen'),
		'type' => 'info');
		
	$options[] = array(
		'desc' => __('In the <a href="http://inkhive.com/product/sixteen-plus" target="_blank">Pro Version (Sixteen Plus)</a> there are options to customize slider by choosing form over 16 animation effects, ability to set transition time and speed and more. Pro Version Supports More than 5 Slides. Upgrade at Just $24.95'),
		'std' => '',
		'type' => 'info');	

	$options[] = array(
		'name' => __('Slider Image 1', 'sixteen'),
		'desc' => __('First Slide', 'sixteen'),
		'id' => 'slide1',
		'class' => '',
		'type' => 'upload');
	
	$options[] = array(
		'desc' => __('Title', 'sixteen'),
		'id' => 'slidetitle1',
		'std' => '',
		'type' => 'text');
	
	$options[] = array(
		'desc' => __('Description or Tagline', 'sixteen'),
		'id' => 'slidedesc1',
		'std' => '',
		'type' => 'textarea');			
		
	$options[] = array(
		'desc' => __('Url', 'sixteen'),
		'id' => 'slideurl1',
		'std' => '',
		'type' => 'text');		
	
	$options[] = array(
		'name' => __('Slider Image 2', 'sixteen'),
		'desc' => __('Second Slide', 'sixteen'),
		'class' => '',
		'id' => 'slide2',
		'type' => 'upload');
	
	$options[] = array(
		'desc' => __('Title', 'sixteen'),
		'id' => 'slidetitle2',
		'std' => '',
		'type' => 'text');	
	
	$options[] = array(
		'desc' => __('Description or Tagline', 'sixteen'),
		'id' => 'slidedesc2',
		'std' => '',
		'type' => 'textarea');		
		
	$options[] = array(
		'desc' => __('Url', 'sixteen'),
		'id' => 'slideurl2',
		'std' => '',
		'type' => 'text');	
		
	$options[] = array(
		'name' => __('Slider Image 3', 'sixteen'),
		'desc' => __('Third Slide', 'sixteen'),
		'id' => 'slide3',
		'class' => '',
		'type' => 'upload');	
	
	$options[] = array(
		'desc' => __('Title', 'sixteen'),
		'id' => 'slidetitle3',
		'std' => '',
		'type' => 'text');	
		
	$options[] = array(
		'desc' => __('Description or Tagline', 'sixteen'),
		'id' => 'slidedesc3',
		'std' => '',
		'type' => 'textarea');	
			
	$options[] = array(
		'desc' => __('Url', 'sixteen'),
		'id' => 'slideurl3',
		'std' => '',
		'type' => 'text');		
	
	$options[] = array(
		'name' => __('Slider Image 4', 'sixteen'),
		'desc' => __('Fourth Slide', 'sixteen'),
		'id' => 'slide4',
		'class' => '',
		'type' => 'upload');	
		
	$options[] = array(
		'desc' => __('Title', 'sixteen'),
		'id' => 'slidetitle4',
		'std' => '',
		'type' => 'text');
	
	$options[] = array(
		'desc' => __('Description or Tagline', 'sixteen'),
		'id' => 'slidedesc4',
		'std' => '',
		'type' => 'textarea');			
		
	$options[] = array(
		'desc' => __('Url', 'sixteen'),
		'id' => 'slideurl4',
		'std' => '',
		'type' => 'text');		
	
	$options[] = array(
		'name' => __('Slider Image 5', 'sixteen'),
		'desc' => __('Fifth Slide', 'sixteen'),
		'id' => 'slide5',
		'class' => '',
		'type' => 'upload');	
		
	$options[] = array(
		'desc' => __('Title', 'sixteen'),
		'id' => 'slidetitle5',
		'std' => '',
		'type' => 'text');	
	
	$options[] = array(
		'desc' => __('Description or Tagline', 'sixteen'),
		'id' => 'slidedesc5',
		'std' => '',
		'type' => 'textarea');		
		
	$options[] = array(
		'desc' => __('Url', 'sixteen'),
		'id' => 'slideurl5',
		'std' => '',
		'type' => 'text');	
			
	//Social Settings
	
	$options[] = array(
		'name' => __('Social Settings', 'sixteen'),
		'type' => 'heading');
	
	$options[] = array(
		'desc' => __('Please set the value of following fields, as per the instructions given along. If you do not want to use an icon, just leave it blank. If some icons are showing up, even when no value is set then make sure they are completely blank, and just save the options once. They will not be shown anymore.', 'sixteen'),
		'type' => 'info');

	$options[] = array(
		'name' => __('Facebook', 'sixteen'),
		'desc' => __('Facebook Profile or Page URL i.e. http://facebook.com/username/ ', 'sixteen'),
		'id' => 'facebook',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');
	
	$options[] = array(
		'name' => __('Twitter', 'sixteen'),
		'desc' => __('Twitter Username', 'sixteen'),
		'id' => 'twitter',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');
	
	$options[] = array(
		'name' => __('Google Plus', 'sixteen'),
		'desc' => __('Google Plus profile url, including "http://"', 'sixteen'),
		'id' => 'google',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Feedburner', 'sixteen'),
		'desc' => __('URL for your RSS Feeds', 'sixteen'),
		'id' => 'feedburner',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');	

	$options[] = array(
		'name' => __('Instagram', 'sixteen'),
		'desc' => __('URL of your Instagram Profile', 'sixteen'),
		'id' => 'instagram',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');	

	$options[] = array(
		'name' => __('Flickr', 'sixteen'),
		'desc' => __('URL for your Flickr Profile', 'sixteen'),
		'id' => 'flickr',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('More social Icons are available in the <a href="http://inkhive.com/product/sixteen-plus" target="_blank">Pro Version (Sixteen Plus)</a>. Upgrade at Just $24.95'),
		'std' => '',
		'type' => 'info');			
						
	$options[] = array(
	'name' => __('Support', 'sixteen'),
	'type' => 'heading');
	
	$options[] = array(
		'desc' => __('Sixteen WordPress theme has been Designed and Created by <a href="http://inkhive.com" target="_blank">Rohit Tripathi</a>. For any Queries or help regarding this theme, <a href="http://wordpress.org/support/theme/sixteen" target="_blank">use our support forum.</a>', 'sixteen'),
		'type' => 'info');	
		
	 $options[] = array(
		'desc' => __('<a href="http://twitter.com/rohitinked" target="_blank">Follow Me on Twitter</a> to know about my upcoming themes.', 'sixteen'),
		'type' => 'info');	
	
	$options[] = array(
		'desc' => __('We Offer Dedicated Personal Support to all our <a href="http://inkhive.com/product/sixteen-plus" target="_blank">Pro Version Customers</a>. Upgrade at Just $24.95'),
		'std' => '',
		'type' => 'info');		
		
		
	
	$options[] = array(
		'name' => __('Live Demo Blog', 'sixteen'),
		'desc' => __('I have created a  <a href="http://demo.inkhive.com/sixteen/" target="_blank">Live Demo Blog</a> of this theme so that you know how it will look once ready.'),
		'std' => '',
		'type' => 'info');	
	
	$options[] = array(
		'name' => __('Regenerating Post Thumbnails', 'sixteen'),
		'desc' => __('If you are using Sixteen Theme on a New Wordpress Installation, then you can skip this section.<br />But if you have just switched to this theme from some other theme, or just updated to the current version of Sixteen, then you are requested regenerate all the post thumbnails. It will fix all the isses you are facing with distorted & ugly homepage thumbnail Images. ', 'sixteen'),
		'type' => 'info');	
		
	$options[] = array(
		'desc' => __('To Regenerate all Thumbnail images, Install and Activate the <a href="http://wordpress.org/plugins/regenerate-thumbnails/" target="_blank">Regenerate Thumbnails</a> WP Plugin. Then from <strong>Tools &gt; Regen. Thumbnails</strong>, re-create thumbnails for all your existing images. And your blog will look even more stylish with Sixteen theme.<br /> ', 'sixteen'),
		'type' => 'info');	
		
			
	$options[] = array(
		'desc' => __('<strong>Note:</strong> Regenerating the thumbnails, will not affect your original images. It will just generate a separate image file for those images.', 'sixteen'),
		'type' => 'info');	
		
	
	$options[] = array(
		'name' => __('Theme Credits', 'sixteen'),
		'desc' => __('Check this if you want to you do not want to give us credit in your site footer.', 'sixteen'),
		'id' => 'credit1',
		'std' => '0',
		'type' => 'checkbox');
	
	$options[] = array(
		'name' => __('Upgrade to Pro', 'sixteen'),
		'type' => 'heading');
	
	$options[] = array(
		'desc' => __('To Upgrade to Sixteen Plus and unlock plenty of features in this theme, please visit <a href="http://inkhive.com/product/sixteen-plus" target="_blank">this link</a>. Upgrade at Just $24.95'),
		'std' => '',
		'type' => 'info');		
		
	$options[] = array(
		'desc' => __('For any queries, you can <a href="http://inkhive.com/contact-us/" target="_blank">contact us</a>.'),
		'std' => '',
		'type' => 'info');				

	return $options;
}