<?php
/**
* @package phpBB-WAP
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

if ( !defined('IN_PHPBB') ) exit;

/**
*	phpBB-WAP 模块BBcode类
*	作者：Crazy
*	这个类有点糟糕，不过这个类估计没人去研究
*/

class Module_bbcode 
{
	var $bbcodes = array();
	var $text = '';
	var $total = array();
	var $reg_bbcodes = array();
	var $mods_mbb = array();
	var $bbcode_exp = array();
	
	function __construct()
	{
		/**
		*	[统计会员]、[统计主题]、[统计帖子]、[统计附件]、[新会员]、[新ID]
		*	这些BBCode默认是没有开启的
		*	如需要开启请把39行到59行的 // 去掉
		*/

		//$this->total = array(
		//	'user' => get_db_stat('usercount'),
		//	'topic' => get_db_stat('topiccount'),
		//	'post' => get_db_stat('postcount'),
		//	'attach' => get_db_stat('attachcount')
		//);
		
		$newest_userdata = get_db_stat('newestuser');
		
		$this->load_main();

		$this->bbcodes = array(
			// 统计的 BBcode 默认是没有开启的
			// 如需要开启请把下面的 // 去掉
			//'[统计会员]'	=> $this->total['user'],
			//'[统计主题]'	=> $this->total['topic'],
			//'[统计帖子]'	=> $this->total['topic'],
			//'[统计附件]'	=> $this->total['attach'],
			'[新会员]'		=> $newest_userdata['username'],
			'[新ID]'		=> $newest_userdata['user_id'],
			'[br]'			=> '<br />',
			'[n]'			=> '&nbsp;',
			'[LOGO]'		=> ROOT_PATH . 'images/' . $this->_config['site_logo'],
			'[当前时间]'	=> create_date('G:i', time(), $this->_config['board_timezone']),
			'[年]'			=> create_date('Y', time(), $this->_config['board_timezone']),
			'[月]'			=> create_date('n', time(), $this->_config['board_timezone']),
			'[日]'			=> create_date('j', time(), $this->_config['board_timezone']),
			'[时]'			=> create_date('G', time(), $this->_config['board_timezone']),
			'[分]'			=> create_date('i', time(), $this->_config['board_timezone']),
			'[秒]'			=> create_date('s', time(), $this->_config['board_timezone']),
			'[星期]'		=> $this->bbcode_week(),
			'[问候语]'		=> $this->bbcode_hello(),
			'[备案信息]'	=> $this->_config['beian_info'],
			'[用户名]'		=> $this->_userdata['username'],
			'[用户ID]'		=> $this->_userdata['user_id'],
			'[未读信息]'	=> $this->_userdata['user_unread_privmsg'],
			'[新信息]'		=> $this->_userdata['user_new_privmsg'],
			'[邮箱]'		=> $this->_userdata['user_email'],
			'[金币]'		=> $this->_userdata['user_points'],
			'[网页标题]'	=> $this->page_title,
			'[注册时间]'	=> create_date('Y-m-d H:i', time(), $this->_config['board_timezone']),
			'[上级页面]'	=> append_sid(ROOT_PATH . 'index.php?page=' . $this->page_ago)
		);
	
		$this->replace_bbcodes = array(
			'#\[MOD=([0-9a-z\-_]{1,15})\]#is' => append_sid(ROOT_PATH . 'loading.php?mod=$1')
		);
		
		$this->callback_bbcodes = array(
			'low' => array(
				'#\[我的地盘_(-1|[1-9][0-9]{0,7})\]#' => array(&$this, 'bbcode_ucp_link'),
				'#\[调用相册_([1-9]|[1-2]\d|5[0])_([1-9][0-9]{0,5})_([1-9][0-9]{0,5})_(-1|0|[1-9]|[1-2]\d|5[0])\]#' => array(&$this, 'bbcode_use_album'),
				'#\[调用友链_(0|[1-9][0-9]{0,7}(\,[1-9][0-9]{0,7})*?)_([0|1])_([1|2|3])_([1-9]|[1-2]\d|5[0])\]#' => array(&$this, 'bbcode_use_links'),
				'#\[调用文章_(0|[1-9][0-9]{0,7}(\,[1-9][0-9]{0,7})*?)_([0|1])_([1|2|3])_([1|2])_([1-9]|[1-2]\d|5[0])\]#' => array(&$this, 'bbcode_use_articles'),
				'#\[调用帖子_(0|[1-9][0-9]{0,7}(\,[1-9][0-9]{0,7})*?)_([0|1])_([1|2|3|4])_([1-7])_([1-9]|[1-2]\d|5[0])\]#' => array(&$this, 'bbcode_use_topic')
			),
			'medium' => array(),
			'high' => array(
				'#\[html\](.*?)\[/html\]#s' => array(&$this, 'bbcode_html')
			),
		);

		$this->load_bbcodes();
	}

