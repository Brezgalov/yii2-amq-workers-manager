<?php

namespace Brezgalov\WorkersManagerAMQ;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "workers_statuses".
 *
 * @property int $id
 * @property string $status
 * @property int $is_busy
 * @property int $pid
 * @property string $queue_name
 * @property string|null $exchange_name
 * @property string $host
 * @property string $port
 * @property string|null $user
 * @property string|null $created_at
 * @property string|null $checked_at
 */
class WorkersStatuses extends \yii\db\ActiveRecord
{
    const PID_ENV = 'WORKERS_PID';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'workers_statuses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'pid', 'queue_name'], 'required'],
            [['pid', 'port', 'is_busy'], 'integer'],
            [['status', 'queue_name', 'exchange_name', 'host', 'user', 'created_at'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => function() {
                    return date('Y-m-d H:i:s');
                }
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'pid' => 'Pid',
            'queue_name' => 'Queue Name',
            'exchange_name' => 'Exchange Name',
            'host' => 'Host',
            'port' => 'Port',
            'user' => 'User',
            'created_at' => 'Created At',
        ];
    }
}
