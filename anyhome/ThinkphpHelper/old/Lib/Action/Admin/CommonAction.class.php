<?php
class CommonAction extends Action {
    public $mid,$mrole,$minfo,$marea;

    //用户父级菜单 子级菜单 当前菜单集 当前菜单
    public $parentlMenus,$childMenus,$currentMenus,$currentMenu;
    public $userAera,$userRole; //当前登录用户的所属 区域ID 角色信息 海岩为 47
    public $group, $ac, $mod;
    public function _initialize()
    {
        $this->ac = ACTION_NAME;
        $this->mod = $this->getActionName();
        $this->group = GROUP_NAME;
        $this->assign('group', $this->group);
        $this->assign('mod', $this->mod);
        $this->assign('ac', $this->ac);
        $this->assign('thismod', $this->mod);
        $this->mid = session('mid');
        $this->assign('mid', $this->mid);

        
        if (!$this->mid) {
            $this->success('请先登录', U('Public/login'));
            exit();
        }

        $Menu = D('Menu');
        $this->currentMenus = $Menu->getAllMenus();
        
        $this->assign( 'currentMenus', $this->currentMenus);

        //$tableTitle
        unset($map);
        $map['mod'] = $this->mod;
        $this->currentMenu = $Menu->where($map)->find();
        $this->assign( 'currentMenu', $this->currentMenu);
        $this->assign( 'tableTitle', $this->currentMenu['name']);


        $this->assign( 'addurl', U( $this->mod.'/add' ) );
        if ( strtolower( $this->ac ) == "index" ) {
            $this->assign( 'do', U( $this->mod.'/add' ) );
            $this->assign( 'do_search', U( $this->mod.'/search' ) );
        }elseif ( strtolower( $this->ac ) == "add" ) {
            $this->assign( 'do', U( $this->mod.'/insert' ) );
        }elseif ( strtolower( $this->ac ) == "edit" ) {
            $this->assign( 'do', U( $this->mod.'/update' ) );
        }
    }


    public function add()
    {
        $this->display();
    }

    protected function _search( $name = '' ) {
        //生成查询条件
        if ( empty( $name ) ) {
            $name = $this->actionName;
        }
        // $name = $this->getActionName();
        $model = D( $name );
        $map = array();
        foreach ( $model->getDbFields() as $key => $val ) {
            if ( isset( $_REQUEST [$val] ) && $_REQUEST [$val] != '' ) {
                $map [$val] = $_REQUEST [$val];
            }
        }
        return $map;
    }

    public function view($id = 0) {
        $model = D( $this->mod );
        $map['id'] = $id;
        $map['mid'] = $this->mid;
        $vo = $model->getOne( $map );
        if ( !$vo ) {
            $this->error( "参数错误.非法参数." );
            exit();
        }
        $this->assign( 'vo', $vo );
        $this->display();
    }

    public function edit($id = 0) {
        $model = D( $this->mod );
        $map['id'] = $id;
        $map['mid'] = $this->mid;
        $vo = $model->getOne( $map );
        if ( !$vo ) {
            $this->error( "参数错误.非法参数." );
            exit();
        }
        $this->assign( 'vo', $vo );
        $this->display();
    }

    public function updateField($columnName ='',$id = 0,$value = '')
    {
        if ($columnName =="" || $id ==0) {
            echo($value);
            return;
        }
        $model = M($this->mod);
        $map['id'] = $id;
        $model->where($map)->setField($columnName,$value);
        echo($value);
    }

    public function insert()
    {
        $_POST['mid'] = $this->mid;
        $model = D( $this->mod );
        if ( false === $model->create() ) {
          $this->error( $model->getError() );
        }
        //保存当前数据对象
        $list = $model->add();
        if ( $list !== false ) { //保存成功
            afterNote('新增成功');
            redirect(cookie( '_currentUrl_' ));
            // $this->success( '新增成功!', cookie( '_currentUrl_' ) );
        } else {
          //失败提示
          $this->error( '新增失败!' );
        }
    }

    function update() 
    {
        $model = D( $this->mod );
        if ( false === $model->create() ) {
          $this->error( $model->getError() );
        }
        $id = I( $model->getPk() );
        // 更新数据
        $map['mid'] = $this->mid;
        $map[$model->getPk()] = $id;
        $list = $model->where( $map )->save();
        if ( false !== $list ) {
          //成功提示
            afterNote('编辑成功');
            redirect(cookie( '_currentUrl_' ));
            // $this->success( '编辑成功!', cookie( '_currentUrl_' ) );
        } else {
          //错误提示
          $this->error( '编辑失败!' );
        }
    }

    public function index() {
        
        $map = $this->_search();
            if ( method_exists( $this, '_filter' ) ) {
            $map = $this->_filter();
        }
        $map['mid'] = $this->mid;
        $map['appid'] = $this->appid;
        $model = D( $this->mod );
            if ( !empty( $model ) ) {
            $this->_list( $model, $map );
        }
        cookie( '_currentUrl_', __SELF__ );
        $this->display();
        return;
    }


    public function delete($id = 0)
    {
        $model = D( $this->mod );
        if ( !empty( $model ) ) {
          $pk = 'id';
          if ( isset( $id ) ) {
            $map[$pk] = array( 'in', explode( ',', $id ) );
            $restult = $model->where( $map )->delete();
            if ( false != $restult ) {
              $this->success( '删除成功！' );
            } else {
              $this->error( '删除失败！' );
            }
          } else {
            $this->error( '非法操作' );
          }
        }
    }



    public function _list($model, $map, $sortBy = ' id desc')
    {
        $vlist = $model->getAll($map);
        $this->assign('volist',$vlist);
        return $vlist;
        $page = I('p',1);
        if ($page < 1) $page = 1;
        $pger  = $model->_list_($map,$page,16,$sortBy);
        $this->assign('volist',$pger['volist']);
        $this->assign('pger',$pger);

        $pgpam = $map;
        $pgvo =  array();
        unset($pgpam['mid']);
        for ($i = 0 ; $i < $pger['pagecount']; $i++) { 
            $pg = array();
            $pgpam['p'] = $i+1;
            $urlParems = http_build_query($pgpam);
            $pg['url'] = U($this->group.'/'.$this->mod.'/'.$this->ac.'?'.$urlParems);
            if ($page == $i+1) {
                $pg['url'] = "#";
                $pg['class'] = "active";
            }
            $pgvo[] = $pg;
        }
        $pgpam['p'] = $page+1;
        $urlParems = http_build_query($pgpam);
        $npage_url = U($this->group.'/'.$this->mod.'/'.$this->ac.'?'.$urlParems);
        $this->assign('npage_url',$npage_url);

        $pgpam['p'] = $page-1;
        $urlParems = http_build_query($pgpam);
        $ppage_url = U($this->group.'/'.$this->mod.'/'.$this->ac.'?'.$urlParems);
        $this->assign('ppage_url',$ppage_url);

        $this->assign('pgvo',$pgvo);
        $this->assign('npage',$page + 1);
        $this->assign('ppage',$page - 1);
        return $pger['volist'];
    }
}