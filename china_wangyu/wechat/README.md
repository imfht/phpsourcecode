# WeChat

- 微信基础授权
- 微信用户信息
- 微信token
- 微信模板
- 微信自定义菜单生产
- 微信JDK
- 微信关键字回复
- 微信模板消息发送
- 基础功能

> 本扩展功能的运行环境要求`PHP5.6`以上。
> 本扩展 `1.0.5` 及以上版本，运行环境要求`PHP7.2`以上。

>### 使用 `git` 安装

~~~

    码云   ：git@github.com:china-wangyu/WeChat.git

~~~

>### 使用 `composer`  安装

#### 由于众所周知的原因，国外的网站连接速度很慢。因此安装的时间可能会比较长，我们建议通过下面的方式使用国内镜像。打开命令行窗口（windows用户）或控制台（Linux、Mac 用户）并执行如下命令：
~~~

    composer config -g repo.packagist composer https://packagist.phpcomposer.com
~~~

#### 使用： 在composer.json添加

    "require": {
        "china-wangyu/WeChat": "^1.0.0"
    },

#### 然后(命令行)：

    composer update


## 接口使用说明

### 接口目录

~~~
WeChat         模块目录

├─ Core        核心目录
        ├─Base.php               抽象基类，主要用户放置一些公用的方法体
        
        ├─User.php               获取微信授权、用户openid、用户信息
        
        ├─Token.php              获取微信access_token (考虑token时限，已用 $_SESSION['access_token'] 储存)
        
        ├─Ticket.php             获取微信jsapi_ticket、获取微信JDK签名 （考虑微信jsapi_ticket时限、已用 $_SESSION['jsapi_ticket'] 储存）
        
        ├─Template.php           获取微信所有消息模板、格式化微信消息模板 （考虑微信消息模板变量问题、及消息发送，以将需要参数存放在$变量名['param']）
        
        ├─Send.php               微信模板消息发送、微信关键字回复、微信自定义菜单生成
        
        ├─QrCode.php             微信生成二维码
        
        ├─Menu.php               微信菜单
        
        ├─Material.php           微信素材
        
        ├─Authorize.php          微信授权认证类
        
├─ Extend         依赖目录

         ├─File.php                 文件存储类。
                
         ├─Json.php                 Json返回类。
        
         ├─Request.php              curl请求封装类。
         
         ├─Tool.php              工具类。
         
├─ Lib            第三方目录
         
         ├─phpqrcode.php                 PHP生成二维码类。 
~~~


## 微信用户 `User`

### 微信授权、获取 `code`
~~~
    * [code 重载http,获取微信授权]
    * @param  string   $appid           [微信公众号APPID]

    \WeChat\Core\User::code('微信appid');  # 重载微信授权
~~~


### 微信用户 `openid`
~~~

    * [openid 获取用户 OPENID]
    * @param  string  $code                         [微信授权CODE]
    * @param  string  $appid                        [微信appid]
    * @param  string  $appSecret                    [微信appSecret]
    * @param  boolen  $type                         [true:获取用户信息  false:用户openid]
    * @return [array] [用户信息 用户openid]

    \WeChat\Core\User::openid(input('get.code'), '微信appid', '微信appSecret');
~~~


### 微信用户信息 `userinfo` (1种： 没有获取`openid`时)
~~~

    * [openid 获取用户 OPENID]
    * @param  string  $code                         [微信授权CODE]
    * @param  string  $appid                        [微信appid]
    * @param  string  $appSecret                    [微信appSecret]
    * @param  boolen  $type                         [true:获取用户信息  false:用户openid]
    * @return [array] [用户信息 用户openid]

    \WeChat\Core\User::openid('获取GET方式的参数code', '微信appid', '微信appSecret', true);
~~~


### 微信用户信息 `userinfo` (2种： 获取`openid`时)
~~~

    * [userInfo 获取用户信息]
    * @param  [type] $access_token   [授权获取用户关键参数：access_token]
    * @param  [type] $openid         [用户openid]

    \WeChat\Core\User::userInfo($access_token, $openid);
~~~

### 微信用户信息 `newuserinfo` (3种： 获取`access_token`时)
~~~

    * [userInfo 获取用户信息]
    * @param  [type] $access_token   [普通access_token]
    * @param  [type] $openid         [用户openid]

    \WeChat\Core\User::newUserinfo($access_token, $openid);
~~~


## 微信 `Token`

### 获取 `access_token`
~~~

    * [gain 获取微信access_token]
    * @param  string   $appid                 [微信AppID]
    * @param  string   $appSecret             [微信AppSecret]
    * @return [string] [微信access_token]

    \WeChat\Core\Token::gain('微信appid', '微信appSecret');  # 获取微信access_token
    
    成功：
            array{
                'access_token'=>'**************************************',
                'expires_in'=> ****,
            }
            
    失败：
        array{
                    'errormsg'=>'*************',
                    'errorcode'=> ***,
                }
~~~


## 微信 `Ticket`

