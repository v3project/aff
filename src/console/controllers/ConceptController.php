<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 04.12.2017
 */

namespace v3p\aff\console\controllers;

use skeeks\cms\savedFilters\models\SavedFilters;
use v3p\aff\models\V3pConcept;
use v3p\aff\models\V3pContentElement;
use v3p\aff\models\V3pFeature;
use v3p\aff\models\V3pFeatureValue;
use v3p\aff\models\V3pFtSoption;
use v3p\aff\models\V3pProduct;
use v3p\aff\models\V3pProductFeatureValue;
use v3p\aff\savedFilters\V3pConceptSavedFiltersHandler;
use yii\console\Controller;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Class ConceptController
 * @package v3p\aff\console\controllers
 */
class ConceptController extends Controller
{
    public function init()
    {
        parent::init();

        ini_set("memory_limit", "8192M");
        set_time_limit(0);
    }

    /**
     * Синхронизация характеристик
     * Учитывается время последнего обновления
     */
    public function actionCreateSavedFilters($isAll = 0)
    {
        $this->stdout("Создание сохраненных фильтров из конццетов\n", Console::BOLD);

        $query = V3pConcept::find()
            //->where(['saved_filter_id' => null])
        ;
        $this->stdout("Кол-во концептов: {$query->count()}\n", Console::BOLD);

        if (!$query->count()) {
            return false;
        }

        /**
         * @var V3pConcept $concept
         */
        foreach ($query->each(100) as $concept) {
            if (!$filter = $concept->savedFilter) {
                $filter = new SavedFilters();
            }
            
            $filter->name = $concept->title;
            $filter->code = $concept->slug;
            $filter->description_full = $concept->description;
            $filter->component = V3pConceptSavedFiltersHandler::class;
            $filter->component_settings = ['v3p_concept_id' => $concept->id];
            $filter->is_active = $concept->state == 'готов' ? true : false;
            $filter->meta_title = $concept->meta_title;
            $filter->meta_description = $concept->meta_description;
            $filter->meta_keywords = $concept->meta_keywords;
            
            //TODO::Добавить транзацкияю
            if ($filter->save()) {
                $concept->saved_filter_id = $filter->id;
                $concept->save();
            }
        }
    }
}