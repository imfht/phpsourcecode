<?php

namespace Home\Model;

use Think\Model;

class BookModel extends Model {
	
	//按bookid,和index获取章节内容
	public function getChapterDetailByIndex($bookid, $index){
		//取出章节
		$map['bookid'] = $bookid;
		$map['index'] = $index;
		
		$detail_chapter = D('BookChapter')->where($map)->find();
		if($detail_chapter){
			$detail_chapter['content'] = M('BookChapterData')->where('chapter_id='.$detail_chapter['id'])->getField('content');
			return $detail_chapter;
		}
		return false;
		
	}
	
	//保存章节
	public function saveChapter($chapter_id, $content){
		//如果内容太少, 则不保存
		if(!$chapter_id || !$content || strlen($content)< 1000)
			return false;

		$map['chapter_id'] = $chapter_id;
		//保存到附表
		$chapter_detail = M('BookChapterData')->field('chapter_id')->where($map)->find();
		if(!$chapter_detail){
		    $info['chapter_id'] = $chapter_id;
		    $info['content']    = $content;
		    
		    $result = M('BookChapterData')->add($info);
		}else{
		    $result = M('BookChapterData')->where($map)->save(array('content' => $content));
		}
		
		if($result){
		    $data['create_time'] = NOW_TIME;
		    $data['status'] = 1;	//更新状态
		    //保存到主表
		    M('BookChapter')->where('id='.$chapter_id)->save($data);
		    return true;
		}else 
		    return false;
	}
}

?>