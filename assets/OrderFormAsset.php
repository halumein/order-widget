<?php
namespace app\widgets\orderForm\assets;

use yii\web\AssetBundle;

class OrderFormAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        // 'yii\jui\JuiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $js = [
        'js/order-form.js',
    ];

    public $css = [
        'css/order-form.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }
}
