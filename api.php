<?php // phpcs:ignore -- Ignore the "\r\n" notice for some machines.

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

/**
 * Enqueue scripts for the api for front-end
 * We need to ensure this script fires in correct order:
 * 1) all plugins, themes, etc
 * 2) this script
 * 3) consent management script
 *
 * This way we can ensure that plugins can use the javascript hooks
 * Consent management plugin should declare dependency on api js
 * API js should load as last, so we give a very high priority
 *
 * @param $hook
 */
function wp_consent_api_enqueue_assets( $hook ) {
	$minified = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'wp-consent-api', WP_CONSENT_API_URL . "assets/js/wp-consent-api$minified.js", array( 'jquery' ), WP_CONSENT_API_VERSION, true );

	//we can pass a default or static consent type to the javascript
	$consent_type = wp_get_consent_type();

	//when the consenttype (optin or optout) can be set dynamically, we can tell plugins to wait in the javascript until the consenttype has been determined
	$waitfor_consent_hook = apply_filters( 'wp_consent_api_waitfor_consent_hook', false );

	//the cookie expiration for the front-end consent cookies
	$expiration = wp_consent_api_cookie_expiration();

	wp_localize_script(
		'wp-consent-api',
		'consent_api',
		array(
			'consent_type'         => $consent_type,
			'waitfor_consent_hook' => $waitfor_consent_hook,
			'cookie_expiration'    => $expiration,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'wp_consent_api_enqueue_assets', PHP_INT_MAX-100 );

/**
 * Validate consent_type
 *
 * @since 1.0.0
 *
 * @param $consent_type
 *
 * @return bool|string $consent_type
 */
function wp_validate_consent_type( $consent_type ) {
	if ( in_array( $consent_type, WP_CONSENT_API::$config->consent_types(), true ) ) {
		return $consent_type;
	}

	return false;
}

/**
 * Validate consent_value
 *
 * @since 1.0.0
 *
 * @param $consent_value
 *
 * @return bool|string $consent_value
 */
function wp_validate_consent_value( $consent_value ) {
	if ( in_array( $consent_value, WP_CONSENT_API::$config->consent_values(), true ) ) {
		return $consent_value;
	}
	return false;
}

/**
 * Validate consent_category
 *
 * @since 1.0.0
 *
 * @param $consent_category
 *
 * @return bool|string $consent_category
 */
function wp_validate_consent_category( $consent_category ) {
	if ( in_array( $consent_category, WP_CONSENT_API::$config->consent_categories(), true ) ) {
		return $consent_category;
	}

	return false;
}

/**
 * Get active consent_type.
 *
 * @since 1.0.0
 *
 * @return string|bool
 */
function wp_get_consent_type() {
	return apply_filters( 'wp_get_consent_type', false );
}


/**
 * Filterable, to allow for use in combination with consent_type
 * return value of wp_consent$level cookie (false, deny or allow)
 *
 * @since 1.0.0
 *
 * @param string $consent_category
 * @param string|bool $requested_by plugin name e.g. complianz-gdpr/complianz-gdpr.php. This can be used to disable consent for a plugin specifically.
 *
 * @return bool
 */
function wp_has_consent( $consent_category, $requested_by = false ) {
	$consent_type     = wp_get_consent_type();
	$consent_category = wp_validate_consent_category( $consent_category );

	if ( ! $consent_type ) {
		//if consent_type is not set, there's no consent management, we should return true to activate all cookies
		$has_consent = true;
	} elseif ( strpos( $consent_type, 'optout' ) !== false && ! isset( $_COOKIE[ "wp_consent_$consent_category" ] ) || ! $_COOKIE[ "wp_consent_$consent_category" ] ) {
		//if it's opt out and no cookie is set or it's false, we should also return true
		$has_consent = true;
	} elseif ( isset( $_COOKIE[ "wp_consent_$consent_category" ] ) && 'allow' === $_COOKIE[ "wp_consent_$consent_category" ] ) {
		//all other situations, return only true if value is allow
		$has_consent = true;
	} else {
		$has_consent = false;
	}

	return apply_filters( 'wp_has_consent', $has_consent, $consent_category, $requested_by );
}

/**
 * Get cookie expiration.
 *
 * @return int Expiration in seconds.
 */
function wp_consent_api_cookie_expiration() {
	return apply_filters( 'wp_consent_api_cookie_expiration', WP_CONSENT_API::$config->cookie_expiration_days() );
}

/**
 * Set accepted consent category.
 *
 * @since 1.0.0
 *
 * @param string $consent_category
 * @param string $value (allow|deny)
 *
 * @return void
 */

function wp_set_consent( $consent_category, $value ) {
	$consent_category = apply_filters( 'wp_set_consent_type', $consent_category );
	$value            = apply_filters( 'wp_set_consent_value', $value );

	$expiration       = wp_consent_api_cookie_expiration() * DAY_IN_SECONDS;
	$consent_category = wp_validate_consent_category( $consent_category );
	$value            = wp_validate_consent_value( $value );

	setcookie( "wp_consent_$consent_category", $value, time() + $expiration, '/' );
}

/**
 * Check if a plugin is registered for the WP Consent API.
 *
 * @since 1.0.0
 *
 * @param string $plugin
 *
 * @return bool $registered
 */

function consent_api_registered( $plugin ) {
	//we consider this plugin to comply ;)
	if ( strpos( $plugin, 'wp-consent-api.php' ) !== false ) {
		return true;
	}

	return apply_filters( "wp_consent_api_registered_{$plugin}", false );
}

/**
 * Wrapper function for the registration of a cookie with WordPress
 * @param string $name
 * @param string $plugin_or_service //plugin or service (e.g. Google Maps) that sets cookie e.g.
 * @param string $category //functional, preferences, statistics-anonymous, statistics,  marketing
 * @param string $expires  //time until the cookie expires
 * @param string $function //what the cookie is meant to do. e.g. 'Store a unique User ID'
 * @param bool $isPersonalData //if the cookie collects personal data
 * @param string $collectedPersonalData //type of personal data that is collected. Only needs to be filled in if isPersonalData =true
 * @param bool $memberCookie //if a cookie is relevant for members of the site only
 * @param bool $administratorCookie //if the cookie is relevant for administrators only
 * @param string $type //HTTP, LOCALSTORAGE, API
 * @param string|bool $domain //domain on which the cookie is set. should by default be the current domain
 */

function wp_add_cookie_info($name, $plugin_or_service, $category, $expires, $function, $isPersonalData, $collectedPersonalData='', $memberCookie = false, $administratorCookie = false, $type='HTTP', $domain = false) {
	WP_CONSENT_API::$cookie_info->add_cookie_info($name, $plugin_or_service, $category, $expires, $function, $isPersonalData, $collectedPersonalData, $memberCookie, $administratorCookie, $type, $domain);
}

/**
 * Wrapper function to get cookie info for one specific cookie, or for all cookies registered.
 * @param string|bool $name
 *
 * @return array
 */

function wp_get_cookie_info($name=false){
	return WP_CONSENT_API::$cookie_info->get_cookie_info($name);
}
