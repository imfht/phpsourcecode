<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/22
 * Time: 下午9:22
 */

namespace inhere\gearman\tools;

/**
 * Class LogParser
 * @package inhere\gearman\tools
 * @NOTICE require open function `exec()` and `shell_exec()`
 */
class LogParser
{
    const MATCH_ERROR = '[ERROR] ';

    /**
     * @var string
     */
    private $file;

    /**
     * @var array
     */
    private $config = [
        'cacheData' => false,
        'cacheDir' => '',
    ];

    /**
     * @var array
     */
    private static $typeKeywordMap  = [
        'started'   => 'Starting job',  // started jobs
        'completed' => 'been completed',     // completed jobs
        'failed'    => 'Failed to do',        // Failed jobs
        'statistic' => ') Statistics {',  // statistic for each job
    ];

    /**
     * LogParser constructor.
     * @param $file
     * @param array $config
     */
    public function __construct($file, array $config = [])
    {
        if (!is_file($file)) {
            throw new \InvalidArgumentException("File not exists. FILE: $file", __LINE__);
        }

        $this->file = $file;
        $this->config = array_merge($this->config, $config);
    }

    /**
     * get all exec jobs info(by Statistics)
     * @return array
     */
    public function getJobsStatistics()
    {
        $data = [];
        $kw = static::$typeKeywordMap['statistic'];

        if ($lines = $this->getMatchedLines($kw)) {
            $data = $this->parseStatisticLines($lines);

            if ($this->config['cacheData'] && ($dir = $this->config['cacheDir'])) {
                $filename = basename($this->file);
                file_put_contents("{$dir}/{$filename}.cache", $data);
            }
        }

        return $data;
    }

    /**
     * get all started jobs info
     * @return array
     */
    public function getJobsInfo()
    {
        $data = [];
        $kw = static::$typeKeywordMap['started'];

        if ($lines = $this->getMatchedLines($kw)) {
            $data = $this->parseStartedLines($lines);
        }

        return $data;
    }

    /**
     * get all failed jobs info
     * @return array
     */
    public function getFailedJobsInfo()
    {
        $data = [];
        $kw = static::$typeKeywordMap['failed'];

        if ($lines = $this->getMatchedLines($kw)) {
            $data = $this->parseFailedLines($lines);
        }

        return $data;
    }

    /**
     * get failed job detail info(contain Exception trace info)
     * @return array
     */
    public function getFailedJobDetial($jobId)
    {
        exec("cat $this->file | grep -A 15 '$jobId'", $lines);

        return $lines;
    }

    /**
     * get failed job exception message and trace info
     * @return array
     */
    public function getFailedJobExceptionTrace($jobId)
    {
        exec("cat $this->file | grep -A 15 '$jobId) Failed'", $lines);

        return $lines;
    }

    /**
     * getTypeCount
     * @param string $type
     * @return int
     */
    public function getTypeCount($type)
    {
        if (isset(static::$typeKeywordMap[$type])) {
            $keyword = static::$typeKeywordMap[$type];
            return $this->getMatchedCount($keyword);
        }

        return 0;
    }

    /**
     * getTypeCounts
     * @return array
     */
    public function getTypeCounts()
    {
        $counts = [];

        foreach (self::$typeKeywordMap as $type => $keyword) {
            $counts[$type] = $this->getMatchedCount($keyword);
        }

        return $counts;
    }

    /**
     * @return int
     */
    public function getErrorCount()
    {
        return $this->getMatchedCount(self::MATCH_ERROR);
    }

