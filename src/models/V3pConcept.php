<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 04.12.2017
 */

namespace v3p\aff\models;

use skeeks\cms\savedFilters\models\SavedFilters;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "v3p_concept".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $title
 * @property string $meta_title
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $keywords
 * @property string $queries
 * @property string $description
 * @property integer $base_brand_id
 * @property integer $base_category_id
 * @property integer $per_page
 * @property integer $page
 * @property string $sorting
 * @property string $sorting_direction
 * @property string $slug
 * @property string $state
 * @property string $published_state
 * @property string $filter_values_jsonarrayed
 * @property integer $saved_filter_id
 *
 * ***
 *
 * @property array $filter_values
 * @property SavedFilters $savedFilter
 * @property V3pFtSoption $baseCategory
 * @property V3pFtSoption $baseBrand
 */
class V3pConcept extends ActiveRecord
{
    const PUBLISHED_STATE_ОПУБЛИКОВАН                = "опубликован";
    const PUBLISHED_STATE_НЕ_ОПУБЛИКОВАН                = "не_опубликован";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'v3p_concept';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['published_state'], 'string'],
            [['queries', 'description', 'filter_values_jsonarrayed'], 'string'],
            [['id', 'base_brand_id', 'base_category_id', 'per_page', 'page', 'saved_filter_id'], 'integer'],
            [['title', 'meta_title', 'meta_keywords', 'meta_description', 'keywords', 'sorting', 'sorting_direction', 'slug', 'state'], 'string', 'max' => 255],
            [['saved_filter_id'], 'unique'],
            [['saved_filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => SavedFilters::className(), 'targetAttribute' => ['saved_filter_id' => 'id']],
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
            'meta_title' => 'Meta Title',
            'meta_keywords' => 'Meta Keywords',
            'meta_description' => 'Meta Description',
            'keywords' => 'Keywords',
            'queries' => 'Queries',
            'description' => 'Description',
            'base_brand_id' => 'Base Brand ID',
            'base_category_id' => 'Base Category ID',
            'per_page' => 'Per Page',
            'page' => 'Page',
            'sorting' => 'Sorting',
            'sorting_direction' => 'Sorting Direction',
            'slug' => 'Slug',
            'saved_filter_id' => 'Saved Filter ID',
            'filter_values_jsonarrayed' => 'Saved Filter ID',
            'state' => 'Saved Filter ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSavedFilter()
    {
        return $this->hasOne(SavedFilters::className(), ['id' => 'saved_filter_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBaseBrand() {
        return $this->hasOne(V3pFtSoption::class, ['id' => 'base_brand_id']);
    }
    /**
     * @return ActiveQuery
     */
    public function getBaseCategory() {
        return $this->hasOne(V3pFtSoption::class, ['id' => 'base_category_id']);
    }

    /**
     * @return array
     */
    public function getFilter_values() {
        if ($this->filter_values_jsonarrayed) {
            return (array) Json::decode($this->filter_values_jsonarrayed);
        }

        return [];
    }



    public function appendBreadcrumbs()
    {
        if (!$this->savedFilter) {
            return $this;
        }

        if ($tree = $this->savedFilter->cmsTree) {
            \Yii::$app->breadcrumbs->setPartsByTree($tree);
            return $this;
        }
        //Выбраны значения и это не базовый концепт
        if ($this->base_category_id) {
            //Поиск базового концепта
            $v3pConcept = V3pConcept::find()->where(['base_category_id' => $this->base_category_id])->andWhere(['filter_values_jsonarrayed' => null])->one();
            if ($v3pConcept)
            {
                $v3pConcept->appendBreadcrumbs();
            } else {
                if ($this->baseCategory) {

                    $parents = $this->baseCategory->parents;

                    foreach ($parents as $tree) {
                        \Yii::$app->breadcrumbs->append([
                            'name' => $tree->title,
                        ]);
                    }
                }
            }
        } else if ($this->base_brand_id) {
            //Поиск базового концепта
            $v3pConcept = V3pConcept::find()->where(['base_brand_id' => $this->base_brand_id])->andWhere(['filter_values_jsonarrayed' => null])->one();
            if ($v3pConcept)
            {
                $v3pConcept->appendBreadcrumbs();
            } else {
                if ($this->baseBrand) {

                    $parents = $this->baseBrand->parents;

                    foreach ($parents as $tree) {
                        \Yii::$app->breadcrumbs->append([
                            'name' => $tree->title,
                        ]);
                    }
                }
            }
        } else {
            if ($this->baseCategory) {

                $parents = $this->baseCategory->parents;

                foreach ($parents as $tree) {
                    \Yii::$app->breadcrumbs->append([
                        'name' => $tree->title,
                    ]);
                }
            } elseif ($this->baseBrand) {
                $parents = $this->baseBrand->parents;
                
                foreach ($parents as $tree) {
                    \Yii::$app->breadcrumbs->append([
                        'name' => $tree->title,
                    ]);
                }
            }
        }

        \Yii::$app->breadcrumbs->append([
            'name' => $this->title,
            'url' => $this->url,
        ]);


        return $this;
    }

    public function getUrl() {
        if ($this->savedFilter) {
            return $this->savedFilter->url;
        }

        return '';
    }
}