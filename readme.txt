=== WP Consent API ===
Contributors: RogierLankhorst
Tags: consent, privacy
Requires at least: 5.0
License: GPL2
Tested up to: 5.3
Requires PHP: 5.6
Stable tag: 1.0.0

Simple Consent API to read and register the current consent category

== Description ==
WP Consent API is a plugin which standardizes communication of accepted consent categories between plugins. It requires a cookie banner plugin and
at least on other plugin that supports the WP Consent API.
With this plugin, all supporting plugins can use the same set of methods to read and register the current consent category, allowing consent management plugins and other plugins to work together, improving compliancy.

WARNING: the plugin itself is not handling consent. It will show you how many plugins you have without Consent API support, and will improve compliancy on your site by ensuring a smooth communication between cookie banner plugins
and cookie placing plugins.

= What problem does this plugin solve? =
Currently it is possibly for a consent management plugin to block third party services like Facebook, Google Maps, Twitter, etc. But if a WordPress plugin places a PHP cookie, a consent management plugin cannot prevent this.

Secondly, there are plugins that integrate the cookie placing code on the clientside in javascript files that, when blocked, break the site.
Or, if such a plugin's javascript is minified, causing the URL to be unrecognizable, it won't get detected by an automatic blocking script.

Lastly, the cookie blocking approach requires a list of all types of URL's that place cookies. A generic API where plugins adhere to can greatly
facilitate a webmaster in getting a site compliant.

= Does usage of this API prevent third party cookies from being set? =
Primary this API is aimed at compliant setting of first party cookies by WordPress plugins. If such a plugin triggers for example Facebook,
usage of this API will be of help. If a user embeds a facebook iframe, a cookie blocker is needed that initially disables the iframe and or scripts.

Third party scripts have to blocked by a cookie blocking functionality
in a consent management plugin. To do this in core would be to intrusive, and is also not applicable to all users: only users with visitors from opt in regions such as the European Union require such a feature. Such a feature also has a risk of breaking things. Additionally, blocking these and showing a nice placeholder, requires even more sophisticated code, all of which should in my opinion not be part of WordPress core, for the same reasons.

= How does it work? =
There are two indicators that together tell if consent is given for a certain consent category, e.g. "marketing":
1) the region based consent_type, which
can be opt-in, opt-out, or other possible consent_types;
2) and the visitor's choice: not set, allow or deny.

The consent_type is a function that wraps a filter, "wp_get_consent_type". If there's no consent management plugin to set it, it will return false. This will cause all consent categories to return true, allowing cookies to be set on all categories.

If opt-in is set using this filter, a category will only return true if the value of the visitor's choice is "allow".

If the region based consent_type is opt-out, it will return true if the visitor's choice is not set or is "allow".

Clientside, a consent management plugin can dynamically manipulate the consent type, and set the several cookie categories.

A plugin can use a hook to listen for changes, or check the value of a given category.

Categories, and most other stuff can be extended with a filter.
= Existing integrations =
- Complianz https://github.com/rlankhorst/complianz-gdpr/compare/consent-API-integration
- Example plugin shipped with this plugin. The plugin basically consists of a shortcode, with a div that shows
a tracking or not tracking message. No actual tracking is done :)

## Demo site
https://wpconsentapi.org/

plugins used to set this up:
- Complianz
- The example plugin https://github.com/rlankhorst/consent-api-example-plugin


= javascript, consent management plugin =
//dynamically set consent type
window.wp_consent_type = 'optin';

//consent management plugin sets cookie when consent category value changes
wp_set_consent('marketing', 'allow');

= javascript, tracking plugin =

//listen to consent change event
document.addEventListener("wp_listen_for_consent_change", function (e) {
var changedConsentCategory = e.detail;
for (var key in changedConsentCategory) {
if (changedConsentCategory.hasOwnProperty(key)) {
if (key === 'marketing' && changedConsentCategory[key] === 'allow') {
console.log("just given consent, set marketing cookie")
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

= PHP =
if (wp_has_consent('marketing')){
//do marketing stuff
}

Any code suggestions? We're on GitHub as well!

== Installation ==
To install this plugin:

Download the plugin
Upload the plugin to the wp-content/plugins directory,
Go to “plugins” in your WordPress admin, then click activate.
== Frequently asked questions ==
= Does this plugin block cookies from being placed? =
No, this plugin provides a framework through which plugins can know if they are allowed to place cookies.
The plugin requires both a consent management plugin for consent management, and a plugin that follows the consent level as can be read from this API.
== Changelog ==
= 1.0.0 =
* changed consent_api_setcookie into consent_api_set_cookie for naming consistency with the getcookie method
* added prefix in javascript set and get methods, to be compatible with PHP cookies, props @phpgeek
* removed duplicate statistics category, props @phpgeek

== Upgrade notice ==

== Screenshots ==
