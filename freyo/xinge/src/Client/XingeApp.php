<?php

namespace Freyo\Xinge\Client;

class XingeApp
{
    const DEVICE_ALL = 0;
    const DEVICE_BROWSER = 1;
    const DEVICE_PC = 2;
    const DEVICE_ANDROID = 3;
    const DEVICE_IOS = 4;
    const DEVICE_WINPHONE = 5;

    const IOSENV_PROD = 1;
    const IOSENV_DEV = 2;

    const IOS_MIN_ID = 2200000000;
    const RESTAPI_PUSHSINGLEDEVICE = 'http://openapi.xg.qq.com/v2/push/single_device';
    const RESTAPI_PUSHSINGLEACCOUNT = 'http://openapi.xg.qq.com/v2/push/single_account';
    const RESTAPI_PUSHACCOUNTLIST = 'http://openapi.xg.qq.com/v2/push/account_list';
    const RESTAPI_PUSHALLDEVICE = 'http://openapi.xg.qq.com/v2/push/all_device';
    const RESTAPI_PUSHTAGS = 'http://openapi.xg.qq.com/v2/push/tags_device';
    const RESTAPI_QUERYPUSHSTATUS = 'http://openapi.xg.qq.com/v2/push/get_msg_status';
    const RESTAPI_QUERYDEVICECOUNT = 'http://openapi.xg.qq.com/v2/application/get_app_device_num';
    const RESTAPI_QUERYTAGS = 'http://openapi.xg.qq.com/v2/tags/query_app_tags';
    const RESTAPI_CANCELTIMINGPUSH = 'http://openapi.xg.qq.com/v2/push/cancel_timing_task';
    const RESTAPI_BATCHSETTAG = 'http://openapi.xg.qq.com/v2/tags/batch_set';
    const RESTAPI_BATCHDELTAG = 'http://openapi.xg.qq.com/v2/tags/batch_del';
    const RESTAPI_QUERYTOKENTAGS = 'http://openapi.xg.qq.com/v2/tags/query_token_tags';
    const RESTAPI_QUERYTAGTOKENNUM = 'http://openapi.xg.qq.com/v2/tags/query_tag_token_num';
    const RESTAPI_CREATEMULTIPUSH = 'http://openapi.xg.qq.com/v2/push/create_multipush';
    const RESTAPI_PUSHACCOUNTLISTMULTIPLE = 'http://openapi.xg.qq.com/v2/push/account_list_multiple';
    const RESTAPI_PUSHDEVICELISTMULTIPLE = 'http://openapi.xg.qq.com/v2/push/device_list_multiple';
    const RESTAPI_QUERYINFOOFTOKEN = 'http://openapi.xg.qq.com/v2/application/get_app_token_info';
    const RESTAPI_QUERYTOKENSOFACCOUNT = 'http://openapi.xg.qq.com/v2/application/get_app_account_tokens';
    const RESTAPI_DELETETOKENOFACCOUNT = 'http://openapi.xg.qq.com/v2/application/del_app_account_tokens';
    const RESTAPI_DELETEALLTOKENSOFACCOUNT = 'http://openapi.xg.qq.com/v2/application/del_app_account_all_tokens';

    public $accessId = ''; //应用的接入Id
    public $secretKey = ''; //应用的skey

    public function __construct($accessId, $secretKey)
    {
        assert(isset($accessId) && isset($secretKey));

        $this->accessId = $accessId;
        $this->secretKey = $secretKey;
    }

    /**
     * 使用默认设置推送消息给单个android设备.
     */
    public static function PushTokenAndroid($accessId, $secretKey, $title, $content, $token)
    {
        $push = new self($accessId, $secretKey);
        $mess = new Message();
        $mess->setTitle($title);
        $mess->setContent($content);
        $mess->setType(Message::TYPE_NOTIFICATION);
        $mess->setStyle(new Style(0, 1, 1, 1, 0));
        $action = new ClickAction();
        $action->setActionType(ClickAction::TYPE_ACTIVITY);
        $mess->setAction($action);
        $ret = $push->PushSingleDevice($token, $mess);

        return $ret;
    }

