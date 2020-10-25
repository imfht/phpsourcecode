<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:33
 */

namespace Home\Controller;


class JobController extends CommonController
{

    public function index(){
        $result = $this->getPagination('Job');
        //var_dump($result['data']);
        $this->assign('jobs', $result['data']);
        $this->assign('rows_count', $result['total_rows']);
        $this->assign('page', $result['show']);
        $this->display();
    }

    public function add(){
        $categorys =  D('Category', 'Service')->getCategorysByRelatinModel('Job');
        if(empty($categorys)){
            return $this->errorReturn('无效的操作！');
        }
        $this->assign('category',$categorys[0]);
        $this->display();
    }

    /**
     * 创建招聘信息
     * @return
     */
    public function create() {
        if (!isset($_POST['job'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = D('Job', 'Service')->add($_POST['job']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('添加招聘信息成功！', U('Job/index'));
    }

    /**
     * 编辑招聘信息
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
            || !D('Job', 'Service')->existJob($_GET['id'])) {
            return $this->error('需要编辑的招聘信息不存在！');
        }
        $job = M('Job')->getById($_GET['id']);
        $this->assign('job', $job);
        $this->display();
    }

    /**
     * 更新管理员信息
     * @return
     */
    public function update() {
        $jobService = D('Job', 'Service');
        if (!isset($_POST['job'])
            || !$jobService->existJob($_POST['job']['id'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = $jobService->update($_POST['job']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新招聘信息成功！', U('Job/index'));
    }

    /**
     * 删除招聘信息
     * @return
     */
    public function delete() {
        if (!isset($_GET['id'])) {
            $this->errorReturn('您需要删除的招聘信息不存在！');
        }

        $job = M('Job')->getById($_GET['id']);
        if (empty($job)) {
            $this->errorReturn('您需要删除的招聘信息不存在！');
        }

        $result = D('Job', 'Service')->delete($job['id']);
        if (false === $result['status']) {
            return $this->errorReturn('系统出错了！');
        }

        $this->successReturn("删除招聘信息 <b>{$job['name']}</b> 成功！");
    }



} 