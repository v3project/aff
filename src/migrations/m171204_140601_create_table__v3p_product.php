<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.08.2015
 */

use yii\db\Schema;
use yii\db\Migration;

class m171204_140601_create_table__v3p_product extends Migration
{
    public function safeUp()
    {
        $tableName = 'v3p_product';

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

            'guiding_available_quantity' => $this->integer(),
            'guiding_available_nsk_quantity' => $this->integer(),

            'guiding_buy_price' => $this->decimal(18, 2),
            'guiding_realize_price' => $this->decimal(18, 2),
            'mr_price' => $this->decimal(18, 2),

            'stock_barcodes' => $this->string(255),

            'keywords' => $this->string(255),
            'sku' => $this->string(255),

            'astype' => $this->string(255),
            'general_ast_product_id' => $this->integer(),
            'nn_in_general_ast_product' => $this->integer(),
            'general_ast_product_sku' => $this->string(255),

            'disable_reason' => $this->string(255),
            'disable_comment' => $this->string(255),
            'duplicate_of_product_id' => $this->integer(),

            'is_disabled' => $this->integer(),

        ], $tableOptions);


        $this->createIndex($tableName . '__created_at', $tableName, 'created_at');
        $this->createIndex($tableName . '__updated_at', $tableName, 'updated_at');

        $this->createIndex($tableName . '__sku', $tableName, 'sku');
        $this->createIndex($tableName . '__guiding_available_quantity', $tableName, 'guiding_available_quantity');
        $this->createIndex($tableName . '__guiding_available_nsk_quantity', $tableName, 'guiding_available_nsk_quantity');

        $this->createIndex($tableName . '__guiding_buy_price', $tableName, 'guiding_buy_price');
        $this->createIndex($tableName . '__guiding_realize_price', $tableName, 'guiding_realize_price');
        $this->createIndex($tableName . '__mr_price', $tableName, 'mr_price');

        $this->createIndex($tableName . '__astype_full', $tableName, ['astype', 'general_ast_product_id', 'nn_in_general_ast_product', 'general_ast_product_sku']);

        $this->createIndex($tableName . '__is_disabled', $tableName, 'is_disabled');

    }

    public function safeDown()
    {
        $this->dropForeignKey("{$tableName}__id", $tableName);
        $this->dropTable($tableName);
    }
}