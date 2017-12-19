<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 13.11.2017
 */
/* @var $this yii\web\View */
/* @var $form \yii\widgets\ActiveForm */
/* @var $model \v3p\aff\models\V3pFtSoption */
$widget = $this->context;
?>

<ul class="sx-filters-tree">
  <li>

  <div class="checkbox">
        <input type="checkbox" id="filter-check-<?= $model->id; ?>" name="<?= $handler->formName(); ?>[f<?= $feature->id; ?>][]"
               value="<?= $model->id; ?>"
            <?= in_array($model->id, (array) $values) ? "checked": ""; ?>
            <?= !in_array($model->id, (array) array_keys($options)) ? "disabled": ""; ?>
            <?/*= !in_array($model->id, (array) $options) ? "disabled": ""; */?>
        >
        <label for="filter-check-<?= $model->id; ?>"><?= $model->title; ?></label>
    </div>
    <? if ($model->children) : ?>
        <? foreach ($model->children as $child) : ?>
            <?= $this->render('_tree_feature-value', [
                    'model' => $child,
                    'handler' => $handler,
                    'feature' => $feature,
                    'values' => $values,
                    'options' => $options,
            ]); ?>
        <? endforeach; ?>
    <? endif; ?>
    </li>
</ul>



