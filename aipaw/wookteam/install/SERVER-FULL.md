# 安装教程

[本文视频教程 https://youtu.be/TOnPkEpjbig](https://youtu.be/TOnPkEpjbig)

## 准备

```sh
yum install gcc gcc-c++ make -y && yum install kernel-devel -y && yum update kernel -y
```

## 安装Nginx

```sh
yum localinstall http://nginx.org/packages/centos/7/noarch/RPMS/nginx-release-centos-7-0.el7.ngx.noarch.rpm && yum repolist enabled | grep "nginx*" && yum install -y nginx && systemctl enable nginx && systemctl start nginx
```

## 安装Php7.2

```sh
rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm && rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm && yum install -y php72w php72w-cli php72w-devel php72w-gd php72w-fpm php72w-mbstring php72w-pear php72w-xml php72w-xmlrpc php72w-common php72w-pdo php72w-mysqli && systemctl start php-fpm && systemctl enable php-fpm
```

### 修改php配置文件

```sh
vim /etc/php-fpm.d/www.conf
```

修改以下配置

```
listen = /var/run/php-fpm/php-fpm.sock
listen.owner = nginx
listen.group = nginx
user = nginx 
group = nginx
```

## 安装Swoole

```sh
pecl install swoole #一路回车
```

### 修改php配置文件

```sh
vim /etc/php.ini
```

添加

```
extension=swoole.so
```

## 安装Composer

```sh
cd /home && curl -sS https://getcomposer.org/installer | php && mv composer.phar  /usr/local/bin/composer
```

## 安装Mysql5.7

```sh
rpm -ivh https://dev.mysql.com/get/mysql57-community-release-el7-11.noarch.rpm && yum -y install mysql-community-server && systemctl enable mysqld && systemctl start mysqld
```

### 配置输出mysql默认密码
```
grep 'temporary password' /var/log/mysqld.log
```

## 安装Supervisor

```sh
yum install -y supervisor && systemctl enable supervisord && systemctl start supervisord
```

## 安装NodeJs12

```sh
curl -sL https://rpm.nodesource.com/setup_12.x | sudo bash - && yum install -y nodejs
```

## 安装git

```sh
yum install git -y
```

-----------------------------

## 创建数据库

```sh
mysql -uroot -p
<输入你的mysql root密码>

create DATABASE wookteam;
```

如果提示`You must reset your password using ALTER USER statement before executing this statement.`需要重置一下root密码

```
ALTER USER 'root'@'localhost' IDENTIFIED BY 'Aa111111.'; 
```

如果提示`Your password does not satisfy the current policy requirements`意思是密码太简单了，换一个复杂一点的


## 创建网站

### 克隆项目到您的本地或服务器

```sh
cd /var/www
git clone https://github.com/kuaifan/wookteam.git
cd wookteam
cp .env.example .env
chmod -R 777 storage
```

### 修改.env

```sh
vim .env
```

修改以下配置

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wookteam
DB_USERNAME=root
DB_PASSWORD=Aa111111.
```

### 设置项目

```sh
composer install
php artisan key:generate
php artisan migrate --seed

npm install
npm run production
```

### 创建Supervisor进程

```sh
vim /etc/supervisord.d/wookteam.ini
```

设置文件内容

```
[program:wookteam]
directory=/var/www/wookteam
command=php bin/laravels start -i
numprocs=1
autostart=true
autorestart=true
startretries=3
user=root
redirect_stderr=true
stdout_logfile=/var/www/wookteam/%(program_name)s.log
```

### 创建Nginx站点

```sh
vim /etc/nginx/conf.d/wookteam.conf
```

设置文件内容

```
map $http_upgrade $connection_upgrade {
    default upgrade;
    ''      close;
}
upstream swoole {
    # Connect IP:Port
    server 127.0.0.1:5200 weight=5 max_fails=3 fail_timeout=30s;
    keepalive 16;
}
server {
    listen 80;
    
    server_name  demo01.wookteam.com;  #你的域名
    root /var/www/wookteam/public;

    autoindex off;
    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri @laravels;
    }

    location =/ws {
        proxy_http_version 1.1;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Real-PORT $remote_port;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header Server-Protocol $server_protocol;
        proxy_set_header Server-Name $server_name;
        proxy_set_header Server-Addr $server_addr;
        proxy_set_header Server-Port $server_port;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection $connection_upgrade;
        # "swoole" is the upstream
        proxy_pass http://swoole;
    }

    location @laravels {
        proxy_http_version 1.1;
        proxy_set_header Connection "";
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Real-PORT $remote_port;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header Server-Protocol $server_protocol;
        proxy_set_header Server-Name $server_name;
        proxy_set_header Server-Addr $server_addr;
        proxy_set_header Server-Port $server_port;
        # "swoole" is the upstream
        proxy_pass http://swoole;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 重启服务

```sh
systemctl restart php-fpm && systemctl restart nginx && systemctl restart supervisord
```

到此安装完毕，希望你使用愉快！

## 默认账号

- admin/123456
- system/123456

## 升级更新

### 注意：在升级之前请备份好你的数据！

```sh
cd /var/www/wookteam/
git fetch --all
git reset --hard origin/master
git pull
composer update
php artisan migrate

npm install
npm run production

systemctl restart supervisord
```


