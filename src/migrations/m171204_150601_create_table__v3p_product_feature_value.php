<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.08.2015
 */

use yii\db\Schema;
use yii\db\Migration;

class m171204_150601_create_table__v3p_product_feature_value extends Migration
{
    public function safeUp()
    {
        $tableName = 'v3p_product_feature_value';

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

            'product_id' => $this->integer()->notNull(),
            'feature_id' => $this->integer()->notNull(),

            'feature_value_type' => $this->string(255)->notNull(),

            'ft_not_value' => $this->string(255),
            'ft_soption_id' => $this->integer(),

            'ft_string_value' => $this->text(),
            'ft_text_value' => $this->text(),

            'ft_int_value' => $this->integer(),
            'ft_int_value2' => $this->integer(),

            'ft_num_value' => $this->decimal(24,4),
            'ft_num_value2' => $this->decimal(24,4),

            'ft_json_value' => $this->text(),

            'ft_bool_value' => $this->boolean(),
            'check_is_valid' => $this->boolean(),
            'feature_type' => $this->string(255),
            'feature_min_value' => $this->integer(),
            'feature_max_value' => $this->integer(),
            'feature_min_choosen_soption_depth' => $this->integer(),
            'feature_max_choosen_soption_depth' => $this->integer(),
            'ft_soption_depth' => $this->integer(),
            'feature_priority' => $this->integer(),
            'feature_value_as_json' => $this->string(255),
            'feature_value_as_text' => $this->string(255),

        ], $tableOptions);


        $this->createIndex($tableName . '__product_id', $tableName, 'product_id');
        $this->createIndex($tableName . '__feature_id', $tableName, 'feature_id');

        $this->createIndex($tableName . '__feature_value_type', $tableName, 'feature_value_type');
        $this->createIndex($tableName . '__ft_soption_id', $tableName, 'ft_soption_id');

        $this->createIndex($tableName . '__full', $tableName, ['ft_int_value', 'ft_int_value2', 'ft_num_value', 'ft_num_value2', 'ft_bool_value', 'ft_soption_id', 'feature_value_type']);

        /*$this->addForeignKey(
            "{$tableName}__feature_id", $tableName,
            'feature_id', '{{%v3p_feature}}', 'id', 'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            "{$tableName}__product_id", $tableName,
            'product_id', '{{%v3p_product}}', 'id', 'CASCADE', 'CASCADE'
        );*/
    }

    public function safeDown()
    {
        /*$this->dropForeignKey("{$tableName}__feature_id", $tableName);
        $this->dropForeignKey("{$tableName}__product_id", $tableName);*/

        $this->dropTable($tableName);
    }
}