<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/20 9:37
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace think;
define('APP_PATH', __DIR__ . '/cqkyicms/');
//define('APP_AUTO_BUILD',true); //开启自动生成
// 加载框架引导文件
require __DIR__ . '/thinkphp/base.php';

Container::get('app',[APP_PATH])->bind('push/Worker')->run()->send();
