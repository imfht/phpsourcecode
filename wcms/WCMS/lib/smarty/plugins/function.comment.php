
<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {counter} function plugin
 *
 * Type: function<br>
 * Name: counter<br>
 * Purpose: print out a counter value
 *
 * @author Monte Ohrt <monte at ohrt dot com>
 * @link http://www.smarty.net/manual/en/language.function.counter.php {counter}
 * (Smarty online manual)
 * @param array $params
 * parameters
 * @param Smarty_Internal_Template $template
 * template object
 * @return string null
 */
function smarty_function_comment($params, $template) {
	
	// 调用留言列表  只显示审核过的评论
	if (isset ( $params ['nid'] )) {
		$limitNum = isset ( $params ['num'] ) ? $params ['num'] : 10;
		$rs = CommentModel::instance ()->getComment ( array ('nid' => $params ['nid'] ), $limitNum );
	} else {
		$limitNum = isset ( $params ['num'] ) ? $params ['num'] : 10;
		
		$rs = CommentModel::instance ()->getComment ( array ('status' => 1 ), $limitNum );
	
	}
	//如果有uid那么获取用户信息
	foreach ( $rs as $k => $v ) {
		if ($v ['uid'] == 0)
			continue;
		$rs [$k] ['reply'] = CommentModel::instance ()->getReply ( $v ['id'],5 );
		$rs [$k] ['replynum'] = count ( $rs [$k] ['reply'] );
		$member = MemberModel::instance ()->getOneMember ( array ('uid' => $v ['uid'] ) );
		$rs [$k] ['real_name'] = $member ['real_name'];
		$rs [$k] ['username'] = $member ['username'];
		$rs [$k] ['face'] = $member ['face'];
		$rs [$k] ['level'] = $member ['level'];
		$rs [$k] ['city'] = $member ['area'];
	}
	$template->assign ( $params ['assign'], $rs );

}

?>