<?php
namespace app\index\controller;
use think\Loader;
use think\Controller;
use think\Db;
class Common extends Controller
{
    // 数据存在检测
    public function dataInDb()
    {
        $data = $_POST;
        $ret = 'N';
        if(isset($data['table'])){
            $wh = json_decode(base64_decode($data['where']),true);
            $table = str_replace('/','_',$data['table']);
            $ctt = Db::table($table)->where($wh)->count();
            if($ctt) $ret = 'Y';
        }
        echo $ret;
        die;
    }
    // 仅仅登录时可使用
    private function onlyLogined()
    {
        if(!$this->uLoginCkeck()){
            utf8();
            echo '页面请求无效';
            die;
        }
    }
    // 图形验证码
    public function captcha()
    {
        $id = isset($_GET['id'])? $_GET['id']:'';
        $id = isset($_POST['id'])? $_POST['id']:$id;
        if($id){
            $this->_captcha($id);
            echo captcha_img();   
        }
        //echo captcha_img();
        //debugOut(captcha_img());
        //echo captcha_src($id);
    }
    // popup窗数据获取- * table/field/order [map/no]
    public function popup()
    {
        $data = $_POST;
        if(count($data)>0){
            $num = 20;
            $no = isset($data['page'])? intval($data['page']):1;            
            $count = 0;
            if(isset($data['map'])){
                $res = Db::table($data['table'])->where($data['map'])->field($data['field'])->order($data['order'])->page($no,$num)->select();
                $count = Db::table($data['table'])->where($data['map'])->count();
            }else{
                $res = Db::table($data['table'])->field($data['field'])->page($no,$num)->order($data['order'])->select();
                $count = Db::table($data['table'])->count();
            }
            $result = ['data'=>$res,'count'=>$count,'no'=>$no,'pages'=>ceil($count/$num)];
            echo json_encode($result);
            die;
        }
        else{
            utf8();
            echo '欢饮访问Conero网站！';
        }
    }
    // 更具主键获取数据(用于提取单列数据- 如数据维护)
    // -> bsjson(map:[table,pk,value])
    public function record(){        
        $data = count($_POST)>0? $_POST:$_GET;
        // 用户登录判断
        $code = uInfo('code');
        if(empty($code) && !isset($data[base64_encode(sysdate('date'))])) return '欢饮访问Conero网站！';

        isset($data['map']) or die('欢饮访问Conero网站！');
        $data = bsjson($data['map']);
        list($tb,$pk,$value) = $data;
        return Db::table($tb)->where($pk,$value)->find();
    }
    // 文件下载
    public function download(){
        list($data) = $this->_getSaveData();
        if($data){
            if(isset($data['file'])){
                $file = $data['file'];
                if(isset($data['64'])) $file = base64_decode($file);
                $file = ROOT_PATH.$file;
                \hyang\Download::filename($file);
                \hyang\Download::load();
            }
        }
    }
}