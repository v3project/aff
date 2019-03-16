<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 04.12.2017
 */

namespace v3p\aff\models;


use paulzi\adjacencyList\AdjacencyListBehavior;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use paulzi\autotree\AutoTreeTrait;

/**
 * This is the model class for table "v3p_ft_soption".
 *
 * @property integer $id
 * @property string $hperiod
 * @property integer $updated_by_worker_id
 * @property integer $feature_id
 * @property string $feature_value_type
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property integer $parent_id
 * @property boolean $is_disabled
 * @property string $title
 * @property string $feature_type
 * @property string $feature_title
 * @property string $created_at
 * @property string $updated_at
 * @property integer $feature_min_choosen_soption_depth
 * @property integer $feature_max_choosen_soption_depth
 * @property string $brand_owner_country
 *
   ***
 * @property string $fullTitle
 */
class V3pFtSoption extends \yii\db\ActiveRecord
{
    use AutoTreeTrait;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return ArrayHelper::merge(parent::behaviors(), [

            [
                'class' => AdjacencyListBehavior::className(),
                'parentAttribute' => 'parent_id',
                'parentsJoinLevels'  => 0,
                'childrenJoinLevels' => 0,
                'sortable'           => false,
            ],
        ]);
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%v3p_ft_soption}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'feature_id',
                    'lft',
                    'rgt',
                    'depth',
                    'parent_id',
                    'feature_min_choosen_soption_depth',
                    'feature_max_choosen_soption_depth'
                ],
                'integer'
            ],
            [['is_disabled'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['feature_value_type', 'feature_type'], 'string', 'max' => 32],
            [['title', 'feature_title', 'brand_owner_country'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'feature_id' => 'Feature ID',
            'feature_value_type' => 'Feature Value Type',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'depth' => 'Depth',
            'parent_id' => 'Parent ID',
            'is_disabled' => 'Is Disabled',
            'title' => 'Title',
            'feature_type' => 'Feature Type',
            'feature_title' => 'Feature Title',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'feature_min_choosen_soption_depth' => 'Feature Min Choosen Soption Depth',
            'feature_max_choosen_soption_depth' => 'Feature Max Choosen Soption Depth',
            'brand_owner_country' => 'Brand Owner Country',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function GETfeatureValues()
    {
        return $this->hasMany(V3pProductFeatureValue::class, ['ft_soption_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getFullTitle() {
        $result = [];
        if ($this->depth > 1) {
            if ($parents = $this->getParents(1)->all()) {
                foreach ($parents as $parent) {
                    $result[] = $parent->title;
                }
            }
        }
        $result[] = $this->title;
        return implode(' / ', $result);
    }
}