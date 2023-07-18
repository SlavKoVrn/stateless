<?php

use yii\db\Migration;

/**
 * Class m230718_061750_create_table_product_category_tag
 */
class m230718_061750_create_table_product_category_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer(),
            'price' => $this->integer(),
            'name' => $this->string(),
            'slug' => $this->string(),
            'description' => $this->text(),
        ], $tableOptions);

        $this->createIndex('idx-product-category_id', '{{%product}}', 'category_id');
        $this->createIndex('idx-product-name', '{{%product}}', 'name');
        $this->createIndex('idx-product-slug', '{{%product}}', 'slug');
        $this->createIndex('idx-product-price', '{{%product}}', 'price');

        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'slug' => $this->string(),
            'description' => $this->text(),
        ], $tableOptions);

        $this->createIndex('idx-category-name', '{{%category}}', 'name');
        $this->createIndex('idx-category-slug', '{{%category}}', 'slug');

        $this->createTable('{{%tag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ], $tableOptions);

        $this->createTable('{{%product_tag}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(),
            'tag_id' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx-product_tag-name', '{{%category}}', 'name');
        $this->createIndex('idx-category_slug-slug', '{{%category}}', 'slug');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product}}');
        $this->dropTable('{{%category}}');
        $this->dropTable('{{%tag}}');
        $this->dropTable('{{%product_tag}}');

    }

}
