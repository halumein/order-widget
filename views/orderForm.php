<div class="order-form-widget">
    <div class="hidden"
                data-role="settings"
                data-payment="<?= $withPayment ? 'true' : 'false' ?>"

                ></div>
    <!-- <div class="row"> -->
        <div class="order-form-container order-create-container" data-role="order-form-container">
            <?= $this->render('_forms/order', ['model' => $orderModel, 'paymentTypes' => $paymentTypes, 'staffer' => $staffer, 'paymentRequire' => $withPayment]); ?>
        </div>
        <div class="order-form-container" data-role="payment-form-container" style="display:none;">
            <?= $this->render('_forms/payment', ['model' => $paymentModel, 'cashboxes' => $cashboxes]); ?>
        </div>
    <!-- </div> -->
</div>
