<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m200219_111652_coupons
 */
class m200219_111652_coupons extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('coupons', [
            'id' => Schema::TYPE_PK,
            'img' => Schema::TYPE_STRING,
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'text' => Schema::TYPE_TEXT . ' NOT NULL',
            'date' => Schema::TYPE_DATE . ' NOT NULL',
            'site' => Schema::TYPE_STRING . ' NOT NULL',
            'actual' => Schema::TYPE_BOOLEAN . ' NOT NULL',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('coupons');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200219_111652_coupons cannot be reverted.\n";

        return false;
    }
    */
}
