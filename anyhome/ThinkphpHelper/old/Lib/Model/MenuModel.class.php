<?php
class MenuModel extends CommonModel
{
    //根据用户ID获取所有菜单
    public function getAllByMid($mid = 0)
    {
        $map['mid'] = $mid;
        $menus = $this->getAllMenus($map);
        return $menus;
    }


    //获取所有菜单
    public function getAllMenus($map)
    {
        $map['pid'] = 0;
        $ms = $this->where($map)->order('idx asc')->select();
        $volist = array();
        foreach ($ms as $k) {
            $chs = $this->getChildsById($k['id']);
            if ($chs) $k['childs'] = $chs;
            $volist[] = $this->ckvo($k);
        }
        return $volist;
    }

    //所有子菜单
    public function getChildsById($id = 0)
    {
        if ($id == 0) return;
        $map['pid'] = $id;
        $ms = $this->where($map)->order('idx asc')->select();
        $volist = array();
        foreach ($ms as $k) {
            $map['pid'] = $k['id'];
            $chs = $this->where($map)->order('idx asc')->select();
            if ($chs) $k['childs'] = $this->ckvolist($chs);
            $volist[] = $this->ckvo($k);
        }
        return $volist;
    }

    public function ckvolist($volist='')
    {
        if(!$volist) return;
        $vlist = array();
        foreach ($volist as $k) {
            $vlist[] = $this->ckvo($k);
        }
        return $vlist;
    }

    public function ckvo($vo ='')
    {
        if(!$vo) return;
        if ($vo['group'] == "")return;
        if ($vo['url'] == "") 
            $vo['url'] = U($vo['group'].'/'.$vo['mod'].'/index');
        return $vo;
    }
}
?>