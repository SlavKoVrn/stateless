<?php

use yii\db\Migration;

/**
 * Class m230725_070051_alter_table_product_add_field_user_id
 */
class m230725_070051_alter_table_product_add_field_user_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'user_id', $this->integer()->after('category_id'));
        Yii::$app->db->createCommand("UPDATE product SET user_id = 1")->query();

        $this->createIndex('idx-product-user_id', '{{%product}}', 'user_id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'user_id');
    }

}
