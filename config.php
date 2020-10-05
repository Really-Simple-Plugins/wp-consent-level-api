<?php // phpcs:ignore -- Ignore the missing class- prefix from file & "\r\n" notice for some machines.

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

if ( ! class_exists( 'WP_CONSENT_API_CONFIG' ) ) {
	/**
	 * WP_CONSENT_API_CONFIG definition.
	 *
	 * @since 1.0.0
	 */
	class WP_CONSENT_API_CONFIG {
		/**
		 * Instance.
		 *
		 * @since 1.0.0
		 *
		 * @var $instance
		 */
		private static $instance;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			if ( isset( self::$instance ) ) {
				// translators: %s the name of the PHP Class used.
				wp_die( esc_html( sprintf( __( '%s is a singleton class and you cannot create a second instance.', 'wp-consent-api' ), get_class( $this ) ) ) );
			}

			self::$instance = $this;
		}

		/**
		 * Get default filterable list of active consent types.
		 *
		 * @since 1.0.0
		 *
		 * @return array $consent_types
		 */
		public function consent_types() {
			return apply_filters(
				'wp_consent_types',
				array(
					'optin',
					'optout',
				)
			);
		}

		/**
		 * Get default filterable cookie prefix.
		 *
		 * @since 1.0.2
		 *
		 * @return array $consent_types
		 */
		public function consent_cookie_prefix() {
			return apply_filters(
				'wp_consent_cookie_prefix',
				'wp_consent'
			);
		}

		/**
		 * Get default filterable list of active consent categories.
		 *
		 * @since 1.0.0
		 *
		 * @return array $consent_categories
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
		 * Get default filterable list of possible consent values.
		 *
		 * @since 1.0.0
		 *
		 * @return array $consent_values
		 */
		public function consent_values() {
			return apply_filters(
				'wp_consent_values',
				array(
					'allow',
					'deny',
				)
			);
		}

		/**
		 * Get default filterable cookie expiration.
		 *
		 * @since 1.0.0
		 *
		 * @return int Cookie expiration in days.
		 */
		public function cookie_expiration_days() {
			return apply_filters( 'wp_cookie_expiration', 30 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- This is intended for Core.
		}
	}
}
