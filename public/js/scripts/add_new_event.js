timely.define(["jquery_timely","domReady","libs/utils","ai1ec_config","scripts/add_new_event/event_location/gmaps_helper","scripts/add_new_event/event_location/input_coordinates_event_handlers","scripts/add_new_event/event_location/input_coordinates_utility_functions","scripts/add_new_event/event_date_time/date_time_event_handlers","scripts/add_new_event/event_cost_helper","external_libs/jquery.calendrical_timespan","external_libs/jquery.inputdate","external_libs/jquery.tools","external_libs/bootstrap_datepicker","external_libs/bootstrap/transition","external_libs/bootstrap/collapse","external_libs/bootstrap/modal","external_libs/bootstrap/alert","external_libs/bootstrap/tab","external_libs/select2"],function(e,t,n,r,i,s,o,u,a,f){var l=function(){var t=new Date(r.now*1e3),n={allday:"#ai1ec_all_day_event",start_date_input:"#ai1ec_start-date-input",start_time_input:"#ai1ec_start-time-input",start_time:"#ai1ec_start-time",end_date_input:"#ai1ec_end-date-input",end_time_input:"#ai1ec_end-time-input",end_time:"#ai1ec_end-time",date_format:r.date_format,month_names:r.month_names,day_names:r.day_names,week_start_day:r.week_start_day,twentyfour_hour:r.twentyfour_hour,now:t};e.timespan(n)},c=function(){e(".ai1ec-panel-collapse").on("hide",function(){e(this).parent().removeClass("ai1ec-overflow-visible")}),e(".ai1ec-panel-collapse").on("shown",function(){var t=e(this);window.setTimeout(function(){t.parent().addClass("ai1ec-overflow-visible")},350)})},h=function(){l(),timely.require(["libs/gmaps"],function(e){e(i.init_gmaps)})},p=function(t,n){var r=null;"[object Array]"===Object.prototype.toString.call(n)?r=n.join("<br>"):r=n,e("#ai1ec_event_inline_alert").html(r),e("#ai1ec_event_inline_alert").removeClass("ai1ec-hidden"),t.preventDefault(),e("#publish, #ai1ec_bottom_publish").removeClass("button-primary-disabled"),e("#publish, #ai1ec_bottom_publish").removeClass("disabled"),e("#publish, #ai1ec_bottom_publish").siblings("#ajax-loading, .spinner").css("visibility","hidden")},d=function(t){o.ai1ec_check_lat_long_fields_filled_when_publishing_event(t)===!0&&(o.ai1ec_convert_commas_to_dots_for_coordinates(),o.ai1ec_check_lat_long_ok_for_search(t));var i=!1,s=[];e("#ai1ec_ticket_ext_url, #ai1ec_contact_url").each(function(){var t=this.value;e(this).removeClass("ai1ec-input-warn");var o=e(this).closest(".ai1ec-panel-collapse").parent().find(".ai1ec-panel-heading .ai1ec-fa-warning");i||o.addClass("ai1ec-hidden").parent().removeClass("ai1ec-tab-title-error");var u=e(this).attr("id"),a="ai1ec_ticket_ext_url"===u;if(""!==t&&!1===n.isValidUrl(t,a)){o.removeClass("ai1ec-hidden").parent().addClass("ai1ec-tab-title-error"),i||e(this).closest(".ai1ec-panel-collapse").collapse("show"),i=!0;var f=u+"_not_valid";s.push(r[f]),e(this).addClass("ai1ec-input-warn")}});var u=e("#ai1ec_contact_email"),a=u.closest(".ai1ec-panel-collapse").parent().find(".ai1ec-panel-heading .ai1ec-fa-warning");u.removeClass("ai1ec-input-warn"),i||a.addClass("ai1ec-hidden").parent().removeClass("ai1ec-tab-title-error");var f=e.trim(u.val());if(""!==f&&!1===n.isValidEmail(f)){a.removeClass("ai1ec-hidden").parent().addClass("ai1ec-tab-title-error"),i||u.closest(".ai1ec-panel-collapse").collapse("show"),i=!0;var l=u.attr("id")+"_not_valid";s.push(r[l]),u.addClass("ai1ec-input-warn")}var c=e("#title, #ai1ec_contact_name, #ai1ec_contact_email, #ai1ec_contact_phone, #content");if(e("#ai1ec_has_tickets").prop("checked")){c.addClass("ai1ec-required"),E(),e("#content").hasClass("ai1ec-error")?T(!0):T(!1),e(".ai1ec-error").not(".ai1ec-hidden .ai1ec-error").length&&(i=!0,e("#ai1ec-add-new-event-accordion > .ai1ec-panel-default > .ai1ec-panel-collapse").removeClass("ai1ec-collapse").css("height","auto"),s.push(r.ticketing_required_fields)),e("#ai1ec_repeat").prop("checked")===!0&&(i=!0,s.push(r.ticketing_repeat_not_supported));if(!1===i){var h=0,d=0;e(".ai1ec-tickets-edit-form").not(".ai1ec-tickets-form-template").each(function(){var t=e(this),n=!1;t.find(".ai1ec-tickets-fields").remove(),t.find("select, input").each(function(){if(!this.name)return;"remove"===this.name&&(n=!0);var r=this.value;"checkbox"==this.type&&(1==this.checked?r="on":r="off"),e("<input />",{type:"hidden",name:"ai1ec_tickets["+h+"]["+this.name+"]","class":"ai1ec-tickets-fields",value:r}).appendTo(t)}),n||d++,h++}),0===d&&(i=!0,s.push(r.ticketing_no_tickets_included))}}else c.removeClass("ai1ec-required");i?p(t,s):(e(".ai1ec-tickets-form-template").remove(),e(".ai1ec-tickets-edit-form").find("input, select").not(".ai1ec-tickets-fields").prop("disabled",!0))},v=function(){e("#ai1ec_google_map").click(s.toggle_visibility_of_google_map_on_click),e("#ai1ec_input_coordinates").change(s.toggle_visibility_of_coordinate_fields_on_click),e("#post").submit(d),e("input.ai1ec-coordinates").blur(s.update_map_from_coordinates_on_blur),e("#ai1ec_bottom_publish").on("click",u.trigger_publish),e(document).on("change","#ai1ec_end",u.show_end_fields).on("click","#ai1ec_repeat_apply",u.handle_click_on_apply_button).on("click","#ai1ec_repeat_cancel",u.handle_click_on_cancel_modal).on("click","#ai1ec_monthly_type_bymonthday, #ai1ec_monthly_type_byday",u.handle_checkbox_monthly_tab_modal).on("click",".ai1ec-btn-group-grid a",u.handle_click_on_toggle_buttons),e("#ai1ec_repeat_box").on("hidden.bs.modal",u.handle_modal_hide),u.execute_pseudo_handlers(),e("#widgetField > a").on("click",u.handle_animation_of_calendar_widget),e(document).on("click",".ai1ec-set-banner-image",m),e(document).on("click",".ai1ec-remove-banner",g),e(document).on("click","#ai1ec_tax_options, #ai1ec_update_tax_options",k)},m=function(){var t={};return t._frame=wp.media({state:"featured-image",states:[new wp.media.controller.FeaturedImage,new wp.media.controller.EditImage]}),t._frame.open(),e(".media-frame:last ").addClass("ai1ec-banner-image-frame"),e(".media-frame-title:last h1").text(e(".ai1ec-set-banner-block .ai1ec-set-banner-image").text()),e(".media-frame-toolbar:last").append(e(".ai1ec-media-toolbar").clone().removeClass("ai1ec-media-toolbar ai1ec-hidden")),e(".ai1ec-save-banner-image").off().on("click",function(){var n=e(".attachments:visible li.selected img").attr("src"),r=e(".attachment-details:visible input[type=text]").val();return n&&r&&e("#ai1ec_event_banner .inside").find(".ai1ec-banner-image-block").removeClass("ai1ec-hidden").find("img").attr("src",n).end().find("input").val(r).end().end().find(".ai1ec-set-banner-block").addClass("ai1ec-hidden").end().find(".ai1ec-remove-banner-block").removeClass("ai1ec-hidden"),t._frame.close(),!1}),!1},g=function(){return e("#ai1ec_event_banner .inside").find(".ai1ec-remove-banner-block").addClass("ai1ec-hidden").end().find(".ai1ec-banner-image-block").addClass("ai1ec-hidden").find("input").val("").end().find("img").attr("src","").end().end().find(".ai1ec-set-banner-block").removeClass("ai1ec-hidden"),!1},y=function(){e("#ai1ec_event").insertAfter("#ai1ec_event_inline_alert"),e("#post").addClass("ai1ec-visible")},b=function(){e("#timezone-select").select2()},w=function(){e(".ai1ec-tickets-datepicker").not(".ai1ec-tickets-datepicker-inited").not(".ai1ec-tickets-form-template .ai1ec-tickets-datepicker").each(function(){var t=e(this),n=t.closest(".ai1ec-tickets-dates-block"),r=e(".ai1ec-tickets-time",n),i=e("input.ai1ec-tickets-full-date",n),s=i.val();t.val(s.substr(0,10)),r.val(s.substr(11,5)),r.on("change",function(){s=i.val(),i.val(s.substr(0,10)+" "+this.value+":00")}),t.addClass("ai1ec-tickets-datepicker-inited").datepicker({autoclose:!0}).on("changeDate",function(e){i.val(this.value+" "+r.val()+":00")})})},E=function(){return e(".ai1ec-tickets-edit-form").not(".ai1ec-tickets-form-template").not(".ai1ec-hidden").find('input[id="ai1ec_ticket_unlimited"]').each(function(){var t=e(this),n=t.closest(".ai1ec-tickets-edit-form"),r=e('input[id="ai1ec_ticket_quantity"]',n);!1===t.prop("checked")?(r.val()==0&&r.val(""),r.addClass("ai1ec-required")):r.removeClass("ai1ec-required")}),e(".ai1ec-tickets-edit-form").not(".ai1ec-tickets-form-template").not(".ai1ec-hidden").find('input[id="ai1ec_ticket_avail"]').each(function(){var t=e(this);t.closest(".ai1ec-tickets-edit-form").find('input[id="ai1ec_ticket_sale_start_date"],input[id="ai1ec_ticket_sale_end_date"]').each(function(){!1===t.prop("checked")?e(this).addClass("ai1ec-required"):e(this).removeClass("ai1ec-required")})}),e(".ai1ec-ticket-field-error").hide(),e(".ai1ec-required").not(".ai1ec-tickets-form-template .ai1ec-required").each(function(){var t=e(this);t.removeClass("ai1ec-error");if(!e.trim(t.val())||"checkbox"===t.attr("type")&&!t.prop("checked"))t.addClass("ai1ec-error"),t.parent().find(".ai1ec-ticket-field-error").show()}),e('[name="ticket_sale_start_date"], [name="ticket_sale_end_date"]').not(".ai1ec-tickets-form-template input").each(function(){var t=e(this),n=t.closest(".ai1ec-tickets-dates-block").find('input[type="text"]');n.removeClass("ai1ec-error"),!t.closest(".ai1ec-avail-block").find('input[name="availibility"]:checked').length&&null===this.value.match(/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/)&&n.addClass("ai1ec-error")}),e("#ai1ec_tax_inputs input").length?e(".ai1ec-tax-options-button").hide():e(".ai1ec-tax-options-button").show(),e(".ai1ec-ticket-field-error:visible").length?!1:!0},S=function(t){var n=t.closest(".ai1ec-tickets-edit-form"),r=e("#ai1ec_ticket_quantity",n);1==t.prop("checked")?r.hide():r.show()},x=function(t){var n=t.closest(".ai1ec-tickets-edit-form"),r=e(".ai1ec-tickets-dates",n);1==t.prop("checked")?r.hide():r.show()},T=function(t){e("#ai1ec-event-description-field-error").remove(),t&&e("#postdivrich").before('<div id="ai1ec-event-description-field-error"><strong style="color: red;">* The Event description is required.</strong></div>')},N=function(){e(document).on("click change",'[id="ai1ec_ticket_unlimited"]',function(){S(e(this))}),e(document).on("click change",'[id="ai1ec_ticket_avail"]',function(){x(e(this))}),e(document).on("click change",'[id="ai1ec_new_ticket_status"]',function(){var t=e(this),n=t.closest(".ai1ec-tickets-panel"),i=t.find(":selected");if("canceled"===i.val()){var s=e("#ai1ec-ticket-taken",n);if(0<s.val()){e("#ai1ec-ticket-status-message",n).text(r.ticketing.cancel_message);return}}n.find("#ai1ec-ticket-status-message").text("")}),e(document).on("click",".ai1ec-remove-ticket",function(){var t=e(this).closest(".ai1ec-tickets-panel"),i=e("#ai1ec-ticket-taken",t);return 0<i.val()?n.alert(r.ticketing.information,r.ticketing.no_delete_text):t.addClass("ai1ec-hidden").append('<input type="hidden" name="remove" value="1">'),!1});var t=function(){var t=e(".ai1ec-tickets-form-template").clone();return t.removeClass("ai1ec-tickets-form-template").appendTo("#ai1ec-ticket-forms"),$checkbox=e("#ai1ec_ticket_unlimited",t),$checkbox.prop("checked",!0),S($checkbox),$checkbox=e("#ai1ec_ticket_avail",t),$checkbox.prop("checked",!0),x($checkbox),w(),!1};e("#ai1ec_add_new_ticket").on("click",t),e(".ai1ec-tickets-edit-form").not(".ai1ec-tickets-form-template").length||t()},C=function(){var t=function(){e(".ai1ec_review_modal").modal("hide"),e(".ai1ec_review_modal").hide()},n=function(){var n=e(".ai1ec_review_negative_feedback, .ai1ec_review_contact_name, .ai1ec_review_contact_email, .ai1ec_review_site_url");n.each(function(){var t=e(this);t.removeClass("ai1ec-error"),t.closest("td").find(".ai1ec-required-message").hide(),t.closest("td").find(".ai1ec-invalid-email-message").hide(),t.closest("td").find(".ai1ec-invalid-site-message").hide();if(!e.trim(t.val()))t.addClass("ai1ec-error"),t.closest("td").find(".ai1ec-required-message").show();else if(t.hasClass("ai1ec_review_contact_email")){var n=/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;!1===n.test(t.val())&&(t.addClass("ai1ec-error"),t.closest("td").find(".ai1ec-invalid-email-message").show())}else if(t.hasClass("ai1ec_review_site_url")){var r=/[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi;!1===r.test(t.val())&&(t.addClass("ai1ec-error"),t.closest("td").find(".ai1ec-invalid-site-message").show())}}),!1===n.hasClass("ai1ec-error")&&(e(".ai1ec_review_send_feedback").button("loading"),n.prop("disabled",!0),e.ajax({url:r.ajax_url,type:"POST",data:{action:"ai1ec_send_feedback_message",message:e(".ai1ec_review_negative_feedback").val(),name:e(".ai1ec_review_contact_name").val(),email:e(".ai1ec_review_contact_email").val(),site:e(".ai1ec_review_site_url").val()},success:function(n){e(".ai1ec_review_messages").remove(),e(".ai1ec-review-form").prepend('<div class="timely ai1ec-alert ai1ec-alert-success ai1ec_review_messages"><strong>'+r.review.message_sent+"</strong></div>"),setTimeout(function(){e(".ai1ec_review_send_feedback").button("reset"),e(".ai1ec_not_enjoying_popup").prop("disabled",!0),t()},3e3)},error:function(t){n.prop("disabled",!1),e(".ai1ec_review_messages").remove(),e(".ai1ec-review-form").prepend('<div class="timely ai1ec-alert ai1ec-alert-danger ai1ec_review_messages"><strong>Error!</strong> '+r.review.message_error+"</div>")}}))},i=function(){o("y")},s=function(){o("n")},o=function(n){t(),e.ajax({url:r.ajax_url,type:"POST",data:{action:"ai1ec_save_feedback_review",feedback:n}})};e(".ai1ec_review_enjoying_no_rating, .ai1ec_review_enjoying_go_wordpress").on("click",i),e(".ai1ec_review_send_feedback").on("click",n),e(".ai1ec_review_not_enjoying_no_rating").on("click",s)},k=function(){var t=e("#ai1ec_tax_box"),n=e(".ai1ec-modal-content",t),r=e(".ai1ec-loading",t);t.modal({backdrop:"static"}),e.post(ajaxurl,{action:"ai1ec_get_tax_box",ai1ec_event_id:e("#post_ID").val()},function(t){var n=ai1ec_tax_frame.contentWindow.document;r.remove(),e(ai1ec_tax_frame).removeClass("ai1ec-hidden"),n.open(),n.write(t.message.body),n.close();var i=0,s=0,o=e("#ai1ec_tax_frame");setInterval(function(){s=o.contents().find("body").height(),s!=i&&o.css("height",(i=s)+"px")},500)},"json")};window.addEventListener("message",function(t){var n=t.data,r="timely_tax_options_",i="timely_tax_cancel",s=e("#ai1ec_tax_inputs");if(n===i){e("#ai1ec_tax_box").modal("hide"),ai1ec_tax_frame.setAttribute("src","");return}if(0!==n.indexOf(r))return;ai1ec_tax_frame.setAttribute("src",""),n=JSON.parse(n.substr(r.length)),e("#ai1ec_tax_box").modal("hide"),e("#ai1ec_tax_options").addClass("ai1ec-hidden"),e("#ai1ec_update_tax_options").removeClass("ai1ec-hidden"),s.html("");for(var o in n)s.append(e("<input />",{type:"hidden",name:"tax_options["+o+"]",value:n[o]}))},!1);var L=function(){h(),t(function(){c(),y(),v(),b(),N(),w(),C()})};return{start:L}});