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


    public function loadFromRequest()
    {
        return parent::loadFromRequest();

        if ($this->handlers) {
            $names = [];
            foreach ($this->handlers as $handler) {
                if (method_exists($handler, 'formName')) {
                    $names[] = $handler->formName();
                }
            }
            \Yii::$app->canurl->ADDimportant_pnames($names);
        }

        if ($data = \Yii::$app->request->get()) {
            //Чистить незаполненные
            /*if (isset($data[$this->filtersParamName])) {
                foreach ($data[$this->filtersParamName] as $key => $value) {
                    if (!$value) {
                        unset($data[$this->filtersParamName][$key]);
                    }
                }
            }
            if (isset($data['_csrf'])) {
                unset($data['_csrf']);
            }

            $this->_data = $data;*/
            $this->load($data);

            /*\Yii::$app->response->redirect($this->getFilterUrl());
            \Yii::$app->end();*/

            /*$newUrl = $this->getFilterUrl();
            \Yii::$app->view->registerJs(<<<JS
window.history.pushState('page', 'title', '{$newUrl}');
JS*/
            /*        );*/


        } /*elseif ($data = \Yii::$app->request->get($this->filtersParamName)) {
            $data = (array)unserialize(base64_decode($data));
            $this->_data = $data;
            $this->load($data);
        }*/

        return $this;
    }


}