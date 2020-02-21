<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m200218_105632_sites
 */
class m200218_105632_sites extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('sites', [
            'id' => Schema::TYPE_PK,
            'link' => Schema::TYPE_STRING . ' NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'error' => Schema::TYPE_BOOLEAN . ' NOT NULL'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('sites');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200218_105632_sites cannot be reverted.\n";

        return false;
    }
    */
}
