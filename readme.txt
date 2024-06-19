=== WP Consent API ===
Contributors: RogierLankhorst, xkon, aurooba, mujuonly, phpgeekdk, paapst, aahulsebos, pputzer, markwolters
Tags: consent, privacy, cookies, api, compliance
Requires at least: 5.0
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tested up to: 6.5
Requires PHP: 7.2
Stable tag: 1.0.7

Simple Consent API to read and register the current consent category.

== Description ==
WP Consent API is a plugin that standardizes the communication of accepted consent categories between plugins. It requires a cookie banner plugin and, at least, one other plugin that supports the WP Consent API.

With this plugin, all supporting plugins can use the same set of methods to read and register the current consent category, allowing consent management plugins and other plugins to work together, improving compliance with privacy laws.

WARNING: the plugin itself will not handle consent. It will show you how many plugins you have without Consent API support and will improve compliance on your site by ensuring smooth communication between cookie banner plugins and plugins that set cookies or track user data.

= What problem does this plugin solve? =
Currently, it is possible for a consent management plugin to block third-party services like Facebook, Google Maps, Twitter, etc. But if a WordPress plugin places a PHP cookie, a consent management plugin cannot prevent this.

Secondly, some plugins integrate the tracking code on the clientside in javascript files that, when blocked, break the site.

Or, if such a plugin's javascript is minified, causing the URL to be unrecognizable and won't get detected by an automatic blocking script.

Lastly, the blocking approach requires a list of all types of URL's that tracks data. A generic API where plugins adhere to can greatly
facilitate a webmaster in getting a site compliant.

= Does usage of this API prevent third-party services from tracking user data? =

Primary this API is aimed at compliant first-party cookies or tracking by WordPress plugins. If such a plugin triggers, for example, Facebook,
usage of this API will be of help. If a user embeds a Facebook iframe, a blocking tool is needed that initially disables the iframe and or scripts.

Third-party scripts have to blocked by blocking functionality in a consent management plugin. To do this in core would be to intrusive, and is also not applicable to all users: only users with visitors from opt-in regions such as the European Union require such a feature. Such a feature also has a risk of breaking things. Additionally, blocking these and showing a nice placeholder requires even more sophisticated code, all of which should in my opinion not be part of WordPress core, for the same reasons.

= How does it work? =
There are two indicators that together tell if consent is given for a specific consent category, e.g., "marketing":
1) the region based consent_type, which
can be opt-in, opt-out, or other possible consent_types;
2) and the visitor's choice: not set, allow, or deny.

The consent_type is a function that wraps a filter, "wp_get_consent_type". If there's no consent management plugin to set it, it will return false. This will cause all consent categories to return true, allowing cookies to be set on all categories.

If opt-in is set using this filter, a category will only return true if the value of the visitor's choice is "allow".

If the region based consent_type is opt-out, it will return true if the visitor's choice is not set or is "allow".

Clientside, a consent management plugin can dynamically manipulate the consent type and set several cookie categories.

A plugin can use a hook to listen for changes or check the value of a given category.

Categories and most other stuff can be extended with a filter.

## Existing integrations
Categorized, and sorted alphabeticaly

