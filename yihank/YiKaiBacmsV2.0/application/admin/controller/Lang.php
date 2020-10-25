<?php
namespace app\admin\controller;
use app\admin\controller\Admin;
/**
 * 语言管理
 */

class Lang extends Admin {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '语言管理',
                'description' => '管理网站调用自定义变量',
                ),
            'menu' => array(
                    array(
                        'name' => '语言列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                ),
            '_info' => array(
                    array(
                        'name' => '添加语言',
                        'url' => url('info'),
                    ),
                )
            );
    }
	/**
     * 列表
     */
    public function index(){
        $breadCrumb = array('语言列表'=>url());
        $list=model('Lang')->loadList();
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('_page',$list->render());
        return $this->fetch();
    }

    /**
     * 信息
     */
    public function info(){
        $lang_id=input('post.lang_id');
        $model = model('Lang');
        if (input('post.')){
            $path=APP_PATH.'/lang/';
            if (!is_writable($path)){
                return ajaxReturn(0,$path.'文件夹没有写入权限，无法操作');
            }
            if ($lang_id){
                $status=$model->edit();

            }else{
                $status=$model->add();
            }
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $this->assign('info',$model->getInfo(input('lang_id')));
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $langId = input('id');
        if(empty($langId)){
            return ajaxReturn(0,'参数不能为空');
        }
        if ($langId==1){
            return ajaxReturn(0,'默认语言不能删除');
        }
        $map = array();
        $map['lang_id'] = $langId;
        if(model('Lang')->del($langId)){
            return ajaxReturn(200,'语言删除成功！');
        }else{
            return ajaxReturn(0,'语言删除失败！');
        }
    }
}

