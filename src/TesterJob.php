<?php

namespace Brezgalov\WorkersManagerAMQ;

class TesterJob extends AbstractJob
{
    /**
     * @var string
     */
    public $queueComponentName = 'queue';

    /**
     * @param $queue
     * @return bool
     * @throws \Exception
     */
    public function run($queue)
    {
        /** @var WorkersConfigs $componentConfig */
        $componentConfig = WorkersConfigs::find()
            ->where(['queue_component_name' => $this->queueComponentName])
            ->one();

        if (empty($componentConfig)) {
            return true;
        }

        $componentConfig->tested_at = date('Y-m-d H:i:s');
        if (!$componentConfig->save()) {
            throw new \Exception('Testing failed');
        }

        return true;
    }
}