= Example plugin =
- [Example plugin](https://github.com/rlankhorst/consent-api-example-plugin). The plugin basically consists of a shortcode, with a div that shows a tracking or not tracking message. No actual tracking is done :-)

= Consent Management Providers =
- [Complianz GDPR/CCPA](https://wordpress.org/plugins/complianz-gdpr/).
- [Cookiebot](https://wordpress.org/plugins/cookiebot).
- [GDPR Cookie Compliance](https://wordpress.org/plugins/gdpr-cookie-compliance/).

= Consent Requiring Plugins =
- [AddToAny](https://wordpress.org/plugins/add-to-any/).
- [AFL UTM Tracker Plugin](https://www.appfromlab.com/product/woocommerce-utm-tracker-plugin/).
- [Google Site Kit](https://wordpress.org/plugins/google-site-kit/).
- [Pixel Manager for WooCommerce](https://wordpress.org/plugins/woocommerce-google-adwords-conversion-tracking-tag/).
- [Woo](https://wordpress.org/plugins/woocommerce/).
- [WP Statistics](https://wordpress.org/plugins/wp-statistics/).

## Demo site
[wpconsentapi.org](https://wpconsentapi.org/)
Below are the plugins used to set up the demo site:

- Complianz
- [The example plugin](https://github.com/rlankhorst/consent-api-example-plugin)

= javascript, consent management plugin =
`
//set consent type
window.wp_consent_type = 'optin'

//dispatch event when consent type is defined. This is useful if the region is detected server side, so the consent type is defined later during the pageload
let event = new CustomEvent('wp_consent_type_defined');
document.dispatchEvent( event );

//consent management plugin sets cookie when consent category value changes
wp_set_consent('marketing', 'allow');
`
= javascript, tracking plugin =
`
//listen to consent change event
document.addEventListener("wp_listen_for_consent_change", function (e) {
  var changedConsentCategory = e.detail;
  for (var key in changedConsentCategory) {
    if (changedConsentCategory.hasOwnProperty(key)) {
      if (key === 'marketing' && changedConsentCategory[key] === 'allow') {
        console.log("just given consent, track user")
      }
    }
  }
});

//basic implementation of consent check:
if (wp_has_consent('marketing')){
  activateMarketing();
  console.log("set marketing stuff now!");
} else {
  console.log("No marketing stuff please!");
}
`
= PHP =
`
//declare complianz with consent level API
$plugin = plugin_basename( __FILE__ );
add_filter( "wp_consent_api_registered_{$plugin}", '__return_true' );

/**
* Example how a plugin can register cookies with the consent API
 * These cookies can then be shown on the front-end, to the user, with wp_get_cookie_info()
 */

function my_wordpress_register_cookies(){
	if ( function_exists( 'wp_add_cookie_info' ) ) {
		wp_add_cookie_info( 'AMP_token', 'AMP', 'marketing', __( 'Session' ), __( 'Store a unique User ID.' ) );
	}
}
add_action('plugins_loaded', 'my_wordpress_register_cookies');


if (wp_has_consent('marketing')){
//do marketing stuff
}
`
Any code suggestions? We're on [GitHub](https://github.com/rlankhorst/wp-consent-level-api) as well!

== Installation ==
To install this plugin:

Download the plugin
Upload the plugin to the wp-content/plugins directory,
Go to "plugins" in your WordPress admin, then click activate.

== Frequently asked questions ==
= Does this plugin block third party services from placing cookies? =
No, this plugin provides a framework through which plugins can know if they are allowed to place cookies or track user data.
The plugin requires both a consent management plugin for consent management and a plugin that follows the consent level as can be read from this API.
= How should I go about integrating my plugin? =
For each action that places cookies or tracks user data, you should consider what type of tracking takes place. There are 5 consent categories:
functional, statistics-anonymous, statistics, preferences, marketing. These are explained below. Your code should check if consent has been given for the applicable category. If no cookie banner plugin is active,
the Consent API will always return with consent (true).
Please check out the example plugin, and the above code examples.
= What is the difference between the consent categories? =

Statistics:
Cookies or any other form of local storage that are used exclusively for statistical purposes (Analytics Cookies).

Statistics-anonymous:
Cookies or any other form of local storage that are used exclusively for anonymous statistical purposes (Anonymous Analytics Cookies), that are placed on a first party domain, and that do not allow identification of particular individuals.

Marketing:
Cookies or any other form of local storage required to create user profiles to send advertising or to track the user on a website or across websites for simular marketing purposes.

Functional:
The cookie or any other form of local storage is used for the sole purpose of carrying out the transmission of a communication over an electronic communications network;

OR

The technical storage or access is strictly necessary for the legitimate purpose of enabling the use of a specific service explicitly requested by the subscriber or user. If cookies are disabled, the requested functionality will not be available. This makes them essential functional cookies.

Preferences:
Cookies or any other form of local storage that can not be seen as statistics, statistics-anonymous, marketing or functional, and where the technical storage or access is necessary for the legitimate purpose of storing preferences.

== Changelog ==
= 1.0.7 =
* Tested up to

= 1.0.6 =
* Tested up to

= 1.0.5 =
* tested up to

= 1.0.4 =
* Improvement: only fire on change event if an actual change was detected on the consent.

= 1.0.3 =
* Fix: wp_has_consent function contained a condition which caused it to always return true on serverside optin consent checks.

= 1.0.2 =
* Fix $collected_personal_data documentation, as $is_personal_data was deprecated (props @jazzsequence, https://github.com/rlankhorst/wp-consent-level-api/issues/54)
* Fix the docblock so it reflects the actual value returned (props @jazzsequence, https://github.com/rlankhorst/wp-consent-level-api/issues/52)
* Styling changes to site health
* Code structure changes
* Removed is_personal_data property, as it's implicitly contained in the personaldata property
* Made cookie prefix filterable props @jazzsequence

= 1.0.1 =
* Cookiebot added as existing integration
* Some small improvements to comply with WordPress coding styles

= 1.0.0 =
* changed consent_api_setcookie into consent_api_set_cookie for naming consistency with the getcookie method
* added prefix in javascript set and get methods, to be compatible with PHP cookies, props @phpgeek
* removed duplicate statistics category, props @phpgeek

== Upgrade notice ==

== Screenshots ==
