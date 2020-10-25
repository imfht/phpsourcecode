<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------
final class App
{
    /**
     * 运行应用
     * @access public
     * @reutrn mixed
     */
    public static function run()
    {
        //session处理
        session(C("SESSION_OPTIONS"));
        //执行应用开始钓子
        Hook::listen("APP_INIT");
        //执行应用开始钓子
        Hook::listen("APP_BEGIN");
        //Debug Start
        DEBUG and Debug::start("APP_BEGIN");
        self::start();
        //Debug End
        DEBUG and Debug::show("APP_BEGIN", "APP_END");
        //日志记录
        !DEBUG and C('LOG_RECORD') and  Log::save();
        //应用结束钓子
        Hook::listen("APP_END");
    }

    /**
     * 运行应用
     * @access private
     */
    static private function start()
    {
        //控制器实例
        $controller = controller(CONTROLLER);
        //控制器不存在
        if (!$controller) {
            //模块检测
            if(!is_dir(MODULE_PATH)){
                _404('模块' .MODULE  . '不存在');
            }
            //空控制器
            $controller = Controller("Empty");
            if (!$controller) {
                _404('控制器' . CONTROLLER .C("CONTROLLER_FIX") .'不存在');
            }
        }
        //执行动作
        try {
            $action = new ReflectionMethod($controller, ACTION);
            if ($action->isPublic()) {
                $action->invoke($controller);
            } else {
                throw new ReflectionException;
            }
        } catch (ReflectionException $e) {
            $action = new ReflectionMethod($controller, '__call');
            $action->invokeArgs($controller, array(ACTION, ''));
        }
    }
}