### 微信 `jsapi_ticket`
~~~

    * [gain 微信jsapi_ticket]
    * @param  string   $access_token          [微信token]
    * @return [string] [微信jsapi_ticket]

    \WeChat\Core\Ticket::gain('微信普通token');
~~~


### 微信 `JDK` 签名
~~~

    * [sign 获取微信JSDK]
    * @param  [string] $ticket        [获取微信JSDK签名]
    * @return [array]  [微信JSDK]

    \WeChat\Core\Ticket::sign('微信jsapi_ticket');
    
    成功：
        array{
            "errcode"=>0,
            "errmsg"=>"ok",
            "ticket"=>"kgt8ON7yVITDhtdwci0qeQM5xOrtZmRriogQ-yl-zBNBoXs56JmAWkbJVY68uajNBIIR4xa5t_dx9W0X6a-tnQ",
            "expires_in"=>7200,
            "time"=>1543832364
        }
            
    失败：
        array{
            'errormsg'=>'*************',
            'errorcode'=> ***,
        }
~~~


## 微信模板消息 `Template`

### 获取所有模板 `gain`
~~~

    * [gain 获取所有消息模板内容]
    * @param  string $accessToken    [微信token]
    * @return [type] [description]

    \WeChat\Core\Template::gain('微信token');
~~~


## 微信推送 `Send`

### 关键字推送 `keyWord`
~~~

     * 被动回复消息
     * @param array $triggerConfig 微信消息对象
     * @param array $triggerData   用户数据
     
    \WeChat\Core\Send::trigger($triggerConfig, $triggerData);

    例如： 使用说明见Authorize类使用方法
        
~~~


### 模板消息推送 `msg`
~~~

    * [msg 发送模板消息]
    * @param  string $accessToken [微信token]
    * @param  string $templateid [模板ID]
    * @param  string $openid     [用户openid]
    * @param  array  $data       [模板参数]
    * @param  string $url        [模板消息链接]
    * @param  string $topcolor   [微信top颜色]
    * @return [ajax] [boolen]

    \WeChat\Core\Send::msg($accessToken, $templateid, $openid, $data = [], $url = '', $topcolor = '#FF0000');
~~~

## 微信菜单 `Menu`

### 获取菜单 `gain`
~~~

    * [gain 获取菜单]
    * @param  string $accessToken [微信token]
    * @return [array] [微信返回值：状态值数组]

    \WeChat\Core\Menu::gain($accessToken);
~~~


### 设置菜单 `set`
~~~

    * [set 生成菜单]
    * @param  string $accessToken [微信token]
    * 例如：$menu =[
                    [
                         'type'=> 'click', //
                         'name'=> '这是第一级button',
                         'list' => [
                            [
                                 'type'=> 'view',
                                 'name'=> '百度',
                                 'url' => 'http://www.baidu.com',
                             ]
                         ],
                    ],
                     [
                         'type'=> 'miniprogram',
                         'name'=> 'xx小程序',
                         'url' => 'http://www.baidu.com',
                         'appid' => 'asdasdas', 小程序APPID
                         'pagepath' => '/page/index/index', // 小程序页面链接
                     ]
                 ];
    * @param  array   $menu                                  [菜单内容 ]
    * @return [array] [微信返回值：状态值数组]

    \WeChat\Core\Menu::set($accessToken, $menu);
~~~


## 二维码 `Qrcode`

### 微信带参二维码 `wechat`
~~~

    * 创建微信带参二维码生成
    * @inheritdoc 详细文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1443433542
    * @param string $accessToken 授权TOKEN
    * @param string $scene_str 字符串
    * @param string $scene_str_prefix 字符串前缀
    * @param int $type 二维码类型：(小于等于1) = 有效时长30天  (大于等于2) = 永久
    * @return array|bool|mixed
    
    \WeChat\Core\QrCode::wechat(string $accessToken,string $scene_str, string $scene_str_prefix = 'wene_', int $type = 1)
    
    成功：
        array(4) {
          ["ticket"] => string(96) "ticket字符串"
          ["expire_seconds"] => int('时长')
          ["url"] => string(45) "二维码内容"
          ["showUrl"] => string(147) "在线地址"
        }
        
    失败：
        array{
            'errormsg'=>'*************',
            'errorcode'=> ***,
        }
~~~


### 创建二维码 `create`
~~~

        * 生成二维码
        * @inheritdoc 文档说明：http://phpqrcode.sourceforge.net/
        * @param string $text 二维码内容
        * @param bool $filePath    二维码储存路径
        * @param string $level 二维码容错机制
        * @param int $size 点大小
        * @param int $margin   点间距
        * @param bool $saveandprint    保存或打印
        * @return string|void
              
        public static function create(string $text = '',
                                     bool $filePath = false,
                                     string $level = QR_ECLEVEL_L,
                                     int $size = 6,
                                     int $margin = 2,
                                     bool $saveandprint=false)
                                       
        使用方式：
        
        1. 生成二维码，但不生成二维码文件
        $qrocde = \WeChat\Core\QrCode::create('二维码内容');
        
        2. 生成二维码文件
        $qrocde = \WeChat\Core\QrCode::create('二维码内容','文件存放路径');
