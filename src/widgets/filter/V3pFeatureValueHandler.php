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
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
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
    protected $ids_query = null;

    public function smartInit(ActiveQuery $baseQuery)
    {
        $activeQuery = clone $baseQuery;
        $activeQuery2 = clone $baseQuery;


        //$activeQuery->joinWith(['v3toysProductProperty as v3property']);
        $activeQuery2->joinWith(['v3toysProductProperty.productFeatureValues as fv']);
        $activeQuery2->select(['fv.feature_id as id']);
        $activeQuery2->distinct(['fv.feature_id' => true]);
        $activeQuery2->andWhere(['fv.feature_type' => 'детальная']);
        $activeQuery2->andWhere(['fv.feature_value_type' => [
            V3pFeature::VALUE_TYPE_BOOL,
            V3pFeature::VALUE_TYPE_LEAF_SOPTION,
            V3pFeature::VALUE_TYPE_ANY_SOPTION
        ]]);
        $activeQuery2->orderBy = [];
        $activeQuery2->groupBy = [];
        $activeQuery2->with = [];

        $this->_feature_ids = $activeQuery2->asArray()->all();

        if ($this->_feature_ids) {
            $this->_feature_ids = ArrayHelper::map($this->_feature_ids, 'id', 'id');
        }


        if ($this->_feature_ids) {
            $query = V3pFeature::find();
            $query->andWhere([
                'id' => $this->_feature_ids
            ]);
            /**
             * @var V3pFeature $feature
             */
            if ($features = $query->all()) {
                foreach ($features as $feature) {
                    $this->_featureInit($feature);
                }
            }
        } else {
            return false;
        }



        $activeQuery->joinWith(['v3toysProductProperty as v3property']);
        $activeQuery->select(['v3property.v3toys_id as id']);
        $activeQuery->orderBy = [];
        $activeQuery->groupBy = [];
        $activeQuery->with = [];

        $this->ids_query = $activeQuery->asArray()->all();
        if ($this->ids_query) {
            $this->ids_query = ArrayHelper::map($this->ids_query, 'id', 'id');
        }

        return $this;
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

    protected $ft_soptions = [];

    /**
     * @param $feature_id
     * @return array
     */
    public function getOptions($feature_id)
    {
        $this->_ft_soptions_data = V3pFtSoption::find()->joinWith('featureValues as fv')
            ->andWhere(['fv.feature_id' => $feature_id])
            ->andWhere(["fv.product_id" => $this->ids_query])
            ->all();

        if ($this->_ft_soptions_data) {
            return ArrayHelper::map(
                $this->_ft_soptions_data,
                'id',
                'title'
            );
        }

        return [];
    }

    public function getSelected() {
        $result = [];

        if ($this->toArray()) {
            foreach ($this->toArray() as $key => $value) {
                $feature = $this->getFeatureByCode($key);
                if ($feature && $this->{$key}) {

                    $options = $this->getOptions($feature->id);

                    if (is_array($this->{$key})) {
                        foreach ($this->{$key} as $id) {
                            if (in_array($feature->value_type, ['any_soption', 'leaf_soption'])) {
                                $result[$id] = ArrayHelper::getValue($options, $id, $id);
                            }

                            if (in_array($feature->value_type, ['int', 'int_range', 'num', 'num_range'])) {

                                $from = $this->getAttributeNameRangeFrom($feature->id);
                                $to = $this->getAttributeNameRangeFrom($feature->id);

                                $valueFrom = $this->{$from};
                                $valueTo = $this->{$to};

                                $result[$id] = "от {$valueFrom} до {$valueTo} " . $feature->measure_title;
                            }

                        }

                    }

                }

            }
        }

        return $result;
    }

    public function getMaxValue($code)
    {
        $value = 0;

        $feature = $this->getFeatureByCode($code);

        if (in_array($feature->value_type, ['int', 'int_range'])) {

            $valueFromDb = V3pProductFeatureValue::find()
                ->select(["max(ft_int_value) as value, max(ft_int_value2) as value2"])
                ->andWhere(['feature_id' => $feature->id])
                ->andWhere(["product_id" => $this->ids_query])
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
                ->andWhere(["product_id" => $this->ids_query])
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
                ->andWhere(["product_id" => $this->ids_query])
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
                ->andWhere(["product_id" => $this->ids_query])
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
                if ($feature && $this->{$key}) {
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
