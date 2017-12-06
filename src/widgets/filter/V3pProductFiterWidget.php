<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 13.11.2017
 */

namespace v3p\aff\widgets\filter;

use skeeks\yii2\queryfilter\QueryFilterShortUrlWidget;
use skeeks\yii2\queryfilter\QueryFilterWidget;

/**
 * Class V3pProductFiterWidget
 * @package v3p\aff\widgets\filter
 */
class V3pProductFiterWidget extends QueryFilterShortUrlWidget
{
    /**
     * @var string
     */
    public $viewFile = '@v3p/aff/widgets/filter/views/product-filter';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        \Yii::$app->canurl->ADDimportant_pname($this->filtersParamName);
        parent::init();
    }





}