<?php
/***************************************************************************
 * lang_admin_attach.php [Russian]
 * -------------------
 * begin : Thu Jan 15 2004
 * copyright : (C) 2004 hobo http://www.hacksphpbb.ru
 * email : portal@hacksphpbb.ru
 *
 * $Id: lang_admin_attach.php,v 1.36 2003/08/30 15:47:39 hobo Exp $
 *
 ****************************************************************************/

/***************************************************************************
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 ***************************************************************************/

//
// Attachment Mod Admin Language Variables
//

// Modules, this replaces the keys used
$lang['Control_Panel'] = '附件统计';
$lang['Shadow_attachments'] = '幽灵文件';
$lang['Forbidden_extensions'] = '禁止扩展名';
$lang['Extension_control'] = '扩展名列表';
$lang['Extension_group_manage'] = '扩展名组';
$lang['Special_categories'] = '特殊类别';
$lang['Sync_attachments'] = '同步附件';
$lang['Quota_limits'] = '限制设定';

// Attachments -> 管理
$lang['Attach_settings'] = '附件设定';
$lang['Manage_attachments_explain'] = '在这里你可以设定附件功能的主要设定。如果你点击测试设定按钮，附件功能可以做些系统测试来确定那功能会正确地运作。如果你有上传文件的问题，请执行这个测试，来变成详细的错误讯息。';
$lang['Attach_filesize_settings'] = '附件大小限制';
$lang['Attach_number_settings'] = '附件数量设定';
$lang['Attach_options_settings'] = '附件选项设定';

$lang['Upload_directory'] = '附件的上传或下载目录';
$lang['Upload_directory_explain'] = '如果你不知道是什么请勿更改，否则附件功能不可用！';
$lang['Attach_img_path'] = '帖子附件前的图标';
$lang['Attach_img_path_explain'] = '在附件链接前面的图标，如果留空则不显示。注意：这个设定会覆盖附件组的设定！';
$lang['Attach_topic_icon'] = '帖子标题前的图标';
$lang['Attach_topic_icon_explain'] = '如果帖子中存在附件，那么帖子标题前就会显示一个图标，如果留空则不显示。';
$lang['Attach_display_order'] = '附件显示顺序';
$lang['Attach_display_order_explain'] = '选择 2.32 版以後的新版附件发表控制台(选择:是)或者是选 2.24 以前旧版的附件发表控制台(选择:否)。这个有点难以解释，因此您还是自己去试一试罗!';
$lang['Show_apcp'] = '显示新版附件发表控制台';
$lang['Show_apcp_explain'] = '选择 2.32 版以後的新版附件发表控制台(选择:是)或者是选 2.24 以前旧版的附件发表控制台(选择:否)。这个有点难以解释，因此您还是自己去试一试罗!';

$lang['Max_filesize_attach'] = '单个上传文件的最大限制';
$lang['Max_filesize_attach_explain'] = '附件的最大的文件大小(字节)。数值 0 代表 “没有限制”。这个设定是受限制的被你的服务器组态。如果你的 php 组态只允许最大上传的大小 2 MB，这个无法由这功能覆写。';
$lang['Attach_quota'] = '全部附件的最大限制';
$lang['Attach_quota_explain'] = '全部的附件限制，0 表示 “无限制”';
$lang['Max_filesize_pm'] = '信息中单个上传附件的最大限制';
$lang['Max_filesize_pm_explain'] = '会员最大可以上传多大的附件到信息中的附件框'; 
$lang['Default_quota_limit'] = '默认限制设定';
$lang['Default_quota_limit_explain'] = '这里你可以选择一个默认限制给新注册的用户和没有指定限客的用户。选项 “无限” 表示没有限制设定，而使用上面的附件大小限制设定。';

$lang['Max_attachments'] = '帖子附件数限制';
$lang['Max_attachments_explain'] = '在一个楼层帖子中最多可以上传多少个附件？';
$lang['Max_attachments_pm'] = '信息附件数限制';
$lang['Max_attachments_pm_explain'] = '在一个信息中最多可以上传多少个附件？';

$lang['Disable_mod'] = '不使用附件功能';
$lang['Disable_mod_explain'] = '选择关闭则关闭全站的附件上传功能！';
$lang['PM_Attachments'] = '信息中是否可以上传附件';
$lang['PM_Attachments_explain'] = '用户之间发送信息的附件';
$lang['Ftp_upload'] = '是否开启 FTP 上传';
$lang['Ftp_upload_explain'] = '开启 FTP 上传功能！';
$lang['Attachment_topic_review'] = '你想要显示附件在查看主题视窗吗?';
$lang['Attachment_topic_review_explain'] = '如果你选择「是」，当你回覆主题时全部已附加的文件将被在查看主题中显示。';

