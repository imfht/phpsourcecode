<?php
/**
 * 营销渠道平台基础类
 */

namespace Core\Platform;

use Core\Model\Account;
use Think\Model;

abstract class Platform {

    /**
     * 回复文字消息
     */
    const POCKET_TEXT = 'text';
    /**
     * 回复图文消息
     * 消息定义为元素集合, 每个元素结构定义为
     * title        - string: 新闻标题,
     * description  - string: 新闻描述,
     * picurl       - string: 图片链接,
     * url          - string: 原文链接
     */
    const POCKET_NEWS = 'news';


    /**
     * &nbsp;&nbsp;&nbsp;通用类型: text, image, voice, video, location, link,
     * &nbsp;&nbsp;&nbsp;扩展类型: subscribe, unsubscribe, qr, trace, menu_click, menu_view, menu_scan, menu_scan_waiting, menu_photo, menu_photo_album, menu_album, menu_location, enter
     * 类型说明:
     * &nbsp;&nbsp;&nbsp;通用类型: 文本消息, 图片消息, 音频消息, 视频消息, 位置消息, 链接消息,
     * &nbsp;&nbsp;&nbsp;扩展类型: 开始关注, 取消关注, 扫描二维码, 追踪位置, 点击菜单(模拟关键字), 点击菜单(链接), 进入聊天窗口
     */

    /**
     * 粉丝发送文字消息
     */
    const MSG_TEXT = 'text';
    /**
     * 粉丝发送图片消息
     */
    const MSG_IMAGE = 'image';
    /**
     * 粉丝关注
     */
    const MSG_SUBSCRIBE = 'subscribe';
    /**
     * 粉丝取消关注
     */
    const MSG_UNSUBSCRIBE = 'unsubscribe';
    /**
     * 粉丝进入对话
     */
    const MSG_ENTER = 'enter';
    /**
     * 粉丝点击菜单
     */
    const MSG_MENU_CLICK = 'menu_click';

    /**
     * 创建平台特定的公众号操作对象
     * @param int $id 公众号编号
     * @return Platform|null
     */
    public static function create($id) {
        $p = new Account();
        $platform = $p->getAccount($id);
        if(!empty($platform)) {
            if($platform['type'] == Account::ACCOUNT_ALIPAY) {
                return new Alipay($platform);
            }
            if($platform['type'] == Account::ACCOUNT_WEIXIN) {
                return new WeiXin($platform);
            }
        }
        return null;
    }

    /**
     * 特定公众号平台的操作对象构造方法
     *
     * @param array $platform 公号平台基础对象
     */
    abstract public function __construct($platform);

