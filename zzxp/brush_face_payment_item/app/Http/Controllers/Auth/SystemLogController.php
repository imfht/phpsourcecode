<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
//use App\Lib\Api\ShareCarApi AS ShareCarApi;
//
use App\Lib\Api\Api AS Api;
use App\Lib\Api\AdminApi As AdminApi;
use View,Session,Redirect,Response;
use Excel;
/**
 * Created by PhpStorm.
 * User: xzl
 * Date: 14/10/23
 * Time: 下午4:16
 */
class SystemLogController extends Controller {

    public function __construct(Request $req){
        //$this->check();
        //$this->share_car = new ShareCarApi;
        //
        $this->member = new Api;
        $this->admin = new AdminApi;
        $this->request = $req;
    }
    //充值记录
    public function MoneyLog(){
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $member_id = $this->request->get('member_id','');
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;
        }
        $param['member_id'] = !empty($member_id) ? $member_id : '';
        $param['type'] = '1';//充值订单
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';

        $money_log = $this->member->getPowerMoneyLog($param);
        // print_r($money_log);exit();
        return \View::make('system_log.moneylog',array('systemLog'=>$money_log['result'],'total'=>$money_log['total'],'start_time'=>$start_time,'end_time'=>$end_time,'size'=>$param['size'],'member_id'=>$member_id,'search'=>$this->request->all(),'$member_id'=>$member_id,'title'=>'充值记录'));
    }

    //消费记录
    public function ConsumeMoneyLog(){
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $member_id = $this->request->get('member_id','');
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;
        }
        $param['member_id'] = !empty($member_id) ? $member_id : '';
        $param['type'] = '-1';//充值订单
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';

        $money_log = $this->member->getPowerMoneyLog($param);
        // print_r($money_log);exit();
        return \View::make('system_log.moneylog',array('systemLog'=>$money_log['result'],'total'=>$money_log['total'],'start_time'=>$start_time,'end_time'=>$end_time,'size'=>$param['size'],'member_id'=>$member_id,'search'=>$this->request->all(),'$member_id'=>$member_id,'title'=>'消费记录'));
    }

    //积分记录 暂缓
    public function integralLog()
    {
        //integrallog
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $member_id = $this->request->get('member_id','');
        $operation_type = $this->request->get('operation_type','');
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;
        }
        $param['member_id'] = !empty($member_id) ? $member_id : '';
        $param['type'] = !empty($operation_type) ? $operation_type : '';
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';

        $money_log = $this->member->getIntegralLog($param);
        return \View::make('system_log.integrallog',array('systemLog'=>$money_log['result'],'total'=>$money_log['total'],'start_time'=>$start_time,'end_time'=>$end_time,'size'=>$param['size'],'member_id'=>$member_id,'search'=>$this->request->all(),'$member_id'=>$member_id,'operation_type'=>$operation_type));
    }

    //提现记录
    public function withdrawalLog()
    {
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $member_id = $this->request->get('member_id','');
        $operation_type = $this->request->get('operation_type','');
        $operation_status = $this->request->get('operation_status','');
        $check_status = $this->request->get('check_status','');
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;
        }
        $param['member_id'] = !empty($member_id) ? $member_id : '';
        $param['type'] = !empty($operation_type) ? $operation_type : '';
        $param['status'] = !empty($operation_status) ? $operation_status : '';
        if(!empty($check_status) && $check_status == 1){
            $param['$check_status'] = 1;
        }elseif(!empty($check_status) && $check_status == 2){
            $param['$check_status'] = 0;
        }
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';

        $money_log = $this->member->getWithdraw($param);

        return \View::make('system_log.withdrawallog',array('systemLog'=>$money_log['result'],'total'=>$money_log['total'],'start_time'=>$start_time,'end_time'=>$end_time,'size'=>$param['size'],'member_id'=>$member_id,'search'=>$this->request->all(),'$member_id'=>$member_id,'operation_type'=>$operation_type,'operation_status'=>$operation_status));
    }

    //佣金记录
    public function CommissionLog()
    {
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $member_id = $this->request->get('member_id','');
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;
        }
        $param['member_id'] = !empty($member_id) ? $member_id : '';
        // $param['typein'] = [5,6,7];//佣金分类
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';

        $money_log = $this->member->getIntroduceLog($param);
        return \View::make('system_log.commissionlog',array('systemLog'=>$money_log['result'],'total'=>$money_log['total'],'start_time'=>$start_time,'end_time'=>$end_time,'size'=>$param['size'],'member_id'=>$member_id,'search'=>$this->request->all(),'member_id'=>$member_id));
    }

    //售后记录
    public function AftersaleLog()
    {
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $member_id = $this->request->get('member_id','');
        $pile_id = $this->request->get('pile_id','');
//        $operation_status = $this->request->get('operation_status','4');
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;
        }
        $param['member_id'] = !empty($member_id) ? $member_id : '';
        $param['pile_id'] = !empty($pile_id) ? $pile_id : '';
