<?php // phpcs:ignore -- Ignore the missing class- prefix from file & "\r\n" notice for some machines.

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

if ( ! class_exists( 'WP_CONSENT_API_COOKIE' ) ) {
	class WP_CONSENT_API_COOKIE {

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
		 * Get list if active consent_types
		 *
		 * @return array() $consent_types
		 */

		public function consent_types() {
			$consent_types = array(
				'optin',
				'optout',
			);

			return apply_filters( 'wp_consent_types', $consent_types );
		}



	}
}
