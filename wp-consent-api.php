<?php
/**
 * Plugin Name: WP Consent Level API
 * Plugin URI: https://www.wordpress.org/plugins/wp-consent-api
 * Description: Simple Consent Level API to read and register the current consent level
 * Version: 1.0.0
 * Text Domain: wp-consent-api
 * Domain Path: /languages
 * Author: WP privacy team
 * Author URI:
 */

/*

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

defined('ABSPATH') or die("you do not have access to this page!");


/**
 * Checks if the plugin can safely be activated, at least php 5.6 and wp 5.0
 * @since 1.0.0
 */
if (!function_exists('cl_api_activation_check')) {
    function cl_api_activation_check()
    {
        if (version_compare(PHP_VERSION, '5.6', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('This plugin requires PHP 5.6 or higher', 'wp-consent-api'));
        }

        global $wp_version;
        if (version_compare($wp_version, '5.0', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('This plugin requires WordPress 5.0 or higher', 'wp-consent-api'));
        }
    }
}
register_activation_hook( __FILE__, 'cl_api_activation_check' );


if (!class_exists('WP_CL_API')) {
    class WP_CL_API
    {

        private static $instance;


        private function __construct()
        {
        }

        public static function instance()
        {

            if (!isset(self::$instance) && !(self::$instance instanceof WP_CL_API)) {
                self::$instance = new WP_CL_API;

                self::$instance->setup_constants();
                self::$instance->includes();

                self::$instance->config = new CL_API_CONFIG();

                self::$instance->hooks();
            }

            return self::$instance;
        }

        private function setup_constants()
        {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            $plugin_data = get_plugin_data(__FILE__);

            define('CL_API_URL', plugin_dir_url(__FILE__));
            define('CL_API_PATH', plugin_dir_path(__FILE__));
            define('CL_API_PLUGIN', plugin_basename(__FILE__));
            $debug = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? time() : '';
            define('CL_API_VERSION', $plugin_data['Version'] . $debug);
            define('CL_API_PLUGIN_FILE', __FILE__);
        }

        private function includes()
        {

            require_once(CL_API_PATH . 'config.php');
            require_once(CL_API_PATH . 'API.php');
        }

        private function hooks()
        {

        }
    }
}

if (!function_exists('WP_CL_API')) {
    function WP_CL_API() {
        return WP_CL_API::instance();
    }

    add_action( 'plugins_loaded', 'WP_CL_API', 9 );
}



/**
 * Load the translation files
 *
 */

if (!function_exists('cl_api_load_translation')) {
    add_action('init', 'cl_api_load_translation', 20);
    function cl_api_load_translation()
    {
        load_plugin_textdomain('wp-consent-api', FALSE, CL_API_PATH . '/config/languages/');
    }
}

