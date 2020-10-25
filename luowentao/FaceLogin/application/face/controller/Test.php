<?php
/**
 * Created by PhpStorm.
 * User: lwt
 * Date: 3/22/19
 * Time: 8:17 PM
 */
namespace app\face\controller;

use think\Loader;
use think\Request;

class Test{

    /**
     * appId
     * @var string
     */
    protected $appId = '百度AI人脸识别注册的appId';

    /**
     * apiKey
     * @var string
     */
    protected $apiKey = '百度AI人脸识别注册的apiKey';

    /**
     * secretKey
     * @var string
     */
    protected $secretKey = '百度AI人脸识别注册的secretKey';

    /**
     * 人臉檢測
     * @return \think\response\Json
     */
    public function index(){
        $request = Request::instance();
        if ($request->method() == "POST"){
            Loader::import("FaceAPI.AipFace",EXTEND_PATH,".php");
            $data = $request->post();
            $image = $data["base64"];
            $imageType = "BASE64";
            $options = array();
            $options["face_field"] = "glasses";
            $client = new \AipFace($this->appId, $this->apiKey, $this->secretKey);
            // 调用人脸检测
            $data = $client->detect($image, $imageType,$options);
            if($data["result"]["face_list"][0]["face_probability"] != 1){
                $content["error_code"] = 402;
                $content["msg"] = "请将脸部对准摄像头！";
                return json($content);
            }else{
                if($data["result"]["face_list"][0]["glasses"]["type"] == "none"){
                    $content["error_code"] = 0;
                    $content["msg"] = "检查到人脸！";
                    return json($content);
                }else if ($data["result"]["face_list"][0]["glasses"]["type"] == "common"){
                    $content["error_code"] = 402;
                    $content["msg"] = "请不要带眼镜！";
                    return json($content);
                }else if ($data["result"]["face_list"][0]["glasses"]["type"] == "sun"){
                    $content["error_code"] = 0;
                    $content["msg"] = "请不要带墨镜！";
                    return json($content);
                }
            }
        }else{
            $content["error_code"] = 400;
            $content["msg"] = "请发送POST请求！";
            return json($content);
        }

    }

    /**
     * 人臉匹配
     * @return \think\response\Json
     */
    public function faceJc(){
        $request = Request::instance();
        if ($request->method() == "POST"){
            Loader::import("FaceAPI.AipFace",EXTEND_PATH,".php");
            $data = $request->post();
            if ($data["groupIdList"] == null || $data["groupIdList"] == ""){
                $content["error_code"] = 402;
                $content["msg"] = "缺少groupIdList！";
                return json($content);
            }else if ($data["base64"] == null || $data["base64"] == ""){
                $content["error_code"] = 402;
                $content["msg"] = "缺少base64！";
                return json($content);
            }
            $image = $data["base64"];
            $imageType = "BASE64";
            $groupIdList = $data["groupIdList"];
            $client = new \AipFace($this->appId, $this->apiKey, $this->secretKey);
            // 调用人脸搜索
            $msg = $client->search($image, $imageType, $groupIdList);
            if ($msg["error_code"] != 0) {
                $content["error_code"] = 401;
                $content["msg"] = $msg["error_msg"];
                return json($content);
            }
            $num = count($msg["result"]["user_list"]);
            for ($i=0;$i<$num;++$i){
                if ($msg["result"]["user_list"][$i]["score"] < 80){
                    $content["error_code"] = 401;
                    $content["msg"] = "没有找到该用户！";
                    return json($content);
                }else{
                    return json($msg);
                }
            }

        }else{
            $content["error_code"] = 400;
            $content["msg"] = "请发送POST请求！";
            return json($content);
        }
    }

