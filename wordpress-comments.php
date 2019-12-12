<?php
/**
 * custom consent function, checking consent level to decide if cookies can be set.
 */
function wp_consent_api_set_comment_cookies($comment, $user, $cookies_consent){
	if (wp_has_consent('preference')){
		$cookies_consent = true;
	}
	wp_set_comment_cookies( $comment, $user, $cookies_consent );
}

/**
 * remove default comment cookies process
 * add custom comment cookies function, overriding consent
 */

function wp_consent_api_wordpress_comments_cookies(){

	remove_action( 'set_comment_cookies', 'wp_set_comment_cookies', 10);
	add_action( 'set_comment_cookies', 'wp_consent_api_set_comment_cookies', 10, 3);

	add_filter('comment_form_default_fields', 'wp_consent_api_wordpress_comment_form_hide_cookies_consent');

}
add_action('init', 'wp_consent_api_wordpress_comments_cookies');

/**
 * Remove consent checkbox
 * @param $fields
 *
 * @return mixed
 *
 */

function wp_consent_api_wordpress_comment_form_hide_cookies_consent( $fields ) {
	unset( $fields['cookies'] );
	return $fields;
}