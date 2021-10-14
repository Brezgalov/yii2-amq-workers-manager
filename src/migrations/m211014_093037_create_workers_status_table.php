<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%workers_status}}`.
 */
class m211014_093037_create_workers_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%workers_statuses}}', [
            'id' => $this->primaryKey(),
            'status' => $this->string()->notNull(),
            'is_busy' => $this->tinyInteger()->notNull()->defaultValue(0),
            'pid' => $this->integer()->notNull(),
            'queue_name' => $this->string()->notNull(),
            'exchange_name' => $this->string(),
            'host' => $this->string(),
            'port' => $this->integer(),
            'user' => $this->string(),
            'created_at' => $this->string(),
        ]);

        $this->createIndex(
            'workers_statuses_IDX_status',
            '{{%workers_statuses}}',
            'status'
        );

        $this->createIndex(
            'workers_statuses_IDX_pid',
            '{{%workers_statuses}}',
            'pid'
        );

        $this->createIndex(
            'workers_statuses_IDX_queue_name',
            '{{%workers_statuses}}',
            'queue_name'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%workers_statuses}}');
    }
}
