<?php
namespace app\admin\model;

use think\Model;

/**
 * 身份模型
 */
class Role extends Model
{

    protected $rule = [
        'name'  => ['require','max'=>25,'checkName'=>true],
        'title' => ['require', 'max'=>64],
    ];

    /**
     * 验证身份名(只能有字母和下划线组成)
     */
    public function checkName($value,$rule){
        if(!preg_match('/^[_a-z]*$/i',$value)){
            return false;
        }
        return true;
    }
    
    protected $auto = ['status', 1];

    protected $autoWriteTimestamp = true;
    

    /**
     * 分页按照$map获取列表
     * @param array $map 查询条件
     * @param int $page 页码
     * @param $order 排序
     * @param null $fields 查询字段，null表示全部字段
     * @param int $r 每页条数
     * @return mixed 一页结果列表
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function selectPageByMap($map=array(),$page=1,$r=20,$order,$fields=null){
        $order=$order?$order:"id asc";
        if($fields==null){
            $list=$this->where($map)->order($order)->page($page,$r)->select();
        }else{
            $list=$this->where($map)->order($order)->field($fields)->page($page,$r)->select();
        }
        $totalCount=$this->where($map)->count();
        return array($list,$totalCount);
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
            $list=collection($this->where($map)->order($order)->select())->toArray();
        }else{
            $list=collection($this->where($map)->order($order)->field($fields)->select())->toArray();
        }
        return $list;
    }

    /**
     * 通过$map获取单条值
     * @param array $map 查询条件
     * @param string $order 排序
     * @param null $fields 查询字段，null表示全部字段
     * @return mixed 结果
     */
    public function getByMap($map=[],$order='id desc',$fields=null){
        //$order=$order?$order:"id asc";
        if($fields==null){
            $data=$this->where($map)->order($order)->find();
        }else{
            $data=$this->where($map)->order($order)->field($fields)->find();
        }
        return $data;
    }


} 