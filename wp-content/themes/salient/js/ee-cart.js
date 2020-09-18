function item_form_basket(form_input) {
    var items = [];
    form_input.each(function (i, v) {
        var quantity = jQuery(v).val();
        var offer_id = jQuery(v).attr("name");
        var product_id = jQuery(v).attr("product_id");
        var variation_name = jQuery(v).attr("attr_razmer");

        if(Number(quantity) > 0) {
            var item = {
                product_id: product_id,
                quantity: quantity,
                variation: {
                    attribute_pa_razmer: variation_name
                },
                variation_id: offer_id
            };
            items.push(item);
        }
    });
    return items;
}

function eeUpdateContentCart(html, form_input){
    var res_data = item_form_basket(form_input);
    if(res_data.length == 0){
        location.reload();
        return false;
    }
    var wc_form_mob = jQuery(html).find('form.ee_mobile_form').html();
    var wc_form_desc = jQuery(html).find('form.ee_desc_form').html();
    var cart_collaterals = jQuery(html).find('div.cart-collaterals').html();

    jQuery('form.ee_mobile_form').html(wc_form_mob);
    jQuery('form.ee_desc_form').html(wc_form_desc);
    jQuery('div.cart-collaterals').html(cart_collaterals);
}
function eeStartLoader(){
    jQuery('form.ee_desc_form').addClass("processing").block({message: null, overlayCSS: {background: "#fff", opacity: .6}});
    jQuery('form.ee_mobile_form').addClass("processing").block({message: null, overlayCSS: {background: "#fff", opacity: .6}});
    jQuery('div.cart-collaterals').addClass("processing").block({message: null, overlayCSS: {background: "#fff", opacity: .6}});
}

jQuery(document).ready(function($){
	
	jQuery('form.ee_desc_form').on('click', 'a.remove', function () {
		var form_input = jQuery('form.ee_desc_form').find('input[type="number"]');
		var product_id = jQuery(this).data("product_id");
		var data = {
			'action': 'remove_product_basket',
			'product_id': product_id
		};
		jQuery.ajax({
			type: 'POST',
			url: location.href,
			data: data,
			beforeSend: function(){
				eeStartLoader();
			},
			success: function(html) {
				eeUpdateContentCart(html,form_input);
			}
		});
	}); 
	
	jQuery('form.ee_mobile_form').on('click', 'a.remove', function () {
		var form_input = jQuery('form.ee_mobile_form').find('input[type="number"]');
		var product_id = jQuery(this).data("product_id");
		var data = {
			'action': 'remove_product_basket',
			'product_id': product_id
		};
		jQuery.ajax({
			type: 'POST',
			url: location.href,
			data: data,
			beforeSend: function(){
				eeStartLoader();
			},
			success: function(html) {
				eeUpdateContentCart(html,form_input);
			}
		});
	});
	
	jQuery('form.woocommerce-cart-form').on('change', 'input[type="number"]', function () {
		var that = jQuery(this);
		var value = jQuery(that).val();
		var max = jQuery(that).attr('max');
		if(Number(value) > Number(max)){
			that.val(max);
		}else if(Number(value) < 0){
			that.val(0);
		}
	});
	
	jQuery('form.ee_mobile_form').on('click', 'button[name="update_cart"]', function (e) {
		e.preventDefault();
		var form_input = jQuery('form.ee_mobile_form').find('input[type="number"]');
		var data_delete = {
			action: 'remove_product_basket',
			product_id: "full",
			items: item_form_basket(form_input)
		};
		jQuery.ajax({
			type: 'POST',
			url: location.href,
			data: data_delete,
			beforeSend: function(){
				eeStartLoader();
			},
			success: function (html) {
				eeUpdateContentCart(html, form_input);
			}
		});
	});
	
	jQuery('form.ee_desc_form').on('click', 'button[name="update_cart"]', function (e) {
		e.preventDefault();
		var form_input = jQuery('form.ee_desc_form').find('input[type="number"]');
		var data_delete = {
			action: 'remove_product_basket',
			product_id: "full",
			items: item_form_basket(form_input)
		};
		jQuery.ajax({
			type: 'POST',
			url: location.href,
			data: data_delete,
			beforeSend: function(){
				eeStartLoader();
			},
			success: function (html) {
				eeUpdateContentCart(html, form_input);
			}
		});
	});
})