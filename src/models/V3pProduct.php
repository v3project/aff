<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 04.12.2017
 */

namespace v3p\aff\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "v3p_product".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $guiding_available_quantity
 * @property integer $guiding_available_nsk_quantity
 * @property string $guiding_buy_price
 * @property string $guiding_realize_price
 * @property string $mr_price
 * @property string $stock_barcodes
 * @property string $keywords
 * @property string $sku
 * @property string $astype
 * @property integer $general_ast_product_id
 * @property integer $nn_in_general_ast_product
 * @property string $general_ast_product_sku
 * @property string $disable_reason
 * @property integer $disable_comment
 * @property integer $duplicate_of_product_id
 * @property integer $is_disabled
 */
class V3pProduct extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%v3p_product}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['guiding_available_quantity', 'guiding_available_nsk_quantity', 'general_ast_product_id', 'nn_in_general_ast_product', 'duplicate_of_product_id',
                //'is_disabled'
            ], 'integer'],
            [['guiding_buy_price', 'guiding_realize_price', 'mr_price'], 'number'],
            [['stock_barcodes', 'keywords', 'sku', 'astype', 'general_ast_product_sku', 'disable_reason', 'disable_comment'], 'string', 'max' => 255],

            [$this->attributes(), 'default', 'value' => null],
            ['is_disabled', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'guiding_available_quantity' => 'Guiding Available Quantity',
            'guiding_available_nsk_quantity' => 'Guiding Available Nsk Quantity',
            'guiding_buy_price' => 'Guiding Buy Price',
            'guiding_realize_price' => 'Guiding Realize Price',
            'mr_price' => 'Mr Price',
            'stock_barcodes' => 'Stock Barcodes',
            'keywords' => 'Keywords',
            'sku' => 'Sku',
            'astype' => 'Astype',
            'general_ast_product_id' => 'General Ast Product ID',
            'nn_in_general_ast_product' => 'Nn In General Ast Product',
            'general_ast_product_sku' => 'General Ast Product Sku',
            'disable_reason' => 'Disable Reason',
            'disable_comment' => 'Disable Comment',
            'duplicate_of_product_id' => 'Duplicate Of Product ID',
            'is_disabled' => 'Is Disabled',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function GETfeatureValues()
    {
        return $this->hasMany(V3pFeatureValue::class, ['feature_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function GETft_soptions()
    {
        return $this->hasMany(V3pFtSoption::class, ['feature_id' => 'id']);
    }
}