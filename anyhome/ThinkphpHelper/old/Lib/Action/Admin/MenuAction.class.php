<?php
class MenuAction extends CommonAction {
    public function _before_add()
    {
        $Menu = M('Menu');
        $map['pid'] = 0;
        $menus = $Menu->where($map)->select();
        $this->assign('menus', $menus);
        unset($map);
        $Group = M('Group');
        $groups = $Group->select();
        $this->assign('groups', $groups);
    }
    public function _before_edit()
    {
        $Menu = M('Menu');
        $map['pid'] = 0;
        $menus = $Menu->where($mpa)->select();
        $this->assign('menus', $menus);
        unset($map);
        $Group = M('Group');
        $groups = $Group->select();
        $this->assign('groups', $groups);
        # code...
    }
    public function index() {
            if ( method_exists( $this, '_filter' ) ) {
            $map = $this->_filter();
        }
        $model = D( $this->mod );
            if ( !empty( $model ) ) {
            $this->_list( $model, $map );
        }

        $Menu = D('Menu');
        $Menus = $Menu->getAllMenus();
        $span = 12 - count($Menus);
        $this->assign('Menus', $Menus);
        $this->assign('span', $span);

        cookie( '_currentUrl_', __SELF__ );
        $this->display();
        return;
    }

    public function sort($id = 0, $idx = 0, $pid = 0)
    {
    	if ($pid != 0 && $id == $pid) $pid = 0;
    	$data['pid'] = $pid;
    	$data['idx'] = $idx;
    	$Menu = M('Menu');
    	$map['id'] = $id;
    	$Menu->where($map)->save($data);
    }
}