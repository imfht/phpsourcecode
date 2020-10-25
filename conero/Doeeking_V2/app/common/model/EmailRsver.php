<?php
/*
 * 邮箱接收器
 * 2017年3月1日 星期三
 */
namespace app\common\model;
use app\common\model\BaseModel;
class EmailRsver extends BaseModel{
    protected $table = 'sys_email_rsv';
    protected $pk = 'listno';
    // 获取 来自发送方式所有邮箱
    // select * from sys_email_rsv a left join sys_email_rel b on a.sd_email = b.email where a.sd_email is not null
    public function getSendEmlist($code=null)
    {
        $code = $code? $code:uInfo('code');
        $data = $this->db()
                ->alias('a')
                ->join('sys_email_rel b','a.rcv_email = b.email','LEFT')
                ->where('a.sd_email is not null')
                ->where('b.user_code='.$code)
                ->field('a.sd_email')
                ->group('a.sd_email')
                ->select();
        // println($data);
        return $data;
    }
}