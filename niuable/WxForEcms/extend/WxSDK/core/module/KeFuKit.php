<?php
namespace WxSDK\core\module;

use WxSDK\core\common\IApp;
use WxSDK\core\model\KeFu;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;
use WxSDK\core\model\kefu\MenuItem;
use WxSDK\core\model\Model;
use WxSDK\Request;
use WxSDK\Url;

class KeFuKit
{

    public static function addkf(IApp $App, KeFu $kf)
    {
        $request = new Request($App, $kf, new Url(Config::$add_kf));
        return $request->run();
    }

    public static function updatekf(IApp $App, KeFu $kf)
    {
        $request = new Request($App, $kf, new Url(Config::$update_kf));
        return $request->run();
    }

    public static function deletekf(IApp $App, KeFu $kf)
    {
        $request = new Request($App, $kf, new Url(Config::$delete_kf));
        return $request->run();
    }

    /**
     * 更新客服头像
     * @param IApp $App
     * @param string $kfAccount
     *            客服账号
     * @param string $filename
     *            头像的文件名，含路径
     * @return \WxSDK\core\common\Ret
     */
    public static function updateKfHeadImage(IApp $App, string $kfAccount, string $filename)
    {
        $url = str_replace("KFACCOUNT", $kfAccount, Config::$kf_update_head_image);
        $media = Tool::createMediaData($filename);
        $model = new Model($media, TRUE);
        
        $request = new Request($App, $model, new Url($url));
        return $request->run();
    }

    /**
     * 获取客服列表
     *
     * @param IApp $App
     * @return \WxSDK\core\common\Ret
     */
    public static function getKfList(IApp $App)
    {
        $model = new Model();
        $request = new Request($App, $model, new Url(Config::$kf_get_list));
        return $request->run();
    }

