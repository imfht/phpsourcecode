###　　MicroPHP是一个免费、开源、敏捷的MVC模式的PHP开发框架。它易拓展而不失灵活，可以轻松对应大中小型项目。MicroPHP没有大而全的功能，只给你最需要的底层功能，但是给你无限的灵活拓展能力。其它框架都有的基本特性这里就不一一罗列。 

# 文件说明:    
    1.MicroPHP.php是整个框架核心程序  

    2.index.php是入口文件同时也是配置文件  

    3.MicroPHP.min.php是压缩版本，建议生产环境替代MicroPHP.php。  

    4.plugin.php是插件模式要被包含的文件，插件模式要保证plugin.php和MicroPHP.min.php在一起，  
      或者修改plugin.php最后的包含MicroPHP.min.php的路径。  
    
    5.application是一个简单的项目结构   

官方首页：http://microphp.us/    
# composer方式安装最新版microphp:    
    1.新建一个空文件夹，比如 microphp  
    
    2.在microphp文件夹里面执行命令：  
      composer create-project snail/microphp  ./ dev-master  
      dev-master是最新版，也可以用版本号，比如：v2.3.1  

    3.在index.php里面的MpRouter::setConfig($system)的下面  
      执行包含：require dirname(__FILE__) . '/vendor/autoload.php';  

# 说一下MicroPHP的特性：  

    1. 整个框架核心就一个文件MicroPHP.php（压缩后的文件MicroPHP.min.php，140KB左右）。   

    2. 入口文件index.php也是整个项目的配置文件，一个入口文件就是一个项目的入口，可以  
       自定义多个入口文件达到不同项目共存的目的，项目之间随意共享类库、帮助文件等目录，  
       框架核心文件采用零侵入式编写，整个框架的运行不依赖任何外部变量，初始化的时候只  
       要通过MpRouter::setConfig($system)注入配置，然后MpRouter::loadClass()执行即可。   
    
    3. 独特的插件模式。  
    
    4. 项目控制器、模型、视图、类库等各种文件夹完全自定义，用到什么就建立什么文件夹，  
       彻底摆脱一堆无用的一大堆文件夹的困扰。  
    
    5. 类库采用懒加载模式，只要按着规定命名规则，然后把你的类库文件扔到类库文件夹，  
       在控制器或者模型中直接new 类库名()即可，系统会自动加载相应的类库文件。    
    
    6. 没有视图就不能指定视图数据？文件可以include共享那么视图为什么不能共享数据呢？  
       在MicroPHP里面你不再有这个困挠，MicroPHP可以在$this->view_vars数组里面存放  
       你的任何想在视图里面使用的全局数据。 特别是网站头部导航用户数据全站每个页面都  
       用到，那么可以自定义个控制器父类，然后和在父类构造方法里面初始化这个用户数据，  
       放到$this->view_vars里面，那么所有的控制器再也不用重复的去取用户数据传给视图。   
    
    7. 灵活的session托管,支持的管理类型：mongodb,mysql,memcache,redis.当然也可以用  
       系统默认的管理方式.session托管是可选的,根据项目具体情况择优选用即可。session  
       托管的好处很多，比如：支持分布式、精确控制session过期时间，等等。    
    
    8. 灵活的缓存机制。   
        (1).可用的方式缓存驱动有：auto,apc,sqlite,files,memcached,redis,wincache,xcache,memcache。   
            auto自动模式寻找的顺序是 : apc,sqlite,files,memcached,redis,wincache,xcache,memcache。   
        (2).缓存配置有个第二驱动机制，比如：当你现在在代码中使用的是memcached, apc等等，  
            然后你的代码转移到了一个新的服务器而且不支持memcached 或 apc这时候怎么办呢？  
            设置第二驱动即可，当你设置的驱动不支持的时候，系统就使用第二驱动。   

更多详细信息,请移步官网:http://microphp.us/