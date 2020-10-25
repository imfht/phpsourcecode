<?php

namespace App\Http\Controllers;

use App\Lib\Api\AdminApi;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Lib\Alioss\AliossApi;
use Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests; 
    
    public function check()
    {
    	$this->middleware('competence');
    }
    
    public function encode_password($password)
    {
    	if (empty($password)) {
    		return;
    	}
    	 
    	$pre_code = 'BukeTech';
    	$suf_code = 'WeiWei';
    	 
    	return md5($pre_code.$password.$suf_code);
    }
    
    public function upload_oss($name)
    {
    	$alioss = new AliossApi;
    	$file = $alioss->uploadFile($name);
    	return $file_url = $file['img_url'];
    }

    /* 后台操作日志
     * 获取修改前、后（提交成功）的内容
     * $res:数据库操作后是否成功的返回值
     * $arrayBefore:取出相同结构的修改之前的字段数组
     * $data:提交过来的数组*/
    public function diffArray($res,$arrayBefore,$data){
        if($res){
            //提交表单$data中有，而$arrayBefore中没有的键值对(改后的键值对)
            $diffData=array_diff_assoc($data,$arrayBefore);
            //取出$arrayBefore中有$diffData全部键的所有键值对(改之前的键值对)
            $dataBefore=array();
            foreach($diffData as $k=>$v){
                if(isset($arrayBefore[$k])){
                    $dataBefore[$k]=$arrayBefore[$k];
                }
            }
            $str1='';
            foreach($dataBefore as $k=>$v){
                $str1=$str1.','.$k.'=>'.$v;
            }
            $str2='';
            foreach($diffData as $k=>$v){
                $str2=$str2.','.$k.'=>'.$v;
            }
            //判断改前改后是否一致,若一致反回false
            if(array_diff_assoc($dataBefore,$diffData) || array_diff_assoc($diffData,$dataBefore)){
                return trim($str1,',').' <span style="color:red;font-weight:bold;">改成了</span> '.trim($str2,',');
            }else{
                return false;
            }
        }
    }



    /**
     * 取汉字的第一个字的首字母
     * @param type $str
     * @return string|null
     */
    // public function _getFirstCharter($str){
    //     if(empty($str)){return '';}
    //     $fchar=ord($str{0});
    //     if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
    //     $s1=iconv('UTF-8','gbk//TRANSLIT//IGNORE',$str);
    //     $s2=iconv('gb2312','utf-8//TRANSLIT//IGNORE',$s1);
    //     $s=$s2==$str?$s1:$str;
    //     $asc=ord($s{0})*256+ord($s{1})-65536;
    //     if($asc>=-20319&&$asc<=-20284) return 'A';
    //     if($asc>=-20283&&$asc<=-19776) return 'B';
    //     if($asc>=-19775&&$asc<=-19219) return 'C';
    //     if($asc>=-19218&&$asc<=-18711) return 'D';
    //     if($asc>=-18710&&$asc<=-18527) return 'E';
    //     if($asc>=-18526&&$asc<=-18240) return 'F';
    //     if($asc>=-18239&&$asc<=-17923) return 'G';
    //     if($asc>=-17922&&$asc<=-17418) return 'H';
    //     if($asc>=-17417&&$asc<=-16475) return 'J';
    //     if($asc>=-16474&&$asc<=-16213) return 'K';
    //     if($asc>=-16212&&$asc<=-15641) return 'L';
    //     if($asc>=-15640&&$asc<=-15166) return 'M';
    //     if($asc>=-15165&&$asc<=-14923) return 'N';
    //     if($asc>=-14922&&$asc<=-14915) return 'O';
    //     if($asc>=-14914&&$asc<=-14631) return 'P';
    //     if($asc>=-14630&&$asc<=-14150) return 'Q';
    //     if($asc>=-14149&&$asc<=-14091) return 'R';
    //     if($asc>=-14090&&$asc<=-13319) return 'S';
    //     if($asc>=-13318&&$asc<=-12839) return 'T';
    //     if($asc>=-12838&&$asc<=-12557) return 'W';
    //     if($asc>=-12556&&$asc<=-11848) return 'X';
    //     if($asc>=-11847&&$asc<=-11056) return 'Y';
    //     if($asc>=-11055&&$asc<=-10247) return 'Z';
    //     return null;
    // }


    /* 后台操作日志
     * 删除/修改前的内容由数组转换为字符串
     * $data:要转换为字符串的数组（数据库查询出来的）
     */
    public function originalContents($data){
        $str='';
        foreach($data as $k=>$v){
            $str=$str.','.$k.'=>'.$v;
        }
        return trim($str,',');
    }




     // GPS坐标转换成百度坐标
    protected function coordsToBaidu($lng, $lat) {
        $baidu_ak = env('BAIDU_AK','');
        $baidu_url = env('BAIDU_URL','');

        $url = "{$baidu_url}?coords={$lng},{$lat}&from=1&to=5&ak={$baidu_ak}";  // from(1:GPS设备获取的角度坐标，wgs84坐标;)
        $data = file_get_contents($url);
        $data = json_decode($data,true);

        if (!is_array($data) || $data['status'] !== 0) {
            return ['lng'=>0,'lat'=>0];
        }

        return ['lng'=>$data['result'][0]['x'],'lat'=>$data['result'][0]['y']];
    }
    
    /**
     * 获取品牌区域
     * @param $api adminapi
     * @return array
     */
    public function getBrandArea(AdminApi $api)
    {
        $system_role_id = session('system_role_id');
        //根据角色id获取品牌
        $role = $api->getSystemRole(['system_role_id'=>$system_role_id]);
        $brand = $role['result'][0]['brand_id'];
        //根据该角色的品牌id获取该品牌所有的区域权限
        $param = [];
        $brandArea = $api->getBrandArea(['brand_id'=>$brand])['result'];
        if(!empty($brandArea)){
            $param['provincial_permissions']=1;
            $param['area_level']=[];
            $param['province']=[];
            $param['city']=[];
            $param['county']=[];
            //按等级归纳subordinate_area
            foreach($brandArea as $v){
                //如果有等级为全国的，则直接将地区等级设置为nation（全国）
                if($v['area_level']=='nation'){
                    $param['area_level']='nation';
                    break;
                }
                //如果为省，则将
                if($v['area_level']=='province'){
                    array_push($param['province'],$v['subordinate_area']);
                }
                //如果为市，则将
                if($v['area_level']=='city'){
                    array_push($param['city'],$v['subordinate_area']);
                }
                //如果为区，则将
                if($v['area_level']=='county'){
                    array_push($param['county'],$v['subordinate_area']);
                }
            }
        }
        
        return $param;
    }

    /**
     * 根据member_id添加后台管理员数据
     */
    public function systemUser($member_id, $param, $type=2){
        $this->admin_api = new AdminApi;
        //查找是否有该管理员的数据 不存在则添加数据
        $system_user_info = $this->admin_api->getSystemUser(['member_id'=>$member_id, 'type'=>$type]);
        isset($system_user_info['result']) && $system_user_info = $system_user_info['result'];
        isset($system_user_info[0]) && $system_user_info = $system_user_info[0];
        //管理员信息为空，则需要增加一条管理员数据
        if (empty($system_user_info)) {
            $data = [
                'member_id' => $param['member_id'],
                'system_role_id' => $type,
                'user_name' => $param['user_name'],
                'type' => $type,
                'phone' => $param['phone'],
                'status' => 1,
                'enabled' => 0,
            ];
            //加入创建人的id
            $data['creator']=Session::get('sys_id');
            //增加的初始密码：
            $password = $this->randomkeys(6);
            $data['password'] = $this->encode_password($password);
            $this->admin_api->addSystemUser($data);
            return $password;
        }
    }

    // function randomkeys($length) {
    //     $returnStr='';
    //     $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    //     for($i = 0; $i < $length; $i ++) {
    //         $returnStr .= $pattern {mt_rand ( 0, 61 )}; //生成php随机数
    //     }
    //     return $returnStr;
    // }

    
    protected function baiduToCoords($bd_lon, $bd_lat) {
        $x = $bd_lon - 0.0065;
        $y = $bd_lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * M_PI);
        $theta = atan2($y, $x) - 0.000003 * cos($x * M_PI);
        $gg_lon = $z * cos($theta);
        $gg_lat = $z * sin($theta);
        return [$gg_lon, $gg_lat];
    }  
}
