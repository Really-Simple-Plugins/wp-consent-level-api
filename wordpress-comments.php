<?php
/**
 * Instead of listening to the consent checkbox, this function checks for the "preferences" consent
 *
 * The consent checkbox is removed
 * if preferences is set, the comments cookies function gets passed a $cookies_consent=true, so cookies can be set. Otherwise, it's false.
 */


/**
 * first we remove the default comment cookies action, and replace with our own
 * we add custom comment cookies action, which checks the consent, then calls the comment cookie function in wp
 */

function wp_consent_api_wordpress_comments_cookies(){

	//remove default wp action
	remove_action( 'set_comment_cookies', 'wp_set_comment_cookies', 10);

	//add our own custom action
	add_action( 'set_comment_cookies', 'wp_consent_api_set_comment_cookies', 10, 3);

	//remove checkbox
	add_filter('comment_form_default_fields', 'wp_consent_api_wordpress_comment_form_hide_cookies_consent');

}
add_action('init', 'wp_consent_api_wordpress_comments_cookies');


/**
 * custom consent function, checking consent level to decide if cookies can be set.
 */
function wp_consent_api_set_comment_cookies($comment, $user, $cookies_consent){
	$cookies_consent = wp_has_consent('preference');
	wp_set_comment_cookies( $comment, $user, $cookies_consent );
}


/**
 * Remove consent checkbox
 * @param $fields
 *
 * @return array $fields
 *
 */

function wp_consent_api_wordpress_comment_form_hide_cookies_consent( $fields ) {
	unset( $fields['cookies'] );
	return $fields;
}