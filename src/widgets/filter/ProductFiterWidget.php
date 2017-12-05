<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 13.11.2017
 */

namespace v3p\aff\widgets\filter;

use skeeks\yii2\queryfilter\QueryFilterWidget;

/**
 * Class ProductFiterWidget
 * @package v3p\aff\widgets\filter
 */
class ProductFiterWidget extends QueryFilterWidget
{
    /**
     * @var string
     */
    public $viewFile = 'product-filter';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        \Yii::$app->canurl->ADDimportant_pname($this->filtersParamName);
        parent::init();
    }
}