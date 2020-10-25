<?php
namespace app\article\controller;
use app\admin\controller\Admin;

/**
 * Class Article 文章栏目控制器类
 * hongkai.wang 20161203  QQ：529988248
 */
class AdminCategory extends Admin
{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        $data = array('info' => array('name' => '栏目管理',
            'description' => '管理网站全部栏目',
        ),
            'menu' => array(
                array('name' => '栏目列表',
                    'url' => url('kbcms/AdminCategory/index'),
                    'icon' => 'list',
                ),
            ),
        );
        $modelList = get_all_service('ContentModel', '');
        if (!empty($modelList)) {
            $i = 0;
            foreach ($modelList as $key => $value) {
                $i++;
                $data['add'][$i]['name'] = '添加' . $value['name'] . '栏目';
                $data['add'][$i]['url'] = url($key . '/AdminCategory/add');
                $data['add'][$i]['icon'] = 'plus';
            }
        }
        return $data;
    }
	//文章栏目添加
	public function add(){
		if (input('post.')){
            $validate=validate('CategoryArticle');
            if(!$validate->scene('add')->check(input('post.'))){
                $this->error($validate->getError());
            }
			$rs=model('CategoryArticle')->add();
			if ($rs===true){
				$this->success('添加成功');
			}else{
				$this->error($rs);
			}
		}else{
            $this->assign('tplList',model('admin/Config')->tplList());//模板文件
            $this->assign('expandList',model('kbcms/FieldsetExpand')->loadList());//扩展字段
			$this->assign('category_list',model('kbcms/Category')->loadList());
			return $this->fetch();
		}
	}
	//文章栏目修改
	public function edit(){
		if ((input('post.'))){
            $validate=validate('CategoryArticle');
            if(!$validate->scene('edit')->check(input('post.'))){
                $this->error($validate->getError());
            }
			$rs=model('CategoryArticle')->edit();
			if ($rs===true){
				$this->success('修改成功');
			}else{
				$this->error('修改失败：'.$rs);
			}
		}else{
			$info=model('CategoryArticle')->getInfo(input('id'));
			//模板赋值
            $this->assign('tplList',model('admin/Config')->tplList());//模板文件
            $this->assign('expandList',model('kbcms/FieldsetExpand')->loadList());//扩展字段
			$this->assign('category_list',model('kbcms/Category')->loadList());
			$this->assign('info',$info);
			return $this->fetch();
		}
	}
	//文章栏目删除
	public function del(){
        $class_id=input('post.id');
		if (empty($class_id)){
			$this->error('参数不能为空');
		}
        //判断子栏目
        if(model('kbcms/Category')->loadList(array(), $class_id)){
            $this->error('请先删除子栏目！');
        }
        //判断栏目下内容
        $where = array();
        $where['A.class_id'] = $class_id;
        $contentNum = model('ContentArticle')->countList($where);
        if(!empty($contentNum)){
            $this->error('请先删除该栏目下的内容！');
        }
        //删除栏目操作
        if(model('CategoryArticle')->del($class_id)){
            $this->success('栏目删除成功！');
        }else{
            $this->error('栏目删除失败！');
        }
	}
}
