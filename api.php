<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );


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

function consent_api_enqueue_assets( $hook ) {
	$minified = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'wp-consent-api', CONSENT_API_URL . "assets/js/wp-consent-api$minified.js", array('jquery'), CONSENT_API_VERSION, true );

	//we can pass a default or static consent type to the javascript
	$consent_type = wp_get_consent_type();

	//when the consenttype (optin or optout) can be set dynamically, we can tell plugins to wait in the javascript until the consenttype has been determined
	$waitfor_consent_hook = apply_filters('wp_consent_api_waitfor_consent_hook', false);

	//the cookie expiration for the front-end consent cookies
	$expiration = wp_consent_api_cookie_expiration();
	wp_localize_script(
		'wp-consent-api',
		'consent_api',
		array(
			'consent_type' => $consent_type,
			'waitfor_consent_hook' => $waitfor_consent_hook,
			'cookie_expiration' => $expiration,
		)
	);
}

add_action( 'wp_enqueue_scripts', 'consent_api_enqueue_assets' , 9999);


/**
 * Validate consent_type
 * @param $consent_type
 *
 * @return bool|string $consent_type
 */

function wp_validate_consent_type($consent_type){
	if (in_array($consent_type, WP_CONSENT_API()->config->consent_types() )){
		return $consent_type;
	}
	return false;
}

/**
 * Validate consent_value
 * @param $consent_value
 *
 * @return bool|string $consent_value
 */

function wp_validate_consent_value($consent_value){
	if (in_array($consent_value, WP_CONSENT_API()->config->consent_values() )){
		return $consent_value;
	}
	return false;
}

/**
 * Validate consent_category
 * @param $consent_category
 *
 * @return bool|string $consent_category
 */

function wp_validate_consent_category($consent_category){
	if (in_array($consent_category, WP_CONSENT_API()->config->consent_categorys() )){
		return $consent_category;
	}
	return false;
}

/**
 * Get active consent_type
 * @return string $consent_type
 */
function wp_get_consent_type() {

	return apply_filters('wp_get_consent_type', FALSE);
}

/**
 * filterable, to allow for use in combination with consent_type
 * return value of wp_consent$level cookie (false, deny or allow)
 *
 * @param $consent_category
 * @return bool $has_consent
 */

function wp_has_consent( $consent_category ) {
	$consent_type = wp_get_consent_type();
	$consent_category = wp_validate_consent_category($consent_category);

	if (!$consent_type) {
		//if consent_type is not set, there's no consent management, we should return true to activate all cookies
		$has_consent_level = true;

	} else if (strpos($consent_type, 'optout')!==FALSE && !isset($_COOKIE["wp_consent_$consent_category"]) || !$_COOKIE["wp_consent_$consent_category"]) {
		//if it's opt out and no cookie is set or it's false, we should also return true
		$has_consent_level = true;

	}else if (isset($_COOKIE["wp_consent_$consent_category"]) && $_COOKIE["wp_consent_$consent_category"] ==='allow'){
		//all other situations, return only true if value is allow
		$has_consent_level = true;
	} else {
		$has_consent_level = false;
	}

	return apply_filters('wp_has_consent', $has_consent_level, $consent_category);
}

/**
 * Get cookie expiration
 * @return int expiration in seconds
 */

function wp_consent_api_cookie_expiration(){
	return apply_filters('wp_consent_api_cookie_expiration', WP_CONSENT_API()->config->cookie_expiration_days());
}

/**
 * set accepted consent category
 *
 * @param string $consent_category
 * @param string $value (allow|deny)
 */

function wp_set_consent( $consent_category, $value ) {
	$consent_category = apply_filters('wp_set_consent_type', $consent_category);
	$value = apply_filters('wp_set_consent_value', $value);

	$expiration = wp_consent_api_cookie_expiration() * DAY_IN_SECONDS;
	$consent_category =  wp_validate_consent_category($consent_category);
	$value =  wp_validate_consent_value($value);

	setcookie("wp_consent_$consent_category", $value, time() + $expiration, '/');
}
