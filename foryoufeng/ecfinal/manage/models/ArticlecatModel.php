<?php
/**
 *品牌
 * Created by PhpStorm.
 * User: root
 * Date: 8/16/16
 * Time: 8:26 PM
 * Vsersion:2.0.0
 */
class ArticlecatModel extends CommonModel{
    protected $tableName='article_cat';
    /**
     * 获取多条结果集
     * @return mixed
     */
     public function all(){
         $result=[];
         $data=$this->order('cat_id desc')->select();

         //将类名分类
         $data = $this->get_tree($data);
         //获取分类信息
         $type = $this->get_type();
         foreach($data as $k=>$v){
             $data[$k]['cat_type'] = $type[$v['cat_type']];
         }
         $result['lists']=$data;
         return $result;
     }
    /**
     * 获取单条结果集
     * @return mixed
     */
    public function show($id){
        $data = $this->where(['cat_id'=>$id])->find();
        return $data;
    }
    /**
     * 删除
     */
    public function destroy($id){
          $info=$this->show($id);
          if($info){
              manage_log('删除了'.$info['cat_name']);
              return $this->where(['cat_id'=>$info['cat_id']])->delete();
          }
          return false;
    }
    /**
     * 修改
     */
    public function edit($data)
    {
        return $this->where(array('cat_id'=>$data['cat_id']))->save($data);
    }

    public function get_tree($list,$html = '|—', $pid = 0, $num = 0){
        $arr = array();
        foreach($list as $v){
            if($v['parent_id'] == $pid){
                $v['level'] = $num;//可做自定义级别使用
                $v['cat_name'] = str_repeat($html, $num).$v['cat_name'];//填充字符串个数
                $arr[] = $v;
                $arr = array_merge($arr, $this->get_tree($list, $html, $v['cat_id'], $num + 1));//递归把子类压入父类数组后
            }
        }
        return $arr;
    }
    public function show_nav()
    {
        //获取ID
        $id = intval(I("cat_id", ''));
        $data['show_in_nav'] = trim(I("show_in_nav", ''));
        if(!$data && !$id){
            return false;
        }
        if($data['show_in_nav'] == '1'){
            $data['show_in_nav'] = '0';
        }else{
            $data['show_in_nav'] = '1';
        }
        return $this->where(array('cat_id'=>"$id"))->save($data);

    }
    //分类信息
    public function get_type()
    {
        $result = $this->field('cat_type')->group('cat_type')->select();
        array_unshift($result,array('cat_type'=>'0'));
        unset($result[0]);
//        print_r($result);exit;
        foreach($result as $key => $value){
            if($result[$key]['cat_type'] == '1'){
                $data[$key]='普通分类';
            }else if($result[$key]['cat_type'] == '2'){
                $data[$key] = '系统分类';
            }else if($result[$key]['cat_type'] == '3'){
                $data[$key] = '网店信息';
            }else if($result[$key]['cat_type'] == '4'){
                $data[$key] = '帮助分类';
            }else if($result[$key]['cat_type'] == '5'){
                $data[$key] = '网店帮助';
            }
        }
        return $data;
    }
    public function get_cat_name($id){
        if($id){
           $where['cat_id'] = array('neq',$id);
        }
        $data = $this->field('cat_name,cat_id,parent_id')->where($where)->order('cat_id desc')->select();
        $data = $this->get_tree($data);
        return $data;
    }
    /**
     * 添加验证文章名称是否存在
     */
    public function show_name($data){
        $data = $this->where(array('cat_name'=>$data))->find();
        return $data;
    }
    /**
     * 修改验证文章名称是否存在
     */
    public function show_edit_name($data){
        $data = $this->where("cat_id !={$data['cat_id']} and cat_name='{$data['cat_name']}'")->find();
        return $data;
    }
}
