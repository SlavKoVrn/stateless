<?php
use yii\db\Migration;

class m230728_132049_create_table_good extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%good}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'category_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'price' => $this->integer(),
            'name' => $this->string(),
            'slug' => $this->string(),
            'description' => $this->text(),
        ], $tableOptions);
        $this->createIndex('idx-good-user_id', '{{%good}}', 'user_id');
        $this->createIndex('idx-good-category_id', '{{%good}}', 'category_id');
        $this->createIndex('idx-good-name', '{{%good}}', 'name');
        $this->createIndex('idx-good-slug', '{{%good}}', 'slug');
    }
    public function safeDown()
    {
        $this->dropTable('{{%good}}');
    }
}