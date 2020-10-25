<?php

/**
 * 检测输入的验证码是否正确，$code为用户输入的验证码字符串
 * @param  [type]  $code  [description]
 * @param  boolean $reset 是否重置验证码
 * @param  string  $id    [description]
 * @return [type]         [description]
 * @author jlb <[<email address>]>
 */
function check_verify($code, $reset = true, $id = ''){
    $verify = new \Think\Verify(array('reset'=>$reset));
    return $verify->check($code, $id);
}
/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count int 要分页的总记录数
 * @param $pagesize int 每页查询条数
 * @return \Think\Page
 * @author Gison
 * @since  2016年09月02日
 */
function getPage($count, $pagesize = 20, $para = array())
{
    $p = new Think\Page($count, $pagesize, $para);
    $p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false;//最后一页不显示为总页数
    return $p;
}

/**
 * 加密函数
 *
 * @param $str string 要加密的字符串
 * @return string 加密后的字符串
 * @author Gison
 * @since  2016年12月07日
 */
function encrypt($str)
{
    return md5(C('AUTH_CODE') . $str);
}

/**
 * 生成箱子或优惠卷编码
 *
 * @param null
 * @return string 加密后的字符串
 * @author Gison
 * @since  2016年12月07日
 */
function get_encode()
{
    return md5(uniqid());
}

/**
 * 给菜单名生成树状结构
 * @param  [type] $arrTree 菜单数组
 * @param  [type] $step	   层次深度
 * @param  [type] $repeatStr 层次标识字符
 * @return array
 * @author jlb         
 */
function menuArrayTree($arrTree,$step=0,$repeatStr='---- ')
{
	static $trList = array();
	foreach ($arrTree as $v) 
	{
		$v['name'] = str_repeat($repeatStr, $step) . $v['name'];
		$trList[] = $v;
		if ( !empty($v['son']) ) 
		{
			menuArrayTree($v['son'], $step + 1);
		}
	}
	return $trList;
}
