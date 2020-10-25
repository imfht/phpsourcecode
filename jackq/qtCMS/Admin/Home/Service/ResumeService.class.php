<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:52
 */

namespace Home\Service;


class ResumeService extends CommonService {

    public function updateReadTagById($id){
        $Resume = $this->getD();
        $map['id']=$id;
        $map['read_tag']='已读';
        $Resume->save($map);
    }

    public function update_remark($resume){
        $Resume = $this->getD();
        $Resume->save($resume);
    }

    /**
     *
     * @param  int
     * @return boolean
     */
    public function delete($id) {
        $Resume = $this->getD();
        $Resume->startTrans();
        // 删除栏目
        $delStatus = $Resume->delete($id);
        // 删除文章
        if (false === $delStatus) {
            $Resume->rollback();
            return $this->resultReturn(false);
        }

        $Resume->commit();
        return $this->resultReturn(true);
    }
    
    protected function getModelName() {
        return 'Resume';
    }


} 