$lang['Ftp_server'] = '服务器名称';
$lang['Ftp_server_explain'] = '无需ftp://';

$lang['Attach_ftp_path'] = '目录';
$lang['Attach_ftp_path_explain'] = 'FTP 的路径，例如: /home/web/uploads';
$lang['Ftp_download_path'] = '下载目录';
$lang['Ftp_download_path_explain'] = '输入路径到你的 FTP 路径，你的附件储存的地方。请输入完整的路径，例如 http://www.mystorage.com/phpBB2/upload。多馀的斜线「/」将被移除。<br />保持这个栏位空白的，如果你有路径外面的你的网站根目录。但是与这个栏位空白的你无法使用实体的下载方式。';
$lang['Ftp_passive_mode'] = '是否开启 FTP 被动模式';
$lang['Ftp_passive_mode_explain'] = '申请PASV命令打开一个远程服务器的端口进行数据传输，并返回它的地址。远程服务器在该端口上侦听，客户端连接到它。';

$lang['No_ftp_extensions_installed'] = '你不能使用FTP上传功能，因为你安装的PHP不支持FTP功能。';

// Attachments -> Shadow Attachments
$lang['Shadow_attachments_explain'] = '在这里你可以删除附件资料从发表文章中当文件从你的文件系统遗失的时候，并且删除那些较久没有附加到任何发表文章的文件。如果你点击它，并且你可以下载或查看文件; 如果目前是没有连结的，文件就不会存在。';
$lang['Shadow_attachments_file_explain'] = '删除全部的附件那些在你的文件系统而且是没有指定到存在的发表文章。';
$lang['Shadow_attachments_row_explain'] = '删除全部发表文章里的附件资料对于文件那些不存在你的文件系统。';
$lang['Empty_file_entry'] = '空文件';

// Attachments -> 同步
$lang['Syncs'] = '缓存同步';
$lang['Sync_thumbnail_resetted'] = '缩略图已重设给附件: %s';
$lang['Attach_sync_finished'] = '附件缓存同步成功';
$lang['Sync_topics'] = '主题缓存同步成功';//预留功能
$lang['Sync_posts'] = '回复缓存同步成功';//预留功能
$lang['Sync_thumbnails'] = '缩略图缓存同步成功';//预留功能

// Extensions -> 扩展名
$lang['Manage_extensions'] = '管理扩展名';
$lang['Manage_extensions_explain'] = '在这里你可以管理你的文件扩展名。如果你想允许或不允许可上档的扩展名，请使用扩展名群组管理。';
$lang['Explanation'] = '解释';
$lang['Extension_group'] = '扩展名组';
$lang['Invalid_extension'] = '无效扩展名';
$lang['Extension_exist'] = '扩展名 %s 已经存在'; // replace %s with the Extension
$lang['Unable_add_forbidden_extension'] = '扩展名 %s 是禁止上传的，你没有将它加入已允许的扩展名群组里。';

// Extensions -> 扩展名群组管理
$lang['Manage_extension_groups'] = '管理扩展名群组';
$lang['Manage_extension_groups_explain'] = '在这里你可以加入，删除和修改你的扩展名群组，你可以停用扩展名群组，指定特殊类别给它们，变更下载办法而且你可以定义上传图示当做被显示在附件适用到群组的最前面时候。';
$lang['Special_category'] = '特殊类';
$lang['Category_images'] = '图片文件';
$lang['Category_stream_files'] = '流媒体文件';
$lang['Category_swf_files'] = 'Flash 文件';
$lang['Allowed'] = '允许的';
$lang['Allowed_forums'] = '已允许的论坛';
$lang['Ext_group_permissions'] = '群组权限';
$lang['Download_mode'] = '下载模式';
$lang['Upload_icon'] = '上传后显示图标路径';
$lang['Max_groups_filesize'] = '最大的文件大小';
$lang['Extension_group_exist'] = '扩展名群组 %s 已经存在'; // %s 代表扩展名小组名称
$lang['Collapse'] = '+';
$lang['Decollapse'] = '-';

