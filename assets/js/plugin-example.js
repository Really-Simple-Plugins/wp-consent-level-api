
/**
 * cookie placing plugin can listen to consent change
 *
 */
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