~~~


##  文件参数储存 `File`
~~~

      * 文件参数储存，可扩展
      * @param string $var  key
      * @param array $val value
    
    // 存值
    \WeChat\Extend\File::param('key','value');
    
    // 取值
    \WeChat\Extend\File::param('key');
~~~


## 微信素材类 `Material`
###  获取素材总数 `getMaterialCount`
~~~

    * 获取素材总数
    * @param string $access_token 普通授权token
    * @return array
    
    \WeChat\Core\Material::getMaterialCount($access_token);
~~~

###  获取素材列表 `getMaterialCount`
~~~

    * 获取素材列表
    * @param string $access_token 普通授权token
    * @return array
    
    \WeChat\Core\Material::getMaterialList($access_token);
~~~

##  微信授权认证 `抽象` 类 `Authorize` 
###  对接微信开发者模式 

>    使用方式

-  第一步：创建自己的微信开发者对接类继承   微信授权认证 `抽象` 类 `Authorize`

~~~
    <?php
    
    class Wechat extends \WeChat\Core\Authorize
    {
       /**
        * 设置与微信对接的TOKEN凭证字符
        * Authorize constructor.
        * @param string $token 微信开发模式TOKEN字符串
        * @param string $appID 微信appid
        * @param string $appScret 微信appScret
        * @inheritdoc 详细文档：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=1833550478&lang=zh_CN
        */
        public function __construct($token, $appID, $appScret)
        {
            parent::__construct($token, $appID, $appScret);
        }
    }
~~~ 

-  第二步：重写微信事件方法和用户输入方法

~~~

    /**
         * 首次关注事件
         * @return mixed|void
         */
        public function follow()
        {
            // TODO: Implement follow() method.
            $sendMsg = '首次关注，类型：搜索或朋友分享推荐';
            $this->text($sendMsg);
        }
    
        /**
         * 扫码关注事件
         * @return mixed|void
         */
        public function scanFollow()
        {
            // TODO: Implement scanFollow() method.
            $this->text('扫码关注');
        }
    
        /**
         * 点击事件
         * @return mixed|void
         */
        public function click()
        {
            // TODO: Implement click() method.
            $this->text('这个是用户点击事件~');
        }
    
        /**
         * 扫码商品事件
         * @return mixed|void
         */
        public function scanProduct()
        {
            // TODO: Implement scanProduct() method.
            $this->text('用户商品扫码');
        }
    
        /**
         * 扫码事件
         * @return mixed|void
         */
        public function scan()
        {
            // TODO: Implement scan() method.
            $this->text('扫码进入');
        }
    
        /**
         * 用户输入
         * @return mixed|void
         */
        public function input()
        {
            // TODO: Implement input() method.
            $this->text('用户输入' );
        }
~~~

###  可返回消息的模板和方法简介

> 只在继承微信开发者模式对接类`Authorize`里面使用

~~~
    $this->userInfo;  // 微信用户信息。
~~~

###  可返回消息的模板和方法简介

> 只在继承微信开发者模式对接类`Authorize`里面使用

- 推送文本消息 `text`

~~~
    
    * 发送文本消息
    * @param string $content 回复的文本内容
    
    $this->text($content);
~~~

- 推送图片消息 `image`

~~~
    
    * 发送图片消息
    * @param string $mediaId 素材ID
    
    $this->image($mediaId)
~~~

- 推送音频消息 `voice`

~~~
    
    * 发送语音消息
    * @param string $mediaId 素材ID
    
    $this->voice($mediaId)
~~~

- 推送视频消息 `video`

~~~
    
    * 发送视频消息
    * @param string $mediaId 素材ID
    * @param string $title 视频标题
    * @param string $description   视频消息的描述
    
    $this->video($mediaId, $title, $description)
~~~

- 推送音乐消息 `music`

~~~
    
    * 发送音乐消息
    * @param string $title 消息标题
    * @param string $description   描述
    * @param string $musicURL  音乐链接
    * @param string $HQMusicUrl    高清音乐URL
    * @param string $ThumbMediaId  缩略图的媒体id，通过素材管理中的接口上传多媒体文件，得到的id
    
    $this->music( $title, $description, $musicURL, $HQMusicUrl, $ThumbMediaId )
~~~

- 推送图文消息 `news`

~~~
    
    /**
     * 发送图文消息
     * @param array $Articles 图文数组
     * @format 格式 $Articles = array(
                                    array(
                                       'Title'=>'标题',
                                      'Description'=>'注释',
                                      'PicUrl'=>'图片地主（含域名的全路径:图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200）',
                                      'Url'=>'点击图文消息跳转链接'
                                    ),
                                );
     */
    
    $this->news($Articles)
~~~


>| 注：如有疑问，请联系邮箱 china_wangyu@aliyun.com


>| 或，请联系QQ 354007048 / 354937820