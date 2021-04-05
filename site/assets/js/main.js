

// {
//     //  basic config parameters
//     elements: "body", //selector for element IDs that are going to be swapped (e.g. "#el1, #el2, #el3")
//         selector : "a:not(.no-ajaxy)", //selector for links to trigger swapping - not elements to be swapped - i.e. a selection of links
//             forms : "form:not(.no-ajaxy)", // selector for ajaxifying forms - set to "false" to disable
//                 canonical : false, // Fetch current URL from "canonical" link if given, updating the History API.  In case of a re-direct...
//                     refresh : false, // Refresh the page even if link clicked is current page

//                         // visual effects settings
//                         requestDelay : 0, //in msec - Delay of Pronto request
//                             scrolltop : "s", // Smart scroll, true = always scroll to top of page, false = no scroll
//                                 bodyClasses : false, // Copy body classes from target page, set to "true" to enable

//                                     // script and style handling settings, prefetch
//                                     deltas : true, // true = deltas loaded, false = all scripts loaded
//                                         asyncdef : true, // default async value for dynamically inserted external scripts, false = synchronous / true = asynchronous
//                                             alwayshints : false, // strings, - separated by ", " - if matched in any external script URL - these are always loaded on every page load
//                                                 inline : true, // true = all inline scripts loaded, false = only specific inline scripts are loaded
//                                                     inlinesync : true, // synchronise inline scripts loading by adding a central tiny delay to all of them
//                                                         inlinehints : false, // strings - separated by ", " - if matched in any inline scripts - only these are executed - set "inline" to false beforehand
//                                                             inlineskip : "adsbygoogle", // strings - separated by ", " - if matched in any inline scripts - these are NOT are executed - set "inline" to true beforehand 
//                                                                 inlineappend : true, // append scripts to the main content element, instead of "eval"-ing them
//                                                                     style : true, // true = all style tags in the head loaded, false = style tags on target page ignored
//                                                                         prefetchoff : false, // Plugin pre-fetches pages on hoverIntent - true = set off completely // strings - separated by ", " - hints to select out

//                                                                             // debugging & advanced settings
//                                                                             verbosity : 0, //Debugging level to console: default off.   Can be set to 10 and higher (in case of logging enabled)
//                                                                                 memoryoff : false, // strings - separated by ", " - if matched in any URLs - only these are NOT executed - set to "true" to disable memory completely
//                                                                                     cb : 0, // callback handler on completion of each Ajax request - default 0
//                                                                                         pluginon : true, // Plugin set "on" or "off" (==false) manually
//                                                                                             passCount: false // Show number of pass for debugging
// };

// let ajaxify = new Ajaxify({ elements: 'main', requestDelay: 100, scrolltop: true, });

// jQuery(window).on("pronto.request", fRequest)
//     .on("pronto.render", fRender);

// function fRequest() {
//     jQuery("#content").fadeOut(100); // requestDelay setting must be same as this animation duration
// }

// function fRender() {
//     jQuery("#content").stop(true, false).fadeIn(500);
// }

function toggleSideMenu() {
    // document.querySelector(" .hamburger").classList.toggle('active');
    document.body.classList.toggle("sideMenuOpen"); // //
    //    document.getElementById('aside').style.width = '250px'; // document.getElementById('main').style.marginLeft='250px' ;
}
window.onresize = () => {
    if (document.body.classList.contains("sideMenuOpen")) toggleSideMenu();
};