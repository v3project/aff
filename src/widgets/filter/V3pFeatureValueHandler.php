<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 13.11.2017
 */

namespace v3p\aff\widgets\filter;

use backend\models\cont\Feature;
use backend\models\cont\FeatureValue;
use skeeks\cms\models\CmsContentElement;
use skeeks\yii2\queryfilter\IQueryFilterHandler;
use v3p\aff\models\V3pFeature;
use v3p\aff\models\V3pFtSoption;
use v3p\aff\models\V3pProduct;
use v3p\aff\models\V3pProductFeatureValue;
use v3project\yii2\productfilter\EavFiltersHandler;
use v3project\yii2\productfilter\IFiltersHandler;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * @property ActiveQuery $baseQuery
 * @property ActiveQuery|int[] $elements
 *
 * ***
 *
 * @property V3pFtSoption $baseCategory
 * @property V3pFtSoption $baseBrand
 *
 * Class V3pProductFeatureValueHandler
 * @package v3p\aff\widgets\filter
 */
class V3pFeatureValueHandler extends DynamicModel
    implements IQueryFilterHandler
{

    public function formName()
    {
        return 'fv';
    }

    public $viewFile = '@v3p/aff/widgets/filter/views/feature-value';

    protected $_features = [];

    protected $_feature_values_data = [];
    protected $_feature_ids = [];
    protected $_ft_soptions_data = [];

    public $base_category_id;
    public $base_brand_id;

    /**
     * @return ActiveQuery
     */
    public function getBaseBrand() {
        return V3pFtSoption::findOne($this->base_brand_id);
    }
    /**
     * @return ActiveQuery
     */
    public function getBaseCategory() {
        return V3pFtSoption::findOne($this->base_category_id);
    }



    /**
     * @var ActiveQuery
     */
    protected $_baseQuery;

    public function init()
    {

        parent::init();

        if (!$this->baseQuery) {
            throw new InvalidConfigException('Не указан базовый запрос');
        }
    }

    public function getFeatureIdsQuery()
    {
        $activeQuery = clone $this->baseQuery;

        $activeQuery->joinWith(['v3toysProductProperty.productFeatureValues as fv']);
        $activeQuery->select(['fv.feature_id as id']);
        $activeQuery->distinct(['fv.feature_id' => true]);
        $activeQuery->andWhere(['NOT IN', 'fv.feature_id', V3pFeature::HIDDEN_FEATURE_IDS]);
        //$activeQuery2->andWhere(['fv.feature_type' => 'детальная']);
        /*$activeQuery->andWhere([
            'fv.feature_value_type' => [
                V3pFeature::VALUE_TYPE_BOOL,
                V3pFeature::VALUE_TYPE_LEAF_SOPTION,
                V3pFeature::VALUE_TYPE_ANY_SOPTION
            ]
        ]);*/
        $activeQuery->orderBy = [];
        $activeQuery->groupBy = [];
        $activeQuery->with = [];

        return $activeQuery;
    }

    public function initFeaturesByIdsQuery(ActiveQuery $featureIdsQuery)
    {
        $this->_feature_ids = $featureIdsQuery->asArray()->all();

        if ($this->_feature_ids) {
            $this->_feature_ids = ArrayHelper::map($this->_feature_ids, 'id', 'id');
        }

        if ($this->_feature_ids) {
            $query = V3pFeature::find()
                ->andWhere(['NOT IN', 'id', V3pFeature::HIDDEN_FEATURE_IDS])
                ->orderBy([
                'priority' => SORT_ASC
            ]);
            $query->andWhere([
                'id' => $this->_feature_ids
            ]);
            $this->initFeatures($query->all());
        } else {
            return false;
        }

        return $this;
    }


    /**
     * @param array $features
     * @return $this
     */
    public function initFeatures($features = [])
    {
        if ($features) {
            foreach ($features as $feature) {
                $this->_featureInit($feature);
            }
        }

        return $this;
    }

    protected $_elements = null;

    /**
     * @return array|null|\yii\db\ActiveRecord[]
     */
    public function getElements()
    {

        if ($this->_elements === null) {
            $activeQuery = clone $this->baseQuery;

            $activeQuery->joinWith(['v3toysProductProperty as v3property']);
            $activeQuery->select(['v3property.v3toys_id as id']);
            $activeQuery->orderBy = [];
            $activeQuery->groupBy = [];
            $activeQuery->with = [];
            //echo $activeQuery->count();die;

            $this->_elements = $activeQuery;

            /*$this->_elements = $activeQuery->asArray()->all();
            if ($this->_elements) {
                $this->_elements = ArrayHelper::map($this->_elements, 'id', 'id');
            }*/
        }

        return $this->_elements;
    }

    /**
     * @param QueryInterface $baseQuery
     * @return $this
     */
    public function setBaseQuery(QueryInterface $baseQuery)
    {
        $this->_baseQuery = clone $baseQuery;
        return $this;
    }

    /**
     * @return ActiveQuery
     */
    public function getBaseQuery()
    {
        return $this->_baseQuery;
    }

    public function initAll()
    {
        /**
         * @var V3pFeature $feature
         */
        if ($features = V3pFeature::find()->all()) {
            foreach ($features as $feature) {
                $this->_featureInit($feature);
            }
        }

        return $this;
    }

    /**
     * @param V3pFeature $feature
     */
    protected function _featureInit(V3pFeature $feature)
    {
        $name = $this->getAttributeName($feature->id);
        if (in_array($feature->value_type, [
            'int',
            'num',
            'int_range',
            'num_range',
        ])) {

            $this->defineAttribute($this->getAttributeNameRangeFrom($feature->id), '');
            $this->defineAttribute($this->getAttributeNameRangeTo($feature->id), '');

            $this->addRule([
                $this->getAttributeNameRangeFrom($feature->id),
                $this->getAttributeNameRangeTo($feature->id)
            ], "safe");

        }

        $this->defineAttribute($name, "");
        $this->addRule([$name], "safe");

        $this->_features[$name] = $feature;
    }

    public $_prefixRange = "r";

    /**
     * @param $propertyCode
     * @return string
     */
    public function getAttributeNameRangeFrom($feature_id)
    {
        return $this->getAttributeName($feature_id) . $this->_prefixRange . "From";
    }

    /**
     * @param $propertyCode
     * @return string
     */
    public function getAttributeNameRangeTo($feature_id)
    {
        return $this->getAttributeName($feature_id) . $this->_prefixRange . "To";
    }

    public function getAttributeName($feature_id)
    {
        return 'f' . $feature_id;
    }

    /**
     * @param V3pFeature $feature
     * @return array
     */
    public function getOptions(V3pFeature $feature)
    {
        $feature_id = $feature->id;

        if (in_array($feature->value_type, [V3pFeature::VALUE_TYPE_ANY_SOPTION, V3pFeature::VALUE_TYPE_LEAF_SOPTION])) {
            $this->_ft_soptions_data = V3pFtSoption::find()->joinWith('featureValues as fv')
                ->andWhere(['fv.feature_id' => $feature_id])
                ->andWhere(["fv.product_id" => $this->elements])
                ->all();

            if ($this->_ft_soptions_data) {
                return ArrayHelper::map(
                    $this->_ft_soptions_data,
                    'id',
                    'title'
                );
            }
        } else if (in_array($feature->value_type, [V3pFeature::VALUE_TYPE_BOOL])) {
            $this->_ft_soptions_data = V3pProductFeatureValue::find()
                ->andWhere(['feature_id' => $feature_id])
                ->andWhere(["product_id" => $this->elements])
                ->distinct(true)
                ->select(['ft_bool_value'])
                ->all();

            if ($this->_ft_soptions_data) {
                $result = [];
                foreach ($this->_ft_soptions_data as $ft_bool_value => $ft_bool_value) {
                    if ($ft_bool_value == 0) {
                        $result[0] = 'Нет';
                    } else if ($ft_bool_value == 1) {
                        $result[1] = 'Да';
                    }
                }

                return $result;
            }
        }




        return [];
    }

    public function getMaxValue($code)
    {
        $value = 0;

        $feature = $this->getFeatureByCode($code);

        if (in_array($feature->value_type, ['int', 'int_range'])) {

            $valueFromDb = V3pProductFeatureValue::find()
                ->select(["max(ft_int_value) as value, max(ft_int_value2) as value2"])
                ->andWhere(['feature_id' => $feature->id])
                ->andWhere(["product_id" => $this->elements])
                ->andWhere([
                    'or',
                    ['is not', 'ft_int_value', null],
                    ['is not', 'ft_int_value2', null]
                ])
                ->asArray()
                ->one();


            if (isset($valueFromDb['value'])) {
                $value = $valueFromDb['value'];
            }

            if (isset($valueFromDb['value2'])) {
                if ($valueFromDb['value2'] > $value) {
                    $value = $valueFromDb['value2'];
                }
            }

        } elseif (in_array($feature->value_type, ['num', 'num_range'])) {
            $valueFromDb = V3pProductFeatureValue::find()
                ->select(["max(ft_num_value) as value, max(ft_num_value2) as value2"])
                ->andWhere(['feature_id' => $feature->id])
                ->andWhere(["product_id" => $this->elements])
                ->andWhere([
                    'or',
                    ['is not', 'ft_num_value', null],
                    ['is not', 'ft_num_value2', null]
                ])
                ->asArray()
                ->one();

            if (isset($valueFromDb['value'])) {
                $value = $valueFromDb['value'];
            }

            if (isset($valueFromDb['value2'])) {
                if ($valueFromDb['value2'] > $value) {
                    $value = $valueFromDb['value2'];
                }
            }
        }

        return $value;
    }

    public function getMinValue($code)
    {
        $value = 0;

        $feature = $this->getFeatureByCode($code);

        if (in_array($feature->value_type, ['int', 'int_range'])) {

            $valueFromDb = V3pProductFeatureValue::find()
                ->select(["min(ft_int_value) as value, min(ft_int_value2) as value2"])
                ->andWhere(['feature_id' => $feature->id])
                ->andWhere(["product_id" => $this->elements])
                ->andWhere([
                    'or',
                    ['is not', 'ft_int_value', null],
                    ['is not', 'ft_int_value2', null]
                ])
                ->asArray()
                ->one();


            if (isset($valueFromDb['value'])) {
                $value = $valueFromDb['value'];
            }

            if (isset($valueFromDb['value2'])) {
                if ($valueFromDb['value2'] < $value) {
                    $value = $valueFromDb['value2'];
                }
            }

        } elseif (in_array($feature->value_type, ['num', 'num_range'])) {


            $valueFromDb = V3pProductFeatureValue::find()
                ->select(["min(ft_num_value) as value, min(ft_num_value2) as value2"])
                ->andWhere(['feature_id' => $feature->id])
                ->andWhere(["product_id" => $this->elements])
                ->andWhere([
                    'or',
                    ['is not', 'ft_num_value', null],
                    ['is not', 'ft_num_value2', null]
                ])
                ->asArray()
                ->one();


            if (isset($valueFromDb['value'])) {
                $value = $valueFromDb['value'];
            }

            if (isset($valueFromDb['value2'])) {
                if ($valueFromDb['value2'] < $value) {
                    $value = $valueFromDb['value2'];
                }
            }
        }

        return $value;
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        $result = [];

        foreach ($this->attributes() as $code) {
            if ($eavAttribute = $this->getFeatureByCode($code)) {
                $result[$code] = $eavAttribute->title . ($eavAttribute->measure_title ? ", {$eavAttribute->measure_title}" : "");
            } else {
                $result[$code] = $code;
            }

        }

        return $result;
    }


    /**
     * @param $code
     * @return V3pFeature|null
     */
    public function getFeatureByCode($code)
    {
        return ArrayHelper::getValue($this->_features, $code);
    }


    /**
     * @param ActiveQuery $activeQuery
     * @return $this
     */
    public function applyToQuery(QueryInterface $activeQuery)
    {
        $applyFilters = false;
        $unionQueries = [];

        if ($this->toArray()) {
            foreach ($this->toArray() as $key => $value) {
                $feature = $this->getFeatureByCode($key);
                if (
                    ($feature && $this->{$key}) ||
                    (
                        $feature
                        && isset($this->{$this->getAttributeNameRangeFrom($feature->id)}) && $this->{$this->getAttributeNameRangeFrom($feature->id)}
                        && isset($this->{$this->getAttributeNameRangeTo($feature->id)}) && $this->{$this->getAttributeNameRangeTo($feature->id)})
                ) {

                    $applyFilters = true;
                    $queryPart = V3pProductFeatureValue::find()->andWhere(['feature_id' => $feature->id])->select(['product_id as id']);

                    if (in_array($feature->value_type, [
                        V3pFeature::VALUE_TYPE_ANY_SOPTION,
                        V3pFeature::VALUE_TYPE_LEAF_SOPTION,
                    ])) {
                        if (is_array($this->{$key})) {
                            $queryPart->andWhere(['ft_soption_id' => $this->{$key}]);
                        }
                    } elseif (in_array($feature->value_type, [
                        V3pFeature::VALUE_TYPE_INT,
                    ])) {
                        $queryPart->andWhere(['>=', 'ft_int_value', (int) $this->{$this->getAttributeNameRangeFrom($feature->id)}]);
                        $queryPart->andWhere(['<=', 'ft_int_value', (int) $this->{$this->getAttributeNameRangeTo($feature->id)}]);
                    } elseif (in_array($feature->value_type, [
                        V3pFeature::VALUE_TYPE_NUM,
                    ])) {
                        $queryPart->andWhere(['>=', 'ft_num_value', $this->{$this->getAttributeNameRangeFrom($feature->id)}]);
                        $queryPart->andWhere(['<=', 'ft_num_value', $this->{$this->getAttributeNameRangeTo($feature->id)}]);
                    } elseif (in_array($feature->value_type, [
                        V3pFeature::VALUE_TYPE_INT_RANGE,
                    ])) {
                        $queryPart->andWhere([
                            'or',
                            [
                                'and',
                                [
                                    '>=', 'ft_int_value', (int) $this->{$this->getAttributeNameRangeFrom($feature->id)}
                                ],
                                [
                                    '<=', 'ft_int_value', (int) $this->{$this->getAttributeNameRangeTo($feature->id)}
                                ],
                            ],
                            [
                                'and',
                                [
                                    '>=', 'ft_int_value2', (int) $this->{$this->getAttributeNameRangeFrom($feature->id)}
                                ],
                                [
                                    '<=', 'ft_int_value2', (int) $this->{$this->getAttributeNameRangeTo($feature->id)}
                                ],
                            ]
                        ]);
                    } elseif (in_array($feature->value_type, [
                        V3pFeature::VALUE_TYPE_NUM_RANGE,
                    ])) {
                        $queryPart->andWhere([
                            'or',
                            [
                                'and',
                                [
                                    '>=', 'ft_num_value', (int) $this->{$this->getAttributeNameRangeFrom($feature->id)}
                                ],
                                [
                                    '<=', 'ft_num_value', (int) $this->{$this->getAttributeNameRangeTo($feature->id)}
                                ],
                            ],
                            [
                                'and',
                                [
                                    '>=', 'ft_num_value2', (int) $this->{$this->getAttributeNameRangeFrom($feature->id)}
                                ],
                                [
                                    '<=', 'ft_num_value2', (int) $this->{$this->getAttributeNameRangeTo($feature->id)}
                                ],
                            ]
                        ]);
                    } elseif (in_array($feature->value_type, [
                        V3pFeature::VALUE_TYPE_BOOL,
                    ])) {
                        $queryPart->andWhere(['ft_bool_value' => $this->{$key}]);
                    }

                    $unionQueries[] = $queryPart;
                }
            }
        }

        if ($applyFilters) {
            if ($unionQueries) {
                /**
                 * @var $unionQuery ActiveQuery
                 */
                $lastQuery = null;
                $unionQuery = null;
                $unionQueriesStings = [];
                foreach ($unionQueries as $query) {
                    if ($lastQuery) {
                        $lastQuery->andWhere(['in', 'product_id', $query]);
                        $lastQuery = $query;
                        continue;
                    }

                    if ($unionQuery === null) {
                        $unionQuery = $query;
                    } else {
                        $unionQuery->andWhere(['in', 'product_id', $query]);
                        $lastQuery = $query;
                    }
                }
            }

            //print_r($unionQuery->createCommand()->rawSql);die;
            $activeQuery->joinWith(['v3toysProductProperty as v3property']);
            $activeQuery->andWhere(['in', 'v3property.v3toys_id', $unionQuery]);
            //print_r($activeQuery->createCommand()->rawSql);die;
        }


        return $this;
    }

    /**
     * @param DataProviderInterface $dataProvider
     * @return $this
     */
    public function applyToDataProvider(DataProviderInterface $dataProvider)
    {
        return $this->initQuery($dataProvider->query);
    }

    /**
     * @param ActiveForm $form
     * @return string
     */
    public function render(ActiveForm $form)
    {
        return \Yii::$app->view->render($this->viewFile, [
            'form' => $form,
            'handler' => $this
        ]);
    }

}
