jQuery(document).ready(function ($) {
    'use strict';

    /**
     * Set consent_type as passed by localize_script, and trigger a hook so geo ip scripts can alter it as needed.
     */
    window.wp_consent_type = cl_api.consent_type;
    $.event.trigger({
        type: "wpSetConsentTypeChange",
        consent_type: window.wp_consent_type,
    });


    /**
     *    processing changes on the hook fired by the cookie banner wpApplyConsentChange
     *    and firing hooks for other plugins to hook into
     */

    function wp_process_consent_change(data) {
        //foreach consent categories, set the value in a cookie
        var consentCategories = data.consentCategories;
        for (var key in consentCategories) {
            if (consentCategories.hasOwnProperty(key)) {
                cl_api_setcookie('wp_consent_'+key, consentCategories[key]);
            }
        }

        //trigger a hook for plugins to hook into
        $.event.trigger({
            type: "wp_listen_for_consent_change",
            consentCategories: consentCategories,
        });

    }
    $(document).on("wp_apply_consent_change", wp_process_consent_change);

    /**
     * cookiebanner should trigger event when consent category changes
     * @type {string}
     */
    var consentCategories = [];
    consentCategories["marketing"] ="allow";
    $.event.trigger({
        type: "wp_apply_consent_change",
        consentCategories: consentCategories,
    });


    /**
     * cookie placing plugin can listen to consent change
     */

    // $(document).on("wp_listen_for_consent_change", myScriptHandler);
    // function myScriptHandler(consentCategories) {
    //     if (consentCategories('marketing')==='allow'){
    //         //do something with category marketing
    //     }
    // }



    /**
     * to retrieve consent directly
     */

    function wpHasConsent(category){
        var consent_type = window.wp_consent_type;
        var has_consent_level = false;
        var cookie_value = cl_api_get_cookie(category);

        if (!consent_type) {
            //if consent_type is not set, there's no consent management, we should return true to activate all cookies
            has_consent_level = true;

        } else if (consent_type.indexOf('optout')!==-1 && cookie_value === '') {
            //if it's opt out and no cookie is set we should also return true
            has_consent_level = true;

        } else if (cookie_value ==='allow'){
            //all other situations, return only true if value is allow
            has_consent_level = true;
        } else {
            has_consent_level = false;
        }

        return has_consent_level;
    }


    function cl_api_setcookie(name, value) {
        var secure = ";secure";
        var days = cl_api.cookie_expiration;
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = ";expires=" + date.toGMTString();

        if (window.location.protocol !== "https:") secure = '';

        document.cookie = name + "=" + value + secure + expires + ";path=/";
    }

    function cl_api_get_cookie(cname) {
        var name = cname + "="; //Create the cookie name variable with cookie name concatenate with = sign
        var cArr = window.document.cookie.split(';'); //Create cookie array by split the cookie by ';'

        //Loop through the cookies and return the cooki value if it find the cookie name
        for (var i = 0; i < cArr.length; i++) {
            var c = cArr[i].trim();
            //If the name is the cookie string at position 0, we found the cookie and return the cookie value
            if (c.indexOf(name) == 0)
                return c.substring(name.length, c.length);
        }

        //If we get to this point, that means the cookie wasn't found, we return an empty string.
        return "";
    }

});