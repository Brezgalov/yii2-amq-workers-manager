<?php

namespace Brezgalov\WorkersManagerAMQ;

use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\queue\amqp_interop\Queue;

class WorkersController extends Controller
{
    /**
     * @var WorkersCheckService
     */
    public $workersCheckService;

    /**
     * WorkersController constructor.
     * @param $id
     * @param $module
     * @param array $config
     */
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        if (empty($this->workersCheckService)) {
            $this->workersCheckService = new WorkersCheckService();
        }
    }

    /**
     * Manage workers configs
     *
     * @param $managerName
     * @throws InvalidConfigException
     */
    public function actionManage($managerName)
    {
        if (!\Yii::$app->has($managerName)) {
            throw new InvalidConfigException('Yii doesnt have workers manager comp. named ' . $managerName);
        }

        /** @var WorkersManagerService $queueComp */
        $managerComp = \Yii::$app->get($managerName);
        if (!($managerComp instanceof WorkersManagerService)) {
            throw new InvalidConfigException('Yii comp. named ' . $managerName . ' is not ' . WorkersManagerService::class);
        }

        $managerComp->handleConfigs();
    }

    /**
     * Start single listener
     *
     * @param $queueName
     * @throws InvalidConfigException
     */
    public function actionListen($queueName)
    {
        if (!\Yii::$app->has($queueName)) {
            throw new InvalidConfigException('Yii doesnt have queue comp. named ' . $queueName);
        }

        /** @var Queue $queueComp */
        $queueComp = \Yii::$app->get($queueName);
        if (!($queueComp instanceof Queue)) {
            throw new InvalidConfigException('Yii comp. named ' . $queueName . ' is not ' . Queue::class);
        }

        $_ENV[WorkersStatuses::PID_ENV] = getmypid();

        $worker = new WorkersStatuses();
        $worker->queue_name = $queueComp->queueName;
        $worker->pid = $_ENV[WorkersStatuses::PID_ENV];

        $worker->host = @$queueComp->host;
        $worker->port = (string)@$queueComp->port;
        $worker->exchange_name = @$queueComp->exchangeName;
        $worker->user = @$queueComp->user;

        $worker->status = WorkersStatuses::STATUS_ACTIVE;

        if (!$worker->save()) {
            throw new \Exception('Cant save worker status: ' . json_encode($worker->getErrorSummary(1)));
        }

        $queueComp->listen();
    }

    /**
     * Update workers statuses
     */
    public function actionCheck()
    {
        /** @var WorkersStatuses[] $workers */
        $workers = WorkersStatuses::find()->where(['status' => WorkersStatuses::STATUS_ACTIVE])->all();

        foreach ($workers as $worker) {
            $worker = $this->workersCheckService->updateStatus($worker);

            if (!$worker->save()) {
//                ErrorHelper::logError('Worker update error', $worker);
            }
        }
    }
}