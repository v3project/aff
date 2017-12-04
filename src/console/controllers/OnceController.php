<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 04.12.2017
 */

namespace v3p\aff\console\controllers;

use v3p\aff\models\V3pFeature;
use v3p\aff\models\V3pFtSoption;
use v3p\aff\models\V3pProduct;
use v3toys\skeeks\models\V3toysProductProperty;
use yii\console\Controller;
use yii\db\Exception;
use yii\helpers\Console;

/**
 * Разовые скрипты нормализации данных
 */
class OnceController extends Controller
{
    public function init()
    {
        parent::init();

        ini_set("memory_limit", "8192M");
        set_time_limit(0);
    }

    /**
     * Миграция данных по товарам из старого в новый контент
     */
    public function actionMigrateProducts()
    {
        $this->stdout("Миграция старых товаров в новые\n", Console::BOLD);

        $query = V3toysProductProperty::find();

        $count = $query->count("*");
        $this->stdout("\tЗаписей к обновлению: {$count}\n");

        if (!$count) {
            return;
        }

        /**
         * @var V3toysProductProperty $modelProperty
         */
        foreach ($query->orderBy(['id' => SORT_ASC])->each(100) as $modelProperty) {

            $this->stdout("\tЗапись: {$modelProperty->id}");

            try {

                if ($model = V3pProduct::find()->andWhere(['id' => $modelProperty->id])->one()) {
                    $this->stdout(" - создан\n", Console::FG_YELLOW);
                } else {
                    $this->stdout(" - добавление - ");
                    $model = new V3pProduct();
                    $model->id = $modelProperty->id;
                    $model->v3p_product_id = $modelProperty->v3toys_id;

                    if ($model->save()) {
                        $this->stdout(" - добавлена!\n", Console::FG_GREEN);
                    } else {
                        $this->stdout(" - не добавлена - " . print_r($model->errors, true) . " \n", Console::FG_RED);
                    }
                }

            } catch (\Exception $e) {
                $this->stdout(" - не добавлена - " . print_r($e->getMessage(), true) . " \n", Console::FG_RED);
            }

        }
    }
}