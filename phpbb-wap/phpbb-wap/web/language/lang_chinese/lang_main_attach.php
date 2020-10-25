<?php
/***************************************************************************
 * lang_main_attach.php [Russian]
 * -------------------
 * begin : Thu Jan 15 2004
 * copyright : (C) 2004 hobo http://www.hacksphpbb.ru
 * email : portal@hacksphpbb.ru
 *
 * $Id: lang_main_attach.php,v 1.27 2003/01/16 11:11:56 hobo Exp $
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
// Attachment Mod Main Language Variables
//

// 验证相关文章
$lang['Rules_attach_can'] = '您 <b>可以</b> 添加附件'; 
$lang['Rules_attach_cannot'] = '您 <b>不可以</b> 添加附件'; 
$lang['Rules_download_can'] = '您 <b>可以</b> 下载附件'; 
$lang['Rules_download_cannot'] = '您 <b>不可以</b> 下载附件'; 
$lang['Sorry_auth_view_attach'] = '对不起，您没有被授权查看或下载这个添加附件'; 

// Viewtopic 页面 -> 附件显示
$lang['Description'] = '描述'; 
$lang['Downloaded'] = '下载过的'; 
$lang['Download'] = '下载'; 
$lang['Filesize'] = '文件大小'; 
$lang['Viewed'] = '看过的'; 
$lang['Download_number'] = '文件被下载或查看 %d 次'; 
$lang['Extension_disabled_after_posting'] = '文件扩展名 “%s” 已被管理员禁用，因此这个附件是不被显示的';

// Posting/PM 页面 -> 初始显示
$lang['Attach_posting_cp'] = '附件发表控制面板'; 
$lang['Attach_posting_cp_explain'] = '如果您点击「新增附件」，您将可以看见附件的对话框。<br />上传新的版本，如果您不想要下载次数被归零而更新原先旧版的文件。<br />如何上传新版本文件，先点击「发表的附件清单」，再点击「新增附件」，再点[新增附件]中的「浏览」，<br />选择新版本的文件之后，再点击[发表的附件清单]中的「上传新的版本」。'; 

// Posting/PM -> 添加附件
$lang['Add_attachment'] = '新增附件';
$lang['Add_attachment_title'] = '新增附件';
$lang['Add_attachment_explain'] = '如果您不想新增附件到您的文章中，请留空即可';
$lang['File_name'] = '文件名';
$lang['File_comment'] = '文件注解';

// Posting/PM -> 已发表的附件
$lang['Posted_attachments'] = '发表的附件清单';
$lang['Options'] = '选项';
$lang['Update_comment'] = '更新注解';
$lang['Delete_attachments'] = '删除附件';
$lang['Delete_attachment'] = '删除附件';
$lang['Delete_thumbnail'] = '删除缩略图';
$lang['Upload_new_version'] = '上传新的版本';

// Errors -> 正在发表的文件
$lang['Invalid_filename'] = '%s 是一个无效的文件名'; // replace %s with given filename
$lang['Attachment_php_size_na'] = '附件太大了。<br />无法取得在 PHP 定义的大小限制。<br />系统无法确定定义在 php.ini 中的最大上传大小。';
$lang['Attachment_php_size_overrun'] = '附加文件太大了。<br />最大上传大小: %d MB。<br />请注意那个大小是定义在 php.ini，这个工具是由 PHP 所设定而且系统无法改变这个数值。'; // replace %d with ini_get('upload_max_filesize')
$lang['Disallowed_extension'] = '扩展名 %s 是不被允许的'; // replace %s with extension (e.g. .php) 
$lang['Disallowed_extension_within_forum'] = '您未被允许在此论坛添加扩展名为 %s的附件';
$lang['Attachment_too_big'] = '附件太大了。<br />最大的大小: %d %s'; // replace %d with maximum file size, %s with size var
$lang['Attach_quota_reached'] = '对不起，已达到全部附件最大的文件大小限制。如果您有问题请联系系统管理员。';
$lang['Too_many_attachments'] = '附件无法被新增，由于最大限制。%d 个的附加文件在这个发表已完成。'; // replace %d with maximum number of attachments
$lang['Error_imagesize'] = '附件/图片必须小于宽度 %d 像素和高度 %d 像素'; 
$lang['General_upload_error'] = '上传错误: 无法上传附件到 %s 。'; // replace %s with local path

$lang['Error_empty_add_attachbox'] = '您必须先在「新增附件」对话盒里点击「浏览」然后在您要更新的项目点击「上传新的版本」。';
$lang['Error_missing_old_entry'] = '无法更新附件，无法找到旧的附件项目。';

// Errors -> 信息相关
$lang['Attach_quota_sender_pm_reached'] = '对不起，但是在您的站内短信收件夹已达到全部附加文件最大文件的大小限制。请删除一些在您的收件夹/寄收匣的附加文件。';
$lang['Attach_quota_receiver_pm_reached'] = '对不起，但是在站内短信收件夹的 \'%s\' 已达到全部附加文件的最大文件的大小限制。请让他们知道，或等待直到他/她删除一些在他的/她的附加文件。';

// Errors -> 下载
$lang['No_attachment_selected'] = '您没有选择一个附件来下载或查看。';
$lang['Error_no_attachment'] = '选择的附件不存在。';

// 删除附件
$lang['Confirm_delete_attachments'] = '您确定您想要删除选择的附件?';
$lang['Deleted_attachments'] = '选择的附件已被删除。';
$lang['Error_deleted_attachments'] = '无法删除附件。';
$lang['Confirm_delete_pm_attachments'] = '您确定您想要删除已发表在这个站内短信中全部的附件吗?';

// 一般错误信息
$lang['Attachment_feature_disabled'] = '附件功能已被停用。';

$lang['Directory_does_not_exist'] = '文件夹 \'%s\' 不存在或找不到。'; // replace %s with directory
$lang['Directory_is_not_a_dir'] = '如果 \'%s\' 是一个文件夹请查核。'; // replace %s with directory
$lang['Directory_not_writeable'] = '文件夹 \'%s\' 是不可写入的。您将需要建立上传路径并变更属性为 777 (或变更拥有者为您 httpd-服务器的拥有者) 要上传文件。<br />如果您只要完全的 ftp-存取 变更文件夹的 \'属性\' 为 rwxrwxrwx。'; // replace %s with directory

$lang['Ftp_error_connect'] = '无法连线到 FTP 服务器: \'%s\'。请检查您的 FTP-设定。';
$lang['Ftp_error_login'] = '无法登入到 FTP 服务器。这用户名称 \'%s\' 或密码是错误的。请检查您的 FTP-设定。';
$lang['Ftp_error_path'] = '无法存取 FTP 文件夹: \'%s\'。请检查您的 FTP 设定。';
$lang['Ftp_error_upload'] = '无法上传文件到 FTP 文件夹: \'%s\'。请检查您的 FTP 设定。';
$lang['Ftp_error_delete'] = '无法删除在 FTP 文件夹的文件: \'%s\'。请检查您的 FTP 设定。<br />';
$lang['Ftp_error_pasv_mode'] = '无法开启/关闭FTP被动模式';

// 附件规则页面
$lang['Rules_page'] = '附件规则';
$lang['Attach_rules_title'] = '已允许扩展名群组和他们的大小';
$lang['Group_rule_header'] = '%s -> 最大上传的大小: %s';
$lang['Allowed_extensions_and_sizes'] = '【说明】';
$lang['Note_user_empty_group_permissions'] = '注意:<br />你可以在这个论坛添加附件，<br />但是因为没有附件扩展名群组被允许添加加，<br />您无法添加任何文件。如果你试着附加文件，<br />你将会收到错误讯息。<br />';

// 限额相关
$lang['Upload_quota'] = '上传限额';
$lang['Pm_quota'] = '私人短信限额';
$lang['User_upload_quota_reached'] = '对不起，你已经达到了你的最大上传限额 %d %s'; // replace %d with Size, %s with Size Lang (MB for example)

// 用户附件控制面板
$lang['User_acp_title'] = '服务附件控制面板';
$lang['UACP'] = '用户附件控制面板';
$lang['User_uploaded_profile'] = '上传了: %s';
$lang['User_quota_profile'] = '限额为: %s';
$lang['Upload_percent_profile'] = '总共的%d%%';

// 公共的变量
$lang['Bytes'] = '字节';
$lang['KB'] = 'KB';
$lang['MB'] = 'MB';
$lang['GB'] = 'GB';
$lang['Attach_search_query'] = '搜索附件';
$lang['Test_settings'] = '测试设定';
$lang['Not_assigned'] = '未被指定';
$lang['No_file_comment_available'] = '无可用的文件注解';
$lang['Attachbox_limit'] = '您的附件箱已使用%d%%';
$lang['No_quota_limit'] = '没有附件限额';
$lang['Unlimited'] = '没有限制的';

?>