    /**
     * @return array
     */
    public function getErrorsInfo()
    {
        return $this->getMatchedLines(self::MATCH_ERROR);
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public function getMatchedLines($keyword)
    {
        exec("cat $this->file | grep '$keyword'", $lines);

        return $lines;
    }

    /**
     * @param $keyword
     * @return int
     */
    public function getMatchedCount($keyword)
    {
        return (int)exec("cat $this->file | grep '$keyword' | wc -l");
    }

    /**
     * @return int
     */
    public function getWorkerStartTimes()
    {
        return (int)exec("cat $this->file | grep 'Started worker #0' | wc -l");
    }

    /**
     * @param array $lines
     * @return mixed
     */
    protected function parseStatisticLines($lines)
    {
        if (!$lines) {
            return null;
        }

        $data = [];
        foreach ($lines as $line) {
            // eg: [2017/06/22 21:02:35.1896] [Worker:14794] [WORKER_INFO] doJob: updateToken(H:testing0:692) Statistics {"status":1,"run_time":"2017\/06\/22 21:02:35.1611","end_time":"2017\/06\/22 21:02:35.1895","exec_count":11}
            $info = explode('] ', trim($line));
            list($role, $pid) = explode(':', $info[1]);
            preg_match('/^doJob: ([\w-]+)\((\S+)\).*Statistics (.*)/', $info[3], $matches);

            if (!isset($matches[1],$matches[2],$matches[3])) {
                throw new \RuntimeException("Log line format is error! cannot parse it.", __LINE__);
            }

            $stat = json_decode($matches[3], true);
            $data[] = array_merge([
                'log_time' => trim($info[0], '['),
                'role' => trim($role, '['),
                'pid' => $pid,// worker pid
                'level' => trim($info[2], '['),
                'job_name' => $matches[1],
                'job_id' => $matches[2],
            ], $stat);
        }

        return $data;
    }

    /**
     * @param array $lines
     * @return mixed
     */
    protected function parseStartedLines(array $lines)
    {
        if (!$lines) {
            return null;
        }

        $data = [];
        foreach ($lines as $line) {
            // eg: [2017/05/22 16:18:36.0349] [Worker:39] [WORKER_INFO] doJob: test_reverse(H:afa64bc05a60:2) Starting job, executed job count: 2
            $info = explode('] ', trim($line));
            list($role, $pid) = explode(':', $info[1]);
            preg_match('/^doJob: ([\w-]+)\((\S+)\).*count: (\d)/', $info[3], $matches);

            if (!isset($matches[1],$matches[2],$matches[3])) {
                throw new \RuntimeException("Log line format is error! cannot parse it.", __LINE__);
            }

            $data[] = [
                'time' => trim($info[0], '['),
                'role' => trim($role, '['),
                'pid' => $pid,
                'level' => trim($info[2], '['),
                'job_name' => $matches[1],
                'job_id' => $matches[2],
                'exec_count' => $matches[3],
            ];
        }

        return $data;
    }

    /**
     * getJobDetail
     * @param  string $jobId
     * @return array
     */
    public function getJobDetail($jobId)
    {
        if (!$str = shell_exec("cat $this->file | grep '$jobId'")) {
            return [];
        }

        $detail = [
            'id' => $jobId,
            'workload' => '!No Data!',
            'err_msg' => '',
            'err_trace' => '',
        ];

        if (strpos($str, 'workload:')) {
            preg_match("/Job workload: (.*)\n.*handler\((\S+)\).*\n\[(.*)\] \[Worker/", $str, $matches);

            if (!isset($matches[1],$matches[2],$matches[3])) {
                throw new \RuntimeException("Log line format is error! cannot parse it.", __LINE__);
            }

            $detail['workload'] = $matches[1];
            $detail['handler'] = $matches[2];
            $detail['end_time'] = $matches[3];
        } else {
            preg_match("/.*handler\((\S+)\).*\n\[(.*)\] \[Worker/", $str, $matches);

            if (!isset($matches[1],$matches[2])) {
                throw new \RuntimeException("Log line format is error! cannot parse it.", __LINE__);
            }

            $detail['handler'] = $matches[1];
            $detail['end_time'] = $matches[2];
        }

        // failed ?
        if (!$detail['status'] = strpos($str, 'been completed')) {
            $errLines = $this->getFailedJobExceptionTrace($jobId);

            $msgLine = array_shift($errLines);
            preg_match("/Exception: (.+)/", $msgLine, $ms);
            $detail['err_msg'] = trim($ms[1]);

            $errLines = array_filter(preg_replace("/.+\[Worker:\d+\].+/", '', $errLines));
            array_shift($errLines);
            $detail['err_trace'] = implode("\n", $errLines);
        }

        return $detail;
    }

    protected function cacheResult()
    {

    }

}
