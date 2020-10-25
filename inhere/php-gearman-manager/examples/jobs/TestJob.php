<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/4
 * Time: 下午8:54
 */

namespace inhere\gearman\examples\jobs;

/**
 * Class TestJob
 * a class implement the '__invoke()'
 *
 * @package inhere\gearman\examples\jobs
 */
class TestJob
{
    /**
     * @param $workload
     * @param \GearmanJob $job
     * @return mixed|void
     */
    public function __invoke($workload, \GearmanJob $job)
    {
        printf("this is %s, success received data. Job=%s Data=$workload\n", __METHOD__, $job->functionName()) ;
    }
}
