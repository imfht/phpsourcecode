<?php
namespace Common\Model;
use Think\Model;
class CommonModel extends Model {
	public function getPage($map = array() ,$page = 1,$limit = 16, $orderby = ' id desc'){
        $list['count'] = $this->where($map)->count('id');
        $list['cpage'] = $page;
        $list['map'] = $map;
        $list['limit'] = $limit;
        $pagecount = ceil($list['count'] / $list['limit']);
        if ($pagecount < 1) $pagecount =1;
        $list['page'] = $pagecount;
        $vlist = $this->where($map)->page($page,$limit)->order("$orderby ")->select();


        $vvlist = array();
        foreach ($vlist as $k) {
            if (method_exists($this, 'ckvo')) {
                $vvlist[] = $this->ckvo($k);
            }else{
                $vvlist[] = $k;
            }
        }
        $list['volist'] = $vvlist;
        return $list;
    }


    public function getOne($map=array(), $sortOrder=array())
    {
        $vo = $this->where($map)->order($sortOrder)->find();
        if (method_exists($this, 'ckvo')) {
            $vo = $this->ckvo($vo);
        }
        return $vo;
    }
}
