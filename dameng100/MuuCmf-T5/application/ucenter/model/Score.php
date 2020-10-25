<?php
namespace app\ucenter\Model;

use think\Model;
use think\Db;
/**
 * 用户积分模型
 */
class Score extends Model
{

    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * getTypeList  获取类型列表
     * @param string $map
     * @return mixed
     */
    public function getTypeList($map = '')
    {
        $list = Db::name('ucenter_score_type')->where($map)->order('id asc')->select();

        return $list;
    }

    public function getTypeListByIndex($map = ''){
        $list = Db::name('ucenter_score_type')->where($map)->order('id asc')->select();
        foreach($list as $v)
        {
            $array[$v['id']]=$v;
        }
        return $array;
    }
    /**
     * getType  获取单个类型
     * @param string $map
     * @return mixed
     */
    public function getType($map = '')
    {
        $type = Db::name('ucenter_score_type')->where($map)->find();
        return $type;
    }

    /**
     * addType 增加积分类型
     * @param $data
     * @return mixed
     */
    public function addType($data)
    {
        $db_prefix = Config('database.prefix');
        $res = Db::name('ucenter_score_type')->insert($data);
        $query = "ALTER TABLE  `{$db_prefix}member` ADD  `score" . $res . "` DOUBLE NOT NULL COMMENT  '" . $data['title'] . "'";
        Db::query($query);
        return $res;
    }

    /**
     * delType  删除分类
     * @param $ids
     * @return mixed
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function delType($ids)
    {
        $db_prefix = Config('database.prefix');
        $res = Db::name('ucenter_score_type')->where(array('id' => array(array('in', $ids), array('gt', 4), 'and')))->delete();
        foreach ($ids as $v) {
            if ($v > 4) {
                $query = "alter table `{$db_prefix}member` drop column score" . $v;
                Db::query($query);
            }
        }
        return $res;
    }

    /**
     * editType  修改积分类型
     * @param $data
     * @return mixed
     */
    public function editType($data)
    {
        $db_prefix = Config('database.prefix');
        $res = Db::name('ucenter_score_type')->update($data);
        $query = "alter table `{$db_prefix}member` modify column `score" . $data['id'] . "` FLOAT comment '" . $data['title'] . "';";
        Db::query($query);
        return $res;
    }


    /**
     * getUserScore  获取用户的积分
     * @param int $uid
     * @param int $type
     * @return mixed
     */
    public function getUserScore($uid, $type)
    {
        $score = Db::name('member')->where(['uid' => $uid])->value('score' . $type);
        return $score;
    }

    /**
     * setUserScore  设置用户的积分
     * @param $uids
     * @param $score
     * @param $type
     * @param string $action
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function setUserScore($uids, $score, $type, $action = 'inc',$action_model ='',$record_id=0,$remark='')
    {

        $model = Db::name('member');
        switch ($action) {
            case 'inc':
                $score = abs($score);
                $res = $model->where(array('uid' => array('in', $uids)))->setInc('score' . $type, $score);
                break;
            case 'dec':
                $score = abs($score);
                $res = $model->where(array('uid' => array('in', $uids)))->setDec('score' . $type, $score);
                break;
            case 'to':
                $res = $model->where(array('uid' => array('in', $uids)))->setField('score' . $type, $score);
                break;
            default:
                $res = false;
                break;
        }

        if(!($action != 'to' && $score == 0)){
            $this->addScoreLog($uids,$type,$action,$score,$action_model,$record_id,$remark);
        }

        foreach ($uids as $val) {
           $this->cleanUserCache($val,$type);
        }
        unset($val);
        return $res;
    }

    /**
     * 添加积分日志
     * @param [type]  $uid       [description]
     * @param [type]  $type      [description]
     * @param string  $action    [description]
     * @param integer $value     [description]
     * @param string  $model     [description]
     * @param integer $record_id [description]
     * @param string  $remark    [description]
     */
    public function addScoreLog($uid, $type, $action='inc',$value=0, $model='',$record_id=0,$remark='')
    {
        $uid = is_array($uid) ? $uid : explode(',',$uid);
        foreach($uid as $v){
            $score =  Db::name('Member')->where(['uid'=>$v])->value('score'.$type);

            $data['uid'] = $v;
            $data['ip'] = request()->ip(1);
            $data['type'] = $type;
            $data['action'] = $action;
            $data['value'] = $value;
            $data['model'] = $model;
            $data['record_id'] = $record_id;
            $data['finally_value'] = $score;
            $data['remark'] = $remark;
            $data['create_time'] = time();
            Db::name('score_log')->insert($data);
        }

        return true;
    }
    /**
     * 清除用户缓存
     * @param  [type] $uid  [description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function cleanUserCache($uid,$type){

        $uid = is_array($uid) ? $uid : explode(',',$uid);
        $type = is_array($type)?$type:explode(',',$type);
        foreach($uid as $val){
            foreach($type as $v){
                clean_query_user_cache($val, 'score' . $v);
            }
            clean_query_user_cache($val, 'title');
        }
    }

    public function getAllScore($uid)
    {
        $typeList = $this->getTypeList(array('status'=>1));
        $return = array();
        foreach($typeList as $key => &$v){
            $v['value'] = $this->getUserScore($uid,$v['id']);
            $return[$v['id']] = $v;

        }
        unset($v);
        return $return;
    }

}