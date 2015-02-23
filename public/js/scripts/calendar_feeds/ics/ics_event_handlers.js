timely.define(["jquery_timely","scripts/calendar_feeds/ics/ics_ajax_handlers","libs/utils","ai1ec_config"],function(e,t,n,r){var i=n.get_ajax_url(),s=function(){var s=e(this),o=e("#ai1ec_feed_url"),u=o.val().replace("webcal://","http://"),a=!1,f;e(".ai1ec-feed-url, #ai1ec_feed_url").css("border-color","#DFDFDF"),e("#ai1ec-feed-error").remove(),e(".ai1ec-feed-url").each(function(){this.value===u&&(e(this).css("border-color","#FF0000"),a=!0,f=r.duplicate_feed_message)}),n.isUrl(u)||(a=!0,f=r.invalid_url_message);if(a)o.addClass("input-error").focus().before(n.make_alert(f,"error"));else{s.button("loading");var l=e("#ai1ec_comments_enabled").is(":checked")?1:0,c=e("#ai1ec_map_display_enabled").is(":checked")?1:0,h=e("#ai1ec_add_tag_categories").is(":checked")?1:0,p=e("#ai1ec_keep_old_events").is(":checked")?1:0,d=e("#ai1ec_feed_import_timezone").is(":checked")?1:0,v={action:"ai1ec_add_ics",nonce:r.calendar_feeds_nonce,feed_url:u,feed_category:e("#ai1ec_feed_category").val(),feed_tags:e("#ai1ec_feed_tags").val(),comments_enabled:l,map_display_enabled:c,keep_tags_categories:h,keep_old_events:p,feed_import_timezone:d};e(".ai1ec-feed-field").each(function(){var t=e(this).val();"checkbox"===e(this).attr("type")&&!e(this).prop("checked")&&(t=0),v[e(this).attr("name")]=t}),e.post(i,v,t.handle_add_new_ics,"json")}},o=function(n){n.preventDefault();var r=e(this).hasClass("remove")?!0:!1,s=e(e(this).data("el")),o=s.closest(".ai1ec-feed-container"),u=e(".ai1ec_feed_id",o).val(),a={action:"ai1ec_delete_ics",ics_id:u,remove_events:r};s.button("loading"),e("#ai1ec-ics-modal").modal("hide"),e.post(i,a,t.handle_delete_ics,"json")},u=function(){e("#ai1ec-ics-modal .ai1ec-btn").data("el",this),e("#ai1ec-ics-modal").modal({backdrop:"static"})},a=function(){var n=e(this),r=n.closest(".ai1ec-feed-container"),s=e(".ai1ec_feed_id",r).val(),o={action:"ai1ec_update_ics",ics_id:s};n.button("loading"),e.post(i,o,t.handle_update_ics,"json")},f=function(){var t=e(this).val(),n=/.google./i;n.test(t)&&e("#ai1ec_feed_import_timezone").prop("checked",!0)};return{add_new_feed:s,submit_delete_modal:o,open_delete_modal:u,update_feed:a,feed_url_change:f}});