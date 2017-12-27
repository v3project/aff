<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 07.12.2017
 */

namespace v3p\aff\savedFilters;

use common\widgets\filters\V3pPerPageFiltersHandler;
use skeeks\cms\models\CmsContent;
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab;
use skeeks\cms\relatedProperties\propertyTypes\PropertyTypeElement;
use skeeks\cms\relatedProperties\propertyTypes\PropertyTypeList;
use skeeks\cms\shop\models\ShopCmsContentElement;
use skeeks\cms\shop\models\ShopTypePrice;
use skeeks\modules\cms\money\components\money\Money;
use v3p\aff\models\V3pConcept;
use v3p\aff\models\V3pFeature;
use v3p\aff\models\V3pFtSoption;
use v3p\aff\models\V3pProductFeatureValue;
use v3p\aff\widgets\filter\V3pFeatureValueHandler;
use v3p\aff\widgets\filter\V3pProductFiterWidget;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * @property V3pConcept $v3pConcept
 *
 * Class V3pConceptSavedFiltersHandler
 * @package v3p\aff\savedFilters
 */
class V3pConceptSavedFiltersHandler extends \skeeks\cms\savedFilters\SavedFiltersHandler
{
    public $v3p_concept_id;

    public function init()
    {
        parent::init();

        $this->name = 'Концепт V3Project';
    }

    public function rules()
    {
        return [
            ['v3p_concept_id', 'integer'],
            ['v3p_concept_id', 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'v3p_concept_id' => 'Концепт',
        ];
    }

    /**
     * @return V3pConcept
     */
    public function getV3pConcept()
    {
        return V3pConcept::findOne($this->v3p_concept_id);
    }

    /*public function load($data, $formName = null)
    {
        if (isset($data['RelatedPropertiesModel'])) {
            $this->filters = $data['RelatedPropertiesModel'];
        }

        return parent::load($data, $formName);
    }*/

    /**
     * @param ActiveFormUseTab $form
     */
    public function renderConfigForm(ActiveForm $form)
    {
        echo "Вы не можете создать этот концепт";
    }


    /**
     * @param ActiveQuery $activeQuery
     * @return $this
     * @throws Exception
     */
    public function filterElementsQuery(ActiveQuery $activeQuery)
    {

        $unionQueries = [];
        $v3pConcept = $this->v3pConcept;

        if ($v3pConcept->base_category_id) {

            /**
             * @var V3pFtSoption $ft_soption
             */
            $ft_soption = V3pFtSoption::find()->where(['id' => $v3pConcept->base_category_id])->one();
            if (!$ft_soption) {
                throw new Exception('!!!');
            }

            $ft_soption_query = V3pFtSoption::find()
                ->select(['id'])
                ->where(['feature_id' => V3pFeature::ID_CATEGORY])
                ->andWhere([
                    '>=',
                    'lft',
                    $ft_soption->lft
                ])
                ->andWhere([
                    '<=',
                    'rgt',
                    $ft_soption->rgt
                ]);

            $unionQueries[] = V3pProductFeatureValue::find()->select(['product_id as id'])->where([
                'feature_id' => V3pFeature::ID_CATEGORY,
                'ft_soption_id' => $ft_soption_query,
            ]);
        } elseif ($v3pConcept->base_brand_id) {


            /**
             * @var V3pFtSoption $ft_soption
             */
            $ft_soption = V3pFtSoption::find()->where(['id' => $v3pConcept->base_brand_id])->one();
            if (!$ft_soption) {
                throw new Exception('!!!');
            }

            $ft_soption_query = V3pFtSoption::find()
                ->select(['id'])
                ->where(['feature_id' => V3pFeature::ID_BRAND])
                ->andWhere([
                    '>=',
                    'lft',
                    $ft_soption->lft
                ])
                ->andWhere([
                    '<=',
                    'rgt',
                    $ft_soption->rgt
                ]);

            $unionQueries[] = V3pProductFeatureValue::find()->select(['product_id as id'])->where([
                'feature_id' => V3pFeature::ID_BRAND,
                'ft_soption_id' => $ft_soption_query,
            ]);
        }


        /**
         * @var $unionQuery ActiveQuery
         */
        $unionQuery = null;

        if ($unionQueries) {

            $lastQuery = null;
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

                //$unionQueriesStings[] = $query->createCommand()->rawSql;
            }
        }

        if ($unionQuery) {
            $activeQuery->joinWith(['v3toysProductProperty']);
            $activeQuery->andWhere(['in', "v3toysProductProperty.v3toys_id", $unionQuery]);
        }

        return $this;
    }


    public function appendBreadcrumbs()
    {
        if ($this->v3pConcept) {
            $this->v3pConcept->appendBreadcrumbs();
        }

        return $this;
    }

    public function loadToPerPageFilterHandler(V3pPerPageFiltersHandler $perPageFiltersHandler)
    {
        $this->v3pConcept->per_page;
        if ($sOptions = $perPageFiltersHandler->sOptions) {
            if (!isset($sOptions[$this->v3pConcept->per_page]))
            {
                $sOptions = ArrayHelper::merge([(string) $this->v3pConcept->per_page => (int) $this->v3pConcept->per_page], $sOptions);
            }
        } else {
            $sOptions = [(string) $this->v3pConcept->per_page => (int) $this->v3pConcept->per_page];
        }

        $perPageFiltersHandler->setSOptions($sOptions);
        $perPageFiltersHandler->value = $this->v3pConcept->per_page;

        return $this;
    }

    /**
     * @param V3pFeatureValueHandler $v3pFeatureValueHandler
     * @return $this
     */
    public function loadToV3pFilterHandler(V3pFeatureValueHandler $v3pFeatureValueHandler) {
        $v3pFeatureValueHandler->base_brand_id = $this->v3pConcept->base_brand_id;
        $v3pFeatureValueHandler->base_category_id = $this->v3pConcept->base_category_id;

        if ($this->v3pConcept && $this->v3pConcept->filter_values) {
            foreach ($this->v3pConcept->filter_values as $row) {
                $featureId = ArrayHelper::getValue($row, 'feature_id');
                $attribute = $v3pFeatureValueHandler->getAttributeName($featureId);

                if (in_array(ArrayHelper::getValue($row, 'feature_value_type'), [V3pFeature::VALUE_TYPE_ANY_SOPTION, V3pFeature::VALUE_TYPE_LEAF_SOPTION])) {
                    /*print_r($attribute);
                    print_r($row);
                    die;*/
                    if (isset($v3pFeatureValueHandler->{$attribute})) {
                        if ($v3pFeatureValueHandler->{$attribute}) {
                            $v3pFeatureValueHandler->{$attribute} = ArrayHelper::merge($v3pFeatureValueHandler->{$attribute}, [ArrayHelper::getValue($row, 'ft_soption_id')]);
                        } else {
                            $v3pFeatureValueHandler->{$attribute} = [ArrayHelper::getValue($row, 'ft_soption_id')];
                        }

                    } else
                    {
                        //TODO: варнинг
                    }

                }
            }
        }

        return $this;
    }

}
