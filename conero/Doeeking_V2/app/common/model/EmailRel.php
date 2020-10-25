<?php
/*
 * 系统邮箱关系映射表
 * 2017年3月1日 星期三
 */
namespace app\common\model;
use app\common\model\BaseModel;
class EmailRel extends BaseModel{
    protected $table = 'sys_email_rel';
    protected $pk = 'listno';
    // 自动登记数据数据 => ['nick'=>'昵称','code'=>'代码']
    public function autoRegisterByCode($uInfo=null)
    {
        $uInfo = $uInfo? $uInfo:uInfo();
        if(empty($uInfo)) return;
        $nick = $uInfo['nick'];
        $email = strtolower($nick).'@yang.com';        
        $code = isset($uInfo['code'])? $uInfo['code']:(db('user_net')->where('user_nick',$nick)->find('user_code'));
        $data = [
            'email'     => $email,
            'user_code' => $code
        ];
        if($this->db()->where($data)->count() > 0) return [1,$email];  // 存在时不保存数据
        if($this->db()->insert($data)){
            $pk = $this->db()->getLastInsID();
            return [$pk,$email];
        }
        return [0,$email];
    }
    // 邮件合法性检测
    // return bool
    public function accountCheck($account,$disAuto=false){
        $account = trim($account);
        $nick = strtolower(substr($account,0,strpos($account,'@')));
        $suffix = strtolower(substr($account,strpos($account,'@')+1));
        $data = uLogic('Conero')->constKV('conero_admin',['mext_nick','mext_self']);
        if($data){
            if($suffix == $data['mext_nick']){
                $qData = db()->query('select count(*) as `hasaccount` from `net_user` where lower(`user_nick`) = "'.$nick.'"');
                $qData = isset($qData[0])? $qData[0]['hasaccount'] : -1;
                if($qData > 0){
                    if(!$disAuto) $this->autoRegisterByCode(['nick'=>$nick]);
                    return true;
                }
                // println($qData == 0);
            }
            elseif($suffix == $data['mext_self']) return true;
        }
        // println($nick,$suffix,$data);
        return false;
    }
}