// Extensions -> 特殊类别
$lang['Manage_categories'] = '管理特殊类别';
$lang['Manage_categories_explain'] = '在这里你可以组态特殊类别。你可以设定特殊叁数和条件给特殊类别指定到扩展名群组。';
$lang['Settings_cat_images'] = 'MIME，例如: image/jpeg';
$lang['Settings_cat_streams'] = '特殊类设定: 流媒体文件';
$lang['Settings_cat_flash'] = '特殊类设定: Flash 文件';
$lang['Display_inlined'] = '是否直接显示图像附件';
$lang['Display_inlined_explain'] = '选择是否显示图像的消息（上）直接或显示为纽带？';
$lang['Max_image_size'] = '图像像素的最大限制（宽x高）';
$lang['Max_image_size_explain'] = '在这里，您可以指定最大尺寸的图像可以嵌入（宽x高以像素为单位）。<br />如果设置为0x0，该选项被禁用。在某些类型的图像，这个选项不起作用由于局限性PHP。';
$lang['Image_link_size'] = '直接显示的图像像素的最大限制（宽x高）';
$lang['Image_link_size_explain'] = '如果在线显示查看是启用的，当图片尺寸超过这里定义的尺寸，图片将被按照原始比例自动缩小(宽度 x 高度,单位:像素)，如果它是设定成 0x0 则不会限制图片的大小。对于限制PHP中的某些图片这个功能将不起作用。';
$lang['Assigned_group'] = '指定的小组';

$lang['Image_create_thumbnail'] = '是否创建缩略图';
$lang['Image_create_thumbnail_explain'] = '永远使用建立缩略图。这个功能几乎推翻在这个特殊类别全部的设定，除了最大图片尺寸之外。使用这个功能将使缩略图被显示于发表的文章中，使用者可以点击缩略图来开启图片。<br />请注意这功能需要安装 Imagick，如果没有安装 Imagick 而且假如已启用安全模式，则 PHP 的 GD-Extention 将被使用。如果图片类型是 PHP 不支援的，这个功能将无法使用。';
$lang['Image_min_thumb_filesize'] = '缩略图的最小限制';
$lang['Image_min_thumb_filesize_explain'] = '如果图像小于指定的大小，微型不会被创建，因为它足够小。';
$lang['Image_imagick_path'] = 'Imagick 缩略图程式 (完整路径)';
$lang['Image_imagick_path_explain'] = '输入 Imagick 的缩略图转换程式的路径，一般是 /usr/bin/convert (在 windows 是: c:/imagemagick/convert.exe)。';
$lang['Image_search_imagick'] = '搜寻 Imagick';

$lang['Use_gd2'] = '是否使用GD2扩展';
$lang['Use_gd2_explain'] = 'PHP将可以使用GD1或GD2扩展功能对图片进行操作. 要正确生成缩略图，并不合使用imagemagick,附件Mod提供了两种不同的方法，可以由您来进行选择. 如果缩图质量差或图片过大，您可以尝试改变设置.';
$lang['Attachment_version'] = '附件Mod版本号 %s'; // %s 代表版本号

// Extensions -> 禁止扩展
$lang['Manage_forbidden_extensions'] = '管理禁止扩展名';
$lang['Manage_forbidden_extensions_explain'] = '在这里你可以加入或删除禁止的扩展名。这扩展名 php, php3 和 php4 是内定预设禁止的基于安全理由，你不可以删除它们。';
$lang['Forbidden_extension_exist'] = '禁止的扩展名 %s 已经存在'; // replace %s with the extension
$lang['Extension_exist_forbidden'] = '扩展名 %s 是已定义在你已允许的扩展名，在你在这里加入它之前请先删除。';

// Extensions -> 扩展名群组权限 -> 群组权限
$lang['Group_permissions_title'] = '扩展名群组权限 -> \'%s\'';
$lang['Group_permissions_explain'] = '在这里你能够限制已选择的扩展名群组给确定的论坛(定义在允许的论坛对话盒)。预设为允许扩展名群组给全部论坛的使用者是可以附件到(一般方式附件功能是给初学者使用)。只要加入那些论坛到你想使用的扩展名群组(扩展名必须在这个群组使用)并在那里准许使用，预设为全部论坛将不会出现当你加入论坛到清单中。你可以在任何时候重新加入全部论坛。如果你加入论坛到你的讨论区和权限是设定到全部论坛是不会有任何改变。但是如果你有变更和限制存取到某些论坛，你必须回到这里检查来加入你新建立的论坛。它是容易执行这个是自动地，但是这个将强制你去编辑一串的文件，因此我会选择这方法它是目前的情形。请谨记在心，那些全部你的论坛将会在此列出。';
$lang['Note_admin_empty_group_permissions'] = '注意:<br />使用在下面列表的论坛，使用者通常是被允许添加附件，但是自从没有扩展名群组在那里被允许去附加的，你的使用者是无法附加任何文件的。如果他们曾经尝试附加文件，他们将会接收到错误讯息。可能你想要设定权限\'可附加的文件\' 来管理在这个论坛的附件。<br /><br />';
$lang['Add_forums'] = '加入论坛';
$lang['Add_selected'] = '加入已选择的';
$lang['Perm_all_forums'] = '全部论坛';

