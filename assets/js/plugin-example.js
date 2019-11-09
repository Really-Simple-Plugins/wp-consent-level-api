jQuery(document).ready(function ($) {
    /**
     * cookie placing plugin can listen to consent change
     *
     */

    $(document).on("wp_listen_for_consent_change", myPluginScriptHandler);
    function myPluginScriptHandler(data) {
        console.log("plugin script handler");
        var changedConsentCategory = data.changedConsentCategory;
        for (var key in changedConsentCategory) {
            if (changedConsentCategory.hasOwnProperty(key)) {
                if (key==='marketing' && changedConsentCategory[key]==='allow'){
                    console.log("set marketing cookie")
                }
            }
        }
    }
});

