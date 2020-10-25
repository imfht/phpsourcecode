<?php

namespace herosphp\gmodel\utils;

/**
 * 创建service文件。
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
use herosphp\files\FileUtils;
use herosphp\string\StringBuffer;

class ServiceFactory {

    /**
     * 创建 service 文件
     * @param $options
     */
    public static function create($options) {

        if ( !isset($options['module']) ) return tprintError("Error : --module is needed.");
        if ( !isset($options['service']) ) return tprintError("Error : service name is needed.");
        if ( !isset($options['model']) ) return tprintError("Error : --model is needed.");
        if ( !isset($options['author']) ) $options['author'] = 'yangjian';
        if ( !isset($options['email']) ) $options['email'] = 'yangjian102621@gmail.com';
        if ( !isset($options['date']) ) $options['date'] = date('Y-m-d');
        if ( !isset($options['desc']) ) $options['desc'] = $options['service'];

        $moduleDir = APP_PATH."modules/{$options['module']}/";
        if ( !is_writable(dirname($moduleDir)) ) {
            tprintError("directory '{$moduleDir}' is not writeable， please add permissions.");
            return;
        }
        //创建目录
        if ( !file_exists($moduleDir.'service') ) {
            FileUtils::makeFileDirs($moduleDir.'service');
        }

        $className = ucfirst($options['service']);
        $replacements = array(
            '{module}' => $options['module'],
            '{desc}' => $options['desc'],
            '{author}' => $options['author'],
            '{email}' => $options['email'],
            '{date}' => $options['date'],
            '{model_name}' => $options['model'],
            '{className}' => $className,
        );

        $filename = $moduleDir.'service/'.$className.'.php';
        if ( file_exists($filename) ) { //若文件已经存在则跳过
            return tprintWarning("Warnning : Service file '{$filename}' is existed， skiped.");
        }

        $tempContent = file_get_contents(dirname(__DIR__)."/template/service.tpl");
        $content = str_replace(array_keys($replacements), $replacements, $tempContent);

        if ( file_put_contents($filename, $content) ) {
            tprintOk("Create Service '{$options['service']}' successfully！");
        } else {
            tprintError("Error : Create Service '{$options['service']}' faild.");
        }

    }

}
