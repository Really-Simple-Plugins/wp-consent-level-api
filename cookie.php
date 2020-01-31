<?php // phpcs:ignore -- Ignore the missing class- prefix from file & "\r\n" notice for some machines.

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

if ( ! class_exists( 'WP_CONSENT_API_COOKIES' ) ) {
	class WP_CONSENT_API_COOKIES {
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



	}
}
