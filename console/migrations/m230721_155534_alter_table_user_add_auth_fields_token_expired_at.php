<?php

use yii\db\Migration;

/**
 * Class m230721_155534_alter_table_user_add_auth_fields_token_expired_at
 */
class m230721_155534_alter_table_user_add_auth_fields_token_expired_at extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'token', $this->string(255));
        $this->addColumn('{{%user}}', 'expired_at', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'token');
        $this->dropColumn('{{%user}}', 'expired_at');
    }

}