    public function updateFace(){
        $request = Request::instance();
        if ($request->method() == "POST"){
            Loader::import("FaceAPI.AipFace",EXTEND_PATH,".php");
            $data = $request->post();
            if ($data["user_info"] == null || $data["user_info"] == ""){
                $content["error_code"] = 402;
                $content["msg"] = "缺少user_info！";
                return json($content);
            }else if ($data["group"] == null || $data["group"] == ""){
                $content["error_code"] = 402;
                $content["msg"] = "缺少group！";
                return json($content);
            }else if ($data["user"] == null || $data["user"] == ""){
                $content["error_code"] = 402;
                $content["msg"] = "缺少user！";
                return json($content);
            }else if ($data["base64"] == null || $data["base64"] == ""){
                $content["error_code"] = 402;
                $content["msg"] = "缺少base64！";
                return json($content);
            }
            $image = $data["base64"];
            $imageType = "BASE64";

            $groupId = $data["group"];

            $userId = $data["user"];
            $options = array();
            $options["user_info"] = $data["user_info"];

            $client = new \AipFace($this->appId, $this->apiKey, $this->secretKey);
            $msg = $client->updateUser($image, $imageType, $groupId, $userId, $options);
            if ($msg["error_code"] == 0){
                $content["error_code"] = 0;
                $content["msg"] = "修改成功！";
                return json($content);
            }else{
                $content["error_code"] = $msg["error_code"];
                $content["msg"] = $msg["error_msg"];
                return json($content);
            }
        }else{
            $content["error_code"] = 400;
            $content["msg"] = "请发送POST请求！";
            return json($content);
        }
    }

    /**
     * 判断人脸是否注册
     * @return \think\response\Json
     */
    public function testingUser(){
        $request = Request::instance();
        if ($request->method() == "POST"){
            Loader::import("FaceAPI.AipFace",EXTEND_PATH,".php");
            $data = $request->post();
            if ($data["user_id"] == null || $data["user_id"] == ""){
                $content["error_code"] = 402;
                $content["msg"] = "缺少user_id！";
                return json($content);
            }else if ($data["group_id"] == null || $data["group_id"] == ""){
                $content["error_code"] = 402;
                $content["msg"] = "缺少group_id！";
                return json($content);
            }
            $userId = $data["user_id"];

            $groupId = $data["group_id"];

            $client = new \AipFace($this->appId, $this->apiKey, $this->secretKey);
            // 调用用户信息查询
            $msg = $client->getUser($userId, $groupId);
            if ($msg["error_code"] == 0) {
                $content["error_code"] = 0;
                $content["msg"] = "该用户已经绑定过人脸";
                return json($content);
            }else{
                $content["error_code"] = 402;
                $content["msg"] = "用户不存在！";
                return json($content);
            }
        }else{
            $content["error_code"] = 400;
            $content["msg"] = "请发送POST请求！";
            return json($content);
        }
    }

    /**
     * 人臉註冊
     * @return \think\response\Json
     */
    public function faceRegister(){
        $request = Request::instance();
        if ($request->method() == "POST"){
            Loader::import("FaceAPI.AipFace",EXTEND_PATH,".php");
            $data = $request->post();
            if ($data["user_info"] == null || $data["user_info"] == ""){
                $content["error_code"] = 402;
                $content["msg"] = "缺少user_info！";
                return json($content);
            }else if ($data["group"] == null || $data["group"] == ""){
                $content["error_code"] = 402;
                $content["msg"] = "缺少group！";
                return json($content);
            }else if ($data["user"] == null || $data["user"] == ""){
                $content["error_code"] = 402;
                $content["msg"] = "缺少user！";
                return json($content);
            }else if ($data["base64"] == null || $data["base64"] == ""){
                $content["error_code"] = 402;
                $content["msg"] = "缺少base64！";
                return json($content);
            }
            $image = $data["base64"];
            $imageType = "BASE64";

            $groupId = $data["group"];

            $userId = $data["user"];
            $options = array();
            $options["user_info"] = $data["user_info"];

            $client = new \AipFace($this->appId, $this->apiKey, $this->secretKey);
            // 带参数调用人脸注册
            $msg = $client->addUser($image, $imageType, $groupId, $userId, $options);
            if ($msg["error_code"] == 0){
                $content["error_code"] = 0;
                $content["msg"] = "注册成功！";
                return json($content);
            }else{
                $content["error_code"] = $msg["error_code"];
                $content["msg"] = $msg["error_msg"];
                return json($content);
            }

        }else{
            $content["error_code"] = 400;
            $content["msg"] = "请发送POST请求！";
            return json($content);
        }

    }

}