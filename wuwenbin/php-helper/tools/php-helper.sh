#!/bin/bash

PATH=/bin:/sbin:/usr/bin:/usr/sbin:$PATH

CMD=$1
for arg in $@; do
    case $arg in
    -d=*)
        OPT_PATH=${arg/-d=/}
    ;;
    -path=*)
        OPT_PATH=${arg/-path=/}
    ;;
    esac
done

function print_help() {
    echo ""
    echo "Usage: [command] [options]"
    echo ""
    echo "  create-project"
    echo "  -d, --path  project directory"
    echo ""
}

function create_project() {
    if test "$OPT_PATH" = ""; then
        echo "* project directory not empty"
        exit 1
    fi

    if test -d "$OPT_PATH"; then
        echo "* project directory has exists";
        exit 1
    fi

    mkdir "$OPT_PATH"
    if test ! -d "$OPT_PATH"; then
        echo "* create project directory failed";
        exit 1
    fi

    mkdir "$OPT_PATH"/root
    mkdir "$OPT_PATH"/root/controller
    mkdir "$OPT_PATH"/root/controller/public
    mkdir "$OPT_PATH"/root/template
    mkdir "$OPT_PATH"/root/template/public
    mkdir "$OPT_PATH"/root/config
    mkdir "$OPT_PATH"/root/common
    mkdir "$OPT_PATH"/root/module
    mkdir "$OPT_PATH"/root/include
    mkdir "$OPT_PATH"/root/data
    mkdir "$OPT_PATH"/root/data/tpl_compile
    mkdir "$OPT_PATH"/root/data/log
    mkdir "$OPT_PATH"/root/data/cache
    mkdir "$OPT_PATH"/root/data/tmp
    mkdir "$OPT_PATH"/public
    mkdir "$OPT_PATH"/public/static
    touch "$OPT_PATH"/README.md
    touch "$OPT_PATH"/public/index.php
    touch "$OPT_PATH"/root/config/base.php
    touch "$OPT_PATH"/root/controller/public/Index.php

    echo '<?php
/**
 * 入口文件
 */
$rootPath = dirname(dirname(__FILE__)) . "/root";
include_once($rootPath . "/common/helper.php");
App::run("public", $rootPath);
' > "$OPT_PATH"/public/index.php

    echo '<?php
/**
 * 全局配置
 */
$config = array();
$config["db"]["default"] = array(
    "host" => "localhost",
    "port" => 3306,
    "user" => "root",
    "pass" => "123456",
    "name" => "test"
);
return $config;
' > "$OPT_PATH"/root/config/base.php

    echo '<?php
/**
 * 默认控制器
 */
class ControllerIndex
{
    /**
     * 默认操作
     */
    public function actionIndex()
    {
        echo "<pre>Hello,world</pre>";
    }
}
' > "$OPT_PATH"/root/controller/public/Index.php

    curl http://git.oschina.net/wuwenbin/php-helper/raw/master/src/helper.php > "$OPT_PATH"/root/common/helper.php

    echo "* create project directory success"
}

case $CMD in
    create-project)
        create_project
    ;;
    *)
        print_help
    ;;
esac

