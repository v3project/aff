<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.08.2015
 */

use yii\db\Schema;
use yii\db\Migration;

class m171204_120601_create_table__v3p_feature extends Migration
{
    public function safeUp()
    {
        $tableName = 'v3p_feature';
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

            'title' => $this->string(255)->notNull(),
            'value_type' => $this->string(255)->notNull(),

            'priority' => $this->integer()->notNull(),
            'buyer_description' => $this->string(255),

            'type' => $this->string(255)->notNull(),
            'measure_title' => $this->string(255),
            'min_value' => $this->integer(),
            'max_value' => $this->integer(),
            'min_choosen_soption_depth' => $this->integer(),
            'max_choosen_soption_depth' => $this->integer(),

            'is_disabled' => $this->integer(1)->notNull(),
            'bool_type' => $this->string(255),

        ], $tableOptions);


        $this->createIndex($tableName . '__created_at', $tableName, 'created_at');
        $this->createIndex($tableName . '__updated_at', $tableName, 'updated_at');

        $this->createIndex($tableName . '__title', $tableName, 'title');
        $this->createIndex($tableName . '__value_type', $tableName, 'value_type');
        $this->createIndex($tableName . '__priority', $tableName, 'priority');
        $this->createIndex($tableName . '__type', $tableName, 'type');
        $this->createIndex($tableName . '__is_disabled', $tableName, 'is_disabled');
    }

    public function safeDown()
    {
        $this->dropTable($tableName);
    }
}