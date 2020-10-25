<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/29 Time: 16:43
 */

namespace WeChat\Core;


/**
 * Class Authorize  微信授权认证类
 * @package WeChat\Core
 */
abstract class Authorize extends Base implements \WeChat\Extend\Authorize
{

    /**
     * @var string $token 设置微信的认证字符
     */
    protected $token = 'TOKEN2018';

    /**
     * @var string $appID 公众号appid
     */
    protected $appid = '';

    /**
     * @var string $appScret 公众号appSecret
     */
    protected $appSecret = '';

    /**
     * @var array $config 微信的数据集合
     */
    protected $config = [];

    /**
     * @var array $userInfo 微信的数据集合
     */
    protected $userInfo = [];

    /**
     * @var array $returnData   回复用户的消息数据
     */
    protected $returnData = array(
        'MsgType' => 'text',  // 可选类型[text: 文本|image: 图片|voice: 语音|video: 视频|music: 音乐|news: 图文]
        'Title' => '',  // 标题
        'Content' => '',    // 	回复的消息内容（换行：在content中能够换行，微信客户端就支持换行显示）
        'PicUrl' => '',     // 图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
        'Url' => '',    // 点击图文消息跳转链接
        'MediaId' => '',    // 	通过素材管理中的接口上传多媒体文件，得到的id。
        'Description' => '',    // 	视频消息的描述
        'MusicURL' => '',   // 音乐链接
        'HQMusicUrl' => '',     // 	高质量音乐链接，WIFI环境优先使用该链接播放音乐
        'ThumbMediaId' => '',   // 缩略图的媒体id，通过素材管理中的接口上传多媒体文件，得到的id
        'ArticleCount' => '',   // 图文消息个数；当用户发送文本、图片、视频、图文、地理位置这五种消息时，开发者只能回复1条图文消息；其余场景最多可回复8条图文消息
        'Articles' => '',   // 图文消息信息，注意，如果图文数超过限制，则将只发限制内的条数
    );

    /**
     * 设置与微信对接的TOKEN凭证字符
     * Authorize constructor.
     * @param string $token 微信开发模式TOKEN字符串
     * @param string $appID 微信appid
     * @param string $appScret 微信appScret
     * @inheritdoc 详细文档：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=1833550478&lang=zh_CN
     */
    public function __construct(string $token,string $appID,string $appScret)
    {
        // 这里填写的是你在微信上设置的TOKEN，但是必须保证与微信公众平台-接口配置信息一致
        if (!empty($token)) $this->token = $token;
        if (!empty($appID)) $this->appid = $appID;
        if (!empty($appScret)) $this->appSecret = $appScret;
    }

    /**
     * 微信授权
     */
    final public function index()
    {
        // 验证数据或回复用户
        (!isset($_REQUEST['echostr'])) ? $this->responseMsg() : $this->valid();
    }

