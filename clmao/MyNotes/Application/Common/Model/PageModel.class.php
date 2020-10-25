<?php
namespace Common\Model;


class PageModel{

    /**
     * 得到分页
     * @param $table 表名
     * @return array
     */
    public function getPage($table) {
        $category = M($table); // 实例化User对象
        $count = $category->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, C('sitePageNum')); // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $data['page'] = $Page->show(); // 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $data['list'] = $category->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        return $data;
    }

}
