<?php
/**
 * job callbacks
 * @var \inhere\gearman\BaseManager $mgr
 */

// $mgr->addHandler('test_pipe', function ($workload, \GearmanJob $job, $mgr)
// {
//     $data = $worker->sendMessage('status');
//     echo $data . PHP_EOL;
// });

$mgr->addHandler('test_reverse', function ($workload, \GearmanJob $job)
{
    echo ucwords(strrev($workload)) . PHP_EOL;
});

$mgr->addHandler('test_job', \inhere\gearman\examples\jobs\TestJob::class);

$mgr->addHandler('test_echo', \inhere\gearman\examples\jobs\EchoJob::class, [
    'worker_num' => 1,
    'focus_on' => 1,
]);
