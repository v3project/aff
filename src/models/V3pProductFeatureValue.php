
<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 04.12.2017
 */

namespace v3p\aff\models;

use app\models\V3pFtSoption;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "v3p_feature_value".
 *
 * @property integer $product_id
 * @property integer $feature_id
 * @property string  $feature_value_type
 * @property string  $ft_not_value
 * @property integer $ft_soption_id
 * @property string  $ft_string_value
 * @property string  $ft_text_value
 * @property integer $ft_int_value
 * @property integer $ft_int_value2
 * @property string  $ft_num_value
 * @property string  $ft_num_value2
 * @property string  $ft_json_value
 * @property boolean $ft_bool_value
 *
 * ***
 * @property V3pFeature $feature
 * @property V3pProduct $product
 *
 */
class V3pProductFeatureValue extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%v3p_product_feature_value}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'feature_id', 'ft_soption_id', 'ft_int_value', 'ft_int_value2'], 'integer'],
            [['ft_text_value', 'ft_json_value'], 'string'],
            [['ft_num_value', 'ft_num_value2'], 'number'],
            [['ft_bool_value'], 'boolean'],
            [['feature_value_type', 'ft_not_value'], 'string', 'max' => 32],
            [['ft_string_value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id'         => 'Product ID',
            'feature_id'         => 'Feature ID',
            'feature_value_type' => 'Feature Value Type',
            'ft_not_value'       => 'Ft Not Value',
            'ft_soption_id'      => 'Ft Soption ID',
            'ft_string_value'    => 'Ft String Value',
            'ft_text_value'      => 'Ft Text Value',
            'ft_int_value'       => 'Ft Int Value',
            'ft_int_value2'      => 'Ft Int Value2',
            'ft_num_value'       => 'Ft Num Value',
            'ft_num_value2'      => 'Ft Num Value2',
            'ft_json_value'      => 'Ft Json Value',
            'ft_bool_value'      => 'Ft Bool Value',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function GETproduct() { return $this->hasOne(V3pProduct::class, ['id' => 'product_id']); }
    /**
     * @return ActiveQuery
     */
    public function GETfeature() { return $this->hasOne(V3pProduct::class, ['id' => 'feature_id']); }

    /**
     * @return ActiveQuery
     */
    public function GETft_soption() { return $this->hasOne(V3pFtSoption::class, ['id' => 'ft_soption_id']); }
}