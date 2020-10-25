#WeixinSystem_BasedOnTP

一、说明

	基于最新 Thinkphp3.2.2 开发而成。

	开发这套程序是因为自己管理着几个微信公众号，想着简单化、结构化、易管理化以前的程序而开发。

	本人学生一枚，如果代码有不好的地方，希望您指出。

	感谢！

二、使用方法

	1、开启“微信公众平台”的“开发者中心”。
		“URL”填入：http://serverName/index.php/Index/一个公众号方法（例如：http://wx.abc.com/index.php/Index/index）。
		“TOKEN”填入：ysdweixin。您也可以在Common/Conf/config.php 下自行配置。

	2、在 Controller 控制器中code出该公众号的业务逻辑。
		示例代码中已经给出2个控制器，一个订阅号和一个服务号

	3、在 EventWidget 中新建一个方法，并code出该方法的逻辑。
		示例代码中已经给出一个订阅号和一个服务号的方法

	4、在 TextWidget 中新建一个方法，并code出该方法的逻辑。
		示例代码中已经给出一个订阅号和一个服务号的方法

	5、拿起手机开始测试吧~

	
三、使用说明

	1、一个公众号对应一个控制器、一个EventWidget方法和一个TextWidget方法。代码中有相应实例

	2、Weixin.class.php 位于 Common\Lib\Weixin 文件夹下。可独立出来单独使用。
	如果不在TP系统内，则应该重写 getValue() 、setValue()和getToken()方法，因为这些方法中应用了TP内置的S函数。由于时间关系，目前没有判断这个函数的存在与否，也没有写出相应的替代方案。

	3、Api.class.php 里面写出了常用的API函数，比如快递查询，天气查询等。也可独立出来单独使用。
	
四、注意
	
	现在该项目目前正在完善中……