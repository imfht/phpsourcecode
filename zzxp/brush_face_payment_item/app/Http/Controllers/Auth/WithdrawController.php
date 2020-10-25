<?php
namespace App\Http\Controllers\Auth;

use App\Imports\UsersImport;
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
class WithdrawController extends Controller {

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
        
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';
        $param['admin']  = true;
        $withdraw = $this->api->getWithdraw($param);
        return \View::make('withdraw.index',array('withdraw'=>$withdraw['result'],'total'=>$withdraw['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'提现记录管理'));
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
        
        if(isset($param['page'])){
            unset($param['page']);
        }
        if(isset($param['size'])){
            unset($param['size']);
        }
        $param['admin']  = true;
        $withdraw = $this->api->getWithdraw($param);
        isset($withdraw['result']) && $withdraw = $withdraw['result'];

         $cellData = [
            ['支付宝批量付款文件模板（前面两行请勿删除）', '', '', ''],
            ['序号（必填）', '收款方支付宝账号（必填）', '收款方姓名（必填）', '金额（必填，单位：元）','备注（选填）','是否付款','系统编号(勿删)'],
        ];
        $index = 1;
        foreach ($withdraw as $key => $value) {
            # code...
            $cellData[] = [$index++,$value['ali_account'],$value['ali_name'],$value['money'],$value['remark'],$value['status'] == 0 ? '否':'是',$value['id']];
        }

        Excel::create($start_time.'到'.$end_time.'提现名单', function ($excel) use ($cellData) {
            $excel->sheet('提现名单', function ($sheet) use ($cellData) {
                $sheet->rows($cellData);

                // Cell Size
                // 省略了未修改代码
                $sheet->setWidth(array(
                    'A' => 10,
                    'B' => 20,
                    'C' => 20,
                    'D' => 30,
                    'E' => 30,
                    'F' => 10,
                    'G' => 20
                ));
                $h_row = [];
                foreach ($cellData as $key => $value) {
                    # code...
                    $h_row[$key + 1] = 20;
                }
                 $sheet->setHeight($h_row);
                

                // Merging cells
                // 省略了未修改代码
                
                $sheet->mergeCells('A1:G1');
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
                $sheet->getStyle("A1:G1")->applyFromArray($style);
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
        Excel::load($path, function($reader) {

            $data = $reader->sheet(0,function($sheet){
                // print_r($sheet->toArray());
                $data = $sheet->toArray();
                foreach ($data as $key => $value) {
                    # code...
                    if($key == 0 || $key == 1){
                        continue;
                    }
                    $withdraw_id = $value[6];
                    $status = $value[5] == '是' ? 1 : 0;
                    $this->api->updateWithdraw(['id'=>$withdraw_id,'status'=>$status,'remark'=>$value[4]]);
                }
            });
        });
        unlink($path);
        return Redirect::to('admin/withdraw/index');
        // return \View::make('withdraw.import',array('message'=>'导入完成'));
    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();
        $res  = $this->api->addWithdraw($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',0);
        $withdraw = $this->api->getWithdraw(['id'=>$id]);
        return \Response::json(isset($withdraw['result']) && isset($withdraw['result'][0]) ? $withdraw['result'][0] : []);
    }
    public function postUpdate(){
        $id = $this->request->get('id',0);
        $data  = $this->request->all();
        $res = $this->api->updateWithdraw($data);
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

        $res = $this->api->delWithdraw(['id'=>$id]);
        if($res === false){
            return $this->api->getErr();
        }else{
            return '1';
        } 
    }
}