<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2015 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: UCT <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace Ucuser\Model;
use Think\Model;

/**
 * Class UcuserScoreModel   粉丝积分模型
 * @package Ucuser\Model
 * @author:patrick contact@uctoo.com
 */
class UcuserScoreModel extends Model
{

    private $typeModel =null;
    protected function _initialize()
    {
        parent::_initialize();
        $this->typeModel =  M('ucenter_score_type');
    }

    /**
     * getTypeList  获取类型列表
     * @param string $map
     * @return mixed
     * @author:patrick contact@uctoo.com
     */
    public function getTypeList($map=''){
       $list = $this->typeModel->where($map)->order('id asc')->select();

       return $list;
   }

    /**
     * getType  获取单个类型
     * @param string $map
     * @return mixed
     * @author:patrick contact@uctoo.com
     */
    public function getType($map=''){
        $type = $this->typeModel->where($map)->find();
        return $type;
    }

    /**
     * addType 增加积分类型
     * @param $data
     * @return mixed
     * @author:patrick contact@uctoo.com
     */
    public function addType($data){
        $db_prefix = C('DB_PREFIX');
       $res = $this->typeModel->add($data);
       $query = "ALTER TABLE  `{$db_prefix}ucuser` ADD  `score".$res."` FLOAT NOT NULL COMMENT  '".$data['title']."'";
       D()->execute($query);
       return $res;
    }

    /**
     * delType  删除分类
     * @param $ids
     * @return mixed
     * @author:patrick contact@uctoo.com
     */
    public function delType($ids){
        $db_prefix = C('DB_PREFIX');
        $res = $this->typeModel->where(array('id'=>array(array('in',$ids),array('gt',4),'and')))->delete();
        foreach($ids as $v){
            if($v>4){
                $query = "alter table `{$db_prefix}ucuser` drop column score".$v;
                D()->execute($query);
            }
      }
        return $res;
    }

    /**
     * editType  修改积分类型
     * @param $data
     * @return mixed
     * @author:patrick contact@uctoo.com
     */
    public function editType($data){
        $db_prefix = C('DB_PREFIX');
        $res = $this->typeModel->save($data);
        $query = "alter table `{$db_prefix}ucuser` modify column `score".$data['id']."` FLOAT comment '".$data['title']."';";
        D()->execute($query);
        return $res;
    }


    /**
     * getUserScore  获取用户的积分
     * @param int $mid
     * @param int $type
     * @return mixed
     * @author:patrick contact@uctoo.com
     */
    public function getUserScore($mid,$type){
        $model = D('Common/Ucuser');
        $score = $model->where(array('mid'=>$mid))->getField('score'.$type);
        return $score;
    }

    /**
     * setUserScore  设置用户的积分
     * @param $mids
     * @param $score
     * @param $type
     * @param string $action
     * @author:patrick contact@uctoo.com
     */
    public function setUserScore($mids,$score,$type,$action='inc'){

        $model = D('Common/Ucuser');
        switch($action){
            case 'inc':
                $score = abs($score);
                $res = $model->where(array('mid'=>array('in',$mids)))->setInc('score'.$type,$score);
                break;
            case 'dec':
                $score = abs($score);
                $res = $model->where(array('mid'=>array('in',$mids)))->setDec('score'.$type,$score);
                break;
            case 'to':
                $res = $model->where(array('mid'=>array('in',$mids)))->setField('score'.$type,$score);
                break;
            default:
                $res = false;
                break;
        }
        foreach($mids as $val){
            clean_query_user_cache($val,'score'.$type);
        }
        unset($val);
        return $res;
    }


}