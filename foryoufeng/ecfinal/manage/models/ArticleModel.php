<?php
/**
 *品牌
 * Created by PhpStorm.
 * User: root
 * Date: 8/16/16
 * Time: 8:26 PM
 * Vsersion:2.0.0
 */
class ArticleModel extends CommonModel{
    protected $tableName='article';
    /**
     * 获取多条结果集
     * @return mixed
     */
     public function all(){
         $result=[];
         $keywords = trim(I('keywords',''));
         $cateid = intval(I('cateid',''));
         //筛选条件处理
         $where['a.title']=array('like',"%$keywords%");
         if($cateid>0){
             $where['a.cat_id']=array('eq',$cateid);
         }
         $count=$this->alias('a')
             ->join('LEFT JOIN __ARTICLE_CAT__ ac ON a.cat_id = ac.cat_id')
             ->field('a.article_id,a.title,a.article_type,a.add_time,a.is_open,ac.cat_name')
             ->where($where)->count();

         $filter=$this->page_and_size($count);
         $result['filter']=$filter;

         $data=$this->alias('a')
             ->join('LEFT JOIN __ARTICLE_CAT__ ac ON a.cat_id = ac.cat_id')
             ->field('a.article_id,a.title,a.article_type,a.add_time,a.is_open,ac.cat_name')
            ->where($where)
             ->order('a.article_id desc')
             ->limit($filter['start'],$filter['page_size'])->select();
         foreach($data as $k => $v){
             $data[$k]['add_time'] = date("Y-m-d H:i:s",$v['add_time']);
         }
         $result['lists']=$data;
         return $result;
     }
    /**
     * 获取单条结果集
     * @return mixed
     */
    public function show($id){
        $data = $this->where(['article_id'=>$id])->find();
        $data['add_time'] = date("Y-m-d H:i:s",$data['add_time']);
        return $data;
    }
    /**
     * 删除
     */
    public function destroy($id){
          $info=$this->show($id);
          if($info){
              manage_log('删除了'.$info['title']);
              return $this->where(['article_id'=>$info['article_id']])->delete();
          }
          return false;
    }
    /**
     * 修改
     */
    public function edit($data)
    {
        return $this->where(array('brand_id'=>$data['brand_id']))->save($data);
    }
    /**
     * 取得品牌列表
     * @return array 品牌列表 id => name
     */

    public function get_brands(){
        $result = $this->field('brand_id,brand_name')->select();
        return $result;
    }
    //获取分类名称
    public function get_article_name(){
        $cat_name = $this->table('__ARTICLE_CAT__')->field('cat_id,cat_name,parent_id')->order('cat_id desc')->select();
        $data = $this->get_tree($cat_name);
        return $data;
    }
    //无限极分类
    public function get_tree($list,$html = '|—', $pid = 0, $num = 0){
        $arr = array();
        foreach($list as $v){
            if($v['parent_id'] == $pid){
                $v['level'] = $num + 1;//可做自定义级别使用
                $v['cat_name'] = str_repeat($html, $num).$v['cat_name'];//填充字符串个数
                $arr[] = $v;
                $arr = array_merge($arr, $this->get_tree($list, $html, $v['cat_id'], $num + 1));//递归把子类压入父类数组后
            }
        }
        return $arr;
    }
    //是否显示
    public function is_open(){
        //获取ID
        $id = intval(I("article_id", ''));
        $data['is_open'] = trim(I("is_open", ''));
        if(!$data && !$id){
            return false;
        }
        if($data['is_open'] == '1'){
            $data['is_open'] = '0';
        }else{
            $data['is_open'] = '1';
        }
        return $this->where(array('article_id'=>"$id"))->save($data);
    }
    /**
     * 添加验证文章名称是否存在
     */
    public function show_name($data){
        $data = $this->where(array('title'=>$data))->find();
        return $data;
    }
    /**
     * 修改验证文章名称是否存在
     */
    public function show_edit_name($data){
        $data = $this->where("article_id !={$data['article_id']} and title='{$data['title']}'")->find();
        return $data;
    }
}
