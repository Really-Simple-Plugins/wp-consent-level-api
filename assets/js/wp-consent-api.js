'use strict';
/**
 * Set consent_type as passed by localize_script, and trigger a hook so geo ip scripts can alter it as needed.
 * It's set on document ready, so the consent management plugin can first add an event listener.
 *
 *
 * Edit: chronologically it seems difficult to create a sort of filter for the consent type.
 * Let's change it so cookiebanners are just required to set it, if it's not available, we use a default, as defined here.
 *
 * This way, if a consent management plugin does not define a consenttype, the one passed here will be used, and it will still work.
 *
 *
 */

window.wp_fallback_consent_type = consent_api.consent_type;
window.waitfor_consent_hook = consent_api.waitfor_consent_hook;

/**
 * Check if a user has given consent for a specific category.
 *
 * @param {string} item The item to check consent against.
 * @param {string} type category or service
 */
function wp_has_consent(item, type= 'category') {
	let has_consent = false;
	console.log("has consent check for "+item);
	//for service consent, we start checking if the service's category already has consent. If so, return true and bail.
	if ( 'service' === type ) {
		let category = wp_get_service_category( item );
		if ( wp_has_consent(category) ) {
			has_consent = true;
		}
	}

	let consent_type;
    if ( typeof (window.wp_consent_type) !== "undefined" ){
        consent_type = window.wp_consent_type;
    }  else {
        consent_type = window.wp_fallback_consent_type
    }

    let cookie_value = consent_api_get_cookie(consent_api.cookie_prefix + '_' + item);
    if ( !consent_type ) {
        //if consent_type is not set, there's no consent management, we should return true to activate all cookies
        has_consent = true;
    } else if (consent_type.indexOf('optout') !== -1 && cookie_value === '') {
        //if it's opt out and no cookie is set we should also return true
		has_consent = true;
    } else {
        //all other situations, return only true if value is allow
		has_consent = (cookie_value === 'allow');
    }

    return has_consent;
}

/**
 * Retrieve the category of a registered service
 *
 * @param {string} service
 * @returns {string}
 */
function wp_get_service_category( service ) {
	let services = consent_api.services;
	services.forEach(function(service_item) {
		if ( service_item.name === service ) {
			return service_item.category;
		}
	});
	return 'marketing';
}

/**
 * Set cookie by consent type.
 *
 * @param {string} name The cookie name to set.
 * @param {string} value The cookie value to set.
 */
function consent_api_set_cookie(name, value) {
    let secure = ";secure";
	let days = consent_api.cookie_expiration;
	let date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
	let expires = ";expires=" + date.toGMTString();

    if (window.location.protocol !== "https:") secure = '';

    document.cookie = name + "=" + value + secure + expires + ";path=/";
}

/**
 * Retrieve a cookie by name.
 *
 * @param {string} name The name of the cookie to get data from.
 */
function consent_api_get_cookie(name) {
    name = name + "=";
    var cookies = window.document.cookie.split(';');

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].trim();
        if (cookie.indexOf(name) == 0)
            return cookie.substring(name.length, cookie.length);
    }

    //If we get to this point, that means the cookie wasn't found, we return an empty string.
    return "";
}

/**
 * Set a new consent category value.
 *
 * @param {string} name The consent category or service to update.
 * @param {string} value The value to update the consent category or service to.
 */
function wp_set_consent(name, value) {
    let event;
    if (value !== 'allow' && value !== 'deny') return;
	let previous_value = consent_api_get_cookie(consent_api.cookie_prefix + '_' + name);
    consent_api_set_cookie(consent_api.cookie_prefix + '_' + name, value);

    //do not trigger a change event if nothing has changed.
    if ( previous_value === value ) return;

    let changedConsentCategory = [];
    changedConsentCategory[name] = value;
    try {
        // For modern browsers except IE:
        event = new CustomEvent('wp_listen_for_consent_change', {detail: changedConsentCategory});
    } catch (err) {
        // If IE 11 (or 10 or 9...?)
        event = document.createEvent('Event');
        event.initEvent('wp_listen_for_consent_change', true, true);
        event.detail = changedConsentCategory;
    }
    // Dispatch/Trigger/Fire the event
    document.dispatchEvent(event);
}



