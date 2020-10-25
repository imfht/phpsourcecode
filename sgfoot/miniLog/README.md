# [请跳转到https://gitee.com/sgfoot/sglogs为最版本](https://gitee.com/sgfoot/sglogs)
# miniLog php日志类
## 1.1版本
    - 更新日期:2017/01/12 pm
    - 解决miniLog::log()多次调用生成多个文件

## 1.0版本
    - 更新日志:2017/01/11
  　- 实现开发时快速调试
    - 快捷查看日志
    - 优点:少配置或零配置,支持任何格式数据记录,支持数G数据存储.支持在浏览或linux环境查看
  - 配置:
   - 首先对写日志的目录写的权限
   - 可以在外部更改的常量:

 -  支持html便捷浏览模式或纯txt查看,值html|txt

```
    defined('MINI_DEBUG_TYPE') or define('MINI_DEBUG_TYPE', 'html');
    调试模式,1可写,0不可写
    defined('MINI_DEBUG_FLAG') or define('MINI_DEBUG_FLAG', 1);
    jquery 地址
    defined('MINI_DEBUG_JSPAHT') or define('MINI_DEBUG_JSPAHT', 'http://cdn.bootcss.com/jquery/1.8.3/jquery.js');
    debug 可写的目录设置,结尾一定要加 保证有可写权限
    defined('MINI_DEBUG_PATH') or define('MINI_DEBUG_PATH', __DIR__ . DIRECTORY_SEPARATOR);
```
 - 更改存储目录:
```
    define('MINI_DEBUG_PATH', __DIR__ . '/');//必须后面加斜杆 /
    miniLog::log('err', 'myFlag');
```
 - 存储不同的文件名:
```
    define('MINI_DEBUG_PATH', __DIR__ . '/');
    miniLog::setCacheFile(date('Y-m-di'));//无需设置文件后缀
    miniLog::log('err', 'myFlag');
```
 -  更改存储格式:
```
    define('MINI_DEBUG_PATH', __DIR__ . '/');
    define('MINI_DEBUG_TYPE', 'txt');//默认为html
    miniLog::log('err', 'myFlag');
```
 - 覆盖文件,相当将之前的数据删除,写入新的数据,可做清空数据用
```
    define('MINI_DEBUG_PATH', __DIR__ . '/');
    miniLog::log(1, 'myFlag', false);
```