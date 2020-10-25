<?php
namespace App\Tasks;

use Framework\SZTask;

/**
 * Class MainTask
 * @package App\Tasks
 */
class MainTask extends SZTask {
    public function execute($params) {
        echo "task run\n";
        $sum = 0;
        for ($i = 0; $i < 1000; $i++) {
            $sum += $i;
        }

        echo "task end{$sum}\n";
        return 0;
    }

    public function finish($params) {
        echo 'task finish';
//        var_dump($params);
    }
}