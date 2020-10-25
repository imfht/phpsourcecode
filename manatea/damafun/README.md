## 简介

* damafun 
* 是个人用于学习php而开发的弹幕视频平台，平台采用了FFMPEG转码技术以及CCL核心弹幕库插件。


## 安装须知
* 项目基于thinkphp3.2.2平台，需要php5.3及以上版本。
* 1.已编译过的ffmpeg.exe文件存放在public/api目录下,若要使用平台中的文件上传功能则需设置ffmpeg.exe的环境变量。
* 2.项目同样使用了ffmpegphp插件放置在public/api目录下。
* 3.项目的常量设定及配置文件均放置于./index.php以及application/common/conf/config.php中。
* 4.请自行根据上传文件的大小配置php.ini，配置页面等待时间：
	* file_uploads = on ;//是否允许通过HTTP上传文件的开关。默认为ON即是开
	* upload_tmp_dir ;//文件上传至服务器上存储临时文件的地方，如果没指定就会用系统默认的临时文件夹
	* upload_max_filesize = 1024m ;//望文生意，即允许上传文件大小的最大值。默认为2M,我们设置为1G
	* post_max_size = 1024m ;//指通过表单POST给PHP的所能接收的最大值，我们也设置为1G
	* max_execution_time = 3600 ;//每个PHP页面运行的最大时间值(秒)，默认30秒，设置为一小时，因为后面转码时间很久。
	* max_input_time = 3600 ;//每个PHP页面接收数据所需的最大时间，默认60秒
	* memory_limit = 8m ;//每个PHP页面所吃掉的最大内存，默认8
* 5.数据库表关系保存在db_oldama1.sql中
*
*


## 2015/11/38更新须知
* 1.添加了评分功能，用户可对于视频进行评价。
* 2.添加了基于视频的过滤算法，用于向用户推荐相关视频。
* 3.数据库添加了cz_recom表，见sqlin.sql中
* PS:需要说明的是：cz_recom中per列为TEXT类型，其存储数据按照“用户id:评分”，数据间用逗号分隔
*


## Author: Chouney Zhang <mysgame@sina.com>


