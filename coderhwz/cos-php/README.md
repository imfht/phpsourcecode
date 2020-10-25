#安装


在composer.json中添加

```javascript
{
    // blabla
    require:{
        "coderhwz/cos-php": "*@dev"
    },
    // blabla

	"repositories": [
        {"type": "vcs", "url": "http://git.oschina.net/coderhwz/cos-php"},
        {"packagist": false}
    ]
}


```



====================当前版本信息====================
当前版本：V1.3

发布日期：2013-11-18

文件大小：11 K 

语言平台：php

====================修改历史====================

V1.0  2013-05-14, 腾讯云平台COS服务的PHP SDK第一版发布
V1.1  2013-08-15, COS PHP SDK替换key验证方式，针对新用户增加sercetId
V1.2  2013-09-11, COS PHP SDK 修改压缩接口参数
V1.3  2013-11-18, COS PHP SDK demo中增加secretId使用
   
        
====================文件结构信息====================
	
lib目录：SDK内部使用类

demo\tutorial.php： 接口示例代码

cos.class.php：接口类代码


====================联系我们====================
腾讯开放平台官网：http://open.qq.com/
您可以访问我们的资料库了解COS的相关信息：http://wiki.open.qq.com/wiki/COS%E6%9C%8D%E5%8A%A1%E4%BB%8B%E7%BB%8D
您也可以通过企业QQ（号码：800013811；直接在QQ的“查找联系人”中输入号码即可开始对话）咨询。
