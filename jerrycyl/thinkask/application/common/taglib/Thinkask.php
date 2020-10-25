<?php


/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\common\Taglib;

use think\template\TagLib;
// use app\common\controller\Base;
/**
 * CX标签库解析类
 * @category   Think
 * @package  Think
 * @subpackage  Driver.Taglib
 * @author    liu21st <liu21st@gmail.com>
 */
class Thinkask extends Taglib{
		// 'doc'       => array('attr' => 'model,field,limit,id,field,key','level'=>3),
		// 'recom'     => array('attr' => 'doc_id,id'),
		// 'link'		=> array('attr' => 'type,limit' , 'close' => 1),//友情链接
		// 'prev'		=> array('attr' => 'id,cate' , 'close' => 1),//上一篇
		// 'next'		=> array('attr' => 'id,cate' , 'close' => 1),//下一篇
	// 标签定义
	protected $tags   =  array(
		// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
		"adv"		=> array('attr' => 'tagname', 'close' => 0), //获取导航
		'nav'       => array('attr' => 'catid', 'close' => 1), //获取导航
		'question'  => array('attr' => 'order,limit,field', 'close' => 1), //问题列表
		'users'     => array('attr' => 'order,limit,field', 'close' => 1), //用户列表
		'tags'      => array('attr' => 'order,limit,field', 'close' => 1),  //获取标签
		'article'   => array('attr' => 'order,limit,field', 'close' => 1),  //获取文章
		'getdb'  	=> array('attr' => 'order,limit,field,table', 'close' => 1),  //公共获取内容
		// 'ques'  	=> array('attr' => 'order,limit', 'close' => 1), //问题列表
	);
	//导航
	/** {nav catid="1"}
          <li class="<?php if($controller==$v['controller']&&$action==$v['action']&&$module==$v['module']){ echo "active";} ?>"><a target="{$v.target}" href="{$v.url}"><i class="icon icon-index"></i> {$v.title}</a></li>
           {/nav}
     */
	public function tagnav($tag, $content){
		$catid =  empty($tag['catid'])?'1':$tag['catid'];
		$parse  = '<?php ';
		$parse .= '$data = model("base")->getdb("nv_index")->where(["catid"=>'.$catid.',"status"=>1])->order("sort desc,id desc")->select();';
		$parse .= 'foreach ($data as &$v) {';
		$parse .= 'if(!$v["url"]||$v["url"]==""){ $v["url"] = turl($v["m"]."/".$v["c"]."/".$v["a"]); }else{ $v["url"] = turl($v["url"]); }';
		$parse .= '?>';
		$parse .= $content;
		$parse .= '<?php } ?>';
		return $parse;
	}
	/**{adv tagname='index_top_right' /}
	 * [tagadv 广告列表]
	 * @Author   Jerry
	 * @DateTime 2017-05-04
	 * @Example  eg:
	 * @param    [type]     $tag [description]
	 * @return   [type]          [description]
	 */
	public function tagadv($tag){
		$quotation_mark = "'"; 
		$tagname = $tag['tagname'];
		$type = $tag['type']?$tag['type']:'img';
		$parse  = $parse   = '<?php ';
		$parse .= '$adv = model("base")->getdb("adv")->where(["tagname"=>"'.$tagname.'"])->find();';
		$parse .= '$type='.$type.';if($type=="img"){ echo "<img style='.$quotation_mark.'width:100%;border-radius: 2%'.$quotation_mark.' src='.$quotation_mark.'".get_file_path($adv["'.$type.'"])."'.$quotation_mark.' />";}else{ echo $adv["'.$type.'"];}';
		$parse .= ' ?>';
		return $parse;
	}
	/**{question order="add_time desc"}
                      <li class="li-box-list"><a href="question/detail/{:encode($v['question_id'])}.html" title="{$v.question_content}">{$v.question_content}</a></li>
                    {/question}
	 * [tagques 问答列表]
	 * @Author   Jerry
	 * @DateTime 2017-05-04
	 * @Example  eg:
	 * @param    [type]     $tag [description]
	 * @return   [type]          [description]
	 */
	public function tagquestion($tag, $content){
		$order =  empty($tag['order'])?'add_time desc ':$tag['order'];
		$limit =  empty($tag['limit'])?'10':$tag['limit'];
		$field =  empty($tag['field'])?'answer_count,view_count,question_id,question_content':$tag['field'];
		$parse  = '<?php ';
		$parse .= '$data = model("base")->getdb("question")->order("'.$order.'")->limit('.$limit.')->field("'.$field.'")->select(); ';
		$parse .= 'foreach ($data as $key => $v) {';
		$parse .= '?>';
		$parse .= $content;
		$parse .= '<?php } ?>';
		return $parse;
	}
	/**{users order="uid desc"}
                       <li class="user-list">
                         <a href="/people/{:encode($v['uid'])}.html" title="{$v.user_name}" class="avatar"><img src="{:get_file_path($v['avatar_file'])}"alt="{$v.user_name}" /></a>
                         <div class="user-content">
                          <h4><a href="" title="{$v.user_name}">{$v.user_name}</a></h4>
                          <p> {:date_friendly($v['reg_time'])} </p>
                         </div>
                       
                       </li>
                    {/users}
	 * [tagquestion 用户列表]
	 * @Author   Jerry
	 * @DateTime 2017-05-04
	 * @Example  eg:
	 * @param    [type]     $tag     [description]
	 * @param    [type]     $content [description]
	 * @return   [type]              [description]
	 */
	public function tagusers($tag, $content){
		$order =  empty($tag['order'])?'add_time desc ':$tag['order'];
		$limit =  empty($tag['limit'])?'10':$tag['limit'];
		$field =  empty($tag['field'])?'uid,avatar_file,user_name,reg_time':$tag['field'];
		$parse  = '<?php ';
		$parse .= '$data = model("base")->getdb("users")->order("'.$order.'")->limit('.$limit.')->field("'.$field.'")->select(); ';
		$parse .= 'foreach ($data as $key => $v) {';
		$parse .= '?>';
		$parse .= $content;
		$parse .= '<?php } ?>';
		return $parse;
	}

