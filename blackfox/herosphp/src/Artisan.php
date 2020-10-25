<?php
/**
 * artisan 命令行工具类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v2.0.0
 */
namespace herosphp;

use herosphp\gmodel\GModel;

class Artisan {

    private static $SHORT_OPS = 'hv';

    private static $LONG_OPTS = array(
        'make-model:'   => "创建Model，参数是Model名称.",
        'table:'   => "为Model指定数据表.",
        'pk:'   => "指定数据表主键. 默认值：id",

        'make-controller:'   => "创建Controller，参数是Controller名称.",
        'make-service:'   => "创建Service，参数是Service名称.",
        'module:' => '为 Model|Service|Controller 指定模块.',
        'model:' => "指定的modelDao的类路径（带命名空间）.",

        'make-db:'   => "创建数据库.",
        'dbhost:'   => "主机ip，默认值：127.0.0.1",
        'dbuser:'   => "数据库用户名. 默认值：root",
        'dbpass:'   => "数据库密码，默认值：123456",
        'dbname:'   => "数据库名称.",
        'charset:'   => "数据库字符编码. 默认值：UTF-8",

        'run:' => '执行一个客户端任务，参数是任务名称',

        'make-table:'   => "创建数据表，需要传入数据表的xml配置文档路径.",

        'import:'   => '从SQL文件导入数据.',

        'author:'   => '组件创建作者 默认：{yangjian}',
        'email:'    => '组件创建者邮箱 默认：{yangjian102621@gmail.com}',
        'desc:'     => '组件描述.',
        'date:'     => '组件创建日期，默认当天.',

        'help'      => '显示帮助信息. Shortcut -h.',
        'version'   => '显示版本信息. Shortcut -v.'
    );

    public static function run() {

        $opts = getopt(self::$SHORT_OPS, array_keys(self::$LONG_OPTS));

        if ( empty($opts) || isset($opts['help']) || isset($opts['h']) ) {
            return self::printHelpInfo();
        }
        if ( isset($opts['version']) || isset($opts['v']) ) {
            return self::printVersion();
        }

        if ( $opts['make-db'] ) { //创建数库
            $opts['dbname'] = $opts['make-db'];
            return GModel::createDatabase($opts);
        }

        if ( $opts['make-table'] ) { //创建数据表
            $opts['xmlpath'] = $opts['make-table'];
            return GModel::createTables($opts);
        }

        if ( $opts['make-model'] ) { //创建模型
            if ( strpos($opts['make-model'], '.xml') ) {
                $opts['xmlpath'] = $opts['make-model'];
            } else {
                $opts['model'] = $opts['make-model'];
            }
            return GModel::createModel($opts);
        }

        if ( $opts['make-service'] ) { //创建服务
            $opts['service'] = $opts['make-service'];
            return GModel::createService($opts);
        }

        if ( $opts['make-controller'] ) { //创建控制器
            $opts['controller'] = $opts['make-controller'];
            return GModel::createController($opts);
        }

        if ( $opts['run'] ) { //运行任务
            $className = ucfirst($opts['run']).'Task';
            $clazz = new \ReflectionClass("client\\tasks\\{$className}");
            $method = $clazz->getMethod('run');
            $method->invoke($clazz->newInstance());
        }

    }

    //打印帮助信息
    protected static function printHelpInfo() {
        tprintOk('Welcome to use HerosPHP artisan.');
        self::printVersion();
        tprintOk('Usage: ');
        printLine("  ./artisan [--make-model=Model | --make-service=Service | --make-controller=Controller | --import=sql] [options]");
        printLine();
        tprintOk('Options: ');
        foreach ( self::$LONG_OPTS as $key => $value ) {
            $key = rtrim($key, ":");
            printLine("  --{$key} {$value}");
        }
    }

    //打印版本信息
    protected static function printVersion() {
        printLine("Version : 1.0");
    }

}
