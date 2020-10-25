<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

//是否开启DEBUG模式（开启 1/true , 关闭 0/false）
define('DEBUG', 	1); 

//游客和已删除的用户id都是-1
define('DELETED', 	-1);
define('ANONYMOUS',	-1);

/**
* 用户权限
* 0 表示普通用户
* 1 超逸管理员
* 2 论坛版主
* 3 超级版主
**/
define('USER',	 					0);
define('ADMIN', 					1);
define('MOD', 						2);

define('HIDDEN_USER',				0);
define('ONLINE_USER',				1);

/**
* 激活会员的方式
* 0 不使用
* 1 用户自行激活
* 2 需要超级管理员审核激活
**/
define('USER_ACTIVATION_NONE', 		0);
define('USER_ACTIVATION_SELF', 		1);
define('USER_ACTIVATION_ADMIN',		2);

//头像
define('USER_AVATAR_NONE', 			0);
define('USER_AVATAR_UPLOAD', 		1);
define('USER_AVATAR_REMOTE', 		2);
define('USER_AVATAR_GALLERY', 		3);

//小组开关
define('GROUP_OPEN', 				0);
define('GROUP_CLOSED', 				1);
define('GROUP_HIDDEN', 				2);

//锁定论坛 or 解锁论坛
define('FORUM_UNLOCKED', 			0);
define('FORUM_LOCKED', 				1);

/**
* 解锁帖子
* 锁定帖子
* 移动帖子
**/
define('TOPIC_UNLOCKED', 			0);
define('TOPIC_LOCKED',	 			1);
define('TOPIC_MOVED', 				2);

//跟踪主题
define('TOPIC_WATCH_NOTIFIED', 		1);
define('TOPIC_WATCH_UN_NOTIFIED', 	0);

// 普通、置顶、公告
define('POST_NORMAL', 				0);
define('POST_STICKY', 				1);
define('POST_ANNOUNCE', 			2);

// 精华
define('POST_UNMARROW', 			0);
define('POST_MARROW', 				1);

// 专题
define('TOPIC_UNCLASS', 			0);

define('BEGIN_TRANSACTION', 		1);
define('END_TRANSACTION', 			2);

//信件
define('PRIVMSGS_READ_MAIL', 		0);
define('PRIVMSGS_NEW_MAIL', 		1);
define('PRIVMSGS_SENT_MAIL', 		2);
define('PRIVMSGS_SAVED_IN_MAIL', 	3);
define('PRIVMSGS_SAVED_OUT_MAIL', 	4);
define('PRIVMSGS_UNREAD_MAIL', 		5);

//session
define('SESSION_METHOD_COOKIE', 	100);
define('SESSION_METHOD_GET', 		101);

//$_GET
define('POST_TOPIC_URL', 			't');
define('POST_CAT_URL', 				'c');
define('POST_FORUM_URL', 			'f');
define('POST_USERS_URL', 			'u');
define('POST_POST_URL', 			'p');
define('POST_GROUPS_URL', 			'g');
define('POST_CLASS_URL', 			'class');

//页面
define('PAGE_INDEX', 				0);
define('PAGE_LOGIN', 				-1);
define('PAGE_SEARCH', 				-2);
define('PAGE_REGISTER', 			-3);
define('PAGE_PROFILE', 				-4);
define('PAGE_VIEWONLINE', 			-6);
define('PAGE_VIEWMEMBERS', 			-7);
define('PAGE_FAQ', 					-8);
define('PAGE_POSTING', 				-9);
define('PAGE_PRIVMSGS', 			-10);
define('PAGE_GROUPCP', 				-11);
define('PAGE_MODS', 				-12);
define('PAGE_PRAVILA', 				-13);
define('PAGE_RULES', 				-14);
define('PAGE_PAGES', 				-15);
define('PAGE_DOWNLOAD',				-16);
define('PAGE_CLASS', 				-17);
define('PAGE_ARTICLE', 				-18);

