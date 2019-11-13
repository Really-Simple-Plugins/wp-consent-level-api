/**
 * Set consent type, for exapmle when client side geo ip is used
 * The consenttype is used in the wp_has_consent function
 */

//get consenttype from server, using ajax, based on geoip
window.wp_consent_type = 'optin';

/**
 * consent management plugin sets cookie when consent category value changes
 *
 */
wp_set_consent('marketing', 'allow');




