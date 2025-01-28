<?php

use yii\db\Migration;

/**
 * Class m250128_160511_create_table_news
 */
class m250128_160511_create_table_news extends Migration
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

        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'slug' => $this->string(),
            'image' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'text' => $this->text(),
        ], $tableOptions);

        $this->createIndex('idx-news-title', '{{%news}}', 'title');
        $this->createIndex('idx-news-slug', '{{%news}}', 'slug');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-news-title', '{{%news}}');
        $this->dropIndex('idx-news-slug', '{{%news}}');
        $this->dropTable('{{%news}}');
    }

}
