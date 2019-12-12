<?php
defined('ABSPATH') or die("you do not have access to this page!");

if (!class_exists("CONSENT_API_SITE_HEALTH")) {
	class CONSENT_API_SITE_HEALTH {

		private static $_this;

		function __construct() {

			if ( isset( self::$_this ) ) {
				wp_die( sprintf( __( '%s is a singleton class and you cannot create a second instance.', 'really-simple-ssl' ), get_class( $this ) ) );

			}

			add_filter( 'site_status_tests', array($this, 'consent_api_integration_check' ) );

			self::$_this = $this;
		}

		static function this() {
			return self::$_this;
		}

		public function consent_api_integration_check( $tests ) {
			$tests['direct']['wp-consent-api'] = array(
				'label' => __( 'WP Consent API test' ),
				'test'  => array($this, "consent_api_test"),
			);

			return $tests;
		}

		public function consent_api_test() {
			$plugins = get_option('active_plugins');
			$not_registered = array();
			$plugins_without_registration = false;
			foreach ($plugins as $plugin){
				if (!consent_api_registered($plugin)){
					$not_registered[] = $plugin;
					$plugins_without_registration = true;
				}
			}
			$result = array(
				'label'       => __( 'All plugins have declared to use the Consent API', 'really-simple-ssl' ),
				'status'      => 'good',
				'badge'       => array(
					'label' => __( 'Compliance' ),
					'color' => 'blue',
				),
				'description' => sprintf(
					'<p>%s</p>',
					__( 'All plugins have declared in their code that they are following the guidelines from the WP Consent API. When used in combination with a Cookie Management plugin, this will improve compliancy for your site.', 'really-simple-ssl' )
				),
				'actions'     => '',
				'test'        => 'wp-consent-api',
			);

			if ($plugins_without_registration) {
				$result['status']      = 'recommended';
				$result['label']       = __( 'One or more plugins are not conforming to the Consent API.', 'wp-consent-api' );
				$result['description'] = __( 'Not all plugins have declared to follow Consent API guidelines. Please contact the developer.', 'wp-consent-api');
				$result['actions'] = implode('<br>', $not_registered);

			}


			return $result;

		}
	}
}