timely.define(["jquery_timely","libs/modal_helper"],function(e){var t=function(t){!e(this).is(":checked")&&e("#ai1ec-facebook-export-modal").length?e("#ai1ec-facebook-export-modal").modal({show:!0,backdrop:"static"}):e("#ai1ec-remove-event-hidden").remove()},n=function(){e("#ai1ec-facebook-export-modal").modal("hide");if(e(this).hasClass("remove")){var t=e("<input />",{type:"hidden",name:"ai1ec-remove-event",value:1,id:"ai1ec-remove-event-hidden"});e("#ai1ec-facebook-publish").append(t)}},r=function(t){t.preventDefault();var n={action:"ai1ec_refresh_tokens"};e.post(ajaxurl,n,function(n){var r=e(t.target).closest("#ai1ec-facebook-publish"),i=r.find(".ai1ec_export_radios"),s=i.find(".ai1ec_multi_choiches"),o=!0;s.length>0&&(o=s.hasClass("hide")),i.length>0?i.replaceWith(n):r.find(".ai1ec_refresh_tokens").before(n),!1===o&&e("#ai1ec-facebook-publish").find(".ai1ec_multi_choiches").removeClass("hide")},"json")},i=function(t){var n=e(".ai1ec_multi_choiches"),r=e(this);0!==n.length&&(this.checked?n.removeClass("hide"):n.addClass("hide"))};return{open_modal_when_user_chooses_to_unpublish_event:t,add_hidden_field_when_user_click_remove_in_modal:n,show_multi_choices_when_present:i,refresh_page_tokens:r}});