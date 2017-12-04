<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 04.12.2017
 */

namespace v3p\aff\console\controllers;

use v3p\aff\models\V3pContentElement;
use v3p\aff\models\V3pFeature;
use v3p\aff\models\V3pFeatureValue;
use v3p\aff\models\V3pFtSoption;
use v3p\aff\models\V3pProduct;
use v3p\aff\models\V3pProductFeatureValue;
use yii\console\Controller;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Синхронизация данных
 *
 */
class SyncController extends Controller
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
    public function actionFeatures($isAll = 0)
    {
        $this->stdout("Синхронизация характеристик\n", Console::BOLD);
        $this->_syncTable('feature', V3pFeature::class, $isAll);
    }

    /**
     * Синхронизация съопций
     * Учитывается время последнего обновления
     */
    public function actionFtSoptions($isAll = 0)
    {
        $this->stdout("Синхронизация съопций\n", Console::BOLD);
        $this->_syncTable('ft_soption', V3pFtSoption::class, $isAll);
    }


    /**
     * Синхронизация характеристик
     * Учитывается время последнего обновления
     */
    public function actionProductFeatureValues($isAll = 0)
    {
        $this->stdout("Синхронизация значений характеристик\n", Console::BOLD);
        $this->_syncTable('product_feature_value', V3pProductFeatureValue::class, $isAll);
    }
    /**
     * Синхронизация характеристик
     * Учитывается время последнего обновления
     */
    public function actionProducts($isAll = 0)
    {
        $this->stdout("Синхронизация товаров\n", Console::BOLD);
        $this->_syncTable('product', V3pProduct::class, $isAll);
    }


    protected function _syncTable($apiTable, $appModelClassName, $isAll = 0){

        $query = (new \yii\db\Query())
            ->from('apiv5.' . $apiTable);


        $last = $appModelClassName::find()
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit(1)
            ->one();

        if ($last && !$isAll) {
            $date = \Yii::$app->formatter->asDatetime(date($last->updated_at));
            $this->stdout("\tВремя обновления последней записи: {$date}\n");
            $query->andWhere(['>=', 'updated_at', $last->updated_at]);


        }

        $count = $query->count("*", \Yii::$app->dbV3project);
        $this->stdout("\tВсего: {$count}\n");

        if (!$count) {
            return;
        }

        foreach ($query->orderBy(['updated_at' => SORT_ASC])->each(1000, \Yii::$app->dbV3project) as $row) {

            $this->stdout("\tЭлемент: {$row['id']}");
            if ($model = $appModelClassName::find()->andWhere(['id' => $row['id']])->one()) {
                $this->stdout(" - обновление - ", Console::FG_YELLOW);

                $model->setAttributes($row);
                if ($model->save()) {
                    $this->stdout(" - обновлена!\n", Console::FG_GREEN);
                } else {
                    $this->stdout(" - не обновлена - " . print_r($model->errors, true) . " \n", Console::FG_RED);
                    die;
                }

            } else {
                $this->stdout(" - добавление - ");
                $model = new $appModelClassName();
                $model->setAttributes($row);
                if ($model->save()) {
                    $this->stdout(" - добавлена!\n", Console::FG_GREEN);
                } else {
                    $this->stdout(" - не добавлена - " . print_r($model->errors, true) . " \n", Console::FG_RED);
                    die;
                }
            }
        }

    }
}