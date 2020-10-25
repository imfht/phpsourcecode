<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Lib\Api\Api AS Api;
use View,Input,Session,Redirect,Response,QrCode;
use ZipArchive;

/**
 * Created by PhpStorm.
 * User: xzl
 * Date: 14/10/23
 * Time: 下午4:16
 */
class BusinessController extends Controller {

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
        $param['admin']  = true;

        $param['order'] = 'id';
        $param['orderby'] = 'DESC';
        
        $business = $this->api->getBusiness($param);
        return \View::make('business.index',array('business'=>$business['result'],'total'=>$business['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'商户注册表管理'));
    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();
        $res  = $this->api->addBusiness($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',0);
        $business = $this->api->getBusiness(['id'=>$id]);
        $business = isset($business['result']) && isset($business['result'][0]) ? $business['result'][0] : [];
        if(isset($business['wx_sign_url'])){
            $business['wx_sign_url'] = urldecode($business['wx_sign_url']);
        }
        return \Response::json($business);
    }
    public function postUpdate(){
        $id = $this->request->get('id',0);
        $data  = $this->request->all();
        $res = $this->api->updateBusiness($data);
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

        $res = $this->api->delBusiness(['id'=>$id]);
        if($res === false){
            return $this->api->getErr();
        }else{
            return '1';
        } 
    }

    public function payQrcode(){
        ///pay/payment.html
        
        $host = env('CODE_HOST');
        $bid = $this->request->get('bid',0);
        $img_path = public_path('/qrcode');
        if(!is_dir($img_path)){
            mkdir($img_path);
        }
        $img_path = public_path('/qrcode/pay_code/');
        if(!is_dir($img_path)){
            mkdir($img_path);
        }

        $file = 'alipay_'.$bid.'.png';
        // $bid = \Session::get('bid',6);

        QrCode::format('png')->size(195)->margin(0)->encoding('UTF-8')->generate($host.'/order/get-ali-code?bid='.$bid, $img_path.$file);
        return ['path'=>'/qrcode/pay_code/'.$file];

    }

    public function getFile(){
        $bid = $this->request->get('bid',0);
        $business = $this->api->getBusiness(['id'=>$bid]);
        isset($business['result']) && $business = $business['result'];
        isset($business[0]) && $business = $business[0];
        $root_path = '../../zpay_phone/public/';
        $file = "

            联系人姓名: {$business['contactPerson']}\r\n
            联系手机号:{$business['contactPhone']}\r\n
            联系人邮箱:{$business['contactEmail']}\r\n
            联系人身份证号码:{$business['idCardNumber']}\r\n
            人身份证有效期:{$business['idNumExp']}\r\n


            结算银行卡号: {$business['bankAccNo']}\r\n
            入账人姓名: {$business['bankAccName']}\r\n
            开户行: {$business['bankBranch']}\r\n
            开户支行: {$business['bankSubbranch']}\r\n
            开户行城市编号: {$business['bank_city']}\r\n

            商户名称: {$business['rname']}\r\n
            法人姓名: {$business['legalPerson']}\r\n
            法人身份证号: {$business['legalNum']}\r\n

            营业执照号:{$business['bno']}\r\n
            营业执照注册名称:{$business['shortName']}\r\n
            营业执照有效期:{$business['licenceExp']}\r\n

            店主身份证号:{$business['accNum']}\r\n
            经营地址:{$business['baddr']}\r\n
            行业:{$business['product_desc']}\r\n

        ";
        $tmp = './business_'.$business['id'].'/';//public_path('/tmp/');
        if(!is_dir($tmp)){
            mkdir($tmp);
        }
        $filename = !empty($business['bankAccName']) ?  $business['bankAccName'] : 'business_'.$business['id'];
        $btmp =  './business_'.$business['id'];//public_path('/tmp/'.$filename);
        if(!is_dir($btmp)){
            mkdir($btmp);
        }

        file_put_contents($btmp.'/business.txt', $file);
        $file = $btmp.'/business.txt';
        $zip = new ZipArchive;
        $zip->open($btmp.'/'.$filename.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile($file);

       
        if(!empty($business['materialUpLoad4File'])){
            $tmp = explode('/',$business['materialUpLoad4File']);
            $tpath = $btmp.'/'.end($tmp);
            copy($root_path.$business['materialUpLoad4File'],$tpath);
            $zip->addFile($tpath);
        }
        if(!empty($business['photoUpLoadFile'])){
            $tmp = explode('/',$business['photoUpLoadFile']);
            $tpath = $btmp.'/'.end($tmp);
            copy($root_path.$business['photoUpLoadFile'],$tpath);
            $zip->addFile($tpath);        
        }
        if(!empty($business['registryUpLoadFile'])){
            $tmp = explode('/',$business['registryUpLoadFile']);
            $tpath = $btmp.'/'.end($tmp);
            copy($root_path.$business['registryUpLoadFile'],$tpath);
            $zip->addFile($tpath); 
        }
        if(!empty($business['materialUpLoad3File'])){
            $tmp = explode('/',$business['materialUpLoad3File']);
            $tpath = $btmp.'/'.end($tmp);
            copy($root_path.$business['materialUpLoad3File'],$tpath);
            $zip->addFile($tpath); 
        }

        if(!empty($business['materialUpLoad7File'])){
            $tmp = explode('/',$business['materialUpLoad7File']);
            $tpath = $btmp.'/'.end($tmp);
            copy($root_path.$business['materialUpLoad7File'],$tpath);
            $zip->addFile($tpath); 
        }

        if(!empty($business['materialUpLoad5File'])){
            $tmp = explode('/',$business['materialUpLoad5File']);
            $tpath = $btmp.'/'.end($tmp);
            copy($root_path.$business['materialUpLoad5File'],$tpath);
            $zip->addFile($tpath); 
        }
        if(!empty($business['legalUploadFile'])){
            $tmp = explode('/',$business['legalUploadFile']);
            $tpath = $btmp.'/'.end($tmp);
            copy($root_path.$business['legalUploadFile'],$tpath);
            $zip->addFile($tpath); 
        }
        if(!empty($business['materialUpLoadFile'])){
            $tmp = explode('/',$business['materialUpLoadFile']);
            $tpath = $btmp.'/'.end($tmp);
            copy($root_path.$business['materialUpLoadFile'],$tpath);
            $zip->addFile($tpath); 
        }

        if(!empty($business['materialUpLoad1File'])){
            $tmp = explode('/',$business['materialUpLoad1File']);
            $tpath = $btmp.'/'.end($tmp);
            copy($root_path.$business['materialUpLoad1File'],$tpath);
            $zip->addFile($tpath); 
        }
        if(!empty($business['bupLoadFile'])){
            $tmp = explode('/',$business['bupLoadFile']);
            $tpath = $btmp.'/'.end($tmp);
            copy($root_path.$business['bupLoadFile'],$tpath);
            $zip->addFile($tpath); 
        }
        if(!empty($business['rupLoadFile'])){
            $tmp = explode('/',$business['rupLoadFile']);
            $tpath = $btmp.'/'.end($tmp);
            copy($root_path.$business['rupLoadFile'],$tpath);
            $zip->addFile($tpath); 
        }

        $zip->close(); //关闭处理的zip文件
        return ['path'=>'/business_'.$business['id'].'/'.$filename.'.zip','file'=>$root_path.$business['materialUpLoad7File']];

    }
}