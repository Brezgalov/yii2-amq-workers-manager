<?php

namespace Brezgalov\WorkersManagerAMQ;

use yii\base\Model;

class WorkersStartService extends Model
{
    /**
     * @var string
     */
    public $controllerName = 'workers';

    /**
     * @var string
     */
    public $listenerAction = 'listen';

    /**
     * @var string
     */
    public $phpPath = '/usr/bin/php';

    /**
     * @var string
     */
    public $yiiPathAlias = '@app/yii';

    /**
     * @param WorkersConfigs $config
     * @param $count
     * @return bool
     */
    public function startWorker(WorkersConfigs $config, $count)
    {
        $yii = \Yii::getAlias($this->yiiPathAlias);

        for ($i = $count; $i > 0; $i -= 1) {
            exec("{$this->phpPath} {$yii} {$this->controllerName}/{$this->listenerAction} {$config->queue_component_name} >/dev/null 2>/dev/null &");
        }

        return true;
    }
}