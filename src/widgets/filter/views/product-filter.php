<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 13.11.2017
 */
/* @var $this yii\web\View */
/* @var $widget \yv\widgets\filters\ProductFilterWidget */
\v3p\aff\widgets\filter\assets\ProductFiterWidgetAsset::register($this);
$this->registerJs(<<<JS
new sx.classes.ProductFilters();
JS
);

$widget = $this->context;
?>
<? $form = \yii\widgets\ActiveForm::begin([
    'method' => 'post',
    'action' => "/" . \Yii::$app->request->pathInfo,
    'options' => [
        'data' => [
            'pjax' => 1
        ]
    ]
]); ?>
<? foreach ($widget->filtersHandlers as $filtersHandler) : ?>
    <? if ($filtersHandler->toArray()) : ?>
        <? foreach ($filtersHandler->toArray() as $key => $value) : ?>
            <?= $filtersHandler->renderByAttribute($key, $form); ?>
        <? endforeach; ?>
    <? endif; ?>
<? endforeach; ?>
<button type="submit" class="btn btn-default">Применить</button>
<? \yii\widgets\ActiveForm::end(); ?>
