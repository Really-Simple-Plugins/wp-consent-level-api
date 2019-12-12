jQuery(document).ready(function($) {

/**
 * cookie placing plugin can listen to consent change
 *
 */
console.log("load plugin example");
document.addEventListener("wp_listen_for_consent_change", function (e) {
    var changedConsentCategory = e.detail;
    for (var key in changedConsentCategory) {
        if (changedConsentCategory.hasOwnProperty(key)) {
            if (key === 'marketing' && changedConsentCategory[key] === 'allow') {
                console.log("set marketing cookie on user actions");
                activateMarketing();
            }
        }
    }
});

/**
 * Or do stuff as soon as the consenttype is defined
 */
    $(document).on("wp_consent_type_defined", activateMyCookies);
    function activateMyCookies(consentData) {
        //your code here
        if (wp_has_consent('marketing')) {
            console.log("do marketing cookie stuff");
        } else {
            console.log("no marketing cookies please");
        }
    }

    //check if we need to wait for the consenttype to be set
    if (!window.waitfor_consent_hook) {
        console.log("we don't have to wait for the consent type, we can check the consent level right away!");
        if (wp_has_consent('marketing')){
            activateMarketing();
            console.log("set marketing stuff now!");
        } else {
            console.log("No marketing stuff please!");
        }
    }

    /**
     * Do stuff that normally would do stuff like tracking personal user data etc.
     */

    function activateMarketing(){
        console.log("fire marketing");
        $('#example-plugin-content .functional-content').hide();
        $('#example-plugin-content .marketing-content').show();
    }
});
