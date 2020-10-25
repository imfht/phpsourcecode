<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\common\behavior;

use think\Env;

/**
 * 应用初始化行为(初始化应用)
 * @author 牧羊人
 * @date 2019/4/23
 * Class InitApp
 * @package app\common\behavior
 */
class InitApp
{
    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @author 牧羊人
     * @date 2019/4/23
     */
    public function run()
    {
        // 初始化常量
        $this->initConst();

        // 安装系统直接return
        if (defined('SYSTEM_INSTALL')) {
            return;
        }

//         // 定义模板变量
//         if (!IS_CLI) {
//             // TODO...
//         }

//         // 系统版本
//         $version = include_once(ROOT_PATH . 'version.php');
//         config($version);
    }

    /**
     * 初始化常量
     * @author 牧羊人
     * @date 2019/4/23
     */
    private function initConst()
    {
        // 初始化系统常量
        $this->initSystemConst();

        // 初始化域名常量
        $this->initDomainConst();

        // 初始化数据库
        $this->initDbInfo();

        // 初始化消息队列参数
        $this->initRabbitMq();
    }

    /**
     * 初始化系统常量
     *
     * @author 牧羊人
     * @date 2019-04-24
     */
    private function initSystemConst()
    {
        // 基础常量
        define('THINK_PATH', \Env::get('think_path'));
        define('ROOT_PATH', \Env::get('root_path'));
        define('APP_PATH', \Env::get('app_path'));
        define('CONFIG_PATH', \Env::get('config_path'));
        define('ROUTE_PATH', \Env::get('route_path'));
        define('RUNTIME_PATH', \Env::get('runtime_path'));
        define('EXTEND_PATH', \Env::get('extend_path'));
        define('VENDOR_PATH', \Env::get('vendor_path'));
        define('PLUGIN_PATH', ROOT_PATH . 'plugins');
        define('PUBLIC_PATH', ROOT_PATH . 'public');

        // 附件常量
        define('ATTACHMENT_PATH', \Env::get('system.attach_path'));
        define('IMG_PATH', ATTACHMENT_PATH . "/img");
        define('UPLOAD_TEMP_PATH', IMG_PATH . '/temp');

        // 系统名称
        define('SITE_NAME', \Env::get('system.sitename'));
        define('NICK_NAME', \Env::get('system.nickname'));
        define('VERSION', \Env::get('system.version'));
    }

    /**
     * 初始化域名常量
     * @author 牧羊人
     * @date 2019/12/31
     */
    private function initDomainConst()
    {
        define('SITE_URL', \Env::get('domain.site_url'));
        define('MAIN_URL', \Env::get('domain.main_url'));
        define('WAP_URL', \Env::get('domain.wap_url'));
        define('API_URL', \Env::get('domain.api_url'));
        define('SCRIPT_URL', \Env::get('domain.script_url'));
        define('IMG_URL', \Env::get('domain.image_url'));
    }

    /**
     * 初始化消息队列参数
     * @author 牧羊人
     * @date 2020/1/2
     */
    private function initRabbitMq()
    {
        define('MQ_HOST', \Env::get('rabbitmq.host'));
        define('MQ_IP', \Env::get('rabbitmq.ip'));
        define('MQ_PORT', \Env::get('rabbitmq.port'));
        define('MQ_USER', \Env::get('rabbitmq.user'));
        define('MQ_PWD', \Env::get('rabbitmq.password'));
    }

    /**
     * 初始化数据库
     * @author 牧羊人
     * @date 2019/4/23
     */
    private function initDbInfo()
    {
        // 数据表前缀
        define('DB_PREFIX', \Env::get('database.prefix'));

        // 数据库分页数
        $list_rows = config('paginate.list_rows');
        empty($list_rows) ? define('DB_LIST_ROWS', 20) : define('DB_LIST_ROWS', $list_rows);
    }
}
