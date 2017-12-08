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
$this->registerCss(<<<CSS

CSS
);
$this->registerJs(<<<JS
new sx.classes.ProductFilters();

(function(sx, $, _)
{
    sx.classes.FiltersForm = sx.classes.Component.extend({

        _onDomReady: function()
        {
            var self = this;
            this.JqueryForm = $("#sx-filters-form");
            if ($(".form-group", this.JqueryForm).length > 0)
            {
                $("button", self.JqueryForm).fadeIn();
            }

            $("input, checkbox, select", this.JqueryForm).on("change", function()
            {
                if ($(this).data('no-submit'))
                {
                    return false;
                }

                self.JqueryForm.submit();
            });
        }
    });

    new sx.classes.FiltersForm();
})(sx, sx.$, sx._);

JS
);

$widget = $this->context;
?>

<? $form = \yii\widgets\ActiveForm::begin([
    'method' => 'post',
    //'action' => "/" . \Yii::$app->request->pathInfo,
    'options' => [
        'id' => 'sx-filters-form',
        'class' => 'sx-product-filters',
        'data' => [
            'pjax' => 1
        ]
    ]
]); ?>
<? foreach ($widget->handlers as $filtersHandler) : ?>
    <?= $filtersHandler->render($form); ?>
<? endforeach; ?>

<? if (\Yii::$app->request->get(\Yii::$app->cmsSearch->searchQueryParamName)) : ?>
    <input type="text" value="<?= \Yii::$app->cmsSearch->searchQuery; ?>" name="<?= \Yii::$app->cmsSearch->searchQueryParamName; ?>" />
<? endif; ?>
<div style="display: none;">
<button type="submit" class="btn btn-default">Применить</button>
</div>
<? \yii\widgets\ActiveForm::end(); ?>

<?
$this->registerJs(<<<JS
$('.filter--group').each(function(){
    var group = $(this),
        groupHeader = group.find('.filter--group--header'),
        classOpen = 'opened',
        showMore = group.find('.filter--show-more a'),
        showMoreTxt = showMore.find('.txt'),
        hiddenCont = group.find('.filter--hidden');

    groupHeader.click(function(){
        if (!group.hasClass(classOpen)) {
            group.addClass(classOpen);
        } else {
            group.removeClass(classOpen);
        }
    });

    showMore.click(function(e){
        e.preventDefault();

        if (hiddenCont.is(':hidden')) {
            hiddenCont.show();
            showMoreTxt.text('Скрыть');
        } else {
            hiddenCont.hide();
            showMoreTxt.text('Показать еще');
        }
    });
});
$("#sx-filters-form").fadeIn();
$('.sx-filters-checkbox-options').each(function () {
    var jContainer = $(this);
    var checkboxes = $('.checkbox', $(this));
    if (checkboxes.length > 4) {
        
        var last = 4;
        var counter = 0;
        checkboxes.each(function (){ 
            counter = counter + 1;
            if ($('input', $(this)).is(":checked")) {
                last = counter;
            }
        });
        
        counter = 0;
        var hiddenCount = 0;
        checkboxes.each(function (){ 
            counter = counter + 1;
            if (counter > last) {
                hiddenCount = hiddenCount + 1;
                $(this).addClass('sx-filter-option-hidden');
            }
        });
        var jLink = $("<a>", {'href' : '#', 'class' : 'dashed-link'}).append('<span class="txt">Показать еще</span> ' + hiddenCount); 
        var jLinkHide = $("<a>", {'href' : '#', 'class' : 'dashed-link'}).append('<span class="txt">Скрыть</span> ' + hiddenCount);
        jLinkHide.hide();
        
        $(this).append(
            $('<div>', {'class' : 'filter--show-more show_all_property'}).append(jLink).append(jLinkHide)
        );
        
        jLink.on('click', function () {
            $('.sx-filter-option-hidden', jContainer).addClass('sx-filter-option-visible');
            jLink.hide();
            jLinkHide.show();
            return false;
        });
        
        jLinkHide.on('click', function () {
            $('.sx-filter-option-visible', jContainer).removeClass('sx-filter-option-visible');
            jLink.show();
            jLinkHide.hide();
            return false;
        });
    }
});
JS
);
?>
