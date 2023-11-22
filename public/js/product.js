$(document).ready(function(){

  var slider = displayGallery(0);

  $("#slide-advertise").owlCarousel({
    items: 2,
    autoplay: true,
    loop: true,
    margin: 10,
    autoplayHoverPause: true,
    nav: true,
    dots: false,
    responsive:{
      0:{
        items: 1,
      },
      992:{
        items: 2,
        animateOut: 'zoomInRight',
        animateIn: 'zoomOutLeft',
      }
    },
    navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>']
  });
  var height_description = $('#description').height();

  if(height_description > 768) {
    $('#description').animate({
      height: '768px',
    }, 500);
    $('#description .loadmore').css('display', 'block');
  }

  $('#description .loadmore a').click(function() {
    $('#description').animate({
      height: height_description + 20 +'px',
    }, 500);
    $('#description .loadmore').css('display', 'none');
    $('#description .hidemore').css('display', 'block');
  });

  $('#description .hidemore a').click(function() {
    $('#description').animate({
      height: '768px',
    }, 500);
    $('#description .loadmore').css('display', 'block');
    $('#description .hidemore').css('display', 'none');
  });

  $(".rating-product").rate();

  if($('.color-product .color-inner.active').attr('can-buy') == 0)
    $('.form-payment .row>div>button').prop('disabled', true);

  $('.select-color .color-inner').click(function() {
    var key = $(this).attr('data-key');
    if(!$(this).hasClass("active")) {
      $('.select-color .color-inner').removeClass('active');
      $(this).addClass('active');
      $('.price-product>div').css('display', 'none');
      $('.price-product>.product-'+key).css('display', 'block');
      if(!!slider.destroy) slider.destroy();
      $('.section-infomation .image-product>div').css('display', 'none');
      $('.section-infomation .image-product>.image-gallery-'+key).css('display', 'block');
      slider = displayGallery(key);
    }
    var can_by = $(this).attr('can-buy');
    $('#qty').val(can_by);
    if(can_by == 0)
      $('.form-payment .row>div>button').prop('disabled', true);
    else
      $('.form-payment .row>div>button').prop('disabled', false);
    var qty = $(this).attr('data-qty');
    $('#qty').attr('max', qty);
  });
  $('button.add_to_cart').click(function() {
    var product_id = $('.color-product .color-inner.active').attr('product-id');
    var qty = $('.form-payment input.qty').val();
    var data = {
      id: product_id,
      qty: qty
    };
    var url = $(this).attr('data-url');
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
  });

});

function displayGallery(key) {
  var slider = $('#imageGallery-' + key).lightSlider({
      gallery:true,
      item:1,
      loop:true,
      thumbItem:5,
      slideMargin:0,
      enableDrag: true,
      controls: false,
      slideMargin: 1,
      currentPagerPosition:'middle',
      onSliderLoad: function(el) {
          el.lightGallery({
              selector: '#imageGallery-' + key + ' .lslide',
          });
      },
      onBeforeSlide: function (el) {
        $('body').addClass('lg-on');
      },
      onAfterSlide: function (el) {
        $('body').removeClass('lg-on');
      }
  });
  return slider;
}
function scrollToxx() {
  $('html, body').animate({ scrollTop: $('.tab-description').offset().top }, 'slow');
  $('.tab-header .nav-tab-custom>li.active').removeClass('active');
  $('.tab-header .nav-tab-custom>li.active, .tab-content>div.active').removeClass('active in');
  $('.tab-header .nav-tab-custom>li:nth-child(2)').addClass('active');
  $('.tab-header .nav-tab-custom>li:nth-child(2), #vote').addClass('active in');
}
function minusInput() {
  var result = document.getElementById('qty');
  var qty = parseInt(result.value);
  var min = parseInt(result.min);
  if(!isNaN(qty) & qty > min )
    result.value--;
  if(result.value == 0)
    $('.form-payment .row>div>button').prop('disabled', true);
  return false;
}
function plusInput() {
  var result = document.getElementById('qty');
  var qty = parseInt(result.value);
  var max = parseInt(result.max);
  if(!isNaN(qty) & qty < max)
    result.value++;
  if(result.value > 0)
    $('.form-payment .row>div>button').prop('disabled', false);
  return false;
}
$(document).ready(function(){
  $('.form-payment button[type="submit"]').click(function () {
    var qty = $('#qty').val();
    var id = $('.select-color .color-inner.active').attr('product-id');
    var input_1 = $("<input>").attr("type", "hidden").attr("name", "type").val('buy_now');
    var input_2 = $("<input>").attr("type", "hidden").attr("name", "id").val(id);
    var input_3 = $("<input>").attr("type", "hidden").attr("name", "qty").val(qty);
    $('.form-payment>form').append(input_1);
    $('.form-payment>form').append(input_2);
    $('.form-payment>form').append(input_3);
  });
});
