<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.08.2015
 */

use yii\db\Schema;
use yii\db\Migration;

class m171204_160601_create_table__v3p_concept extends Migration
{
    public function safeUp()
    {
        $tableName = 'v3p_concept';

        $tableExist = $this->db->getTableSchema($tableName, true);
        if ($tableExist) {
            return true;
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable($tableName, [
            'id' => $this->primaryKey(),

            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),

            'title' => $this->string(255),

            'meta_title' => $this->string(255),
            'meta_keywords' => $this->string(255),
            'meta_description' => $this->string(255),

            'keywords' => $this->string(255),

            'queries' => $this->text(),
            'description' => $this->text(),

            'base_brand_id' => $this->integer(),
            'base_category_id' => $this->integer(),

            'per_page' => $this->integer(),
            'page' => $this->integer(),

            'sorting' => $this->string(255),
            'sorting_direction' => $this->string(255),

            'slug' => $this->string(255),

            'saved_filter_id' => $this->integer(),

            'state' => $this->string(255),
            'filter_values_jsonarrayed' => $this->text(),

        ], $tableOptions);


        $this->createIndex($tableName . '__created_at', $tableName, 'created_at');
        $this->createIndex($tableName . '__updated_at', $tableName, 'updated_at');

        $this->createIndex($tableName . '__title', $tableName, 'title');
        $this->createIndex($tableName . '__base_brand_id', $tableName, 'base_brand_id');
        $this->createIndex($tableName . '__base_category_id', $tableName, 'base_category_id');

        $this->createIndex($tableName . '__saved_filter_id', $tableName, 'saved_filter_id', true);

        $this->addForeignKey(
            "{$tableName}__saved_filter_id", $tableName,
            'saved_filter_id', '{{%saved_filters}}', 'id', 'SET NULL', 'SET NULL'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey("{$tableName}__saved_filter_id", $tableName);

        $this->dropTable($tableName);
    }
}