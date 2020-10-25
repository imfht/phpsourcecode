<?php
namespace app\admin\controller;
use app\admin\controller\AdminController;
/**
 * 备份还原
 */
class AdminBackupController extends AdminController {

    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '备份还原',
                'description' => '备份还原整站数据库',
                ),
            'menu' => array(
                    array(
                        'name' => '备份列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                ),
            'add' => array(
                    array(
                        'name' => '新建备份',
                        'url' => url('add'),
                    ),
                ),
            );
    }

	/**
     * 列表
     */
    public function index(){
        //查询数据
        $list = target('Database')->backupList();
        //位置导航
        $breadCrumb = array('备份列表'=>url());
        //模板传值
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->adminDisplay();
    }

    /**
     * 增加
     */
    public function add(){
        if(!IS_POST){
            $breadCrumb = array('备份列表'=>url('index'),'新建'=>url());
            //查询数据
            $list = target('Database')->loadTableList();
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','新建');
            $this->assign('list',$list);
            $this->adminDisplay('info');
        }else{
            $type = request('post.type');
            switch ($type) {
                case 1:
                    $action = 'optimizeData';
                    break;
                case 2:
                    $action = 'repairData';
                    break;
                default:
                    $action = 'backupData';
                    break;
            }
            if(target('Database')->$action()){
                $this->success('数据库操作执行完毕！',url('index'));
            }else{
                $msg = target('Database')->getError();
                if(empty($msg)){
                    $this->error('数据库操作执行失败');
                }else{
                    $this->error($msg);
                }
                
            }
        }
    }

    /**
     * 导入
     */
    public function import(){
        $time = request('post.data');
        if(empty($time)){
            $this->error('参数不能为空！');
        }
        //获取备份数量
        if(target('Database')->importData($time)){
            $this->success('备份恢复成功！',url('index'));
        }else{
            $msg = target('Database')->error;
            if(empty($msg)){
                $this->error('备份恢复失败！');
            }else{
                $this->error($msg);
            }
        }
    }

    /**
     * 删除
     */
    public function del(){
        $time = request('post.data');
        if(empty($time)){
            $this->error('参数不能为空！');
        }
        //获取备份数量
        target('Database')->delData($time);
        $this->success('备份文件删除完毕！');
    }


}

