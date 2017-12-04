<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 04.12.2017
 */

namespace v3p\aff\models;

use skeeks\cms\shop\models\ShopCmsContentElement;

/**
 * @property V3pProduct $v3pProduct
 *
 * Class ShopCmsContentElement
 * @package skeeks\cms\shop\models
 */
class V3pContentElement extends ShopCmsContentElement
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getV3pProduct()
    {
        return $this->hasOne(V3pProduct::className(), ['id' => 'id']);
    }


}