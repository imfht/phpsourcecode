<?php
return [ 
		'wx_url' => [ 
				'get_access_token' => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET',
				'get_user_list' => 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=ACCESS_TOKEN&next_openid=NEXT_OPENID',
				'get_user_detail' => 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN',
				'up_media_forever' => 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=ACCESS_TOKEN&type=TYPE',
				'create_menu' => 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN',
				'mass_preview' => 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=ACCESS_TOKEN',
				'up_news_forever' => 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=ACCESS_TOKEN',
				'up_news_for_mass' => 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=ACCESS_TOKEN', // 貌似无效，可能是对数据格式要求比较严格
				'do_mass_by_group' => 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=ACCESS_TOKEN',
				'do_mass_by_id' => 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=ACCESS_TOKEN',
				'get_mass_status' => 'https://api.weixin.qq.com/cgi-bin/message/mass/get?access_token=ACCESS_TOKEN',
				//'up_media_short_time' => 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=ACCESS_TOKEN&type=TYPE', // 原上传多媒体文件接口
				'up_media_short_time'=>'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=ACCESS_TOKEN&type=TYPE',// 上传临时文件
				'up_img_for_news_content'=>'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=ACCESS_TOKEN',//上传图文正文内的图片，不占用素材数量(^_^)
				'up_video_for_mass' => 'http://file.api.weixin.qq.com/cgi-bin/media/uploadvideo?access_token=ACCESS_TOKEN', //官方提供的url 是https协议,但是curl时出错
				'get_media_file'=>'https://api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id=MEDIA_ID', //获取临时素材=“下载多媒体文件”接口
		] 
];