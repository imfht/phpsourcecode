<?php
namespace app\article\controller;
use app\admin\controller\Admin;
use app\article\model\ContentArticle;

/**
 * Class AdminContent 文章控制器类
 * hongkai.wang 20161203  QQ：529988248
 */
class AdminContent extends Admin{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '文章管理',
                'description' => '管理网站的所有文章',
            ),
            'menu' => array(
                array(
                    'name' => '文章列表',
                    'url' => url('index'),
                    'icon' => 'list',
                ),

            ),
            '_info' => array(
                array(
                    'name' => '添加文章',
                    'url' => url('info'),
                    'icon' => 'plus',
                ),
            )
        );
    }
	//文章文章列表
	public function index(){
        //筛选条件
        $where = array();
        $keyword = input('keyword');
        $classId = input('class_id');
        $positionId = input('position_id');
        $status = input('status');
        if(!empty($keyword)){
            $where['A.title'] = ['like','%'.$keyword.'%'];
        }
        if(!empty($classId)){
            $where['C.class_id'] = $classId;
        }
        if(!empty($positionId)){
            $where[] = ['exp','find_in_set('.$positionId.',position)'];;
        }
        if(!empty($status)){
            switch ($status) {
                case '1':
                    $where['A.status'] = 1;
                    break;
                case '2':
                    $where['A.status'] = 2;
                    break;
            }
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['status'] = $status;
        $pageMaps['class_id'] = $classId;
        $pageMaps['position_id'] = $positionId;
        //查询数据
        $list = model('ContentArticle')->loadList($where);
        //位置导航
        $breadCrumb=array(array('name'=>'文章管理','url'=>url('index')));
        //模板传值
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('categoryList',model('kbcms/Category')->loadList());
        $this->assign('positionList',model('kbcms/Position')->loadList());
        $this->assign('_page',$list->render());
        $this->assign('pageMaps',$pageMaps);
		return $this->fetch();
	}
    /**
     * 详情
     */
    public function info(){
        $content_id=input('post.content_id');
        $model = model('ContentArticle');
        if (input('post.')){
            if ($content_id){
                $status=$model->edit();
            }else{
                $status=$model->add();
            }
            if($status){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $this->assign('category_list',model('kbcms/Category')->loadList());
            $this->assign('position_list',model('kbcms/Position')->loadList());
            $this->assign('tplList',model('admin/Config')->tplList());//模板文件
            $this->assign('info',$model->getInfo(input('id')));//页面信息
            return $this->fetch();
        }
    }
	//文章文章删除
	public function del(){
        $content_id=input('id');
        if(empty($content_id)){
            return ajaxReturn(0,'参数不能为空');
        }
        if(model('ContentArticle')->del($content_id)){
            return ajaxReturn(200,'文章删除成功！');
        }else{
            return ajaxReturn(0,'文章删除失败！');
        }
	}
}
