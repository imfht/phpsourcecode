<?php

namespace application\core\components;

/**
 * 为日志添加额外信息，如 $_GET、$_POST、$_COOKIE 等
 *
 * @package application\core\components
 */
class AliyunLogFilter extends \CLogFilter
{
    public $dumper = 'print_r';

    public $logVars = array('_GET', '_POST', '_FILES', '_COOKIE', '_SERVER');

    public function filter(&$logs)
    {
        $context = $this->getContext();

        if (!empty($logs) && !empty($context)) {
            foreach ($logs as $k => $log) {
                if (is_array($log)) {
                    if (is_array($logs[$k][0])) {
                        $logs[$k][0]['context'] = $context;
                    } elseif (is_string($logs[$k][0])) {
                        $logs[$k][0] .= sprintf("\nContext:\n%s", $context);
                    }
                }
            }
        }
    }
}