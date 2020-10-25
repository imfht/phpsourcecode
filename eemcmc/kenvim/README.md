#kenvim


效果图见： <img src="http://dl2.iteye.com/upload/attachment/0101/3178/594b33ab-2bbf-3ecd-8b82-701e182dc779.png">

个人博客：http://vb2005xu.iteye.com/blog/2118919

色色的vim配置文件集成版本，你是不是总纠结于vim的高大尚，你是不是总被vim粉讥笑成小鸟，下定决心来试着使用下vim，但是不是又被坑爹的配置吓到了，国内的文档层次参差不齐，互相抄袭，也不知道哪个是对的，所以我下定决心自己来整一个，现在将其开放给像我一样的vim小白，希望大家不用在纠结于此了

安装步骤：

1. 将下载的文件解压缩到 你的用户目录，比如我是 /home/kenxu/softken/kenfiles/kenvim

然后

2. 建2个软链接就搞定了

ln -s /home/kenxu/softken/kenfiles/kenvim/vimfiles ~/.vim

ln -s /home/kenxu/softken/kenfiles/kenvim/_vimrc ~/.vimrc

如果你没装 ctags 的话，请手动装下吧，比如centos 就sudo yum install ctags

debian:sudo apt-get install ctags

startos: sudo yget --install ctags

仓库代码： http://git.oschina.net/eemcmc/kenvim

百度云下载地址：http://yun.baidu.com/s/1ntjxD9J



== ctags ==

ctags在http://ctags.sourceforge.net/下载源码，编译后安装。常规的标记命令为 ctags -R 。"-R"表示递归创建，也就包括源代码根目录下的所有子目录下的源程序

代码提示快捷键如下：

本页内文字提示： ctrl + N 或者 ctrl + P

提示php内置函数，可以 ctrl + x , ctrl + o 来





F10 切换目录树

F5 检查php语法

F3 切换缓冲区

F8 切换taglist窗口

 

新增对PHP 命名空间支持

 

新增自动在打开vim时生成对应的tags文件，在目录下只要新增 vimscri

 

列出几个常用的快捷键

 

,cu 取消注释

,cc 使用注释

 

tags 跳转

 

ctrl + ] 转到函数声明处

ctrl + T 返回

：help tags 获取帮助

----------------------------------------------------
替换 taglist 为 tagbar 并新增tagbar-php插件，使得PHP变量在tagbar只显示的更好

诸位需要将 
sudo ln -s /home/kenxu/softken/kenfiles/kenvim/phpctags /bin/phpctags

要使用 phpctags 要求PHP必须开启了 phar扩展

效果图见： <img src="http://git.oschina.net/eemcmc/kenvim/raw/master/screenshots/img-02.png">


== 某些快捷键 ==
mac 下使用鼠标效果,请按住 alt

esc->":w !pbcopy"(把当前文件复制到剪贴板)
esc->":r !pbpaste"(把剪贴板内容粘贴)
esc->"set paste" 然后快捷键粘贴最后 esc->"set nopaste"退出这个模式
