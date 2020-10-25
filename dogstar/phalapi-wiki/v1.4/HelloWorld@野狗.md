## 运行Hello World
> 运行你的第一个PhalApi的Hello World！

### 入口文件

    #file: /public/index.php  #主入口文件
    #file: /public/demo/index.php #demo模块入口文件
    #默认访问模块下default控制index方法
    
    访问接口链接:
    [1] http://www.xxx.com/public (等同于访问[2]的url)
    [2] http://www.xxx.com/public/index.php?service=Default.index
    [3] http://www.xxx.com/public/demo/index.php?service=Default.index (指定访问demo模块的接口Default控制器index方法)
    
    如果服务器配置网站根目录直接指向Public则访问:
    [1] http://www.xxx.com/ (等同于访问[2]的url)
    [2] http://www.xxx.com/index.php?service=Default.index
    [3] http://www.xxx.com/demo/index.php?service=Default.index (指定访问demo模块的接口Default控制器index方法)
> 服务器根目录指向Puclic目录，让访问更加安全。

---

### 输出Hello World!


```php
#file: /Demo/Api/Default.php #Demo模块Default控制器文件

#修改Default控制器中index方法
public function index()
    {
        return ['show' => 'hello world!', 'des' => 'this is PhalApi Demo'];
    }
```


```html
#访问接口输出显示:
{
    "ret": 200,
    "data": {
        "show": "hello world!",
        "des": "this is PhalApi Demo"
    },
    "msg": ""
}
```
至此已经完成了第一hello world!的接口!