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
Simple Consent API to read and register the current consent category

= What problem does this plugin solve? =
Currently it is possibly to block third party services like Facebook, Google Maps, Twitter, etc. But if a WordPress plugin places a PHP cookie,
a cookie banner plugin cannot prevent this. Although the majority of cookies that are placed by first party tools, some are not.

Secondly, there are plugins that integrate the cookie placing code on the clientside in javascript files that, when blocked, break the site.
Or, if such a plugin's javascript is minified, causing the URL to be unrecognizable, it won't get detected by an automatic blocking script.

Lastly, the cookie blocking approach requires a list of all types of URL's that place cookies. A generic API where plugins adhere to can greatly
facilitate a webmaster in getting a site compliant.

= Does usage of this API prevent third party cookies from being set? =
Primary this API is aimed at compliant setting of first party cookies by WordPress plugins. If such a plugin triggers for example Facebook,
usage of this API will be of help. If a user embeds a facebook iframe, a cookie blocker is need that initially disables the iframe and or scripts.

Third party scripts have to blocked by a cookie blocking functionality
in a Cookie Banner plugin. To do this in core would be to intrusive, and is also not applicable to all users: only users with visitors in opt in regions
require such a feature. Such a feature als has risks of breaking things. Additionally, blocking these and showing a nice placeholder, requires even
more sophisticated code, all of which should in my opinion not be part of WordPress core, for the same reasons.

= How does it work? =
There are two indicators that together tell if consent is given for a certain consent category, e.g. "marketing": the consent_type, which
can be opt-in, opt-out, our other possible consent_types, and the user's choice, not set, allow or deny.

The consent_type is function that wraps a filter, "wp_get_consent_type". If there's no cookie banner plugin to set it, it will return false.

This will cause all consent categories to return true, allowing cookies to be set on all categories.

If opt-in is set, a category will only return true if the value is "allow", if the consent_type is opt-out, it will return true if not set or allow.

Any code suggestions? We're on [GitHub](https://github.com/rlankhorst/wp-consent-level-api) as well!

== Installation ==
To install this plugin:

1. Download the plugin
2. Upload the plugin to the wp-content/plugins directory,
3. Go to “plugins” in your WordPress admin, then click activate.

== Frequently asked questions ==
= Does this plugin block cookies from being placed? =
No, this plugin provides a framework through which plugins can know if they are allowed to place cookies.
The plugin requires both a cookie banner plugin for consent management, and a plugin that follows the consent level as can be read from this API.
== Changelog ==
= 1.0.0 =

== Upgrade notice ==

== Screenshots ==


