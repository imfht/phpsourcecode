#wxjssdk_laravel


让微信分享更简单!

***

这是使用Lumen开发的一款专为微信分享的服务端系统, 有了它, 微信要使用分享功能只需要在你html代码里增加简单几行代码:

    var url = "http://server.com/get_js_sdk?url=" + location.href;
    var script = document.createElement('script');
    script.setAttribute('src', url);
    document.getElementsByTagName('head')[0].appendChild(script);
    

`注`其中server.com为该系统的网址. 
	   
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script>
	    var url = "http://server.com/get_js_sdk?url=" + location.href;
	    var script = document.createElement('script');
	    script.setAttribute('src', url);
	    document.getElementsByTagName('head')[0].appendChild(script);
	    
	    wx.ready(function() {
	        wx.onMenuShareTimeline({
	            title: '这里是分享标题', // 分享标题
	            link: 'http://www.baidu.com', // 分享链接
	            imgUrl: 'http://img5.imgtn.bdimg.com/it/u=274501810,3917839687&fm=21&gp=0.jpg', // 分享图标
	            success: function () {
	                // 用户确认分享后执行的回调函数
	            },
	            cancel: function () {
	                // 用户取消分享后执行的回调函数
	            }
	        });
	    });
	</script>



没有使用过微信分享的请先查阅[微信文档](http://mp.weixin.qq.com/wiki/11/74ad127cc054f6b80759c40f77ec03db.html)

## 服务端安装

没有安装过Lumen的请先参考Lumen文档

官方文档: [http://lumen.laravel.com](http://lumen.laravel.com)

中文文档: [http://lumen.golaravel.com](http://lumen.golaravel.com)

**安装**

	git clone git@git.oschina.net:liaoshixiong/wxjssdk_laravel.git
	cd wxjssdk_laravel
	composer install
	cp .env.example .env


**配置说明**

	WX_APPID=               //见微信文档
	WX_APPSECRET=           //见微信文档
	WX_JSSDK_DEBUG=true     //jssdk网页端debug开关, 调试的时候使用
	APP_DEBUG=true          //上线后设置成false
	
	CACHE_DRIVER=file       //我这里使用的缓存驱动是文件缓存, 你也可以使用memcache, redis等,详见Lumen文档
	QUEUE_DRIVER=sync       //就按这样设置就好


## 注意事项
1. 微信JS接口安全域名要设置客户端的域名

	> 例如, 要分享出去的网址为 http://client.com/index.html, 那么你需要在 公众账号设置-功能设置-JS接口安全域名 添加一个域名 client.com