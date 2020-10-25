<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-04-27
 * Time: 16:06
 */

namespace inhere\gearman\jobs;

/**
 * Class Job
 * @package inhere\gearman\jobs
 */
abstract class Job implements JobInterface
{
    /**
     * @var mixed
     */
    protected $context;

    /**
     * the job id
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * Job constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        // some init ...
    }

    /**
     * do the job
     * @param string $workload
     * @param \GearmanJob $job
     * @return mixed
     */
    public function run($workload, \GearmanJob $job)
    {
        $result = false;
        $this->id = $job->handle();
        $this->name = $job->functionName();

        try {
            if (false !== $this->beforeRun($workload, $job)) {
                $result = $this->doRun($workload, $job);

                $this->afterRun($result);
            }
        } catch (\Exception $e) {
            $this->onException($e);
        }

        return $result;
    }

    /**
     * beforeRun
     * @param $workload
     * @param \GearmanJob $job
     */
    protected function beforeRun($workload, \GearmanJob $job)
    {
    }

    /**
     * doRun
     * @param $workload
     * @param \GearmanJob $job
     * @return mixed
     */
    abstract protected function doRun($workload, \GearmanJob $job);

    /**
     * afterRun
     * @param mixed $result
     */
    protected function afterRun($result)
    {
    }

    /**
     * @param \Exception $e
     */
    protected function onException(\Exception $e)
    {
        // error
    }

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }
}
