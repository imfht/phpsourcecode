<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/4
 * Time: 下午8:54
 */

namespace inhere\gearman\examples\jobs;

use inhere\gearman\jobs\Job;

/**
 * Class EchoJob
 * @package inhere\gearman\examples\jobs
 */
class EchoJob extends Job
{
    /**
     * {@inheritDoc}
     */
    protected function doRun($workload, \GearmanJob $job)
    {
        echo "receive: $workload\n";
    }
}
