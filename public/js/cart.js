function htmlItemCart(item, url) {
  var html =
    '<div class="item product-' + item.item.id +'">' +
      '<div class="image-product">' +
        '<a href="' + url + '/product/' + item.item.product.id + '" target="_blank" title="' + item.item.product.name + ' - ' + item.item.color + '">' +
          '<img src="' + url + '/storage/images/products/' + item.item.product.image + '">' +
        '</a>' +
      '</div>' +
      '<div class="info-product">' +
        '<div class="name"><a href="' + url + '/product/' + item.item.product.id + '" target="_blank" title="' + item.item.product.name + ' - ' + item.item.color + '">' + item.item.product.name + ' - ' + item.item.color + '</a></div>' +
        '<div class="price"><strong>' + formatMoney(item.price, "{{amount_no_decimals_with_comma_separator}}₫") + '</strong></div>' +
        '<div class="quantity-block">' +
          '<div class="input-group-btn">' +
            '<button onclick="minus(' + item.item.id + ');" class="reduced_pop items-count btn-minus btn btn-default bootstrap-touchspin-down" type="button">–</button>' +
            '<input type="text" onchange="if(this.value == 0) this.value=1;" maxlength="12" min="' + '1' + '" max="' + item.item.quantity + '" disabled class="form-control quantity-r2 quantity js-quantity-product input-text number-sidebar input_pop input_pop qtyItem' + item.item.id + '" id="qtyItem' + item.item.id +'" name="Lines" size="4" value="' + item.qty + '" data-url="' + url + '/cart/update">' +
            '<button onclick="plus(' + item.item.id + ');" class="increase_pop items-count btn-plus btn btn-default bootstrap-touchspin-up" type="button">+</button>' +
          '</div>' +
        '</div>' +
        '<div class="remove-item">' +
          '<a href="javascript:;" onclick="removeItem($(this));" class="btn btn-link btn-item-delete remove-item-cart" data-id="' + item.item.id + '" title="Xóa" data-url="' + url + '/cart/remove"><i class="fas fa-times"></i></a>' +
        '</div>' +
      '</div>' +
    '</div>';
  return html;
}
function htmlCart(cart, url) {
  var html = '';
  if(cart.totalQty == 0) {
    html =
      '<div class="row">' +
        '<div class="col-md-4 col-md-offset-4">' +
          '<div class="cart-empty">' +
            '<img src="' + url + '/images/empty-cart.png' + '" alt="Giỏ Hàng Trống">' +
            '<div class="btn-cart-empty">' +
              '<a href="' + url + '/products" title="Tiếp tục mua sắm">Tiếp Tục Mua Sắm</a>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>';
  } else {
    var htmlItem = '';
    $.each(cart.items, function(key, item) {
      htmlItem = htmlItem + htmlItemCart(item, url);
    });
    html =
      '<div class="row">' +
        '<div class="col-md-8">' +
          '<div class="cart-items">' +
            htmlItem +
          '</div>' +
        '</div>' +
        '<div class="col-md-4">' +
          '<div class="total-price">' +
            '<div class="box-price-top">' +
              '<div class="title">Tạm Tính</div>' +
              '<div class="price">' + formatMoney(cart.totalPrice, "{{amount_no_decimals_with_comma_separator}}₫") + '</div>' +
            '</div>' +
            '<div class="box-price-up">' +
              '<div class="title">Thành Tiền</div>' +
              '<div class="price">' + formatMoney(cart.totalPrice, "{{amount_no_decimals_with_comma_separator}}₫") + '</div>' +
            '</div>' +
            '<div class="btn-action">' +
              '<button title="Thanh Toán Ngay">Thanh Toán Ngay</button>' +
              '<a href="' + url + '/products" class="btn-btn-default" title="Tiếp Tục Mua Hàng">Tiếp Tục Mua Hàng</a>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>';
  }
  return html;
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
      $('.site-cart .section-cart .section-content>.row').remove();
      $('.site-cart .section-cart .section-content').append(htmlCart(data.response, data.url));
      $('.site-cart .section-cart .section-header .section-title span').empty();
      $('.site-cart .section-cart .section-header .section-title span').append('( ' + data.response.totalQty + ' Sản Phẩm )');
      Swal.fire({
        title: 'Thành Công',
        text: data.msg,
        type: 'success'
      })
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
function minus(id) {
  var result = $('#qtyItem' + id);
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
        console.log(data);
        $('.site-cart .section-cart .cart-items .item.product-' + data.response.id + ' .price').empty();
        if(data.response.price == data.response.salePrice) {
          $('.site-cart .section-cart .cart-items .item.product-' + data.response.id + ' .price').append(
            '<strong>' + formatMoney(data.response.price, "{{amount_no_decimals_with_comma_separator}}₫") + '</strong>'
          );
        } else {
          $('.site-cart .section-cart .cart-items .item.product-' + data.response.id + ' .price').append(
            '<strong>' + formatMoney(data.response.price, "{{amount_no_decimals_with_comma_separator}}₫") + '</strong>' +
            '<del>' + formatMoney(data.response.salePrice, "{{amount_no_decimals_with_comma_separator}}₫") + '</del>'
          );
        }

        $('.site-cart .section-cart .cart-items .item.product-' + data.response.id + ' #qtyItem' + data.response.id).val(data.response.qty);
        $('.site-cart .section-cart .cart-items .item.product-' + data.response.id + ' #qtyItem' + data.response.id).attr('max', data.response.maxQty);

        $('.site-cart .section-cart .total-price .box-price-top .price').empty();
        $('.site-cart .section-cart .total-price .box-price-top .price').append(
          formatMoney(data.response.totalPrice, "{{amount_no_decimals_with_comma_separator}}₫")
        );
        $('.site-cart .section-cart .total-price .box-price-up .price').empty();
        $('.site-cart .section-cart .total-price .box-price-up .price').append(
          formatMoney(data.response.totalPrice, "{{amount_no_decimals_with_comma_separator}}₫")
        );
        $('.site-cart .section-cart .section-header .section-title>span').empty();
        $('.site-cart .section-cart .section-header .section-title>span').append('( ' + data.response.totalQty + ' Sản Phẩm )');
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

  var result = $('#qtyItem' + id);
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
        $('.site-cart .section-cart .cart-items .item.product-' + data.response.id + ' .price').empty();
        if(data.response.price == data.response.salePrice) {
          $('.site-cart .section-cart .cart-items .item.product-' + data.response.id + ' .price').append(
            '<strong>' + formatMoney(data.response.price, "{{amount_no_decimals_with_comma_separator}}₫") + '</strong>'
          );
        } else {
          $('.site-cart .section-cart .cart-items .item.product-' + data.response.id + ' .price').append(
            '<strong>' + formatMoney(data.response.price, "{{amount_no_decimals_with_comma_separator}}₫") + '</strong>' +
            '<del>' + formatMoney(data.response.salePrice, "{{amount_no_decimals_with_comma_separator}}₫") + '</del>'
          );
        }

        $('.site-cart .section-cart .cart-items .item.product-' + data.response.id + ' #qtyItem' + data.response.id).val(data.response.qty);
        $('.site-cart .section-cart .cart-items .item.product-' + data.response.id + ' #qtyItem' + data.response.id).attr('max', data.response.maxQty);

        $('.site-cart .section-cart .total-price .box-price-top .price').empty();
        $('.site-cart .section-cart .total-price .box-price-top .price').append(
          formatMoney(data.response.totalPrice, "{{amount_no_decimals_with_comma_separator}}₫")
        );
        $('.site-cart .section-cart .total-price .box-price-up .price').empty();
        $('.site-cart .section-cart .total-price .box-price-up .price').append(
          formatMoney(data.response.totalPrice, "{{amount_no_decimals_with_comma_separator}}₫")
        );
        $('.site-cart .section-cart .section-header .section-title>span').empty();
        $('.site-cart .section-cart .section-header .section-title>span').append('( ' + data.response.totalQty + ' Sản Phẩm )');
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
$(document).ready(function(){
  $('.site-cart .total-price .btn-action button').click(function() {
    var url = $(this).attr('data-url');
    var token = $('meta[name="csrf-token"]').attr('content');
    post(url, {type: 'buy_cart', _token: token});
  });
});
