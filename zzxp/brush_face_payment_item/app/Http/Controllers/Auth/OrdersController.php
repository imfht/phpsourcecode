<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Lib\Api\Api AS Api;
use View,Input,Session,Redirect,Response;
use Maatwebsite\Excel\Facades\Excel;
/**
 * Created by PhpStorm.
 * User: xzl
 * Date: 14/10/23
 * Time: 下午4:16
 */
class OrdersController extends Controller {

    public function __construct(Request $req){
        $this->api = new Api;
        $this->request = $req;
    }
    public function index(){
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;   
        }

        if(isset($param['status']) && $param['status'] === ''){
            unset($param['status']);
        }
        if(isset($param['send_status']) && $param['send_status'] === ''){
            unset($param['send_status']);
        }
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';
        $param['admin'] = true;

        $orders = $this->api->getOrders($param);
        // print_r($orders);exit();
        return \View::make('orders.index',array('orders'=>$orders['result'],'total'=>$orders['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'订单表管理'));
    }

    public function export(){

        $param = $this->request->all();
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');

        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;   
        }
        $param['admin'] = true;
        $param['export'] = true;
        $orders = $this->api->getOrders($param);
        isset($orders['result']) && $orders = $orders['result'];

        $cellData = [['订单批量导出模板(此行请勿删除)']];
        $index = 1;
        $mergeCells = [$index];
        foreach ($orders as $key => $value) {
            # code...
            $cellData[] = ['订单号',$value['order_sn'],'订单时间',$value['created_at'],'发货状态',$value['send_status'] == 1 ? '已发货':'未发货'];
            $cellData[] = ['收货人姓名',$value['name'],'收货人电话',$value['phone'],'收货人地址',$value['province'].' '.$value['city'].' '.$value['county'].' '.$value['address']];
            $cellData[] = ['快递单号',$value['express_sn'],'快递公司',$value['express_name'],'快递时间',$value['express_time']];
            $cellData[] = ['商品编号','商品名称','','','商品数量'];

            $index += 4;
            $mergeCells[] = $index;
            foreach ($value['info'] as $goods) {
                # code...
                $cellData[] = [$goods['id'],$goods['title'],'','',$goods['number']];
                $index += 1;
                $mergeCells[] = $index;
            }
            $cellData[] = [];
            $cellData[] = [];
            $index += 2;
        }

        Excel::create($start_time.'到'.$end_time.'订单名单', function ($excel) use ($cellData,$mergeCells) {
            $excel->sheet('提现名单', function ($sheet) use ($cellData,$mergeCells) {
                $sheet->rows($cellData);

                // Cell Size
                // 省略了未修改代码
                $sheet->setWidth(array(
                    'A' => 10,
                    'B' => 25,
                    'C' => 10,
                    'D' => 25,
                    'E' => 10,
                    'F' => 35
                ));
                $h_row = [];
                foreach ($cellData as $key => $value) {
                    # code...
                    $h_row[$key + 1] = 20;
                }
                 $sheet->setHeight($h_row);
                

                // Merging cells
                // 省略了未修改代码
                
                $sheet->mergeCells('A1:F1');
                foreach($mergeCells as $kindex){
                    $sheet->mergeCells('B'.$kindex.':D'.$kindex);
                    $sheet->mergeCells('E'.$kindex.':F'.$kindex);
                }
                // $sheet->setMergeColumn(array(
                //     'columns' => array('A'),
                //     'rows' => array(
                //         array(3, 4),
                //     )
                // ));

                // Alignment
                $style = array(
                    'alignment' => array(
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );
                $sheet->getDefaultStyle()->applyFromArray($style);
                $sheet->getStyle("A1:F1")->applyFromArray($style);
                // $sheet->getStyle("A3:A4")->applyFromArray($style);
                // $sheet->getStyle("D3:D4")->applyFromArray($style);
            });
        })->export('xlsx');
    }
   
    public function import(){

        if(empty($_FILES['import'])){
            return '上传的内容不能为空';
        }
        $fileName = explode('.',$_FILES['import']['name']);
        $fileAlias = $_FILES["import"]["tmp_name"];
        $fileName = date('YmdHis').'.'.array_pop($fileName);
        
        $path = public_path($fileName);

        move_uploaded_file($fileAlias, $path);

        // echo $fileAlias , "<br />";
        // exit();
        // (new UsersImport)->import($path,null,\Maatwebsite\Excel\Excel::XLSX);
        // $obj = $this;
        Excel::load($path, function($reader) {

            $reader->sheet(0,function($sheet){
                // print_r($sheet->toArray());
                $data = $sheet->toArray();
                // print_r($data);
                $index = 3;
                foreach($data as $key => $row){
                    if($key == $index){
                        // print_r($row);
                        $order = $data[$key -2];
                        if(!empty($row[1])){
                            $result = $this->api->updateOrders(['order_sn'=>$order[1],'express_sn'=>$row[1],'express_name'=>$row[2],'express_time'=>$row[3],'send_status'=>1]);
                        }
                        $index += 7;
                    }
                }
            });
        });
        unlink($path);
        return Redirect::to('admin/orders/index');
        // return \View::make('withdraw.import',array('message'=>'导入完成'));
    }

    public function unpay(){
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;   
        }
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';
        $param['admin'] = true;
        $param['status'] = 0;

        $orders = $this->api->getOrders($param);
        // print_r($orders);exit();
        return \View::make('orders.index',array('orders'=>$orders['result'],'total'=>$orders['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'订单表管理'));
    }
    public function pay(){
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;   
        }
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';
        $param['admin'] = true;
        $param['status'] = 1;

        $orders = $this->api->getOrders($param);
        // print_r($orders);exit();
        return \View::make('orders.index',array('orders'=>$orders['result'],'total'=>$orders['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'订单表管理'));
    }
    public function unsend(){
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;   
        }
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';
        $param['admin'] = true;
        $param['status'] = 1;
        $param['send_status'] = 0;

        $orders = $this->api->getOrders($param);
        // print_r($orders);exit();
        return \View::make('orders.index',array('orders'=>$orders['result'],'total'=>$orders['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'订单表管理'));
    }
    public function send(){
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;   
        }
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';
        $param['admin'] = true;
        $param['status'] = 1;
        $param['send_status'] = 1;

        $orders = $this->api->getOrders($param);
        // print_r($orders);exit();
        return \View::make('orders.index',array('orders'=>$orders['result'],'total'=>$orders['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'订单表管理'));
    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();
        $res  = $this->api->addOrders($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',0);
        $orders = $this->api->getOrders(['id'=>$id,'admin'=>true]);
        $order_info = $this->api->getOrderInfo(['order_id'=>$id]);

        $orders = isset($orders['result']) && isset($orders['result'][0]) ? $orders['result'][0] : [];
        $order_info = isset($order_info['result']) ? $order_info['result'] : [];


        return \Response::json(['orders'=>$orders,'order_info'=>$order_info]);
    }
    public function postUpdate(){
        $id = $this->request->get('id',0);
        $data  = $this->request->all();
        $res = $this->api->updateOrders($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function postDel(){
        $id = $this->request->get('ids','');
        if(empty($id)){
            return '没有选择任何记录';
        }

        $res = $this->api->delOrders(['id'=>$id]);
        if($res === false){
            return $this->api->getErr();
        }else{
            return '1';
        } 
    }
}