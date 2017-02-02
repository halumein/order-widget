<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

?>
<?php $form = ActiveForm::begin([
    'id' => $model->formName(),
    'action' =>  Url::to(['/order-form/pay-order-ajax']),
    'options' => [
        'data-role' => 'payment-form',
    ]
]); ?>

<div class="row row-centered">
    <div class="col-xs-12 col-centered col-fixed text-right">
        <div class="form-group">
            <?= Html::button('Провести оплату', [
                'class' => 'btn btn-success',
                'id' => 'submit-payment',
                'style' => 'width: 100%',
                'data-role' => 'payment-confirm'
            ]); ?>
        </div>
    </div>
</div>

<div class="row row-centered">
    <div class="col-xs-12 col-centered col-fixed text-right">
        <div class="form-group">
            <?= Html::button('Без оплаты', [
                'class' => 'btn btn-danger',
                'id' => 'cancel-payment',
                'style' => 'width: 100%',
                'data-role' => 'payment-cancel'
            ]); ?>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingThree">
        <h4 class="panel-title">
            <a class="heading" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseTwo">
                Оплата заказа
            </a>
        </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree" aria-expanded="false">
        <div class="row panel-body">
            <div class="hidden" data-role="tools" data-less-sum=1<?php // echo $lessSum ?>>
                <?= $form->field($model, 'item_id')->textInput([
                        'data-role' => 'payment-order-id',
                    ])?>
                <?= $form->field($model, 'itemCost')->textInput([
                        'data-role' => 'payment-order-cost',
                    ])?>
                <?= $form->field($model, 'paymentTypeId')->textInput([
                        'data-role' => 'payment-type-id',
                    ])?>
            </div>

                <div class="col-xs-12 text-center">
                    <h3>Сумма к оплате: <span data-role="payment-cost-notice">0</h3>
                </div>

                <div class="col-xs-12 col-centered col-fixed text-left">
                    <?= $form->field($model, 'cashbox_id')->dropDownList([
                        'items' => ArrayHelper::map($cashboxes, 'id', 'name'),
                        'data-role' => 'payment-cashbox-id'
                        ]) ?>
                </div>

                <div class="col-xs-6 col-centered col-fixed text-left">
                    <?= $form->field($model, 'sum')->textInput([
                        'maxlength' => true,
                        'data-role' => 'payment-sum',
                        'data-less' => 'false',
                        'placeholder' => "внесено",
                        'class' => "form-control"
                        ]) ?>
                </div>
            	<div class="col-xs-6 col-centered col-fixed text-left">
            		<p><label>Сдача:</label></p>
            		<div class="payment-change"><strong data-role="payment-change-notice">0</strong></div>
            	</div>

                <div class="col-xs-12 col-centered col-fixed text-left">
                    <?= $form->field($model, 'comment')->textArea([
                        'class' => 'form-control',
                        'rows' => 4,
                        'data-role' => 'payment-comment'
                        ]) ?>
                </div>
            <div class="col-xs-12 col-centered col-fixed">
            	<p><span id="payment-notice" data-role="payment-notice"></span></p>
            </div>


        </div>
    </div>
</div>

<div class="row row-centered">
    <div class="col-xs-12 col-centered col-fixed text-right">
        <div class="form-group">
            <?= Html::button('Провести оплату', [
                'class' => 'btn btn-success',
                'id' => 'submit-payment',
                'style' => 'width: 100%',
                'data-role' => 'payment-confirm'
            ]); ?>
        </div>
    </div>
</div>

<div class="row row-centered">
    <div class="col-xs-12 col-centered col-fixed text-right">
        <div class="form-group">
            <?= Html::button('Без оплаты', [
                'class' => 'btn btn-danger',
                'id' => 'cancel-payment',
                'style' => 'width: 100%',
                'data-role' => 'payment-cancel'
            ]); ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
