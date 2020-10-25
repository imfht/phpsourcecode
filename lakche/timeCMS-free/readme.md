#timeCMS
    本系统主要为新手上手laravel的示例，完全免费开源，各位可以放心大胆的使用
    本系统主要适合做个人博客，企业门户，工作室网站等
    本系统自带一套简单模板

    开源协议：MIT License http://opensource.org/licenses/MIT
    演示网站：探索者日志 http://www.obday.com
    时光CMS，基于laravel5.1的开源CMS系统。时光流逝那些朦胧的回忆，只留下最值得珍惜的瞬间。

###版本说明
    最新上线版本为1.0版本，也是第一个上线版本，欢迎大家使用
    上个版本已完成：系统设置，分类管理，文章管理，单页管理，用户管理，迁移文件，种子文件
    本期开发中：使用说明，单元测试，图集模型
    本期已完成：人物模型，项目模型，基本缓存

    每次升级维护的时候可以开启维护模式
    php artisan down 打开维护模式
    php artisan up 关闭维护模式
    维护模式下网站任何页面均将显示503错误模板

    本系统已自带debug工具，在开发模式下，屏幕最下方将出现debug工具条，可以查看项目信息。
    在生产模式中，不会出现debug工具条，请修改.env文件的APP_DEBUG=false

    系统中有很多地方尚未规范，比如部分功能重复，将在基础功能完善之后规范一次代码

###单元测试：
    在网站根目录运行以下命令即可
    vendor\bin\phpunit tests\****
    ****为要测试的类

###主题功能：
    同一个主题的模板都放在resources\views的同一个目录下面，比如time
    需要使用主题模板的控制器引用下Theme类，使用Theme类的view方法，例子如下
    use Theme;
    class WelcomeController extends Controller
    {
        public function index()
        {
            return Theme::view('welcome.index');
        }
    }
    Theme类的view方法，语法与laravel原来的view一样。
    view自动向模板传递参数$theme，模板引用的地方写成@include($theme.'/xxx')格式即可
    模板类文件为 app/Libs/Theme.php
    目前主题直接定义在该文件中，后期将改为数据库保存模式，方便用户在后台修改
    增加主题功能的目的是为了方便用户快速切换主题
    既用户下载主题包后放在resources\views文件夹下，就可以直接在后台切换主题
    主题对应样式文件，建议放在public文件的相应目录下面，比如time

###安装说明
     请尽量在linux系统（推荐Debian或者Ubuntu及拓展的发行版本）下执行下面的操作
     复制代码仓库
        git clone http://git.oschina.net/lakche/timeCMS-free.git timecms
     安装所需插件
        composer install
     如果一直失败，或提示找不到某些插件，可以先执行下面指令再安装插件
        composer config -g repositories.packagist composer http://packagist.phpcomposer.com
     复制.env.example重命名为.env
        cp .env.example .env
     生成APP_KEY
        php artisan key:generate
     打开.env文件，配置数据库连接账号和密码
        DB_HOST=localhost
        DB_DATABASE=timecms
        DB_USERNAME=root
        DB_PASSWORD=123456
     执行数据迁移文件
         php artisan migrate
     执行种子文件
         php artisan db:seed
     如果执行种子文件提示类未找到，请执行
         composer dumpautoload -o
     如果你的IDE支持，比如PhpStorm，可以执行下面指令增强提示功能
         php artisan ide-helper:generate
     网站架设完成
        默认管理员账户：admin，密码：timecms

###更多说明
    有什么不明白或者对系统有意见的，可以访问官网：www.obday.com
    或者联系本人QQ:402227052