	function parser_mbb($text)
	{
		$this->text = $text;
		$this->text = ' ' . $this->text;
		if ( !(strpos($this->text, '[') && strpos($this->text, ']')) ) return substr($this->text, 1);
		$this->bbcode_replace();
		return $this->text;
	}
	
	function bbcode_replace()
	{
		foreach ($this->bbcodes as $str_find_tag => $str_replace_tag ) $this->text = str_replace($str_find_tag, $str_replace_tag, $this->text);

		foreach ($this->replace_bbcodes as $key => $value) $this->text = preg_replace($key, $value, $this->text);

		foreach ($this->callback_bbcodes['low'] as $key => $value) $this->text = preg_replace_callback($key, $value, $this->text);

		foreach ($this->callback_bbcodes['medium'] as $key => $value) $this->text = preg_replace_callback($key, $value, $this->text);

		foreach ($this->callback_bbcodes['high'] as $key => $value) $this->text = preg_replace_callback($key, $value, $this->text);
	}

	function reg_bbcodes($type, $key, $value, $level = 'low')
	{
		switch ($type)
		{
			case 'str':
				$this->bbcodes = array_merge($this->bbcodes, array($key => $value));
				break;

			case 'preg':
				$this->replace_bbcodes = array_merge($this->replace_bbcodes, array($key => $value));
				break;

			case 'call':
				$this->callback_bbcodes = array_merge_recursive($this->callback_bbcodes, array($level => array($key => $value)));
				break;
		}
	}

	function reg_bbcode_exp($bbcode_tag, $use_method)
	{
		$this->bbcode_exp = array_merge($this->bbcode_exp, array($bbcode_tag => $use_method));
	}

	function load_main()
	{
		$sql = 'SELECT mod_dir 
			FROM ' . MODS_TABLE . '
			WHERE mod_power = 1';

		if (!$result = $this->_db->sql_query($sql))
		{
			trigger_error('无法取得MODS的信息', E_USER_WARNING);
		}

		while ($row = $this->_db->sql_fetchrow($result))
		{
			$class_name = $row['mod_dir'] . '_module_bbcode';
			$mods = $row['mod_dir'];
			//echo ROOT_PATH . "mods/$mods/class/$class_name.php";
			if (file_exists(ROOT_PATH . "mods/$mods/class/module_bbcode.php"))
			{
				if (!class_exists($class_name))
				{
					require ROOT_PATH . "mods/$mods/class/module_bbcode.php";

					$this->mods_mbb[$class_name] = new $class_name($this);

					$this->mods_mbb[$class_name]->main();
				}
			}
		}
	}

	function load_bbcodes()
	{
		foreach ($this->mods_mbb as $key => $value) $this->mods_mbb[$key]->bbcodes();
	}

	function bbcode_html($html)
	{
		return htmlspecialchars_decode($html[1], ENT_QUOTES);
	}

