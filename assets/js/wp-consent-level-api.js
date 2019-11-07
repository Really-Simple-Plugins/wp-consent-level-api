jQuery(document).ready(function ($) {
    'use strict';


    /**
     * cookie placing plugin can listen to consent level change
     */

    // $(document).on("wpListenForConsentLevelChange", myScriptHandler);
    // function myScriptHandler(consentLevels) {
    //     if (consentLevels('marketing')==='allow'){
    //         //do something with level marketing
    //     }
    // }


    /**
     * cookiebanner should trigger event when consent category changes
     * @type {string}
     */
    // consentLevels["marketing"] ="allow";
    // $.event.trigger({
    //     type: "wpApplyConsentLevelChange",
    //     consentLevels: consentLevels,
    // });

    /**
     *    processing changes on the hook fired by the cookie banner wpApplyConsentLevelChange
     *    and firing hooks for other plugins to hook into
     */

    function wpProcessConsentLevelChange(consentLevels) {
        console.log(consentLevels);
        //foreach consent level, set the value in a cookie
        for (var key in consentLevels) {
            if (consentLevels.hasOwnProperty(key)) {
                clapiSetCookie('wp_consent_'+key, consentLevels[key][0], cookie_expiry);
            }
        }

        //trigger a hook for plugins to hook into
        $.event.trigger({
            type: "wpListenForConsentLevelChange",
            consentLevels: consentLevels,
        });

    }
    $(document).on("wpApplyConsentLevelChange", wpProcessConsentLevelChange);

    /**
     * to retrieve consentlevel directly
     */

    function wpHasConsentLevel(){

    }


    function clapiSetCookie(name, value, days) {
        var secure = ";secure";

        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = ";expires=" + date.toGMTString();

        if (window.location.protocol !== "https:") secure = '';

        document.cookie = name + "=" + value + secure + expires + ";path=/";
    }

    function clapiGetCookie(cname) {
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