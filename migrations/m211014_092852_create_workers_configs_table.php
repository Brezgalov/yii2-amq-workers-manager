<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%workers_configs}}`.
 */
class m211014_092852_create_workers_configs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%workers_configs}}', [
            'id' => $this->primaryKey(),
            'queue_component_name' => $this->string()->notNull()->unique(),
            'workers_count' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%workers_configs}}');
    }
}
