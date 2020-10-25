<?php
/**
 * 小程序版本
 * https://github.com/EastWorld/wechat-app-mall.git
 */
namespace Weixin\Controller;
use Think\Controller;

class AppitController extends Controller {
    const APPID = 'wx0b1a73f4d330c505';
    const APP_SECRET = '1dcc8a7b363e4a0e215d81d6fe037ab7';

    public function _initialize() {
        header('Content-type: application/json');
        $this->save();
        $this->categorys = F('category_content');
        $this->db = D('Content');
        $this->db->set_model(3);
    }

    public function index(){
        echo 'it120';
    }

    public function config_get_value(){
        $key = I('get.key');
        if($key == 'mallName'){
            $return['data']['value'] = '泉州艺术考级';
        }else{
            $return['data']['value'] = '100';
        }
        $return['code'] = 0;
        $this->ajaxReturn($return);
    }

    public function user_wxapp_login() {
        $code = I('get.code');
        $author_detail = $this->authorizationByCode($code);
        $openid      = $author_detail['openid'];
        $session_key = $author_detail['session_key'];
        if ($openid) {
            $info['openid'] = $openid;
            //查看是否存在
            $detail = M('User')->where($info)->find();
            $userid = $detail['userid'];
            $return['code'] = 0;
            if (empty($detail)) {
                // $userid = M('User')->add($info);
                $return['code'] = 10000;
            }else{
                //更新session_key
                M('User')->where(['userid' => $userid])->save(['session_key' => $session_key]);
            }

            $return['data'] = array(
                'uid' => $userid,
                'token' => $openid
            );
        }
        $this->ajaxReturn($return);
    }

    public function authorizationByCode($code){
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . self::APPID . "&secret=" . self::APP_SECRET . "&js_code=" . $code . "&grant_type=authorization_code";
        $arr = getCurlData($url);
        $arr = json_decode($arr, true);
        return $arr;
    }

    public function user_wxapp_register_complex(){
        $code = I('get.code');
        $author_detail = $this->authorizationByCode($code);
        $iv = I('get.iv');
        $encryptedData = I('get.encryptedData');

        Vendor('Aes.WXBizDataCrypt');
        $pc = new \WXBizDataCrypt(self::APPID, $author_detail['session_key']);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            $userinfo = json_decode($data, true);
            //保存到数据库
            $info['openid'] = $userinfo['openId'];
            $info['nickname'] = $userinfo['nickName'];
            $info['gender'] = $userinfo['gender'];
            // $info[''] = $userinfo['language'];
            $info['city'] = $userinfo['city'];
            $info['province'] = $userinfo['province'];
            $info['country'] = $userinfo['country'];
            $info['headimg'] = $userinfo['avatarUrl'];
            M('User')->add($info);

        } else {
            
        }
    }

    public function banner_list(){
        $poster_banner = D('Poster')->getDetailById(11);
        $image_list = $poster_banner['setting'];
        if($image_list){
            foreach ($image_list as $key => $value) {
                $image_list[$key]['picUrl'] = $value['imageurl'];
            }
        }
        //轮播图
        $return['code'] = 0;
        $return['data'] = $image_list;
        $this->ajaxReturn($return);
    }

    //获取所有分类
    public function shop_goods_category_all(){
        $list = [];
        if($this->categorys){
            foreach ($this->categorys as $key => $value) {
                $category['id'] = $value['catid'];
                $category['name'] = $value['catname'];
                $list[] = $category;
            }
        }
        $return['code'] = 0;
        $return['data'] = $list;
        $this->ajaxReturn($return);
    }

    //取出产品列表
    public function shop_goods_list(){
        $catid = I('get.categoryId');
        $nameLike = I('get.nameLike');
        if($catid){
            $map['catid'] = $catid;
        }
        if($nameLike){
            $map['title'] = ['like', "%$nameLike%"];
        }
        $list = $this->db->where($map)->select();
        if($list){
            foreach ($list as $key => $value) {
                $list[$key]['name'] = $value['title'];
                $list[$key]['minPrice'] = $value['price'];
                $list[$key]['pic'] = thumb($value['thumb'], 180, 120);
            }
        }
        $return['code'] = 0;
        $return['data'] = $list;
        $this->ajaxReturn($return);

    }

    //取出详情
    public function shop_goods_detail(){
        $id = I('get.id');
        $detail = $this->db->getDetail($id);
        if($detail){
            $detail['basicInfo']['stores'] = 100;   //库存
            $detail['basicInfo']['name'] = $detail['title'];
            $detail['basicInfo']['minPrice'] = $detail['price'];  
            $detail['basicInfo']['minScore'] = 0;  
            $detail['basicInfo']['id'] = $detail['id'];
            $detail['basicInfo']['pic'] = thumb($detail['thumb'], 180, 120);
            $detail['content']['nodes'] = $detail['content'];

            //获取缩略图列表
            $pic['pic'] = $detail['thumb'];
            $detail['pics'][] = $pic;

            $return['code'] = 0;
            $return['data'] = $detail;

        }
        $this->ajaxReturn($return);
    }

    public function user_detail(){
        $userid = $this->getUserIdByLoginToken(I('post.token'));

    }

    public function order_list(){

    }

    public function getUserIdByLoginToken($loginToken){
        $map['openid'] = $loginToken;
        $userid = M('User')->where($map)->getField('userid');
        return $userid;
    }

    public function shop_goods_reputation(){

    }

    public function score_send_rule(){

    }
    //保存post信息
    public function save($data = null) {
        if (!$data) {
            if (IS_POST) {
                $data = file_get_contents("php://input"); //接收post数据
            } else {
                $data = $_GET;
            }
        }
        //$file_in = file_get_contents("php://input"); //接收post数据
        $info['refer'] = $_SERVER['REQUEST_URI'];
        $info['data'] = serialize($data);
        $info['create_time'] = NOW_TIME;
        $info['ip'] = get_client_ip(0, true);
        $result = M('post_log')->add($info);

    }

    public function _curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $txt = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        }
        curl_close($ch);
        return json_decode($txt, true);
    }
    public function vget($url) {
        $info = curl_init();
        curl_setopt($info, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($info, CURLOPT_HEADER, 0);
        curl_setopt($info, CURLOPT_NOBODY, 0);
        curl_setopt($info, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($info, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($info, CURLOPT_URL, $url);
        $output = curl_exec($info);
        curl_close($info);
        return $output;
    }

}