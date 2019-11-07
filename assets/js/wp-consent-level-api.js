jQuery(document).ready(function ($) {
    'use strict';




    //to hook into the event that fires when the scripts are enabled, use script like this:
    $(document).on("cmplzEnableScripts", myScriptHandler);
    function myScriptHandler(consentData) {
        //your code here
        console.log(consentData.consentLevel);
        if (consentData.consentLevel==='all'){
            //do something with level all
        }
    }
});