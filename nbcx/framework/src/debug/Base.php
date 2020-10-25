<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\debug;
use nb\console\output\Output;
use nb\console\output\Table;
use nb\console\output\table\Cell;
use nb\console\output\table\Separator;
use nb\Pool;
use nb\Request;
use nb\Router;

/**
 * Base
 *
 * @package nb\src\debug
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/7
 */
class Base extends Command {

    //是否已经中断程序运行了
    private $died = false;

    /**
     * 中断程序运行
     * @param $status
     */
    public function quit($status) {
        if($this->died) {
            return;
        }
        $this->died = true;
        if($status) echo $status;
        throw new \Exception('die');
    }

    /**
     * 统计信息，存入Bug
     */
    public function end(){
        if($this->startd === false) {
            return false;
        }

        if(!Pool::object('nb\\event\\Framework')->debug()) {
            return false;
        }

        $record = $this->record;
        $log = $this->get();


        $record['spend'] = round(microtime(true)-$record['spend'],3);
        $record['mem'] = number_format((array_sum(explode(' ',memory_get_usage())) - $record['mem'])/1024).'kb';

        if(in_array('all',$this->show)||in_array('trace',$this->show)){
            $record['runfile'] = get_included_files();
        }
        if(empty($log)){
            $log[] = $record;
        }
        else{
            $n = array_unshift($log, $record);//向数组插入元素
            if($n >= $this->n) unset($log[$this->n]);
        }
        $this->printf($record);
        $this->put($log);
    }

    protected function printf($record) {
        $output = new Output();

        if(Request::driver()->data) {
            $output->info('Request');
            \nb\Debug::optimize(Request::driver()->data);
            echo "\n";
        }

        if(!empty($record['log'])) {
            $output->info('Log');
            foreach ($record['log'] as $log ){
                \nb\Debug::optimize($log['k']);
                echo '=>';
                \nb\Debug::optimize($log['v']);
                echo "\n";
            }
        }

        if(!empty($record['e'])) {
            $output->info('Exception');
            foreach ($record['e'] as $k => $v ){
                echo '['.$v['type'].']'.$v['message'].'('.$v['file'].':'.$v['line'].')'."\n";
            }
        }


        if(!empty($record['sql'])){
            $output->info('Sql');
            foreach ($record['sql'] as $k => $v ){
                echo htmlspecialchars($v['sql']) ."\n";
                $i = 1;
                if(!empty($v['param'])) {
                    foreach ($v['param'] as $v){
                        echo $i++;
                        echo ' ==> '.htmlspecialchars($v);
                        echo "\n";
                    }
                }
            }
        }
        $output->info('----------------------------------------------');
    }

    protected function test() {
        $output = new Output();
        $table = new Table($output);
        $table->setHeaders(['ISBN', '书名', '作者'])
            ->setRows([
                ['99921-58-10-7', 'Divine Comedy', 'Dante Alighieri'],
                new Separator(),
                [new Cell('This value spans 3 columns.', ['colspan' => 3])]
            ]);
        $table->render();
    }
}