//权限
define('AUTH_LIST_ALL', 			0);
define('AUTH_ALL', 					0);
define('AUTH_REG', 					1);
define('AUTH_ACL', 					2);
define('AUTH_MOD', 					3);
define('AUTH_ADMIN', 				5);

define('AUTH_VIEW', 				1);
define('AUTH_READ', 				2);
define('AUTH_POST', 				3);
define('AUTH_REPLY', 				4);
define('AUTH_EDIT', 				5);
define('AUTH_DELETE', 				6);
define('AUTH_ANNOUNCE', 			7);
define('AUTH_STICKY', 				8);
define('AUTH_MARROW',				9);
define('AUTH_POLLCREATE', 			10);
define('AUTH_VOTE', 				11);
define('AUTH_ATTACH', 				12);
define('AUTH_DOWNLOAD', 			13);

//投诉
define('REPORT_POST_NEW', 			1);
define('REPORT_POST_CLOSED', 		2);

/**
* 表
**/
define('CONFIRM_TABLE', 			$table_prefix.'confirm');
define('AUTH_ACCESS_TABLE', 		$table_prefix.'auth_access');
define('BANLIST_TABLE', 			$table_prefix.'banlist');
define('CATEGORIES_TABLE', 			$table_prefix.'categories');
define('CONFIG_TABLE', 				$table_prefix.'config');
define('DISALLOW_TABLE', 			$table_prefix.'disallow');
define('FORUMS_TABLE', 				$table_prefix.'forums');
define('GROUPS_TABLE', 				$table_prefix.'groups');
define('POSTS_TABLE', 				$table_prefix.'posts');
define('POSTS_TEXT_TABLE', 			$table_prefix.'posts_text');
define('PRIVMSGS_TABLE', 			$table_prefix.'privmsgs');
define('PRIVMSGS_TEXT_TABLE', 		$table_prefix.'privmsgs_text');
define('PRIVMSGS_IGNORE_TABLE', 	$table_prefix.'privmsgs_ignore');
define('PRUNE_TABLE', 				$table_prefix.'forum_prune');
define('RANKS_TABLE', 				$table_prefix.'ranks');
define('RULES_TABLE', 				$table_prefix.'rules');
define('RULES_CAT_TABLE', 			$table_prefix.'rules_cat');
define('SEARCH_TABLE', 				$table_prefix.'search_results');
define('SEARCH_WORD_TABLE', 		$table_prefix.'search_wordlist');
define('SEARCH_MATCH_TABLE', 		$table_prefix.'search_wordmatch');
define('SESSIONS_TABLE', 			$table_prefix.'sessions');
define('SESSIONS_KEYS_TABLE', 		$table_prefix.'sessions_keys');
define('SMILIES_TABLE', 			$table_prefix.'smilies');
define('TOPICS_TABLE', 				$table_prefix.'topics');
define('TOPICS_WATCH_TABLE', 		$table_prefix.'topics_watch');
define('USER_GROUP_TABLE', 			$table_prefix.'user_group');
define('USERS_TABLE', 				$table_prefix.'users');
define('WORDS_TABLE', 				$table_prefix.'words');
define('VOTE_DESC_TABLE', 			$table_prefix.'vote_desc');
define('VOTE_RESULTS_TABLE', 		$table_prefix.'vote_results');
define('VOTE_USERS_TABLE', 			$table_prefix.'vote_voters');
define('STYLES_TABLE', 				$table_prefix.'styles');
define('EXTENSION_GROUPS_TABLE', 	$table_prefix.'extension_groups');
define('EXTENSIONS_TABLE', 			$table_prefix.'extensions');
define('FORBIDDEN_EXTENSIONS_TABLE',$table_prefix.'forbidden_extensions');
define('ATTACHMENTS_DESC_TABLE', 	$table_prefix.'attachments_desc');
define('ATTACHMENTS_TABLE', 		$table_prefix.'attachments');
define('QUOTA_TABLE', 				$table_prefix.'attach_quota');
define('QUOTA_LIMITS_TABLE', 		$table_prefix.'quota_limits');
define('MODS_TABLE', 				$table_prefix.'mods');
define('MODULES_TABLE',				$table_prefix.'modules');
define('PAGES_TABLE', 				$table_prefix.'pages');
define('PAGE_MODULES_TABLE', 		$table_prefix.'page_modules');
define('CLASS_TABLE', 				$table_prefix.'class');
define('LINKS_TABLE', 				$table_prefix.'links');
define('LINKCLASS_TABLE',			$table_prefix.'linkclass');
define('GUESTBOOK_TABLE', 			$table_prefix.'guestbook');
define('FORUM_MODULE_TABLE', 		$table_prefix.'forum_module');
define('ARTICLES_CLASS_TABLE', 		$table_prefix.'articles_class');
define('ARTICLES_TABLE', 			$table_prefix.'articles');
define('ARTICLES_TEXT_TABLE', 		$table_prefix.'articles_text');
define('ARTICLES_REPLY_TABLE', 		$table_prefix.'articles_reply');
define('DOWNLOADS_TABLE', 			$table_prefix.'download');
define('FRIENDS_TABLE', 			$table_prefix.'friends');
define('PROFILE_GUESTBOOK_TABLE',	$table_prefix.'profile_guestbook');
define('UCP_MAIN_TABLE', 			$table_prefix.'ucp_main');
define('TOPIC_COLLECT_TABLE', 		$table_prefix.'topic_collect');

