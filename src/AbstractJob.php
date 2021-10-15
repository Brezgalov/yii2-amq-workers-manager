<?php

namespace Brezgalov\WorkersManagerAMQ;

use yii\base\Model;
use yii\queue\JobInterface;

abstract class AbstractJob extends Model implements JobInterface
{
    public function execute($queue)
    {
        $pid = null;
        if (array_key_exists(WorkersStatuses::PID_ENV, $_ENV)) {
            $pid = $_ENV[WorkersStatuses::PID_ENV];
        }

        $worker = WorkersStatuses::findOne([
            'pid' => $pid,
            'status' => WorkersStatuses::STATUS_ACTIVE
        ]);

        if ($worker) {
            $worker->is_busy = 1;

            if (!$worker->save()) {
//                ErrorHelper::logError('IS_BUSY error', $worker);
            }
        }

        $res = true;
        try {
            $res = $this->run($queue);
        } catch (\Exception $ex) {
            // silence is golden
        }


        if ($worker) {
            $worker->is_busy = 0;

            if (!$worker->save()) {
//                ErrorHelper::logError('IS_BUSY error', $worker);
            }
        }

        return $res;
    }

    /**
     * @param $queue
     * @return mixed
     */
    public abstract function run($queue);
}