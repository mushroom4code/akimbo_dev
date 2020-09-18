(function( $ ) {
	'use strict';

	$(function() {

		$('input.wootuner_checkbox').iCheck({
			checkboxClass: 'icheckbox_square-green',
			increaseArea: '20%'
	    });

		$('.toggle-templates-name').click(function(e){
			e.preventDefault();
			jQuery(".woo-template-path").toggleClass("active");
		});

		$(".select-all-checkboxes").toggle(
			function(e){
				e.preventDefault();
				var target_id = jQuery(this).parents(".woo_tuner_section_title").data("target");
				$('#'+target_id+' input.wootuner_checkbox').iCheck('check');
			},
			function(e){
				e.preventDefault();
				var target_id = jQuery(this).parents(".woo_tuner_section_title").data("target");
				$('#'+target_id+' input.wootuner_checkbox').iCheck('uncheck');
			}
		);

		$('.woo_tuner_section_title').click(function(e){
			e.preventDefault();
			var target_id       = jQuery(this).data("target");
			var target_section  = jQuery("div#"+target_id);
			var toggle_checkbox = jQuery(this).find(".select-all-checkboxes");
			if(!target_section.hasClass("hidden")){
				$(this).find(".dashicons").removeClass('dashicons-arrow-down-alt2');
				$(this).find(".dashicons").addClass('dashicons-arrow-up-alt2');
				target_section.addClass("hidden");
				toggle_checkbox.attr("disabled","disabled");
			} else {
				$(this).find(".dashicons").removeClass('dashicons-arrow-up-alt2');
				$(this).find(".dashicons").addClass('dashicons-arrow-down-alt2');
				target_section.removeClass("hidden");
				toggle_checkbox.removeAttr("disabled");
			}
		});

		jQuery( ".sortable-list" ).sortable({
			placeholder: "ui-sortable-placeholder",
			update: function(event, ui) {

				//test object
				var neworder = [];
				jQuery('.sortable-list li').each(function() {
					var id       = jQuery(this).attr("id");
					var priority = jQuery(this).data("priority");
					var option   = jQuery(this).data("option");
					var obj      = {};
					obj.id       = id;
					obj.priority = priority;
					obj.option   = option;
					neworder.push({"hook":obj.id, "priority": obj.priority, "option": obj.option});
				});
				console.log(neworder);
				// endof test object
				//
				jQuery(".woo-tuner-loader-wrapper").fadeTo(250,1);
				var newOrder = jQuery(this).sortable('toArray');
				if( newOrder ){
					update_woo_tuner_widgets_list(newOrder);
				}
			}
		});

	});

	$( window ).load(function() {
		console.log('woocommerce-tuner v.0.0.1 was loaded');
	});

})( jQuery );

function update_woo_tuner_widgets_list(newOrder){
	jQuery.ajax({
		type 	 : "post",
		dataType : "json",
		url 	 : ajaxurl,
		data 	 : {
			action	 : "update_single_product_widgets",
			newOrder : newOrder
		},
		success: function(response) {
			if(response.type == "success") {
				jQuery(".woo-tuner-loader-wrapper").fadeTo(250,0);
			}
			else {
				alert("error");
			}
		}
	});
}
