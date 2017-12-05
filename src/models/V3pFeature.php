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
 * @property string $created_at
 * @property string $updated_at
 * @property string $title
 * @property string $value_type
 * @property integer $priority
 * @property string $buyer_description
 * @property string $type
 * @property string $measure_title
 * @property integer $min_value
 * @property integer $max_value
 * @property integer $min_choosen_soption_depth
 * @property integer $max_choosen_soption_depth
 * @property boolean $is_disabled
 * @property string $bool_type
 *
 * ***
 *
 * @property V3pFtSoption[] $ftSoptions
 */
class V3pFeature extends ActiveRecord
{
    public static function primaryKey() {
        return ['id'];
    }

    const VALUE_TYPE_LEAF_SOPTION = 'leaf_soption';
    const VALUE_TYPE_ANY_SOPTION = 'any_soption';
    const VALUE_TYPE_BOOL = 'bool';
    const VALUE_TYPE_INT = 'int';
    const VALUE_TYPE_NUM = 'num';
    const VALUE_TYPE_INT_RANGE = 'int_range';
    const VALUE_TYPE_NUM_RANGE = 'num_range';
    const VALUE_TYPE_JSON = 'json';
    const VALUE_TYPE_STRING = 'string';
    const VALUE_TYPE_TEXT = 'text';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%v3p_feature}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['id', 'priority', 'min_value', 'max_value', 'min_choosen_soption_depth', 'max_choosen_soption_depth'],
                'integer'
            ],
            [['created_at', 'updated_at'], 'safe'],
            [['buyer_description'], 'string'],
            [['is_disabled'], 'boolean'],
            [['title'], 'string', 'max' => 255],
            [['value_type', 'type', 'measure_title', 'bool_type'], 'string', 'max' => 32],
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
            'title' => 'Title',
            'value_type' => 'Value Type',
            'priority' => 'Priority',
            'buyer_description' => 'Buyer Description',
            'type' => 'Type',
            'measure_title' => 'Measure Title',
            'min_value' => 'Min Value',
            'max_value' => 'Max Value',
            'min_choosen_soption_depth' => 'Min Choosen Soption Depth',
            'max_choosen_soption_depth' => 'Max Choosen Soption Depth',
            'is_disabled' => 'Is Disabled',
            'bool_type' => 'Bool Type',
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
    public function GETftSoptions()
    {
        return $this->hasMany(V3pFtSoption::class, ['feature_id' => 'id']);
    }
}