/**
 * Set consent type, for exapmle when client side geo ip is used
 * The consenttype is used in the wp_has_consent function
 */
document.addEventListener("wp_set_consent_type", function (e) {
    consenttype = e.detail;
    //maybe change consenttype to optout based on user's ip
    window.wp_consent_type = 'optout';
});


/**
 * consent management plugin sets cookie when consent category value changes
 *
 */
wp_set_consent('marketing', 'allow');




