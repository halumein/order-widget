<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class OrderFormController extends Controller
{
    /**
     * @inheritdoc
     */
    // public function behaviors()
    // {
    //     return [
    //         'access' => [
    //             'class' => AccessControl::className(),
    //             'rules' => [
    //                 [
    //                     'actions' => ['error'],
    //                     'allow' => true,
    //                 ],
    //                 [
    //                     'actions' => ['index', 'create-order'],
    //                     'allow' => true,
    //                     'roles' => ['@'],
    //                 ],
    //             ],
    //         ],
    //         'verbs' => [
    //             'class' => VerbFilter::className(),
    //             'actions' => [
    //                 'logout' => ['post'],
    //             ],
    //         ],
    //     ];
    // }

    public function actionCreateOrderAjax()
    {
        $orderModel = yii::$app->orderModel;
        $paymentRequire = true; // требовать оплату
        $lessPayment = false; // платёж может быть меньше суммы заказа
        $model = new $orderModel;

        $order = yii::$app->request->post('Order');

        if (isset($order['staffer'])) {
            $model->staffer = $order['staffer'];
        }

        if ($model->load(yii::$app->request->post()) && $model->save()) {

            $module = Yii::$app->getModule('order');
            $orderEvent = new \pistol88\order\events\OrderEvent(['model' => $model, 'elements' => $model->elements]);
            $module->trigger($module::EVENT_ORDER_CREATE, $orderEvent);

            if ($module->paymentFreeTypeIds && in_array($model->payment_type_id, $module->paymentFreeTypeIds)) {
                $paymentRequire = false;
                \Yii::$app->order->setStatus($model->id, 'payed');
            }

            $cashboxModule = Yii::$app->getModule('cashbox');
            if ($cashboxModule->lessSumPaymentTypes && in_array($model->payment_type_id, $cashboxModule->lessSumPaymentTypes)) {
                $lessPayment = true;
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return [
                'status' => 'success',
                'orderId' => $model->id,
                'orderCost' => $model->cost,
                'paymentTypeId' => $model->payment_type_id,
                'paymentRequire' => $paymentRequire,
                'lessPayment' => $lessPayment,
            ];

        } else {

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return [
                'status' => 'error'
            ];
        }
    }

    public function actionPayOrderAjax()
    {
        $request = Yii::$app->request->post();

        if ($request) {
            $params = [];
            $type = 'income';
            $cashboxId = $request['Operation']['cashbox_id'];
            $params['model'] = Yii::$app->getModule('cashbox')->orderModel;
            $params['comment'] = $request['Operation']['comment'];
            $params['itemId'] = $request['Operation']['item_id'];

            // ёбаный стыд, но пока так. проверяет если внесено больше денег
            // чем стоит заказ (крупная купюра с которой сдали)
            // то присваиваем входящую сумму равной стоимости заказа.
            if ($request['Operation']['sum'] > $request['Operation']['itemCost'] ) {
                $sum = $request['Operation']['itemCost'];
            } else {
                $sum = $request['Operation']['sum'];
            }

            $status = false;
            // частично оплачен
            if ($request['Operation']['sum'] < $request['Operation']['itemCost']) {
                if (\Yii::$app->getModule('cashbox')->halfpayedStatus) {
                    $status = \Yii::$app->getModule('cashbox')->halfpayedStatus;
                }
            } else {
                // полностью оплачен
                if (\Yii::$app->getModule('cashbox')->payedStatus) {
                    $status = \Yii::$app->getModule('cashbox')->payedStatus;
                }
            }

            if ($status) {
                Yii::$app->order->setStatus($params['itemId'], $status);
            }

            $transaction = Yii::$app->cashbox->addTransaction($type, $sum, $cashboxId, $params);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($transaction['status'] !== 'success') {
                return $transaction['error'];
            }

            return [
                'status' => 'success',
            ];

        } else {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return [
                'status' => 'error',
                'message' => 'no data'
            ];
        }

    }

    public function actionCartInfo()
    {
        die(json_encode([
            'cart' => \pistol88\cart\widgets\ElementsList::widget(['columns' => '3', 'showCountArrows' => false, 'type' => ElementsList::TYPE_FULL]),
            'total' => \pistol88\cart\widgets\CartInformer::widget(['htmlTag' => 'div', 'text' => '{c} на {p}']),
            'count' => yii::$app->cart->count,
        ]));
    }


}
