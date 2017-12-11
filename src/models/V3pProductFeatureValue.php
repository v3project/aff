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
use yii\helpers\Json;

/**
 * This is the model class for table "v3p_product_feature_value".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $product_id
 * @property integer $feature_id
 * @property string $feature_value_type
 * @property string $ft_not_value
 * @property integer $ft_soption_id
 * @property string $ft_string_value
 * @property string $ft_text_value
 * @property integer $ft_int_value
 * @property integer $ft_int_value2
 * @property integer $ft_num_value
 * @property integer $ft_num_value2
 * @property string $ft_json_value
 * @property integer $ft_bool_value
 * @property integer $check_is_valid
 * @property string $feature_type
 * @property integer $feature_min_value
 * @property integer $feature_max_value
 * @property integer $feature_min_choosen_soption_depth
 * @property integer $feature_max_choosen_soption_depth
 * @property integer $ft_soption_depth
 * @property integer $feature_priority
 * @property string $feature_value_as_json
 * @property string $feature_value_as_text
 *
 * ***
 *
 * @property string $valueAsText
 * @property V3pFeature $feature
 * @property V3pFtSoption $ftSoption
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
            [['ft_bool_value'], 'boolean'],
            [['ft_num_value', 'ft_num_value2'], 'number'],
            [['ft_text_value', 'feature_value_as_json', 'feature_value_as_text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['product_id', 'feature_id', 'feature_value_type'], 'required'],
            [
                [
                    'id',
                    'product_id',
                    'feature_id',
                    'ft_soption_id',
                    'ft_int_value',
                    'ft_int_value2',
                    'check_is_valid',
                    'feature_min_value',
                    'feature_max_value',
                    'feature_min_choosen_soption_depth',
                    'feature_max_choosen_soption_depth',
                    'ft_soption_depth',
                    'feature_priority'
                ],
                'integer'
            ],
            [
                ['feature_value_type', 'ft_not_value', 'ft_string_value', 'ft_json_value', 'feature_type'],
                'string',
                'max' => 255
            ],
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
            'product_id' => 'Product ID',
            'feature_id' => 'Feature ID',
            'feature_value_type' => 'Feature Value Type',
            'ft_not_value' => 'Ft Not Value',
            'ft_soption_id' => 'Ft Soption ID',
            'ft_string_value' => 'Ft String Value',
            'ft_text_value' => 'Ft Text Value',
            'ft_int_value' => 'Ft Int Value',
            'ft_int_value2' => 'Ft Int Value2',
            'ft_num_value' => 'Ft Num Value',
            'ft_num_value2' => 'Ft Num Value2',
            'ft_json_value' => 'Ft Json Value',
            'ft_bool_value' => 'Ft Bool Value',
            'check_is_valid' => 'Check Is Valid',
            'feature_type' => 'Feature Type',
            'feature_min_value' => 'Feature Min Value',
            'feature_max_value' => 'Feature Max Value',
            'feature_min_choosen_soption_depth' => 'Feature Min Choosen Soption Depth',
            'feature_max_choosen_soption_depth' => 'Feature Max Choosen Soption Depth',
            'ft_soption_depth' => 'Ft Soption Depth',
            'feature_priority' => 'Feature Priority',
            'feature_value_as_json' => 'Feature Value As Json',
            'feature_value_as_text' => 'Feature Value As Text',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function GETproduct()
    {
        return $this->hasOne(V3pProduct::class, ['id' => 'product_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFeature()
    {
        return $this->hasOne(V3pFeature::class, ['id' => 'feature_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFtSoption()
    {
        return $this->hasOne(V3pFtSoption::class, ['id' => 'ft_soption_id']);
    }


    /**
     * @return string
     */
    public function getValueAsText()
    {
        $feature = $this->feature;

        if (!$feature)
        {
            return '!!!' . $this->id;
        }
        if ($this->ft_not_value)
        {
            return $this->ft_not_value;
        }

        /*if ($feature->key == 'astype')
        {
            if ($this->ft_json_value)
            {
                $data = Json::decode($this->ft_json_value);
                return (string) ArrayHelper::getValue($data, 'astype')  . "\n" .  $this->ft_json_value;
            }
        }*/

        if (!$feature->value_type) {
            return '!!!';
        }

        if (in_array($feature->value_type, [
            V3pFeature::VALUE_TYPE_ANY_SOPTION,
            V3pFeature::VALUE_TYPE_LEAF_SOPTION,
        ]))
        {
            return $this->ftSoption ? $this->ftSoption->title : '-';
        }

        if (in_array($feature->value_type, [
            V3pFeature::VALUE_TYPE_INT,
        ]))
        {
            return (string) $this->ft_int_value  . " " . $feature->measure_title;;
        }

        if (in_array($feature->value_type, [
            V3pFeature::VALUE_TYPE_NUM,
        ]))
        {
            return (string) $this->ft_num_value  . " " . $feature->measure_title;;
        }

        if (in_array($feature->value_type, [
            V3pFeature::VALUE_TYPE_TEXT,
        ]))
        {
            return (string) $this->ft_text_value;
        }


        if (in_array($feature->value_type, [
            V3pFeature::VALUE_TYPE_JSON,
        ]))
        {
            if ($this->ft_json_value)
            {
                return (string) print_r(Json::decode($this->ft_json_value), true);
            } else
            {
                return (string) $this->ft_json_value;
            }
        }

        if (in_array($feature->value_type, [
            V3pFeature::VALUE_TYPE_STRING,
        ]))
        {
            return (string) $this->ft_string_value;
        }

        if (in_array($feature->value_type, [
            V3pFeature::VALUE_TYPE_INT_RANGE,
        ]))
        {
            return (string) "от " . $this->ft_int_value . " до " . $this->ft_int_value2 . " " . $feature->measure_title;
        }

        if (in_array($feature->value_type, [
            V3pFeature::VALUE_TYPE_NUM_RANGE,
        ]))
        {
            return (string) "от " . $this->ft_num_value . " до " . $this->ft_num_value2 . " " . $feature->measure_title;
        }

        if (in_array($feature->value_type, [
            V3pFeature::VALUE_TYPE_BOOL,
        ]))
        {
            return (string) \Yii::$app->formatter->asBoolean($this->ft_bool_value);
        }

        throw new Exception('!!!');
    }

    /**
     * @param $v3pProductId
     * @return ActiveQuery
     */
    public static function findByV3pProduct($v3pProductId) {
        return static::find()
            ->with('feature')
            ->joinWith('feature as f')
            ->with('ftSoption')
            ->orderBy(['f.priority' => SORT_ASC])
            ->where(['product_id' => $v3pProductId])
            ->andWhere(['NOT IN', 'f.id', V3pFeature::HIDDEN_FEATURE_IDS]);
    }
}