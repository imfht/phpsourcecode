<?php
/**
 * 多线程下载
 * 
 * @package     MultiThreading.php
 * @author      Jing <tangjing3321@gmail.com>
 * @version     1.0
 * @date        2018年3月29日
 */

namespace SlimCustom\Libs\Download;

use \SlimCustom\Libs\Thread\Client as ThreadClient;

require __DIR__ . '/../Thread/Client.php';

set_time_limit(0);

class MultiThreading
{
    /**
     * 线程池
     * 
     * @var array
     */
    private $threads;
    
    /**
     * 线程数量限制
     * 
     * @var integer
     */
    private $limit;
    
    /**
     * 下载配置信息
     * 
     * @var array
     */
    private $downloadConfigs = [];
    
    /**
     * 初始化下载配置
     * 
     * @param array $urls
     * @param string $targetDir
     * @param string $displayProgress
     * @param integer $dirGenerationRule
     * @param \Closure $curlProgressCallback
     * @param integer $limit
     */
    public function __construct($urls, $targetDir, $displayProgress = false, $dirGenerationRule = 1, $curlProgressCallback = null, $limit = 2)
    {
        $this->downloadConfigs = [
            'urls' => $urls,
            'target_dir' => $targetDir,
            'display_progress' => $displayProgress,
            'dirGeneration_rule' => $dirGenerationRule,
            'curlProgress_callback' => $curlProgressCallback
        ];
        $this->limit = $limit;
    }
    
    /**
     * 启动多线程下载
     */
    public function run() {
        // 多任务进度展示线程，关闭单进程模式下的单任务进度输出
        if ($this->downloadConfigs['display_progress']) {
            $this->displayProgressThread();
            $this->downloadConfigs['display_progress'] = false;
        }
        // 下载线程
        $this->downloadThread();
        // 开始和检测子线程
        foreach($this->threads as $thread) {
            $thread->start();
        }
        foreach($this->threads as $thread) {
            $thread->join();
        }
    }
    
    /**
     * 下载线程
     */
    private function downloadThread()
    {
        $multiThreading = $this;
        foreach ($this->downloadConfigs['urls'] as $key => $url) {
            $this->threads[] = new ThreadClient([$url, $this->downloadConfigs, $multiThreading], function ($url, $downloadConfigs, $multiThreading) {
                require_once __DIR__ . '/../../../../public/index.php';
                $download = download($url, $downloadConfigs['target_dir'], $downloadConfigs['display_progress'], $downloadConfigs['dirGeneration_rule'], $downloadConfigs['curlProgress_callback']);
                $download->run();
            });
        }
    }
    
    /**
     * 多任务进度展示线程
     * 
     * @param array $urls
     */
    private function displayProgressThread()
    {
        $this->threads[] = new ThreadClient([$this->downloadConfigs['urls']], function ($urls) {
            require_once __DIR__ . '/../../../../public/index.php';
            while (true) {
                echo "\33[s";
                $count = 1;
                foreach ($urls as $url) {
                    $printStr = cache()->get('download.rate.printStr.' . md5($url), '');
                    echo "\033[{$count};0H\033[K {$printStr}" . PHP_EOL;
                    $count++;
                }
                echo "\33[u";
                usleep(100000);
            }
        });
    }
}