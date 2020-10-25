<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * @author 大蒙<59262424@qq.com>
 */

namespace Articles\Widget;


use Think\Controller;

class HomeBlockWidget extends Controller{
    public function render()
    {
        $this->assignArticle();
        $this->display(T('Application://Articles@Widget/homeblock'));
    }

    private function assignArticle()
    {
        $num = modC('ARTICLES_SHOW_COUNT', 4, 'Articles');
        $type= modC('ARTICLES_SHOW_TYPE', 0, 'Articles');
        $field = modC('ARTICLES_SHOW_ORDER_FIELD', 'view', 'Articles');
        $order = modC('ARTICLES_SHOW_ORDER_TYPE', 'desc', 'Articles');
        $cache = modC('ARTICLES_SHOW_CACHE_TIME', 600, 'Articles');
        $list = S('articles_home_data');
        if (!$list) {
            if($type){
                /**
                 * 获取推荐位数据列表
                 * @param  number  $pos      推荐位 1-系统首页，2-推荐阅读，4-本类推荐
                 * @param  number  $category 分类ID
                 * @param  number  $limit    列表行数
                 * @param  boolean $filed    查询字段
                 * @param order 排序
                 * @return array             数据列表
                 */
                $list=D('Articles/Articles')->position(1,null,$num,true,$field . ' ' . $order);
            }else{
                $map = array('status' => 1,'dead_line'=>array('gt',time()));
                $list = D('Articles/Articles')->getList($map,$field . ' ' . $order,$num);
            }
            foreach ($list as &$v) {
                $val['user']=query_user(array('space_url','nickname'),$v['uid']);
            }
            unset($v);
            if(!$list){
                $list=1;
            }
            S('articles_home_data', $list, $cache);
        }
        unset($v);
        if($list==1){
            $list=null;
        }
        $this->assign('articles_lists', $list);
    }
} 