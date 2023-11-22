$(document).ready(function(){
  $("body .support-cart").hover(
    function () {
      $("body #menu-overlay").addClass('reveal');
    },
    function () {
      $("body #menu-overlay").removeClass("reveal");
    }
  );
  $("#menu-overlay").hover(
    function() {
      $(this).removeClass('reveal');
    }
  );
});

function htmlItemCart(item, url) {
  var html =
    '<li class="item productid-' + item.item.id + '">' +
      '<a class="product-image" href="' + url + '/product/' + item.item.product.id + '" title="' + item.item.product.name + ' - ' + item.item.color + '">' +
        '<img alt="' + item.item.product.name + ' - ' + item.item.color + '" src="' + url + '/storage/images/products/' + item.item.product.image + '"width="' + '80' + '"\>' +
      '</a>' +
      '<div class="detail-item">' +
        '<div class="product-details">' +
          '<a href="javascript:;" data-id="' + item.item.id + '" title="Xóa" class="remove-item-cart fa fa-remove" data-url="' + url + '/cart/remove' + '" onclick="removeItem($(this));">' +
            '<i class="fas fa-times"></i>' +
          '</a>' +
          '<p class="product-name">' +
            '<a href="' + url + '/product/' + item.item.product.id + '" title="' + item.item.product.name + ' - ' + item.item.color + '">' + item.item.product.name + ' - ' + item.item.color + '</a>' +
          '</p>' +
        '</div>' +
      '<div class="product-details-bottom">' +
        '<span class="price pricechange">' + formatMoney(item.price, "{{amount_no_decimals_with_comma_separator}}₫") + '</span>' +
        '<div class="quantity-select">' +
          '<input class="variantID" type="hidden" name="variantId" value="' + item.item.id + '">' +
          '<button onClick="minus(' + item.item.id + ');" class="reduced items-count btn-minus" type="button">–</button>' +
          '<input type="text" disabled maxlength="3" min="1" max="' + item.item.quantity + '" onchange="if(this.value == 0)this.value=1;" class="input-text number-sidebar qty' + item.item.id + '" id="qty' + item.item.id + '" name="Lines" id="updates_' + item.item.id + '" size="4" value="' + item.qty + '" data-url="' + url + '/minicart/update' +'">' +
          '<button onClick="plus(' + item.item.id + ');" class="increase items-count btn-plus" type="button">+</button>' +
        '</div>' +
      '</div>' +
    '</li>';
  return html;
}
function htmlCart(cart, url) {
  var html = '';
  if(cart.totalQty == 0) {
    html =
      '<ul id="cart-sidebar" class="mini-products-list count_li">' +
        '<div class="no-item">' +
          '<p>Không có sản phẩm nào trong giỏ hàng.</p>' +
        '</div>' +
      '</ul>';
  } else {
    var htmlItem = '';
    $.each(cart.items, function(key, item) {
      htmlItem = htmlItem + htmlItemCart(item, url);
    });
    html =
      '<ul id="cart-sidebar" class="mini-products-list count_li">' +
        '<ul class="list-item-cart">' +
          htmlItem +
        '</ul>' +
        '<div><div class="top-subtotal">Tổng cộng: <span class="price">' +
          formatMoney(cart.totalPrice, "{{amount_no_decimals_with_comma_separator}}₫") +
        '</span></div></div>' +
        '<div><div class="actions clearfix">' +
          '<a href="javascript:;" onclick="showCheckout($(this));" class="btn btn-gray btn-checkout" title="Thanh toán" data-url="' + url + '/checkout">' +
            '<span>Thanh toán</span>' +
          '</a>' +
          '<a href="/cart" class="view-cart btn btn-white margin-left-5" title="Giỏ hàng">' +
            '<span>Giỏ hàng</span>' +
          '</a>' +
        '</div></div>'+
      '</ul>';
  }
  return html;
}
function minus(id) {
  var result = $('#qty' + id);
  var qty = parseInt(result.val());
  var min = parseInt(result.attr('min'));
  if(!isNaN(qty) & qty > min ) {
    var data = {id: id, qty: qty - 1}
    var url = result.attr('data-url');
    $.ajax({
      url: url,
      type: 'POST',
      data: data,
      dataType: 'JSON',
      success: function(data) {
        $('#cart-sidebar .item.productid-' + data.response.id + ' .price').empty();
        $('#cart-sidebar .item.productid-' + data.response.id + ' .price').append(
          formatMoney(data.response.price, "{{amount_no_decimals_with_comma_separator}}₫")
        );

        $('#cart-sidebar .item.productid-' + data.response.id + ' #qty' + data.response.id).val(data.response.qty);
        $('#cart-sidebar .item.productid-' + data.response.id + ' #qty' + data.response.id).attr('max', data.response.maxQty);

        $('#cart-sidebar .top-subtotal .price').empty();
        $('#cart-sidebar .top-subtotal .price').append(
          formatMoney(data.response.totalPrice, "{{amount_no_decimals_with_comma_separator}}₫")
        );
        $('.mini-cart .count_item_pr').empty();
        $('.mini-cart .count_item_pr').append(data.response.totalQty);
      },
      error: function(data) {
        var errors = data.responseJSON;
        Swal.fire({
          title: 'Thất bại',
          text: errors.msg,
          type: 'error'
        })
      }
    });
  }
  return false;
}
function plus(id) {

  var result = $('#qty' + id);
  var qty = parseInt(result.val());
  var max = parseInt(result.attr('max'));
  if(!isNaN(qty) & qty < max ) {
    var data = {id: id, qty: qty + 1}
    var url = result.attr('data-url');
    $.ajax({
      url: url,
      type: 'POST',
      data: data,
      dataType: 'JSON',
      success: function(data) {
        $('#cart-sidebar .item.productid-' + data.response.id + ' .price').empty();
        $('#cart-sidebar .item.productid-' + data.response.id + ' .price').append(
          formatMoney(data.response.price, "{{amount_no_decimals_with_comma_separator}}₫")
        );

        $('#cart-sidebar .item.productid-' + data.response.id + ' #qty' + data.response.id).val(data.response.qty);

        $('#cart-sidebar .top-subtotal .price').empty();
        $('#cart-sidebar .top-subtotal .price').append(
          formatMoney(data.response.totalPrice, "{{amount_no_decimals_with_comma_separator}}₫")
        );
        $('.mini-cart .count_item_pr').empty();
        $('.mini-cart .count_item_pr').append(data.response.totalQty);
      },
      error: function(data) {
        var errors = data.responseJSON;
        Swal.fire({
          title: 'Thất bại',
          text: errors.msg,
          type: 'error'
        })
      }
    });
  }
  return false;
}
function removeItem(obj) {
  var product_id = obj.attr('data-id');
  var url = obj.attr('data-url');
  var data = {
    id: product_id
  };
  $.ajax({
    url: url,
    type: 'POST',
    data: data,
    dataType: 'JSON',
    success: function(data) {
      $('#cart-sidebar').remove();
      $('.mini-cart .top-cart-content').append(htmlCart(data.response, data.url));
      $('.support-cart .count_item_pr').empty();
      $('.support-cart .count_item_pr').append(data.response.totalQty);
    },
    error: function(data) {
      var errors = data.responseJSON;
      Swal.fire({
        title: 'Thất bại',
        text: errors.msg,
        type: 'error'
      })
    }
  });
}
function post(path, params, method='post') {

  // The rest of this code assumes you are not using a library.
  // It can be made less wordy if you use one.
  const form = document.createElement('form');
  form.method = method;
  form.action = path;

  for (const key in params) {
    if (params.hasOwnProperty(key)) {
      const hiddenField = document.createElement('input');
      hiddenField.type = 'hidden';
      hiddenField.name = key;
      hiddenField.value = params[key];

      form.appendChild(hiddenField);
    }
  }

  document.body.appendChild(form);
  form.submit();
}
function showCheckout(obj) {
  var url = obj.attr('data-url');
  var token = $('meta[name="csrf-token"]').attr('content');
  post(url, {type: 'buy_cart', _token: token});
}
