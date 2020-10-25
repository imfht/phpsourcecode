<?php

class CommonModel extends Model
{
    public function _list_($map = array() ,$page = 1,$limit = 16, $orderby = ' id desc'){
        $list['count'] = $this->where($map)->count('id');
        $list['page'] = $page;
        $list['map'] = $map;
        $list['limit'] = $limit;
        $pagecount = ceil($list['count'] / $list['limit']);
        if ($pagecount < 1) $pagecount =1;
        $list['pagecount'] = $pagecount;
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


    /*
     * 获取一条记录
     * @param  $map  需要查找的sql语句中的where条件
     * @return array 返回结果
    **/
    public function getOne($map=array(), $sortOrder=array())
    {
        $vo = $this->where($map)->order($sortOrder)->find();
        if (method_exists($this, 'ckvo')) {
            $vo = $this->ckvo($vo);
        }
        return $vo;
    }


    public function getAll($map='')
    {
        $volist = $this->where($map)->select();
        return $volist;
    }
}
?>