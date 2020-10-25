<?php
// 公有model
// +----------------------------------------------------------------------
// | PHP version 5.3+                
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\model;

use think\Model;

class BaseModel extends Model
{
    /**
     * @param  array $data 入库数据
     * @param  string $id 主键健值
     * @return 执行结果
     */
	public function editData($data,$id='id')
    {
        if($data[$id]){
            unset($data['create_time']);//编辑状态下去除改变创建时间
            $res=$this->save($data);
        }else{
            //unset($data[$id]);
            $res=$this->add($data);
        }
        return $res;
    }

    /**
     * @param  array $map 查询过滤
     * @param  integer $page 分页值
     * @param  string $order 排序参数
     * @param  string $field 结果字段
     * @param  integer $r 每页数量
     * @return 结果集
     */
    public function getListByPage($map,$order='sort asc,update_time desc',$field='*',$r=20)
    {
        //$page=$this->where($map)->paginate($r);
        //$list=$this->page($page,$r)->order($order)->field($field)->select();
        $list=$this->where($map)->order($order)->field($field)->paginate($r,false,['query' => request()->param()]);
        $page=$list->render();
        //return $list;
        return array($list,$page);
    }

    /**
     * @param  array $map 查询过滤
     * @param  string $field 获取的字段
     * @param  string $order 排序
     * @return 结果集
     */
    public function getList($map,$field='*',$order='sort asc')
    {
        $lists = $this->where($map)->field($field)->order($order)->select();
        return $lists;
    }

    /**
     * 通过$map获取列表
     * @param array $map 查询条件
     * @param $order 排序
     * @param null $fields 查询字段，null表示全部字段
     * @return mixed 结果列表
     */

    public function selectByMap($map=array(),$order=null,$fields=null){
        $order=$order?$order:"id asc";
        if($fields==null){
            $list=$this->where($map)->order($order)->select();
        }else{
            $list=$this->where($map)->order($order)->field($fields)->select();
        }
        return $list;
    }

    /**
     * * 通过$map获取单条值
     * @param array $map 查询条件
     * @param string $order 排序
     * @param null $fields 查询字段，null表示全部字段
     * @return mixed 结果
     */
    public function getByMap($map=array(),$order,$fields=null){
        $order=$order?$order:"id asc";
        if($fields==null){
            $data=$this->where($map)->order($order)->find();
        }else{
            $data=$this->where($map)->order($order)->field($fields)->find();
        }
        return $data;
    }

    /**
     * 将格式数组转换为树
     *
     * @param array $list
     * @param integer $level 进行递归时传递用的参数
     */
    private $formatTree; //用于树型数组完成递归格式的全局变量
    private function _toFormatTree($list,$level=0,$title = 'title') {
        foreach($list as $key=>$val){
            $tmp_str=str_repeat("&nbsp;",$level*2);
            $tmp_str.="└";

            $val['level'] = $level;
            $val['title_show'] =$level==0?$val[$title]."&nbsp;":$tmp_str.$val[$title]."&nbsp;";
                // $val['title_show'] = $val['id'].'|'.$level.'级|'.$val['title_show'];
            if(!array_key_exists('_child',$val)){
                array_push($this->formatTree,$val);
            }else{
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push($this->formatTree,$val);
                   $this->_toFormatTree($tmp_ary,$level+1,$title); //进行下一层递归
                }
            }
            return;
        }

    public function toFormatTree($list,$title = 'title',$pk='id',$pid = 'pid',$root = 0){
        $list = list_to_tree($list,$pk,$pid,'_child',$root);
        $this->formatTree = array();
        $this->_toFormatTree($list,0,$title);
        return $this->formatTree;
    }
}