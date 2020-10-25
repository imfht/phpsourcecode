<?php

namespace Gouguoyin\LogViewer;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class LogViewerService
{
    protected $config;
    protected $logName;

    /**
     * 扩展包名
     */
    const PACKAGE_NAME = 'log-viewer';

    /**
     * 日志等级
     */
    const LOG_LEVEL_INFO      = 'info';
    const LOG_LEVEL_ERROR     = 'error';
    const LOG_LEVEL_NOTICE    = 'notice';
    const LOG_LEVEL_WARNING   = 'warning';
    const LOG_LEVEL_DEBUG     = 'debug';
    const LOG_LEVEL_ALERT     = 'alert';
    const LOG_LEVEL_EMERGENCY = 'emergency';
    const LOG_LEVEL_CRITICAL  = 'critical';

    public function __construct()
    {
        $this->config = config(self::PACKAGE_NAME);

        App::setLocale($this->config['locale_language']);
    }

    /**
     * 获取包名
     * @return string
     */
    public function getPackageName()
    {
        return self::PACKAGE_NAME;
    }

    /**
     * 获取所有日志
     * @param string $keywords 关键字
     * @return array
     */
    public function getAllLogs($keywords = null)
    {
        $logs = [];
        foreach (File::allFiles(storage_path('logs')) as $log) {
            $pathName = $log->getRelativePathname();

            if (Str::contains($pathName, $keywords)) {
                $logs[] = $log->getRelativePathname();
            }

            if (!$keywords) {
                $logs[] = $log->getRelativePathname();
            }
        }
        return array_reverse($logs);
    }

    /**
     * 设置日志路径
     * @param $logName
     */
    public function setLogPath($logName)
    {
        $this->logName = $logName;
    }

    /**
     * 获取日志名
     * @return string
     */
    public function getLogName()
    {
        return $this->logName;
    }

    /**
     * 获取日志路径
     * @return string
     */
    public function getLogPath()
    {
        return storage_path('logs' . DIRECTORY_SEPARATOR . $this->logName);
    }

    /**
     * 获取日志大小
     * @return string
     */
    public function getLogSize()
    {
        $bytes = File::size($this->getLogPath());

        $size = ['B', 'K', 'M', 'G', 'T'];
        for ($i = 0; $bytes >= 1024 && $i < 4; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $size[$i];
    }

    /**
     * 获取日志最后修改时间
     * @return string
     */
    public function getLogModified()
    {
        return Carbon::parse(File::lastModified($this->getLogPath()))->format('Y-m-d H:i:s');
    }

    /**
     * 获取日志内容
     * @return array
     */
    public function getLogContents()
    {
        $content = File::sharedGet($this->getLogPath());
        $pattern = "/^\[(?<datetime>.*)\]\s(?<env>\w+)\.(?<level>\w+):(?<message>.*)/m";

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER, 0);

        $logs = [];
        foreach ($matches as $match) {
            $logs[] = [
                'datetime' => $match['datetime'],
                'env'      => $match['env'],
                'level'    => strtolower($match['level']),
                'message'  => trim($match['message'])
            ];
        }

        return $logs;
    }

    /**
     * 获取日志等级图标
     * @param $level
     * @return mixed
     */
    public function getLevelIcon($level)
    {
        $icons = [
            self::LOG_LEVEL_INFO      => 'fa-info-circle',
            self::LOG_LEVEL_ERROR     => 'fa-times-circle',
            self::LOG_LEVEL_NOTICE    => 'fa-question-circle',
            self::LOG_LEVEL_WARNING   => 'fa-warning',
            self::LOG_LEVEL_DEBUG     => 'fa-question-circle',
            self::LOG_LEVEL_ALERT     => 'fa-info-circle',
            self::LOG_LEVEL_CRITICAL  => 'fa-times-circle',
            self::LOG_LEVEL_EMERGENCY => 'fa-warning',
        ];

        return $icons[strtolower($level)];
    }

    /**
     * 获取日志等级颜色
     * @param $level
     * @return mixed
     */
    public function getLevelColor($level)
    {
        $icons = [
            self::LOG_LEVEL_INFO      => 'text-success',
            self::LOG_LEVEL_ERROR     => 'text-danger',
            self::LOG_LEVEL_NOTICE    => 'text-warning',
            self::LOG_LEVEL_WARNING   => 'text-warning',
            self::LOG_LEVEL_DEBUG     => 'text-debug',
            self::LOG_LEVEL_ALERT     => 'text-warning',
            self::LOG_LEVEL_CRITICAL  => 'text-danger',
            self::LOG_LEVEL_EMERGENCY => 'text-warning',
        ];

        return $icons[strtolower($level)];
    }

    /**
     * 授权检测
     * @return bool
     */
    public function authorization()
    {
        return app()->environment('local') || Gate::allows($this->getPackageName());
    }

}
