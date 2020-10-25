<?php
$www_root='/server/www/';
$www_root_user='web';
$www_root_group='web';
if(count($argv)!=2){
echo 'Usage : php '.$argv[0].' <虚拟主机域名> '."\n";
exit();
}
$domain=$argv[1];
$templete='server {
   listen 80;
   server_name {domain};
   root '.$www_root.'{domain};
   index index.html index.htm index.php;
   access_log  logs/{domain}.access.log  access;
   charset UTF-8;
   location = /favicon.ico {
      log_not_found off;
      access_log off;
   }
   #limit_req zone=req_one burst=120 nodelay;
   #limit_rate  20k;
   include common_core.conf;
   include common_php5.3.conf;
   include common_file.conf;
}
';
$templete=str_replace('{domain}',$domain,$templete);
$vhost_file="/server/tengine/conf/vhost/vhost_{$domain}.conf";
$www_folder=$www_root.$domain;
if(file_exists($vhost_file)){
  exit($vhost_file.' already exists.'."\n");
}
file_put_contents($vhost_file,$templete);
if(!is_dir($www_folder)){
    mkdir($www_folder);
}
echo `chown -R $www_root_user.$www_root_group $www_folder`;
echo `/server/tengine/nginx.sh reload`;
