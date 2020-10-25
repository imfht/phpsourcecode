#!/bin/bash
#数据库信息
db_user=###db_user###
db_passwd=###db_passwd###
db_host=###db_host###
#文件名  
file_name=`date +%Y%m%d`.sql
webfile_name=`date +%Y%m%d`.web
backup_dir=###root_path###/data/backup/
www_dir=###wwwroot_path###/
#mysql编译后命令所在位置  
mysql=/usr/local/mysql/bin/mysql  
mysqldump=/usr/local/mysql/bin/mysqldump
 
#测试备份目录是否可写，如果不可写就报错退出  
test ! -w $backup_dir && echo "Error: $backup_dir is unwrite." && exit 0 

#检测最旧的备份数据库是否存在，如果在就删掉
test -d "$backup_dir/backup.7/" && rm -rf "$backup_dir/backup.7" 

#循环修改备份数据库目录的编号，记录新旧程度  
for i in 6 5 4 3 2 1 0
do 
    if(test -d "$backup_dir"/backup."$i")  
    then  
        next_i=`expr $i + 1`  
        mv "$backup_dir"/backup."$i" "$backup_dir"/backup."$next_i" 
    fi  
done  

#测试备份目录中最新备份文件夹是否存在，如果不在就创建  
test ! -d "$backup_dir/backup.1/" && mkdir "$backup_dir/backup.1" 
   
#制定要备份的数据库
for db in ###db_name###  
do 
    $mysqldump -u $db_user -h $db_host -p$db_passwd --opt -B $db | gzip -6 > "$backup_dir/backup.1/$db.$file_name.gz"
done 

#制定要备份的WEB目录(如果WEB巨大,则注释此段代码,建议采用人工方式)  
for www in ###www_name###
do 
    tar -zcvf $backup_dir/backup.1/$www.$webfile_name.tar.gz --directory=$www_dir$www --exclude=./data/backup ./
done 

exit 0;
