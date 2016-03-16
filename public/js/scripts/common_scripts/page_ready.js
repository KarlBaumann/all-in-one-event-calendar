/**
 * @license RequireJS domReady 2.0.0 Copyright (c) 2010-2012, The Dojo Foundation All Rights Reserved.
 * Available via the MIT or new BSD license.
 * see: http://github.com/requirejs/domReady for details
 */

timely.define("domReady",[],function(){function e(e){var t;for(t=0;t<e.length;t++)e[t](o)}function t(){var t=u;s&&t.length&&(u=[],e(t))}function n(){s||(s=!0,l&&clearInterval(l),t())}function r(e){return s?e(o):u.push(e),r}var i=typeof window!="undefined"&&window.document,s=!i,o=i?document:null,u=[],a,f,l;if(i){if(document.addEventListener)document.addEventListener("DOMContentLoaded",n,!1),window.addEventListener("load",n,!1);else if(window.attachEvent){window.attachEvent("onload",n),f=document.createElement("div");try{a=window.frameElement===null}catch(c){}f.doScroll&&a&&window.external&&(l=setInterval(function(){try{f.doScroll(),n()}catch(e){}},30))}(document.readyState==="complete"||document.readyState==="interactive")&&n()}return r.version="2.0.0",r.load=function(e,t,n,i){i.isBuild?n(null):r(n)},r}),timely.require(["jquery_timely","domReady"],function(e,t){t(function(){e(document).trigger("page_ready.ai1ec")})}),timely.define("scripts/common_scripts/page_ready",function(){});