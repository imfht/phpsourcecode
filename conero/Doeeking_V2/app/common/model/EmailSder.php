<?php
/*
 * 邮箱发送器
 * 2017年3月1日 星期三
 */
namespace app\common\model;
use app\common\model\BaseModel;
use hyang\Util;
class EmailSder extends BaseModel{
    protected $table = 'sys_email_sd';
    protected $pk = 'listno';
    // select * from sys_email_sd a left join sys_email_rel b on a.rcv_email = b.email
    public function sendEmail($data)
    {
        $ret = [
            'error' => -1 ,
            'msg'   => '邮件发送出错'
        ];
        $rcvEm = $data['rcv_email'];
        $rcvEm = Util::unspace($rcvEm);
        if(substr_count($rcvEm,';') > 0){
            $rcvList = explode(';',$rcvEm);
            unset($data['rcv_email']);
            $rsver = model('EmailRsver');
            $ctt = 0;$all = count($rcvList);
            foreach($rcvList as $v){
                $svdata = $data;
                $svdata['rcv_email'] = $v;
                if($this->db()->insert($data)){
                    $pk = $this->db()->getLastInsID();        
                    $rsvRcd = $svdata;    
                    $rsvRcd['sd_listno'] = $pk;
                    if($rsver->insert($rsvRcd)) $ctt += 1;
                }
            }
            $ret = [
                'error' => ($ctt>0? 1: 0),
                'msg'   => ($ctt>0? '邮件发送成功('.$ctt.'/'.$all.')': '十分遗憾，邮件未能发送到收件人。本次公共提交数据'.$all.'条')
            ];
        }
        elseif($this->db()->insert($data)){
            $pk = $this->db()->getLastInsID();
            $rsver = model('EmailRsver');
            $rsvRcd = $data;
            $rsvRcd['sd_listno'] = $pk;
            if($rsver->insert($rsvRcd)) 
                $ret = [
                    'error' => 0 ,
                    'msg'   => '邮件发送成功'
                ];
            else
                $ret = [
                    'error' => 1 ,
                    'msg'   => '十分遗憾，邮件未能发送到收件人'
                ];
        }
        return $ret;
    }
}