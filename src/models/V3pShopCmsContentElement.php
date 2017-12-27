<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 06.03.2016
 */

namespace v3p\aff\models;

use skeeks\cms\shop\models\ShopCmsContentElement;
use skeeks\cms\shop\models\ShopProductPrice;
use v3p\aff\models\V3pProduct;
use v3toys\skeeks\models\V3toysProductProperty;
use yii\db\ActiveQuery;

/**
 * @property V3toysProductProperty $v3toysProductProperty
 *
 * @property ShopProductPrice $shopProductPrice1
 * @property ShopProductPrice $shopProductPrice2
 * @property ShopProductPrice $shopProductPrice3
 * @property ShopProductPrice $shopProductPrice4
 *
 * Class V3pShopCmsContentElement
 * @package v3p\aff\models
 */
class V3pShopCmsContentElement extends ShopCmsContentElement
{
    /**
     * @return ActiveQuery
     */
    static public function find()
    {
        $query = parent::find();
        $content_id = \Yii::$app->v3p->cms_content_id;
        if (!$content_id) {
            //TODO: throw new Exception('Не настроен компонент v3p');
            $content_id = 2;
        }
        return $query->andWhere(['content_id' => $content_id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getV3toysProductProperty()
    {
        return $this->hasOne(V3toysProductProperty::className(), ['id' => 'id'])->from(['v3toysProductProperty' => V3toysProductProperty::tableName()]);
    }

    /**
     * @return $this
     */
    public function getShopProductPrice1() {
        //TODO: реализовать
        return $this->hasOne(ShopProductPrice::class, ['product_id' => 'id'])->via('shopProduct')->andWhere([
            ''
        ]);
    }

    /**
     * @return $this
     */
    public function getShopProductPrice2() {
        return $this->getShopProductPrice1();
    }

    /**
     * @return $this
     */
    public function getShopProductPrice3() {
        return $this->getShopProductPrice1();
    }

    /**
     * @return $this
     */
    public function getShopProductPrice4() {
        return $this->getShopProductPrice1();
    }

}