//附件系统
define('INLINE_LINK', 				1);
define('PHYSICAL_LINK', 			2);

define('NONE_CAT', 					0);
define('IMAGE_CAT', 				1);
define('STREAM_CAT', 				2);
define('SWF_CAT', 					3);

define('MEGABYTE', 					1024);
define('ADMIN_MAX_ATTACHMENTS', 	50); 
define('THUMB_DIR', 				'thumbs');
define('MODE_THUMBNAIL', 			1);
define('GPERM_ALL', 				0); 
define('QUOTA_UPLOAD_LIMIT', 		1);
define('QUOTA_PM_LIMIT', 			2);

//模块

define('MODULE_HEADER',				-1);// 全局顶部
define('MODULE_TOP',				-2);// 顶部
define('MODULE_BOTTOM',				-3);// 底部
define('MODULE_FOOTER',				-4);// 全局底部
define('MODULE_HEAD', 				-5);// head
define('MODULE_MAIN', 				0);

define('MODULE_COMMON', 			0);// 普通模块
define('MODULE_VIEWFORUM', 			1);// 子论坛

// phpbb_chmod() permissions
@define('CHMOD_ALL', 				7);
@define('CHMOD_READ', 				4);
@define('CHMOD_WRITE', 				2);
@define('CHMOD_EXECUTE', 			1);

define('QUICK_ANSWER_OFF', 			0);
define('QUICK_ANSWER_ON', 			1);
define('QUICK_ANSWER_USER', 		2);

define('PAGE_ALBUM', -19);

define('PERSONAL_GALLERY', 0);

define('ALBUM_ANONYMOUS', -1);
define('ALBUM_GUEST', -1);

define('ALBUM_USER', 0);
define('ALBUM_ADMIN', 1);
define('ALBUM_MOD', 2);
define('ALBUM_PRIVATE', 3);

define('ALBUM_UPLOAD_PATH', 'images/album/');
define('ALBUM_CACHE_PATH', 'images/album/cache/');

define('ALBUM_TABLE', $table_prefix.'album');
define('ALBUM_CAT_TABLE', $table_prefix.'album_cat');
define('ALBUM_CONFIG_TABLE', $table_prefix.'album_config');
define('ALBUM_COMMENT_TABLE', $table_prefix.'album_comment');
define('ALBUM_RATE_TABLE', $table_prefix.'album_rate');


?>