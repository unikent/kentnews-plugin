<?php

// Force frontend redirect from news backend public site
add_action('get_header', 'kentnews_redirect_frontend');

/**
 * If a single article is being viewed, auto redirect to front end version
 */
function kentnews_redirect_frontend(){
	if(is_single() && isset($_GET['preview_id'])){
		return wp_redirect( kentnews_front_end_preview() );
	}
}

// Enable custom preview
add_filter('preview_post_link', 'kentnews_front_end_preview');

/**
 * Rewrite preview link to point to news front end
 */
function kentnews_front_end_preview($link='') {

	// Get post ID & STATUS
	$id = get_the_ID();
	$status = get_post_status($id);

	// If published post, use get autosave method to find "preview" id.
	// Else just use id, if this is a draft post already
	if($status === 'publish'){
		$preview = wp_get_post_autosave($id);
		// if preview id found, use it.
		if($preview) $id = $preview->ID;
	}

	// Build preview key
	$preview_key = md5($id);

	// Build link (frontend url is configured in .env file)
	return WP_FRONTEND."preview/{$id}?preview_key={$preview_key}&time=".time();
}



add_action('dispatch_api','kentnews_authenticate_api');

/**
 * validate access to thermal API's
 *
 */
function kentnews_authenticate_api(){

	if(!defined("API_KEY") || API_KEY === '') die("Disabled. Auth key not set.");

	// Unless auth key is passed, disallow any connection to Thermal
	if(!isset($_GET['api_key']) || $_GET['api_key'] !== API_KEY){
		die("Authorization required.");
	}

	// If key is valid, allow full api access
	// Uses additional filter "is_user_logged_in" in wordpress-saml

	// Since this bit is somewhat weird, whats happening is:

	// First is_user_logged_in pass(s) triggers "false" (as no id) - this doesn't matter as API
	// access is allowed to none users (this is just a general check).
	// Second is_user_logged_in pass (once API has been invoked and this code has run), requires
	// additional permissions. These are granted by setting current user to 1.
	// Since SAML imposes additional checks, to avoid it kicking the session out (and triggering wp_logout)
	// when we don't have valid SAML tokens, additionally override the "is_user_logged_in" via a filter
	// of the same name.

	// This code can only be initiated during API use & is read-only.
	add_filter("is_user_logged_in", function(){
		return ($_GET['api_key'] === API_KEY);
	});
	wp_set_current_user(1);
}


function kentnews_homepage_preview_links(){
	global $post;
	echo '<div id="previewButtons"><h4>Preview on News Center homepage as:</h4>';
	echo '<a class="button" href="' . WP_FRONTEND .'preview/index/' . $post->ID . '/standard?preview_key=' . API_KEY .'}">Standard Item</a>';
	echo '<a class="button" href="' . WP_FRONTEND .'preview/index/' . $post->ID . '/hero?preview_key=' . API_KEY .'}">Hero</a>';
	echo '<a class="button" href="' . WP_FRONTEND .'preview/index/' . $post->ID . '/feature1?preview_key=' . API_KEY .'}">Feature 1</a>';
	echo '<a class="button" href="' . WP_FRONTEND .'preview/index/' . $post->ID . '/feature2?preview_key=' . API_KEY .'}">Feature 2</a>';
	echo '<a class="button" href="' . WP_FRONTEND .'preview/index/' . $post->ID . '/feature3?preview_key=' . API_KEY .'}">Feature 3</a></div>';

}
add_action('post_submitbox_misc_actions','kentnews_homepage_preview_links');