	/** {tags order="topic_id desc"}
                      <li><a href="javascript:;">{$v.topic_title}</a></li>
                    {/tags}
	 * [tagquestion 标签列表]
	 * @Author   Jerry
	 * @DateTime 2017-05-04
	 * @Example  eg:
	 * @param    [type]     $tag     [description]
	 * @param    [type]     $content [description]
	 * @return   [type]              [description]
	 */
	public function tagtags($tag, $content){
		$order =  empty($tag['order'])?'add_time desc ':$tag['order'];
		$limit =  empty($tag['limit'])?'30':$tag['limit'];
		$field =  empty($tag['field'])?'topic_id,topic_title,add_time':$tag['field'];
		$parse  = '<?php ';
		$parse .= '$data = model("base")->getdb("topic")->order("'.$order.'")->limit('.$limit.')->field("'.$field.'")->select(); ';
		$parse .= 'foreach ($data as $key => $v) {';
		$parse .= '?>';
		$parse .= $content;
		$parse .= '<?php } ?>';
		return $parse;
	}
	/**
	 * [tagarticle 获得文章]
	 * @Author   Jerry
	 * @DateTime 2017-05-04T11:59:33+0800
	 * @Example  eg:
	 * @return   [type]                   [description]
	 */
	public function tagarticle($tag, $content){
		$order =  empty($tag['order'])?'add_time desc ':$tag['order'];
		$limit =  empty($tag['limit'])?'30':$tag['limit'];
		$field =  empty($tag['field'])?'id,title,add_time':$tag['field'];
		$parse  = '<?php ';
		$parse .= '$data = model("base")->getdb("article")->order("'.$order.'")->limit('.$limit.')->field("'.$field.'")->select(); ';
		$parse .= 'foreach ($data as $key => $v) {';
		$parse .= '?>';
		$parse .= $content;
		$parse .= '<?php } ?>';
		return $parse;
	}
	/**
	 * [taggetdb 公共获取内容]
	 * @Author   Jerry
	 * @DateTime 2017-05-04T12:01:50+0800
	 * @Example  eg:
	 * @param    [type]                   $tag     [description]
	 * @param    [type]                   $content [description]
	 * @return   [type]                            [description]
	 */
	public function taggetdb($tag, $content){
		if($tab['table']) die('table为必填');
		$order =  empty($tag['order'])?'add_time desc ':$tag['order'];
		$limit =  empty($tag['limit'])?'30':$tag['limit'];
		$field =  empty($tag['field'])?'*':$tag['field'];
		$table =  $tag['table'];
		$parse  = '<?php ';
		$parse .= '$data = model("base")->getdb("'.$table.'")->order("'.$order.'")->limit('.$limit.')->field("'.$field.'")->select(); ';
		$parse .= 'foreach ($data as $key => $v) {';
		$parse .= '?>';
		$parse .= $content;
		$parse .= '<?php } ?>';
		return $parse;
	}
	


}