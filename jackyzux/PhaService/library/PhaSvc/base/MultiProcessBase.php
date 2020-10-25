<?php
namespace PhaSvc\Base;

/**
 * 基于 Swoole 的多进程任务处理
 *
 */

Trait MultiProcessBase
{
    public $process_name = 'PhaService';
    public $debugger     = FALSE;
    public $isLinux      = FALSE;
    public $master_pid   = 0;
    public $max_precess  = 5;
    public $works        = [];
    public $swoole_table = NULL;
    //public $new_index=0;
    public $memory_size = 1024;

    /**
     * CreateMultiProcessor
     *
     * @DoNotCover
     * @param null  $max_precess
     * @param null  $process_name
     * @param false $debugger
     */
    public function CreateMultiProcessor($max_precess = NULL, $process_name = NULL, $debugger = FALSE)
    {
        if (strtolower(PHP_OS) == 'linux') $this->isLinux = TRUE;

        if (NULL === $max_precess) {
            $this->max_precess = swoole_cpu_num();
        } else {
            $this->max_precess = $max_precess;
        }

        $this->debugger = $debugger;

        if (NULL !== $process_name) $this->process_name = $process_name;

        try {
            $this->swoole_table = new swoole_table($this->memory_size);
            $this->swoole_table->column('index', swoole_table::TYPE_INT); // 用于父子进程间数据交换
            $this->swoole_table->create();

            if ($this->isLinux) swoole_set_process_name($this->process_name . ':master');

            $this->master_pid = posix_getpid();
            $this->run();
            $this->processWait();

        } catch (\Exception $e) {
            die('[EXCEPTION][' . date('Y/m/d H:i:s') . '] ' . $e->getMessage());
        }
    }//end


    /**
     * 控制台调试输出
     *
     * @DoNotCover
     * @param $msg
     */
    public function cDebug($msg)
    {
        if (TRUE == $this->debugger)
            echo $msg;
    }//end

    /**
     * Run multi process
     *
     * @DoNotCover
     */
    public function run()
    {
        for ($i = 0; $i < $this->max_precess; $i++) {
            $this->CreateProcess();
            usleep(500000);
        }
    }//end


    /**
     * Create sub process
     *
     * @DoNotCover
     * @param null $index
     */
    public function CreateProcess($index = NULL)
    {
        if (is_null($index)) {
            //如果没有指定了索引，新建的子进程，开启计数
            $index = $this->swoole_table->get('index');
            if ($index === FALSE) {
                $index = 0;
            } else {
                $index = $index['index'] + 1;
            }
            $this->cDebug('[DEBUG]' . date('Y/m/d H:i:s') . 'WORKER_INDEX:' . $index . PHP_EOL);
        }

        $this->swoole_table->set('index', ['index' => $index]);

        $process = new swoole_process(function (swoole_process $worker) use ($index) {

            if ($this->isLinux) swoole_set_process_name($this->process_name . ':WORKER-' . $index);

            $this->cDebug('[CREATE_WORKER]' . date('Y/m/d H:i:s') . 'WORKER_INDEX:' . $index . PHP_EOL);

            //处理真实业务,继承本类重写RealWork 函数即可
            $this->RealWork($index, date('Y/m/d H:i:s'));

        }, FALSE, FALSE);

        $pid                 = $process->start();
        $this->works[$index] = $pid;

        return $pid;

    }//end


    /**
     * 真实任务处理, 子类请复写
     *
     * @DoNotCover
     * @param $index
     * @param $param
     */
    public function RealWork($index, $param)
    {
        $this->cDebug(sprintf(
            "[REAL_WORK][%s] WORKER_INDEX:%d, PARAM:%s",
            date("Y/m/d H:i:s"),
            $index,
            $param));
    }//end


    /**
     * 监控子进程状态,如果退出,重启子进程
     *
     * @DoNotCover
     * @throws Exception
     */
    public function processWait()
    {
        while (1) {
            if (count($this->works)) {
                $ret = swoole_process::wait();
                // $status = swoole_process::kill($ret['pid'], $signo = 0);
                // if ($status)
                // {
                //     swoole_process::kill($ret['pid'], $signo = SIGTERM);
                // }
                if ($ret) {
                    $this->rebootProcess($ret);
                }
            } else {
                break;
            }
        }
    }//end


    /**
     * 重启子进程
     *
     * @DoNotCover
     * @param $ret
     *
     * @throws Exception
     */
    public function rebootProcess($ret)
    {
        $pid   = $ret['pid'];
        $index = array_search($pid, $this->works);
        if ($index !== FALSE) {
            $index   = intval($index);
            $new_pid = $this->CreateProcess($index);
            $this->cDebug(
                "[REBOOT_PROCESS]["
                . date("Y/m/d H:i:s") . "] WORKER_INDEX:{$index}, NEW_WORKER_PID:{$new_pid} [SUCCESS]" . PHP_EOL);
            return;
        }

        throw new \Exception('[REBOOT_PROCESS][' . date("Y/m/d H:i:s") . '] ERROR, PID NOT FOUND');
    }//end

}//end class

