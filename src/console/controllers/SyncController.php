<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 04.12.2017
 */

namespace v3p\aff\console\controllers;

use yii\console\Controller;

/**
 * Синхронизация данных
 *
 * Class SyncController
 * @package v3p\aff\console\controllers
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
     */
    public function actionFeatures()
    {

    }

    /**
     * Синхронизация значений характеристик
     */
    public function actionFeatureValues()
    {

    }
}