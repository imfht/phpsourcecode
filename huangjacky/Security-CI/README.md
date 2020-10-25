#Security-CI
> 作者: huangjacky  
> QQ: 4462676  
> Email: huangjacky@163.com
结合日常工作将一些安全特性注入到CodeIgniter框架中,从而使开发更加安全和易用.

# 代办
## 1. 完善过滤函数
check_helper.php
## 2. 增加过滤功能支持,已实现
目前只有参数验证
## 3. 针对常见的参数验证和过滤,已实现
$_COOKIE, $_GET, $_POST,cookie不需要验证
## 4. RESTful框架搭建
更简单方便的使用RESTful功能

# 使用
applicaton/config/params_check.php中配置,配置参数校验的方式和函数,以及校验不通过后的回调函数.  
```
$config['params'] = array(  
    'id' => array('Int','gt:4'),//id参数必须是整数,而且要大于0  
);  
/**  
 \* 当参数校验失败的后执行的操作  
 */  
$config['param_error_callback'] = function ($pname){  
    echo 'param: '.$pname. ' with wrong value';  
};```  
 
> $config\['params'\]如果设置为空,那么就不会使用参数校验功能