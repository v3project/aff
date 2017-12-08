<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 13.11.2017
 */
/* @var $this yii\web\View */
/* @var $widget \v3p\aff\widgets\filter\V3pProductFiterWidget */
\v3p\aff\widgets\filter\assets\V3pProductFiterWidgetAsset::register($this);
$this->registerJs(<<<JS
new sx.classes.ProductFilters();
JS
);

$widget = $this->context;
?>
<? $form = \yii\widgets\ActiveForm::begin([
    'method' => 'post',
    //'action' => "/" . \Yii::$app->request->pathInfo,
    'options' => [
        'data' => [
            'pjax' => 1
        ]
    ]
]); ?>
<? foreach ($widget->handlers as $filtersHandler) : ?>
    <?= $filtersHandler->render($form); ?>
<? endforeach; ?>
<button type="submit" class="btn btn-default">Применить</button>
<? \yii\widgets\ActiveForm::end(); ?>