	function bbcode_use_topic($topic)
	{
		global $db;
		
		$module_text = '';
		$while_br = ($topic[3]) ? '<br />' : '';
		$forum_sql = ($topic[1] == 0) ? '' : ' WHERE forum_id IN(' . $topic[1] . ') ';
		$query_str = ($topic[1] == 0) ? ' WHERE ' : ' AND ';

		switch ($topic[5])
		{
			// 新帖
			case 1:
				$sql = 'SELECT topic_id, topic_title
					FROM ' . TOPICS_TABLE . '
					' . $forum_sql . '
					ORDER BY topic_id DESC
					LIMIT 0, ' . $topic[6];
				break;
			
			// 显示最近一个星期的热门帖子
			case 2:
				$sql = 'SELECT topic_id, topic_title 
					FROM ' . TOPICS_TABLE . "
						$forum_sql
						$query_str topic_time >= " . (time() - 604800) . '
					ORDER BY topic_views DESC
					LIMIT 0, ' . $topic[6];
				break;
			
			// 随机数据
			case 3:
				$sql = 'SELECT topic_id, topic_title 
					FROM ' . TOPICS_TABLE . "
					$forum_sql
					$query_str topic_id >= (SELECT ROUND(RAND() * (SELECT MAX(topic_id) FROM " . TOPICS_TABLE . ')))
					LIMIT 0, ' . $topic[6];
				break;
			
			// 按回复数量
			case 4:
				$sql = 'SELECT topic_id, topic_title 
					FROM ' . TOPICS_TABLE . '
					' . $forum_sql . '
					ORDER BY topic_replies DESC
					LIMIT 0 , ' . $topic[6];
				break;
			
			// 动态
			case 5:
				$forum_sql = ($topic[1] == 0) ? '' : ' WHERE t.forum_id IN(' . $topic[1] . ') ';
				
				$sql = 'SELECT t.topic_id, t.topic_title
					FROM ' . TOPICS_TABLE . ' AS t, ' . POSTS_TABLE . " AS p
					$forum_sql
					$query_str p.post_id = t.topic_last_post_id
					ORDER BY p.post_id DESC 
					LIMIT 0, " . $topic[6];
				break;

			// 精华帖子
			case 6:
				$sql = 'SELECT topic_id, topic_title
					FROM ' . TOPICS_TABLE . " 
					$forum_sql
					$query_str topic_marrow = " . POST_MARROW . '
					ORDER BY topic_id DESC
					LIMIT 0, ' . $topic[6];
				break;

			// 专题帖子
			case 7:
				$sql = 'SELECT topic_id, topic_title
					FROM ' . TOPICS_TABLE . "
					$forum_sql
					$query_str topic_class <> " . TOPIC_UNCLASS . '
					ORDER BY topic_id DESC
					LIMIT 0, ' . $topic[6];
					break;
			
			default:
				return $topic[0];
		}
		
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('无法查询帖子帖子数据', E_USER_WARNING);
		}
		
		switch ($topic[4])
		{
			// 只显示标题
			case 1:
				while( $row = $db->sql_fetchrow($result) )
				{
					$module_text .= $row['topic_title'] . $while_br;
				}
				break;
			
			// 显示超链接
			case 2:
				while( $row = $db->sql_fetchrow($result) )
				{
					$module_text .= '<a href="' . append_sid(ROOT_PATH . 'viewtopic.php?t=' . $row['topic_id']) . '">' . $row['topic_title'] . '</a>' . $while_br;
				}
				break;
			
			// 显示带标题号的帖子名称
			case 3:
				$i = 1;
				while( $row = $db->sql_fetchrow($result) )
				{
					$module_text .= $i . '、' . $row['topic_title'] . $while_br;
					$i++;
				}
				break;

			// 显示带标题号的帖子超链接
			case 4:
				$i = 1;
				while( $row = $db->sql_fetchrow($result) )
				{
					$module_text .= $i . '、<a href="' . append_sid(ROOT_PATH . 'viewtopic.php?t=' . $row['topic_id']) . '">' . $row['topic_title'] . '</a>' . $while_br;
					$i++;
				}
				break;
			// 虽然这是不会匹配到的...
			default:
				return $topic[0];
		}
		
		return $module_text;
	}

	function bbcode_ucp_link($user_id){
		if ($user_id[1] == -1) return append_sid(ROOT_PATH . 'index.php');
		else if ($user_id[1] == 0) return append_sid(ROOT_PATH . 'ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $this->_userdata['user_id']);
		else return append_sid(ROOT_PATH . 'ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $user_id[1]);
	}
	
	function bbcode_week() {
		$week_data = create_date('N', time(), $this->_config['board_timezone']);
		switch ($week_data) {
			case 1: return '一'; break;
			case 2: return '二'; break;
			case 3: return '三'; break;
			case 4: return '四'; break;
			case 5: return '五'; break;
			case 6: return '六'; break;
			case 7: return '日'; break;
			default: return'';
		}
		return $week_text;
	}
	
	function bbcode_hello() {
		$hello_time = create_date('H', time(), $this->_config['board_timezone']);
		if ( $hello_time < 5) $hello_text = '凌晨';
		elseif ( $hello_time < 9) $hello_text = '早上';
		elseif ( $hello_time < 14) $hello_text = '中午';
		elseif ( $hello_time < 18) $hello_text = '下午';
		else $hello_text = '晚上';
		return $hello_text;
	}

	function bbcode_use_album($param)
	{
		$per = $param[1];
		$width = $param[2];
		$height = $param[3];
		$length = $param[4];

		$sql = 'SELECT pic_id, pic_title FROM ' . ALBUM_TABLE  .' 
			WHERE pic_approval = 1 
			ORDER BY pic_time DESC
			LIMIT ' . $per;
		if (!$result = $this->_db->sql_query($sql)) trigger_error('无法查询', E_USER_WARNING);
		
		$text = '';

		while ($row = $this->_db->sql_fetchrow($result))
		{
			$albumrow[] = $row;
		}

		if (count($albumrow) == true)
		{
			$text .= '<div class="index-album">';
			$text .= '<table>';
			$text .= '<tr>';
			foreach ($albumrow as $key => $value)
			{	
				$text .= '<td><a href="' . append_sid(ROOT_PATH . 'album.php?action=page&pic_id=' . $value['pic_id']) . '" title="' . $value['pic_title'] .'"><img src="' . append_sid(ROOT_PATH . 'album.php?action=pic&pic_id=' . $value['pic_id']) . '" width="' . $width . '" height="' . $height . '" alt="' . $value['pic_title'] . '" /></a></td>';	
			}
			$text .= '</tr>';
			$text .= '</tr>';
			foreach ($albumrow as $key => $value)
			{
				if ($length == -1)
				{
					$text .= '<th>' . $value['pic_title'] . '</th>';
				}
				else if($length == 0)
				{
					$text .= '';
				}
				else
				{
					if ($length >= mb_strlen($value['pic_title'], 'UTF-8'))
					{
						$text .= '<th>' . $value['pic_title'] . '</th>';
					}
					else
					{
						$text .= '<th>' . mb_substr($value['pic_title'], 0, $length, 'UTF-8') . '</th>';
					}
				}
				
			}
			$text .= '</tr>';
			$text .= '</table>';
			$text .= '</div>';		
		}

		return $text;
	}

	function bbcode_use_links($param)
	{
		$module_text = '';

		$cat_id = $param[1];// 友链的分类ID，如果调用多个分类用英文逗号(,)分割，0表示所有分类
		$br 	= $param[3];// 之后是否添加换行符，0：否，1：是
		$sort 	= $param[4];// 友链的排序方式，1、最新链入，2、链入数最多，3、最新加入的
		$limit 	= $param[5];// 显示条数，返回条数，最多可设置50条

		$br_str 	= ($br) ? '<br />' : '';

		$where_sql 	= ($class_id == 0) ? '' : ' WHERE link_class_id IN(' . $class_id . ') ';
		$query_str	= ($class_id == 0) ? ' WHERE ' : ' AND ';

		switch ($sort)
		{
			// 最新
			case 1:
				$sql = "SELECT link_id, link_name
					FROM " . LINKS_TABLE . "
					$where_sql
						$query_str link_show = 1
					ORDER BY link_last_visit DESC
					LIMIT $limit";
				break;
			// 链入最多
			case 2:
				$sql = "SELECT link_id, link_name
					FROM " . LINKS_TABLE . "
					$where_sql
						$query_str link_show = 1
					ORDER BY link_in DESC
					LIMIT $limit";
				break;
			case 3:
				$sql = "SELECT link_id, link_name
					FROM " . LINKS_TABLE . "
					$where_sql
						$query_str link_show = 1
					ORDER BY link_join_time DESC
					LIMIT $limit";
				break;
			default:
				return $param[0];
				break;
		}

		if ( !($result = $this->_db->sql_query($sql)) )
		{
			trigger_error('无法查询友链数据', E_USER_WARNING);
		}

		while( $row = $this->_db->sql_fetchrow($result) )
		{
			$module_text .= '<a href="' . append_sid(ROOT_PATH . 'links.php?mode=view&id=' . $row['link_id']) . '">' . $row['link_name'] . '</a>' . $br_str;
		}

		return $module_text;

	}

	function bbcode_use_articles($param)
	{
		
		$module_text = '';

		$class_id 	= $param[1];// 文章的分类ID，如果调用多个分类用英文逗号(,)分割，0表示所有分类
		$br 		= $param[3];// 之后是否添加换行符，0：否，1：是
		$style		= $param[4];// 显示方式，1、正常调用，2、在每篇文章的前面添加文章分类(分类可点击)，3、在每篇文章的前面添加文章分类(分类不可点击)
		$type		= $param[5];// 显示类型，1、最新发表的文章，2、本周热门文章
		$limit 		= $param[6];// 显示条数，返回条数，最多可设置50条

		$br_str 	= ($br) ? '<br />' : '';
		$where_sql 	= ($class_id == 0) ? '' : ' WHERE a.article_class IN(' . $class_id . ') ';
		$query_str	= ($class_id == 0) ? ' WHERE ' : ' AND ';

		switch ($type)
		{
			case 1:
				$sql = "SELECT a.article_id, a.article_title, ac.ac_id, ac.ac_name
					FROM " . ARTICLES_TABLE . " a, " . ARTICLES_CLASS_TABLE . " ac
					$where_sql 
					$query_str a.article_class = ac.ac_id
						AND a.article_approval = 1
					ORDER BY a.article_time DESC";
				break;
			
			case 2:
				$sql = "SELECT a.article_id, a.article_title, ac.ac_id, a.ac_name
					FROM " . ARTICLES_TABLE . " a, " . ARTICLES_CLASS_TABLE . " ac
					$where_sql
						$query_str a.article_class = ac.ac_id 
						AND a.article_time >= " . (time() - 2592000) . "
						AND a.article_approval = 1
					ORDER BY a.article_time DESC";
					
				break;
			default:
				return $param[0];
				break;
		}

		if ( !($result = $this->_db->sql_query($sql)) )
		{
			trigger_error('无法查询文章数据', E_USER_WARNING);
		}

		switch ($style)
		{
			case 1:
				while( $row = $this->_db->sql_fetchrow($result) )
				{
					$module_text .= '<a href="' . append_sid(ROOT_PATH . 'article.php?mode=view&id=' . $row['article_id']) . '">' . $row['article_title'] . '</a>' . $br_str;
				}
				break;
			case 2:
				while( $row = $this->_db->sql_fetchrow($result) )
				{
					$module_text .= '[<a href="' . append_sid('article.php?mode=cat&id=' . $row['ac_id']) . '">' . $row['ac_name'] . '</a>]<a href="' . append_sid(ROOT_PATH . 'article.php?mode=view&id=' . $row['article_id']) . '">' . $row['article_title'] . '</a>' . $br_str;
				}
				break;
			case 3:
				while( $row = $this->_db->sql_fetchrow($result) )
				{
					$module_text .= '<a href="' . append_sid(ROOT_PATH . 'article.php?mode=view&id=' . $row['article_id']) . '">[' . $row['ac_name'] . ']' . $row['article_title'] . '</a>' . $br_str;
				}
				break;
			default:
				return $param[0];
				break;
		}

		return $module_text;

	}

}

?>