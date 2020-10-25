备份部署步骤：
1、smbmount debain安装 
sudo apt-get install smbfs
　　
2、挂载备份主机目录
服务器建立挂载名如: mkdir /home/backup
建立挂载命令:smbmount //58.222.157.2/backup_folder /home/backup -o username=administrator,password=******

注：取消挂载命令 umount /home/backup
　　
3、定时任务shell脚本
备份脚本：site_backup 

4、rontab_task 定时任务集 加载到 www 用户的crontab 任务中(www用户需要对备份目录有权限读写/所有)
root> crontab -u www /home/wwwroot/haier_tpm/source/module/crontab/crontab_task

增加如下一行
0 0 * * * /path/site_backup > /dev/null 2>&1

每天零点运行一次备份
注定时格式说明

定时周期	定时格式
每分钟执行五次	*/5 * * * * /path/site_backup > /dev/null 2>&1
每小时执行		0 * * * * /path/site_backup > /dev/null 2>&1
每天执行		0 0 * * * /path/site_backup > /dev/null 2>&1
每周执行		0 0 * * 0 /path/site_backup > /dev/null 2>&1
每月执行		0 0 1 * * /path/site_backup > /dev/null 2>&1
每年执行		0 0 1 1 * /path/site_backup > /dev/null 2>&1
　　
5、查看定时任务是否已加载
root> crontab -l -u www

0 0 * * * /home/wwwroot/haier_tpm/source/module/crontab/cron_backup_trigger

或者建议
 crontab_task 定时任务集 加载到 www 用户的crontab 任务中
