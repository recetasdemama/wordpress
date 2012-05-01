<?php
/*
Plugin Name: Random Redirect
Plugin URI: http://wordpress.org/extend/plugins/random-redirect/
Description: Allows you to create a link to yourblog.example.com/?random which will redirect someone to a random post on your blog, in a StumbleUpon-like fashion. You can also specific in the URL `random_post_type` or `random_cat_id`.
Version: 1.1
Author: Matt Mullenweg
Author URI: http://ma.tt/
*/

//TODO : Add URL-Rewrite

function matt_random_redirect() {
	global $wpdb;
	
	$query = "SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";

	if ( isset( $_GET['random_cat_id'] ) ) {
		$random_cat_id = (int) $_GET['random_cat_id'];
		$query = "SELECT DISTINCT ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id = $random_cat_id) INNER JOIN  $wpdb->term_taxonomy AS tt ON(tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category') WHERE post_type = 'post' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	if ( isset( $_GET['random_post_type'] ) ) {
		$post_type = preg_replace( '|[^a-z]|i', '', $_GET['random_post_type'] );
		$query = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND post_password = '' AND 	post_status = 'publish' ORDER BY RAND() LIMIT 1";
	}

	$random_id = $wpdb->get_var( $query );

	wp_redirect( get_permalink( $random_id ) );
	exit;
}

if ( isset( $_GET['random'] ) )
	add_action( 'template_redirect', 'matt_random_redirect' );

?>