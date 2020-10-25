<?php 
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
##本地测试
namespace app\common\behavior;
class HostConfig
{
    public function run(&$params)
    {
        ##本地测试
        if($_SERVER['HTTP_HOST']=="thinkask.com"||$_SERVER['HTTP_HOST']=="www.thinkask.com"||$_SERVER['HTTP_HOST']=="www.thinkask.net"||$_SERVER['HTTP_HOST']=="thinkask.net"){
            config('multi_route_modules',['www.thinkask.net'=>['jblog',]]);
            config('root_domain',"thinkask.com");
        }

   }


}