# 测试文件

## 拉取测试文件

```bash
$ git submodule update --init --recursive
```

## 长语音识别结果异步接收

```bash
$ php -S 0.0.0.0:8081
```

回调参数填写 `IP:8081/callback.php`