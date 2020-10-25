<?php
namespace Muuevent\Model;
use Think\Model;
use Think\Page;

class MuueventModel extends Model{
    protected $_validate = array(
        array('title', '1,100', '标题长度不合法', self::EXISTS_VALIDATE, 'length'),
        array('explain', '1,40000', '内容长度不合法', self::EXISTS_VALIDATE, 'length'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
        array('uid', 'is_login',3, 'function'),
    );

    public function getListByPage($map,$page=1,$order='create_time desc',$field='*',$r=20)
    {
        $totalCount=$this->where($map)->count();
        if($totalCount){
            $list=$this->where($map)->page($page,$r)->order($order)->field($field)->select();
        }
        return array($list,$totalCount);
    }

    public function getDataById($id)
    {
        $map['id'] = $id;
        $result = $this->where($map)->find();
        return $result;
    }

    public function editData($data)
    {
        $data=$this->create();
        if($data['id']){
            $res=$this->save($data);
        }else{
            $res=$this->add($data);
        }
        return $res;
    }


    /**
     * 真实删除
     * @param [type] $ids [description]
     */
    public function setTrueDel($ids)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $map['id']=array('in',$ids);
        $res=$this->where($map)->delete();
        return $res;
    }
    
    /**
     * 接口签名
     * @param  [type] $timestamp [description]
     * @param  [type] $noce      [description]
     * @return [type]            [description]
     */
    public function createSignature($timestamp,$noce){
        $arr['timestamp'] = $timestamp;
        $arr['noce'] = $noce;
        $arr['secret'] = modC('RESTFUL_CONFIG_SECRET','','Restful');
        //按照首字母大小写顺序排序
        sort($arr,SORT_STRING);
        //拼接成字符串
        $str = implode($arr);
        //进行加密
        $signature = sha1($str);
        $signature = md5($signature);
        //转换成大写
        $signature = strtoupper($signature);
        return $signature;
    }
    

}
