<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );


/**
 * Enqueue scripts for the api for front-end
 *
 * @param $hook
 */

function clapi_enqueue_assets( $hook ) {
	$minified = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'wp-consent-level-api', CLAPI_URL . "assets/js/wp-consent-level-api$minified.js", array( 'jquery' ), CLAPI_VERSION, true );
}

add_action( 'wp_enqueue_scripts', 'clapi_enqueue_assets' );


/**
 * set cookie wp_consenttype, filterable to allow for geo ip tools, optin, optout
 * @param string $consenttype
 */

function wp_set_consenttype($consenttype) {
	$expiration = WP_CL_API()->config->expiration();
	$consenttype = apply_filters('wp_set_consenttype', wp_validate_consenttype($consenttype));
	setcookie('wp_consenttype', $consenttype, time() + $expiration, '/');
}

/**
 * Validate consenttype
 * @param $consenttype
 *
 * @return bool|string $consenttype
 */

function wp_validate_consenttype($consenttype){
	if (in_array($consenttype, WP_CL_API()->config->consenttypes() )){
		return $consenttype;
	}
	return false;
}

/**
 * Validate consentvalue
 * @param $consentvalue
 *
 * @return bool|string $consentvalue
 */

function wp_validate_consentvalue($consentvalue){
	if (in_array($consentvalue, WP_CL_API()->config->consentvalues() )){
		return $consentvalue;
	}
	return false;
}

/**
 * Validate consentlevel
 * @param $consentlevel
 *
 * @return bool|string $consentlevel
 */

function wp_validate_consentlevel($consentlevel){
	if (in_array($consentlevel, WP_CL_API()->config->consentlevels() )){
		return $consentlevel;
	}
	return false;
}

/**
 * Get active consenttype
 * return value of wp_consenttype cookie
 * @return string $consenttype
 */
function wp_get_consenttype() {
	if (isset($_COOKIE['wp_consenttype'])){
		$consenttype = wp_validate_consenttype($_COOKIE['wp_consenttype']);
	} else {
		$consenttype = false;
	}

	return apply_filters('wp_get_consenttype', $consenttype);
}

/**
 * filterable, to allow for use in combination with consenttype
 * return value of wp_consent$level cookie (false, deny or allow)
 *
 * @param $consentlevel
 * @return bool $has_consent_level
 */

function wp_has_consent_level( $consentlevel ) {
	$consentlevel =  wp_validate_consentlevel($consentlevel);

	if (isset($_COOKIE["wp_consent_$consentlevel"]) && $_COOKIE["wp_consent_$consentlevel"] ==='allow'){
		$has_consent_level = true;
	} else {
		$has_consent_level = false;
	}
	return apply_filters('wp_has_consent_level', $has_consent_level);
}

/**
 * set accepted consentlevel
 *
 * @param string $consentlevel
 * @param string $value (allow|deny)
 */

function wp_set_consent_level( $consentlevel, $value ) {
	$consentlevel = apply_filters('wp_set_consentlevel_type', $consentlevel);
	$value = apply_filters('wp_set_consentlevel_value', $value);

	$expiration = WP_CL_API()->config->expiration();
	$consentlevel =  wp_validate_consentlevel($consentlevel);
	$value =  wp_validate_consentvalue($value);

	setcookie("wp_consent_$consentlevel", $value, time() + $expiration, '/');
}
