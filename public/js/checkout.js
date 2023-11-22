$(document).ready(function(){
  $('.payment-methods .list-content label').click(function() {
    $('.payment-methods .list-content>li').removeClass('active');
    $(this).parent('li').addClass('active');
  });
});
// validate form

$(document).ready(function(){
  var constraints = {
    email: {
      presence: {
        message: "^Email không được trống!"
      },
      email: {
        message: "^Email không đúng định dạng!"
      }
    },
    name: {
      presence: {
        message: "^Tên không được trống!"
      },
      length: {
        minimum: 3,
        maximum: 25,
        message: "^Độ dài từ 3 đến 25 ký tự"
      }
    },
    phone: {
      presence: {
        message: "^Số điện thoại không được trống!"
      },
      format: {
        pattern: "0[^6421][0-9]{8}",
        flags: "i",
        message: "^Số điện thoại không đúng định dạng!"
      }
    },
    address: {
      presence: {
        message: "^Địa chỉ không được trống!"
      }
    },
  };
  var form_checkout = $('.form-checkout>form');
  var form = document.querySelector('.form-checkout>form');

  var inputs = document.querySelectorAll(".form-checkout>form input, .form-checkout>form textarea, .form-checkout>form select")

  for (var i = 0; i < inputs.length; ++i) {
    inputs.item(i).addEventListener("change", function(ev) {
      var errors = validate(form, constraints) || {};
      showErrorsForInput(this, errors[this.name])
    });
  }

  $('button[type="submit"]').click(function() {

    var errors = validate(form, constraints);
    showErrors(form, errors || {});

    if(!errors) {
      var token = $('meta[name="csrf-token"]').attr('content');
      form_checkout.append($('<input type="hidden" name="_token">').val(token));

      var payment_method = $('input[type="radio"]:checked').val();
      form_checkout.append($('<input type="hidden" name="payment_method">').val(payment_method));

      var buy_method = form_checkout.attr('buy-method');
      form_checkout.append($('<input type="hidden" name="buy_method">').val(buy_method));

      if(buy_method == 'buy_now') {
        var product = $('.col-order .col-content .section-items .item').attr('data-product');
        form_checkout.append($('<input type="hidden" name="product_id">').val(product));

        var qty = $('.col-order .col-header h2 span').attr('data-qty');
        form_checkout.append($('<input type="hidden" name="totalQty">').val(qty));

        var price = $('.col-order .col-content .section-items .item').attr('data-price');
        form_checkout.append($('<input type="hidden" name="price">').val(price));
      }
      form_checkout.submit();
    }
  });

});

// Updates the inputs with the validation errors
function showErrors(form, errors) {
  // We loop through all the inputs and show the errors for that input
  _.each(form.querySelectorAll(".form-checkout>form input[name], .form-checkout>form select[name]"), function(input) {
    // Since the errors can be null if no errors were found we need to handle
    // that
    showErrorsForInput(input, errors && errors[input.name]);
  });
}

// Shows the errors for a specific input
function showErrorsForInput(input, errors) {
  // This is the root of the input
  var formGroup = closestParent(input.parentNode, "form-group")
    // Find where the error messages will be insert into
    ,
    messages = formGroup.querySelector(".messages");
  // First we remove any old messages and resets the classes
  resetFormGroup(formGroup);
  // If we have errors
  if (errors) {
    // we first mark the group has having errors
    formGroup.classList.add("has-error");
    // then we append all the errors
    _.each(errors, function(error) {
      addError(messages, error);
    });
  } else {
    // otherwise we simply mark it as success
    formGroup.classList.add("has-success");
  }
}

// Recusively finds the closest parent that has the specified class
function closestParent(child, className) {
  if (!child || child == document) {
    return null;
  }
  if (child.classList.contains(className)) {
    return child;
  } else {
    return closestParent(child.parentNode, className);
  }
}

function resetFormGroup(formGroup) {
  // Remove the success and error classes
  formGroup.classList.remove("has-error");
  formGroup.classList.remove("has-success");
  // and remove any old messages
  _.each(formGroup.querySelectorAll(".help-block.error"), function(el) {
    el.parentNode.removeChild(el);
  });
}

// Adds the specified error with the following markup
// <p class="help-block error">[message]</p>
function addError(messages, error) {
  var block = document.createElement("p");
  block.classList.add("help-block");
  block.classList.add("error");
  block.innerText = error;
  messages.appendChild(block);
}
