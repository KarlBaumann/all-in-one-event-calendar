timely.define(["jquery_timely","external_libs/bootstrap_tab"],function(e){var t=function(){return{is_float:function(e){return!isNaN(parseFloat(e))},is_valid_coordinate:function(e,t){var n=t?90:180;return this.is_float(e)&&Math.abs(e)<n},convert_comma_to_dot:function(e){return e.replace(",",".")},field_has_value:function(t){var n="#"+t,r=e(n),i=!1;return r.length===1&&(i=e.trim(r.val())!==""),i},make_alert:function(t,n,r){var i="";switch(n){case"error":i="alert alert-error";break;case"success":i="alert alert-success";break;default:i="alert"}var s=e("<div />",{"class":i,html:t});if(!r){var o=e("<a />",{"class":"close","data-dismiss":"alert",href:"#",text:"x"});s.prepend(o)}return s},get_ajax_url:function(){return typeof window.ajaxurl=="undefined"?"http://localhost/wordpress/wp-admin/admin-ajax.php":window.ajaxurl},isUrl:function(e){var t=/(http|https|webcal):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;return t.test(e)},isValidEmail:function(e){var t=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;return t.test(e)},activate_saved_tab_on_page_load:function(t){null===t||undefined===t?e("ul.nav-tabs a:first").tab("show"):e("ul.nav-tabs a[href="+t+"]").tab("show")}}}();return t});