<?php
namespace app\install\model;
use think\Model;
use think\db;

class Index extends Model{
	 
	/**
	 * install
	 * 安装
	 * @return mixed[] 错误信息
	 */
	public function install(){
		global $ecms_config;
		$dbtbpre=$ecms_config['db']['dbtbpre'];
		$phome_db_dbchar=$ecms_config['db']['setchar'];
		//检查是否重复安装,同时预防中彩票式地安装了相同数据表
		$name[]='wx_wx';
		$name[]='wx_app';
		$name[]='wx_msg';
		$name[]='wx_news';
		$name[]='wx_msgreply';
		$name[]='wx_reply';
		$name[]='wx_file';
		$name[]='wx_mass';
		$name[]='wx_user';
		$name[]='wx_menu';
		
		$check=null;
		foreach($name as $k=>$v){
			
			$table=$dbtbpre.$v;
			
			$r=Db::query("SHOW TABLEs LIKE '{$table}'");
			if(count($r)!=0){
				$check[]=$table;
			}
		}
		
		if($check){
			$result='';
			foreach($check as $k => $v){
				$result.=$v.'，';
			}
			$html="您已经安装过，或通过其他渠道安装了下列数据表:<br>".$result."如需继续，则需先卸载、再重新安装，或删除上述数据表。<br>注意，卸载或删除操作不可恢复，请谨慎操作。";
			return ['errCode'=>606,'errMsg'=>$html];
		}
		//建立微信插件主表index
		Db::execute("drop table IF EXISTS `{$dbtbpre}wx_wx`");
		Db::execute("CREATE TABLE `{$dbtbpre}wx_wx` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
            `name` varchar(40) NOT NULL COMMENT '公众号名称',
            `type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '类型，如未认证订阅号',
            `app_id` varchar(50) DEFAULT NULL COMMENT '与微信后台对应',
            `app_secret` varchar(50) DEFAULT NULL COMMENT '与微信后台对应',
            `way_of_key` varchar(255) DEFAULT NULL COMMENT '加密方式',
            `encoding_aes_key` varchar(255) DEFAULT NULL COMMENT '微信加密密钥',
            `token` varchar(255) DEFAULT NULL COMMENT '令牌/口令',
            `gid` varchar(40) DEFAULT NULL COMMENT '微信原始ID',
            `active` tinyint(3) NOT NULL DEFAULT '0' COMMENT '主/活跃公众号',
            `access_token` varchar(255) DEFAULT NULL COMMENT '全局唯一票据',
            `access_token_time` varchar(20) DEFAULT NULL COMMENT '全局唯一票据获取时间',
            `menu` varchar(255) DEFAULT NULL COMMENT '本地编辑菜单',
            `cg_menu` varchar(255) DEFAULT NULL COMMENT '本地编辑菜单',
            `web_menu` varchar(255) DEFAULT NULL COMMENT '正在使用的菜单',
            `update_time` varchar(255) DEFAULT NULL COMMENT '最后更新时间',
            `create_time` varchar(255) DEFAULT NULL COMMENT '创建时间',
            `token_expire` bigint(20) DEFAULT '14' COMMENT 'token到期时间',
            PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET={$phome_db_dbchar};");
		//建立app设置记录表
		Db::execute("drop table IF EXISTS `{$dbtbpre}wx_app`");
		Db::execute("CREATE TABLE `{$dbtbpre}wx_app` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`aid` varchar(255) NOT NULL default '0' COMMENT '关联微信号，默认0全部关注',
			`app_name` varchar(100) NULL COMMENT '应用名称',
			`is_ok` tinyint(3) NOT NULL DEFAULT '1' COMMENT '是否启用',
			`type` varchar(20) NULL COMMENT '关键词或key值',
			`keyword` varchar(255) NULL COMMENT '关键字',
			`class_name` varchar(50) NULL COMMENT '类名',
			`fun_name` varchar(50) NULL COMMENT '函数名',
			`description` text NULL COMMENT '应用注释',
			`level` tinyint(8) NOT NULL DEFAULT '0' COMMENT '优先级',
			`update_time` varchar(255) NULL comment '最后更新时间',
			`create_time` varchar(255) NULL comment '创建时间',
			PRIMARY KEY (`id`),
			KEY `level` (`level`)
		) ENGINE=MyISAM DEFAULT CHARSET={$phome_db_dbchar};");
		//建立信息表msg
		Db::execute("drop table IF EXISTS `{$dbtbpre}wx_msg`");
		Db::execute("CREATE TABLE `{$dbtbpre}wx_msg` (
			`id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
			`aid` varchar(255) NOT NULL default '0' COMMENT '关联微信公众号',
			`is_reply` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否已经回复',
			`is_keep` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否为收藏消息',
			`is_keyword` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否为关键词消息',
			`my_name` varchar(255) NULL COMMENT '公众账号名',
			`user_name` varchar(255) NULL COMMENT '关注者Openid',
			`msg_type` varchar(100) NULL COMMENT '消息类型',
			`msg_id` varchar(255) NULL COMMENT '消息ID',
			`media_id` varchar(255) NULL COMMENT '媒体ID',
			`content` text COMMENT '文本消息内容',
			`img_url` varchar(255) NULL COMMENT '图片链接',
			`format` varchar(100) NULL COMMENT '语音格式',
			`recognition` text COMMENT '语音识别内容',
			`thumb_media_id` varchar(255) NULL COMMENT '缩略图媒体id',
			`location_x` varchar(50) NULL COMMENT '纬度',
			`location_y` varchar(50) NULL COMMENT '经度',
			`scale` varchar(10) NULL COMMENT '地图缩放大小',
			`label` varchar(255) NULL COMMENT '位置信息',
			`title` varchar(255) NULL COMMENT '消息标题',
			`description` varchar(255) COMMENT '描述',
			`url` varchar(255) NULL COMMENT '消息链接',
			`event` varchar(255) NULL COMMENT '事件',
			`event_key` varchar(255) NULL COMMENT '事件KEY值',
			`ticket` varchar(50) NULL COMMENT '二维码票据',
			`latitude` varchar(10) NULL COMMENT '地理位置纬度',
			`longitude` varchar(10) NULL COMMENT '地理位置经度',
			`precision` varchar(50) NULL COMMENT '地理位置精度',
			`update_time` varchar(255) NULL comment '最后更新时间',
			`create_time` varchar(255) NULL comment '创建时间',
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET={$phome_db_dbchar};");
		//建立素材表news
		Db::execute("drop table IF EXISTS `{$dbtbpre}wx_news`");
		Db::execute("CREATE TABLE `{$dbtbpre}wx_news` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`aid` varchar(255) NOT NULL default '0' COMMENT '关联微信，可用数组序列化表示多个',
			`ids` varchar(255) NULL COMMENT '多图文中单图id集合json',
			`share` tinyint(3) NOT NULL default '0' COMMENT '共享其他微信开关',
			`is_ok` tinyint(3) NOT NULL default '2' COMMENT '是否启用，默认2',
			`is_open_outside` tinyint(3) NOT NULL default '0' COMMENT '是否开启外链，默认0',
			`outside_url` varchar(255) NULL COMMENT '外链地址',
			`title` varchar(255) NOT NULL COMMENT '标题',
			`group` varchar(255) NOT NULL default '0' COMMENT '分组',
			`author` varchar(50) NULL COMMENT '来源作者',
			`sender` varchar(50) NULL COMMENT '发布者',
			`title_img` varchar(255) NULL COMMENT '封面图片',
			`abstract` varchar(255) NULL COMMENT '内容简介',
			`content` text COMMENT '正文',
			`send_time` varchar(50) NULL COMMENT '发布时间',
			`is_link_img` tinyint(3) NOT NULL DEFAULT '1' COMMENT '正文是否加封面图片,默认1加',
			`url` varchar(255) NULL COMMENT '原文链接',
			`media_id` varchar(255) NULL comment '微信端media_id',
			`create_at` varchar(255) NULL comment '上传至微信的时间',
			`update_time` varchar(255) NULL comment '最后更新时间',
			`create_time` varchar(255) NULL comment '创建时间',
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET={$phome_db_dbchar};");
		//建立手动回复消息记录表msgreply
		Db::execute("drop table IF EXISTS `{$dbtbpre}wx_msgreply`");
		Db::execute("CREATE TABLE `{$dbtbpre}wx_msgreply` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`aid` varchar(255) NOT NULL DEFAULT '0' COMMENT '关联微信公众号',
			`msg_id` varchar(255) NULL COMMENT '被回复消息id',
			`my_name` varchar(255) NULL COMMENT '公众号账号',
			`user_name` varchar(255) NULL COMMENT '关注者openid',
			`msg_type` varchar(100) NULL COMMENT '消息类型',
			`text` varchar(255) NULL COMMENT '文本类型内容',
			`img` varchar(255) NULL COMMENT '图片类型url',
			`voice` varchar(255) NULL COMMENT '语音类型url',
			`video` varchar(255) NULL COMMENT '视频类型url',
			`music` varchar(255) NULL COMMENT '音乐类型url',
			`news` varchar(255) NULL COMMENT '图文类型json',
			`update_time` varchar(255) NULL comment '最后更新时间',
			`create_time` varchar(255) NULL comment '创建时间',
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET={$phome_db_dbchar};");
		//建立自动回复表reply
		Db::execute("drop table IF EXISTS `{$dbtbpre}wx_reply`");
		Db::execute("CREATE TABLE `{$dbtbpre}wx_reply` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`aid` varchar(255) NOT NULL DEFAULT '0' COMMENT '关联微信公众号',
			`type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '自动回复类型',
			`is_ok` tinyint(3) NOT NULL DEFAULT '1' COMMENT '是否启用',
			`is_menu_key` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否菜单key值',
			`is_like` tinyint(3) NOT NULL DEFAULT '0' COMMENT '模糊匹配',
			`keyword` varchar(255) NULL COMMENT '关键字',
			`msg_type` varchar(255) NOT NULL DEFAULT 'text' COMMENT '回复消息格式',
			`level` int(10) NOT NULL DEFAULT '0' COMMENT '应用权限的优先级',
			`text` text COMMENT '文本消息',
			`img` varchar(255) COMMENT '图片消息',
			`voice` varchar(255) COMMENT '语音消息',
			`video` varchar(255) COMMENT '视频消息',
			`music` varchar(255) COMMENT '音乐消息',
			`news` varchar(255) COMMENT '图文消息',
			`media_id` varchar(255) COMMENT '媒体id',
			`update_time` varchar(255) NULL comment '最后更新时间',
			`create_time` varchar(255) NULL comment '创建时间',
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET={$phome_db_dbchar};");
		//建立文件附件表file
		Db::execute("drop table IF EXISTS `{$dbtbpre}wx_file`");
		Db::execute("CREATE TABLE `{$dbtbpre}wx_file` (
			`id` int(10) NOT NULL AUTO_INCREMENT COMMENT '附件id',
            `aid` varchar(255) NOT NULL DEFAULT '0' COMMENT '关联微信',
            `title` varchar(255) DEFAULT NULL COMMENT '标题',
            `description` varchar(255) DEFAULT NULL COMMENT '说明',
            `name` varchar(255) DEFAULT NULL COMMENT '文件名',
            `type` varchar(255) DEFAULT NULL COMMENT '附件类型',
            `is_ok` tinyint(3) NOT NULL DEFAULT '1' COMMENT '开启',
            `path` varchar(255) DEFAULT NULL COMMENT '文件路径',
            `size` int(11) DEFAULT NULL COMMENT '文件大小',
            `ext` varchar(20) DEFAULT NULL COMMENT '文件扩展名',
            `lifecycle` varchar(30) DEFAULT NULL COMMENT '微信保存时间长短',
            `group` tinyint(2) DEFAULT NULL COMMENT '文件分类',
            `media_id` varchar(255) DEFAULT NULL COMMENT '微信端地址',
            `up_to_wx_time` varchar(255) DEFAULT NULL COMMENT '临时媒体上传至微信时间',
            `url` varchar(255) DEFAULT NULL COMMENT '微信端（永久）图片地址',
            `thumb_id` int(11) DEFAULT NULL COMMENT '视频缩略图id,关联本表数据',
            `thumb_url` varchar(255) DEFAULT NULL COMMENT '作为临时缩略图的微信url',
            `thumb_media_id` varchar(255) DEFAULT NULL COMMENT '作为临时缩略图的微信media_id',
            `thumb_up_time` varchar(255) DEFAULT NULL COMMENT '作为临时缩略图上传至微信时间',
            `thumb` varchar(255) DEFAULT NULL COMMENT '视频缩略图(本地地址)',
            `thumb_long_media_id` varchar(255) DEFAULT NULL COMMENT '作为永久缩略图的media_id',
            `thumb_long_url` varchar(255) DEFAULT NULL COMMENT '作为永久缩略图的url',
            `short_media_id` varchar(255) DEFAULT NULL COMMENT '临时素材id',
            `news_url` varchar(255) DEFAULT NULL COMMENT '图文正文图片url',
            `update_time` varchar(255) DEFAULT NULL COMMENT '最后更新时间',
            `create_time` varchar(255) DEFAULT NULL COMMENT '创建时间',
            PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET={$phome_db_dbchar};");
	
		//建立群发列表mass
		Db::execute("drop table IF EXISTS `{$dbtbpre}wx_mass`");
		Db::execute("CREATE TABLE `{$dbtbpre}wx_mass` (
			`id` int(10) NOT NULL AUTO_INCREMENT COMMENT '群发id',
			`aid` varchar(255) NOT NULL default '0' COMMENT '关联微信',
			`is_ok` int(2) NULL COMMENT '草稿标记',
			`is_auto` tinyint(3) NULL COMMENT '自动',
			`group` varchar(255) NULL COMMENT '用户组',
			`sex` varchar(1) NULL COMMENT '性别',
			`area` varchar(255) NULL COMMENT '地区',
			`msg_type` varchar(255) NULL COMMENT '群发内容类型',
			`text` varchar(255) NULL COMMENT '群发文本',
			`img` varchar(255) NULL COMMENT '群发图片',
			`voice` varchar(255) NULL COMMENT '群发语音',
			`video` varchar(255) NULL COMMENT '群发视频',
			`news` varchar(255) NULL COMMENT '群发图文',
			`send_time` varchar(255) NULL COMMENT '预约时间',
			`is_do` tinyint(3) NULL COMMENT '执行标记',
			`do_send_time` varchar(255) NULL COMMENT '执行时间',
			`media_id` varchar(255) NULL COMMENT '微信端地址',
			`url` varchar(255) NULL COMMENT '微信端图片地址',
			`msg_id` varchar(255) NULL COMMENT '微信群发id',
			`msg_status` varchar(255) NULL COMMENT '微信群发状态',
			`total_count` varchar(255) NULL COMMENT '计划接收人数',
			`filter_count` varchar(255) NULL COMMENT '允许发送人数',
			`sent_count` varchar(255) NULL COMMENT '成功发送人数',
			`error_count` varchar(255) NULL COMMENT '失败发送人数',
			`msg_data_id` varchar(255) NULL COMMENT '图文群发分析接口ID',
			`update_time` varchar(255) NULL comment '最后更新时间',
			`create_time` varchar(255) NULL comment '创建时间',
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET={$phome_db_dbchar};");
		//建立用户列表user
		Db::execute("drop table IF EXISTS `{$dbtbpre}wx_user`");
		Db::execute("CREATE TABLE `{$dbtbpre}wx_user` (
			`id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
			`aid` varchar(255) NOT NULL default '0' COMMENT '关联微信',
			`union_id` varchar(255) NULL COMMENT '用户id，主',
			`open_id` varchar(255) NULL COMMENT '用户openid，次',
			`subscribe` varchar(255) NULL COMMENT '订阅',
			`nick_name` varchar(255) NULL COMMENT '昵称',
			`sex` tinyint(3) NULL COMMENT '性别',
			`city` varchar(255) NULL COMMENT '城市',
			`country` varchar(255) NULL COMMENT '国家',
			`province` varchar(255) NULL COMMENT '省份',
			`language` varchar(255) NULL COMMENT '语言',
			`head_img_url` varchar(255) NULL COMMENT '头像地址',
			`subscribe_time` varchar(255) NULL COMMENT '关注时间',
			`remark` varchar(255) NULL COMMENT '备注',
			`group_id` varchar(255) NULL COMMENT '用户组id',
			`update_time` varchar(255) NULL comment '最后更新时间',
			`create_time` varchar(255) NULL comment '创建时间',
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET={$phome_db_dbchar};");
		//建立菜单表menu
		Db::execute("drop table IF EXISTS `{$dbtbpre}wx_menu`");
		Db::execute("CREATE TABLE `{$dbtbpre}wx_menu` (
			`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
            `aid` varchar(255) NOT NULL DEFAULT '0' COMMENT '关联微信',
            `menu` text COMMENT '菜单json',
            `location` varchar(255) DEFAULT NULL COMMENT '位置（本地/云端）',
            `type` varchar(255) NOT NULL DEFAULT '0' COMMENT '类型',
            `active` varchar(255) DEFAULT NULL COMMENT '是否生效',
            `up_to_wx_time` varchar(255) DEFAULT NULL COMMENT '上传至微信时间',
            `update_time` varchar(255) DEFAULT NULL COMMENT '最后更新时间',
            `create_time` varchar(255) DEFAULT NULL COMMENT '创建时间',
            `title` varchar(255) DEFAULT NULL COMMENT '标题，用于区分多个菜单组',
            PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET={$phome_db_dbchar};");
		//增加插件菜单
		$data=['classname'=>'微信公众号管理','issys'=>'0','myorder'=>'0','classtype'=>'2','groupids'=>''];
		$menuclassid=Db::name('enewsmenuclass')->insertGetId($data);
		$baseUrl = $this->getPath();
		Db::execute("insert into `{$dbtbpre}enewsmenu` values(NULL,'基本设置','{$baseUrl}/index','0','$menuclassid','2');");
		Db::execute("insert into `{$dbtbpre}enewsmenu` values(NULL,'消息管理','{$baseUrl}/msg','1','$menuclassid','2');");
		Db::execute("insert into `{$dbtbpre}enewsmenu` values(NULL,'自动回复','{$baseUrl}/reply','2','$menuclassid','2');");
		Db::execute("insert into `{$dbtbpre}enewsmenu` values(NULL,'图文管理','{$baseUrl}/news','3','$menuclassid','2');");
		Db::execute("insert into `{$dbtbpre}enewsmenu` values(NULL,'附件管理','{$baseUrl}/file','4','$menuclassid','2');");
		//Db::execute("insert into `{$dbtbpre}enewsmenu` values(NULL,'应用管理','{$baseUrl}/app.php','5','$menuclassid','2');");
		Db::execute("insert into `{$dbtbpre}enewsmenu` values(NULL,'关注者管理','{$baseUrl}/user','6','$menuclassid','2');");
		Db::execute("insert into `{$dbtbpre}enewsmenu` values(NULL,'自定义菜单','{$baseUrl}/menu','7','$menuclassid','2');");
		Db::execute("insert into `{$dbtbpre}enewsmenu` values(NULL,'群发管理','{$baseUrl}/mass','8','$menuclassid','2');");
		//Db::execute("insert into `{$dbtbpre}enewsmenu` values(NULL,'卸载本插件','{$baseUrl}/install','9','$menuclassid','2');");
	}
	public function getPath(){
	    if(PHP_OS === 'Linux'){
	        $arr = explode('/',rtrim(ROOT_PATH,'/'));
	    }else{
	        $arr = explode('\\',rtrim(ROOT_PATH,'\\'));
	    }
	    return "../extend/".$arr[count($arr)-1]."/public/index.php";
	}
	/**
	 * uninstall
	 * 卸载
	 */
	public function uninstall(){
		global $ecms_config;
		$dbtbpre=$ecms_config['db']['dbtbpre'];
// 		$phome_db_dbchar=$ecms_config['db']['setchar'];
		//删除表
		Db::execute("DROP TABLE IF EXISTS `{$dbtbpre}wx_wx`,`{$dbtbpre}wx_app`,`{$dbtbpre}wx_msg`,`{$dbtbpre}wx_news`,`{$dbtbpre}wx_msgreply`,`{$dbtbpre}wx_reply`,`{$dbtbpre}wx_file`,`{$dbtbpre}wx_set`,`{$dbtbpre}wx_mass`,`{$dbtbpre}wx_user`,`{$dbtbpre}wx_menu`;");
		//删除插件菜单
		$menuclassr=Db::query("select classid from {$dbtbpre}enewsmenuclass where classname='微信公众号管理' limit 1");
		if(isset($menuclassr[0])){ //屏蔽未安装就卸载的Bug
			$menuclassr=$menuclassr[0];
			Db::execute("delete from {$dbtbpre}enewsmenuclass where classid='$menuclassr[classid]'");
			Db::execute("delete from {$dbtbpre}enewsmenu where classid='$menuclassr[classid]'");
		}
	}
}