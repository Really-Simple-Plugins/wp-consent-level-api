<?php // phpcs:ignore -- Ignore the missing class- prefix from file & "\r\n" notice for some machines.

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

if ( ! class_exists( 'CONSENT_API_CONFIG' ) ) {
	class CONSENT_API_CONFIG {

		private static $_this;

		function __construct() {
			if ( isset( self::$_this ) ) {
				// translators: %s the name of the PHP Class used.
				wp_die( sprintf( __( '%s is a singleton class and you cannot create a second instance.', 'complianz-gdpr' ), get_class( $this ) ) );
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

		/**
		 * Get list if active consent_categories
		 *
		 * @return array() $consent_categories
		 */
		public function consent_categories() {
			return apply_filters(
				'wp_consent_categories',
				array(
					'functional',
					'preferences',
					'statistics',
					'statistics-anonymous',
					'marketing',
				)
			);
		}

		/**
		 * Get list of possible consent_values
		 *
		 * @return array() $consent_values
		 */
		public function consent_values() {
			$consent_values = array(
				'allow',
				'deny',
			);

			return apply_filters( 'wp_consent_values', $consent_values );
		}


		public function cookie_expiration_days() {
			return apply_filters( 'wp_cookie_expiration', 30 );
		}
	}
}
