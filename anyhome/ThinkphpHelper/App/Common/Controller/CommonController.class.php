<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {
    public $md, $ac, $con;
    public function _initialize()
    {
    	$this->ac = ACTION_NAME;
        $this->con = CONTROLLER_NAME;
        $this->md = MODULE_NAME;
        $this->assign('md', $this->md);
        $this->assign('con', $this->con);
        $this->assign('ac', $this->ac);

        $this->assign('addUrl', U($this->con.'/add'));
        $this->assign('editUrl', U($this->con.'/edit?id='.I('id')));
        $this->assign('deleteUrl', U($this->con.'/delete?id='.I('id')));
        $this->assign('backUrl', U($this->con.'/index'));

        $this->assign('insertUrl', U($this->con.'/insert'));
        $this->assign('updateUrl', U($this->con.'/update'));
    }

    public function index($page = 1)
    {
        if ( method_exists( $this, '_filter' ) ) {
            $map = $this->_filter();
        }
    	$model = D( $this->con );
        if ( !empty( $model ) ) {
            $data = $model->getPage($map,$page);
        	$this->assign('data', $data);
        }
        $this->display();
    }

    function update($id = 0) 
    {

        $model = D( $this->con );
        if ( false === $model->create() ) {
          $this->error( $model->getError() );
        }
        // 更新数据
        $map['id'] = $id;
        $list = $model->where( $map )->save();
        if ( false !== $list ) {
          //成功提示
            $this->success( '编辑成功!', cookie( '_currentUrl_' ) );
        } else {
          //错误提示
          $this->error( '编辑失败!' );
        }
    }


    public function edit($id = 0) {
        $model = D( $this->con );
        $map['id'] = $id;
        $vo = $model->getOne( $map );
        if ( !$vo ) {
            $this->error( "参数错误.非法参数." );
            exit();
        }
        $this->assign( 'vo', $vo );
        $this->display();
    }

}