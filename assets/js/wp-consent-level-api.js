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
        //set the changed content levels in wp cookies
        console.log(consentLevels);

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

});