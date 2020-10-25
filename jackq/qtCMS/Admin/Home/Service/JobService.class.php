<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:52
 */

namespace Home\Service;


class JobService extends CommonService
{

    /**
     * 添加
     * @param
     * @return array
     */
    public function add($job)
    {
        $Job = $this->getD();
        $Job->startTrans();
        if (false === ($job = $Job->create($job))) {
            return $this->errorResultReturn($Job->getError());
        }
        $as = $Job->add($job);
        if (false === $as) {
            $Job->rollback();
            return $this->errorResultReturn('系统出错了！');
        }
        $Job->commit();
        return $this->resultReturn(true);
    }

    public function delete($id)
    {
        $Job = $this->getD();
        $job = $Job->getById($id);
        if (empty($job)) {
            return $this->resultReturn(false);
        }
        $Job->startTrans();
        // 删除栏目
        $delStatus = $Job->delete($id);
        if (false === $delStatus) {
            $Job->rollback();
            return $this->resultReturn(false);
        }
        $Job->commit();
        return $this->resultReturn(true);
    }

    public function update($job) {
        $Job = $this->getD();
        if (false === ($job = $Job->create($job))) {
            return $this->errorResultReturn($Job->getError());
        }
        if (false === $Job ->save($job)) {
            return $this->errorResultReturn('系统错误！');
        }
        return $this->resultReturn(true);
    }

    public function  existJob($id){
        return !is_null($this->getM()->getById($id));
    }

    protected function getModelName()
    {
        return 'Job';
    }
} 