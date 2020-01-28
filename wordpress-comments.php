<?php // phpcs:ignore -- Ignore the  "\r\n" notice for some machines.

/**
 * Instead of listening to the consent checkbox, this way we check for the "preferences" consent.
 *
 * The consent checkbox is removed if preferences is set, the comments cookies function gets
 * passed a $cookies_consent=true, so cookies can be set. Otherwise, it's false.
 */

add_action(
	'init',
	function() {
		// Remove the default WordPress comment cookies action.
		remove_action(
			'set_comment_cookies',
			'wp_set_comment_cookies',
			10
		);

		// Custom action to check the consent level and decide if cookies can be set.
		add_action(
			'set_comment_cookies',
			function( $comment, $user, $cookies_consent ) {
				$cookies_consent = wp_has_consent( 'preferences' );
				wp_set_comment_cookies( $comment, $user, $cookies_consent );
			},
			10,
			3
		);

		// Remove consent checkbox.
		add_filter(
			'comment_form_default_fields',
			function( $fields ) {
				unset( $fields['cookies'] );
				return $fields;
			}
		);
	}
);
