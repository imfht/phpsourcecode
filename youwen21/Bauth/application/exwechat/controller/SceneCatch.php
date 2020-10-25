<?php

namespace app\exwechat\controller;

use youwen\exwechat\exRequest;

/**
 * 微信事件消息－控制器
 *
 */
class SceneCatch
{



    public function __construct()
    {
        
    }

    public function check($keyWord, $openId, $type='chat')
    {
        // 设置场景
        $prefix = mb_substr($keyWord, 0, 2,'UTF-8');
        // 更改或删除场景值
        if($prefix == '__'){
            $value = mb_substr($keyWord, 2, mb_strlen($keyWord),'UTF-8');
            if(false !== strpos($value, ':')){
                $arr = explode(':', $value);
                if($arr[1] == "_delete"){
                    $this->deleteScene($openId, $arr[0]);
                }else{
                    $this->setScene($openId, $arr[0], $arr[1]);
                }
            }
            return true;
        }else if($prefix == '??'){
            $value = mb_substr($keyWord, 2, mb_strlen($keyWord), 'UTF-8');
            $ret = $this->getScene($openId, $value);
            $comment = $ret? $ret: "$value:场景值是空";
            echo (new \youwen\exwechat\exXMLMaker())->createText($comment);
            exit;
        }
        $ret = $this->getScene($openId, $type);
        if($ret){
            return $ret;
        }
        return false;
    }

    public function deleteScene($openId, $type)
    {
        $map['openId'] = $openId;
        $map['sceneType'] = $type;
        return db('we_scene')->where($map)->delete();
    }

    /** 
     * 获取聊天情景
     * @return string 返回用户当前聊天场景
     * @author baiyouwen
     */
    public function getScene($openId, $sceneType='chat')
    {
        $map['openId'] = $openId;
        $map['sceneType'] = $sceneType;
        $ret = db('we_scene')->where($map)->find();
        if($ret){
            return $ret['sceneValue'];
        }
        return false;
    }

    /** 
     * 设置用户聊天场景
     * @author baiyouwen
     */
    public function setScene($openId, $sceneType, $sceneValue)
    {
        $data['openId'] = $openId;
        $data['sceneValue'] = $sceneValue;
        $data['sceneType'] = $sceneType;

        $check = db('we_scene')->where(['openId'=>$data['openId'], 'sceneType'=>$data['sceneType']])->find();
        if($check){
            $ret = db('we_scene')->where(['openId'=>$data['openId'], 'sceneType'=>$data['sceneType']])->update($data);
            return $ret;
        }else{
            $ret = db('we_scene')->insert($data);
            return $ret;
        }
    }
}
