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
     * cookie placing plugin can listen to consent change
     */

    // $(document).on("wp_listen_for_consent_change", myScriptHandler);
    // function myScriptHandler(consentCategories) {
    //     if (consentCategories('marketing')==='allow'){
    //         //do something with category marketing
    //     }
    // }


    /**
     * cookiebanner should trigger event when consent category changes
     * @type {string}
     */
    // consentCategories["marketing"] ="allow";
    // $.event.trigger({
    //     type: "wp_apply_consent_change",
    //     consentCategories: consentCategories,
    // });

    /**
     *    processing changes on the hook fired by the cookie banner wpApplyConsentChange
     *    and firing hooks for other plugins to hook into
     */

    function wp_process_consent_change(consentCategories) {
        console.log(consentCategories);
        //foreach consent categories, set the value in a cookie
        for (var key in consentCategories) {
            if (consentCategories.hasOwnProperty(key)) {
                cl_api_setcookie('wp_consent_'+key, consentCategories[key][0], cookie_expiry);
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
     * to retrieve consent directly
     */

    function wpHasConsent(category){

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