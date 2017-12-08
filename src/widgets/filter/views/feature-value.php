<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 13.11.2017
 */
/* @var $this yii\web\View */
/* @var $widget \v3p\aff\widgets\filter\V3pProductFiterWidget */
/* @var $handler \v3p\aff\widgets\filter\V3pFeatureValueHandler */
/* @var $form \yii\widgets\ActiveForm */
/* @var $code string */
$widget = $this->context;
?>
<? foreach ($handler->toArray() as $code => $value) : ?>

    <?
    $feature = $handler->getFeatureByCode($code);
    ?>

    <? if ($feature && in_array($feature->value_type, ['int', 'num', 'int_range', 'num_range'])) : ?>
    <?
        $min = $handler->getMinValue($code);
        $max = $handler->getMaxValue($code);

        $val1Name = $handler->getAttributeNameRangeFrom($feature->id);
        $val1 = $handler->{$val1Name} ? $handler->{$val1Name} : $min;

        $val2Name = $handler->getAttributeNameRangeTo($feature->id);
        $val2 = $handler->{$val2Name} ? $handler->{$val2Name} : $max;

        $fromId = \yii\helpers\Html::getInputId($handler, $handler->getAttributeNameRangeFrom($feature->id));
        $toId = \yii\helpers\Html::getInputId($handler, $handler->getAttributeNameRangeTo($feature->id));
        $id = \yii\helpers\Html::getInputId($handler, $handler->getAttributeName($feature->id));

        ?>
        <? if ($min != $max
            //&& $max > 0
        ) : ?>
            <div class="sx-product-filter-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <label><?= $handler->getAttributeLabel($code); ?></label>
                    </div>

                    <div class="col-md-6">
                        <?= $form->field($handler, $handler->getAttributeNameRangeFrom($feature->id))
                            ->textInput(['placeholder' => $min])
                            ->label('От');
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($handler, $handler->getAttributeNameRangeTo($feature->id))
                            ->textInput(['placeholder' => $max])
                            ->label('До');
                        ?>
                    </div>


                    <div class="row">
                        <div class="col-md-12" style="height: 40px;">
                            <? echo \yii\jui\Slider::widget([
                                'clientEvents' => [
                                    'change' => new \yii\web\JsExpression(<<<JS
                        function( event, ui ) {
                          $("#{$fromId}").change();
                        },
JS
    ),
                                    'slide' => new \yii\web\JsExpression(<<<JS
                        function( event, ui ) {
                            $("#{$fromId}").val(ui.values[ 0 ]);
                            $("#{$toId}").val(ui.values[ 1 ]);
                        },
JS
    ),
                                ],
                                'clientOptions' => [
                                    'range' =>  true,
                                    'min' => (float) $min,
                                    'max' => (float) $max,
                                    'values' => [(float) $val1, (float) $val2],
                                ],
                            ]); ?>
                            <!--<div id="<?/*= $id; */?>"></div>-->
                        </div>
                    </div>
                </div>
            </div>
        <? endif; ?>
    <? elseif ($feature && in_array($feature->value_type, ['leaf_soption', 'any_soption'])) : ?>

        <? if ($options = $handler->getOptions($feature->id)) : ?>
            <? if (count($options) > 1) : ?>

                <?
                    $code = $handler->getAttributeName($feature->id);
                    $values = $handler->{$code};
                    $class = '';
                    $classCollapsed = 'collapsed';
                    $classCollapsedIn = '';
                    if ($values) {
                        $class = 'opened sx-filter-selected';
                        $classCollapsed = '';
                        $classCollapsedIn = 'in';
                    }

                    $info = '';
                    if ($feature->buyer_description) {
                        $info = "<i class='fa fa-question' title='{$feature->buyer_description}'></i>";
                    }
                ?>

            <div class="panel panel-default <?= $class; ?>">
                <div class="panel-heading" role="tab" id="heading<?= $feature->id; ?>">
                  <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $feature->id; ?>" aria-expanded="true" aria-controls="collapse<?= $feature->id; ?>" class="<?= $classCollapsed; ?>">
                      <?= $feature->title; ?>
                    </a>
                  </h4>
                </div>
                <div id="collapse<?= $feature->id; ?>" class="panel-collapse collapse <?= $classCollapsedIn; ?>" role="tabpanel" aria-labelledby="heading<?= $feature->id; ?>">
                  <div class="panel-body">

                      <?= $form->field($handler, $code, [
                            'options'      => [
                                'class' => 'filter--group ' . $class,
                                'tag' => 'section'
                            ],
                        ])->label(false)->checkboxList(
                            $options
                            , [
                        'class' => 'sx-filters-checkbox-options filter--group--inner',
                        'item' => function ($index, $label, $name, $checked, $value) use ($feature)
                        {
                            $input = \yii\helpers\Html::checkbox($name, $checked, [
                                'id' => 'filter-check-' .  $feature->id . "-" . $index,
                                'value' => $value
                            ]);
                            return <<<HTML
    <div class="checkbox">
    {$input}
    <label for="filter-check-{$feature->id}-{$index}">{$label}</label>
    </div>
HTML;

                        }
                    ]); ?>

                  </div>
                </div>
            </div>


            <? endif; ?>
        <? endif; ?>
    <? elseif ($feature && in_array($feature->value_type, ['bool'])) : ?>

        <? if ($feature->bool_type == 'yes') : ?>
            <div class="sx-product-filter-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($handler, $handler->getAttributeName($feature->id))->checkbox(); ?>
                    </div>
                </div>
            </div>
        <? else : ?>

            <?
                $code = $handler->getAttributeName($feature->id);
                $values = $handler->{$code};
                $class = '';
                $classCollapsed = 'collapsed';
                $classCollapsedIn = '';
                if ($values) {
                    $class = 'opened sx-filter-selected';
                    $classCollapsed = '';
                    $classCollapsedIn = 'in';
                }

                $info = '';
                if ($feature->buyer_description) {
                    $info = "<i class='fa fa-question' title='{$feature->buyer_description}'></i>";
                }
            ?>




            <div class="panel panel-default <?= $class; ?>">
                <div class="panel-heading" role="tab" id="heading<?= $feature->id; ?>">
                  <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $feature->id; ?>" aria-expanded="true" aria-controls="collapse<?= $feature->id; ?>" class="<?= $classCollapsed; ?>">
                      <?= $feature->title; ?>
                    </a>
                  </h4>
                </div>
                <div id="collapse<?= $feature->id; ?>" class="panel-collapse collapse <?= $classCollapsedIn; ?>" role="tabpanel" aria-labelledby="heading<?= $feature->id; ?>">
                  <div class="panel-body">

                      <?= $form->field($handler, $code, [
                            'options'      => [
                                'class' => 'filter--group ' . $class,
                                'tag' => 'section'
                            ],
                        ])->label(false)->checkboxList(
                            \Yii::$app->formatter->booleanFormat
                            , [
                        'class' => 'sx-filters-checkbox-options filter--group--inner',
                        'item' => function ($index, $label, $name, $checked, $value) use ($feature)
                        {
                            $input = \yii\helpers\Html::checkbox($name, $checked, [
                                'id' => 'filter-check-' .  $feature->id . "-" . $index,
                                'value' => $value
                            ]);
                            return <<<HTML
    <div class="checkbox">
    {$input}
    <label for="filter-check-{$feature->id}-{$index}">{$label}</label>
    </div>
HTML;

                        }
                    ]); ?>

                  </div>
                </div>
            </div>



        <? endif; ?>
    <? endif; ?>

<? endforeach; ?>

