<?php

namespace Brezgalov\WorkersManagerAMQ;

use Yii;

/**
 * This is the model class for table "workers_configs".
 *
 * @property int $id
 * @property string $queue_component_name
 * @property int $workers_count
 */
class WorkersConfigs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'workers_configs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['queue_component_name', 'workers_count'], 'required'],
            [['workers_count'], 'integer'],
            [['queue_component_name'], 'string', 'max' => 255],
            [['queue_component_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'queue_component_name' => 'Queue Component Name',
            'workers_count' => 'Workers Count',
        ];
    }
}