    /**
     * 获取当前平台模型
     *
*@return \Core\Model\Account
     */
    public function getAccount() {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 对来自平台的请求进行安全校验
     * @retun boolean
     */
    public function checkSign() {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 接入平台时的校验操作
     * @retun boolean
     */
    public function touchCheck() {
        $account = $this->getAccount();
        if(!empty($account)) {
            $rec = array();
            $rec['isconnect'] = 1;
            $m = new Model();
            $m->table('__PLATFORMS__')->data($rec)->where("`id`='{$account['id']}'")->save();
        }
    }

    /**
     * 登记当前消息中的用户资料, 在派生类中实现时, 应至少传递 openid, unionid, subscribe, subscribetime, unsubscribetime, tag
     * @param $message
     * @return boolean
     */
    public function booking($message) {
        $account = $this->getAccount();
        if(!empty($account) && !empty($message)) {
            $fan = coll_elements(array('openid', 'unionid', 'subscribe', 'subscribetime', 'unsubscribetime', 'tag'), $message);
            $fan['platformid'] = $account['id'];
            $condition = '`platformid`=:platformid AND `openid`=:openid';
            $pars = array();
            $pars[':platformid'] = $fan['platformid'];
            $pars[':openid'] = $fan['openid'];
            $m = new Model();
            $fanid = $m->table('__MMB_MAPPING_FANS__')->where($condition)->bind($pars)->getField('`fanid`');
            if(empty($fanid)) {
                $fan['uid'] = 0;
                //判断用户中心策略
                $fan['salt'] = util_random(8);
                if(empty($fan['subscribetime'])) {
                    $fan['subscribetime'] = TIMESTAMP;
                }
                $m->table('__MMB_MAPPING_FANS__')->data($fan)->add();
            } else {
                if(empty($fan['subscribetime'])) {
                    unset($fan['subscribetime']);
                }
                if(empty($fan['unsubscribetime'])) {
                    unset($fan['unsubscribetime']);
                }
                $m->table('__MMB_MAPPING_FANS__')->data($fan)->where("`fanid`='{$fanid}'")->save();
            }
        }
    }

    /**
     * 查询当前平台支持的消息类型, 当前支持的类型包括:
     * &nbsp;&nbsp;&nbsp;通用类型: text, image, voice, video, location, link,
     * &nbsp;&nbsp;&nbsp;扩展类型: subscribe, unsubscribe, qr, trace, menu_click, menu_view, menu_scan, menu_scan_waiting, menu_photo, menu_photo_album, menu_album, menu_location, enter
     * 类型说明:
     * &nbsp;&nbsp;&nbsp;通用类型: 文本消息, 图片消息, 音频消息, 视频消息, 位置消息, 链接消息,
     * &nbsp;&nbsp;&nbsp;扩展类型: 开始关注, 取消关注, 扫描二维码, 追踪位置, 点击菜单(模拟关键字), 点击菜单(链接), 进入聊天窗口
     *
     * @return array 当前公号支持的消息类型集合
     */
    public function queryAvailableMessages($type = '') {
        return array();
    }

    /**
     * 查询当前公号支持的响应类型
     *
     * 当前支持的类型包括:<br/>
     * &nbsp;&nbsp;&nbsp; text, image, voice, video, music, news, link, card
     *
     * @return array 当前公号支持的响应结构集合
     */
    public function queryAvailablePackets($type = '') {
        return array();
    }

    /**
     * 分析消息内容,并返回消息结构, 参数为平台消息结构
     * @param array $message 平台消息结构
     * @return array 统一消息结构
     */
    public function parse($message) {
        $packet = array();
        if (!empty($message)){
            $obj = simplexml_load_string($message, 'SimpleXMLElement', LIBXML_NOCDATA);
            if($obj instanceof SimpleXMLElement) {
                $packet['from'] = strval($obj->FromUserName);
                $packet['to'] = strval($obj->ToUserName);
                $packet['time'] = strval($obj->CreateTime);
                $packet['type'] = strval($obj->MsgType);
                $packet['event'] = strval($obj->Event);

                foreach ($obj as $variable => $property) {
                    $packet[strtolower($variable)] = (string)$property;
                }

                if($packet['type'] == 'text') {
                    $packet['content'] = strval($obj->Content);
                    $packet['redirection'] = false;
                    $packet['source'] = null;
                }
                if($packet['type'] == 'image') {
                    $packet['url'] = strval($obj->PicUrl);
                }
                if($packet['type'] == 'voice') {
                    $packet['media'] = strval($obj->MediaId);
                    $packet['format'] = strval($obj->Format);
                }
                if($packet['type'] == 'video') {
                    $packet['media'] = strval($obj->MediaId);
                    $packet['thumb'] = strval($obj->ThumbMediaId);
                }
                if($packet['type'] == 'location') {
                    $packet['location_x'] = strval($obj->Location_X);
                    $packet['location_y'] = strval($obj->Location_Y);
                    $packet['scale'] = strval($obj->Scale);
                    $packet['label'] = strval($obj->Label);
                }
                if($packet['type'] == 'link') {
                    $packet['title'] = strval($obj->Title);
                    $packet['description'] = strval($obj->Description);
                    $packet['url'] = strval($obj->Url);
                }

                //处理其他事件类型
                if($packet['type'] == 'event') {
                    $packet['type'] = $packet['event'];
                }
                if($packet['type'] == 'subscribe') {
                    //开始关注
                    $scene = strval($obj->EventKey);
                    if(!empty($scene)) {
                        $packet['scene'] = str_replace('qrscene_', '', $scene);
                        $packet['ticket'] = strval($obj->Ticket);
                    }
                }
                if($packet['type'] == 'unsubscribe') {
                    //取消关注
                }
                if($packet['type'] == 'SCAN') {
                    //扫描二维码
                    $packet['type'] = 'qr';
                    $packet['scene'] = strval($obj->EventKey);
                    $packet['ticket'] = strval($obj->Ticket);
                }
                if($packet['type'] == 'LOCATION') {
                    //追踪地理位置
                    $packet['type'] = 'trace';
                    $packet['location_x'] = strval($obj->Latitude);
                    $packet['location_y'] = strval($obj->Longitude);
                    $packet['precision'] = strval($obj->Precision);
                }
                if($packet['type'] == 'CLICK') {
                    $packet['type'] = 'click';
                    $packet['content'] = strval($obj->EventKey);
                }
                if($packet['type'] == 'VIEW') {
                    $packet['type'] = 'view';
                    $packet['url'] = strval($obj->EventKey);
                }
                if($packet['type'] == 'ENTER') {
                    //进入聊天窗口
                    $packet['type'] = 'enter';
                }
            }
        }
        return $packet;
    }

    /**
     * 响应消息内容, 参数为统一响应结构
     * @param array $packet 统一响应结构, 见文档 todo
     * @return string 平台特定的消息响应内容
     */
    public function response($packet) {
        if (!is_array($packet)) {
            return $packet;
        }
        if(empty($packet['CreateTime'])) {
            $packet['CreateTime'] = TIMESTAMP;
        }
        if(empty($packet['MsgType'])) {
            $packet['MsgType'] = 'text';
        }
        if(empty($packet['FuncFlag'])) {
            $packet['FuncFlag'] = 0;
        } else {
            $packet['FuncFlag'] = 1;
        }
        return array2xml($packet);
    }

    /**
     * 获取当前公号是否支持消息推送
     * @return bool 是否支持
     */
    public function isPushSupported() {
        return false;
    }

    /*
     * 向指定的用户推送消息
     * @param string $uniid 指定用户(统一用户) todo
     * @param array $packet 统一响应结构
     * @return bool 是否成功
     */
    public function push($uid, $packet) {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 获取当前公号是否支持群发消息
     * @return boolean 是否支持
     */
    public function isBroadcastSupported() {
        return false;
    }

    /**
     * 向一组用户发送群发消息, 可选的可以指定是否要指定特定组
     * @param array $packet 统一消息结构
     * @param array $targets 单独向一组用户群发, 或指定fans列表发送
     */
    public function broadcast($packet, $targets = array()) {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 查询当前公号是否支持菜单操作
     * @return bool 是否支持
     */
    public function isMenuSupported() {
        return false;
    }

    /**
     * 为当前公众号创建菜单
     * @param array $menu 统一菜单结构 todo
     * @return bool 是否创建成功
     */
    public function menuCreate($menu) {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 删除当前公众号的菜单
     * @return bool 是否删除成功
     */
    public function menuDelete() {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 修改当前公众号的菜单
     * @param array $menu 统一菜单结构
     * @return bool 是否修改成功
     */
    public function menuModify($menu) {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 查询菜单
     * @return array 统一菜单结构
     */
    public function menuQuery() {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 查询当前公号粉丝管理的支持程度
     * @return array 返回结果为支持的方法列表(fansGroupAll, fansGroupCreate, ...)
     */
    public function queryFansActions() {
        return array();
    }

    /**
     * 查询当前公号记录的分组信息
     * @return array 统一分组结构集合
     */
    public function fansGroupAll() {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 在当前公号记录中创建一条分组信息
     * @param array $group 统一分组结构 todo
     * @return bool 是否执行成功
     */
    public function fansGroupCreate($group) {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 在当前公号记录中修改一条分组信息
     * @param array $group 统一分组结构
     * @return bool 是否执行成功
     */
    public function fansGroupModify($group) {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 将指定用户移至另一分组中
     * @param string $uniid 指定用户(统一用户)
     * @param array $group 统一分组结构
     * @return bool 是否执行成功
     */
    public function fansMoveGroup($uniid, $group) {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 查询指定的用户所在的分组
     * @param string $uniid 指定用户(统一用户)
     * @return array $group 统一分组结构
     */
    public function fansQueryGroup($uniid) {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 查询指定的用户的基本信息
     * @param string $uniid 指定用户(统一用户)
     * @param boolean $isPlatform 指定的参数是否为平台编号
     * @return array 统一粉丝信息结构 todo
     */
    public function fansQueryInfo($uniid, $isPlatform) {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 查询当前公号的所有粉丝
     * @return array 统一粉丝信息结构集合
     */
    public function fansAll() {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 查询当前公号地理位置追踪的支持情况
     * @return array 返回结果为支持的方法列表(traceCurrent, traceHistory)
     */
    public function queryTraceActions() {
        return array();
    }

    /**
     * 追踪指定的用户的当前位置
     * @param string $uniid 指定用户(统一用户)
     * @return array 地理位置信息
     */
    public function traceCurrent($uniid) {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 追踪指定的用户的地理位置
     * @param string $uniid 指定用户(统一用户)
     * @param int $time 追踪的时间范围
     * @return array 地理位置信息追踪集合
     */
    public function traceHistory($uniid, $time) {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 查询当前公号二维码支持情况
     * @return array 返回结果为支持的方法列表(barCodeCreateDisposable, barCodeCreateFixed)
     */
    public function queryBarCodeActions() {
        return array();
    }

    /**
     * 生成临时的二维码
     *
     */
    public function barCodeCreateDisposable($barcode) {
        trigger_error('not supported.', E_USER_WARNING);
    }

    /**
     * 生成永久的二维码
     */
    public function barCodeCreateFixed($barcode) {
        trigger_error('not supported.', E_USER_WARNING);
    }
    
    public function createShareData() {
        trigger_error('not supported.', E_USER_WARNING);
    }
}
