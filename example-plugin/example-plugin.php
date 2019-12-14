<?php // phpcs:ignore -- Ignore the "\r\n" notice for some machines.

/**
 * Plugin Name: Example plugin for the WP Consent Level API
 * Plugin URI: https://www.wordpress.org/plugins/wp-consent-api
 * Description: Example plugin to demonstrate usage of the Consent API
 * Version: 1.0.0
 * Text Domain: wp-consent-api
 * Domain Path: /languages
 * Author: WP privacy team
 * Author URI:
 */

/**
 * Tell the consent API we're following the api guidelines
 */
$plugin = plugin_basename( __FILE__ );

add_filter(
	"wp_consent_api_registered_$plugin",
	function() {
		return true;
	}
);

add_action( 'wp_enqueue_scripts', 'example_plugin_enqueue_assets' );
function example_plugin_enqueue_assets( $hook ) {
	wp_enqueue_script( 'example-plugin', plugin_dir_url( __FILE__ ) . 'main.js', array( 'jquery' ), CONSENT_API_VERSION, true );
}

add_shortcode( 'example-plugin-shortcode', 'example_plugin_load_document' );

function example_plugin_load_document( $atts = array(), $content = null, $tag = '' ) {
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );
	ob_start();

	// override default attributes with user attributes
	$atts = shortcode_atts( array( 'type' => false ), $atts, $tag );
	?>

	<div id="example-plugin-content">
		<div class="functional-content">
			<h1>No consent has been given yet. </h1>
		</div>
		<div class="marketing-content" style="display:none">
			<h1>Woohoo! let's start tracking you :)</h1>
		</div>
	</div>

	<?php
	return ob_get_clean();
}
