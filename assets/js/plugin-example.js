
/**
 * cookie placing plugin can listen to consent change
 *
 */
console.log("load plugin example");
document.addEventListener("wp_listen_for_consent_change", function (e) {
    console.log("plugin script handler");
    var changedConsentCategory = e.detail;
    for (var key in changedConsentCategory) {
        if (changedConsentCategory.hasOwnProperty(key)) {
            if (key === 'marketing' && changedConsentCategory[key] === 'allow') {
                console.log("set marketing cookie")
            }
        }
    }
});

/**
 * Or do stuff as soon as the consenttype is defined
 */

jQuery(document).ready(function($) {
    $(document).on("wp_consent_type_defined", myScriptHandler);

    function myScriptHandler(consentData) {
        //your code here
        if (wp_has_consent('marketing')) {
            console.log("do marketing cookie stuff");
        } else {
            console.log("no marketing cookies please");
        }
    }
});

