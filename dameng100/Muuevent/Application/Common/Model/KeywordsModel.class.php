<?php
/**
**关键字模型
 */

namespace Common\Model;
use Think\Model;

class keywordsModel extends Model
{	
	/*
	*按条件获取分页列表
	*/
	public function getListPage($map,$page=1,$order='create_time desc',$r=10)
    {
        $totalCount=$this->where($map)->count();
        if($totalCount){
            $list=$this->where($map)->order($order)->page($page,$r)->select();
        }
        return array($list,$totalCount);
    }

    /**
    *真实删除内容
    **/
    public function setTrueDel($ids)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $map['id']=array('in',$ids);
        $res=$this->where($map)->delete();
        
        return $res;
    }
    /*
	* 把关键字写入关键字表中
    * 调用方法 D('Common/keywords')->editKeywords('Articles',$aId?$aId:$result,$data['keywords']);
    */
    public function editKeywords($app=MODULE_NAME,$rowid,$keywords)
    {
        	//如果是编辑只写入关键字的差值
            //$aKeys = explode(',',$resKey);
            //$aKeys2 = explode(',',$data['keywords']);
            //$arrayKeys = array_diff($aKeys2,$aKeys);//获取关键字数组的差值
            //$sKeys = implode(',',$arrayKeys);
            if(empty($keywords)){
                return false;
            }else{
            	$data['app']=$app;
            	$data['row']=$rowid;
            	$data['keywords']=str2arr($keywords,','); //将关键字字符串转为数组

            	$nArray=[];
            	foreach($data['keywords'] as $key=>$val){
            		$nArray[$key]['title'] = $val;
            		$nArray[$key]['app'] = $app;
            		$nArray[$key]['row'] = $rowid;
            	}
            	unset($val);
            	foreach($nArray as $val){
    				$result = $this->addStrKeyword($val);
    			}
    			unset($val);
            	return true;
            }
    }
	/*
	**单个字符串关键字写入数据中
	*/
	private function addStrKeyword($data)
	{
		$res = $this->where($data)->find();
		if(!$res){
			$data['create_time'] = time();
			$result = $this->add($data);
		}
		$this->addKeywordsCount($data);
		return $result;
	}
	private function addKeywordsCount($data)
	{
		$map['title']=$data['title'];
		$res = M('KeywordsCount')->where($map)->find();
		if($res){
			$num = $this->where($map)->count();
            $num = $num+1;
			M('KeywordsCount')->where($map)->save(array('num'=>$num));
		}else{
			$keywords['title']=$data['title'];
            $keywords['create_time']=time();
			M('KeywordsCount')->add($keywords);
		}
	}
}