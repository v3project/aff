<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.08.2015
 */

use yii\db\Schema;
use yii\db\Migration;

class m171204_130601_create_table__v3p_ft_soption extends Migration
{
    public function safeUp()
    {
        $tableName = 'v3p_ft_soption';

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

            'feature_id' => $this->integer()->notNull(),
            'feature_value_type' => $this->string(255)->notNull(),

            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'depth' => $this->integer()->notNull(),

            'parent_id' => $this->integer(),

            'is_disabled' => $this->integer(1)->notNull(),

            'feature_type' => $this->string(255),
            'feature_title' => $this->string(255),

            'feature_min_choosen_soption_depth' => $this->integer(),
            'feature_max_choosen_soption_depth' => $this->integer(),

            'brand_owner_country' => $this->string(255),

        ], $tableOptions);


        $this->createIndex($tableName . '__created_at', $tableName, 'created_at');
        $this->createIndex($tableName . '__updated_at', $tableName, 'updated_at');

        $this->createIndex($tableName . '__title', $tableName, 'title');
        $this->createIndex($tableName . '__feature_id', $tableName, 'feature_id');

        $this->createIndex($tableName . '__feature_value_type', $tableName, 'feature_value_type');

        $this->createIndex($tableName . '__parent_id', $tableName, 'parent_id');

        $this->createIndex($tableName . '__is_disabled', $tableName, 'is_disabled');

        $this->createIndex($tableName . '__tree', $tableName, ['lft', 'rgt', 'depth']);

        /*$this->addForeignKey(
            "{$tableName}__feature_id", $tableName,
            'feature_id', '{{%v3p_feature}}', 'id', 'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            "{$tableName}__parent_id", $tableName,
            'parent_id', '{{%v3p_ft_soption}}', 'id', 'CASCADE', 'CASCADE'
        );*/
    }

    public function safeDown()
    {
        $this->dropForeignKey("{$tableName}__feature_id", $tableName);
        $this->dropForeignKey("{$tableName}__parent_id", $tableName);

        $this->dropTable($tableName);
    }
}