<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 26.07.2015
 */
namespace v3p\aff\widgets\filter\assets;
use yii\web\AssetBundle;

/**
 * Class ProductFiterWidgetAsset
 * @package v3p\aff\widgets\filter\assets
 */
class V3pProductFiterWidgetAsset extends AssetBundle
{
    public $sourcePath = '@v3p/aff/widgets/filter/assets/src';

    public $css = [
        'product-filter.css'
    ];
    public $js = [
        'product-filter.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'skeeks\sx\assets\Custom',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
