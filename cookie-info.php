<?php // phpcs:ignore -- Ignore the missing class- prefix from file & "\r\n" notice for some machines.

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

if ( ! class_exists( 'WP_CONSENT_API_COOKIE_INFO' ) ) {
	class WP_CONSENT_API_COOKIE_INFO {
		public $registered_cookies;
		private static $_this;

		function __construct() {
			if ( isset( self::$_this ) ) {
				// translators: %s the name of the PHP Class used.
				wp_die( sprintf( __( '%s is a singleton class and you cannot create a second instance.', 'wp-consent-api' ), get_class( $this ) ) );
			}

			self::$_this = $this;
		}

		static function this() {
			return self::$_this;
		}

		/**
		 * Wrapper function for the registration of a cookie with WordPress
		 * @param string $name
		 * @param string $plugin_or_service //plugin or service (e.g. Google Maps) that sets cookie e.g.
		 * @param string $category //functional, preferences, statistics-anonymous, statistics,  marketing
		 * @param string $expires  //time until the cookie expires
		 * @param string $function //what the cookie is meant to do. e.g. 'Store a unique User ID'
		 * @param bool $isPersonalData //if the cookie collects personal data
		 * @param bool $memberCookie //if a cookie is relevant for members of the site only
		 * @param bool $administratorCookie //if the cookie is relevant for administrators only
		 * @param string $collectedPersonalData //type of personal data that is collected. Only needs to be filled in if isPersonalData =true
		 * @param string|bool $domain //domain on which the cookie is set. should by default be the current domain
		 */

		public function add_cookie_info($name, $plugin_or_service, $category, $expires, $function, $isPersonalData, $memberCookie, $administratorCookie, $collectedPersonalData='', $domain = false)  {

			//if domain is not passed, we assume it's first party, from this domain.
			if (!$domain) $domain = site_url();

			$this->registered_cookies[ $name ] = array(
				'plugin_or_service'     => sanitize_text_field($plugin_or_service),
				'category'              => wp_validate_consent_category($category),
				'expires'               => sanitize_text_field($expires),
				'function'              => sanitize_text_field($function),
				'isPersonalData'        => (bool) $isPersonalData,
				'collectedPersonalData' => sanitize_text_field($collectedPersonalData),
				'memberCookie'          => (bool) $memberCookie,
				'administratorCookie'   => (bool) $administratorCookie,
				'domain'                => esc_url_raw($domain),
			);
		}


		/**
		 * Get cookie info for one specific cookie, or for all cookies registered.
		 * @param string|bool $name
		 *
		 * @return array
		 */

		public function get_cookie_info($name=false){

			if ($name && isset($this->registered_cookies[$name])){
				return $this->registered_cookies[$name];
			}

			return $this->registered_cookies;
		}



	}
}
