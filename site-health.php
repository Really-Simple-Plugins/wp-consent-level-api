<?php // phpcs:ignore -- Ignore the wrong filename (class- prefix) & "\r\n" notice for some machines.

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

/**
 * WP_CONSENT_API class.
 *
 */
if ( ! class_exists( 'WP_CONSENT_API_SITE_HEALTH' ) ) {
	class WP_CONSENT_API_SITE_HEALTH {
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
		function __construct() {
			if ( isset( self::$instance ) ) {
				// translators: %s the name of the PHP Class used.
				wp_die( sprintf( __( '%s is a singleton class and you cannot create a second instance.', 'wp-consent-api' ), get_class( $this ) ) );
			}

			add_filter( 'site_status_tests', array( $this, 'consent_api_integration_check' ) );

			self::$instance = $this;
		}

		/**
		 * Attach the WP Consent API Site Health tests.
		 *
		 * @since 1.0.0
		 *
		 * @param array $tests The Site Health tests.
		 */
		public function consent_api_integration_check( $tests ) {
			$tests['direct']['wp-consent-api'] = array(
				'label' => __( 'WP Consent API test' ),
				'test'  => array( $this, 'wp_consent_api_test' ),
			);

			return $tests;
		}

		/**
		 * Run the WP Consent API Site Health tests.
		 *
		 * @since 1.0.0
		 *
		 * @return array $result The WP Consent API Site Health tests results.
		 */
		public function wp_consent_api_test() {
			$plugins                      = get_option( 'active_plugins' );
			$not_registered               = array();
			$plugins_without_registration = false;

			foreach ( $plugins as $plugin ) {
				if ( ! consent_api_registered( $plugin ) ) {
					$not_registered[]             = $plugin;
					$plugins_without_registration = true;
				}
			}

			$result = array(
				'label'       => __( 'All plugins have declared to use the Consent API', 'wp-consent-api' ),
				'status'      => 'good',
				'badge'       => array(
					'label' => __( 'Compliance' ),
					'color' => 'blue',
				),
				'description' => sprintf(
					'<p>%s</p>',
					__( 'All plugins have declared in their code that they are following the guidelines from the WP Consent API. When used in combination with a Cookie Management plugin, this will improve compliancy for your site.', 'wp-consent-api' )
				),
				'actions'     => '',
				'test'        => 'wp-consent-api',
			);

			if ( $plugins_without_registration ) {
				$result['status']      = 'recommended';
				$result['label']       = __( 'One or more plugins are not conforming to the Consent API.', 'wp-consent-api' );
				$result['description'] = __( 'Not all plugins have declared to follow Consent API guidelines. Please contact the developer.', 'wp-consent-api' );
				$result['actions']     = implode( '<br>', $not_registered );
			}

			return $result;
		}
	}
}