    public static function sendTextMsg(IApp $App, string $toUserOpenId
        , string $text, string $kfAccount = NULL)
    {
        $data = array(
            "touser" => $toUserOpenId,
            "msgtype" => "text",
            "text" => array(
                "content" => $text
            )
        );
        if(null != $kfAccount){
            $data["customservice"] = array(
                "kf_account" =>$kfAccount
            );
        }
        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_send_msg));
        return $request->run();
    }

    public static function sendImageMsg(IApp $App, string $toUserOpenId
        , string $mediaId, string $kfAccount = NULL)
    {
        $data = array(
            "touser" => $toUserOpenId,
            "msgtype" => "image",
            "image" => array(
                "media_id" => $mediaId
            )
        );
        if(null != $kfAccount){
            $data["customservice"] = array(
                "kf_account" =>$kfAccount
            );
        }
        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_send_msg));
        return $request->run();
    }
    public static function sendVoiceMsg(IApp $App, string $toUserOpenId
        , string $mediaId, string $kfAccount = NULL)
    {
        $data = array(
            "touser" => $toUserOpenId,
            "msgtype" => "voice",
            "voice" => array(
                "media_id" => $mediaId
            )
        );
        if(null != $kfAccount){
            $data["customservice"] = array(
                "kf_account" =>$kfAccount
            );
        }
        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_send_msg));
        return $request->run();
    }
    public static function sendVideoMsg(IApp $App, string $toUserOpenId
        , string $mediaId, string $thumbMediaId, string $title, string $description
        , string $kfAccount = NULL)
    {
        $data = array(
            "touser" => $toUserOpenId,
            "msgtype" => "video",
            "video" => array(
                "media_id" => $mediaId,
                'thumb_media_id' => $thumbMediaId,
                "title"=>$title,
                "description"=>$description
            )
        );
        if(null != $kfAccount){
            $data["customservice"] = array(
                "kf_account" =>$kfAccount
            );
        }
        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_send_msg));
        return $request->run();
    }
    /**
     * 
     * @param IApp $App
     * @param string $toUserOpenId
     * @param string $musicUrl
     * @param string $thumbMediaId
     * @param string $title
     * @param string $description
     * @param string $hqMusicUrl 高清链接，如果空，赋值$musicUrl
     * @return \WxSDK\core\common\Ret
     */
    public static function sendMusicMsg(IApp $App, string $toUserOpenId
        , string $musicUrl, string $thumbMediaId, string $title
        , string $description, string $hqMusicUrl = null, string $kfAccount = NULL)
    {
        $hqMusicUrl = $hqMusicUrl?$hqMusicUrl : $musicUrl;
        $data = array(
            "touser" => $toUserOpenId,
            "msgtype" => "music",
            "music" => array(
                "title"=>$title,
                "description"=>$description,
                "musicurl" => $musicUrl,
                'thumb_media_id' => $thumbMediaId,
                "hqmusicurl"=>$hqMusicUrl
            )
        );
        if(null != $kfAccount){
            $data["customservice"] = array(
                "kf_account" =>$kfAccount
            );
        }
        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_send_msg));
        return $request->run();
    }
    /**
     * 发送图文消息（点击跳转到外链）
     * @param IApp $App
     * @param string $toUserOpenId
     * @param string $url
     * @param string $title
     * @param string $description
     * @param string $picUrl
     * @param string $kfAccount
     * @return \WxSDK\core\common\Ret
     */
    public static function sendNewsMsgOuter(IApp $App, string $toUserOpenId
        , string $url, string $title, string $description, string $picUrl, string $kfAccount = NULL)
    {
        $data = array(
            "touser" => $toUserOpenId,
            "msgtype" => "news",
            "news" => array(
                "articles"=>array(
                    array(
                        "title"=>$title,
                        "description"=>$description,
                        "url" => $url,
                        "picurl"=>$picUrl
                    )
                )
            )
        );
        if(null != $kfAccount){
            $data["customservice"] = array(
                "kf_account" =>$kfAccount
            );
        }
        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_send_msg));
        return $request->run();
    }
   /**
    * 发送图文消息（点击跳转到图文消息页面） 
    * @param IApp $App
    * @param string $toUserOpenId
    * @param string $mediaId
    * @return \WxSDK\core\common\Ret
    */ 
    public static function sendNewsMsgInner(IApp $App, string $toUserOpenId
        , string $mediaId, $kfAccount = null)
    {
        $data = array(
            "touser" => $toUserOpenId,
            "msgtype" => "mpnews",
            "mpnews" => array(
                "media_id"=>$mediaId
            )
        );
        if(null != $kfAccount){
            $data["customservice"] = array(
                "kf_account" =>$kfAccount
            );
        }
        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_send_msg));
        return $request->run();
    }
    /**
     * 发送菜单消息，用户可以点击菜单，公众号可以收到text消息
     * 收到的消息中有bizmsgmenuid字段
     * @param IApp $App
     * @param string $toUserOpenId
     * @param string $headContent
     * @param string $tailContent
     * @param string $kfAccount
     * @param MenuItem ...$menuItems
     * @return \WxSDK\core\common\Ret
     */
    public static function sendMenuMsg(IApp $App, string $toUserOpenId
        , string $headContent, string $tailContent, string $kfAccount=null, MenuItem... $menuItems)
    {
        $data = array(
            "touser" => $toUserOpenId,
            "msgtype" => "msgmenu",
            "msgmenu" => array(
                "head_content"=>$headContent,
                "tail_content"=>$tailContent,
                "item"=>$menuItems
            )
        );
        if(null != $kfAccount){
            $data["customservice"] = array(
                "kf_account" =>$kfAccount
            );
        }
        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_send_msg));
        return $request->run();
    }
    public static function createSession(IApp $App, $toUserOpenId, $kfAccount){
        $data = array(
            "kf_account" => $kfAccount,
            "openid" => $toUserOpenId
        );

        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_creat_session));
        return $request->run();
    }
    public static function closeSession(IApp $App, $toUserOpenId, $kfAccount){
        $data = array(
            "kf_account" => $kfAccount,
            "openid" => $toUserOpenId
        );

        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_close_session));
        return $request->run();
    }
    public static function getSession(IApp $App, $toUserOpenId){
        $url = str_replace('OPENID', $toUserOpenId, Config::$kf_get_session);
        $model = new Model();
        $request = new Request($App, $model, new Url($url));
        return $request->run();
    }
    public static function getSessionList(IApp $App, $kfAccount){
        $url = str_replace('KFACCOUNT', $kfAccount, Config::$kf_get_session_list);
        $model = new Model();
        $request = new Request($App, $model, new Url($url));
        return $request->run();
    }
    /**
     * 获取未接入会话的列表
     */
    public static function getWaitCaseList(IApp $App){
        $model = new Model();
        $request = new Request($App, $model, new Url(Config::$kf_get_wait_case_list));
        return $request->run();
    }
    /**
     * @param IApp $App
     * @param int $starttime
     * @param int $endtime
     * @param int $msgid
     * @param int $number 每次获取条数，最多10000条
     * 
     * @return \WxSDK\core\common\Ret
     */
    public static function getMsgRecordList(IApp $App, int $starttime, int $endtime, $msgid = 1, int $number = 10000){
        $array = [
            "starttime" =>$starttime,
            "endtime" =>$endtime,
            "msgid" => $msgid,
            "number" => $number
        ];
        $model = new Model();
        $request = new Request($App, $model, new Url(Config::$kf_get_msg_list));
        return $request->run();
    }
    /**
     * 发送卡券
     * 特别注意:客服消息接口投放卡券仅支持非自定义Code码和导入code模式的卡券的卡券
     * @param IApp $App
     * @param string $toUserOpenId
     * @param string $cardId
     * @return \WxSDK\core\common\Ret
     */
    public static function sendCardMsg(IApp $App, string $toUserOpenId, string $cardId, $kfAccount = null)
    {
        $data = array(
            "touser" => $toUserOpenId,
            "msgtype" => "wxcard",
            "wxcard" => array(
                "card_id"=>$cardId
            )
        );
        if(null != $kfAccount){
            $data["customservice"] = array(
                "kf_account" =>$kfAccount
            );
        }
        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_send_msg));
        return $request->run();
    }
    
    public static function sendMiniProgramMsg(IApp $App, string $toUserOpenId, string $title, string $appid, string $pagepath, string $thumbMediaId, $kfAccount = null)
    {
        $data = array(
            "touser" => $toUserOpenId,
            "msgtype" => "miniprogrampage",
            "miniprogrampage" => array(
                "title"=>$title,
                "appid"=>$appid,
                "pagepath"=>$pagepath,
                "thumb_media_id"=>$thumbMediaId
            )
        );
        if(null != $kfAccount){
            $data["customservice"] = array(
                "kf_account" =>$kfAccount
            );
        }
        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_send_msg));
        return $request->run();
    }
    /**
     * 发送输入状态
     * 下发输入状态，需要客服之前30秒内跟用户有过消息交互。
     * 在输入状态中（持续15s），不可重复下发输入态。
     * 在输入状态中，如果向用户下发消息，会同时取消输入状态。
     * @param IApp $App
     * @param string $toUserOpenId
     * @return \WxSDK\core\common\Ret
     */
    public static function sendInputState(IApp $App, string $toUserOpenId) {
        $data = array(
            "touser" => $toUserOpenId,
            "command" => "Typing",
        );
        $model = new Model($data);
        $request = new Request($App, $model, new Url(Config::$kf_send_msg));
        return $request->run();
    }
}

