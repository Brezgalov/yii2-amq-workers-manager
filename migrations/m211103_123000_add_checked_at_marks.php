<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%workers_status}}`.
 */
class m211103_123000_add_checked_at_marks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%workers_configs}}', 'tested_at', $this->dateTime());

        $this->addColumn('{{%workers_statuses}}', 'checked_at', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%workers_configs}}', 'tested_at');

        $this->dropColumn('{{%workers_statuses}}', 'checked_at');
    }
}
