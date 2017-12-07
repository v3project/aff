<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 07.12.2017
 */

namespace v3p\aff\savedFilters;

use skeeks\cms\models\CmsContent;
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab;
use skeeks\cms\relatedProperties\propertyTypes\PropertyTypeElement;
use skeeks\cms\relatedProperties\propertyTypes\PropertyTypeList;
use skeeks\cms\shop\models\ShopCmsContentElement;
use skeeks\cms\shop\models\ShopTypePrice;
use skeeks\modules\cms\money\components\money\Money;
use v3p\aff\models\V3pFeature;
use v3p\aff\models\V3pFtSoption;
use v3p\aff\models\V3pProductFeatureValue;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * Class V3pSavedFiltersHandler
 * @package aff\aff\savedFilters
 */
class V3pSavedFiltersHandler extends \skeeks\cms\savedFilters\SavedFiltersHandler
{
    public $base_brand_id;
    public $base_category_id;
    public $per_page;
    public $page;
    public $sorting;
    public $sorting_direction;

    public function init()
    {
        parent::init();

        $this->name = 'Фильтрация товаров V3Project';
    }

    public function rules()
    {
        return [
            ['base_brand_id', 'integer'],
            ['base_category_id', 'integer'],
            ['per_page', 'integer'],
            ['page', 'integer'],
            ['sorting', 'string'],
            ['sorting_direction', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'base_brand_id' => 'Базовый бренд',
            'base_category_id' => 'Базовая категория',
            'per_page' => 'Количество на странице',
            'page' => 'Отображаемая страница',
            'sorting' => 'Сортировка',
            'sorting_direction' => 'Направление сортировки',
        ];
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
        echo $form->field($this, 'base_category_id')->listBox(
            ArrayHelper::merge(['' => ' --- '],ArrayHelper::map(
                V3pFtSoption::find()->where(['feature_id' => V3pFeature::ID_CATEGORY])->all(),
                'id',
                'title'
            )), ['size' => 1]
        );

        echo $form->field($this, 'base_brand_id')->listBox(
            ArrayHelper::merge(['' => ' --- '],ArrayHelper::map(
                V3pFtSoption::find()->where(['feature_id' => V3pFeature::ID_BRAND])->all(),
                'id',
                'title'
            )), ['size' => 1]
        );

        echo $form->field($this, 'per_page')->textInput();
        echo $form->field($this, 'page')->textInput();
        echo $form->field($this, 'sorting')->textInput();
        echo $form->field($this, 'sorting_direction')->textInput();
    }



    /**
     * @param ActiveQuery $activeQuery
     * @return $this
     * @throws Exception
     */
    public function filterElementsQuery(ActiveQuery $activeQuery) {

        $unionQueries = [];
        if ($this->base_category_id) {

            /**
             * @var V3pFtSoption $ft_soption
             */
            $ft_soption = V3pFtSoption::find()->where(['id' => $this->base_category_id])->one();
            if (!$ft_soption) {
                throw new Exception('!!!');
            }

            $ft_soption_query = V3pFtSoption::find()
                ->select(['id'])
                ->where(['feature_id' => 1])
                ->andWhere([
                    '>=', 'lft', $ft_soption->lft
                ])
                ->andWhere([
                    '<=', 'rgt', $ft_soption->rgt
                ]);

            $unionQueries[] = V3pProductFeatureValue::find()->select(['product_id as id'])->where([
                'feature_id' => 1,
                'ft_soption_id' => $ft_soption_query,
            ]);
        } elseif ($this->base_brand_id) {


            /**
             * @var V3pFtSoption $ft_soption
             */
            $ft_soption = V3pFtSoption::find()->where(['id' => $this->base_brand_id])->one();
            if (!$ft_soption) {
                throw new Exception('!!!');
            }

            $ft_soption_query = V3pFtSoption::find()
                ->select(['id'])
                ->where(['feature_id' => 2])
                ->andWhere([
                    '>=', 'lft', $ft_soption->lft
                ])
                ->andWhere([
                    '<=', 'rgt', $ft_soption->rgt
                ]);

            $unionQueries[] = V3pProductFeatureValue::find()->select(['product_id as id'])->where([
                'feature_id' => 2,
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
            $activeQuery->joinWith(['v3toysProductProperty as v3property']);
            $activeQuery->andWhere(['in', "v3property.v3toys_id", $unionQuery]);
        }

        return $this;
    }
}
