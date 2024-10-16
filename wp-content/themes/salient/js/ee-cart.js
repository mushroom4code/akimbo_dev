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
    item_form_basket(form_input);
	location.reload();
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

	jQuery(document).on('click', 'button[name="clear-cart"]', function (e) {
		e.preventDefault();
		let data = {
			'action': 'empty_all',
		};
		jQuery.ajax({
			type: 'POST',
			url:  location.href,
			data: data,
			success: function (datas) {
				if(datas !== ''){
					document.location.reload();
					$(document).ready(function($){
						$(document).find('.woocommerce-error').attr('style','display:none;');
					})
				}
			}
		});
	});

	// Enterego / Rodionova


	$('span.news').empty();
	$(".recall-phone").mask("+7 (999) 999-9999");
	$('.recall-button').on('click', function () {
		$.fancybox.open({
			src: '#recall-popup',
		});
		$('.news').removeClass('bad-news');
		$('.news').removeClass('good-news');
		$('.news').html('');
	});

	$('#send_mail').on('click',function(){
		let that = $(this);
		let lengthPhoneVal = $('.recall-phone').val().length;
		let issetError = false;

		$(that).prop('disabled', true);
		if(lengthPhoneVal == 0 ){
			$(".news").addClass('bad-news');
			$('.news').html('Укажите Ваш номер телефона!');
			issetError = true;
		}
		if(lengthPhoneVal !== 0 && lengthPhoneVal !== 17){
			$('.news').addClass('bad-news');
			$('.news').html('Ваш телефон указан не верно!');
			issetError = true;
		}
		let phone = $('input.phone').val();
		let name = $('input.name').val();
		console.log(phone)
		console.log(name)
		$.ajax({
			type: 'POST',
			url: '/wp-content/themes/salient/send_mail.php',
			data: {
				action: 'send_mail',
				phone:phone,
				name: name
			},
			beforeSend: function(){
				eeStartLoader();
			},
			success: function (result) {
				console.log(result);
				$('span.news').empty();
				if(result.status == 'true'){
					$('span.news').text('Ваше сообщение принято!  Вам скоро перезвонят.');
					setTimeout(function() {
						$.fancybox.close();}, 2000);
				}else{
					$('span.news').text('Ошибка отправки!  Повторите попытку!');
					setTimeout(function() {
						$.fancybox.close();}, 2000);
				}
				console.log(result);
			}
		});
	})

	let href_1 = window.location.href;
	let href =  'https://akimbo.docker.oblako-1c.ru/register/';
	if (href_1 == href) {
		$('p#billing_postcode_field').addClass('none');
	} else {
		$('p#billing_postcode_field').addClass('none');
	}

	$('#openBut').on('click',function(){
		$('#openButtons').toggleClass();
	})
})