// Attachments -> 附件限制
$lang['Manage_quotas'] = '管理附件限制';
$lang['Manage_quotas_explain'] = '这里你可以增加/删除/修改限制。你可以把这些限制分配给会员和团队。要把一个限制分配给一个会员，你必须通过会员管理->管理选项，选择用户后你会在下面看到这个选项。要把一个限制分配给一个团队，你必须通过团队管理->管理选项，选择团队后你会看到配置项。如果你想查看一个限制被分配给了哪些用户和团队，点击限制描述左边的\'浏览\'。';
$lang['Assigned_users'] = '被分配的用户';
$lang['Assigned_groups'] = '被分配的团队';
$lang['Quota_limit_exist'] = '限制 %s 已经存在。'; // %s 代表限制的描述

// Attachments -> 附加控制面板
$lang['Control_panel_title'] = '文件附加控制台';
$lang['Control_panel_explain'] = '在这里你可以查看和管理全部的附件基本设定在使用者，附件，查看其它...';
$lang['File_comment_cp'] = '文件注解';

// Control Panel -> 搜索
$lang['Search_wildcard_explain'] = '使用 * 当做通配符做为符合的部分';
$lang['Size_smaller_than'] = '附件大小小于(字节)';
$lang['Size_greater_than'] = '附件大小大于(字节)';
$lang['Count_smaller_than'] = '下载次数是小于';
$lang['Count_greater_than'] = '下载次数是大于';
$lang['More_days_old'] = '比这些天数更旧的';
$lang['No_attach_search_match'] = '没有附件符合你的搜索条件';

// Control Panel -> 统计
$lang['Number_of_attachments'] = '附件的数量';
$lang['Total_filesize'] = '总计文件大小';
$lang['Number_posts_attach'] = '发表文章和附件的数量';
$lang['Number_topics_attach'] = '主题与附件的数量';
$lang['Number_users_attach'] = '个别的使用者已发表的附件';
$lang['Number_pms_attach'] = '总计附件的数量在站内短信';

// Control Panel -> 附件
$lang['Statistics_for_user'] = '%s 的附件统计'; // replace %s with username
$lang['Size_in_kb'] = '大小 (KB)';
$lang['Downloads'] = '下载次数';
$lang['Post_time'] = '发表时间';
$lang['Posted_in_topic'] = '所属帖子';
$lang['Submit_changes'] = '更改';

// 排序方式
$lang['Sort_Attachments'] = '附件';
$lang['Sort_Size'] = '文件大小';
$lang['Sort_Filename'] = '文件名称';
$lang['Sort_Comment'] = '文件注解';
$lang['Sort_Extension'] = '扩展名';
$lang['Sort_Downloads'] = '下载次数';
$lang['Sort_Posttime'] = '发表时间';
$lang['Sort_Posts'] = '发表';

// 浏览方式
$lang['View_Statistic'] = '附件统计';
$lang['View_Search'] = '搜索附件';
$lang['View_Username'] = '用户附件';
$lang['View_Attachments'] = '所有附件';

// 完成。。。。
$lang['Attach_config_updated'] = '附件设定更新完毕'; 
$lang['Click_return_attach_config'] = '请点 %s这里%s 回到附件设定';
$lang['Test_settings_successful'] = '设定测试完成，设定看起来是好的。';

// 一些基本的定义
$lang['Attachments'] = '附件相关管理';
$lang['Attachment'] = '附件';
$lang['Extensions'] = '扩展名相关管理';
$lang['Extension'] = '扩展名';

// 附件的权限
$lang['Auth_attach'] ='上传附件'; 
$lang['Auth_download'] ='下载权限'; 

?>