<?php
namespace WxSDK\core\module;

use WxSDK\core\common\IApp;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;
use WxSDK\core\model\user\TagModel;

class UserKit
{
    /**
     * 
     * @param IApp $App
     * @param string $tagName
     * @return \WxSDK\core\common\Ret data数组：{   "tag":{ "id":134,//标签id "name":"广东"   } }
     */
    public static function createTag(IApp $App, string $tagName){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_tag_create);
            $data = array("tag"=>array("name"=>$tagName));
//             $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }
    /**
     * 
     * @param IApp $App
     * @return \WxSDK\core\common\Ret data数组
     * {   
        * "tags":[{
            * "id":1,
            * "name":"每天一罐可乐星人",
            * "count":0 //此标签下粉丝数
        * },
        * {
            * "id":2,
            * "name":"星标组",
            * "count":0
        * },
        * {
            * "id":127,
            * "name":"广东",
            * "count":5
         * }
        * ] }
     */
    public static function getTag(IApp $App){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_tag_get);
            $ret = Tool::doCurl($url);
            return $ret;
        }else{
            return $ret;
        }
    }
    public static function updateTag(IApp $App, TagModel $tagModel){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_tag_update);
            $data = array("tag"=>$tagModel);
//             $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }
    public static function deleteTag(IApp $App, TagModel $tagModel){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_tag_delete);
            $data = array("tag"=>$tagModel);
//             $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }

    /**
     * 
     * @param IApp $App
     * @param string $tagId
     * @param string $nextOpenId 不填表示从头开始拉
     * @return \WxSDK\core\common\Ret data:
     * {  
            "count":2,//这次获取的粉丝数量   
            "data":{//粉丝列表
            "openid":[  
            "ocYxcuAEy30bX0NXmGn4ypqx3tI0",    
            "ocYxcuBt0mRugKZ7tGAHPnUaOW7Y"  ]  
        },  
            "next_openid":"ocYxcuBt0mRugKZ7tGAHPnUaOW7Y"//拉取列表最后一个用户的openid 
        }
     */
    public static function getByTag(IApp $App, string $tagId, string $nextOpenId = NULL){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_get_by_tag);
            $data = array("tagid"=>$tagId);
            if(null != $nextOpenId){
                $data["next_openid"] = $nextOpenId;
            }
//             $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }

    public static function setTag(IApp $App, string $tagId, array $openIdList){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_set_tag);
            $data = array(
                "tagid"=>$tagId,
                "openid_list"=>$openIdList
            );
//             $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }
    public static function unsetTag(IApp $App, string $tagId, array $openIdList){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_set_untag);
            $data = array(
                "tagid"=>$tagId,
                "openid_list"=>$openIdList
            );
//             $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }
    /**
     * 获取指定用户身上的标签
     * @param IApp $App
     * @param string $openId
     * @return \WxSDK\core\common\Ret data：{   "tagid_list":[//被置上的标签列表 134, 2   ] }
     */
    public static function getTagByUser(IApp $App, string $openId){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_tag_get);
            $data = array("openid"=>$openId);
//             $data = json_encode($data);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }
    public static function updateRemark(IApp $App, string $openId, string $remark){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_update_remark);
            $data = array(
                "openid"=>$openId,
                "remark"=>$remark
            );
