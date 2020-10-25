<?php
/***************************************************************************
 *                            lang_main.php [简体中文]
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

//    Краткая информация о проблемах с русским языком:
//
// 1) Если вместо русских букв вылезают символы &####;, то выкиньте из шаблона
//    overall_header.tpl строку <meta http-equiv="Content-Type" content="text/html; charset={S_CONTENT_ENCODING}">
// 2) Если не работает поиск по русскому тексту, то раскомментируйте следующую строку
//    (может не сработать, если нет соответствующей локали: проконсультируйтесь у своего
//    системного администратора):

// setlocale(LC_ALL, 'ru_RU.CP1251');
//$lang['ENCODING'] ='gb2312';
$lang['ENCODING'] = 'utf-8';
$lang['DIRECTION'] = 'ltr';
$lang['LEFT'] = 'left';
$lang['RIGHT'] = 'right';
$lang['DATE_FORMAT'] =  'Y年m月d日';  // This should be changed to the default date format for your language, php date() format

// This is optional, if you would like a _SHORT_ message output
// along with our copyright message indicating you are the translator
// please add it here.
$lang['TRANSLATION'] = '<a href=\'http://phpbb-wap.com/\'>中文phpBB-WAP</a>';

//
// Common, these terms are used
// extensively on several pages
//
$lang['Forum'] = '论坛首页';
$lang['Category'] = '论坛栏目';
$lang['Topic'] = '帖子';
$lang['Topics'] = '帖子';
$lang['Replies'] = '回复';
$lang['Views'] = '阅读';
$lang['Post'] = '发表帖子';        // 单数
$lang['Posts'] = '发表帖子';       // 复数
$lang['Posted'] = '发表时间';
$lang['Username'] = '用户名';
$lang['Password'] = '密码';
$lang['Email'] = 'Email';
$lang['Poster'] = '作者';          // post，也即是回复作者
$lang['Author'] = '作者';
$lang['Time'] = '时间';
$lang['Hours'] = '小时';
$lang['Message'] = '内容';

$lang['1_Day'] = '1 天以来';
$lang['7_Days'] = '7 天以来';
$lang['2_Weeks'] = '2 周以来';
$lang['1_Month'] = '1 个月来';
$lang['3_Months'] = '3 个月来';
$lang['6_Months'] = '6 个月来';
$lang['1_Year'] = '1 年以来';

$lang['Go'] = '确定';
$lang['Jump_to'] = '跳转';
$lang['Submit'] = '提交';
$lang['Reset'] = '重置';
$lang['Cancel'] = '清除';
$lang['Preview'] = '预览';
$lang['Confirm'] = '确认';
$lang['Spellcheck'] = '拼写检查';
$lang['Yes'] = '是';
$lang['No'] = '否';
$lang['Enabled'] = '开启';
$lang['Disabled'] = '关闭';
$lang['Error'] = '错误';

$lang['Next'] = '下一页>>';
$lang['Previous'] = '<<上一页';
$lang['Goto_page'] = '转到页面';
$lang['Joined'] = '注册时间';
$lang['IP_Address'] = 'IP信息';

$lang['Select_forum'] = '选择论坛';
$lang['View_latest_post'] = '查看最后发表的帖子';
$lang['View_newest_post'] = '查看最新发表的帖子';
$lang['Page_of'] = '第<b>%d</b>页(共有<b>%d</b>页)'; // 分页统计

$lang['ICQ'] = '用户 ICQ';
$lang['AIM'] = 'AIM';
$lang['MSNM'] = 'MSNM';
$lang['YIM'] = 'YIM';

$lang['Forum_Index'] = '论坛 %s';

$lang['Post_new_topic'] = '发表回复';
$lang['Reply_to_topic'] = '回复帖子';
$lang['Reply_with_quote'] = '引用回复';

$lang['Click_return_topic'] = '点击 %s这里%s 返回主题'; // 请不要修改或者汉化 %s
$lang['Click_return_login'] = '点击 %s这里%s 返回重试';
$lang['Click_return_forum'] = '点击 %s这里%s 返回论坛';
$lang['Click_view_message'] = '点击 %s这里%s 查看贴子';
$lang['Click_return_modcp'] = '点击 %s这里%s 返回版主控制面版';
$lang['Click_return_group'] = '点击 %s这里%s 返回管理团队信息';

$lang['Admin_panel'] ='网站后台管理'; 

$lang['Board_disable'] = '对不起, 该论坛暂时不可用, 请稍候重试';


//
// 全局 Header strings
//
$lang['Registered_users'] = '注册用户:';
$lang['Browsing_forum'] = '浏览论坛:';
$lang['Online_users_zero_total'] = '当前共有 <b>0</b> 位用户在线';
$lang['Online_users_total'] = '当前共有 <b>%d</b> 位用户在线';
$lang['Online_user_total'] = '当前共有 <b>%d</b> 位用户在线';
$lang['Reg_users_zero_total'] = '在线会员：0, ';
$lang['Reg_users_total'] = '在线会员：%d, ';
$lang['Reg_user_total'] = '在线会员： %d, ';
$lang['Hidden_users_zero_total'] = '隐身用户：0 位';
$lang['Hidden_users_total'] = '隐身用户：%d 位';
$lang['Hidden_user_total'] = '隐身用户：%d 位';
$lang['Guest_users_zero_total'] = '在线游客：0';
$lang['Guest_users_total'] = '在线游客：%d';
$lang['Guest_user_total'] = '在线游客：%d';
$lang['Record_online_users'] = '最高在线记录 <b>%s</b> 位 ( %s )'; 

$lang['Admin_online_color'] ='%s 超级管理员 %s'; 
$lang['Mod_online_color'] ='%s 论坛版主 %s'; 

$lang['You_last_visit'] = '最近访问: %s'; 
$lang['Current_time'] = '现在时间: %s'; 

$lang['Search_new'] = '浏览论坛新贴';
$lang['Search_your_posts'] = '浏览您的发贴';
$lang['Search_unanswered'] = '尚无回贴主题';

$lang['Register'] = '注册用户';
$lang['Profile'] = '用户资料';
$lang['Edit_profile'] = '编辑用户资料';
$lang['Search'] = '搜索论坛';
$lang['Memberlist'] = '成员列表';
$lang['FAQ'] = '问题解答';
$lang['BBCode_guide'] = 'BBCode 向导';
$lang['Usergroups'] = '团队';
$lang['Last_Post'] = '最新发贴';
$lang['Moderator'] = '论坛版主:';
$lang['Moderators'] = '论坛版主';


//
// 统计
//
$lang['Posted_articles_zero_total'] = '发贴总数: <b>0</b>'; 
$lang['Posted_articles_total'] = '帖子总数: <b>%d</b>'; 
$lang['Posted_article_total'] = '发贴总数: <b>%d</b>'; 
$lang['Registered_users_zero_total'] = '注册成员: <b>0</b>'; 
$lang['Registered_users_total'] = '注册成员: <b>%d</b>'; 
$lang['Registered_user_total'] = '注册成员: <b>%d</b>'; 
$lang['Newest_user'] = '最新注册: <b>%s%s%s</b>'; 

$lang['No_new_posts_last_visit'] = '自您上次访问以来, 没有新贴发表';
$lang['No_new_posts'] = '没有新贴';
$lang['New_posts'] = '新的发贴';
$lang['New_post'] = '新的发贴';
$lang['No_new_posts_hot'] = '没有新贴 [ 热贴 ]';
$lang['New_posts_hot'] = '新的发贴 [ 热贴 ]';
$lang['No_new_posts_locked'] = '没有新贴 [ 锁定 ]';
$lang['New_posts_locked'] = '新的发贴 [ 锁定 ]';
$lang['Forum_is_locked'] = '论坛锁定';


//
// 登录
//
$lang['Enter_password'] = '请输入用户名称和用户密码登陆论坛';
$lang['Login'] = '登录';
$lang['Logout'] = '退出';

$lang['Forgotten_password'] = '忘记密码?';

$lang['Log_me_in'] = '记住我';

$lang['Error_login'] = '用户名称或者用户密码错误';


//
// 首页
//
$lang['Index'] = '论坛首页';
$lang['No_Posts'] = '没有发表帖子';
$lang['No_forums'] = '没有建立论坛';

$lang['Private_Message'] = '私人消息';
$lang['Private_Messages'] = '私人消息';
$lang['Who_is_Online'] = '谁在线？';

$lang['Mark_all_forums'] = '标记所有论坛已读';
$lang['Forums_marked_read'] = '所有论坛已经标记已读';


//
// Viewforum 模版
//
$lang['View_forum'] ='浏览论坛'; 

$lang['Forum_not_exist'] ='您选择的论坛不存在'; 
$lang['Reached_on_error'] = '指定到达的页面错误';

$lang['Display_topics'] = '查看主题范围';
$lang['All_Topics'] = '所有主题';

$lang['Topic_Announcement'] = '<b>[公告]</b>';
$lang['Topic_Sticky'] = '<b>[置顶]</b>';
$lang['Topic_Moved'] = '<b>[移动]</b>';
$lang['Topic_Poll'] = '<b>[投票]</b>';

$lang['Mark_all_topics'] = '标记所有主题已读';
$lang['Topics_marked_read'] = '这个论坛的所有主题已经标记已读';

$lang['Rules_post_can'] = '您 <b>可以</b> 发表新贴';
$lang['Rules_post_cannot'] = '您 <b>不能</b> 发表新贴';
$lang['Rules_reply_can'] = '您 <b>可以</b> 回复主题';
$lang['Rules_reply_cannot'] = '您 <b>不能</b> 回复主题';
$lang['Rules_edit_can'] = '您 <b>可以</b> 编辑发贴';
$lang['Rules_edit_cannot'] = '您 <b>不能</b> 编辑发贴';
$lang['Rules_delete_can'] = '您 <b>可以</b> 删除发贴';
$lang['Rules_delete_cannot'] = '您 <b>不能</b> 删除发贴';
$lang['Rules_vote_can'] = '您 <b>可以</b> 投票贴子';
$lang['Rules_vote_cannot'] = '您 <b>不能</b> 投票贴子';
$lang['Rules_moderate'] = '您 <b>可以</b> %s管理论坛%s'; 

$lang['No_topics_post_one'] = '论坛中没有贴子<br />点击 <b>发表新贴</b> 链接发表贴子';

//
// Viewtopic 模版
//
$lang['View_topic'] = '查看主题';

$lang['Guest'] = '论坛游客';
$lang['Post_subject'] = '帖子标题';
$lang['View_next_topic'] = '下一主题';
$lang['View_previous_topic'] = '上一主题';
$lang['Submit_vote'] = '提交投票';
$lang['View_results'] = '查看结果';

$lang['No_newer_topics'] = '论坛没有更新的主题';
$lang['No_older_topics'] = '论坛没有更旧的主题';
$lang['Topic_post_not_exist'] = '主题不存在';
$lang['No_posts_topic'] = '主题没有这样的贴子';

$lang['Display_posts'] = '显示贴子范围';
$lang['All_Posts'] = '所有贴子';
$lang['Newest_First'] = '新贴在前面';
$lang['Oldest_First'] = '旧贴在前面';

$lang['Back_to_top'] ='返回顶部'; 

$lang['Read_profile'] = '浏览用户个人资料'; 
$lang['Visit_website'] = '访问发贴作者网站';
$lang['ICQ_status'] = 'ICQ 状态';
$lang['Edit_delete_post'] = '编辑';
$lang['View_IP'] = '查看IP';
$lang['Delete_post'] = '删除';

$lang['wrote'] = '写道:'; 
$lang['Quote'] = '引用:'; 
$lang['Code'] = '代码:'; 

$lang['Edited_time_total'] = '最近编辑: %s 于 %s, 编辑次数: %d'; 
$lang['Edited_times_total'] = '最近编辑: %s 于 %s, 编辑次数: %d'; 

$lang['Lock_topic'] = '锁定主题';
$lang['Unlock_topic'] = '解锁主题';
$lang['Move_topic'] = '移动主题';
$lang['Delete_topic'] = '删除主题';
$lang['Split_topic'] = '分割主题';

$lang['Stop_watching_topic'] = '停止跟踪';
$lang['Start_watching_topic'] = '跟踪主题';
$lang['No_longer_watching'] = '您不再跟踪这个主题';
$lang['You_are_watching'] = '您现在开始跟踪这个主题';

$lang['Total_votes'] = '投票共计';

//
// 发表、回复（非私人消息）
//
$lang['Message_body'] ='帖子内容'; 
$lang['Topic_review'] ='主题评论'; 

$lang['No_post_mode'] = '没有指定发贴模式'; 

$lang['Post_a_new_topic'] = '发表一个新的主题';
$lang['Post_a_reply'] = '对当前主题发表回复';
$lang['Post_topic_as'] = '发表一个新的主题';
$lang['Edit_Post'] = '编辑主题/帖子';
$lang['Options'] = '发贴选项';

$lang['Post_Announcement'] = '公告';
$lang['Post_Sticky'] = '置顶';
$lang['Post_Normal'] = '正常';

$lang['Confirm_delete'] = '您真的要删除该贴子吗?';
$lang['Confirm_delete_poll'] = '您真的要删除该投票吗?';

$lang['Flood_Error'] = '您不能马上发表第二条信息, 因为小于发表两条信息所必须最小间隔时间, 请稍候重试';
$lang['Empty_subject'] = '发表新贴, 必须指定主题标题';
$lang['Empty_message'] = '发贴过程必须有贴子内容';
$lang['Forum_locked'] = '论坛已经锁定, 不能发表新贴, 回复主题和编辑发贴';
$lang['Topic_locked'] = '主题已经锁定,不能编辑贴子和回复主题';
$lang['No_post_id'] = '您必须选择要编辑的贴子';
$lang['No_topic_id'] = '您必须选择要回复的主题';
$lang['No_valid_mode'] = '您只能发表新贴, 回复主题或者引用回复, 请返回重试';
$lang['No_such_post'] = '没有这样的贴子, 请返回重试';
$lang['Edit_own_posts'] = '对不起, 您只能编辑自己的发贴';
$lang['Delete_own_posts'] = '对不起, 您只能删除自己的发贴';
$lang['Cannot_delete_replied'] = '对不起, 您不能删除发贴, 因为已经有回贴';
$lang['Cannot_delete_poll'] = '对不起, 您不能删除一个带有活动投票的贴子';
$lang['Empty_poll_title'] = '您必须输入投票标题';
$lang['To_few_poll_options'] = '您必须最小输入两个投票选项';
$lang['To_many_poll_options'] = '您偿试太多的投票选项';
$lang['Post_has_no_poll'] = '这个贴子没有投票';
$lang['Already_voted'] = '您已经投票';
$lang['No_vote_option'] = '你必须指定投票选项';

$lang['Add_poll'] = '添加投票';
$lang['Add_poll_explain'] = '假如不想为主题添加投票, 请保留下面项目空白';
$lang['Poll_question'] = '投票问题';
$lang['Poll_option'] = '投票选项';
$lang['Add_option'] = '添加选项';
$lang['Update'] = '更新';
$lang['Delete'] = '删除';
$lang['Poll_for'] = '有效期限';
$lang['Days'] = '天数'; 
$lang['Poll_for_explain'] = '[ 单位:天. 输入 0 或者保留空白表示无限期接受投票 ]';
$lang['Delete_poll'] = '删除投票'; 

$lang['Disable_HTML_post'] = '在该贴中屏蔽 HTML 代码';
$lang['Disable_BBCode_post'] = '在该贴中屏蔽 BBCode 代码';
$lang['Disable_Smilies_post'] = '在该贴中屏蔽表情代码';

$lang['HTML_is_ON'] = 'HTML 代码 <u>开放</u>';
$lang['HTML_is_OFF'] = 'HTML 代码 <u>关闭</u>';
$lang['BBCode_is_ON'] = 'BBCode 代码 <u>开放</u>';
$lang['BBCode_is_OFF'] = 'BBCode 代码 <u>关闭</u>';
$lang['Smilies_are_ON'] = '表情代码 <u>开放</u>';
$lang['Smilies_are_OFF'] = '表情代码 <u>关闭</u>';

$lang['Attach_signature'] = '个性签名 (个性签名可以在用户资料中进行修改)';
$lang['Notify'] = '当有回贴时候请通知我';

$lang['Stored'] = '您的贴子已经成功发表';
$lang['Deleted'] = '您的贴子已经成功删除';
$lang['Poll_delete'] = '您的投票已经成功删除';
$lang['Vote_cast'] = '您已经完成投票';

$lang['Topic_reply_notification'] = '主题回复通知';

$lang['bbcode_b_help'] = '粗体文字: [b]粗体文字[/b]  (alt+b)';
$lang['bbcode_i_help'] = '斜体文字: [i]斜体文字[/i]  (alt+i)';
$lang['bbcode_u_help'] = '下划线文字: [u]下划线文字[/u]  (alt+u)';
$lang['bbcode_q_help'] = '引用文字: [quote]引用文字[/quote]  (alt+q)';
$lang['bbcode_c_help'] = '代码显示: [code]代码范例[/code]  (alt+c)';
$lang['bbcode_l_help'] = '子弹列表: [list]子弹列表[/list] (alt+l)';
$lang['bbcode_o_help'] = '顺序列表: [list=]顺序列表[/list]  (alt+o)';
$lang['bbcode_p_help'] = '插入图像: [img]http://image_url[/img]  (alt+p)';
$lang['bbcode_w_help'] = '插入链接: [url]http://url[/url] 或者 [url=http://url]链接标题[/url]  (alt+w)';
$lang['bbcode_a_help'] = '关闭所有标签';
$lang['bbcode_s_help'] = '字体颜色: [color=red]范例文字[/color]  提示: 也可以使用 color=#FF0000';
$lang['bbcode_f_help'] = '字体大小: [size=x-small]小字体[/size]';

$lang['Font_color'] = '字体颜色';
$lang['color_default'] = '默认';
$lang['color_dark_red'] = '深红';
$lang['color_red'] = '红色';
$lang['color_orange'] = '橙色';
$lang['color_brown'] = '棕色';
$lang['color_yellow'] = '黄色';
$lang['color_green'] = '绿色';
$lang['color_olive'] = '橄榄';
$lang['color_cyan'] = '青色';
$lang['color_blue'] = '蓝色';
$lang['color_dark_blue'] = '深蓝';
$lang['color_indigo'] = '靛蓝';
$lang['color_violet'] = '蓝紫';
$lang['color_white'] = '白色';
$lang['color_black'] = '黑色';

$lang['Font_size'] = '字体大小';
$lang['font_tiny'] = '很小';
$lang['font_small'] = '小号';
$lang['font_normal'] = '正常';
$lang['font_large'] = '大号';
$lang['font_huge'] = '巨大';

$lang['Close_Tags'] = '关闭标签';
$lang['Styles_tip'] = '提示: 这些风格设置可以快速地应到指定的文字当中';


//
// 私人消息
//
$lang['Private_Messaging'] = 'Личные сообщения';

$lang['Login_check_pm'] = '私人信息';
$lang['New_pms'] = '您有%d条新消息'; // You have 2 new messages
$lang['New_pm'] = '您有%d条新消息'; // You have 1 new message
$lang['No_new_pm'] = '没有未读消息';
$lang['Unread_pms'] = '未读信息 %d';
$lang['Unread_pm'] = '未读信息 %d';
$lang['No_unread_pm'] = '没有未读信息';
$lang['You_new_pm'] = '1 条新的悄悄话在收件箱';
$lang['You_new_pms'] = '条新的悄悄话在收件箱';
$lang['You_no_new_pm'] = '当前没有新的悄悄话';

$lang['Unread_message'] = '已读消息';
$lang['Read_message'] = '未读消息';

$lang['Read_pm'] = '阅读信息';
$lang['Post_new_pm'] = '发布信息';
$lang['Post_reply_pm'] = '回复';
$lang['Post_quote_pm'] = '引用';
$lang['Edit_pm'] = '编辑';

$lang['Inbox'] = '收件箱';
$lang['Outbox'] = '已发箱';
$lang['Savebox'] = '草稿箱';
$lang['Sentbox'] = '发件箱';
$lang['Flag'] = '图标';
$lang['Subject'] = '标题';
$lang['From'] = '来自';
$lang['To'] = '收件人';
$lang['Date'] = '日期';
$lang['Mark'] = '标记';
$lang['Sent'] = '发送';
$lang['Saved'] = '保存';
$lang['Delete_marked'] = '删除已读';
$lang['Delete_all'] = '全部删除';
$lang['Save_marked'] = '保存已读'; 
$lang['Save_message'] = '保存信息';
$lang['Delete_message'] = '删除信息';

$lang['Display_messages'] = '显示信息范围'; 
$lang['All_Messages'] = '所有信息';

$lang['No_messages_folder'] = '该目录没有内容';

$lang['PM_disabled'] = '论坛屏蔽了私人信息功能';
$lang['Cannot_send_privmsg'] = '对不起, 仅论坛管理员可以拒收私人信息';
$lang['No_to_user'] = '必须指定一个用户名称才能发送信息';
$lang['No_such_user'] = '对不起, 没有这样的用户';

$lang['Disable_HTML_pm'] = '在这个信件里禁止HTML语言';
$lang['Disable_BBCode_pm'] = '在这个信件里禁止BBCode';
$lang['Disable_Smilies_pm'] = '在这个信件里禁止表情符号';

$lang['Message_sent'] ='您的消息已发送'; 

$lang['Click_return_inbox'] = '点击 %s这里%s 返回论坛首页';
$lang['Click_return_index'] = '点击 %s这里%s 返回论坛首页';

$lang['Send_a_new_message'] = '发送私人信息';
$lang['Send_a_reply'] = '回复私人信息';
$lang['Edit_message'] = '编辑私人信息';

$lang['Notification_subject'] = '有新的私人信息';

$lang['Find_username'] = '查找用户名称';
$lang['Find'] = '查找';
$lang['No_match'] = '没有找到匹配';

$lang['No_post_id'] = '没有指定发贴 ID';
$lang['No_such_folder'] = '没有这样的文件夹';
$lang['No_folder'] = '没有指定文件';

$lang['Mark_all'] = '全部已读';
$lang['Unmark_all'] = '全部未读';

$lang['Confirm_delete_pm'] = '您真的要删除这条信息吗?';
$lang['Confirm_delete_pms'] = '您真的要删除这些信息吗?';

$lang['Inbox_size'] = '收件箱已用 %d%%'; 
$lang['Sentbox_size'] = '发件箱已用 %d%%'; 
$lang['Savebox_size'] = '保存箱已用 %d%%'; 

$lang['Click_view_privmsg'] = '点击 %s这里%s 进入收件箱';


//
// 个人资料、注册
//
$lang['Viewing_user_profile'] = '查看%s的个人资料'; // %s 是用户名
$lang['About_user'] = '关于 %s'; // %s 是用户名

$lang['Edit_Prorile_Reg'] ='修改密码'; 
$lang['Edit_Prorile_Style'] ='编辑风格'; 
$lang['Edit_Select_Style'] = '选择风格';
$lang['Edit_Prorile_Config'] ='修改设置'; 
$lang['Edit_Prorile_Info'] ='修改资料'; 

$lang['Website'] = '网站';
$lang['Location'] = '所在地';
$lang['Contact'] = '联系方式'; 
$lang['Email_address'] = 'E-mail地址';
$lang['Send_private_message'] ='发送私人消息'; 
$lang['Hidden_email'] ='[隐藏]'; 
$lang['Interests'] ='爱好'; 
$lang['Occupation'] ='职业'; 
$lang['Poster_rank'] ='排名'; 

$lang['Total_posts'] = '发贴总数';
$lang['User_post_pct_stats'] = '总数比率: %d%%'; 
$lang['User_post_day_stats'] = '平均每天发贴数量: %.2f'; 
$lang['Search_user_posts'] = '搜索 %s 的所有发贴'; 

$lang['No_user_id_specified'] = '对不起, 用户不存在';
$lang['Wrong_Profile'] ='您不能编辑他人的资料。'; 

$lang['Only_one_avatar'] = '只能指定一个头像类型';
$lang['File_no_data'] = '您指定的 URL 没有包含相关的文件数据';
$lang['No_connection_URL'] = '无法连接到您指定的 URL';
$lang['Incomplete_URL'] = '您输入 URL 不完整';
$lang['Wrong_remote_avatar_format'] = '指定的 URL 远程头像不正确';
$lang['No_send_account_inactive'] = '对不起, 因为您的帐号尚未激活, 所以无法使用找回密码功能. 请联系论坛管理员以获得更多的信息';

$lang['Always_smile'] = '总是允许表情符号';
$lang['Always_html'] = '总是允许 HTML';
$lang['Always_bbcode'] = '总是允许 BBCode';
$lang['Always_add_sig'] = '总是附上个性签名';
$lang['Always_notify'] = '总是接受回贴通知';
$lang['Always_notify_to_email'] = '回复时顺便发送 e-mail';
$lang['Always_notify_to_pm'] = '回复时顺便发送私人消息';
$lang['Notify_to_pm_subject'] = '无法发送主题到私人消息';
$lang['Notify_to_pm_msg'] = '您好! 您收到此消息是因为浏览主题“ %s ”，在本主题中，一个新的消息自您上次访问. 您可以点击链接读取; 新的通知将不会被接收，直到你检查出的话题 %s . 如果你不想按照主题，或点击链接“ 不再通知 ” , 在页面的底部，或点击以下链接 %s';
$lang['Always_notify_explain'] = '当有人回复主题时候, 发送邮件通知给我. 可以在您的任何发贴中修改';

$lang['Board_style'] = '风格';
$lang['Board_lang'] = '语言';
$lang['No_themes'] = '没有主题可选择';
$lang['Timezone'] = '所在时区';
$lang['Date_format'] = '日期格式';
$lang['Date_format_explain'] = '使用方法完全遵循于 PHP <a href=\'http://www.php.net/date\' target=\'_other\'>date()</a> 功能. 例如: d M Y h:i a, 其中, d 代表日期, M 代表月份, Y 代表年份, h:i a 代表 AM/PM 时间格式, H:i 代表 24-小时 时间格式. 您可以根据使用习惯进行调整其在论坛中的显示方式';
$lang['Signature'] = '个性签名';
$lang['Signature_explain'] = '个性签名将添加在您所发表贴子的尾部. 当前限制其长度为 %d 英文字符';
$lang['Public_view_email'] = '总是显示我的邮件地址';

$lang['Current_password'] = '当前密码';
$lang['New_password'] = '新的密码';
$lang['Confirm_password'] = '确认密码';
$lang['Confirm_password_explain'] = '当您希望改变密码或是您的电子邮件地址时您必须确认现在正在使用的密码';
$lang['password_if_changed'] = '假如不想修改密码, 请保留空白';
$lang['password_confirm_if_changed'] = '当修改密码时候, 请重复输入新的密码';

$lang['Avatar'] = '个性头像';
$lang['Avatar_explain'] = '个性头像是在您发布信息的时候显示在用户名称下方的小图片. 一次只能使用一个小图片, 图像大小限制在: 宽不能大于 %d 像素, 高不能大于 %d 像素并且文件长度必须小于 %dkB.';
$lang['Upload_Avatar_file'] = '从您的电脑上传个性头像';
$lang['Upload_Avatar_URL'] = '从一个 URL 链接上传个性头像';
$lang['Upload_Avatar_URL_explain'] = '输入完整的头像图片所在 URL 链接位置, 则图片将被复制到该本地服务器上.';
$lang['Pick_local_Avatar'] = '从头像列表中选择个性头像';
$lang['Link_remote_Avatar'] = '链接个性头像';
$lang['Link_remote_Avatar_explain'] = '输入完整的头像图片所在 URL 链接位置, 则您的个性头像链接到该网址.';
$lang['Avatar_URL'] = '个性头像 URL';
$lang['Select_from_gallery'] = '从头像列表中选择个性头像';
$lang['View_avatar_gallery'] = '显示个性头像列表';

$lang['Select_avatar'] = '选择头像';
$lang['Return_profile'] = '重置头像';
$lang['Select_category'] = '选择分类';

$lang['Delete_Image'] ='删除图像'; 
$lang['Current_Image'] ='当前图像'; 

$lang['Notify_on_privmsg'] = '私人信息邮件通知';
$lang['Popup_on_privmsg'] = '当有新的私人信息弹出窗口'; 
$lang['Popup_on_privmsg_explain'] = '当有新的私人信息时候, 某些模板可以打开新的窗口进行提示'; 
$lang['Hide_user'] = '隐藏您的论坛在线状态';

$lang['Profile_updated'] = '您的用户资料已经更新';
$lang['Profile_updated_inactive'] = '您的用户资料已经更新, 但因为修改了某些重要的用户资料部分, 当前帐号暂时处于不可用. 请收取邮件将被告知如何重新激活您的帐号, 或者请求论坛管理员恢复激活您的帐号';

$lang['Password_mismatch'] = '两次输入密码不匹配';
$lang['Current_password_mismatch'] = '提供的当前密码不正确所以修改密码不成功';
$lang['Password_long'] = '密码不能多于 32 个字符';
$lang['Username_taken'] ='对不起，该名称的用户已经存在'; 
$lang['Username_invalid'] ='对不起，你不能使用这个名字'; 
$lang['Username_disallowed'] ='对不起，这个名字不允许使用'; 
$lang['Email_taken'] ='对不起，这个E-mail地址已经被其他用户使用'; 
$lang['Email_banned'] ='对不起，这个E-mail地址已经被列入黑名单'; 
$lang['Email_invalid'] ='对不起，这个E-mail地址是不正确'; 
$lang['Invalid_username'] ='所请求的用户名已被使用，禁止或包含不恰当字符，（例如）'; 
$lang['Signature_too_long'] ='签名太长'; 
$lang['Fields_empty'] ='您必须填写必填字段'; 
$lang['Avatar_filetype'] ='头像文件必须是.JPG .gif或.png'; 
$lang['Avatar_filesize'] ='头像文件不大于 %d KB'; 
$lang['Avatar_imagesize'] ='头像不得超过%d像素宽和%d像素高'; 

$lang['Welcome_subject'] = '欢迎您访问 %s 论坛'; 
$lang['New_account_subject'] = '新的用户帐号';
$lang['Account_activated_subject'] = '帐号已经激活';

$lang['Account_added'] = '谢谢您的注册, 帐号已经建立. 马上就使用您的用户名称和用户密码登陆论坛';
$lang['Account_inactive'] = '您的帐号已经建立. 然而, 由于论坛需要帐号激活请求, 一段激活代码已经通过您先前提供的邮件地址发送出去. 请收信并查阅激活代码';
$lang['Account_inactive_admin'] = '您的帐号已经建立. 然而, 由于论坛限制帐号必须由论坛管理员激活. 当您的帐号被论坛管理员激活时候将发送一个邮件通知给您';
$lang['Account_active'] = '您的帐号已经激活. 谢谢您的注册';
$lang['Account_active_admin'] = '该帐号现在已经激活';
$lang['Reactivate'] = '重新激活帐号!';
$lang['Already_activated'] = '您已经激活帐号';
$lang['COPPA'] = '您的帐号已经获得批准并且被激活, 请收信查阅相关信息.';

$lang['Registration'] = '论坛注册章程';
$lang['Reg_agreement'] = '尽管论坛管理员和版主们将努力控制不适合发表的贴子产生, 但不可能做到仔细阅读每个贴子内容. 论坛用户发表的所有信息将迅速出现在论坛上, 其所发表的信息仅代表发贴者本人的言行与见解, 并且由此可能引起的任何争议与纠纷, 甚至法律责任均由发贴者单独承担其所发表内容的全部责任。与本论坛和 <a href=http://www.phpbb.com target=_blank>phpBB Group</a> (phpBB2 开发者)及其<a href=http://www.chinacpu.com target=_blank>中国中心资讯网</a>(本简体中文版本制作者)无任何关系, 都不承担任何责任.<br /><br />您必需同意不发表带有辱骂, 淫秽, 粗俗, 诽谤, 带有仇恨性, 恐吓的, 不健康的或是任何违反法律的内容. 如果您这样做将导致您的账户将立即和永久性的被封锁. (您的网络服务提供商也会被通知). 在这个情况下, 这个IP地址的所有用户都将被记录. 您必须同意系统管理成员们有在任何时间删除, 修改, 移动或关闭任何主题的权力. 作为一个论坛的一个成员, 您必须同意您所提供的任何资料都将被存入数据库中, 这些资料除非有您的同意, 系统管理员们绝不会对第三方公开, 然而我们不能保证任何可能导致资料泄露的骇客入侵行为.<br /><br />1. 遵守中华人民共和国的各项有关法律法规. <br />2. 不得在发布任何色情非法, 以及危害国家安全的言论. <br />3. 严禁链接有关政治, 色情, 宗教, 迷信等违法信息. <br />4. 承担一切因您的行为而直接或间接导致的民事或刑事法律责任. <br />5. 互相尊重, 遵守互联网络道德; 严禁互相恶意攻击, 漫骂. <br />6. 管理员及版主有权保留或删除论坛中的任意内容. <br />7. 论坛管理员拥有一切管理论坛的权力.<br /><br />这个讨论区系统使用cookie来储存您的个人信息(在您使用的本地计算机), 这些cookie不包含任何您曾经输入过的信息,它们只为了方便您能更方便的浏览. 电子邮件地址只用来确认您的注册和发送密码使用.(如果您忘记了密码,将会发送新密码的地址)<br /><br />当您点击下面链接的同时, 表示您愿意无条件接受上述阐述与声明, 并且对所有的言行负责.<br /><br />论坛管理员在任何时候, 拥有删除, 编辑, 移动或者关闭任何线索(贴子)的权力';

$lang['Agree_under_13'] = '我同意这些协议, 但是 <b>小于</b> 13 岁';
$lang['Agree_over_13'] = '我同意这些协议, 并且 <b>大于</b> 13 岁';
$lang['Agree_not'] = '我不同意这些协议';

$lang['Wrong_activation'] = '激活代码有误';
$lang['Send_password'] = '发送新密码给我'; 
$lang['Password_updated'] = '新的密码已经建立, 请收信并查阅信息, 以便了解如何使新的密码生效';
$lang['No_email_match'] = '无法从论坛用户列表中找到与该邮件地址相匹配的用户名称';
$lang['New_password_activation'] = '新的密码已经生效';
$lang['Password_activated'] = '您的帐号已经生效. 请使用您收到的邮件中提供的用户名称和用户密码进行登陆论坛';

$lang['Send_email_msg'] = '发送邮件消息';
$lang['No_user_specified'] = '没有指定用户';
$lang['User_prevent_email'] = '该用户不希望接收邮件. 请偿试发送私人信息';
$lang['User_not_exist'] = '用户不存在';
$lang['CC_email'] = '发送一份拷贝给您自己';
$lang['Email_message_desc'] = '只能发送纯文本信息, 不能包括任何 HTML 代码或者 BBCode 论坛代码. 该返回信息将发送到您的邮件地址.';
$lang['Flood_email_limit'] = '您当前不能发送其他的邮件给其他人, 请稍候重试';
$lang['Recipient'] = '接收者';
$lang['Email_sent'] = '邮件已经发送';
$lang['Send_email'] = '发送邮件';
$lang['Empty_subject_email'] = '您必须指定邮件标题';
$lang['Empty_message_email'] = '您必须邮件内容';

//
// 确认
//
$lang['Confirm_code_wrong'] ='确认代码输入无效'; 
$lang['Too_many_registers'] ='您已经达到了注册尝试限制数量,请稍后再试.'; 
$lang['Confirm_code_impaired'] = '如果您有视力障碍或任何其他原因无法读取该代码，请联系论坛的 %s超级管理员%s.';
$lang['Confirm_code'] = '验证码';
$lang['Confirm_code_explain'] = '请输入您看到的验证码！';

//
// 会员列表
//
$lang['Select_sort_method'] ='排序方式'; 
$lang['Sort'] ='排序'; 
$lang['Sort_Top_Ten'] ='Top 10'; 
$lang['Sort_Joined'] ='注册时间'; 
$lang['Sort_money'] ='money数量';
$lang['Sort_Username'] ='用户昵称'; 
$lang['Sort_Location'] ='所在地'; 
$lang['Sort_Posts'] ='用户帖子'; 
$lang['Sort_Email'] ='邮件'; 
$lang['Sort_Website'] ='博客'; 
$lang['Sort_Ascending'] ='递增'; 
$lang['Sort_Descending'] ='递减'; 
$lang['Order'] = '顺序'; 

//
// 小组管理面板
//
$lang['Group_Control_Panel'] ='用户组控制面版'; 
$lang['Group_member_details'] = '团队列表';
$lang['Group_member_join'] = '加入用户组';

$lang['Group_Information'] = '用户组信息';
$lang['Group_name'] = '用户组名称';
$lang['Group_description'] = '用户组描述';
$lang['Group_membership'] = '用户组成员资格';
$lang['Group_Members'] = '用户组成员';
$lang['Group_Moderator'] = '用户组管理者';
$lang['Pending_members'] = '未决成员';

$lang['Group_type'] = '用户组类型';
$lang['Group_open'] = '打开用户组';
$lang['Group_closed'] = '关闭用户组';
$lang['Group_hidden'] = '隐藏用户组';

$lang['Current_memberships'] ='团队列表如下'; 
$lang['Non_member_groups'] ='不是该组的成员'; 
$lang['Memberships_pending'] ='候选人的群体成员的'; 

$lang['No_groups_exist'] ='还没有组'; 
$lang['Group_not_exist'] ='本组不存在'; 

$lang['Join_group'] ='加入小队'; 
$lang['No_group_members'] ='这组没有成员'; 
$lang['Group_hidden_members'] ='这是隐藏的组，你看不到它的成员'; 
$lang['No_pending_group_members'] ='在这组有没有候补成员'; 
$lang['Group_joined'] ='您已要求加入该组织。当你请求版主小组批准，您将收到通知。'; 
$lang['Group_request'] ='案件被要求加入该组织。'; 
$lang['Group_approved'] ='您的请求得到了批准。'; 
$lang['Group_added'] ='您已计入本小队'; 
$lang['Already_member_group'] ='你已经是这个组的成员'; 
$lang['User_is_member_group'] ='用户已经是这个小组的成员'; 
$lang['Group_type_updated'] ='类型成功地更新'; 

$lang['Could_not_add_user'] ='选择用户不存在'; 
$lang['Could_not_anon_user'] ='你不能让匿名用户组的成员'; 

$lang['Confirm_unsub'] ='你确定要离开这个组？'; 
$lang['Confirm_unsub_pending'] ='你确定要拒绝参加这一组？您加入的要求，既不否认也不授予'; 

$lang['Unsub_success'] = '您已经成功退出.';

$lang['Approve_selected'] = '选择确认';
$lang['Deny_selected'] = '选择拒绝';
$lang['Not_logged_in'] = '您必须登陆到用户组.';
$lang['Remove_selected'] = '选择删除';
$lang['Add_member'] = '添加成员';
$lang['Not_group_moderator'] = '您不是该团队的管理员, 所以无法执行团队管理功能.';

$lang['Login_to_join'] = '请登陆用户或者管理团队成员';
$lang['This_open_group'] = '这是一个开放的团队, 点击申请成员';
$lang['This_closed_group'] = '这是一个关闭的团队, 不接受新的成员';
$lang['This_hidden_group'] = '这是一个隐藏的团队, 不容许自动增加成员';
$lang['Member_this_group'] = '您是这个团队的成员';
$lang['Pending_this_group'] = '您的申请正在审核中';
$lang['Are_group_moderator'] = '您是团队管理员';
$lang['None'] = '尚无用户';

$lang['Subscribe'] = '订阅';
$lang['Unsubscribe'] = '退订';
$lang['View_Information'] = '浏览信息';


//
// 搜索
//
$lang['Search_query'] = '搜索条件';
$lang['Search_options'] = '搜索选项';

$lang['Search_keywords'] = '按关键字搜索';
$lang['Search_keywords_explain'] = '您可以使用 <u>AND</u> , <u>OR</u> 和 <u>NOT</u> 连接词得到更准确的搜索结果. 使用 * 作为通配符. 短语请使用 &quot;&quot; 包括起来';
$lang['Search_author'] = '搜索作者';
$lang['Search_author_explain'] = '使用 * 作为通配符,例如:你要搜索的用户名是admin,那么你可以输入*dm*或*mi*或"*m*"来搜索！';

$lang['Search_for_any'] = '模糊搜索';
$lang['Search_for_all'] = '精确搜索';
$lang['Search_title_msg'] = '搜索贴子主题和内容';
$lang['Search_msg_only'] = '仅仅搜索贴子的内容';

$lang['Return_first'] = '只显示贴子前面'; // followed by xxx characters in a select box
$lang['characters_posts'] = '个字符';

$lang['Search_previous'] = '搜索范围'; // followed by days, weeks, months, year, all in a select box

$lang['Sort_by'] = '排列方法';
$lang['Sort_Time'] = '发表时间';
$lang['Sort_Post_Subject'] = '贴子标题';
$lang['Sort_Topic_Title'] = '主题标题';
$lang['Sort_Author'] = '主题作者';
$lang['Sort_Forum'] = '论坛名称';

$lang['Display_results'] = '显示结果';
$lang['All_available'] = '所有论坛';
$lang['No_searchable_forums'] = '您没有权限搜索任何论坛';

$lang['No_search_match'] = '没有找到本匹配的主题或者贴子';
$lang['Found_search_match'] = '找到 %d 个搜索结果'; // eg. Search found 1 match
$lang['Found_search_matches'] = '找到 %d 个搜索结果'; // eg. Search found 24 matches
$lang['Search_Flood_Error'] = '您不可以短时间内反复搜索，请稍候重试！';

$lang['Close_window'] = '关闭窗口';


//
// 权限相关
//
// 下面的 %s 讲取代 'user' arrays 数组
$lang['Sorry_auth_announce'] = '对不起, 仅 %s 可以发表公告';
$lang['Sorry_auth_sticky'] = '对不起, 仅 %s 可以置顶贴子'; 
$lang['Sorry_auth_read'] = '对不起, 仅 %s 可以阅读主题'; 
$lang['Sorry_auth_post'] = '对不起, 仅 %s 可以发表新贴'; 
$lang['Sorry_auth_reply'] = '对不起, 仅 %s 可以回复发贴'; 
$lang['Sorry_auth_edit'] = '对不起, 仅 %s 可以编辑发贴'; 
$lang['Sorry_auth_delete'] = '对不起, 仅 %s 可以删除贴子'; 
$lang['Sorry_auth_vote'] = '对不起,仅 %s 可以参与投票'; 

// 这里这些 %s 替换上面的字符串
$lang['Auth_Anonymous_Users'] = '游客';
$lang['Auth_Registered_Users'] = '注册用户';
$lang['Auth_Users_granted_access'] = '授权用户';
$lang['Auth_Moderators'] = '论坛版主';
$lang['Auth_Administrators'] = '超级用户';

$lang['Not_Moderator'] = '您不是该论坛版主';
$lang['Not_Authorised'] = '没有权限';

$lang['You_been_banned'] = '您已经被论坛禁止<br />请联系网站管理员或者论坛管理员以获得更多信息';


//
// Viewonline 模版
//
$lang['Reg_users_zero_online'] = '0 注册用户和 '; // There ae 5 Registered and
$lang['Reg_users_online'] = '%d 位注册用户和 ';
$lang['Reg_user_online'] = '%d 位注册用户和 '; // There ae 5 Registered and
$lang['Hidden_users_zero_online'] = '0 位隐身用户在线'; // 6 Hidden users online
$lang['Hidden_users_online'] = '%d 位隐身用户在线';
$lang['Hidden_user_online'] = '%d 位隐身用户在线'; // 6 Hidden users online
$lang['Guest_users_online'] = '%d 位论坛游客在线';
$lang['Guest_users_zero_online'] = '0 位论坛游客在线'; // There are 10 Guest users online
$lang['Guest_user_online'] = '%d 位论坛游客在线';
$lang['No_users_browsing'] = '当前没有用户浏览论坛';

$lang['Online_explain'] = '该数据只反映最近五分钟以来的论坛用户活动情况';

$lang['Forum_Location'] = '论坛位置';
$lang['Last_updated'] = '最新动作';

$lang['Forum_index'] = '论坛首页';
$lang['Logging_on'] = '正在登陆论坛';
$lang['Posting_message'] = '正在发表贴子';
$lang['Searching_forums'] = '正在搜索论坛';
$lang['Viewing_profile'] = '浏览用户资料';
$lang['Viewing_online'] = '浏览论坛在线';
$lang['Viewing_member_list'] = '浏览成员列表';
$lang['Viewing_priv_msgs'] = '浏览私人信息';
$lang['Viewing_FAQ'] = '浏览问题解答';


//
// 版主管理面板
//
$lang['Mod_CP'] = '论坛版主控制面版';
$lang['Mod_CP_explain'] = '您有权限使用下面表单对该论坛进行管理. 您可以锁定, 解锁, 移动或者删除任何主题. <br>假如该论坛被指定为加密论坛, 您还可以对论坛用户进行访问授权.';

$lang['Select'] = '选择';
$lang['Delete'] = '删除';
$lang['Move'] = '移动';
$lang['Lock'] = '锁定';
$lang['Unlock'] = '解锁';

$lang['Topics_Removed'] = '选择的主题已经成功从数据库中删除.';
$lang['Topics_Locked'] = '选择的主题已经锁定';
$lang['Topics_Moved'] = '选择的主题已经移动';
$lang['Topics_Unlocked'] = '选择的主题已经解锁';
$lang['No_Topics_Moved'] = '没有主题移动';

$lang['Confirm_delete_topic'] = '您真的要删除选定的主题吗?';
$lang['Confirm_lock_topic'] = '您真的要锁定选定的主题吗?';
$lang['Confirm_unlock_topic'] = '您真的要解锁选定的主题吗?';
$lang['Confirm_move_topic'] = '您真的要移动选择的主题吗?';

$lang['Move_to_forum'] = '移动主题到新的论坛';
$lang['Leave_shadow_topic'] = '移动帖子到新论坛,保留原帖子';

$lang['Split_Topic'] = '分割主题控制面版';
$lang['Split_Topic_explain'] = '使用下面表单, 可以将主题一分为二, 可以选择任一贴子或者从选定的贴子进行分割';
$lang['Split_title'] = '新主题标题';
$lang['Split_forum'] = '新贴所在论坛';
$lang['Split_posts'] = '分割已选贴子';
$lang['Split_after'] = '选择分割贴子';
$lang['Topic_split'] = '所选主题已经成功分割';

$lang['Too_many_error'] = '选择的贴子太多. 只可以选择主题中的一个贴子进行分割!';

$lang['None_selected'] = '您没有预先选择任何主题以供操作. 请返回最少选择一个.';
$lang['New_forum'] = '新的论坛';

$lang['This_posts_IP'] = '发表本贴使用的 IP 是';
$lang['Other_IP_this_user'] = '该用户曾经使用的其他 IP 是';
$lang['Users_this_IP'] = '使用该 IP 的用户有';
$lang['IP_info'] = 'IP 信息';
$lang['Lookup_IP'] = '查找 IP';


//
// 时间之类的东东
//
$lang['All_times'] = '所有时间: %s'; // eg. All times are GMT - 12 Hours (times from next block)

$lang['-12'] = 'GMT - 12 时间';
$lang['-11'] = 'GMT - 11 时间';
$lang['-10'] = 'GMT - 10 时间';
$lang['-9'] = 'GMT - 9 时间';
$lang['-8'] = 'GMT - 8 时间';
$lang['-7'] = 'GMT - 7 时间';
$lang['-6'] = 'GMT - 6 时间';
$lang['-5'] = 'GMT - 5 时间';
$lang['-4'] = 'GMT - 4 时间';
$lang['-3.5'] = 'GMT - 3.5 时间';
$lang['-3'] = 'GMT - 3 时间';
$lang['-2'] = 'GMT - 2 时间';
$lang['-1'] = 'GMT - 1 时间';
$lang['0'] = 'GMT';
$lang['1'] = 'GMT + 1 时间';
$lang['2'] = 'GMT + 2 时间';
$lang['3'] = 'GMT + 3 时间';
$lang['3.5'] = 'GMT + 3.5 时间';
$lang['4'] = 'GMT + 4 时间';
$lang['4.5'] = 'GMT + 4.5 时间';
$lang['5'] = 'GMT + 5 时间';
$lang['5.5'] = 'GMT + 5.5 时间';
$lang['6'] = 'GMT + 6 时间';
$lang['6.5'] = 'GMT + 6.5 时间';
$lang['7'] = 'GMT + 7 时间';
$lang['8'] = 'GMT + 8 北京时间';
$lang['9'] = 'GMT + 9 时间';
$lang['9.5'] = 'GMT + 9.5 时间';
$lang['10'] = 'GMT + 10 时间';
$lang['11'] = 'GMT + 11 时间';
$lang['12'] = 'GMT + 12 时间';
$lang['13'] = 'GMT + 13 时间';

// These are displayed in the timezone select box
$lang['tz']['-12'] = 'GMT - 12 时间';
$lang['tz']['-11'] = 'GMT - 11 时间';
$lang['tz']['-10'] = 'GMT - 10 时间';
$lang['tz']['-9'] = 'GMT - 9 时间';
$lang['tz']['-8'] = 'GMT - 8 时间';
$lang['tz']['-7'] = 'GMT - 7 时间';
$lang['tz']['-6'] = 'GMT - 6 时间';
$lang['tz']['-5'] = 'GMT - 5 时间';
$lang['tz']['-4'] = 'GMT - 4 时间';
$lang['tz']['-3.5'] = 'GMT - 3.5 时间';
$lang['tz']['-3'] = 'GMT - 3 时间';
$lang['tz']['-2'] = 'GMT - 2 时间';
$lang['tz']['-1'] = 'GMT - 1 时间';
$lang['tz']['0'] = 'GMT';
$lang['tz']['1'] = 'GMT + 1 时间';
$lang['tz']['2'] = 'GMT + 2 时间';
$lang['tz']['3'] = 'GMT + 3 时间';
$lang['tz']['3.5'] = 'GMT + 3.5 时间';
$lang['tz']['4'] = 'GMT + 4 时间';
$lang['tz']['4.5'] = 'GMT + 4.5 时间';
$lang['tz']['5'] = 'GMT + 5 时间';
$lang['tz']['5.5'] = 'GMT + 5.5 时间';
$lang['tz']['6'] = 'GMT + 6 时间';
$lang['tz']['6.5'] = 'GMT + 6.5 时间';
$lang['tz']['7'] = 'GMT + 7 时间';
$lang['tz']['8'] = 'GMT + 8 北京时间';
$lang['tz']['9'] = 'GMT + 9 时间';
$lang['tz']['9.5'] = 'GMT + 9.5 时间';
$lang['tz']['10'] = 'GMT + 10 时间';
$lang['tz']['11'] = 'GMT + 11 时间';
$lang['tz']['12'] = 'GMT + 12 时间';
$lang['tz']['13'] = 'GMT + 13 时间';

$lang['datetime']['Sunday'] = '星期日';
$lang['datetime']['Monday'] = '星期一';
$lang['datetime']['Tuesday'] = '星期二';
$lang['datetime']['Wednesday'] = '星期三';
$lang['datetime']['Thursday'] = '星期四';
$lang['datetime']['Friday'] = '星期五';
$lang['datetime']['Saturday'] = '星期六';
$lang['datetime']['Sun'] = '日';
$lang['datetime']['Mon'] = '一';
$lang['datetime']['Tue'] = '二';
$lang['datetime']['Wed'] = '三';
$lang['datetime']['Thu'] = '四';
$lang['datetime']['Fri'] = '五';
$lang['datetime']['Sat'] = '六';
$lang['datetime']['January'] = '1';//一月
$lang['datetime']['February'] = '2';//二月
$lang['datetime']['March'] = '3';//三月
$lang['datetime']['April'] = '4';//四月
$lang['datetime']['Mays'] = '5';//五月
$lang['datetime']['June'] = '6';//六月
$lang['datetime']['July'] = '7';//七月
$lang['datetime']['August'] = '8';//八月
$lang['datetime']['September'] = '9';//九月
$lang['datetime']['October'] = '10';//十月
$lang['datetime']['November'] = '11';//十一月
$lang['datetime']['December'] = '12';//十二月
$lang['datetime']['Jan'] = '1';
$lang['datetime']['Feb'] = '2';
$lang['datetime']['Mar'] = '3';
$lang['datetime']['Apr'] = '4';
$lang['datetime']['May'] = '5';
$lang['datetime']['Jun'] = '6';
$lang['datetime']['Jul'] = '7';
$lang['datetime']['Aug'] = '8';
$lang['datetime']['Sep'] = '9';
$lang['datetime']['Oct'] = '10';
$lang['datetime']['Nov'] = '11';
$lang['datetime']['Dec'] = '12';

//
// 错误提示 (not related to a
// specific failure on a page)
//
$lang['Information'] = '系统提示';
$lang['Critical_Information'] = '重要信息';

$lang['General_Error'] = '一般错误';
$lang['Critical_Error'] = '重要错误';
$lang['An_error_occured'] = '发生错误';
$lang['A_critical_error'] = '一个重要错误';

$lang['Admin_reauthenticate']= '要访问超级管理面板，你必须重新输入您的用户名和密码！';

$lang['Login_attempts_exceeded'] ='已达到尝试登录次数(%s)，您需要等待(%s)分钟才能再次尝试登录。'; 
$lang['Please_remove_install'] ='请先把install文件夹删除或改名！';
$lang['Session_invalid'] ='错误！请重新加载页面！'; 
$lang['Invalid_session'] ='错误！请重新加载页面！'; 

//
// That's all, Folks!
// -------------------------------------------------

?>