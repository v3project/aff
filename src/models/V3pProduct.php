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
 * This is the model class for table "v3p_feature".
 *
 * @property integer $id
 * @property string $v3p_product_id
 *
 * ***
 *
 * @property V3pFtSoption[] $ft_soptions
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
            [
                ['id', 'v3p_product_id', 'general_ast_product_id', 'nn_in_general_ast_product'],
                'integer'
            ],
            [['created_at', 'updated_at'], 'safe'],
            [['eneral_ast_product_sku', 'sku', 'stock_barcodes', 'astype', 'eneral_ast_product_sku'], 'string'],
            [['is_disabled'], 'boolean'],
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