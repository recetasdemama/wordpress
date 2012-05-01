<?php
/*
Plugin Name: Sort Categories By Title
Plugin URI: http://www.mikesmullin.com
Description: Allows easy sorting of Categories by Title if your readers don't care when things were posted.
Version: 1.0
Author: Mike Smullin
Author URI: http://www.mikesmullin.com
*/

add_action('pre_get_posts','sort_categories_by_title');

function sort_categories_by_title($x) {
	if(is_category() and !is_category(13)) {
		$x->query_vars['orderby'] = 'title';
		$x->query_vars['order'] = 'ASC';
	}
	else if(is_category(13)) {
		$x->query_vars['order'] = 'DSC';
	}}
?>