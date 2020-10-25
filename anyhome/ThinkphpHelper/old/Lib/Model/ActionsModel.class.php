<?php
class ActionsModel extends CommonModel
{
	//判断指定操作是否存在
    public function FunctionName($group ='', $mod = '', $ac = '')
    {
    	$map['group'] = $group;
    	$map['mod'] = $mod;
    	$map['ac'] = $ac;
    	$vo = $this->where($map)->find();
    	if (!$vo) return false;
    	return $vo;
    	
    }
    public function getAll()
    {
        $Module = M('Module');
        $vlist = $this->select();
        $volist = array();
        foreach ($vlist as $k) {
            $k = $this->ckvo($k);
            $vo['actions'] = $vo;
            $volist[$k['mod']][] = $k;
        }
        $vvlist = array();
        foreach ($volist as $key => $value) {
            $map['name'] = $key;
            $mod = $Module->where($map)->find();
            $vo = array();
            $vo['name'] = $mod['name'];
            $vo['title'] = $mod['title'];
            $vo['tbname'] = $mod['tbname'];
            $vo['actions'] = $value;
            $vvlist[] = $vo;
        }
        return $vvlist;
    }


    public function ckvo($vo='')
    {
        if (!$vo) return $vo;
        if ($vo['title'] =='' && $vo['ac'] == 'add') {
            $vo['title'] = '新增';
        }elseif ($vo['title'] =='' && $vo['ac'] == 'edit') {
            $vo['title'] = '编辑';
        }elseif ($vo['title'] =='' && $vo['ac'] == 'index') {
            $vo['title'] = '首页';
        }elseif ($vo['title'] =='' && $vo['ac'] == 'list') {
            $vo['title'] = '列表';
        }elseif ($vo['title'] =='' && $vo['ac'] == 'update') {
            $vo['title'] = '更新';
        }elseif ($vo['title'] =='' && $vo['ac'] == 'insert') {
            $vo['title'] = '插入';
        }elseif ($vo['title'] =='' && $vo['ac'] == 'view') {
            $vo['title'] = '查看';
        }elseif ($vo['title'] =='' && $vo['ac'] == 'delete') {
            $vo['title'] = '删除';
        }elseif ($vo['title'] =='' && $vo['ac'] == 'search') {
            $vo['title'] = '搜索';
        }
        return $vo;
    }
}
?>