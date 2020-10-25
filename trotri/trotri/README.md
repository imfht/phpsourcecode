Trotri
======

当前版本：beta-1.0.0，仅支持PHP5.3及以上版本，包含两部分：TFC框架(Trotri Foundation Classes缩写)、Trotri基于TFC开发的CMS系统。
___
<br>

##TFC - Trotri Foundation Classes
###整洁、快速、专业的PHP框架
TFC功能非常丰富，包括：DB、MVC、路由、缓存、日志、插件、多语言、模版零件、加解密、自动验证、身份验证、角色认证、图片水印、缩略图、验证码、程序引导、业务平台等。<br>
请点击右边 “Download ZIP” 按钮下载并解压。<br>
代码存放地址：“根目录/libraries/tfc”<br>
API文档存放地址：“根目录/docs/TFC-Api/index.html”<br>
包图类图存放地址：“根目录/docs/TFC-Api/graph_class.html”
___

##Trotri
###基于TFC开发的CMS系统
Trotri功能包括：自动生成代码、用户组、用户权限、文档（支持动态表单）、菜单、广告、文件批量上传、专题、投票、QQ和微信账号登录、多语言、支持插件、基于组件开发等。<br>
请点击右边 “Download ZIP” 按钮下载，下载后解压安装即可，直接用浏览器访问 “根目录/webroot/install.php” 文件进行安装。
___

##框架特色
1、Saf层：实现各种代理，如：DB操作代理、密钥代理、上传代理、Cookie代理、语言包，目的是基于配置实现各种操作，好处是方便修改和测试，如新增一个上传操作，只需要增加一个配置即可，不需要开发和测试成本。功能简要：<br>
  &nbsp;&nbsp;&nbsp;&nbsp;1)、配置：“DB信息”、“连接DB失败后，尝试重连的次数”等信息，就可以实现DB操作，不必再和底层的DB类打交道。并且自动打印SQL日志，方便测试。<br>
  &nbsp;&nbsp;&nbsp;&nbsp;2)、配置：“加密密钥”、“签名密钥”、“加密串有效期”可以实现加解密操作，如Cookie数据加解密、身份认证，不必再和底层的Mcrypt类打交道。<br>
  &nbsp;&nbsp;&nbsp;&nbsp;3)、配置：“允许上传的文件大小”、“目录规则”、“允许的文件后缀和类型”等信息，可以实现文件上传、Flash上传、Zip上传，不必再和底层的Upload类打交道。<br>
2、模板组件化和零件化，也称为components和widget，模板由各个易拆卸的零件拼装，每个零件都可维护多套CSS风格、每个组件都是一个独立的功能块。widget如：表单、表格、分页，components如：菜单、广告。<br>
3、Plugins，基于观测者模式实现，目的是实现易拆卸、易组合、实现功能时不必修改主程序。如新增文档是主程序，则“新增文档前过滤非法词语”、“给特殊词语加连接”、“新增文档后分词索引”、“保存扩展字段”，这些都是Plugin，都是基于配置顺序执行。<br>
4、独立和可重用的DB层和Service层，用户的终端有PC、Pad、微信、Wap等等，每个终端都是一个App，它们可以共用一套DB和Service逻辑，App中不必再次实现Service，只需选择需要用哪些Service。<br>
5、强大的生成代码功能，绝不生成简单而又无法实际使用的代码，生成代码过程：<br>
  &nbsp;&nbsp;&nbsp;&nbsp;1)、自动读取表结构，获取字段名、字段类型、默认值、注释，这些信息对应表单的Input-name、Input-type、Input-value、Input名，将这些数据导入生成代码表。<br>
  &nbsp;&nbsp;&nbsp;&nbsp;2)、手动添加表单字段分组，对字段选择各种验证规则，修改一些特殊字段的提示内容和Input类型。<br>
  &nbsp;&nbsp;&nbsp;&nbsp;3)、导出的代码包含SQL语句、表单、表格、表单验证、语言包、常量等。<br>
6、表格、表单和表单验证都基于配置，如：表格新增或删除一列、表单新增或删除一行、新增或删除一个字段验证，都只需简单的修改下配置。<br>
7、Controller层每个Action都是一个独立的文件，Action又细分为展示数据类和Ajax请求类。即减轻Controller层负担，又规范Action输出数据格式。<br>
8、可以用原生的SQL语句，这样更容易实现分表逻辑和复杂SQL，还支持自动组建SQL语句，组建SQL语句前，会自动缓存表结构，但是修改表结构后需要手动删除缓存。
___


####安装步骤：
1、系统要求：PHP5.3或以上版本、PDO支持。<br/>
2、下载并解压后，直接用浏览器访问 “根目录/webroot/install.php” 文件进行安装。<br/>
3、如果安装出错，请阅读 “根目录/webroot/docs/Install-Readme.txt” 文档。

####Linux环境下安装注意点：
Linux对目录权限要求严格，为了安装正确，先将下面几个目录权限设置为：可读可写可执行（chmod 777 目录名）<br>
1、根目录/cfg/db        - 数据库配置：安装时填写的数据库配置存放在该目录。<br>
2、根目录/cfg/key       - 密钥配置：安装时随机生成的加密密钥、签名密钥存放在该目录。<br>
3、根目录/log           - 日志目录：存放系统打印的Warning日志、SQL语句等日志。<br>
4、根目录/data/runtime  - 临时文件：存放用户权限数据、表结构、生成的代码等。<br>
5、根目录/data/u        - 上传目录：用户上传图片存放目录。<br>
___

<br/>
* Trotri技术交流群：178497611
* [新浪微博：@Trotri](http://weibo.com/u/3849507848 "Trotri官方微博") 
* [官方网站：trotri.com](http://www.trotri.com/ "官方网站：http://www.trotri.com/") 

###
        亲，若您有任何Bug反馈、功能建议、技术分享，请马上发邮件到trotri@yeah.net，感激涕零！
        注：若您给我们提供Bug反馈、功能建议、技术分享，就代表您授权我们在网站首页展示您的建议。

宋欢
trotri@yeah.net