    /**
     * 推送消息给单个设备.
     */
    public function PushSingleDevice($deviceToken, $message, $environment = 0)
    {
        $ret = ['ret_code' => -1, 'err_msg' => 'message not valid'];

        if (!($message instanceof Message) && !($message instanceof MessageIOS)) {
            return $ret;
        }
        if (!$this->ValidateMessageType($message)) {
            $ret['err_msg'] = 'message type not fit accessId';

            return $ret;
        }
        if ($message instanceof MessageIOS) {
            if ($environment != self::IOSENV_DEV && $environment != self::IOSENV_PROD) {
                $ret['err_msg'] = 'ios message environment invalid';

                return $ret;
            }
        }
        if (!$message->isValid()) {
            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['expire_time'] = $message->getExpireTime();
        $params['send_time'] = $message->getSendTime();
        if ($message instanceof Message) {
            $params['multi_pkg'] = $message->getMultiPkg();
        }
        $params['device_token'] = $deviceToken;
        $params['message_type'] = $message->getType();
        $params['message'] = $message->toJson();
        $params['timestamp'] = time();
        $params['environment'] = $environment;

        return $this->callRestful(self::RESTAPI_PUSHSINGLEDEVICE, $params);
    }

    private function ValidateMessageType($message)
    {
        if ((float) $this->accessId >= self::IOS_MIN_ID and $message instanceof MessageIOS) {
            return true;
        } elseif ((float) $this->accessId < self::IOS_MIN_ID and $message instanceof Message) {
            return true;
        } else {
            return false;
        }
    }

    protected function callRestful($url, $params)
    {
        $paramsBase = new ParamsBase($params);
        $sign = $paramsBase->generateSign(RequestBase::METHOD_POST, $url, $this->secretKey);
        $params['sign'] = $sign;

        $requestBase = new RequestBase();
        $ret = $this->json2Array($requestBase->exec($url, $params, RequestBase::METHOD_POST));

        return $ret;
    }

    //json转换为数组
    protected function json2Array($json)
    {
        $json = stripslashes($json);

        return json_decode($json, true);
    }

    /**
     * 使用默认设置推送消息给单个ios设备.
     */
    public static function PushTokenIos($accessId, $secretKey, $content, $token, $environment)
    {
        $push = new self($accessId, $secretKey);
        $mess = new MessageIOS();
        $mess->setAlert($content);
        $ret = $push->PushSingleDevice($token, $mess, $environment);

        return $ret;
    }

    /**
     * 使用默认设置推送消息给单个android版账户.
     */
    public static function PushAccountAndroid($accessId, $secretKey, $title, $content, $account)
    {
        $push = new self($accessId, $secretKey);
        $mess = new Message();
        $mess->setTitle($title);
        $mess->setContent($content);
        $mess->setType(Message::TYPE_NOTIFICATION);
        $mess->setStyle(new Style(0, 1, 1, 1, 0));
        $action = new ClickAction();
        $action->setActionType(ClickAction::TYPE_ACTIVITY);
        $mess->setAction($action);
        $ret = $push->PushSingleAccount(0, $account, $mess);

        return $ret;
    }

    /**
     * 推送消息给单个账户.
     */
    public function PushSingleAccount($deviceType, $account, $message, $environment = 0)
    {
        $ret = ['ret_code' => -1];
        if (!is_int($deviceType) || $deviceType < 0 || $deviceType > 5) {
            $ret['err_msg'] = 'deviceType not valid';

            return $ret;
        }
        if (!is_string($account) || empty($account)) {
            $ret['err_msg'] = 'account not valid';

            return $ret;
        }
        if (!($message instanceof Message) && !($message instanceof MessageIOS)) {
            $ret['err_msg'] = 'message is not android or ios';

            return $ret;
        }
        if (!$this->ValidateMessageType($message)) {
            $ret['err_msg'] = 'message type not fit accessId';

            return $ret;
        }
        if ($message instanceof MessageIOS) {
            if ($environment != self::IOSENV_DEV && $environment != self::IOSENV_PROD) {
                $ret['err_msg'] = 'ios message environment invalid';

                return $ret;
            }
        }
        if (!$message->isValid()) {
            $ret['err_msg'] = 'message not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['expire_time'] = $message->getExpireTime();
        $params['send_time'] = $message->getSendTime();
        if ($message instanceof Message) {
            $params['multi_pkg'] = $message->getMultiPkg();
        }
        $params['device_type'] = $deviceType;
        $params['account'] = $account;
        $params['message_type'] = $message->getType();
        $params['message'] = $message->toJson();
        $params['timestamp'] = time();
        $params['environment'] = $environment;

        return $this->callRestful(self::RESTAPI_PUSHSINGLEACCOUNT, $params);
    }

    /**
     * 使用默认设置推送消息给单个ios版账户.
     */
    public static function PushAccountIos($accessId, $secretKey, $content, $account, $environment)
    {
        $push = new self($accessId, $secretKey);
        $mess = new MessageIOS();
        $mess->setAlert($content);
        $ret = $push->PushSingleAccount(0, $account, $mess, $environment);

        return $ret;
    }

    /**
     * 使用默认设置推送消息给所有设备android版.
     */
    public static function PushAllAndroid($accessId, $secretKey, $title, $content)
    {
        $push = new self($accessId, $secretKey);
        $mess = new Message();
        $mess->setTitle($title);
        $mess->setContent($content);
        $mess->setType(Message::TYPE_NOTIFICATION);
        $mess->setStyle(new Style(0, 1, 1, 1, 0));
        $action = new ClickAction();
        $action->setActionType(ClickAction::TYPE_ACTIVITY);
        $mess->setAction($action);
        $ret = $push->PushAllDevices(0, $mess);

        return $ret;
    }

    /**
     * 推送消息给APP所有设备.
     */
    public function PushAllDevices($deviceType, $message, $environment = 0)
    {
        $ret = ['ret_code' => -1, 'err_msg' => 'message not valid'];
        if (!is_int($deviceType) || $deviceType < 0 || $deviceType > 5) {
            $ret['err_msg'] = 'deviceType not valid';

            return $ret;
        }

        if (!($message instanceof Message) && !($message instanceof MessageIOS)) {
            return $ret;
        }
        if (!$this->ValidateMessageType($message)) {
            $ret['err_msg'] = 'message type not fit accessId';

            return $ret;
        }
        if ($message instanceof MessageIOS) {
            if ($environment != self::IOSENV_DEV && $environment != self::IOSENV_PROD) {
                $ret['err_msg'] = 'ios message environment invalid';

                return $ret;
            }
        }
        if (!$message->isValid()) {
            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['expire_time'] = $message->getExpireTime();
        $params['send_time'] = $message->getSendTime();
        if ($message instanceof Message) {
            $params['multi_pkg'] = $message->getMultiPkg();
        }
        $params['device_type'] = $deviceType;
        $params['message_type'] = $message->getType();
        $params['message'] = $message->toJson();
        $params['timestamp'] = time();
        $params['environment'] = $environment;

        if (!is_null($message->getLoopInterval()) && $message->getLoopInterval() > 0
            && !is_null($message->getLoopTimes()) && $message->getLoopTimes() > 0
        ) {
            $params['loop_interval'] = $message->getLoopInterval();
            $params['loop_times'] = $message->getLoopTimes();
        }
        //var_dump($params);

        return $this->callRestful(self::RESTAPI_PUSHALLDEVICE, $params);
    }

    /**
     * 使用默认设置推送消息给所有设备ios版.
     */
    public static function PushAllIos($accessId, $secretKey, $content, $environment)
    {
        $push = new self($accessId, $secretKey);
        $mess = new MessageIOS();
        $mess->setAlert($content);
        $ret = $push->PushAllDevices(0, $mess, $environment);

        return $ret;
    }

    /**
     * 使用默认设置推送消息给标签选中设备android版.
     */
    public static function PushTagAndroid($accessId, $secretKey, $title, $content, $tag)
    {
        $push = new self($accessId, $secretKey);
        $mess = new Message();
        $mess->setTitle($title);
        $mess->setContent($content);
        $mess->setType(Message::TYPE_NOTIFICATION);
        $mess->setStyle(new Style(0, 1, 1, 1, 0));
        $action = new ClickAction();
        $action->setActionType(ClickAction::TYPE_ACTIVITY);
        $mess->setAction($action);
        $ret = $push->PushTags(0, [0 => $tag], 'OR', $mess);

        return $ret;
    }

    /**
     * 推送消息给指定tags的设备
     * 若要推送的tagList只有一项，则tagsOp应为OR.
     */
    public function PushTags($deviceType, $tagList, $tagsOp, $message, $environment = 0)
    {
        $ret = ['ret_code' => -1, 'err_msg' => 'message not valid'];
        if (!is_int($deviceType) || $deviceType < 0 || $deviceType > 5) {
            $ret['err_msg'] = 'deviceType not valid';

            return $ret;
        }
        if (!is_array($tagList) || empty($tagList)) {
            $ret['err_msg'] = 'tagList not valid';

            return $ret;
        }
        if (!is_string($tagsOp) || ($tagsOp != 'AND' && $tagsOp != 'OR')) {
            $ret['err_msg'] = 'tagsOp not valid';

            return $ret;
        }

        if (!($message instanceof Message) && !($message instanceof MessageIOS)) {
            return $ret;
        }
        if (!$this->ValidateMessageType($message)) {
            $ret['err_msg'] = 'message type not fit accessId';

            return $ret;
        }
        if ($message instanceof MessageIOS) {
            if ($environment != self::IOSENV_DEV && $environment != self::IOSENV_PROD) {
                $ret['err_msg'] = 'ios message environment invalid';

                return $ret;
            }
        }
        if (!$message->isValid()) {
            return $ret;
        }

        $params = [];
        $params['access_id'] = $this->accessId;
        $params['expire_time'] = $message->getExpireTime();
        $params['send_time'] = $message->getSendTime();
        if ($message instanceof Message) {
            $params['multi_pkg'] = $message->getMultiPkg();
        }
        $params['device_type'] = $deviceType;
        $params['message_type'] = $message->getType();
        $params['tags_list'] = json_encode($tagList);
        $params['tags_op'] = $tagsOp;
        $params['message'] = $message->toJson();
        $params['timestamp'] = time();
        $params['environment'] = $environment;

        if (!is_null($message->getLoopInterval()) && $message->getLoopInterval() > 0
            && !is_null($message->getLoopTimes()) && $message->getLoopTimes() > 0
        ) {
            $params['loop_interval'] = $message->getLoopInterval();
            $params['loop_times'] = $message->getLoopTimes();
        }

        return $this->callRestful(self::RESTAPI_PUSHTAGS, $params);
    }

    /**
     * 使用默认设置推送消息给标签选中设备ios版.
     */
    public static function PushTagIos($accessId, $secretKey, $content, $tag, $environment)
    {
        $push = new self($accessId, $secretKey);
        $mess = new MessageIOS();
        $mess->setAlert($content);
        $ret = $push->PushTags(0, [0 => $tag], 'OR', $mess, $environment);

        return $ret;
    }

    public function __destruct()
    {
    }

    /**
     * 推送消息给多个账户.
     */
    public function PushAccountList($deviceType, $accountList, $message, $environment = 0)
    {
        $ret = ['ret_code' => -1];
        if (!is_int($deviceType) || $deviceType < 0 || $deviceType > 5) {
            $ret['err_msg'] = 'deviceType not valid';

            return $ret;
        }
        if (!is_array($accountList) || empty($accountList)) {
            $ret['err_msg'] = 'accountList not valid';

            return $ret;
        }
        if (!($message instanceof Message) && !($message instanceof MessageIOS)) {
            $ret['err_msg'] = 'message is not android or ios';

            return $ret;
        }
        if (!$this->ValidateMessageType($message)) {
            $ret['err_msg'] = 'message type not fit accessId';

            return $ret;
        }
        if ($message instanceof MessageIOS) {
            if ($environment != self::IOSENV_DEV && $environment != self::IOSENV_PROD) {
                $ret['err_msg'] = 'ios message environment invalid';

                return $ret;
            }
        }
        if (!$message->isValid()) {
            $ret['err_msg'] = 'message not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['expire_time'] = $message->getExpireTime();
        if ($message instanceof Message) {
            $params['multi_pkg'] = $message->getMultiPkg();
        }
        $params['device_type'] = $deviceType;
        $params['account_list'] = json_encode($accountList);
        $params['message_type'] = $message->getType();
        $params['message'] = $message->toJson();
        $params['timestamp'] = time();
        $params['environment'] = $environment;

        return $this->callRestful(self::RESTAPI_PUSHACCOUNTLIST, $params);
    }

    /**
     * 创建批量推送任务
     */
    public function CreateMultipush($message, $environment = 0)
    {
        $ret = ['ret_code' => -1];
        if (!($message instanceof Message) && !($message instanceof MessageIOS)) {
            $ret['err_msg'] = 'message is not android or ios';

            return $ret;
        }
        if (!$this->ValidateMessageType($message)) {
            $ret['err_msg'] = 'message type not fit accessId';

            return $ret;
        }
        if ($message instanceof MessageIOS) {
            if ($environment != self::IOSENV_DEV && $environment != self::IOSENV_PROD) {
                $ret['err_msg'] = 'ios message environment invalid';

                return $ret;
            }
        }
        if (!$message->isValid()) {
            $ret['err_msg'] = 'message not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['expire_time'] = $message->getExpireTime();
        if ($message instanceof Message) {
            $params['multi_pkg'] = $message->getMultiPkg();
        }
        $params['message_type'] = $message->getType();
        $params['message'] = $message->toJson();
        $params['timestamp'] = time();
        $params['environment'] = $environment;

        return $this->callRestful(self::RESTAPI_CREATEMULTIPUSH, $params);
    }

    /**
     * 按帐号大批量推送
     */
    public function PushAccountListMultiple($pushId, $accountList)
    {
        $pushId = intval($pushId);
        $ret = ['ret_code' => -1];
        if ($pushId <= 0) {
            $ret['err_msg'] = 'pushId not valid';

            return $ret;
        }
        if (!is_array($accountList) || empty($accountList)) {
            $ret['err_msg'] = 'accountList not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['push_id'] = $pushId;
        $params['account_list'] = json_encode($accountList);
        $params['timestamp'] = time();

        return $this->callRestful(self::RESTAPI_PUSHACCOUNTLISTMULTIPLE, $params);
    }

    /**
     * 按Token大批量推送
     */
    public function PushDeviceListMultiple($pushId, $deviceList)
    {
        $pushId = intval($pushId);
        $ret = ['ret_code' => -1];
        if ($pushId <= 0) {
            $ret['err_msg'] = 'pushId not valid';

            return $ret;
        }
        if (!is_array($deviceList) || empty($deviceList)) {
            $ret['err_msg'] = 'deviceList not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['push_id'] = $pushId;
        $params['device_list'] = json_encode($deviceList);
        $params['timestamp'] = time();

        return $this->callRestful(self::RESTAPI_PUSHDEVICELISTMULTIPLE, $params);
    }

    /**
     * 查询消息推送状态
     *
     * @param array $pushIdList pushId(string)数组
     */
    public function QueryPushStatus($pushIdList)
    {
        $ret = ['ret_code' => -1];
        $idList = [];
        if (!is_array($pushIdList) || empty($pushIdList)) {
            $ret['err_msg'] = 'pushIdList not valid';

            return $ret;
        }
        foreach ($pushIdList as $pushId) {
            $idList[] = ['push_id' => $pushId];
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['push_ids'] = json_encode($idList);
        $params['timestamp'] = time();

        return $this->callRestful(self::RESTAPI_QUERYPUSHSTATUS, $params);
    }

    /**
     * 查询应用覆盖的设备数.
     */
    public function QueryDeviceCount()
    {
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['timestamp'] = time();

        return $this->callRestful(self::RESTAPI_QUERYDEVICECOUNT, $params);
    }

    /**
     * 查询应用标签.
     */
    public function QueryTags($start = 0, $limit = 100)
    {
        $ret = ['ret_code' => -1];
        if (!is_int($start) || !is_int($limit)) {
            $ret['err_msg'] = 'start or limit not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['start'] = $start;
        $params['limit'] = $limit;
        $params['timestamp'] = time();

        return $this->callRestful(self::RESTAPI_QUERYTAGS, $params);
    }

    /**
     * 查询标签下token数量.
     */
    public function QueryTagTokenNum($tag)
    {
        $ret = ['ret_code' => -1];
        if (!is_string($tag)) {
            $ret['err_msg'] = 'tag is not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['tag'] = $tag;
        $params['timestamp'] = time();

        return $this->callRestful(self::RESTAPI_QUERYTAGTOKENNUM, $params);
    }

    /**
     * 查询token的标签.
     */
    public function QueryTokenTags($deviceToken)
    {
        $ret = ['ret_code' => -1];
        if (!is_string($deviceToken)) {
            $ret['err_msg'] = 'deviceToken is not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['device_token'] = $deviceToken;
        $params['timestamp'] = time();

        return $this->callRestful(self::RESTAPI_QUERYTOKENTAGS, $params);
    }

    /**
     * 取消定时发送
     */
    public function CancelTimingPush($pushId)
    {
        $ret = ['ret_code' => -1];
        if (!is_string($pushId) || empty($pushId)) {
            $ret['err_msg'] = 'pushId not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['push_id'] = $pushId;
        $params['timestamp'] = time();

        return $this->callRestful(self::RESTAPI_CANCELTIMINGPUSH, $params);
    }

    public function BatchSetTag($tagTokenPairs)
    {
        $ret = ['ret_code' => -1];

        foreach ($tagTokenPairs as $pair) {
            if (!($pair instanceof TagTokenPair)) {
                $ret['err_msg'] = 'tag-token pair type error!';

                return $ret;
            }
            if (!$this->ValidateToken($pair->token)) {
                $ret['err_msg'] = sprintf('invalid token %s', $pair->token);

                return $ret;
            }
        }
        $params = $this->InitParams();

        $tag_token_list = [];
        foreach ($tagTokenPairs as $pair) {
            array_push($tag_token_list, [$pair->tag, $pair->token]);
        }
        $params['tag_token_list'] = json_encode($tag_token_list);

        return $this->callRestful(self::RESTAPI_BATCHSETTAG, $params);
    }

    private function ValidateToken($token)
    {
        if (intval($this->accessId) >= 2200000000) {
            return strlen($token) == 64;
        } else {
            return strlen($token) == 40 || strlen($token) == 64;
        }
    }

    public function InitParams()
    {
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['timestamp'] = time();

        return $params;
    }

    public function BatchDelTag($tagTokenPairs)
    {
        $ret = ['ret_code' => -1];

        foreach ($tagTokenPairs as $pair) {
            if (!($pair instanceof TagTokenPair)) {
                $ret['err_msg'] = 'tag-token pair type error!';

                return $ret;
            }
            if (!$this->ValidateToken($pair->token)) {
                $ret['err_msg'] = sprintf('invalid token %s', $pair->token);

                return $ret;
            }
        }
        $params = $this->InitParams();

        $tag_token_list = [];
        foreach ($tagTokenPairs as $pair) {
            array_push($tag_token_list, [$pair->tag, $pair->token]);
        }
        $params['tag_token_list'] = json_encode($tag_token_list);

        return $this->callRestful(self::RESTAPI_BATCHDELTAG, $params);
    }

    public function QueryInfoOfToken($deviceToken)
    {
        $ret = ['ret_code' => -1];
        if (!is_string($deviceToken)) {
            $ret['err_msg'] = 'deviceToken is not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['device_token'] = $deviceToken;
        $params['timestamp'] = time();

        return $this->callRestful(self::RESTAPI_QUERYINFOOFTOKEN, $params);
    }

    public function QueryTokensOfAccount($account)
    {
        $ret = ['ret_code' => -1];
        if (!is_string($account)) {
            $ret['err_msg'] = 'account is not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['account'] = $account;
        $params['timestamp'] = time();

        return $this->callRestful(self::RESTAPI_QUERYTOKENSOFACCOUNT, $params);
    }

    public function DeleteTokenOfAccount($account, $deviceToken)
    {
        $ret = ['ret_code' => -1];
        if (!is_string($account) || !is_string($deviceToken)) {
            $ret['err_msg'] = 'account or deviceToken is not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['account'] = $account;
        $params['device_token'] = $deviceToken;
        $params['timestamp'] = time();

        return $this->callRestful(self::RESTAPI_DELETETOKENOFACCOUNT, $params);
    }

    public function DeleteAllTokensOfAccount($account)
    {
        $ret = ['ret_code' => -1];
        if (!is_string($account)) {
            $ret['err_msg'] = 'account is not valid';

            return $ret;
        }
        $params = [];
        $params['access_id'] = $this->accessId;
        $params['account'] = $account;
        $params['timestamp'] = time();

        return $this->callRestful(self::RESTAPI_DELETEALLTOKENSOFACCOUNT, $params);
    }
}
