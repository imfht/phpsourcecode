<?php

/*
  文章 content表 模型
 */

namespace Admin\Model;

use Think\Model;

class ContentModel extends Model {

    /**
     * 得到status
     * @param $id content的ID
     * @return int
     */
    public function getStatus($id) {
        return M('content')->where('id=' . $id)->getField('status');
    }

    /**
     * 得到所有ID
     * @return array
     */
    public function getAllID() {
        $time = time();
        return M('content')->where("status = 1 and time < $time")->getField('id', true);
    }

    /**
     * 得到文章分页
     * @param $c_id 分类ID
     * @param $status 状态
     * @return array
     */
    public function getPage($c_id = 0, $status = 1, $time = 0,$key='') {
        $Content = M('Content'); // 实例化User对象
        $where='';
        if ($time == 0) {
            $time = time();
            $where = " status=$status and time < $time"; 
        }else{
            $where = " status=$status";
        }
        if ($c_id > 0) {
            $where = " status=$status and time < $time and c_id = $c_id"; 
        }
        if($key!=''){
            $where = " status=$status and time < $time and  excerpt like '%$key%'";
        }
        $count = $Content->where($where)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, C('sitePageNum')); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->rollPage = 10;
        $Page->lastSuffix = false; // 最后一页是否显示总页数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $ext = C('DB_PREFIX');
        $list = M('content')->where($where)->field("{$ext}content.id,{$ext}content.title,{$ext}category.title as c_title,excerpt,time,c_id,author")->join('__CATEGORY__ ON __CATEGORY__.id = __CONTENT__.c_id')->order('time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $data['page'] = $Page->show();
        $data['list'] = $list;
        return $data; // 分页显示输出
    }

    /**
     * 得到所有上下篇文章
     * @return array
     */
    public function getPrevNext($id, $flag = 'prev') {
        $time = time();
        $arr = null;
        if ($flag == 'next') {
            $arr = M('content')->where("id < $id and status=1 and time < $time")->field('id,title')->order('id desc')->find();
            if (empty($arr)) {
                $arr = M('content')->where("id > $id and status=1 and time < $time")->field('id,title')->order('id desc')->find();
            }
        } elseif ($flag == 'prev') {
            $arr = M('content')->where("id > $id and status=1 and time < $time")->field('id,title')->order('id asc')->find();
            if (empty($arr)) {
                $arr = M('content')->where("id < $id and status=1 and time < $time")->field('id,title')->order('id asc')->find();
            }
        }
        return $arr;
    }

}
