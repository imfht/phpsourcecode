<?php
/***************************************************************************
 *                            lang_admin.php [Russian]
 *                              -------------------
 *     begin                : Sat Dec 16 2000
 *     copyright            : (C) 2001 The phpBB Group
 *     email                : support@phpbb.com
 *
 *     $Id$
 *
 ****************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

//
// Перевод: Alexey V. Borzov (borz_off) borz_off@cs.msu.su
// Доработка, сопровождение и поддержка: официальный сайт поддержки phpBB на русском языке http://www.phpbbguru.net/
//

$lang['General'] = '网站的相关管理';
$lang['Users'] = '会员相关管理';
$lang['Groups'] = '小组相关管理';
$lang['Forums'] = '论坛相关管理';
$lang['Styles'] = '风格管理';

$lang['Configuration'] = '全局设置';
$lang['Permissions'] = '论坛权限';
$lang['Manage'] = '管理';
$lang['Disallow'] = '敏感用户名';
$lang['Prune'] = '清除';
$lang['Mass_Email'] = '群发邮件';
$lang['Ranks'] = '用户等级';
$lang['Smilies'] = '表情图标';
$lang['Ban_Management'] = '黑名单';
$lang['Word_Censor'] = '敏感词';
$lang['Export'] = '导出';
$lang['Create_new'] = '创建';
$lang['Add_new'] = '添加';
$lang['Backup_DB'] = '备份数据';
$lang['Restore_DB'] = '还原数据';


//
// Index
//
$lang['Admin'] = '系统管理';
$lang['Not_admin'] = '您没有权限进入管理员控制面板';
$lang['Welcome_phpBB'] = '欢迎使用 phpBB';
$lang['Admin_intro'] = '谢谢您选择 phpBB 作为您的论坛解决方案. 该页面将快捷地显示论坛当前状态. 您可以点击左边的 <u>管理目录</u> 链接. 返回论坛首页, 点击左边的 phpBB 图标. 点击左边的其他链接将获得论坛的种种控制功能, 每个页面都会获得相关的使用提示.';
$lang['Main_index'] = '论坛首页';
$lang['Forum_stats'] = '论坛统计';
$lang['Admin_Index'] = '管理目录';
$lang['Preview_forum'] = '预览论坛';

$lang['Click_return_admin_index'] = '点击 %s这里%s 返回超级管理页面';

$lang['Statistic'] = '论坛统计';
$lang['Value'] = '统计数值';
$lang['Number_posts'] = '发贴数量';
$lang['Posts_per_day'] = '平均每日发贴';
$lang['Number_topics'] = '主题数量';
$lang['Topics_per_day'] = '平均每日主题';
$lang['Number_users'] = '注册成员';
$lang['Users_per_day'] = '平均每日注册';
$lang['Board_started'] = '论坛开始';
$lang['Avatar_dir_size'] = '头像目录大小';
$lang['Database_size'] = '数据库大小';
$lang['Gzip_compression'] ='Gzip 压缩';
$lang['Not_available'] = '没有数值';

$lang['ON'] = '开启'; // This is for GZip compression
$lang['OFF'] = '关闭'; 



//
// DB Utils
//
$lang['Database_Utilities'] = '数据工具';

$lang['Restore'] = '恢复';
$lang['Backup'] = '备份';
$lang['Restore_explain'] = '在这个选项中您可以恢复 phpBB 2 所使用的数据库表格. 如果您的服务器支持 GZIP 压缩的文件, 服务器将会自动解压您所上传的压缩文件. <b>注意！</b> 恢复过程中将会完全覆盖所有现存的资料. 数据库恢复过程可能会花费较长的时间, 在恢复完成前请不要关闭或离开这个页面.';
$lang['Backup_explain'] = '在这个选项中,您可以备份 phpBB 2 论坛的所有资料数据. 如果您有其它自行定义数据表放在 phpBB 2 论坛所使用的数据库内, 而且您也想备份这些的表格, 请在下方的 <b>附加数据表</b> 栏内输入它们的名字并用逗号区别开 (例如: abc, cde). 如果您的服务器有支持 GZIP 压缩格式, 您可以在下载前使用 GZIP 压缩来减小文件的大小.';

$lang['Backup_options'] = '备份选项';
$lang['Start_backup'] = '开始备份';
$lang['Full_backup'] = '完整备份';
$lang['Structure_backup'] = '结构备份';
$lang['Data_backup'] = '数据备份';
$lang['Additional_tables'] = '附加数据表';
$lang['Gzip_compress'] = 'Gzip 压缩格式';
$lang['Select_file'] = '选择文件';
$lang['Start_Restore'] = '开始恢复';

$lang['Restore_success'] = '数据库成功恢复.<br /><br />论坛已被恢复成备份时的状态.';
$lang['Backup_download'] = '请等待. 您的备份文件将被下载!';
$lang['Backups_not_supported'] = '对不起! 备份数据不支持您的数据库系统';

$lang['Restore_Error_uploading'] = '上传备份文件有误';
$lang['Restore_Error_filename'] = '文件名称有问题, 请重选文件再试';
$lang['Restore_Error_decompress'] = '不能解压 gzip 文件, 请上传纯文本文件';
$lang['Restore_Error_no_file'] = '没有文件可以上传';


//
// Auth pages
//
$lang['Select_a_User'] = '选择用户';
$lang['Select_a_Group'] = '选择一个团队';
$lang['Select_a_Forum'] = '选择一个论坛';
$lang['Auth_Control_User'] = '用户权限控制'; 
$lang['Auth_Control_Group'] = '团队权限控制'; 
$lang['Auth_Control_Forum'] = '论坛权限控制'; 
$lang['Look_up_User'] = '查询用户'; 
$lang['Look_up_Group'] = '查询团队'; 
$lang['Look_up_Forum'] = '查询论坛';

$lang['Group_auth_explain'] = '在这个选项中您可以更改团队的权限设定及指定管理员资格. 请注意, 修改团队权限设定后, 独立的用户权限可能仍然可以使用户进入限制论坛. 如果发生这种情况将会显示权限冲突的警告.';
$lang['User_auth_explain'] = '在这个选项中您可以更改用户的权限设定及指定管理员资格. 请注意, 修改用户权限设定后, 独立的用户权限可能仍然可以使用户进入限制论坛. 如果发生这种情况将会显示权限冲突的警告.';
$lang['Forum_auth_explain'] = '在这个选项中您可以更改论坛的使用权限. 您可以选择使用简单或是高级两种模式, 高级模式能提供您完整的权限设定控制. 请注意, 所有的改变都将会影响到用户的论坛使用权限.';

$lang['Simple_mode'] = '简单模式';
$lang['Advanced_mode'] = '高级模式';
$lang['Moderator_status'] = '版主资格';

$lang['Allowed_Access'] = '允许进入';
$lang['Disallowed_Access'] = '禁止进入';
$lang['Is_Moderator'] = '是版主';
$lang['Not_Moderator'] = '非版主';

$lang['Conflict_warning'] = '权限冲突警告';
$lang['Conflict_access_userauth'] = '这个用户仍然可以通过团队成员的资格进入特定的论坛. 您可以更改团队权限或是取消这个用户的团队资格来禁止该用户进入限制的论坛.团队权限如下:';
$lang['Conflict_mod_userauth'] = '这个用户仍然可以通过团队成员的资格拥有论坛管理的权限. 您可以更改团队权限或是取消这个用户的权限来禁止该用户进行论坛管理.论坛管理权限如下:';

$lang['Conflict_access_groupauth'] = '下列用户仍然可以通过用户权限设定进入这个特定的论坛. 您可以更改用户权限来取消他们进入限制的论坛. 用户权限如下: ';
$lang['Conflict_mod_groupauth'] = '下列用户依然可以通过他们的用户权限拥有论坛管理的权限. 您可以更改用户权限来取消他们的论坛管理权限. 用户权限如下: ';

$lang['Public'] = '公开';
$lang['Private'] = '加密';
$lang['Registered'] = '注册用户';
$lang['Administrators'] = '超级用户';
$lang['Hidden'] = '隐藏';

// These are displayed in the drop down boxes for advanced
// mode forum auth, try and keep them short!
$lang['Forum_ALL'] = '全部用户';
$lang['Forum_REG'] = '注册用户';
$lang['Forum_PRIVATE'] = '秘密成员';
$lang['Forum_MOD'] = '论坛版主';
$lang['Forum_ADMIN'] = '论坛管理';

$lang['View'] = '浏览';
$lang['Read'] = '阅读';
$lang['Post'] = '发表';
$lang['Reply'] = '回复';
$lang['Edit'] = '编辑';
$lang['Delete'] = '删除';
$lang['Sticky'] = '置顶';
$lang['Announce'] = '公告'; 
$lang['Vote'] = '投票';
$lang['Pollcreate'] = '建立投票';

$lang['Permissions'] = '权限设置';
$lang['Simple_Permission'] = '基本权限';

$lang['User_Level'] = '用户等级'; 
$lang['Auth_User'] = '注册用户';
$lang['Auth_Admin'] = '超级用户';
$lang['Group_memberships'] = '用户团队列表';
$lang['Usergroup_members'] = '团队成员列表';

$lang['Forum_auth_updated'] = '论坛权限设定更新';
$lang['User_auth_updated'] = '用户权限设定更新';
$lang['Group_auth_updated'] = '团队权限设定更新';

$lang['Auth_updated'] = '权限设定已经更新';
$lang['Click_return_userauth'] = '点击 %s这里%s 返回用户权限设定';
$lang['Click_return_groupauth'] = '点击 %s这里%s 返回团队权限设定';
$lang['Click_return_forumauth'] = '点击 %s这里%s 返回论坛权限设定';


//
// Banning
//
$lang['Ban_control'] = '封锁控制';
$lang['Ban_explain'] = '在这个选项中您可以设定用户的封锁. 您可以封锁一个指定的用户，一个指定范围的 IP 地址或是计算机主机名称, 这些方法禁止被封锁的用户进入论坛首页. 您也可以指定封锁电子邮件地址来防止注册用户使用不同的帐号重复注册. 请注意当您只是封锁一个电子邮件地址时将不会影响到用户在您论坛的登陆或是发表文章, 您应该使用前面两种方式其中之一或是两种一起来建立封锁.';
$lang['Ban_explain_warn'] = '当您输入一个IP地址范围时, 这个范围内所有的IP地址都将会被封锁. 您可以使用统配符 * 定义要封锁的ip地址来降低被攻击的可能. 如果您一定要输入一个范围请尽量保持精简和适当以免影响正常的使用.';

$lang['Select_username'] = '选择一个用户名称';
$lang['Select_ip'] = '选择一个 IP 地址';
$lang['Select_email'] = '选择一个邮件地址';

$lang['Ban_username'] = '封锁一个或多个指定的用户名称';
$lang['Ban_username_explain'] = '您可以使用鼠标和组合键 (如: Ctrl 或 Shift)一次封锁多个用户名称';

$lang['Ban_IP'] = '封锁一个或多个 IP 地址或是计算机主机名称';
$lang['IP_hostname'] = 'IP 地址或是计算机主机名称';
$lang['Ban_IP_explain'] = '要指定多个不同的 IP 地址或是主机名称, 请使用逗号 (,) 来分隔它们. 要指定 IP 地址的范围, 请使用 (-) 来分隔起始地址及结束地址, 或是使用统配符 (*)';

$lang['Ban_email'] = '封锁一个或多个电子邮件地址';
$lang['Ban_email_explain'] = '要指定多个不同的电子邮件地址, 请使用逗号 (,) 来分隔它们, 或是使用通配符 (*), 例如: *@hotmail.com';

$lang['Unban_username'] = '解除一个或多个封锁的用户名称';
$lang['Unban_username_explain'] = '您可以使用鼠标及组合键 (如: Ctrl 或 Shift)一次解除多个封锁的用户名称';

$lang['Unban_IP'] = '解除一个或多个封锁的 IP 地址';
$lang['Unban_IP_explain'] = '您可以使用鼠标及组合键 (例如: Ctrl 或 Shift), 一次解除多个封锁的 IP 地址';

$lang['Unban_email'] = '解除一个或多个封锁的电子邮件地址';
$lang['Unban_email_explain'] = '您可以使用鼠标及组合键 (例如: Ctrl 或 Shift), 一次解除多个封锁的电子邮件地址';

$lang['No_banned_users'] = '没有被封锁的用户名称';
$lang['No_banned_ip'] = '没有被封锁的 IP 地址';
$lang['No_banned_email'] = '没有被封锁的电子邮件地址';

$lang['Ban_update_sucessful'] = '封锁列表已经成功更新';
$lang['Click_return_banadmin'] = '点击 %s这里%s 返回封锁设定';


//
// Configuration
//
$lang['General_Config'] = '基本配置';
$lang['Config_explain'] = '该表单允许您定制整个论坛的一些配置选项. 具体的用户和论坛配置请点击左边相应的链接.';

$lang['Click_return_config'] = '点击 %s这里%s 返回基本配置';

$lang['General_settings'] = '论坛基本设置';
$lang['Server_name'] = '网站域名';
$lang['Server_name_explain'] = '论坛所运行位置的网站域名';
$lang['Script_path'] = '脚本路径';
$lang['Script_path_explain'] = '与论坛域名相对应的路径';
$lang['Server_port'] = '服务端口';
$lang['Server_port_explain'] = '您的服务器所运行的端口,默认值是80,只有在非默认值时改变这个选项';
$lang['Site_name'] = '论坛名称';
$lang['Site_desc'] = '论坛描述';
$lang['Board_disable'] = '关闭论坛';
$lang['Board_disable_explain'] = '这将会关闭论坛. 当您执行这个设定时请勿登出,您将无法重新登陆!';
$lang['Acct_activation'] = '启用帐号激活';
$lang['Acc_None'] = '关闭'; // These three entries are the type of activation
$lang['Acc_User'] = '注册用户激活';
$lang['Acc_Admin'] = '超级用户激活';

$lang['Abilities_settings'] = '用户及论坛基本设定';
$lang['Max_poll_options'] = '投票项目的最大数目';
$lang['Flood_Interval'] = '灌水判断';
$lang['Flood_Interval_explain'] = '文章发表的间隔时间 (秒)'; 
$lang['Board_email_form'] = '用户电子邮件列表';
$lang['Board_email_form_explain'] = '用户可以互相发送电子邮件在这个论坛';
$lang['Topics_per_page'] = '每页显示主题数';
$lang['Posts_per_page'] = '每页显示发表数';
$lang['Hot_threshold'] = '热门话题设定数';
$lang['Default_style'] = '预设风格';
$lang['Override_style'] = '忽视用户选择的风格';
$lang['Override_style_explain'] = '将用户所选的风格改为预设风格';
$lang['Default_language'] = '预设语言';
$lang['Date_format'] = '日期格式';
$lang['System_timezone'] = '系统时区';
$lang['Enable_gzip'] = '开启 GZip 文件压缩格式';
$lang['Enable_prune'] = '开启计划删除模式';
$lang['Allow_HTML'] = '允许使用 HTML 语法';
$lang['Allow_BBCode'] = '允许使用 BBCode 代码';
$lang['Allowed_tags'] = '允许使用 HTML 标签';
$lang['Allowed_tags_explain'] = '以逗号分隔 HTML 标签';
$lang['Allow_smilies'] = '允许使用表情符号';
$lang['Smilies_path'] = '表情符号储存路径';
$lang['Smilies_path_explain'] = '在您 phpBB 2 根目录底下的路径, 例如: images/smilies';
$lang['Allow_sig'] = '允许使用签名档';
$lang['Max_sig_length'] = '签名档长度限定';
$lang['Max_sig_length_explain'] = '用户个人签名最多可使用字数';
$lang['Allow_name_change'] = '允许更改登陆名称';

$lang['Avatar_settings'] = '个性头像设定';
$lang['Allow_local'] = '使用系统相册';
$lang['Allow_remote'] = '允许链接头像图片';
$lang['Allow_remote_explain'] = '从其他网址链接头像图片';
$lang['Allow_upload'] = '允许用户上传头像';
$lang['Max_filesize'] = '头像文件大小设定';
$lang['Max_filesize_explain'] = '由用户上传头像图片';
$lang['Max_avatar_size'] = '图片大小不可大于';
$lang['Max_avatar_size_explain'] = '(高 x 宽 像素)';
$lang['Avatar_storage_path'] = '个性头像储存路径';
$lang['Avatar_storage_path_explain'] = '在您 phpBB 2 根目录底下的路径, 例如: images/avatars';
$lang['Avatar_gallery_path'] = '系统相册储存路径';
$lang['Avatar_gallery_path_explain'] = '在您 phpBB 2 根目录底下的路径, 例如: images/avatars/gallery';

$lang['COPPA_settings'] = 'COPPA (美国儿童网路隐私保护法) 设定';
$lang['COPPA_fax'] = 'COPPA 传真号码';
$lang['COPPA_mail'] = 'COPPA 邮递地址';
$lang['COPPA_mail_explain'] = '这是供家长寄送 COPPA 用户注册申请书的邮递地址';

$lang['Email_settings'] = '电子邮件设定';
$lang['Admin_email'] = '管理员邮件地址';
$lang['Email_sig'] = '电子邮件签名档';
$lang['Email_sig_explain'] = '这个签名档将会被附加在所有由论坛系统送出的电子邮件中';
$lang['Use_SMTP'] = '使用 SMTP 服务器发送电子邮件';
$lang['Use_SMTP_explain'] = '如果您想要使用 SMTP 服务器发送电子邮件请选择 是';
$lang['SMTP_server'] = 'SMTP 服务器名称';
$lang['SMTP_username'] = 'SMTP 用户名';
$lang['SMTP_username_explain'] = '只有您的smtp服务器要求用户时才填写这个选项';
$lang['SMTP_password'] = 'SMTP 密码';
$lang['SMTP_password_explain'] = '只有您的smtp服务器要求密码时才填写这个选项';

$lang['Disable_privmsg'] = '私人消息';
$lang['Inbox_limits'] = '收件夹最大容量';
$lang['Sentbox_limits'] = '寄件夹最大容量';
$lang['Savebox_limits'] = '储存夹最大容量';

$lang['Cookie_settings'] = 'Cookie 设定'; 
$lang['Cookie_settings_explain'] = '这些设定控制着 Cookie 的定义, 就一般的情况, 使用系统预设值就可以了. 如果您要更改这些设定, 请谨慎设定, 不当的设定将影响用户的登陆';
$lang['Cookie_domain'] = 'Cookie 域名';
$lang['Cookie_name'] = 'Cookie名称';
$lang['Cookie_path'] = 'Cookie 路径';
$lang['Cookie_secure'] = 'Cookie 加密 [ https ]';
$lang['Cookie_secure_explain'] = '假如您的服务器运行于 SSL 方式请设置为开启, 否则请设置为关闭';
$lang['Session_length'] = 'Session 有效期限 [ 秒 ]';

// Visual Confirmation
$lang['Visual_confirm'] = '允许视觉确认';
$lang['Visual_confirm_explain'] = '当用户在注册过程中, 必须输入一段由图像定义的字符串.';

// Autologin Keys - added 2.0.18
$lang['Allow_autologin'] = '允许自动登录论坛';
$lang['Allow_autologin_explain'] = '无论用户在论坛上就可以自动登录';
$lang['Autologin_time'] = '自动设置时间';
$lang['Autologin_time_explain'] = '日以来任期内最后一次访问，在此期间，用户可以自动登录到论坛。输入0或留空禁用此功能。';

// Search Flood Control - added 2.0.20 
$lang['Search_Flood_Interval'] = '搜索间隔时间'; 
$lang['Search_Flood_Interval_explain'] = '用户必须间隔 xx 秒才能再次搜素论坛';

//
// Forum Management
//
$lang['Forum_admin'] = '论坛管理';
$lang['Forum_admin_explain'] = '在这个控制面板里您可以增加, 删除, 编辑和重新排列分类和论坛, 以及设定论坛内的相应资料.';
$lang['Edit_forum'] = '编辑论坛';
$lang['Create_forum'] = '新建论坛';
$lang['Create_category'] = '新建分类';
$lang['Remove'] = '删除';
$lang['Action'] = '执行操作';
$lang['Update_order'] = '更新顺序';
$lang['Config_updated'] = '论坛配置成功';
$lang['Edit'] = '编辑';
$lang['Delete'] = '删除';
$lang['Move_up'] = '上移';
$lang['Move_down'] = '下移';
$lang['Resync'] = '重整对应数据';
$lang['No_mode'] = '没有设定模式';
$lang['Forum_edit_delete_explain'] = '您可以使用下列表格来调整一般的设定选项. 用户及版面设定请使用画面左方 (系统管理) 的相关链接.';

$lang['Move_contents'] = '移动所有内容';
$lang['Forum_delete'] = '删除版面';
$lang['Forum_delete_explain'] = '您可以使用下列表格来删除版面 (或分类), 并可移动包含在版面内的所有内容内容.';

$lang['Status_locked'] = '锁定';
$lang['Status_unlocked'] = '解锁';
$lang['Forum_settings'] = '版面基本设定';
$lang['Forum_name'] = '版面名称';
$lang['Forum_desc'] = '版面描述';
$lang['Forum_status'] = '版面状态';
$lang['Forum_pruning'] = '计划删除';

$lang['prune_freq'] = '定期检查周期';
$lang['prune_days'] = '删除在几天内没有文章回覆的主题';
$lang['Set_prune_data'] = '您已经开启版面计划删文的功能, 但并未完成相关设定. 请回到上一步设定相关的项目';

$lang['Move_and_Delete'] = '移动删除';

$lang['Delete_all_posts'] = '删除所有文章';
$lang['Nowhere_to_move'] = '没有移动的位置';

$lang['Edit_Category'] = '编辑分类名称';
$lang['Edit_Category_explain'] = '使用以下表格修改分类名称';

$lang['Forums_updated'] = '版面及分类资料成功更新';

$lang['Must_delete_forums'] = '在删除这个分类之前, 您必须先删除分类底下的所有版面';

$lang['Click_return_forumadmin'] = '点击 %s这里%s 返回版面管理';


//
// Smiley Management
//
$lang['smiley_title'] = '表情符号编辑';
$lang['smile_desc'] = '在这个选项中, 您可以增加, 删除或是编辑表情符号或表情符号包以便用户在文章发表或是个人消息中使用.';

$lang['smiley_config'] = '表情符号设定';
$lang['smiley_code'] = '表情符号代码';
$lang['smiley_url'] = '表情图片';
$lang['smiley_emot'] = '表情情绪';
$lang['smile_add'] = '增加一个新表情';
$lang['Smile'] = '表情图标';
$lang['Emotion'] = '代表情绪';

$lang['Select_pak'] = '选择的表情符号包 (.pak) 文件';
$lang['replace_existing'] = '替换现有的表情符号';
$lang['keep_existing'] = '保留现有的表情符号';
$lang['smiley_import_inst'] = '您应将表情符号包解压并上传至适当的表情符号目录.  然后选择正确的项目载入表情符号.';
$lang['smiley_import'] = '载入表情符号包';
$lang['choose_smile_pak'] = '选择一个表情符号包 .pak 文件';
$lang['import'] = '载入表情符号';
$lang['smile_conflicts'] = '在冲突的情况下所应做出的选择';
$lang['del_existing_smileys'] = '载入前先删除旧的表情符号';
$lang['import_smile_pack'] = '载入表情符号包';
$lang['export_smile_pack'] = '建立表情符号包';
$lang['export_smiles'] = '如您希望将现有的表情符号制作成表情符号包, 请点击 %s这里%s 下载 smiles.pak 文件, 并确定其后缀为.pak.';

$lang['smiley_add_success'] = '新的表情符号已经成功加入';
$lang['smiley_edit_success'] = '表情符号已经成功更新';
$lang['smiley_import_success'] = '表情符号包已经成功载入!';
$lang['smiley_del_success'] = '表情符号已经成功删除';
$lang['Click_return_smileadmin'] = '点击 %s这里%s 返回表情符号编辑';
$lang['Confirm_delete_smiley'] = '您确定要删除这个表情吗？';


//
// User Management
//
$lang['User_admin'] = '用户管理';
$lang['User_admin_explain'] = '在这个控制面板里, 您可以变更用户的个人资料以及现存的特殊选项. 如果您要修改用户的权限, 请使用用户及团队管理的权限设定功能.';

$lang['Look_up_user'] = '查询用户';

$lang['Admin_user_fail'] = '无法更新用户的个人资料';
$lang['Admin_user_updated'] = '用户的个人资料已经成功更新';
$lang['Click_return_useradmin'] = '点击 %s这里%s 返回用户管理';

$lang['User_delete'] = '删除用户';
$lang['User_delete_explain'] = '点击这里将会删除用户, 这个选择将无法恢复';
$lang['User_deleted'] = '用户被成功删除.';

$lang['User_status'] = '用户帐号已激活';
$lang['User_allowpm'] = '允许使用私人讯息';
$lang['User_allowavatar'] = '允许使用个性头像';

$lang['Admin_avatar_explain'] = '在这个选项您可以浏览或删除用户现存的个性头像';

$lang['User_special'] = '管理员专区';
$lang['User_special_explain'] = '您可以变更用户的帐号激活状态及其它未授权用户的选项设定, 普通用户无法自行改变这些设定';

//
// Group Management
//
$lang['Group_administration'] = '团队管理';
$lang['Group_admin_explain'] = '在这个控制面板里您可以管理所有的用户团队, 您可以建立, 删除以及编辑现存的用户团队. 您可以指定团队管理员, 设定团队模式 (开放/封闭/隐藏) 以及团队的名称和描述.';
$lang['Error_updating_groups'] = '团队更新时发生错误';
$lang['Updated_group'] = '团队已经成功更新';
$lang['Added_new_group'] = '新的团队已经成功加入';
$lang['Deleted_group'] = '团队已被顺利删除';
$lang['New_group'] = '新建团队';
$lang['Edit_group'] = '编辑团队';
$lang['group_name'] = '团队名称';
$lang['group_description'] = '团队描述';
$lang['group_moderator'] = '团队管理员';
$lang['group_status'] = '团队状态';
$lang['group_open'] = '开放团队';
$lang['group_closed'] = '关闭团队';
$lang['group_hidden'] = '隐藏团队';
$lang['group_delete'] = '删除团队';
$lang['group_delete_check'] = '删除团队';
$lang['submit_group_changes'] = '提交更新';
$lang['reset_group_changes'] = '清除重设';
$lang['No_group_name'] = '您必许指定团队名称';
$lang['No_group_moderator'] = '您必许指定团队的管理员';
$lang['No_group_mode'] = '您必须指定团队状态 (开放/封闭/隐藏)';
$lang['No_group_action'] = '没有指定操作';
$lang['delete_group_moderator'] = '删除原有的团队管理员?';
$lang['delete_moderator_explain'] = '如果您变更了团队管理员而且勾选这个选项会将原有的团队管理员从团队中移除, 如不勾选, 这个用户将成为团队的普通成员.';
$lang['Click_return_groupsadmin'] = '点击 %s这里%s 返回团队管理.';
$lang['Select_group'] = '选择团队';
$lang['Look_up_group'] = '查询团队';


//
// Prune Administration
//
$lang['Forum_Prune'] = '版面计划删除';
$lang['Forum_Prune_explain'] = '这将删除所有在限定时间内没有回覆的主题. 如果您没有指定时限 (日数), 所有的主题都将会被删除. 但是无法删除正在进行中的投票主题或是公告. 您必须手动移除这些主题.';
$lang['Do_Prune'] = '执行计划删除';
$lang['All_Forums'] = '所有版面';
$lang['Prune_topics_not_posted'] = '删除在几天内没有文章回覆的主题';
$lang['Topics_pruned'] = '计划删除的主题';
$lang['Posts_pruned'] = '计划删除的文章';
$lang['Prune_success'] = '成功完成版面文章删除';


//
// Word censor
//
$lang['Words_title'] = '字符过滤';
$lang['Words_explain'] = '在这个控制面板里您可以建立, 编辑及删除过滤文字, 这些指定的文字将会被过滤并以替换文字显示. 另外用户也将无法使用含有这些限定文字的名称来注册. 限定的名称允许使用统配符 *, 例如: *test* 代表包括 detestable等, test* 包括 testing等, *test 包括 detest等';
$lang['Word'] = '过滤文字';
$lang['Edit_word_censor'] = '编辑过滤文字';
$lang['Replacement'] = '替换文字';
$lang['Add_new_word'] = '增加过滤文字';
$lang['Update_word'] = '更新过滤文字';

$lang['Must_enter_word'] = '您必须输入要过滤的文字及其替换文字';
$lang['No_word_selected'] = '您没有选择要编辑的过滤文字';

$lang['Word_updated'] = '您所选择的过滤文字已经成功更新';
$lang['Word_added'] = '新的过滤文字已经成功加入';
$lang['Word_removed'] = '您所选择的过滤文字已被成功移除';

$lang['Click_return_wordadmin'] = '点击 %s这里%s 返回文字过滤';
$lang['Confirm_delete_word'] = '您确定要删除该文字过滤吗?';


//
// Mass Email
//
$lang['Mass_email_explain'] = '在这个选项里您可以发送电子邮件讯息给所有的用户或是特定的团队的成员. 这封电子邮件将被寄送至系统管理员提供的电子邮件信箱, 并以密件副本的方式寄送给所有收件人. 如果收件人数过多, 系统需要较长的时间来执行, 请在提交送出后耐心等候, <b>切勿</b>在程序完成之前停止网页动作.当发送完成时将显示提示.';
$lang['Compose'] = '写邮件'; 

$lang['Recipients'] = '收件人'; 
$lang['All_users'] = '所有用户';

$lang['Email_successfull'] = '讯息已经寄出';
$lang['Click_return_massemail'] = '点击 %s这里%s 返回电子邮件通知';


//
// Ranks admin
//
$lang['Ranks_title'] = '等级管理';
$lang['Ranks_explain'] = '在这个控制面板里, 您可以在增加, 编辑, 浏览以及删除等级. 您也可以使用等级应用于用户管理功能.';

$lang['Add_new_rank'] = '新建等级';

$lang['Rank_title'] = '等级名称';
$lang['Rank_special'] = '特殊等级';
$lang['Rank_minimum'] = '最小发贴数量';
$lang['Rank_maximum'] = '最大发贴数量';
$lang['Rank_image'] = '等级图片 (请使用 phpBB2 绝对路径)';
$lang['Rank_image_explain'] = '使用这个来定义等级图片的路径';

$lang['Must_select_rank'] = '您必须选择一个等级';
$lang['No_assigned_rank'] = '没有指定的等级';

$lang['Rank_updated'] = '等级设置已经成功更新';
$lang['Rank_added'] = '新的等级已经成功加入';
$lang['Rank_removed'] = '等级名称已被成功删除';
$lang['No_update_ranks'] = '等级名称已经被成功删除, 尽管如引, 使用该等级的用户帐号没有获得更新.  您需要手动复置那些使用过该等级的用户帐号';

$lang['Click_return_rankadmin'] = '点击 %s这里%s 返回等级管理';
$lang['Confirm_delete_rank'] = '您确定要删除该等级吗？';


//
// Disallow Username Admin
//
$lang['Disallow_control'] = '禁用帐号控制';
$lang['Disallow_explain'] = '在这个选项中, 您可以控制禁用的用户帐号名称 (可使用通配符 *). 请注意, 您无法禁用已经注册使用的用户名称, 您必须先删除这个用户帐号, 才能使用禁用帐号的功能.';

$lang['Delete_disallow'] = '删除';
$lang['Delete_disallow_title'] = '删除禁用帐的号名称';
$lang['Delete_disallow_explain'] = '您可以从列表中选择要移除的禁用帐号名称';

$lang['Add_disallow'] = '增加';
$lang['Add_disallow_title'] = '增加禁用的帐号名称';
$lang['Add_disallow_explain'] = '您可以使用通配符 * 来禁用范围较大的用户名称';

$lang['No_disallowed'] = '没有禁用的帐号名称';

$lang['Disallowed_deleted'] = '您所选择的禁用帐号名称已成功被移除';
$lang['Disallow_successful'] = '新的禁用帐号名称已经成功加入';
$lang['Disallowed_already'] = '无法禁用您所输入的帐号名称. 该帐号名称可能已在禁用列表内或已被注册使用';

$lang['Click_return_disallowadmin'] = '点击 %s这里%s 返回禁用帐号控制';


//
// Styles Admin
//
$lang['Styles_admin'] = '版面风格管理';
$lang['Styles_explain'] = '使用这个功能您可以增加, 移除及管理各种不同的版面风格 (范本和主题) 提供用户选择使用.';
$lang['Styles_addnew_explain'] = '以下列表包含所有可使用的主题. 这份列表上的主题均尚未安装到 phpBB 2 的数据库内. 要安装新的主题请直接按下右方的执行链接.';

$lang['Select_template'] = '选择范本名称';

$lang['Style'] = '风格';
$lang['Template'] = '范本';
$lang['Install'] = '安装';
$lang['Download'] = '下载';

$lang['Edit_theme'] = '编辑主题';
$lang['Edit_theme_explain'] = '您可以使用下列表格编辑主题设定.';

$lang['Create_theme'] = '增加主题';
$lang['Create_theme_explain'] = '您可以使用下列表格来为指定的范本增加新的主题. 当设定颜色时 (您必须使用十六进位码, 例如: FFFFFF) 不包含起始字元 #, 举例如下: CCCCCC 为正确的表示法, #CCCCCC 则是错误的.';

$lang['Export_themes'] = '输出主题';
$lang['Export_explain'] = '在这个版面里, 您可以输出指定范本的主题资料. 由列表中选择指定的范本后, 系统将会建立主题的配置数据文件并储存到指定的范本目录. 如果资料无法储存, 您可以下载这个资料文件. 如果您希望系统能直接储存这些文件数据, 您必须确定指定范本目录可写. 如果您需要更多这方面的资料, 请参考 phpBB 2 使用说明.';

$lang['Theme_installed'] = '指定的主题已经安装完成';
$lang['Style_removed'] = '指定的版面风格已从数据库中移除. 要从您的系统中完全的移除这个版面风格, 您必须从 templates 中移除对应的范本目录';
$lang['Theme_info_saved'] = '指定的主题资料已经成功储存. 您必须立即修改 theme_info.cfg 成唯读属性 (如果适用于指定的范本目录)';
$lang['Theme_updated'] = '指定的主题已被更新. 您必须输出新的主题设定值';
$lang['Theme_created'] = '主题已被建立. 您必须输出主题设定文件, 以维持正常的操作及资料安全';

$lang['Confirm_delete_style'] = '您确定要删除这个版面风格吗?';

$lang['Download_theme_cfg'] = '系统无法写入主题的设定文件. 您可以点击以下的按钮下载这个文件. 当您下载完这个文件后, 您即可将文件移到包含此范本的目录之下. 重新包装这个文件用以发行或是在其它地方使用.';
$lang['No_themes'] = '您指定的范本并没有包含任何的主题. 要建立新的主题, 请按下左方控制面板的建立链接';
$lang['No_template_dir'] = '无法打开范本目录. 这有可能是因为此目录设定为不可读取的属性或是文件根本不存在';
$lang['Cannot_remove_style'] = '您无法移除预设的版面风格. 请先变更版面的预设风格后再重试一次';
$lang['Style_exists'] = '指定的版面风格名称已经存在, 请回到上一步并选择一个不同的名称';

$lang['Click_return_styleadmin'] = '点击 %s这里%s 返回版面风格管理';

$lang['Theme_settings'] = '主题设定';
$lang['Theme_element'] = '主题元件';
$lang['Simple_name'] = '简易名称';
$lang['Value'] = '数值';
$lang['Save_Settings'] = '储存设定';

$lang['Stylesheet'] = 'CSS 风格表';
$lang['Background_image'] = '背景图案';
$lang['Background_color'] = '背景颜色';
$lang['Theme_name'] = '主题名称';
$lang['Link_color'] = '正常的链接颜色';
$lang['Text_color'] = '文字颜色';
$lang['VLink_color'] = '参观过的链接颜色 (visited)';
$lang['ALink_color'] = '鼠标按下的链接颜色 (active)';
$lang['HLink_color'] = '鼠标移过的链接颜色 (hover)';
$lang['Tr_color1'] = '表格列颜色 1';
$lang['Tr_color2'] = '表格列颜色 2';
$lang['Tr_color3'] = '表格列颜色 3';
$lang['Tr_class1'] = '表格列属性 1';
$lang['Tr_class2'] = '表格列属性 2';
$lang['Tr_class3'] = '表格列属性 3';
$lang['Th_color1'] = '项目标题颜色 1';
$lang['Th_color2'] = '项目标题颜色 2';
$lang['Th_color3'] = '项目标题颜色 3';
$lang['Th_class1'] = '项目标题属性 1';
$lang['Th_class2'] = '项目标题属性 2';
$lang['Th_class3'] = '项目标题属性 3';
$lang['Td_color1'] = '资料格颜色 1';
$lang['Td_color2'] = '资料格颜色 2';
$lang['Td_color3'] = '资料格颜色 3';
$lang['Td_class1'] = '资料格属性 1';
$lang['Td_class2'] = '资料格属性 2';
$lang['Td_class3'] = '资料格属性 3';
$lang['fontface1'] = '字体类型 1';
$lang['fontface2'] = '字体类型 2';
$lang['fontface3'] = '字体类型 3';
$lang['fontsize1'] = '字体大小 1';
$lang['fontsize2'] = '字体大小 2';
$lang['fontsize3'] = '字体大小 3';
$lang['fontcolor1'] = '字体颜色 1';
$lang['fontcolor2'] = '字体颜色 2';
$lang['fontcolor3'] = '字体颜色 3';
$lang['span_class1'] = 'Span 属性 1';
$lang['span_class2'] = 'Span 属性 2';
$lang['span_class3'] = 'Span 属性 3';
$lang['img_poll_size'] = '投票统计量图示大小 [px]';
$lang['img_pm_size'] = '个人消息使用量图示大小 [px]';


//
// Install Process
//
$lang['Welcome_install'] = '欢迎安装 phpBB 2 论坛系统';
$lang['Initial_config'] = '基本设定';
$lang['DB_config'] = '数据库设定';
$lang['Admin_config'] = '论坛管理员配置';
$lang['continue_upgrade'] = '在您下载完系统设定文件 (config.php) 之后, 您可以按下 继续升级 的按钮继续下一步. 请在所有升级程序完成后再上传设定档.';
$lang['upgrade_submit'] = '继续升级';

$lang['Installer_Error'] = '安装过程发生错误';
$lang['Previous_Install'] = '先前已经安装过';
$lang['Install_db_error'] = '尝试升级数据库里发生一个错误';

$lang['Re_install'] = '您先前安装的 phpBB 2 论坛系统正在运行中. <br /><br />如果您希望重新安装 phpBB 2 论坛系统请选择 是 的按钮.  请注意, 执行后将会移除所有的现存资料, 而且不会有任何备份! 系统管理员帐号及密码将被重新建立, 所有设定也将不会被保留. <br /><br />请在您按下 是 的按钮前谨慎考虑!';

$lang['Inst_Step_0'] = '感谢您选择 phpBB 2 论坛系统. 您必须填写下列资料以完成安装程序. 在安装前, 请先确定您所要使用的数据库已经建立.';

$lang['Start_Install'] = '开始安装';
$lang['Finish_Install'] = '完成安装';

$lang['Default_lang'] = '预设论坛语言';
$lang['DB_Host'] = '数据库服务器主机名称 / DSN';
$lang['DB_Name'] = '您的数据库名称';
$lang['DB_Username'] = '数据库用户名称';
$lang['DB_Password'] = '数据库用户密码';
$lang['Database'] = '您的数据库';
$lang['Install_lang'] = '选择安装语言';
$lang['dbms'] = '数据库类型';
$lang['Table_Prefix'] = '数据表前缀';
$lang['Admin_Username'] = '管理员用户名称';
$lang['Admin_Password'] = '管理员用户密码';
$lang['Admin_Password_confirm'] = '重输管理员密码';

$lang['Inst_Step_2'] = '您的论坛管理员用户已经建立.  相关基本安装过程已经完成. 您将可以使用管理员用户名称对新安装的论坛进行管理. 请检查整体设置资料并依据您的意愿进行任何修改. 谢谢您选择 phpBB 2.';

$lang['Unwriteable_config'] = '您的系统设定档无法写入, 您可以点击下方按钮下载设定文件, 再将这个文件上传至 phpBB 2 论坛的资料夹. 在完成后您必须使用管理员帐号跟密码登陆并进入系统管理控制面板 (在您登陆后, 下方将出现一个进入\'系统管理控制面板\'的链接) 检查您的基本配置设定. 最后感谢您选择使用安装 phpBB 2 论坛系统.';
$lang['Download_config'] = '下载配置';

$lang['ftp_choose'] = '选择下载方式';
$lang['ftp_option'] = '<br />在 FTP 设定完成后, 您可以使用自动上传的功能.';
$lang['ftp_instructs'] = '您已经选择使用 FTP 去自动安装您的 phpBB 2 论坛.  请输入下列资料来简化这个过程. 请注意: FTP 路径须跟您安装 phpBB 2 的 FTP 路径完全相同.';
$lang['ftp_info'] = '输入您的 FTP 信息';
$lang['Attempt_ftp'] = '使用 FTP 上传设定文件:';
$lang['Send_file'] = '自行上传设定文件';
$lang['ftp_path'] = '安装 phpBB 2 的 FTP 路径:';
$lang['ftp_username'] = '您的 FTP 用户名称';
$lang['ftp_password'] = '您的 FTP 用户密码';
$lang['Transfer_config'] = '开始传送';
$lang['NoFTP_config'] = 'FTP 上传设定文件失败. 请下载设定文件并使用手动上传.';

$lang['Install'] = '全新安装';
$lang['Upgrade'] = '升级版本';


$lang['Install_Method'] = '请选择安装模式';

$lang['Install_No_Ext'] = '您服务器上的php配置不支持您所选择的数据库类型';

$lang['Install_No_PCRE'] = '您的php配置不支持安装phpBB2所需要的Perl语言标准表达模式的兼容性';

//
// Version Check
//
$lang['Version_up_to_date']= '您的WAP版本的phpBB是最新的，还不需要更新！';
$lang['Version_not_up_to_date']= '您目前的 phpBB-WAP 版本 <b>是</b> 最新的！. 更新, 请输入地址 <a href="http://www.phpbb.com/downloads.php" target="_new">http://www.phpbb.com/downloads.php</a> 为最新的！.';
$lang['Latest_version_info']= '新版本：<b>phpBB-WAP %s</b>.';
$lang['Current_version_info']= '您目前的版本 <b>phpBB-WAP %s</b>.';
$lang['Connect_socket_error']= '无法连接到 phpBB-WAP 服务器, 收到一条错误消息:<br />%s';
$lang['Socket_functions_disabled']= '您不能使用接口.';
$lang['Mailing_list_subscribe_reminder']= '要获得最新的 中文phpBB-WAP 升级信息，请点击 <a href="http://phpbb-wap.com/" target="_new">phpbb-wap.com</a>.';
$lang['Version_information']= '版本信息 phpBB-WAP';

// 
// Login attempts configuration 
// 
$lang['Max_login_attempts'] = '允许错误登录'; 
$lang['Max_login_attempts_explain'] = '当用户输入密码错误超过 x 次'; 
$lang['Login_reset_time'] = '新注册用户限制'; 
$lang['Login_reset_time_explain'] = '新注册的用户需要等待多长时间才可以进入论坛！';

//
// That's all Folks!
// -------------------------------------------------

?>