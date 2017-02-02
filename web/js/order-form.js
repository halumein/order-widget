if (typeof halumein == "undefined" || !halumein) {
    var halumein = {};
}

halumein.orderFormWidget = {
    init : function() {
        console.log('order form widget init');

        $clientFormBlock = $('[data-role=client-form-container]');
        $settings = $('[data-role=settings]');




        $orderForm = $('[data-role=order-form]');
        $orderFormComment = $('[data-role=order-comment]');
        $orderPaymentTypeSelect = $('[data-role=payment-type-select]');
        $orderSubmit = $('[data-role=order-submit]');
        $orderFormBlock = $('[data-role=order-form-container]');

        $paymentForm = $('[data-role=payment-form]');
        $paymentConfirm = $('[data-role=payment-confirm]');
        $paymentCancel = $('[data-role=payment-cancel]');
        $paymentFormBlock = $('[data-role=payment-form-container]');

        $paymentFormOrderIdInput = $paymentForm.find('[data-role=payment-order-id]');
        $paymentFormOrderCostInput = $paymentForm.find('[data-role=payment-order-cost]');
        $paymentTypeIdInput = $paymentForm.find('[data-role=payment-type-id]');
        $orderCostNotice = $paymentForm.find('[data-role=payment-cost-notice]');
        $paymentCashboxIdSelect = $paymentForm.find('[data-role=payment-cashbox-id]');
        $paymentSumInput = $paymentForm.find('[data-role=payment-sum]');
        $paymentChangeNotice = $paymentForm.find('[data-role=payment-change-notice]');
        $paymentComment = $paymentForm.find('[data-role=payment-comment]');
        $paymentNoticeBlock = $paymentForm.find('[data-role=payment-notice]');


        $orderSubmit.on('click', function() {
            if (+$('.pistol88-cart-count').html() > 0) {
                halumein.orderFormWidget.orderCreate($orderForm);
            }
        });

        $paymentSumInput.keydown(function (e) {
	        // Allow: backspace, delete, tab, escape, enter and .
	        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
	             // Allow: Ctrl+A
	            (e.keyCode == 65 && e.ctrlKey === true) ||
	             // Allow: Ctrl+C
	            (e.keyCode == 67 && e.ctrlKey === true) ||
	             // Allow: Ctrl+X
	            (e.keyCode == 88 && e.ctrlKey === true) ||
	             // Allow: home, end, left, right
	            (e.keyCode >= 35 && e.keyCode <= 39)) {
	                 // let it happen, don't do anything
	                 return;
	        }
	        // Ensure that it is a number and stop the keypress
	        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
	            e.preventDefault();
	        }
	    });

		$paymentSumInput.keyup(function (e) {
            var income = +$paymentSumInput.val(),
    			paymentCost = +$paymentFormOrderCostInput.val();
			if (income > paymentCost) {
				$paymentChangeNotice.html(income - paymentCost);
			} else {
				$paymentChangeNotice.html(0);
			}

            if (e.keyCode == 13) {
                if ($paymentSumInput.data('less') === false && $paymentSumInput.val() < $paymentFormOrderCostInput.val() && $paymentComment.val() === '') {
                    $paymentNoticeBlock.html('Напишите в комментарии почему сумма платежа меньше стоимости заказа');
                } else {
                    halumein.orderFormWidget.paymentConfirm($paymentForm);
                }
            }
		});

        $paymentConfirm.on('click', function() {
            var sum = +$paymentSumInput.val();
            var cost = +$paymentFormOrderCostInput.val();

            if ($paymentSumInput.data('less') === false && sum < cost && $paymentComment.val() === '') {
                $paymentNoticeBlock.html('Напишите в комментарии почему сумма платежа меньше стоимости заказа');
            } else {
                halumein.orderFormWidget.paymentConfirm($paymentForm);
            }

        });

        $paymentCancel.on('click', function() {
            halumein.orderFormWidget.changeForm();
            halumein.orderFormWidget.backToPrimaryState();
        });


    },

    orderCreate : function($form) {
        var sendUrl = $form.attr('action'),
            serializedFormData = $form.serialize(),
            paymentRequire = $($settings).data('payment');

        $.ajax({
            type : 'POST',
            url : sendUrl,
            data : serializedFormData,
            success : function(response) {
                if (response.status === 'success') {
                    if (response.paymentRequire && paymentRequire) {
                        $paymentFormOrderIdInput.val(response.orderId);
                        $paymentFormOrderCostInput.val(response.orderCost);
                        $paymentTypeIdInput.val(response.paymentTypeId);
                        $orderCostNotice.html(response.orderCost);
                        $paymentSumInput.val(response.orderCost).data('less', response.lessPayment);

                        halumein.orderFormWidget.changeForm();
                        $paymentSumInput.focus();
                        $paymentSumInput.select();
                    } else {
                        halumein.orderFormWidget.backToPrimaryState();
                    }
                } else {
                    alert('Ошибка создания заказа');
                    halumein.orderFormWidget.backToPrimaryState();
                }
            },
            fail : function() {
                alert('ошибка при отправке данных');
                console.log('fail');
            }
        });
        return false;
    },

    paymentConfirm : function($form) {
        var csrfToken = $form.find('[name="_csrf-backend"]').val(),
            sendUrl = $form.attr('action'),
            serializedFormData = $form.serialize();
        $.ajax({
            type : 'POST',
            url : sendUrl,
            data : serializedFormData,
            success : function(response) {
                if (response.status === 'success') {
                    halumein.orderFormWidget.changeForm();
                    halumein.orderFormWidget.backToPrimaryState();
                } else {
                    alert('Ошибка оплаты');
                    halumein.orderFormWidget.changeForm();
                    halumein.orderFormWidget.backToPrimaryState();
                }
            },
            fail : function() {
                alert('ошибка при отправке данных');
                console.log('fail');
            }
        });
        return false;
    },

    clearCart : function() {
        $('.pistol88-cart').fadeOut('fast', function() {
            $(this).html('Корзина пуста').fadeIn()
        });
        $('.pistol88-cart-count').html('0');
        $('.pistol88-cart-informer').find('.pistol88-cart-count').html('0');
        $('.pistol88-cart-informer').find('.pistol88-cart-price').find("span").html('0');
        $('.pistol88-cart-informer').find('.pistol88-cart-price').find("s").remove();
    },

    backToPrimaryState : function() {
        // чистим блок с корзиной
        $('.pistol88-cart').fadeOut('fast', function() {
            $(this).html('Корзина пуста').fadeIn()
        });
        $('.pistol88-cart-count').html('0');
        $('.pistol88-cart-informer').find('.pistol88-cart-count').html('0');
        $('.pistol88-cart-informer').find('.pistol88-cart-price').find("span").html('0');
        $('.pistol88-cart-informer').find('.pistol88-cart-price').find("s").remove();

        // чистим блок промо
        halumein.orderFormWidget.clearPromocode();



        $clientFormBlock.find('input').val('');
        $clientFormBlock.removeClass('in');


        // на мойках шоукейс пока не используется
        // halumein.showcase.renderTargetContent('main');
        // удаляем все хлебные крошки кроме главной
        // $('[data-role=breadcrumbs]').find('[data-target=main]').nextAll().remove();

        $orderPaymentTypeSelect.prop("selectedIndex",0);
        $orderFormComment.val('');

        $paymentChangeNotice.html(0)
        $paymentSumInput.val('').data('less', false);;
        $paymentComment.val('');
        $paymentNoticeBlock.html('');
    },
    clearPromocode: function() {
        var form = $('[data-role=promocode-enter-form]');

        if ($(form).find('[name=promocode]').val() == '') {
            return false;
        }

        var data = $(form).serialize();
        data = data+'&clear=1';

        jQuery.post($(form).attr('action'), data,
            function(json) {
                if(json.result == 'success') {
                    $(form).find('input[type=text]').css({'border': '1px solid #ccc'}).val('');
                    $(form).find('.promo-code-discount').show('slow').html(json.message);

                    setTimeout(function() { $('.promo-code-discount').hide('slow'); }, 2300);

                    if(json.informer) {
                        $('.pistol88-cart-informer').replaceWith(json.informer);
                    }
                }
                else {
                    $(form).find('input[type=text]').css({'border': '1px solid red'});
                    console.log(json.errors);
                }

                return true;

            }, "json");

        return false;
    },
    changeForm : function() {
        $orderFormBlock.animate({width:'toggle'},350);
        $paymentFormBlock.animate({width:'toggle'},350);
    },

}

$(function() {
    halumein.orderFormWidget.init();
});
