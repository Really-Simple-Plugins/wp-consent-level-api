<?php  // phpcs:ignore WordPress.Files.Filename.InvalidClassFileName
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
 *
 * @wordpress-plugin
 * Plugin Name:       WP Consent API
 * Plugin URI:        https://wordpress.org/plugins/wp-consent-api
 * Description:       Consent Level API to read and register the current consent level for cookie management and improving compliance with privacy laws.
 * Version:           1.0.8
 * Author:            RogierLankhorst
 * Author URI:        https://github.com/rlankhorst/wp-consent-level-api
 * Requires at least: 5.0
 * Requires PHP:      5.6
 * License:           GPL2+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 */

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you cannot directly access this file.' );
}


if ( ! function_exists( 'wp_consent_api_activation_check' ) ) {
	/**
	 * Checks if the plugin can safely be activated, at least php 5.6 and wp 5.0
	 *
	 * @since 1.0.0
	 */
	function wp_consent_api_activation_check() {
		global $wp_version;

		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( esc_html( __( 'This plugin requires PHP 5.6 or higher', 'wp-consent-api' ) ) );
		}

		if ( version_compare( $wp_version, '5.0', '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( esc_html( __( 'This plugin requires WordPress 5.0 or higher', 'wp-consent-api' ) ) );
		}
	}
}
register_activation_hook( __FILE__, 'wp_consent_api_activation_check' );

if ( ! class_exists( 'WP_CONSENT_API' ) ) {
	/**
	 * WP_CONSENT_API class.
	 */
	class WP_CONSENT_API {
		/**
		 * Instance.
		 *
		 * @since 1.0.0
		 *
		 * @var WP_CONSENT_API|null
		 */
		private static $instance;

		/**
		 * Config.
		 *
		 * @var WP_CONSENT_API_CONFIG
		 */
		public static $config;


		/**
		 * Site Health Checks.
		 *
		 * @var WP_CONSENT_API_SITE_HEALTH
		 */
		public static $site_health;

		/**
		 * Cookie info
		 *
		 * @var WP_CONSENT_API_COOKIE_INFO
		 */
		public static $cookie_info;

		/**
		 * Instantiate the class.
		 *
		 * @since 1.0.0
		 *
		 * @return WP_CONSENT_API
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function __construct() {
			$this->setup_constants();
			$this->includes();

			self::$config      = new WP_CONSENT_API_CONFIG();
			self::$site_health = new WP_CONSENT_API_SITE_HEALTH();
			self::$cookie_info = new WP_CONSENT_API_COOKIE_INFO();
		}

		/**
		 * Define Consent API related constants.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function setup_constants() {
			define( 'WP_CONSENT_API_URL', plugin_dir_url( __FILE__ ) );
			define( 'WP_CONSENT_API_PATH', plugin_dir_path( __FILE__ ) );
			define( 'WP_CONSENT_API_PLUGIN', plugin_basename( __FILE__ ) );
			define( 'WP_CONSENT_API_VERSION', '1.0.8' );
			define( 'WP_CONSENT_API_PLUGIN_FILE', __FILE__ );
		}

		/**
		 * Include the extra plugin files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function includes() {
			require_once WP_CONSENT_API_PATH . 'config.php';
			require_once WP_CONSENT_API_PATH . 'cookie-info.php';
			require_once WP_CONSENT_API_PATH . 'api.php';
			require_once WP_CONSENT_API_PATH . 'site-health.php';
			require_once WP_CONSENT_API_PATH . 'wordpress-comments.php';
		}
	}

	/**
	 * Load the plugins main class.
	 */
	add_action( 'plugins_loaded', array( WP_CONSENT_API::class, 'get_instance' ), 9 );
}