//             $data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }

    /**
     * 
     * @param IApp $App
     * @param string $openId
     * @param string $language 返回国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语；默认简体中文
     * @return \WxSDK\core\common\Ret data:
     * {
            "subscribe": 1, 
            "openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M", 
            "nickname": "Band", 
            "sex": 1, 
            "language": "zh_CN", 
            "city": "广州", 
            "province": "广东", 
            "country": "中国", 
            "headimgurl":"http://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
            "subscribe_time": 1382694957,
            "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
            "remark": "",
            "groupid": 0,
            "tagid_list":[128,2],
            "subscribe_scene": "ADD_SCENE_QR_CODE",
            "qr_scene": 98765,
            "qr_scene_str": ""
        }
     */
    public static function getInfo(IApp $App, string $openId, string $language = null){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $language = $language ? $language : "zh_CN";
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_get_info);
            $url = str_replace("OPENID", $openId, $url);
            $url = str_replace("LANG", $language, $url);
            $ret = Tool::doCurl($url);
            return $ret;
        }else{
            return $ret;
        }
    }

    /**
     * 
     * @param IApp $App
     * @param array $openIds
     * @param string $language
     * @return \WxSDK\core\common\Ret data:正常情况下，微信会返回下述JSON数据包给公众号（示例中为一次性拉取了2个openid的用户基本信息，第一个是已关注的，第二个是未关注的）
     * {
           "user_info_list": [
               {
                   "subscribe": 1, 
                   "openid": "otvxTs4dckWG7imySrJd6jSi0CWE", 
                   "nickname": "iWithery", 
                   "sex": 1, 
                   "language": "zh_CN", 
                   "city": "揭阳", 
                   "province": "广东", 
                   "country": "中国", 
        
                   "headimgurl": "http://thirdwx.qlogo.cn/mmopen/xbIQx1GRqdvyqkMMhEaGOX802l1CyqMJNgUzKP8MeAeHFicRDSnZH7FY4XB7p8XHXIf6uJA2SCunTPicGKezDC4saKISzRj3nz/0",
        
                  "subscribe_time": 1434093047, 
                   "unionid": "oR5GjjgEhCMJFyzaVZdrxZ2zRRF4", 
                   "remark": "", 
        
                   "groupid": 0,
                   "tagid_list":[128,2],
                   "subscribe_scene": "ADD_SCENE_QR_CODE",
                   "qr_scene": 98765,
                   "qr_scene_str": ""
        
              }, 
               {
                   "subscribe": 0, 
                   "openid": "otvxTs_JZ6SEiP0imdhpi50fuSZg"
               }
           ]
        }
     */
    public static function getInfoList(IApp $App, array $openIds, string $language = null){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $language = $language ? $language : "zh_CN";
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_get_info_list);
            $list = [];
            foreach ($openIds as $v){
                $list[] = array(
                    "openid"=>$v,
                    "lang" =>$language
                );
            }
            $data = array(
                "user_list"=>$list
            );
//             $data = json_encode($data);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }

    /**
     * 
     * @param IApp $App
     * @param string $nextOpenid
     * @return \WxSDK\core\common\Ret data:
     * {
            "total":2,
            "count":2,
            "data":{
            "openid":["OPENID1","OPENID2"]},
            "next_openid":"NEXT_OPENID"
        }
     * 一次拉取调用最多拉取10000个关注者的OpenID，可以通过多次拉取的方式来满足需求
     * 关注者列表已返回完时，返回next_openid为空
     */
    public static function getList(IApp $App, string $nextOpenid = null){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $nextOpenid = $nextOpenid ? $nextOpenid : "";
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_get_list);
            $url = str_replace("NEXT_OPENID", $nextOpenid, $url);
            $ret = Tool::doCurl($url);
            return $ret;
        }else{
            return $ret;
        }
    }

    /**
     * 
     * @param IApp $App
     * @param string $nextOpenid
     * @return \WxSDK\core\common\Ret data: 每次调用最多可拉取 10000 个OpenID，当列表数较多时，可以通过多次拉取的方式来满足需求
     * {
         "total":23000,
         "count":10000,
         "data":{"
            openid":[
               "OPENID1",
               "OPENID2",
               ...,
               "OPENID10000"
            ]
          },
          "next_openid":"OPENID10000"
        }
     */
    public static function getBlackList(IApp $App, string $nextOpenid = null){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $nextOpenid = $nextOpenid ? $nextOpenid : "";
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_get_blacklist);
            $data = array(
                "begin_openid"=> $nextOpenid
            );
//             $data = json_encode($data);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }

    /**
     * 
     * @param IApp $App
     * @param array $openIds 最多20个openid
     * @return \WxSDK\core\common\Ret
     */
    public static function addBlackList(IApp $App, array $openIds){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_add_blacklist);
            $data = array(
                "openid_list"=> $openIds
            );
//             $data = json_encode($data);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }

    /**
     * 
     * @param IApp $App
     * @param array $openIds 最多20个openid
     * @return \WxSDK\core\common\Ret
     */
    public static function deleteBlackList(IApp $App, array $openIds){
        $ret = $App->getAccessToken();
        if($ret->ok()){
            $url = str_replace("ACCESS_TOKEN", $ret->getData(), Config::$user_delete_blacklist);
            $data = array(
                "openid_list"=> $openIds
            );
//             $data = json_encode($data);
            $ret = Tool::doCurl($url,$data);
            return $ret;
        }else{
            return $ret;
        }
    }
}