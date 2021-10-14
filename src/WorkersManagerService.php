<?php

namespace Brezgalov\WorkersManagerAMQ;

use yii\base\BootstrapInterface;
use yii\base\Model;
use yii\console\Application as ConsoleApp;

class WorkersManagerService extends Model implements BootstrapInterface
{
    /**
     * @var string
     */
    public $controllerName = 'workers';

    /**
     * @var string[]
     */
    public $workersController = WorkersController::class;

    /**
     * @var WorkersStartService
     */
    public $workersStartService;

    /**
     * @var WorkersStopService
     */
    public $workersStopService;

    /**
     * WorkersManagerService constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        if (empty($this->workersStartService)) {
            $this->workersStartService = new WorkersStartService();
        }

        if (empty($this->workersStopService)) {
            $this->workersStopService = new WorkersStopService();
        }
    }

    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        if ($app instanceof ConsoleApp) {
            $app->controllerMap[$this->controllerName] = $this->workersController;
        }
    }

    /**
     * @return bool
     */
    public function handleConfigs()
    {
        /** @var WorkersConfigs[] $configs */
        $configs = WorkersConfigs::find()->all();

        foreach ($configs as $config) {
            $this->handleSingleConfig($config);
        }

        return true;
    }

    /**
     * @param WorkersConfigs $config
     * @return bool
     */
    public function handleSingleConfig(WorkersConfigs $config)
    {
        $workersCount = WorkersStatuses::find()
            ->andWhere(['status' => WorkersStatuses::STATUS_ACTIVE])
            ->count();

        $countDiff = $config->workers_count - $workersCount;

        if ($countDiff == 0) {
            return true;
        }

        return $countDiff < 0 ? (
            $this->stopWorker($config, abs($countDiff))
        ) : (
            $this->startWorker($config, $countDiff)
        );
    }

    /**
     * @param WorkersConfigs $config
     * @param $count
     * @return bool
     */
    public function stopWorker(WorkersConfigs $config, $count)
    {
        return $this->workersStopService->stopWorkerByConfig($config, $count);
    }

    /**
     * @param WorkersConfigs $config
     * @param $count
     * @return bool
     */
    public function startWorker(WorkersConfigs $config, $count)
    {
        $this->workersStartService->controllerName = $this->controllerName;

        return $this->workersStartService->startWorker($config, $count);
    }
}