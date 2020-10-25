<?php
/*	代码库配置文件 v150421	By:shileiye	*/
require_once 'inc/inc.php';		//载入公用库
$info=array(
	/*	系统设置	*/
	"swtskins"=>"html",		//模块样式路径
	"swtnocanshu"=>"strblqw",		//swt.php无参数默认显示模块
	"bdtjid"=>"0af5ec7608af49ebfa34b64b800adb0a",		//百度统计ID,留空则不启用
	"cnzzid"=>"",		//CNZZ统计ID,留空则不启用
	"taotelid"=>"OQ==",		//套电ID,留空则不启用
	"mqid"=>"3340",		//美恰ID
	"lxbuid"=>"9888580",		//离线宝uid
	"lxbgid"=>"149398",		//离线宝gid
	"lrinvitetimeout"=>5,		//商务通邀请框首次弹出时间（秒）
	"lr_next_invite_seconds"=>60,		//商务通邀请框关闭后再次弹出时间（秒）
	"imgwhat"=>0,		//默认显示图片数组列（从0开始，涉及文件showimg.php）
	
	/*	信息设置	*/
	"web"=>"湖南省交通医院",		//网站名称
	"yy"=>"湖南省交通医院",		//医院名称
	"swtid"=>"PYT21475798",		//商务通ID
	"qq"=>"2557988157",		//QQ号码
	"dh"=>"0731-82233632",		//电话号码
	"tel"=>"/tel",		//免费电话连接
	"dz"=>"湖南省长沙市开福区八一路546号（亚华大酒店斜对面）",		//医院地址
	"zj"=>"蒋兆瑜",		//主推专家名称
	"zjjj"=>"1983年蒋兆瑜于湖南医科大学毕业后穿上白大褂，1993年至1997年，蒋兆瑜先后进入中国人民解放军第一军医大学和华西医院神经内科进修，主攻儿童多动症抽动症等儿童神经系统疾病。",		//主推专家简介
	"zjurl"=>"/lanmu/zj/311.html",		//主推专家连接
	
	/*	其他设置	*/
	"isjquery"=>0,		//是否加载jQuery库（0=是）
	"issuperslide"=>0,		//是否加载SuperSlide库（0=是）
	"isopenqqchat"=>0,		//延时多少秒后自动弹出QQ聊天窗口（0=不弹出）
	"swtgourl"=>"",		//index.php无参数默认跳转连接（留空则跳转商务通，可选qq,mq,tel）
	"pcorsj"=>"",		//强制显示手机或PC端显示模块（sj=手机模块，pc=PC模块，空=自动判断）
	"isaonclick"=>0,		//是否自动给页面的/swt/连接加上gotoswt(event,this)方法（0=是）
	"isatel"=>0,		//是否自动格式化替换页面的电话连接为配置连接（0=是，只在手机端生效）
	"dhurltel"=>1,		//是否在PC端转换电话连接为tel设置的页面（0=是）
	"autoup"=>1		//自动检查升级本程序时间间隔（单位：天，0=不自动更新）
);
/*	模版设置
1、key名称等于调用的参数。
2、值里面直接填模版文件名则直接调用相应模版。
3、完整模版文件名结尾加上|sj则只在手机端显示，|pc则只在PC端显示
4、结尾|后为空则自动判断手机或PC访问调用相应模版，模版文件名前缀sj_和pc_不用填
5、注意，客户端自动判断需要制作pc端和手机端两个模版。
*/
$moban=array(
	"s"=>"swt_body.js|",				//商务通
	"t"=>"pc_swt_top.js",			//顶部菜单拦（需jQuery）
	"r"=>"swt_right.js|",				//右边侧栏
	"b"=>"pc_swt_bottom.js",		//底部横栏（需jQuery及SuperSlide）
	"l"=>"swt_left.js|",					//左边侧栏
	"q"=>"pc_swt_qq.js|pc",		//QQ抖动窗口
	"w"=>"sj_swt_wx.js|sj",			//微信消息提示（只在手机端显示）
	"m"=>"other_meiqia.js"		//美恰JS调用（只在美恰ID填写后生效）
);
require_once 'inc/autoset.php';		//载入自动处理参数