(function ($) {

  var is_blocked = function ($node) {
    return $node.is('.processing') || $node.parents('.processing').length;
  };

  var block = function ($node) {
    if (!is_blocked($node)) {
      $node.addClass('processing').block({
        message: null,
        overlayCSS: {
          background: '#fff',
          opacity: 0.6
        }
      });
    }
  };

  var unblock = function ($node) {
    $node.removeClass('processing').unblock();
  };

  $(document).on('click', '.single_add_to_cart_button', function (e) {

    if (qlwce.add_product_ajax === 'yes') {

      var $button = $(this),
              $form = $button.closest('form.cart'),
              product_id = $form.find('input[name=add-to-cart]').val() || $button.val();

      if (product_id) {

        e.preventDefault();

        var data = {
          action: 'qlwce_add_product_ajax',
          nonce: qlwce.nonce,
          'add-to-cart': product_id,
        };

        $form.serializeArray().forEach(function (element) {
          data[element.name] = element.value;
        });

        $(document.body).trigger('adding_to_cart', [$button, data]);

        $.ajax({
          type: 'post',
          url: wc_add_to_cart_params.ajax_url,
          data: data,
          beforeSend: function (response) {
            $button.removeClass('added').addClass('loading');
          },
          complete: function (response) {
            $button.addClass('added').removeClass('loading');
          },
          success: function (response) {

            if (response.error & response.product_url) {
              window.location = response.product_url;
              return;
            } else {
              $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
              $(document.body).trigger('added_to_cart_message', [response.fragments, response.cart_hash, $button]);

              $('.woocommerce-notices-wrapper').empty().append(response.notices);

            }
          },
        });

        return false;
      }
    }
  });

  $(document).bind('added_to_cart_message', function (e, cart) {

    if (qlwce.add_product_ajax_message === 'yes') {

      $.ajax({
        type: 'post',
        url: wc_add_to_cart_params.ajax_url,
        data: {
          action: 'qlwce_add_product_ajax_message',
          nonce: qlwce.nonce
        },
        beforeSend: function (response) {
          $('.woocommerce-notices-wrapper').addClass('loading');
        },
        complete: function (response) {
          $('.woocommerce-notices-wrapper').removeClass('loading');
        },
        success: function (response) {

          if (response) {

            $('.woocommerce-notices-wrapper').empty().append(response);

          }
        },
      });

    }

  });

  $('#order_review').on('change', 'input.qty', function (e) {
    e.preventDefault();

    var $qty = $(this);

    setTimeout(function () {

      var hash = $qty.attr('name').replace(/cart\[([\w]+)\]\[qty\]/g, "$1"),
              qty = parseFloat($qty.val());

      $.ajax({
        type: 'post',
        url: wc_add_to_cart_params.ajax_url,
        data: {
          action: 'qlwce_update_cart',
          nonce: qlwce.nonce,
          hash: hash,
          quantity: qty
        },
        beforeSend: function (response) {
          block($('#order_review'));
        },
        complete: function (response) {
          unblock($('#order_review'));
        },
        success: function (response) {
          if (response) {
            $('#order_review').html($(response).html());
          }
        },
      });

    }, 400);

  });

  $('#order_review').on('click', 'a.remove', function (e) {
    e.preventDefault();

    var hash = $(this).data('cart_item_key');

    $.ajax({
      type: 'post',
      url: wc_add_to_cart_params.ajax_url,
      data: {
        action: 'qlwce_update_cart',
        nonce: qlwce.nonce,
        quantity: 0,
        hash: hash
      },
      beforeSend: function (response) {
        block($('#order_review'));
      },
      complete: function (response) {
        unblock($('#order_review'));
      },
      success: function (response) {
        if (response) {
          $('#order_review').html($(response).html());
        }
      },
    });

  });
})(jQuery);