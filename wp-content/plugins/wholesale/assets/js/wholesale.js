jQuery(function() {

  if( wholesale_settings.grand_total ) {

    var tables = jQuery(document).find('table.wholesale');
    for( var i = 0; i<tables.length; i++ ) {
      tables[i] = jQuery( tables[i] );
      var columns = parseInt( tables[i].find('.product-title').attr('colspan') ) - 1;
      tables[i].children('tbody:last-of-type').
        append('<tr class="grand_total-row"><td colspan="' + columns + '">Total:</td><td class="grand_total">0</td></tr>');
    }
    jQuery(document).find('table.wholesale').append('<tr></tr>')
  }

  function update_row(row) {
    update_tally(row);
    update_total_price(row);
    if( wholesale_settings.grand_total ) {
      update_grand_total(row);
    }
  }

  function update_grand_total(row) {
    var table = row.parents('table');
    var grand_total = table.find('tbody:last-of-type > tr > .grand_total');
    var totals = table.find('.total');
    var total = 0;

    for (var i = 0; i < totals.length; i++) {
      var val = parseFloat(totals[i].innerHTML);
      if (!isNaN(val)) {
        total += val;
      }
    }

    grand_total.html(total.toFixed(2));
  }

  function update_tally(row) {
    var inputs = row.find('input');
    var tally = 0;

    for (var i = 0; i < inputs.length; i++) {
      var val = parseInt(inputs[i].value);
      if (!isNaN(val)) {
        tally += val;
      }
    }

    row.attr('tally', tally);
    row.children('.tally').html(tally);
  }

  function update_total_price(row) {
    var total = parseFloat(row.attr('price')) * parseInt(row.attr('tally'));
    if (total % 1 !== 0) {
      total = total.toFixed(2);
    }
    row.attr('total', total);
    row.children('.total').html(total);
  }

  function create_items_array(button) {
    var items = [];
    var rows;

    if (wholesale_settings.button_for_every_table) {
      rows = button.parent().prev().find('tr');
    } else {
      rows = jQuery(document).find('table.wholesale tr');
    }

    jQuery.each(rows, function() {

      var self = jQuery(this);

      if (self.attr('tally')) {

        var inputs = self.find('input');

        for (var i = 0; i < inputs.length; i++) {
          var input = jQuery(inputs[i]);
          if (input.val() && !isNaN(input.val()) && input.val() != 0) {

            var item = {
              product_id: self.attr('product_id'),
              quantity: input.val()
            }

            if (input.attr('variation_id')) {

              var attributes = {}
              var value;

              var attributes_names = input.parents('tbody').attr('attributes').split(/\s+/);
              for (var k = 0; k < attributes_names.length; k++) {
                value = input.attr('attr_' + attributes_names[k]);
                if (value !== undefined) {
                  attributes['attribute_pa_razmer'] = value;
                }
              }

              item['variation_id'] = parseInt(input.attr('variation_id'));
              item['variation'] = attributes;
            }

            items.push(item);
          }
        }
      }
    });

    return items;
  }

  function clear_after_adding_to_cart(button) {
    var rows;

    if (wholesale_settings.button_for_every_table) {
      rows = button.parent().prev().find('tr');
    } else {
      rows = jQuery('table.wholesale tr');
    }

    rows.removeAttr('tally').removeAttr('total')
      .find('input').val('');

    rows.find('td.tally').html('0');
    rows.find('td.total').html('0');
  }

  function something_went_wrong(button) {
    button.removeClass('loading');
  }

  function success(button) {
    button.removeClass('loading');
    clear_after_adding_to_cart(button);
    button.parent().children('span').show();
  }

  function add_items_to_cart(button) {

    button.addClass('loading');

    if (jQuery(document).find('.wholesale .incorrect').length > 0) {
      window.alert('Sorry, unable to add your choices to the cart.\nPlease review the minimum requirements for the products you have chosen and try again.');
      button.removeClass('loading');
      return false;
    }

    var data = {
      action: 'wholesale_add_to_cart',
      items: create_items_array(button)
    }

    jQuery.post( wholesale_settings.ajax_url, data, function(response) {

      if (!response) {
        something_went_wrong(button);
        return false;
      }

      var this_page = window.location.toString();

      this_page = this_page.replace('add-to-cart', 'added-to-cart');

      if (response.error && response.product_url) {
        something_went_wrong(button);
        window.location = response.product_url;
        return false;
      }

      var fragments = response.fragments;
      var cart_hash = response.cart_hash;

      // Block fragments class
      if (fragments) {
        jQuery.each(fragments, function(key, value) {
          jQuery(key).addClass('updating');
        });
      }

      // Replace fragments
      if (fragments) {
        jQuery.each(fragments, function(key, value) {
          jQuery(key).replaceWith(jQuery(jQuery.trim(value)));
        });
      }

      // Trigger event so themes can refresh other areas
      jQuery('body').trigger('added_to_cart', [fragments, cart_hash]);

      // Adding to cart was successful
      success(button);
    });
  }

  update_row(jQuery(document).find('input.product-quantity:eq(0)').closest('tr'));

  jQuery(document).on('change', 'table.wholesale input',function() {
    var self = jQuery(this);
    if (self.is(':disabled')) {
      return;
    }

    var val = self.val();
    var max = Number(self.attr('max'));
    var min = Number(self.attr('ws_min'));
    if (isNaN(min)) {
      min = 0;
    }

    if(val > max){
        val = max;
        self.val(max);
    }
    if(val < min){
        val = min;
        self.val(min);
    }


    var step = parseInt(self.attr('step'));
    if (max % step != 0) {
      max = step * Math.floor(max / step);
    }
    if (isNaN(step)) {
      step = 1;
    }
    if (val % step != 0) {
      val = step * Math.floor(val / step) + step;
    }
    if (val > max) {
      val = max;
    } else
    if (val < 0) {
      val = 0;
    }

    if (self.parent().children('span').length === 0) {
      self.parent().prepend('<span class="warning">Min. ' + min + '</span>');
      self.parent().children('span').hide();
    }

    if (val < min && val != 0) {
      self.addClass('incorrect');
      self.parent().children('span').show();
    } else {
      self.removeClass('incorrect');
      self.parent().children('span').hide();
    }

    //We do it just to make sure there will be only integer values (we can't buy 1.5 of product)

    // Update parent row
    update_row(self.closest('tr'));
  });

  var carts = jQuery(document).find('.wholesale-cart');

  if (!wholesale_settings.button_for_every_table) {
    for (var i = 0; i < carts.length - 1; i++) {
      jQuery(carts[i]).remove();
    }
  }

  carts.show();

  jQuery(document).on('click','.wholesale-cart > .add_to_cart', function(e) {
    jQuery(this).parent().children('span').hide();
    add_items_to_cart(jQuery(this));
  })

})
