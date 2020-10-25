<?php

return array(
	'kd_tips1'=>array(
		'title'=>'<div style="color:blue;">说明：微信公众号中绑定登录与PC第三方微信登录不同【微信公众号登录使用的是公众平台，PC使用的是开放平台】;<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;相关的配置请参照第三方登录配置文档进行配置;</div><div style="color:red;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;QQ登录支持[PC,手机版,APP]<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;微信登录支持[PC版,APP]<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;微博登录支持[PC版,APP]<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;支付宝登录支持[APP]</div>',
		'type'=>'hidden',
		'value'=>''
	),
	'thirdTypes'=>array(
		'title'=>"第三方登录方式",
		'type'=>'checkbox',
		'options'=>array(
			'qq'=>'QQ登录',	
			'weixin'=>'微信登录',
			'weibo'=>'微博登录',
			'app_qq'=>'qq登录（App）',
			'app_weixin'=>'微信登录（App）',
			'app_alipay'=>'支付宝登录（App）',
		),
		'value'=>'',
	),
	'group'=> array(
		'type'=>'group',
		'options'=>array(
			'qq'=>array(
				'title'=>'QQ登录',
				'options'=>array(
					'qq_tips'=>array(
						'title'=>'<div style="color:blue;">回调地址说明：
						<div style="line-height:20px;">http://域名/index.php/addon/thirdlogin-thirdlogin-qqcallback.html;<br/>http://域名/addon/thirdlogin-thirdlogin-qqcallback.html;</div>
						<div style="line-height:20px;">http://域名/index.php/addon/thirdlogin-thirdlogin-mobileqqcallback.html;<br/>http://域名/addon/thirdlogin-thirdlogin-mobileqqcallback.html;</div></div>
						<div style="color:red;">如果有多端使用QQ登录，则需要使用unionId机制,需要给腾讯客服发邮件开通，具体操作请查看《wstmart 用户手册》</div>',
						'type'=>'hidden',
						'value'=>'',
						'tip'=>''
					),
					'appId_qq'=>array(
						'title'=>'QQ AppID:',
						'type'=>'text',
						'value'=>'',
						'tip'=>''
					),
					'appKey_qq'=>array(
						'title'=>'QQ AppKey:',
						'type'=>'text',
						'value'=>'',
						'tip'=>''
					),
					'img_qq'=>array(
						'type'=>'hidden',
						'value'=>'/addons/thirdlogin/view/images/qq.png',
						'tip'=>''
					)
				)
			),
			'weixin'=>array(
				'title'=>'微信登录',
				'options'=>array(
					'appId_weixin'=>array(
						'title'=>'微信 AppID:',
						'type'=>'text',
						'value'=>'',
						'tip'=>''
					),
					'appKey_weixin'=>array(
						'title'=>'微信 AppKey:',
						'type'=>'text',
						'value'=>'',
						'tip'=>''
					),
					'img_weixin'=>array(
						'type'=>'hidden',
						'value'=>'/addons/thirdlogin/view/images/weixin.png',
						'tip'=>''
					)
				)
			),
			'weibo'=>array(
				'title'=>'微博登录',
				'options'=>array(
					'appId_weibo'=>array(
						'title'=>'微博 AppID:',
						'type'=>'text',
						'value'=>'',
						'tip'=>''
					),
					'appKey_weibo'=>array(
						'title'=>'微博 AppKey:',
						'type'=>'text',
						'value'=>'',
						'tip'=>''
					),
					'img_weibo'=>array(
						'type'=>'hidden',
						'value'=>'/addons/thirdlogin/view/images/weibo.png',
						'tip'=>''
					)
				)
			),
			'app_alipay'=>array(
				'title'=>'支付宝登录（App）',
				'options'=>array(
					'parentId'=>array(
						'title'=>'商户parentId:',
						'type'=>'text',
						'value'=>'',
						'tip'=>''
					)
				)
			)
		)
	)
);
