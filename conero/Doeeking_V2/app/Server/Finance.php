<?php
namespace app\Server;
use hyang\Logic;
use think\Db;
use app\Server\Conero;
class Finance extends Logic
{    
    public $cro;
    public function init(){
        $this->cro = new Conero();
    }    
    // 财务登账-事务甲方
    public function master($type=null,$selected=null)
    {
        $map = ['center_id'=>$this->center_id];
        if(empty($type)){
          $map['type'] = '0M';
          $map['is_use'] = 'Y';
        }
        elseif(is_array($type)) $map = array_merge($map,$type);
        elseif('sider' == $type) $map = '`type` != "0M" and `is_use`="Y" and `center_id`="'.($this->center_id).'"';
        elseif('all' == $type) $map = '`is_use`="Y" and `center_id`="'.($this->center_id).'"';        
        elseif(is_string($type)) $map['type'] = $type;
        $data = Db::table('finc_organ')->where($map)->select();
        if(empty($data)){
            $this->initForgan();
            $data = Db::table('finc_organ')->where($map)->select();
        }
        $html = '<option></option>';
        foreach($data as $v)
        {
            $html .= '<option value="'.$v['id'].'"'.(($selected && $selected == $v['id'])? ' selected':'').'>'.$v['name'].'</option>';
        }
        return $html;
    }
    // 财务登账-用途
    public function purpose($select=null,$selected=null)
    {
        $data = $this->cro->_const('finc_');
        $html = $select? $select:'<select name="purpose">';
        $html .= '<option></option>';
        foreach($data as $v)
        {
            $html .= '<option value="'.$v['plus_name'].'"'.(($selected && $selected == $v['plus_name'])? ' selected':'').'>'.$v['plus_desc'].'</option>';
        }
        $html .= '</select>';
        return $html;
    }
    // 当发现没有 财务机构的话-自动生成"现金"虚拟账号
    public function initForgan()
    {
        $tb = 'finc_organ';
        $map = ['name'=>'现金','center_id'=>$this->center_id];
        if($this->dataExist($map,$tb)) return false;
        $data = array_merge($map,['set_date'=>sysdate('date'),'type'=>'0M']);
        return $this->save($data,$tb);
    }
    // 年度财务或月度财务推荐 2017年2月8日 星期三
    // $acc 月度编号>> 2017-08-00/年份-月份-00
    // $check 数据检查类型- account/budget
    // $cid center_id 中心识别码
    public function accRecommend($accno,$check=null,$cid=null){
        $rNo = '';
        // 只针对月度账单编号
        $recom = [];        
        if(substr($accno,-2) == '00'){
            $year = substr($accno,0,4);
            $month = substr($accno,5,7);
            $mInt = intval($month);
            $yaccNo = $year.'-00-00';
            $maccNo = null;   
                           
            // 季度数据生成
            if($mInt<4) $maccNo = $year.'-00-01';                                 // [1-3]
            elseif($mInt<7 && $mInt>3) $maccNo = $year.'-00-02';                  // [4-6]
            elseif($mInt<6 && $mInt>10) $maccNo = $year.'-00-03';                 // [7-9]
            elseif($mInt<9) $maccNo = $year.'-00-04';                             // [10-12]
            $cid = $cid? $cid:$this->center_id;
            $check = $check? strtolower($check):null;   
            switch($check){
                case 'account':
                    if(Db::table('finc_account')->where(['center_id'=>$cid,'acc_no'=>$yaccNo])->count() == 0) $recom['year'] = $yaccNo;
                    if(Db::table('finc_account')->where(['center_id'=>$cid,'acc_no'=>$maccNo])->count() == 0) $recom['month'] = $maccNo;
                    break;
                case 'budget':
                    if(Db::table('finc_budget')->where(['center_id'=>$cid,'bud_no'=>$yaccNo])->count() == 0) $recom['year'] = $yaccNo;
                    if(Db::table('finc_budget')->where(['center_id'=>$cid,'bud_no'=>$maccNo])->count() == 0) $recom['month'] = $maccNo;
                    break;
                default:
                    $recom = [
                        'year'=>$yaccNo,
                        'month'=>$maccNo
                    ];
            }            
        }
        return $recom;
    }
}
