<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-09 14:52:10
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-11 23:07:20
 */

namespace common\services\common;

use api\models\DdApiAccessToken;
use common\models\DdMember;
use Yii;
use yii\base\InvalidConfigException;
use common\queues\MailerJob;
use common\services\BaseService;
use yii\data\Pagination;

use function PHPSTORM_META\map;

class MemberService extends BaseService
{
    private $member_id = 1;
    
    public function setAccessToken($token)
    {   
        global $_GPC;
        $tokens = DdApiAccessToken::find()->where(['access_token'=>$token])->one();
        $this->member_id = $tokens['member_id'];
    }

    // 全局设置商家id
    public function setmember_id($id)
    {
        $this->member_id = $id;
    }

     // 全局设置商家id
     public function getmember_id()
     {   
         return $this->member_id;
     }

    public  function baseInfo()
    {
        $member_id =  $this->member_id;
        $bloc_id   = Yii::$app->params['bloc_id'];
        $store_id   = Yii::$app->params['store_id'];
        $list =  DdMember::find()->with(['account','group','fans'])->where([
            'member_id'=>$member_id,
            'bloc_id'=>$bloc_id,
            'store_id'=>$store_id,
            ])->asArray()->one();
        // return DdMember::find()->with(['account','group','fans'])->where([
        //     'member_id'=>$member_id,
        //     'bloc_id'=>$bloc_id,
        //     'store_id'=>$store_id,
        //     ])->createCommand()->getRawSql();
        return $list;    
    }

    // 修改用户基础信息
    public static function editInfo($member_id,$fields=[])
    {
        $DdMember = DdMember::find()->where(['member_id'=>$member_id])->one();
        $res = $DdMember->update($fields);    
        return $res;
    }

    // 获取所有的会员信息
    public static function memberLists($where,$memberAlias,$joinModel,$joinfiled,$fields=[],$page,$pageSize=20)
    {
        $selectFs = [];
        foreach ($fields as $key => $value) {
            $selectFs[] = '`u`.'.$value;
        }
        $memberTablename = DdMember::tableName(); 
        $joinTablename   = $joinModel::tableName();
        $query = DdMember::find()->where($where)
            ->alias($memberAlias)
            ->with(['account','group','fans'])
            ->leftJoin($joinTablename.' AS u','u.'.$joinfiled.' = '.$memberAlias.'.member_id')
            ->select([$memberAlias.'.*',implode(',', $selectFs)]);
       
        $count = $query->count();

        // 使用总数来创建一个分页对象
        $pagination = new Pagination([
            'totalCount' => $count,
            'pageSize' => $pageSize,
            'page' => $page-1,
        ]);

        $list = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();
        
        foreach ($list as $key => $value) {
            $list[$key]['status_str'] = $value['status']==0?'正常':'拉黑';
            $list[$key]['create_time'] = date('Y-m-d H:i',$value['create_time']);
        }
        return ['count'=>$count,'list'=>$list];    
    }

}