<?php // phpcs:ignore WordPress.Files.Filename.InvalidClassFileName
/**
 * This file is part of WP Consent API.
 *
 * Copyright 2020 Rogier Lankhorst and the WordPress Core Privacy team.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://www.gnu.org/licenses/.
 *
 * @package wordpress/consent-api
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

if ( ! class_exists( 'WP_CONSENT_API_COOKIE_INFO' ) ) {
	/**
	 * A class implementing the WP Consent API cookie handling.
	 *
	 * @since 1.0.0
	 */
	class WP_CONSENT_API_COOKIE_INFO {
		/**
		 * An array of information about registered cookies.
		 *
		 * @var array
		 */
		public $registered_cookies;

		/**
		 * The Singleton.
		 *
		 * @var self|null
		 */
		private static $instance;

		/**
		 * Creates a new instance.
		 */
		public function __construct() {
			if ( isset( self::$instance ) ) {
				// translators: %s the name of the PHP Class used.
				wp_die( esc_html( sprintf( __( '%s is a singleton class and you cannot create a second instance.', 'wp-consent-api' ), get_class( $this ) ) ) );
			}

			self::$instance = $this;
		}

		/**
		 * Retrieves the current instance.
		 *
		 * @return self
		 */
		public static function this() {
			return self::$instance;
		}

		/**
		 * Wrapper function for the registration of a cookie with WordPress.
		 *
		 * @param string $name                    The name of the cookie.
		 * @param string $plugin_or_service       Plugin or service that sets cookie (e.g. Google Maps).
		 * @param string $category                One of 'functional', 'preferences', 'statistics-anonymous', 'statistics', or 'marketing'.
		 * @param string $expires                 Time until the cookie expires.
		 * @param string $function                What the cookie is meant to do (e.g. 'Store a unique User ID').
		 * @param string $collected_personal_data Type of personal data that is collected. Only needs to be filled in if `$is_personal_data` is `true`.
		 * @param bool   $member_cookie           Whether the cookie is relevant for members of the site only.
		 * @param bool   $administrator_cookie    Whether the cookie is relevant for administrators only.
		 * @param string $type                    One of 'HTTP', 'LOCALSTORAGE', or 'API'.
		 * @param string $domain                  Optional. Domain on which the cookie is set. Defaults to the current site URL.
		 */
		public function add_cookie_info( $name, $plugin_or_service, $category, $expires, $function, $collected_personal_data = '', $member_cookie = false, $administrator_cookie = false, $type = 'HTTP', $domain = '' ) {

			// If the domain is not passed, we assume it's first party, from this domain.
			if ( empty( $domain ) ) {
				$domain = site_url();
			}

			$this->registered_cookies[ $name ] = array(
				'plugin_or_service'     => sanitize_text_field( $plugin_or_service ),
				'category'              => wp_validate_consent_category( $category ),
				'expires'               => sanitize_text_field( $expires ),
				'function'              => sanitize_text_field( $function ),
				'collectedPersonalData' => sanitize_text_field( $collected_personal_data ),
				'memberCookie'          => (bool) $member_cookie,
				'administratorCookie'   => (bool) $administrator_cookie,
				'domain'                => esc_url_raw( $domain ),
				'type'                  => sanitize_text_field( $type ),
			);
		}


		/**
		 * Get cookie info for one specific cookie, or for all cookies registered.
		 *
		 * @param string|bool $name The name of the cookie.
		 *
		 * @return array
		 */
		public function get_cookie_info( $name = false ) {
			if ( $name && isset( $this->registered_cookies[ $name ] ) ) {
				return $this->registered_cookies[ $name ];
			}

			return $this->registered_cookies;
		}
	}
}
