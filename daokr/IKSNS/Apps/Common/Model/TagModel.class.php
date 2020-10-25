<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
* @Email:810578553@qq.com
* @小麦 修改时间2014年3月16日晚 03:30 全站标签管理
*/
namespace Common\Model;
use Think\Model;

class TagModel extends Model
{
	// 通过topic获取tag
	function getObjTagByObjid($objname,$idname,$objid){
		$where = array($idname=>$objid);
		$arrTagIndex = M('tag_'.$objname.'_index')->where($where)->select();	
		if(is_array($arrTagIndex)){
			foreach($arrTagIndex as $item){
				$arrTag[] = $this->getOneTag($item['tagid']);
			}
		}
		return $arrTag;
	}
	//通过tagid获得tagname
	function getOneTag($tagid){
		$tagData = $this->where(array('tagid'=>$tagid))->find();	
		return $tagData;
	}
	//通过tagid获得tagname
	function getOneTagByName($tagname){
		$tagData = $this->where(array('tagname'=>$tagname))->find();
		return $tagData;
	}
	//添加多个标签
	function addTag($objname='',$idname='',$objid='',$tags){
	
		//前台添加
		if($objname != '' && $idname != '' && $objid!='' && $tags!=''){
			//$tags = str_replace ( '，', ',', $tags );
			$tag = preg_replace('/\s+/', ',',  $tags );//修正用空格 分割 tag标签
			$arrTag = explode(',', $tag);
			foreach($arrTag as $item){
				$tagname = clearText($item);
				if(strlen($tagname) < '32' && $tagname != ''){
					$uptime = time();
					$tagcount = $this->where(array('tagname'=>$tagname))->count();
					if($tagcount == 0){
						if (false !== $this->create ( array('tagname'=>$tagname,'uptime'=>$uptime) )){
							$tagid = $this->add();
							$tagIndexCount = M('tag_'.$objname.'_index')->where(array($idname=>$objid, 'tagid'=> $tagid))->count();
							if ($tagIndexCount==0){
								M('tag_'.$objname.'_index')->create(array($idname=>$objid,'tagid'=>$tagid));
								M('tag_'.$objname.'_index')->add();
							}
							$tagIdCount = M('tag_'.$objname.'_index')->where(array('tagid'=>$tagid))->count();
							$this->where(array('tagid'=>$tagid))->setField(array('count_'.$objname=>$tagIdCount, 'uptime'=>$uptime));							
						}
						
					}else{
						
						$tagData = $this->where(array('tagname'=>$tagname))->find();
						$tagIndexCount = M('tag_'.$objname.'_index')->where(array($idname=>$objid, 'tagid'=> $tagData['tagid']))->count();
						if ($tagIndexCount==0){
							M('tag_'.$objname.'_index')->create(array($idname => $objid,'tagid'=> $tagData['tagid']));
							M('tag_'.$objname.'_index')->add();
						}
						$tagIdCount = M('tag_'.$objname.'_index')->where(array('tagid'=>$tagData['tagid']))->count();
						$this->where(array('tagid'=>$tagData['tagid']))->setField(array('count_'.$objname=>$tagIdCount, 'uptime'=>$uptime));
					}					
				}
			}
			
		}elseif($tags!=''){ 
			//后台批量添加tag
			$arrTag =  explode("\n", $tags);
			foreach($arrTag as $item){
				$tagname = clearText($item);
				if(strlen($tagname) < '32' && $tagname != ''){
					$uptime = time();
					$tagcount = $this->where(array('tagname'=>$tagname))->count();
					if($tagcount == 0){
						if (false !== $this->create ( array('tagname'=>$tagname,'uptime'=>$uptime) )){
							$tagid = $this->add();							
						}
					}
				}
			}
		}
	}
	//通过指定索引删除tag 如：topic_index
	function delTagById($tagid)
	{
		$where['tagid'] = array('exp',' IN ('.$tagid.') ');
		//先删除tag
		$this->where($where)->delete();
		//删除索引 tag_obj_index 表 暂时只支持删除 小组和帖子的tag标签 以后再优化
		M('tag_group_index')->where($where)->delete();
		M('tag_topic_index')->where($where)->delete();
		return true;
	}
	//根据标题分词  中文分词
	public function get_tags_by_title($title, $num=10)
	{
		vendor('pscws4.pscws4', '', '.class.php');
		$pscws = new PSCWS4();
		$pscws->set_dict(IKPHP_DATA . 'scws/dict.utf8.xdb');
		$pscws->set_rule(IKPHP_DATA . 'scws/rules.utf8.ini');
		$pscws->set_ignore(true);
		$pscws->send_text($title);
		$words = $pscws->get_tops($num);
		$pscws->close();
		$tags = array();
		foreach ($words as $val) {
			$tags[] = $val['word'];
		}
		return $tags;
	}
	// 2013-9-11 新增 指定索引表 删除 指定id tag
	public function delObjTagByObjid($objname,$idname,$objid)
	{
		$arrTagIndex = M('tag_'.$objname.'_index')->where(array($idname=>$objid))->select();
		if(is_array($arrTagIndex))
		{
			foreach($arrTagIndex as $item){
				$this->where(array('tagid'=>$item['tagid']))->delete();
			}
			//删除索引 tag_obj_index 表
			M('tag_'.$objname.'_index')->where(array($idname=>$item[$idname]))->delete();
		}
	}
	
}