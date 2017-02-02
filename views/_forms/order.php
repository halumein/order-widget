<?php
use yii\bootstrap\ActiveForm;

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

 ?>


 <?php $form = ActiveForm::begin([
         'id' => $model->formName(),
         'action' => Url::to(['/order-form/create-order-ajax']),
         'options' => [
             'data-role' => 'order-form',
            //  'data-ajax' => $useAjax ? 'true' : 'false',
         ]
     ]);
 ?>

    <div class="form-group offer">
         <?= Html::button(Yii::t('order', 'Create order'), [
                    'class' => 'btn btn-success order-create-button',
                    'id' => 'order-form-submit',
                    'data-role' => 'order-submit',
                ]); ?>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title">
                <a class="heading collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseTwo">
                    Клиент
                </a>
                <?=Html::a('<i class="glyphicon glyphicon-search"></i> Найти клиента', '#usersModal', ['id' => 'choose-user-id', 'class' => 'pull-right', 'data-toggle' => "modal", 'data-target' => "#usersModal"]);?>
            </h4>
        </div>

        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne" aria-expanded="false" data-role="client-form-container">
            <div class="panel-body">
                <?= \pistol88\order\widgets\ChooseClient::widget(['form' => $form, 'model' => $model]);?>
                <?php $this->registerJs("pistol88.createorder.updateCartUrl = '".Url::toRoute(['/order-form/cart-info'])."';"); ?>
                <select class="form-control service-choose-property" data-role="gos-nomer">
                    <option>Автомобиль...</option>
                </select>
            </div>
        </div>
    </div>

    <?php if ($staffer) { ?>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="staffer-heading">
                <h4 class="panel-title">
                    <a class="heading collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#staffer-collapse" aria-expanded="false" aria-controls="staffer-collapse">
                        Работник
                    </a>
                </h4>
            </div>
            <div id="staffer-collapse" class="panel-collapse collapse" role="tabpanel" aria-labelledby="staffer-collapse" aria-expanded="false" data-role="staffer-form-container">
                <div class="panel-body">
                    <?php foreach ($staffer as $key => $worker) { ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="<?= $worker->id ?>" checked name='Order[staffer][]' ><?= $worker->name ?> <?php if($worker->category) { ?>(<?= $worker->category->name ?>) <?php } ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

     <div class="panel panel-default">
         <div class="panel-heading" role="tab" id="headingTwo">
             <h4 class="panel-title">
                 <a class="heading" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                     Заказ
                 </a>
             </h4>
         </div>
         <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo" aria-expanded="false">
             <div class="row panel-body">
                 <div class="col-lg-12">
                     <div style="display: none;">
                         <?= $form->field($model, 'status')->label(false)->textInput(['value' => 'new', 'type' => 'hidden', 'maxlength' => true]) ?>
                     </div>
                     <?= $form->field($model, 'payment_type_id')->dropDownList($paymentTypes, [
                         'data-role' => 'payment-type-select',
                         ]) ?>
                 </div>
                 <div class="col-lg-12 col-xs-12">
                     <?= $form->field($model, 'comment')->textArea([
                         'maxlength' => true,
                         'data-role' => 'order-comment'
                         ]) ?>
                 </div>
                 <?php if($fields = $model->allfields) { ?>
                     <div class="col-lg-12 col-xs-12">
                         <?php foreach($fields as $fieldModel) { ?>
                             <div class="col-lg-12 col-xs-12">
                                 <?php
                                 if($widget = $fieldModel->type->widget) {
                                     echo $widget::widget(['form' => $form, 'fieldModel' => $fieldModel]);
                                 }
                                 else {
                                     echo $form->field(new FieldValue, 'value['.$fieldModel->id.']')->label($fieldModel->name)->textInput(['required' => ($fieldModel->required == 'yes')]);
                                 }
                                 ?>
                                 <?php if($fieldModel->description) { ?>
                                     <p><small><?=$fieldModel->description;?></small></p>
                                 <?php } ?>
                             </div>
                         <?php } ?>
                     </div>
                 <?php } ?>
             </div>
         </div>
     </div>

     <div class="form-group offer">
         <?= Html::button(Yii::t('order', 'Create order'), [
                    'class' => 'btn btn-success order-create-button',
                    'id' => 'order-form-submit',
                    'data-role' => 'order-submit',
                ]); ?>
     </div>
 <?php ActiveForm::end(); ?>
