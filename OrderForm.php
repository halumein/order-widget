<?php
namespace backend\widgets\orderForm;

use pistol88\order\models\Order;
use pistol88\order\models\PaymentType;
use halumein\cashbox\models\Operation;
use halumein\cashbox\models\Cashbox;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii;

class OrderForm extends \yii\base\Widget
{
    public $withPayment = false;
    public $staffer = false;

    public function init()
    {
        \app\widgets\orderForm\assets\OrderFormAsset::register($this->getView());

        if (is_callable($this->staffer)) {
            $this->staffer = $this->staffer->__invoke();
        }

        return parent::init();
    }

    public function run()
    {
        $orderModel = new Order();
        $paymentModel = new Operation();

        $paymentTypes = ArrayHelper::map(PaymentType::find()->orderBy('order DESC')->all(), 'id', 'name');
        $cashboxes = Cashbox::getAvailable();


        return $this->render('orderForm', [
            'orderModel' => $orderModel,
            'paymentModel' => $paymentModel,
            'paymentTypes' => $paymentTypes,
            'withPayment' => $this->withPayment,
            'cashboxes' => $cashboxes,
            'staffer' => $this->staffer,
        ]);
    }
}
