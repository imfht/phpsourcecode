# nginx-php7-composer
nginx php7 dockerfile

该镜像集成了nginx+php7+composer，脚本由supervisor进行管理, 可自由安装php扩展，可配置多nginx server，一条命令即可搭建完整环境

* gitee地址:https://gitee.com/ccccccchu/nginx-php7-composer
* github地址:https://github.com/chuxin123/nginx-php7-composer

## 编译
``` 
docker build -t cccchu/nginx-php7 .
``` 

## container目录
* 代码目录：/data/www 
* php扩展目录：/data/phpextini
* php扩展安装脚本目录：/data/phpextfile
* nginx配置目录：/usr/local/nginx/conf/vhost
* 本地工作目录：/Users/apple

## 运行
``` 
docker run -it --name test_docker -v /Users/apple/source_codes:/data/www -v /Users/apple/nginx-php7/extini:/data/phpextini -v /Users/apple/nginx-php7/extfile:/data/phpextfile  -v /Users/apple/nginx-php7/site-enabled:/usr/local/nginx/conf/vhost -p 8081:80 -d cccchu/nginx-php7
``` 
> 注意:修改脚本或配置文件,需要重启容器