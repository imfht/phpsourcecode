<?php


namespace application\core\components;

use application\core\utils\ArrayUtil;
use application\core\utils\HttpClient\HttpClientFactory;
use application\core\utils\Ibos;

/**
 * 调用 API 中心的接口，将日志写入阿里云 SLS
 *
 * @package application\core\components
 */
class AliyunLog extends \CLogRoute
{
    /**
     * API 中心日志接口
     *
     * @var string
     */
    const LOG_API_URL = 'http://api.ibos.cn/v4/aliyunlog/write';

    /**
     * Processes log messages and sends them to specific destination.
     * Derived child classes must implement this method.
     *
     * @param array $logs list of messages. Each array element represents one message
     * with the following structure:
     * array(
     *   [0] => message (string)
     *   [1] => level (string)
     *   [2] => category (string)
     *   [3] => timestamp (float, obtained by microtime(true));
     */
    protected function processLogs($logs)
    {
        $aesKey = Ibos::app()->setting->get('setting/aeskey');
        $httpClient = HttpClientFactory::create();
        // IBOS 版本信息，如：IBOS - SAAS - PRO - 4.2.0 - 201701031355
        $ibosVersion = sprintf('IBOS - %s - %s - %s - %s', strtoupper(ENGINE), strtoupper(VERSION_TYPE), VERSION,
            VERSION_DATE);

        $logItems = array();

        foreach ($logs as $log) {
            $message = ArrayUtil::getValue($log, 0, '');
            $level = ArrayUtil::getValue($log, 1, 'info');
            $category = ArrayUtil::getValue($log, 2, 'default');
            $timestamp = ArrayUtil::getValue($log, 3, time());

            $logItems[] = array(
                'ibosVersion' => $ibosVersion,
                'level' => $level,
                'category' => $category,
                'message' => $message,
                'localDatetime' => date('Y-m-d H:i:s', $timestamp),
            );
        }

        if (!empty($logItems)) {
            try {
                $httpClient->jsonPost(self::LOG_API_URL, array(), array(
                    'project' => 'ibos-saas',
                    'logStore' => 'ibos',
                    'topic' => $aesKey,
                    'source' => Ibos::app()->request->getUserHostAddress(),
                    'logItems' => $logItems,
                ));
            } catch (\Exception $e) {
                // 用户无法连接外网
            }
        }

    }
}