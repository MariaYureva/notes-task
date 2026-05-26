<?php

use yii\db\Migration;

/**
 * Создаёт таблицу `notes` для хранения заметок пользователей.
 *
 * Связи:
 * - `user_id` → `user.id` (ON DELETE CASCADE)
 */
class m260526_120000_create_notes_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%notes}}', [
            'id'         => $this->primaryKey(),
            'user_id'    => $this->integer()->notNull(),
            'title'      => $this->string(255)->notNull(),
            'content'    => $this->text(),
            'color'      => $this->string(7)->notNull()->defaultValue('#6366f1'),
            'is_pinned'  => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-notes-user_id',
            '{{%notes}}',
            'user_id'
        );

        $this->addForeignKey(
            'fk-notes-user_id',
            '{{%notes}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-notes-user_id', '{{%notes}}');
        $this->dropIndex('idx-notes-user_id', '{{%notes}}');
        $this->dropTable('{{%notes}}');
    }
}
