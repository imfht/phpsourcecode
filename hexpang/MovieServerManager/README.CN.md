# 电影下载系统
一个电影下载管理系统，适合放在树莓派上。使用Laravel开发

## 项目更新

1. 2020-07-14 更新(使用WatchSoMuch资源,未仔细测试)
2. 这是最后一次更新(不排除小修复),之后将不再进行新站点资源的爬取.
3. 奥力给!

## 需求
1. Aria2
2. php*-curl
3. php*-ssh2
4. composer

## 配置信息(.env)
```
SERVER_HOST=localhost
SERVER_PORT=22
SERVER_USERNAME=pi
SERVER_PASSWORD=rsapberrypi
SERVER_ARIA=http://localhost:6800/jsonrpc
```

## 安装方式
1. git clone
2. composer update
3. copy .env.example to .env
4. php artisan key:gen

## 截图
![1](https://gitee.com/hexpang/MovieServerManager/raw/master/screenshots/1.png)

![2](https://gitee.com/hexpang/MovieServerManager/raw/master/screenshots/2.png)

![3](https://gitee.com/hexpang/MovieServerManager/raw/master/screenshots/3.png)

![4](https://gitee.com/hexpang/MovieServerManager/raw/master/screenshots/4.png)
