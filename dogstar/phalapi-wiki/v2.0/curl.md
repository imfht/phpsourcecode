# CURL请求

当需要进行curl请求时，可使用PhalApi封装的CURL请求类[PhalApi\CUrl](https://github.com/phalapi/kernal/blob/master/src/CUrl.php)，从而实现快捷方便的请求。  

## 发起GET请求

例如，需要请求的链接为：```http://demo2.phalapi.net/```，则可以：  

```
// 先实例
$curl = new \PhalApi\CUrl();

// 第二个参数，表示超时时间，单位为毫秒
$rs = $curl->get('http://demo2.phalapi.net/?username=dogstar', 3000);

echo $rs;
// 输出类似如下：
// {"ret":200,"data":{"title":"Hello dogstar","version":"2.1.2","time":1513506356},"msg":""}
```

## 发起POST请求

当需要发起POST请求时，和GET方式类似，但需要把待POST的参数单独传递，而不是拼接在URL后面。如： 
```
try {
    // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
    $curl = new \PhalApi\CUrl(2);

    // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
    $rs = $curl->post('http://demo2.phalapi.net/?', array('username' => 'dogstar'), 3000);

    // 一样的输出
    echo $rs;
} catch (\PhalApi\Exception\InternalServerErrorException $ex) {
    // 错误处理……
}
```