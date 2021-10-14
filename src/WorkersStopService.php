<?php

namespace Brezgalov\WorkersManagerAMQ;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\queue\amqp_interop\Queue;

class WorkersStopService extends Model
{
    /**
     * @var WorkersCheckService
     */
    public $workersCheckService;

    /**
     * WorkersStopService constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        if (empty($this->workersCheckService)) {
            $this->workersCheckService = new WorkersCheckService();
        }
    }

    /**
     * @param WorkersConfigs $config
     * @param $count
     * @return bool
     */
    public function stopWorkerByConfig(WorkersConfigs $config, $count)
    {
        if (!\Yii::$app->has($config->queue_component_name)) {
            throw new InvalidConfigException("Component {$config->queue_component_name} not found");
        }

        /** @var Queue $queue */
        $queue = \Yii::$app->get($config->queue_component_name);

        if (!($queue instanceof Queue)) {
            throw new InvalidConfigException('Yii comp. named ' . $config->queue_component_name . ' is not ' . Queue::class);
        }

        /** @var WorkersStatuses $workers */
        $workers = WorkersStatuses::find()
            ->andWhere(['is_busy' => 0]) // удаляем только воркеры которые не заняты
            ->andWhere(['queue_name' => $queue->queueName])
            ->andWhere(['status' => WorkersStatuses::STATUS_ACTIVE])
            ->orderBy(['id' => SORT_ASC])
            ->limit($count)
            ->all();

        foreach ($workers as $worker) {
            if (!$this->stopWorker($worker)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param WorkersStatuses $worker
     * @return bool
     */
    public function stopWorker(WorkersStatuses $worker)
    {
        exec("kill {$worker->pid}");
        sleep(1); // wait for kill to manage stuff after command finished

        $worker = $this->workersCheckService->updateStatus($worker);
        if (!$worker->save()) {
//            ErrorHelper::logError('Worker update on stop error', $worker);
        }

        return true;
    }
}