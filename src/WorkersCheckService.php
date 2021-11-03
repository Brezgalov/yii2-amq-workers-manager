<?php

namespace Brezgalov\WorkersManagerAMQ;

use yii\base\Model;

class WorkersCheckService extends Model
{
    /**
     * @param WorkersStatuses $worker
     * @return WorkersStatuses
     */
    public function updateStatus(WorkersStatuses $worker)
    {
        //posix_kill - linux only
        $worker->status = posix_kill($worker->pid,0) ? WorkersStatuses::STATUS_ACTIVE : WorkersStatuses::STATUS_INACTIVE;
        $worker->checked_at = date('Y-m-d H:i:s');

        return $worker;
    }
}