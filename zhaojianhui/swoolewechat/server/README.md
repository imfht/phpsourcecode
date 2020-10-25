#说明文档

WebSocket服务器
----
```shell
php server/websocketServer.php
```
客户端websocket_client.html，需要修改js代码中的ip和端口，可以直接用浏览器打开此页面。然后打开chrome的调试工具，或火狐的firebug，
然后终端执行websocket.send("hello"),向服务器发送信息。

> HttpServer和AppServer虽然可以直接访问，但是还是要配合nginx或apache，请求静态文件是由Nginx/Apache直接处理，当请求的文件不存在时，发送给Swoole服务器，来进行处理。

HttpServer的使用方法
----
http服务器跟fpm和apache很像，只是去包含documentRoot中的php文件，没有带有任何额外功能。
与appServer.php不同，httpServer.php是没有携带任何Swoole Web框架功能的。
```shell
php server/httpServer.php
```

HttpClient的使用方法
----
类似于curl、file_get_contents等方法，http请求客户端
```shell
php server/httpClient.php
```

AppServer的使用方法
----
AppServer就是Swoole的内置应用服务器，你需要按照Swoole Web框架的规范来写代码，所以应用程序的代码都在apps/目录中。
URL会路由到Controller的方法中，数据库的处理使用Swoole框架提供的Model或者SelectDB，模板使用smarty引擎或者直接使用php作为模板。
```shell
php server/appServer.php
```

TaskServer的使用方法
----
TaskServer是异步任务应用，可将耗时的工作放到异步任务中处理，例如发送邮件，
```shell
php server/taskServer.php//异步任务消耗
php server/taskClient.php//异步任务投递
```

EventWorkerServer的使用方法
----
EventWorkerServer是做事件触发功能,可做异步脚本，同样一段数据可以同时丢给不同的处理者处理，例如有的数据同时需要
```shell
php server/eventWorkersServer.php//启动事件触发功能
php server/eventWorkersClient.php//触发事件
```

FtpServer的使用方法
----
FtpServer建立ftp服务
```shell
sudo /usr/local/php/bin/php server/ftpServer.php start -w 10
```