<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;
use Admin\Model\AuthGroupModel;

/**
 * 文档基础模型
 */
class PageModel extends Model{

    /* 自动验证规则 */
    protected $_validate = array(
        array('name', '/^[a-zA-Z]\w{0,39}$/', '文档标识不合法', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', 'checkName', '标识已经存在', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,80', '标题长度不能超过80个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('description', '1,140', '简介长度不能超过140个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('title', 'htmlspecialchars', self::MODEL_BOTH, 'function'),
        array('description', 'htmlspecialchars', self::MODEL_BOTH, 'function'),
        array('view', 0, self::MODEL_INSERT),
        array('comment', 0, self::MODEL_INSERT),
        array('create_time', 'getCreateTime', self::MODEL_BOTH,'callback'),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', 'getStatus', self::MODEL_BOTH, 'callback'),
    );

    /**
     * 获取详情页数据
     * @param  integer $id 文档ID
     * @return array       详细数据
     */
    public function detail($id){
        /* 获取基础数据 */
        $info = $this->field(true)->find($id);
        if(!(is_array($info) || 1 !== $info['status'])){
            $this->error = '文档被禁用或已删除！';
            return false;
        }

        return $info;
    }

    /**
     * 新增或更新一个文档
     * @param array  $data 手动传入的数据
     * @return boolean fasle 失败 ， int  成功 返回完整的数据
     * @author huajie <banhuajie@163.com>
     */
    public function update($data = null){
        /* 获取数据对象 */
        $data = $this->token(false)->create($data);
        if(empty($data)){
            return false;
        }

        /* 添加或新增基础内容 */
        if(empty($data['id'])){ //新增数据
            $id = $this->add(); //添加基础内容
            if(!$id){
                $this->error = '新增基础内容出错！';
                return false;
            }
        } else { //更新数据
            $status = $this->save(); //更新基础内容
            if(false === $status){
                $this->error = '更新基础内容出错！';
                return false;
            }
        }

        hook('documentSaveComplete', array('model_id'=>$data['model_id']));

        //行为记录
        if($id){
            action_log('add_page', 'page', $id, UID);
        }

        //内容添加或更新完成
        return $data;
    }

    /**
     * 获取数据状态
     * @return integer 数据状态
     */
    protected function getStatus(){
        $id = I('post.id');
        if(empty($id)){	//新增
            $status = 1;
        }else{				//更新
            $status = $this->getFieldById($id, 'status');
            //编辑草稿改变状态
            if($status == 3){
                $status = 1;
            }
        }
        return $status;
    }

    /**
     * 创建时间不写则取当前时间
     * @return int 时间戳
     * @author huajie <banhuajie@163.com>
     */
    protected function getCreateTime(){
        $create_time    =   I('post.create_time');
        return $create_time?strtotime($create_time):NOW_TIME;
    }


    /**
     * 检查标识是否已存在(只需在同一根节点下不重复)
     * @param string $name
     * @return true无重复，false已存在
     * @author huajie <banhuajie@163.com>
     */
    protected function checkName(){
        $name        = I('post.name');
        $id          = I('post.id', 0);

        $map = array('name' => $name, 'id' => array('neq', $id), 'status' => array('neq', -1));

        $res = $this->where($map)->getField('id');
        if ($res) {
            return false;
        }
        return true;
    }

    /**
     * 生成不重复的name标识
     * @author huajie <banhuajie@163.com>
     */
    private function generateName(){
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789';	//源字符串
        $min = 10;
        $max = 39;
        $name = false;
        while (true){
            $length = rand($min, $max);	//生成的标识长度
            $name = substr(str_shuffle(substr($str,0,26)), 0, 1);	//第一个字母
            $name .= substr(str_shuffle($str), 0, $length);
            //检查是否已存在
            $res = $this->getFieldByName($name, 'id');
            if(!$res){
                break;
            }
        }
        return $name;
    }


    /**
     * 删除状态为-1的数据（包含扩展模型）
     * @return true 删除成功， false 删除失败
     * @author huajie <banhuajie@163.com>
     */
    public function remove(){
        //查询假删除的基础数据
        if ( is_administrator() ) {
            $map = array('status'=>-1);
        }

        //删除基础数据
        $ids = array_merge( $base_ids, (array)array_column($orphan,'id') );
        if(!empty($ids)){
            $res = $this->where( array( 'id'=>array( 'IN',trim(implode(',',$ids),',') ) ) )->delete();
        }

        return $res;
    }
}