//        $param['status'] = !empty($operation_status) && $operation_status != 4 ? $operation_status : '';
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';

        $money_log = $this->member->getServiceLog($param);
        return \View::make('system_log.aftersalelog',array('systemLog'=>$money_log['result'],'total'=>$money_log['total'],'start_time'=>$start_time,'end_time'=>$end_time,'size'=>$param['size'],'member_id'=>$member_id,'search'=>$this->request->all(),'$member_id'=>$member_id,'pile_id'=>$pile_id));
    }

    //支付记录
    public function MoneyOrder(){
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $member_id = $this->request->get('member_id','');
        $operation_type = $this->request->get('operation_type','');
        $operation_status = $this->request->get('operation_status','');
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;
        }
        $param['member_id'] = !empty($member_id) ? $member_id : '';
        $param['type'] = !empty($operation_type) ? $operation_type : '';
        $param['status'] = !empty($operation_status) ? $operation_status : '';
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';

        $money_log = $this->member->getMoneyOrder($param);

        return \View::make('system_log.moneyorder',array('systemLog'=>$money_log['result'],'total'=>$money_log['total'],'start_time'=>$start_time,'end_time'=>$end_time,'size'=>$param['size'],'member_id'=>$member_id,'search'=>$this->request->all(),'$member_id'=>$member_id,'operation_type'=>$operation_type,'operation_status'=>$operation_status));
    }

    //故障记录
    public function faultLog()
    {
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $pile_id = $this->request->get('pile_id','');
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;
        }
        $param['pile_id'] = !empty($pile_id) ? $pile_id : '';
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';

        $money_log = $this->member->getPileWarn($param);
        return \View::make('system_log.faultlog',array('systemLog'=>$money_log['result'],'total'=>$money_log['total'],'start_time'=>$start_time,'end_time'=>$end_time,'size'=>$param['size'],'pile_id'=>$pile_id,'search'=>$this->request->all()));
    }

    public function index(){
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        $operation_type=$this->request->get('operation_type','');
        $content_id=$this->request->get('content_id','');


        //管理员名
        $operation_name=$this->request->get('operation_name','');
        $controller   = $this->request->get('controller','');

        $member_id   = $this->request->get('member_id','');
        $status   = $this->request->get('status','');
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;
        }
        $param['order'] = 'system_log_id';
        $param['orderby'] = 'DESC';
        //print_r($param);exit;

        unset($param['start_time']);unset($param['end_time']);

        //获取当前管理员的角色信息
        $roleBrand=$this->admin->getSystemRole(['system_role_id'=>Session::get('system_role_id')])['result'][0]['brand_id'];
        //此处需要的只是角色表下的brand_id，即brand表里的id（而不是brand下的brand）
        $param['roleBrand']=$roleBrand;

        //print_r($param);exit;
        $systemLog = $this->admin->getSystemLog($param);
        //print_r($systemLog);exit;
        foreach ($systemLog['result'] as &$line) {
            foreach ($line as $k => $v) {
                if ($k == 'created_at' || $k == 'updated_at') {
                    //精确到分钟
                    $line[$k] = substr($v, 0, 19);
                }
            }
        }


        return \View::make('system_log.index',array('systemLog'=>$systemLog['result'],'total'=>$systemLog['total'],'start_time'=>$start_time,'end_time'=>$end_time,'operation_name'=>$operation_name,'controller'=>$controller,'operation_type'=>$operation_type,'size'=>$param['size'],'search'=>$this->request->all(),'content_id'=>$content_id));
    }

    public function getAdd(){
    }

    public function postAdd(){
        $data = $this->request->all();
        $res  = $this->admin->addSystemLog($data);
        if(empty($res)){
            return $this->admin->getErr();
        }else{
            return '1';
        }
    }


    /* 后台操作日志
     * 获取修改后（提交成功）的内容
     * $res:数据库操作后是否成功的返回值
     * $arrayBefore:取出相同结构的修改之前的字段数组
     * $data:提交过来的数组*/
    function diffArray($res,$arrayBefore,$data){
        if($res){
            //取出修改后的键值对（修改前没有，而修改后有的键值对）
            $arrayMod=array_diff_assoc($data,$arrayBefore);
            //取出修改后（和之前不同的字段）的键
            $arrayKeys=array_keys($arrayMod);
            //修改后的（没修改的不包含在内）
            $arr2=[];
            $str2='';
            foreach($arrayKeys as $k=>$v){
                if(!empty($arrayMod[$v])){
                    $arr2[$v]=$arrayMod[$v];
                    $str2=$str2.','.$v.'=>'.$arrayMod[$v];
                }
            }
            $str2=ltrim($str2,',');

            //修改前的（只有被修改的字段才有）
            $str1='';
            foreach($arr2 as $k=>$v){
                if(!empty($arrayBefore[$k])){
                    $str1=$str1.','.$k.'=>'.$arrayBefore[$k];
                }
            }
            $str1=ltrim($str1,',');

            //修改前后对比插入数据库
            if(!empty($str1) && !empty($str2)){
                return $str1.' 改为 '.$str2;
            }else{
                return '';
            }
        }
    }




    /*public function export()
    {
        $start_time = $this->request->get('start_time', '');
        $end_time = $this->request->get('end_time', '');
        $car_name = $this->request->get('car_no', '');
        $status = $this->request->get('status', '');

        $data = [
            'order' => 'created_at',
            'orderby' => 'DESC',
        ];
        $data['created_at'][] = $start_time;
        $data['created_at'][] = $end_time;
        $orders = $this->share_car->getOrders($data);
        isset($orders['result']) && $orders = $orders['result'];

        foreach ($orders as $k => $v) {
            $export_data[] = [
                '订单编号' => $v['order_sn'],
                '车辆名称' => $v['title'],
                '车牌号码' => $v['car_no'],
                '用车时长' => $this->SecondsCalc($v['total_time']),
                '总金额' => $v['total_money'],
                '优惠金额' => $v['free_money'],
                '订单结束时间' => $v['updated_at']
            ];
        }
        $result = Excel::create('共享汽车订单'.date('YmdHis'),function ($excel) use ($export_data) {
            $excel->sheet('', function ($sheet) use ($export_data) {
                $sheet->appendRow(['订单编号','车辆名称','车牌号码','用车时长','总金额','优惠金额','订单结束时间']);
                $sheet->rows($export_data);
            });
        })->export('xls');
        exit;
    }*/




}