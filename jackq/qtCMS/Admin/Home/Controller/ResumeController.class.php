<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:33
 */

namespace Home\Controller;


class ResumeController extends CommonController{

    public function index(){
        $result = $this->getPagination('Resume',null,null,'public_time desc');
        //var_dump($result['data']);
        $this->assign('resumes', $result['data']);
        $this->assign('rows_count', $result['total_rows']);
        $this->assign('page', $result['show']);
        $this->display();
    }

    public function look(){
        if (!isset($_GET['id'])) {
            $this->errorReturn('您需要查看的简历不存在！');
        }

        $resume = M('Resume')->getById($_GET['id']);
        if (empty($resume)) {
            $this->errorReturn('您需要查看的简历不存在！');
        }

        D('Resume', 'Service')->updateReadTagById($_GET['id']);
        //var_dump($result['data']);
        $this->assign('resume', $resume);
        $this->display();
    }

    public function update_remark(){
        $resume = $_POST['resume'];
        if (!isset($resume)) {
            return $this->errorReturn('无效的操作！');
        }
        if(empty($resume['remark'])){
            $this->errorReturn('简历备注不能为空！');
        }
        D('Resume', 'Service')->update_remark($resume);
        $this->successReturn("简历备注操作成功！",U('Resume/index'));
    }

    /**
     * 删除
     * @return
     */
    public function delete() {
        if (!isset($_GET['id'])) {
            $this->errorReturn('您需要删除的简历不存在！');
        }

        $resume = M('Resume')->getById($_GET['id']);
        if (empty($resume)) {
            $this->errorReturn('您需要删除的简历不存在！');
        }

        $result = D('Resume', 'Service')->delete($resume['id']);
        if (false === $result['status']) {
            return $this->errorReturn('系统出错了！');
        }

        $this->successReturn("删除简历 <b>{$resume['name']}</b> 成功！");
    }





} 