    /**
     * 若确认此次GET请求来自微信服务器，请原样返回echostr参数内容，则接入生效，否则接入失败。
     */
    final protected function valid()
    {
        $echoStr = $_REQUEST['echostr'];
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    /**
     * 开发者通过检验signature对请求进行校验
     * @return bool
     */
    final protected function checkSignature()
    {
        $tmpArr = array($this->token, $_REQUEST['timestamp'], $_REQUEST['nonce']);
        sort($tmpArr);
        $tmpStr = sha1(implode($tmpArr));
        return ($tmpStr == $_REQUEST['signature']) ? true: false;
    }

    /**
     * 公众号的消息推送，回复
     */
    final protected function responseMsg()
    {
        try{
            $postStr = file_get_contents("php://input");
            if (!empty($postStr)) {
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

                // 微信提醒数组
                $this->config = json_decode(json_encode($postObj), true);

                // 普通授权token
                $resToken = Token::gain($this->appid, $this->appSecret);

                $this->userInfo  = [];
                if (isset($resToken['access_token'])){
                    // 微信用户信息
                    $this->userInfo = User::newUserInfo($resToken['access_token'], $this->config['FromUserName']);
                }

                // 逻辑操作，需要更改逻辑的就在这个方法咯~
                $this->handle();

                // 被动发送消息
                Send::trigger($this->config,$this->returnData);
            }
        }catch (\Exception $exception){
            $this->text($exception->getMessage());
        }
        echo '';
        exit;
    }

    /**
     * 首次关注事件
     * @return mixed|void
     */
    public function follow()
    {
        // TODO: Implement follow() method.
        $sendMsg = '您好，感谢您关注,爱你么么哒~';
        $this->text($sendMsg);
    }

    /**
     * 扫码关注事件
     * @return mixed|void
     */
    public function scanFollow()
    {
        // TODO: Implement scanFollow() method.
        $this->text('扫码关注' . json_encode($this->config));
    }

    /**
     * 点击事件
     * @return mixed|void
     */
    public function click()
    {
        // TODO: Implement click() method.
        $this->text('这个是用户点击事件~'. json_encode($this->config));
    }

    /**
     * 扫码商品事件
     * @return mixed|void
     */
    public function scanProduct()
    {
        // TODO: Implement scanProduct() method.
        $this->text('用户商品扫码' . json_encode($this->config));
    }

    /**
     * 扫码事件
     * @return mixed|void
     */
    public function scan()
    {
        // TODO: Implement scan() method.
        $this->text('扫码进入' . json_encode($this->config));
    }

    /**
     * 用户输入
     * @return mixed|void
     */
    public function input()
    {
        // TODO: Implement input() method.
        $this->text('用户输入' . json_encode($this->config));
    }


    /**
     * 用户操作方法
     * @param \WeChat\Core\Authorize->returnData 返回数据数组
     * @param \WeChat\Core\Authorize->config 微信数据包
     * @return mixed
     */
    final public function handle()
    {
        // TODO: Implement handle() method.
        switch ($this->config['MsgType']){
            case $this->config['MsgType'] =='text':
                $this->input();
                break;
            case $this->config['Event'] == 'subscribe' :
                $params = explode('_', trim($this->config['EventKey'])); // 扫码参数
                !isset($params[1]) ?
                    $this->follow() : // 搜索公众号或推荐公众号关注
                    $this->scanFollow(); // 扫码关注
                break;
            case $this->config['Event'] == 'user_scan_product_enter_session': // 用户商品扫码
                $this->scanProduct();
                break;
            case $this->config['Event'] == 'CLICK': // 用户点击事件
                $this->click();
                break;
            case $this->config['Event'] == 'SCAN': // 扫码进入
                $this->scan();
                break;
        }
    }

    /**
     * 发送文本消息
     * @param string $content 回复的文本内容
     */
    final protected function text(string $content = '这是个友好的回复~')
    {
        $this->returnData['MsgType'] = __FUNCTION__;
        $this->returnData['Content'] = $content;
    }

    /**
     * 发送图片消息
     * @param string $mediaId 素材ID
     */
    final protected function image(string $mediaId)
    {
        $this->returnData['MsgType'] = __FUNCTION__;
        $this->returnData['MediaId'] = $mediaId;
    }

    /**
     * 发送语音消息
     * @param string $mediaId 素材ID
     */
    final protected function voice(string $mediaId)
    {
        $this->returnData['MsgType'] = __FUNCTION__;
        $this->returnData['MediaId'] = $mediaId;
    }

    /**
     * 发送视频消息
     * @param string $mediaId 素材ID
     * @param string $title 视频标题
     * @param string $description   视频消息的描述
     */
    final protected function video(string $mediaId,string $title = '这是一个标题',string $description = '消息的描述')
    {
        $this->returnData['MsgType'] = __FUNCTION__;
        $this->returnData['MediaId'] = $mediaId;
        $this->returnData['Title'] = $title;
        $this->returnData['Description'] = $description;
    }

    /**
     * 发送音乐消息
     * @param string $title 消息标题
     * @param string $description   描述
     * @param string $musicURL  音乐链接
     * @param string $HQMusicUrl    高清音乐URL
     * @param string $ThumbMediaId  缩略图的媒体id，通过素材管理中的接口上传多媒体文件，得到的id
     */
    final protected function music(string $title = '这是一个标题',string $description = '消息的描述',
                             string $musicURL = '', string $HQMusicUrl = '', string $ThumbMediaId = '')
    {
        $this->returnData['MsgType'] = __FUNCTION__;
        $this->returnData['Title'] = $title;
        $this->returnData['Description'] = $description;
        $this->returnData['MusicURL'] = $musicURL;
        $this->returnData['HQMusicUrl'] = $HQMusicUrl;
        $this->returnData['ThumbMediaId'] = $ThumbMediaId;
    }


    /**
     * 发送图文消息
     * @param array $Articles 图文数组
     * @format 格式 $Articles = array(
                                    array(
                                       'Title'=>'标题',
                                      'Description'=>'注释',
                                      'PicUrl'=>'图片地主（含域名的全路径:图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200）',
                                      'Url'=>'点击图文消息跳转链接'
                                    ),
                                );
     */
    final protected function news(array $Articles = [])
    {
        if (!isset($Articles[0]['Title'])) {
            echo '';
            die;
        }
        $this->returnData['MsgType'] = __FUNCTION__;
        $this->returnData['ArticleCount'] = count($Articles);
        $this->returnData['Articles'] = $Articles;
    }



}