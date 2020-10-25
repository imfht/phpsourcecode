<?php

/**
 * Class InfoTask
 * @description('The services information', 'Display the service and system information')
 */

use PhaSvc\Base\TaskBase;

class InfoTask extends TaskBase
{
    /**
     * mainAction
     * @description('The services information')
     */
    public function mainAction()
    {
        //'┌', '├', '└','─', '─', '─','┬', '┼', '┴','┐', '┤', '┘','┌┬┐├┼┤└┴┘─│'

        $config = $this->getDI()->get('config');

        echo PHP_EOL . PHP_EOL;
        $this->cout('┌' . str_repeat('─', 78) . '┐', 'f159', TRUE);
        $this->cout('│' . str_repeat(' ', 30)
            . 'SYSTEM INFORMATION'
            . str_repeat(' ', 30) . '│',
            'f159', TRUE);
        $this->cout('├' . str_repeat('─', 78) . '┤', 'f159', TRUE);

        $this->cout('│ PROJECT : ', 'f159');
        $this->cout($config['appName'], 'bold,f1', TRUE);

        $this->cout('│ VERSION : ', 'f159');
        $this->cout($config['version'] . $config['rev_version'], 'f2', TRUE);

        $this->cout('│ WEBSITE : ', 'f159');
        $this->cout($config['website'], 'f3', TRUE);

        $this->cout('├' . str_repeat('─', 78) . '┤', 'f159', TRUE);
        $this->cout('│    操作系统: ' . php_uname('s') . ' ' . php_uname('r'), 'f159', TRUE);
        $this->cout('│    运行方式: ' . php_sapi_name(), 'f159', TRUE);
        $this->cout('│    进程用户: ' . get_current_user(), 'f159', TRUE);
        $this->cout('│     PHP版本: ' . PHP_VERSION, 'f159', TRUE);
        $this->cout('│    Zend版本: ' . zend_version(), 'f159', TRUE);
        $this->cout('│ Phalcon版本: ' . Phalcon\Version::get(), 'f159', TRUE);
        $this->cout('│    当前时区: ' . date_default_timezone_get(), 'f159', TRUE);
        $this->cout('│    当前时间: ' . date('Y-m-d H:i:s'), 'f159', TRUE);
        $this->cout('│    内存限制: ' . ini_get('memory_limit'), 'f159', TRUE);
        $this->cout('│  套接字超时: ' . ini_get('default_socket_timeout'), 'f159', TRUE);
        $this->cout('│    执行超时: ' . ini_get('max_execution_time'), 'f159', TRUE);

        $this->cout('└' . str_repeat('─', 78) . '┘', 'f159', TRUE);
        echo PHP_EOL . PHP_EOL;

    }//end


}//end
