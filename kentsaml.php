<?php

define('KENTAUTH_SAML_PATH', getenv('KENTAUTH_SAML_PATH') ? getenv('KENTAUTH_SAML_PATH') : realpath('../../sp/simplesamlphp'));

define('KENTAUTH_SP', getenv('KENTAUTH_SP') ? getenv('KENTAUTH_SP') : 'default-sp');

$saml_included =true;

if(in_array(WP_ENV,array('development','production','live','test'))) {
	include_once(KENTAUTH_SAML_PATH . '/lib/_autoload.php');
}else{
	$saml_included = false;
}

if($saml_included){
	try {
		$as = new SimpleSAML_Auth_Simple(KENTAUTH_SP);
	}
	catch (Exception $e) {
		$as = NULL;
		$saml_included = false;
	}
}

if($saml_included) {
	add_action('authenticate', 'kent_wp_saml_authenticate', 10, 2);
	add_action('wp_logout', 'kent_wp_saml_logout');
	add_action('lost_password', 'kent_wp_saml_disable_function');
	add_action('retrieve_password', 'kent_wp_saml_disable_function');
	add_action('password_reset', 'kent_wp_saml_disable_function');
	add_filter('show_password_fields', '__return_false');
}

//if (is_multisite()) {
//	add_action('network_admin_menu', 'kent_wp_saml_add_options_page');
//}else{
//	add_action('admin_menu', 'kent_wp_saml_add_options_page');
//}
//
//function kent_wp_saml_add_options_page() {
//
//	if (is_multisite()) {
//		$role= 'manage_network_options';
//	}else {
//		$role = 'manage_options';
//	}
//
//	add_options_page('KentSAML Auth', 'KentSAML Auth', $role,
//					 'kentsamlauth', 'kent_wp_saml_options_page');
//
//}

function kent_wp_saml_options_page(){

}

function kent_wp_saml_disable_function(){
	wp_die('Function Disabled!','Function Disabled',array('response'=>403,'back_link'=>true));
}

if($saml_included) {
	/*
	 Log the user out from WordPress if the simpleSAMLphp SP session is gone.
	 This function overrides the is_logged_in function from wp core.
	 (Another solution could be to extend the wp_validate_auth_cookie func instead).
	*/
	function is_user_logged_in() {
		global $as;
		// Allow use of "is_user_logged_in" filter to override current login mechanism
		$is_authed = apply_filters("is_user_logged_in", null);
		if($is_authed !== null) {
			return $is_authed;
		}
		$user = wp_get_current_user();
		if($user->ID > 0) {
			// User is local authenticated but SP session was closed
			if(!isset($as)) {
				try {
					$as = new SimpleSAML_Auth_Simple(KENTAUTH_SP);
				} catch(Exception $e) {
					return false;
				}
			}

			if(!$as->isAuthenticated()) {
				wp_logout();

				return false;
			} else {
				return true;
			}
		}

		return false;
	}
}


/*
 We call simpleSAMLphp to authenticate the user at the appropriate time.
 If the user has not logged in previously, we create an account for them.
*/
function kent_wp_saml_authenticate($user, $username) {
	if(is_a($user, 'WP_User')) { return $user; }
	global $as;

	// fix from http://wordpress.org/support/topic/suggested-change-for-fixing-re-login
	try {
		$as->requireAuth(array('ReturnTo' => wp_login_url(wp_make_link_relative($_REQUEST["redirect_to"]), false)));
	}
	catch (Exception $e) {
		wp_die("SAML login could not be initiated");
	}
	// Reset values from input ($_POST and $_COOKIE)
	$username = '';

	$attributes = $as->getAttributes();

	$username = $attributes['uid'][0];

	if ($username != substr(sanitize_user($username, TRUE), 0, 60)) {
		$error = sprintf(__('<p><strong>ERROR</strong><br /><br />
				We got back the following identifier from the login process:<pre>%s</pre>
				Unfortunately that is not suitable as a username.<br />
				Please contact the <a href="mailto:%s"> administrator</a> for support.</p>'), $username, get_option('admin_email'));
		wp_die($error);
	}

	$user = get_user_by('login', $username);

	if ($user) {
		// user already exists
		return $user;
	} else {

		// User is not in the WordPress database
		// They passed SimpleSAML and so are authorised
		// Add them to the database

		// User must have an e-mail address to register

		if($attributes['mail'][0]) {
			// Try to get email address from attribute
			$user_email = $attributes['mail'][0];
		} else {

			// Otherwise use default email suffix
			$user_email = $username . '@kent.ac.uk';

		}

		$user_info = array();
		$user_info['user_login'] = $username;
		$user_info['user_pass'] = wp_generate_password(); // Gets reset later on.
		$user_info['user_email'] = $user_email;

		$user_info['first_name'] = $attributes['givenName'][0];
		$user_info['last_name'] = $attributes['sn'][0];

		$user_info['role'] = "author";


		$wp_uid = wp_insert_user($user_info);
		if ( is_object($wp_uid) && is_a($wp_uid, 'WP_Error') ) {
			$error = $wp_uid->get_error_messages();
			$error = implode("<br>", $error);
			$error = '<p><strong>ERROR</strong>: '.$error.'</p>';
			wp_die($error);
		}
		kent_wp_saml_invalidate_password($wp_uid);
		return get_user_by('login', $username);
	}

}

function kent_wp_saml_logout() {

	global $as;

	$as->logout(get_option('siteurl'));

}

function kent_wp_saml_invalidate_password($ID) {
	global $wpdb;
	$wpdb->query(
		$wpdb->prepare(
			"UPDATE wp_users SET user_pass = '~~~invalidated_password~~~' WHERE ID = %d",
			$ID
		)
	);
}