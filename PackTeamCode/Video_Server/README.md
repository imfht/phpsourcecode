# Video_Server ![badge-mit](https://img.shields.io/badge/LICENSE-MIT-brightgreen.svg) ![badge-php](https://img.shields.io/badge/PHP-7.2-brightgreen.svg)

## 开发进度

**本项目已经终止开发 下一个版本将使用新的语言发布在新的项目TCS(TransCodeServer)**
[TCS_Main](https://gitee.com/PackTeamCode/TCS_Main)

当前已测试通过可以运行在生产环境的版本:`![badge-php](https://img.shields.io/badge/Version-V0.3_Alpha-brightgreen.svg)

**请勿将Dev版本用于生产环境 Dev版本可能包含尚未完成的代码或未解决的BUG**

## 运行环境

系统要求:Windows 64Bit

运行库要求:VC2015(PHP要求)

## 使用教程

更多教程请前往Wiki查看:https://gitee.com/PackTeamCode/Video_Server/wikis

生产环境请下载Release版本

Step.1 安装MariaDB

>>Tip:安装时请设置自己的ROOT密码 并开启全局使用UTF-8编码

Step.2 修改设置

>>配置文件位于config/config.php 可修改Mysql,Redis等设置

Step.3 运行Run.cmd 即可启动服务

### 输出乱码

如低于Windows 10/Server 2016的版本运行发生乱码(无法正常显示颜色代码) 请用管理员命令行(cmd) 进入ANSION文件夹 使用此命令
```
ansicon.exe -I
```
然后关闭所有打开的命令行 并重新运行Run.cmd 即可解决

### 安全关闭服务

运行Stop.cmd后 将自动结束Nginx与CGI 但由于CGI保活机制 可能CGI会自动重启 直接关闭CGI窗口即可

等待Worker工作完毕后 可自行将主线程与Worker线程结束

>>Tip:结束机制还有待完善

## 加入我们

[Coding.NET项目地址](https://coding.net/u/haha_Dashen/p/Video_Server) | [码云项目地址](https://gitee.com/PackTeamCode/Video_Server)

此项目QQ群:674511398

PackTeam官方讨论群:296114195 

