timely.define(["jquery_timely","external_libs/jquery.autocomplete_geomod"],function(e){e.fn.extend({geo_autocomplete:function(t,n){return options=e.extend({},e.Autocompleter.defaults,{geocoder:t,mapwidth:100,mapheight:100,maptype:"terrain",mapkey:"ABQIAAAAbnvDoAoYOSW2iqoXiGTpYBT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQNumU68AwGqjbSNF9YO8NokKst8w",mapsensor:!1,parse:function(t,n,r){var i=[];return t&&n&&n=="OK"&&e.each(t,function(t,n){if(n.geometry&&n.geometry.viewport){var s=n.formatted_address.split(","),o=s[0];e.each(s,function(t,n){if(n.toLowerCase().indexOf(r.toLowerCase())!=-1)return o=e.trim(n),!1}),i.push({data:n,value:o,result:o})}}),i},formatItem:function(e,t,n,r){var i="https://maps.google.com/maps/api/staticmap?visible="+e.geometry.viewport.getSouthWest().toUrlValue()+"|"+e.geometry.viewport.getNorthEast().toUrlValue()+"&size="+options.mapwidth+"x"+options.mapheight+"&maptype="+options.maptype+"&key="+options.mapkey+"&sensor="+(options.mapsensor?"true":"false"),s=e.formatted_address.replace(/,/gi,",<br/>");return'<img src="'+i+'" width="'+options.mapwidth+'" height="'+options.mapheight+'" /> '+s+'<br clear="both"/>'}},n),options.highlight=options.highlight||function(e){return e},options.formatMatch=options.formatMatch||options.formatItem,options.resultsClass="ai1ec-geo-ac-results-not-ready",this.each(function(){e(this).one("focus",function(){var t=setInterval(function(){var n=e(".ai1ec-geo-ac-results-not-ready");n.length&&(n.removeClass("ai1ec-geo-ac-results-not-ready").addClass("ai1ec-geo-ac-results").wrap('<div class="timely"/>').children("ul").addClass("dropdown-menu"),clearInterval(t))},500)}),new e.Autocompleter(this,options)})}})});