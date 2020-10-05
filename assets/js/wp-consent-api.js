'use strict';
/**
 * Set consent_type as passed by localize_script, and trigger a hook so geo ip scripts can alter it as needed.
 * It's set on document ready, so the consent management plugin can first add an event listener.
 *
 *
 * Edit: chronologically it seems difficult to create a sort of filter for the consent type.
 * Let's change it so cookiebanners are just required to set it, it it's not available, we use a default, as defined here.
 *
 * This way, if a consent management plugin does not define a consenttype, the one passed here will be used, and it will still work.
 *
 *
 */

window.wp_fallback_consent_type = consent_api.consent_type;
window.waitfor_consent_hook = consent_api.waitfor_consent_hook;

/**
 * to retrieve consent directly
 */

function wp_has_consent(category) {
    var consent_type;
    if (typeof (window.wp_consent_type) !== "undefined"){
        consent_type = window.wp_consent_type;
    }  else {
        consent_type = window.wp_fallback_consent_type
    }

    var has_consent_level = false;
    var cookie_value = consent_api_get_cookie(consent_api.cookie_prefix + '_' + category);

    if (!consent_type) {
        //if consent_type is not set, there's no consent management, we should return true to activate all cookies
        has_consent_level = true;

    } else if (consent_type.indexOf('optout') !== -1 && cookie_value === '') {
        //if it's opt out and no cookie is set we should also return true
        has_consent_level = true;

    } else {
        //all other situations, return only true if value is allow
        has_consent_level = (cookie_value === 'allow');
    }

    return has_consent_level;
}

/**
 * Set cookie by consent type
 * @param name
 */

function consent_api_set_cookie(name, value) {
    var secure = ";secure";
    var days = consent_api.cookie_expiration;
    var date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    var expires = ";expires=" + date.toGMTString();

    if (window.location.protocol !== "https:") secure = '';

    document.cookie = name + "=" + value + secure + expires + ";path=/";
}

/**
 * Get cookie by consent type
 * @param name
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
 * Set a new consent category value
 * @param category
 * @param value
 */

function wp_set_consent(category, value) {
    var event;
    if (value !== 'allow' && value !== 'deny') return;

    consent_api_set_cookie(consent_api.cookie_prefix + '_' + category, value);
    var changedConsentCategory = [];
    changedConsentCategory[category] = value;
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








