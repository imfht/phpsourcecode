<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Admin\Model;
use Think\Model;
class BlogReplyModel extends Model{
	
	
	public function show_blog_reply_page(){
		
		$sql='SELECT br.*,b.title as blog_title FROM '.C('DB_PREFIX').'blog_reply br,'.C('DB_PREFIX').'blog b where b.blog_id=br.blog_id';
		
		$count=count(M()->query($sql));
		
		$Page = new \Think\Page($count,C('BACK_PAGE_NUM'));
		
		$show  = $Page->show();// 分页显示输出	
		
		$sql.=' order by b.blog_id desc LIMIT '.$Page->firstRow.','.$Page->listRows;
		
		$list=M()->query($sql);


		
		return array(
			'empty'=>'<tr><td colspan="20">~~暂无数据</td></tr>',
			'list'=>$list,
			'page'=>$show
		);	
		
	}	
	
}
?>