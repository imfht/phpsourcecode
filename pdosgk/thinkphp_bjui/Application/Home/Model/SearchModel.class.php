<?php

namespace Home\Model;

use Think\Model;
/**
 * 仿phpcms中的搜索模块
 * @author Lain
 *
 */
class SearchModel extends Model {
	
	/**
	 * 添加到全站搜索、修改已有内容
	 * @param $typeid
	 * @param $id
	 * @param $data
	 * @param $text 不分词的文本
	 * @param $adddate 添加时间
	 * @param $iscreateindex 是否是后台更新全文索引
	 */
	public function update_search($typeid ,$id = 0,$data = '',$text = '',$adddate = 0, $iscreateindex=0) {
		$segment = new \Lain\Phpcms\segment();
		//分词结果
		$fulltext_data = $segment->get_keyword($segment->split_result($data));
		$fulltext_data = $text.' '.$fulltext_data;
		if(!$iscreateindex) {
			$r = $this->where(array('typeid'=>$typeid,'id'=>$id))->field('searchid')->find();
		}
	
		if($r) {
			$searchid = $r['searchid'];
			$this->where('searchid='.$searchid)->save(array('data'=>$fulltext_data,'adddate'=>$adddate));
		} else {
			$searchid = $this->add(array('typeid'=>$typeid,'id'=>$id,'adddate'=>$adddate,'data'=>$fulltext_data));
		}
		return $searchid;
	}
	
	/*
	 * 删除全站搜索内容
	*/
	public function delete_search($typeid ,$id) {
		$this->where(array('typeid'=>$typeid,'id'=>$id))->delete();
	}
}

?>