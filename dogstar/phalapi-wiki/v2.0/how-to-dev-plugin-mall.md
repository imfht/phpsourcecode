# 茶店微信小程序应用开发实战

下面将以茶店微信小程序为例，实战讲解如何在PhalApi上开发一个应用，并且进行发布销售。

此应用项目实战主要包括以下几部分：  
 + 微信小程序（客户端部分）
 + API接口
 + 运营平台茶店运营平台的后台界面

当前实战示例的应用属于半成品，你可以基于此应用继续开发，改善升级成你自己的应用或插件。

> 视频教程链接：[PhalApi茶店微信小程序应用开发实战教程](https://www.bilibili.com/video/av95817153)

## 最终项目的实现效果

### 插件上架后的展示效果

商品地址：http://www.yesx2.com/phalapi-mini-tea  

![](http://cdn7.okayapi.com/yesyesapi_20200312174646_c11cdee922c66706ffa2b5c16900ef2c.png)

> 温馨提示：可购买此配套的完整源代码进行学习，效果更佳。  


### 微信小程序（客户端部分）
微信小程序包括以下功能和页面：
 + 登录授权
 + 商城首页
 + 商品详情页
 + 购买页
 + 下单页
 + 朋友圈发现页
 + 我的页面（含我的订单、我的购物车等页面）

以下是实现的效果：

登录授权  
![](http://cdn7.okayapi.com/yesyesapi_20200312161449_efd01b62808b98b1a2b0ae7840f250d6.png)

商城首页   
![](http://cdn7.okayapi.com/yesyesapi_20200312161514_5df9836c78a648a63bfab4799d9adc9d.png)

商品详情页  
![](http://cdn7.okayapi.com/yesyesapi_20200312161539_e620953a7df44b61827ffb216438c018.png)

购买页   
![](http://cdn7.okayapi.com/yesyesapi_20200312161558_72e33a6ce66cf468cbe40bf1d2335584.png)

朋友圈发现页  
![](http://cdn7.okayapi.com/yesyesapi_20200312161618_1299fbe415b777a7faa592617d293dc5.png)

我的页面  
![](http://cdn7.okayapi.com/yesyesapi_20200312161642_53ed889efb0d67f955f8436997ada73a.png)

我的订单  
![](http://cdn7.okayapi.com/yesyesapi_20200312161658_b89a0e5509d217fdde2abf0a6a77f863.png)

### 前台API接口
前台的接口，也就是提供给客户端微信小程序调用的接口。根据自己的应用需求开发，这里有以下接口：

 + App.PhalApi_MiniTea_Tea.AddToShopCar 添加到购物车
 + App.PhalApi_MiniTea_Tea.CreateNewOrder 创建订单
 + App.PhalApi_MiniTea_Tea.GetTeaDetail 获取商品详情
 + App.PhalApi_MiniTea_Tea.GetTopSwiper 获取首页轮播图
 + App.PhalApi_MiniTea_Tea.PostMoment 发布发现朋友圈
 + App.PhalApi_MiniTea_Tea.QueryList 获取首页商品列表
 + App.PhalApi_MiniTea_Tea.QueryMomentList 查看朋友圈
 + App.PhalApi_MiniTea_Tea.QueryMyOrderList 查询我的订单
 + App.PhalApi_MiniTea_Tea.QueryMyShopCar 我的购物车
 + App.PhalApi_MiniTea_Tea.UserLogin 微信小程序用户授权登录

![](http://cdn7.okayapi.com/yesyesapi_20200312162947_65d177fe8feeb76409f9c833e27cecea.png)

### 运营平台界面

配置的运营平台管理界面，目前只是实现了一个订单查看页面，其他页面可自行添加。

![](http://cdn7.okayapi.com/yesyesapi_20200312162046_02a6d27a182469487fcc4d93559a914f.png)

下面介绍如何进行实战开发。

## 创建新插件

首先，进入项目根目录，执行命令：  
```
$ php ./bin/phalapi-plugin-create.php phalapi_mini_tea
开始生成插件json配置文件……
/Users/dogstar/projects/github/phalapi/plugins/phalapi_mini_tea.json json配置文件生成 ok 

开始创建插件文件和目录……
/Users/dogstar/projects/github/phalapi/public/../config/phalapi_mini_tea.php... 
/Users/dogstar/projects/github/phalapi/public/../plugins/phalapi_mini_tea.php... 
/Users/dogstar/projects/github/phalapi/public/../data/phalapi_mini_tea.sql... 
/Users/dogstar/projects/github/phalapi/public/../public/portal/phalapi_mini_tea... 
/Users/dogstar/projects/github/phalapi/public/../public/portal/page/phalapi_mini_tea/index.html... 
/Users/dogstar/projects/github/phalapi/public/../src/app/Api/Phalapiminitea/Main.php... 
/Users/dogstar/projects/github/phalapi/public/../src/app/Domain/Phalapiminitea/Main.php... 
/Users/dogstar/projects/github/phalapi/public/../src/app/Model/Phalapiminitea/Main.php... 
/Users/dogstar/projects/github/phalapi/public/../src/portal/Api/Phalapiminitea/Main.php... 
插件文件和目录生成 ok 

开始添加运营平台菜单……
phalapi_mini_tea插件菜单添加 ok 

恭喜，插件创建成功，可以开始开发啦！
```

## 开发前台API接口

上面只是创建了一个插件的骨架，熟悉后可以自由发挥，根据自己的插件情况进行灵活调整。

我们先从为微信小程序客户端提供前台接口开始，并且从商城首页开始。具体讲解怎么进行开发。

商城首页   
![](http://cdn7.okayapi.com/yesyesapi_20200312161514_5df9836c78a648a63bfab4799d9adc9d.png)

商城首页，需要提供接口获取商品列表。所以，需要根据ADM分层模式在src/app目录里，也就是App命名空间下，添加相关的代码。  

生成插件骨架代码时，已经给我们生成了以下代码和目录：  
```
src/app/Domain/Phalapiminitea/
src/app/Model/Phalapiminitea/
src/portal/Api/Phalapiminitea/
```

但当你熟悉后，我们推荐命名方式采用【开发者/公司/组织名字】+【插件名称】进行命名。例如PhalApi的茶店微信小程序，代码在：  
```
src/app/Api/PhalApi/MiniTea
src/app/Domain/PhalApi/MiniTea
src/app/Model/PhalApi/MiniTea
```

为方便管理，同时推荐并且建议把同一个插件内的全部前台接口都放在同一个接口类里。例如上面都以：```App.PhalApi_MiniTea_Tea.```为开头，都在```App\Api\PhalApi\MiniTea\Tea```接口类里。

![](http://cdn7.okayapi.com/yesyesapi_20200312162947_65d177fe8feeb76409f9c833e27cecea.png)

以刚才首页获取商品接口为例，我们开始开发第一个接口：App.PhalApi_MiniTea_Tea.QueryList，获取首页商品列表。

新建文件：src/app/Api/PhalApi/MiniTea/Tea.php，API相关代码如下：
```php
<?php
namespace App\Api\PhalApi\MiniTea;

use PhalApi\Api;
use App\Domain\PhalApi\MiniTea\Tea as TeaDomain;

/**
 * 茶店微信小程序
 */
class Tea extends Api {
    public function getRules() {
        return array(
           'queryList' => array(
               'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'require' => true, 'desc' => '第几页'),
               'perpage' => array('name' => 'perpage', 'type' => 'int', 'default' => 10, 'require' => true, 'desc' => '分页数量'),
           ),
       );
    }

    /**
     * 获取首页商品列表
     * @desc 获取小程序首页列表数据
     */
    public function queryList() {
        $rs = array('total' => 0, 'items' => array());
        $domin = new TeaDomain();
        $rs['total'] = $domin->getTotal();
        $rs['items'] = $domin->queryList($this->page, $this->perpage);
        return $rs;
    }
}
```

接下来，创建Domain层的对应的文件：src/app/Domain/PhalApi/MiniTea/Tea.php，实现代码。  
```php
<?php
namespace App\Domain\PhalApi\MiniTea;
use App\Model\PhalApi\MiniTea\Tea as TeaModel;

class Tea {
    public function getTotal() {
        $model = new TeaModel();
        return $model->getTotal();
    }

    public function queryList($page, $perpage) {
        $model = new TeaModel();
        return $model->queryList($page, $perpage);
    }
}
```

最后，是Model层，也就是操作数据库的地方。添加文件：src/app/Model/PhalApi/MiniTea/Tea.php，保存代码。
```php
<?php
namespace App\Model\PhalApi\MiniTea;
use PhalApi\Model\NotORMModel;

class Tea extends NotORMModel {

    public function getTableName($id) {
        return 'phalapi_mini_tea';
    }

    public function getTotal() {
        return $this->getORM()->count();
    }

    public function queryList($page, $perpage) {
        return $this->getORM()
            ->limit(($page - 1) * $perpage, $perpage)
            ->fetchAll();
    }
}
```

这里面，需要一张数据库表```phalapi_mini_tea```，因此先设计好这个表结构，然后保存到data/phalapi_mini_tea.sql数据库文件中，后面客户安装时需要用到。  
```sql
DROP TABLE IF EXISTS `phalapi_mini_tea`;
CREATE TABLE `phalapi_mini_tea` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `tea_name` varchar(255) NOT NULL COMMENT '茶名',
        `tea_desc` varchar(255) DEFAULT NULL COMMENT '茶描述',
        `tea_price` double(100,2) NOT NULL COMMENT '茶价格',
        `tea_titlepage` varchar(255) DEFAULT NULL COMMENT '茶封面',
        `tea_picture1` varchar(255) DEFAULT NULL COMMENT '茶轮播照片1',
        `tea_picture2` varchar(255) DEFAULT NULL COMMENT '茶轮播照片2',
        `tea_picture3` varchar(255) DEFAULT NULL COMMENT '茶轮播照片3',
        `tea_tag` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '茶的标签（热门，包邮，紧张等）',
        `tea_specification` varchar(255) DEFAULT NULL COMMENT '茶的规格 ',
        `tea_presentation_img1` varchar(255) DEFAULT NULL COMMENT '茶的商品描述图片1  ',
        `tea_presentation_img2` varchar(255) DEFAULT NULL COMMENT '茶的商品描述图片2    ',
        `tea_presentation_img3` varchar(255) DEFAULT NULL COMMENT '茶的商品描述图片3  ',
        `tea_presentation_img4` varchar(255) DEFAULT NULL COMMENT '茶的商品描述图片4    ',
        `tea_presentation_img5` varchar(255) DEFAULT NULL COMMENT '茶的商品描述图片5  ',
        `tea_presentation_img6` varchar(255) DEFAULT NULL COMMENT '茶的商品描述图片6    ',
        `tea_presentation_img7` varchar(255) DEFAULT NULL COMMENT '茶的商品描述图片7  ',
        `tea_presentation_img8` varchar(255) DEFAULT NULL COMMENT '茶的商品描述图片8    ',
        `tea_presentation_img9` varchar(255) DEFAULT NULL COMMENT '茶的商品描述图片9  ',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB COMMENT='小程序茶种类照片价格等信息 ';
```
设计好表后，记得导入到你的数据库，也可以顺便添加一些商品测试数据。
```sql
INSERT INTO `phalapi_mini_tea` VALUES ('3', '鸭屎香|潮州正宗凤凰单枞茶鸭屎香单丛茶500g', ' 凤凰单枞茶与凤凰单丛茶同义，是青茶品种之一，产于广东省潮州市凤凰镇凤凰山区，当地人称为凤凰茶，属乌龙茶系，半发酵茶，是六大茶类之一，凤凰单枞（丛）茶采用传统的制茶工艺，茶香气自然，回甘悠远，其香型众多：分别是蜜兰香，鸭屎香，黄枝香，芝兰香等等。', '120.00', 'https://gd1.alicdn.com/imgextra/i1/2386976319/O1CN01rvZ7Vg1wY9FDYDo75_!!2386976319.jpg', 'http://cdn7.okayapi.com/0D19F4F8568B4232213F87FC45C03253_20190313181046_f8fa5b77db517ca5e03edf6b9cc12c8a.jpeg', 'http://cdn7.okayapi.com/0D19F4F8568B4232213F87FC45C03253_20190313181051_06146edcf39c29072e2a3c4f88ce94d7.jpeg', 'https://img.alicdn.com/imgextra/i3/3952137956/O1CN0128dtZEVuzw4LNEd_!!3952137956.jpg', '包邮', '桶装', 'https://img.alicdn.com/imgextra/i2/3952137956/O1CN0128dtZ85KcsMc5sT_!!3952137956.jpg', 'https://img.alicdn.com/imgextra/i4/3952137956/O1CN0128dtZ8hARHQ4MDr_!!3952137956.jpg', 'https://img.alicdn.com/imgextra/i2/3952137956/O1CN0128dtZ7s4M6j0OMp_!!3952137956.jpg', 'https://img.alicdn.com/imgextra/i4/3952137956/O1CN0128dtZ5NKXzMorXw_!!3952137956.jpg', 'https://img.alicdn.com/imgextra/i3/3952137956/O1CN0128dtZ5NNUxhoPS0_!!3952137956.jpg', 'https://img.alicdn.com/imgextra/i4/3952137956/O1CN0128dtZ71tGrTbiyZ_!!3952137956.jpg', '', '', ''), ('4', '乌岽单从|春茶鸭屎香凤凰单枞茶', '乌岽单从|春茶鸭屎香凤凰单枞茶，好喝耐泡，人工手工挑选，经过十余道工序后精致地呈现在我们面前。', '388.00', 'https://img.alicdn.com/imgextra/i1/3167001606/O1CN01aAAion1NjaZrp6cjH_!!3167001606.jpg_430x430q90.jpg', 'http://cdn7.okayapi.com/0D19F4F8568B4232213F87FC45C03253_20190313181107_66399132c3d9cd5a1590a763be98c815.jpeg', 'http://cdn7.okayapi.com/0D19F4F8568B4232213F87FC45C03253_20190313181121_60c07bffea8db4f38676b35feb73cff8.jpeg', 'https://img.alicdn.com/imgextra/i3/3348339472/O1CN014u9rdF2JqE0xFc3ls_!!3348339472.jpg', '热门', '', 'https://img.alicdn.com/imgextra/i2/73657124/O1CN0122Uq1mp7MCeE7QW_!!73657124.jpg', 'https://img.alicdn.com/imgextra/i2/73657124/O1CN0122Uq1m8jI5yst1Y_!!73657124.jpg', 'https://img.alicdn.com/imgextra/i3/73657124/O1CN0122Uq1lGqCPaJuT9_!!73657124.jpg', '', '', '', '', '', ''), ('5', '金骏眉红茶茶叶散装浓香型金俊眉罐装凤鼎红礼盒装共500g', '汤色红艳明亮，细嫩的滇红茶汤冷后会出现特殊的“冷后浑”。茶的香气是浓郁的花果香或焦糖香，茶汤的滋味醇厚，略带涩味。', '88.00', 'https://img.alicdn.com/imgextra/i4/44653043/TB2O69GXhjaK1RjSZKzXXXVwXXa_!!0-saturn_solar.jpg_220x220.jpg_.webp', 'https://img.alicdn.com/imgextra/i4/2148741879/TB2EVWPgrSYBuNjSspfXXcZCpXa_!!2148741879.jpg_430x430q90.jpg', 'https://gd1.alicdn.com/imgextra/i1/3374201759/O1CN01w0Ue8R1OrfAk3LBEw_!!3374201759.jpg', 'https://gd3.alicdn.com/imgextra/i3/3374201759/O1CN01Sb2LjO1OrfAmNlrqT_!!3374201759.png', '热门', '500g', 'https://img.alicdn.com/imgextra/i3/1728336722/O1CN01himml81zWipi6UjqB_!!1728336722.jpg', 'https://img.alicdn.com/imgextra/i3/1728336722/O1CN011zWioLF2PXWHPZ7_!!1728336722.jpg', 'https://img.alicdn.com/imgextra/i4/1728336722/O1CN011zWioGDZw1RlyHp_!!1728336722.jpg', '', '', '', '', '', ''), ('6', '正宗安溪茶叶铁观音新茶特级乌龙茶清香型礼盒装500g中闽农哥', '', '288.00', 'https://img.alicdn.com/imgextra/i3/1087306018955005943/TB2Xr1ZihRDOuFjSZFzXXcIipXa_!!0-saturn_solar.jpg_220x220.jpg_.webp', 'https://img.alicdn.com/imgextra/https://img.alicdn.com/imgextra/i4/2453837426/O1CN017BeATH24j9gRl7AfA_!!2453837426.jpg_430x430q90.jpg', 'https://img.alicdn.com/imgextra/https://img.alicdn.com/imgextra/i3/835643150/O1CN01ZZRT9s1Z8k5rRJea7_!!835643150.jpg_430x430q90.jpg', '', '包邮', '', 'https://img.alicdn.com/imgextra/i1/835643150/TB28KSCdCiJ.eBjSspiXXbqAFXa_!!835643150.jpg', 'https://img.alicdn.com/imgextra/i4/835643150/TB2PUy3iTlYBeNjSszcXXbwhFXa_!!835643150.jpg', 'https://img.alicdn.com/imgextra/i4/835643150/TB2t14GiQ9WBuNjSspeXXaz5VXa_!!835643150.jpg', '', '', '', '', '', ''), ('7', '凤凰单枞茶蜜兰香 潮州特级乌岽雪片 高山单丛茶叶浓香型500g', '', '98.00', 'https://gd1.alicdn.com/imgextra/i1/1801931690/TB2b.A0aTJYBeNjy1zeXXahzVXa_!!1801931690.jpg', 'https://gd4.alicdn.com/imgextra/i4/1801931690/TB2OXvWisuYBuNkSmRyXXcA3pXa_!!1801931690.jpg', 'https://gd2.alicdn.com/imgextra/i2/1801931690/TB2vO3.Xsv_F1JjSZFmXXchWXXa_!!1801931690.jpg', 'https://gd1.alicdn.com/imgextra/i1/1801931690/TB2pN.DarAlyKJjSZFBXXbtiFXa_!!1801931690.jpg', '热门', '', 'https://img.alicdn.com/imgextra/i1/1801931690/TB2voUkquOSBuNjy0FdXXbDnVXa_!!1801931690.jpg', 'https://img.alicdn.com/imgextra/i2/1801931690/TB2AoLdindYBeNkSmLyXXXfnVXa_!!1801931690.jpg', 'https://img.alicdn.com/imgextra/i4/1801931690/TB2mIMGqER1BeNjy0FmXXb0wVXa_!!1801931690.jpg', '', '', '', '', '', ''), ('8', '蜜兰香潮州凤凰单枞茶凤凰单丛乌岽茶叶高山蜜兰香凤凰单丛500g', '', '299.00', 'https://gd2.alicdn.com/imgextra/i1/1107518742/TB2hpgpbnMG5uJjSZFAXXbmspXa_!!1107518742.jpg', 'https://gd4.alicdn.com/imgextra/i4/1107518742/TB2Uq3fabEF6uJjSZFOXXXUvVXa_!!1107518742.jpg', 'https://gd3.alicdn.com/imgextra/i3/1107518742/TB2S1E2bcD85uJjSZFpXXXz3VXa_!!1107518742.jpg', 'https://gd1.alicdn.com/imgextra/i1/1107518742/TB2X4myrgoQMeJjy0FpXXcTxpXa_!!1107518742.jpg', '热门', '', 'https://img.alicdn.com/imgextra/i1/1107518742/O1CN01nGj85a2ERsuCXhln7_!!1107518742.jpg', 'https://img.alicdn.com/imgextra/i3/1107518742/O1CN01Zr3t6i2ERsuAI7zLE_!!1107518742.jpg', 'https://img.alicdn.com/imgextra/i4/1107518742/O1CN01stPPei2ERsuAC1HL8_!!1107518742.jpg', '', '', '', '', '', '');

```

在明白应用的需求后，并且创建了Api、Domain、Model相关的PHP代码，也设计创建了数据库表，还准备了一些测试数据库，那么接下来就可以自己先测试一下刚才编写的接口。

先来看下生成的在线接口文档，正常情况下，你可以通过以下链接：  
```
http://dev.phalapi.net/docs.php?service=App.PhalApi_MiniTea_Tea.QueryList&detail=1&type=fold
```
> 温馨提示：记得把域名换成你自己的。

可以看到刚才开发的新接口。  
![](http://cdn7.okayapi.com/yesyesapi_20200312164004_ab89ba05034098e09830dbac2a4cd5c3.png)

通过在线测试，可以测试接口是否正常。  

![](http://cdn7.okayapi.com/yesyesapi_20200312164136_c16becff458169366b416470cde14a16.png)

你也可以直接通过接口链接，在浏览器上访问。例如模拟客户端请求：
```
http://dev.phalapi.net/?s=App.PhalApi_MiniTea_Tea.QueryList&perpage=2
```

> 温馨提示：记得把域名换成你自己的。

正常情况可以看到类似：  
![](http://cdn7.okayapi.com/yesyesapi_20200312164248_5b41587dc12617a67b89d5c7278e923a.png)

如果接口有问题，则进入调试模式（在接口链接上添加```&__debug__=1```），根据调试信息修改即可。

## 开发微信小程序

安装好微信开发者工具后。  

![](http://cdn7.okayapi.com/yesyesapi_20200312164532_eb3614106f59b969616a5e560b46ad50.jpeg)

创建一个新的小程序。  

![](http://cdn7.okayapi.com/yesyesapi_20200312164620_52659e63525b9ab65abc3f9b148b343c.png)

在本发开发调试时，记得要在项目右上角的【详情】-【本地配置】，勾选：不校验合法域名、web-view（业务域名）、TLS 版本以及 HTTPS 证书。不设置的话，本地无法请求你本地的接口。  

![](http://cdn7.okayapi.com/yesyesapi_20200312164833_4002d7794c3d28c587f2eb773ec04def.png)

随后，也是很重要的一步，就是把你本地开发环境的接口域名或者最终正式环境的接口域名，配置到app.js文件中。  

![](http://cdn7.okayapi.com/yesyesapi_20200312164943_8285b635ad82e38b0dfd43c3d9816f12.png)

> 温馨提示：记得把域名换成你自己的。

完成这些基本的配置后，就可以开发小程序的首页了。  

先编写模板代码，修改index/index.wxml文件，保存代码。  
```html

<scroll-view class="container"
             style="height:{{systemInfo.windowHeight}}px; display: {{loading.isViewHidden ? 'none' : 'block'}}"
             scroll-y="true" lower-threshold="300" bindscrolltolower="loadMoreGoods">

    <swiper class="swiper" indicator-dots="{{indicatorDots}}" autoplay="{{autoplay}}" interval="{{interval}}"
            duration="{{duration}}" circular='true' style="height:{{pageSetting.swiperHeight}}rpx">
        <block wx:for="{{swiperData}}">
            <swiper-item>
                <image src="{{item.imgUrl}}" class="swiper-image" mode="scaleToFill"/>
            </swiper-item>
        </block>
    </swiper>
    <view class="index-goods-list">
        <block wx:for-items="{{goodsData}}">
            <block wx:if="{{item.id}}">
                <view class="index-goods-item" data-id="{{item.id}}" bindtap="showGoodsDetailPage">

                    <view data-id="{{item.id}}" bindtap="showGoodsDetailPage" class="index-goods-img-view">
                        <image src="{{item.goods_img}}" mode="aspectFit" class="index-goods-img"></image>
                    </view>

                    <view class="index-goods-text-view">
                        <text class="index-goods-title">{{item.goods_title}}</text>
                        <view class="index-goods-desc-view" wx:if="{{item.tea_tag == '包邮'}}">
                            <text class="index-goods-desc">包邮</text>
                        </view>
                        <view class="index-goods-desc-view index-goods-desc-hot-view" wx:if="{{item.tea_tag == '热门'}}">
                            <text class="index-goods-desc-hot">热门</text>
                        </view>
                        <view class="index-goods-btns">
                            ¥
                            <text class="pirce">{{item.tea_price}}</text>
                        </view>
                    </view>
                </view>
            </block>
        </block>
    </view>

    <view class="downline">
        <text>————我也是有底线的————</text>
    </view>

    <loading hidden="{{loading.hidden}}">{{loading.msg}}</loading>
    <toast hidden="{{toast.hidden}}" icon="{{toast.icon}}" duration="3000" bindchange="toastChange">{{toast.msg}}
    </toast>

</scroll-view>
<!-- loading -->
<loading hidden="{{loading.hidden}}">{{loading.msg}}</loading>
<toast hidden="{{toast.hidden}}" icon="{{toast.icon}}" duration="3000" bindchange="toastChange">{{toast.msg}}</toast>

```

然后，编写js代码，实现首页接口的请求。

封装接口请求：  
```javascript


let S_request = {
  index: {
    getGoodsList: function (page, cb) {

      let params = {
        s: "App.PhalApi_MiniTea_Tea.QueryList",	// 必须，待请求的接口服务名称
        page: page,
        perpage: "5"
      };
      let secondParams = {
        s: "App.PhalApi_MiniTea_Tea.GetTopSwiper",	// 必须，待请求的接口服务名称
        page: 1,
        perpage: "5"
      };

      wx.request({
        header: utils.requestHeader(),
        url: getApp().globalData.okayapiHost,
        data: okayapi.enryptData(params),

        success: (res) => {
          let data = res.data.data.items;

          let goods = [];
          var swiperData = [];
          for (let i = 0; i < data.length; i++) {
            goods.push({
              id: data[i].id,
              goods_img: data[i].tea_titlepage,
              goods_title: data[i].tea_name,
              goods_desc: data[i].tea_desc,
              tea_tag: data[i].tea_tag,
              tea_price: data[i].tea_price,
            })
          };
          wx.request({
            header: utils.requestHeader(),
            url: getApp().globalData.okayapiHost,
            data: okayapi.enryptData(secondParams),
            success: (res) => {
              let data = res.data.data.items;

              for (let i = 0; i < data.length; i++) {
                if (data[i] != null) {
                  swiperData.push({
                    imgUrl: data[i].swiper_picture,
                  })
                }
              }
              typeof cb == "function" && cb(goods, swiperData);
            },
            fail: (err) => {
              console.log('error', err);
              err.statusCode = CONFIG.CODE.REQUESTERROR;
            }
          });

          goods.statusCode = CONFIG.CODE.REQUESTSUCCESS;

        },
        fail: (err) => {
          err.statusCode = CONFIG.CODE.REQUESTERROR;
          typeof cb == "function" && cb(err);
        }
      })
    }
  },
```

在index/index.js文件中，调用刚才封装的请求，实现界面渲染。  
```

  //加载完成
  onLoad: function() {
    this.setData({
      systemInfo: app.getSystemInfo()
    });

    //通过requestService实例对象拿到数据
    S_request.index.getGoodsList(curPageNumber, (goodsData, swiperData) => {
      if (goodsData.statusCode == CONFIG.CODE.REQUESTERROR) {
        this.setData({

          "toast.hidden": false,
          "toast.icon": "clear",
          "toast.msg": "请求超时",
          "loading.hidden": true

        });
        return;
      }

      this.setData({
        goodsData: goodsData,
        swiperData: swiperData,
      });

      curPageNumber += 1;
      app.MLoading(this, curPageRequsetNumber);

    });

  },
```

调试成功后，就可以看到首页的商品和轮播图啦！~  

![](http://cdn7.okayapi.com/yesyesapi_20200312161514_5df9836c78a648a63bfab4799d9adc9d.png)
  
## 开发运营平台的界面

如果你的插件需要配置提供运营平台的界面，那么可以按以下步骤来开发。  
 
 + 为你的插件添加运营平台菜单    
 + 添加运营平台需要的后台API接口（这部分已经有接口模板代码）
 + 添加运营平台需要的界面模板（这部分也已经有前端模板代码）  
 
我们先从菜单入口开始，默认添加的插件菜单如下：  
```
delete from `phalapi_portal_menu` where id = 457602782;
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '457602782', 'phalapi_mini_tea', 'page/phalapi_mini_tea/index.html', '9999', '1', 'fa fa-list-alt');
```

你可以根据自己的需求，修改菜单的标题、位置和结构、页面位置。  
![](http://cdn7.okayapi.com/yesyesapi_20200312165901_0164ea172a3ac11f7b4c903116a0c23c.png) 

修改默认生成的后台接口模板代码，例如：src/portal/Api/Phalapiminitea/Main.php，或者直接新建你的接口类。这里是新建了一个接口类，文件路径是：src/portal/Api/PhalApi/MiniTea/TeaOrder.php，代码是：  
```php
<?php
namespace Portal\Api\PhalApi\MiniTea;

use Portal\Common\DataApi;

/**
 * 茶店微信小程序
 * @ ignore
 * @author dogstar 20200308
 */
class TeaOrder extends DataApi {

    protected function getDataModel() {
        return new \Portal\Model\PhalApi\MiniTea\Order();
    }

}
```

出来的运营平台接口有：  

![](http://cdn7.okayapi.com/yesyesapi_20200312170232_2c84ecc98f23d3fb0be2eacff050eb03.png)

注意到上面需要用到```Portal\Model\PhalApi\MiniTea\Order```类，这个类还没有，我们需要继续创建，并在里面指定刚才我们的数据库表，也就是订单表。

新建文件：src/portal/Model/PhalApi/MiniTea/Order.php，放置代码：  
```php
<?php
namespace Portal\Model\PhalApi\MiniTea;

use PhalApi\Model\DataModel;

class Order extends DataModel {

    public function getTableName($id) {
        return 'phalapi_mini_tea_order';
    }
}
```

随后，就可以开发运营平台的界面了。你可以参考原来CURD的示例，这里有数据列表页、搜索、编辑和修改、删除等功能，把模板复制过来，稍微修改就可以完成常规的界面需求了。  

这里把CURD的列表页模板public/portal/page/phalapi-curd-table/index.html复制到public/portal/page/phalapi-minitea/index.html，并且修改成：  
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>layui</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../../lib/layui-v2.5.5/css/layui.css" media="all">
    <link rel="stylesheet" href="../../css/public.css" media="all">
</head>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">

        <fieldset class="table-search-fieldset">
            <legend>搜索订单</legend>
            <div style="margin: 10px 10px 10px 10px">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">订单号</label>
                            <div class="layui-input-inline">
                                <input type="text" name="order_number" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <button type="submit" class="layui-btn layui-btn-primary" lay-submit  lay-filter="data-search-btn"><i class="layui-icon"></i> 搜 索</button>
                        </div>
                    </div>
                </form>
            </div>
        </fieldset>

        <script type="text/html" id="toolbarDemo">
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm data-add-btn" lay-event="add"> 添加订单 </button>
                <button class="layui-btn layui-btn-sm layui-btn-danger data-delete-btn" lay-event="delete"> 删除订单 </button>
            </div>
        </script>

        <table class="layui-hide" id="currentTableId" lay-filter="currentTableFilter"></table>

        <script type="text/html" id="currentTableBar">
            <a class="layui-btn layui-btn-xs data-count-edit" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-xs layui-btn-danger data-count-delete" lay-event="delete">删除</a>
        </script>

    </div>
</div>
<script src="../../lib/layui-v2.5.5/layui.js" charset="utf-8"></script>
<script>
    layui.use(['form', 'table'], function () {
        var $ = layui.jquery,
            form = layui.form,
            table = layui.table;

        table.render({
            elem: '#currentTableId',
            url: '/?s=Portal.PhalApi_MiniTea_TeaOrder.TableList',
            parseData: function(res){ //res 即为原始返回的数据
                return {
                    "code": res.ret == 200 ? 0 : res.ret, //解析接口状态
                    "msg": res.msg, //解析提示文本
                    "count": res.data.total, //解析数据长度
                    "data": res.data.items //解析数据列表
                };
            },
            toolbar: '#toolbarDemo',
            defaultToolbar: ['filter', 'exports', 'print', {
                title: '提示',
                layEvent: 'LAYTABLE_TIPS',
                icon: 'layui-icon-tips'
            }],
            cols: [[
                {type: "checkbox", width: 50, fixed: "left"},
                {field: 'id', width: 20, title: 'ID', sort: true},
                {field: 'order_number', minWidth: 50, title: '订单号'},
                {field: 'order_buyer', minWidth: 30, title: '顾客'},
                {field: 'order_goods', minWidth: 80, title: '商品明细'},
                {field: 'order_goods_num', minWidth: 50, title: '商品数量'},
                {field: 'order_price', minWidth: 30, title: '总价'},
                {title: '操作', minWidth: 50, templet: '#currentTableBar', fixed: "right", align: "center"}
            ]],
            limits: [10, 15, 20, 25, 50, 100],
            limit: 15,
            page: true
        });

        // 监听搜索操作
        form.on('submit(data-search-btn)', function (data) {
            var result = JSON.stringify(data.field);
            layer.alert(result, {
                title: '最终的搜索信息'
            });

            //执行搜索重载
            table.reload('currentTableId', {
                page: {
                    curr: 1
                }
                , where: {
                    searchParams: result
                }
            }, 'data');

            return false;
        });

        /**
         * toolbar监听事件
         */
        table.on('toolbar(currentTableFilter)', function (obj) {
            if (obj.event === 'add') {  // 监听添加操作
                var index = layer.open({
                    title: '添加用户',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: '/page/table/add.html',
                });
                $(window).on("resize", function () {
                    layer.full(index);
                });
            } else if (obj.event === 'delete') {  // 监听删除操作
                var checkStatus = table.checkStatus('currentTableId')
                    , data = checkStatus.data;
                layer.alert(JSON.stringify(data));
            }
        });

        //监听表格复选框选择
        table.on('checkbox(currentTableFilter)', function (obj) {
            console.log(obj)
        });

        table.on('tool(currentTableFilter)', function (obj) {
            var data = obj.data;
            if (obj.event === 'edit') {

                var index = layer.open({
                    title: '编辑用户',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: '/page/table/edit.html',
                });
                $(window).on("resize", function () {
                    layer.full(index);
                });
                return false;
            } else if (obj.event === 'delete') {
                layer.confirm('真的删除行么', function (index) {
                    obj.del();
                    layer.close(index);
                });
            }
        });

    });
</script>
<script>

</script>

</body>
</html>
```


最后出来的效果是：  

![](http://cdn7.okayapi.com/yesyesapi_20200312162046_02a6d27a182469487fcc4d93559a914f.png)

## 打包插件

在完成你的插件开发后，包括接口、客户端小程序、运营平台功能等，你就可以开始打包了。

打包前，需要检查下以文件或目录是否齐全，重点是修改```./plugins/插件编号.json```这个插件json配置文件。  

例如这里是plugins/phalapi_mini_tea.json文件，配置是：  
```json
{
    "plugin_key": "phalapi_mini_tea",
    "plugin_name": "PhalApi茶店微信小程序",
    "plugin_author": "PhalApi",
    "plugin_desc": "专门用于学习的教程配置插件，属于半成品，可继续迭代开发。",
    "plugin_version": "1.0",
    "plugin_encrypt": 0,
    "plugin_depends": {
        "PHP": "5.6",
        "MySQL": "5.3",
        "PhalApi": "2.12.2",
        "composer": [],
        "extension": []
    },
    "plugin_files": {
        "config": "config\/phalapi_mini_tea.php",
        "plugins": "plugins\/phalapi_mini_tea.php",
        "data": "data\/phalapi_mini_tea.sql",
        "public": [
            "public\/portal\/page\/phalapi-minitea"
        ],
        "src": [
            "src\/app\/Api\/PhalApi\/MiniTea",
            "src\/app\/Domain\/PhalApi\/MiniTea",
            "src\/app\/Model\/PhalApi\/MiniTea",
            "src\/portal\/Model\/PhalApi\/MiniTea",
            "src\/portal\/Api\/Phalapi\/MiniTea"
        ]
    }
}
```  

以下是一份实用的打包清单：  

 + 1、确保插件json配置是否符合JSON格式
 + 2、确保插件的相关信息无误（包括插件编号、名称、开发者名称、版本号、描述、是否加密、依赖等声明）
 + 3、确保插件需要的源代码已经配置齐全（对照src/app、src/portal、public/portal等前后端代码）
 + 4、确保数据库初始化文件已包含需要的菜单配置以及数据库表配置
 
确认无误后，在本地执行打包命令。例如：  
```
$ php ./bin/phalapi-plugin-build.php phalapi_mini_tea
插件已打包发布完毕！
/Users/dogstar/projects/github/phalapi/plugins/phalapi_mini_tea.zip
``` 

成功打包后，会有./plugins目录下，生成相应的zip压缩包。例如这里的：./plugins/phalapi_mini_tea.zip。  

![](http://cdn7.okayapi.com/yesyesapi_20200312171440_a49a41dd4857148a97c3d2c42f4f197c.png)

你可以解压此包，检测是否已包含你全部需要发布的文件。  

> 温馨提示：请放心，在本地你可以无限次重复打包。

本插件打包后，经过开发以及调整，最终包含的文件和源代码有：  
```
config/phalapi_mini_tea.php
data/phalapi_mini_tea.sql
plugins/phalapi_mini_tea.json
plugins/phalapi_mini_tea.php
public/portal/page/phalapi-minitea/index.html
src/app/Api/PhalApi/MiniTea/Tea.php
src/app/Domain/PhalApi/MiniTea/Tea.php
src/app/Domain/PhalApi/MiniTea/TeaMoment.php
src/app/Domain/PhalApi/MiniTea/TeaOrder.php
src/app/Domain/PhalApi/MiniTea/TeaShopCar.php
src/app/Domain/PhalApi/MiniTea/TeaSwiper.php
src/app/Domain/PhalApi/MiniTea/TeaUser.php
src/app/Model/PhalApi/MiniTea/Tea.php
src/app/Model/PhalApi/MiniTea/TeaMoment.php
src/app/Model/PhalApi/MiniTea/TeaOrder.php
src/app/Model/PhalApi/MiniTea/TeaShopCar.php
src/app/Model/PhalApi/MiniTea/TeaSwiper.php
src/app/Model/PhalApi/MiniTea/TeaUser.php
src/portal/Api/PhalApi/MiniTea/TeaOrder.php
src/portal/Model/PhalApi/MiniTea/Order.php
```

## 安装包测试

当你的插件开发完成并完成打包后，必须要进行安装测试，避免用户在购买或下载后无法安装或使用。  

有两种测试安装的方式，一种是在你本地进行环境测试，但不推荐，因为会干扰你原来的源代码。因此推荐使用PhalApi另外创建一个站点，模拟客户进行安装。分开两个环境，即开发环境，和客户环境。

这里使用了另外一个环境，专门用于进行测试安排。  
 
先把打包后的插件压缩包传到或者复制到客户环境。选中刚才本地打包好的插件压缩包。例如：  
![](http://cdn7.okayapi.com/yesyesapi_20200312171925_0e66dba0c5087a27824b3b172ddafca2.png) 

上传后，移到./plugins目录。  
```
$ mv ./phalapi_mini_tea.zip ./plugins/    
```

上传后，你可以直接通过命令的方式进行安装测试，执行命令：  
```
$ php ./bin/phalapi-plugin-install.php phalapi_mini_tea
正在安装 phalapi_mini_tea
开始检测插件安装包 phalapi_mini_tea
检测插件是否已安装
插件已安装：plugins/phalapi_mini_tea.json
开始安装插件……
检测插件安装情况……
插件已安装：plugins/phalapi_mini_tea.json
插件：phalapi_mini_tea（PhalApi茶店微信小程序），开发者：PhalApi，版本号：1.0，安装完成！
开始检测环境依赖、composer依赖和PHP扩展依赖
PHP版本需要：5.6，当前为：7.1.33
MySQL版本需要：5.3
PhalApi版本需要：2.12.2，当前为：2.12.2
开始数据库变更……
插件安装完毕！
```

注意观察是否有异常，或者是否提示安装失败。

另外，你也可以通过运营平台，通过界面的方式来安装。  
![](http://cdn7.okayapi.com/yesyesapi_20200312172216_d66c3948e8360619d1163d25ec64bb87.png)

如果安装后提示失败，那么很可能是文件和目录没有写入权限。  
![](http://cdn7.okayapi.com/yesyesapi_20200312172402_99dc9f7a197ef72c56df09bf78c86568.png) 

这时候，可以执行预热的脚本增加写入权限。  
```
$ php ./bin/phalapi-plugin-prepare.php 
插件安装环境已预热完毕！
```

成功安装后，可以看到类似以下界面。  
![](http://cdn7.okayapi.com/yesyesapi_20200312172610_ba1dc01094bdda767db031faf31bbb34.png)

安装完成后，就可以进行功能验收和接口测试了。自测OK并且验收通过后，就可以进行插件发布的操作。  


## 发布插件

进入PhalApi应用市场：http://www.yesx2.com/  

注册开发者，并且实名认证通过后，就可以发布你的插件了。  

![](http://cdn7.okayapi.com/yesyesapi_20200312172930_e6be02c9f9717b1100ba8991e3b2d767.png)

把刚才验收通过的插件压缩包源代码，以及价格等商品信息提供给我们，在审核通过后即可上架销售。  

> 温馨提示：上架流程正在搭建中，要联系dogstar优先处理。  


