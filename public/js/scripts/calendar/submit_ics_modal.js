timely.define(["jquery_timely","ai1ec_config","libs/utils","libs/recaptcha","libs/select2_multiselect_helper","libs/collapse_helper","external_libs/Placeholders"],function(e,t,n,r,i){var s=e(".ai1ec-submit-ics-form"),o=e("#ai1ec-submit-ics-modal .ai1ec-loading"),u=function(){r.init_recaptcha(s),i.init(s)},a=function(){r.init_recaptcha(s)},f=function(r){r.preventDefault(),e(".ai1ec-alerts",s).html("");var i=e("#ai1ec_calendar_url",s).val(),u=e("#ai1ec_submitter_email",s).val();if(i===""||u===""){var a=n.make_alert(t.mail_url_required,"error",!0);e(".ai1ec-alerts",s).append(a)}else{if(!n.isUrl(i)){var a=n.make_alert(t.invalid_url_message,"error",!0);e(".ai1ec-alerts",s).append(a),e("#ai1ec_calendar_url",s).focus();return}if(!n.isValidEmail(u)){var a=n.make_alert(t.invalid_email_message,"error",!0);e(".ai1ec-alerts",s).append(a),e("#ai1ec_submitter_email",s).focus();return}var f=s.serialize();o.addClass("show"),e.ajax({data:f+"&action=ai1ec_add_ics_frontend",type:"POST",dataType:"json",url:t.ajax_url,success:function(t){o.removeClass("show"),e("#recaptcha_response_field",s).length&&typeof Recaptcha!="undefined"&&Recaptcha.reload();var r=t.success?"success":"error",i=n.make_alert(t.message,r,!0);e(".ai1ec-alerts",s).append(i),e(".ai1ec-nonce-fields",s).html(t.nonce),"success"===r&&(e("#ai1ec_calendar_url, #ai1ec_submitter_email",s).val(""),e("#ai1ec_categories",s).select2("val",""))}})}};return{handle_form_submission:f,init_form:u,init_recaptcha:a}});