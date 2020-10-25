<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:33
 */

namespace Home\Controller;


class CategoryController extends CommonController{

    public function index(){
        $result = $this->getPagination('Category','pid is null or pid = 0 ',null,'sort');
        //var_dump($result['data']);
        $this->assign('categorys', $result['data']);
        $this->assign('rows_count', $result['total_rows']);
        $this->assign('page', $result['show']);
        $this->display();
    }

    public function add(){
        $this->assign('parents', D('Category', 'Service')->getParents());
        $this->display();
    }

    /**
     * 创建栏目
     * @return
     */
    public function create() {
        if (!isset($_POST['category'])) {
            return $this->errorReturn('无效的操作！');
        }
        $result = D('Category', 'Service')->add($_POST['category']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }
        return $this->successReturn('添加栏目成功！', U('Category/index'));
    }

    /**
     * 编辑栏目
     * @return
     */
    public function edit() {
        $categoryService = D('Category', 'Service');
        if (!isset($_GET['id']) || !$categoryService->existById($_GET['id'])) {
            return $this->error('需要编辑的栏目信息不存在！');
        }
        $category = $categoryService->getById($_GET['id']);
        $this->assign('category', $category);
        $this->assign('existSubCategory', $categoryService->existSubCategory($_GET['id']));
        $this->assign('parents', $categoryService->getParents($_GET['id']));
        $this->display();
    }

    /**
     * 更新管理员信息
     * @return
     */
    public function update() {
        $categoryService = D('Category', 'Service');
        if (!isset($_POST['category'])
            || !$categoryService->existById($_POST['category']['id'])) {
            return $this->errorReturn('无效的操作！');
        }
        $result = $categoryService->update($_POST['category']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }
        return $this->successReturn('更新栏目信息成功！', U('Category/index'));
    }

    /**
     * 删除栏目
     * @return
     */
    public function delete() {
        $categoryService = D('Category', 'Service');
        if (!isset($_GET['id']) || !$categoryService->existById($_GET['id'])) {
            return $this->error('需要编辑的栏目信息不存在！');
        }
        $result = $categoryService->delete($_GET['id']);
        if (false === $result['status']) {
            return $this->errorReturn('系统出错了！');
        }
        $this->successReturn("删除栏目成功！");
    }

    public function buildTree(){
        layout(false);
        $this->assign('categorys', D('Category', 'Service')->getCategorysByRelatinModel('Article'));
        $this->display("Article:buildTree");
    }



} 