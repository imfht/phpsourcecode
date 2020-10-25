<?php
namespace app\api\service;

use WxSDK\core\common\IApp;
use WxSDK\core\common\IReply;
use WxSDK\core\model\ReplyMsg;
use WxSDK\core\model\news\NewItem;
use app\common\model\WxApp;
use app\common\model\WxWx;
use app\file\model\WxFile;
use app\file\service\UpService;
use app\msgReply\model\WxMsgreply;
use app\msg\model\WxMsg;
use app\news\model\WxNews;
use app\reply\model\WxReply;

class AutoReply implements IReply
{

    private $my_wx;
    private $my_input; // 'my_'前缀是为了避免命名重复
    private $my_output; // 'my_'前缀是为了避免命名重复
    /**
     * @var \WXBizMsgCrypt 
     */
    private $my_pc; // 加解密类的实例，在获取微信发送数据时，判断后，执行实例化
    private $responseMsg;
    public function __construct() {
        // $user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
        // $this->my_output['text'] = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
        if (! $this->checkFrom ()) { // 判断来源
            echo '来源非法';
            exit ();
        }
    }
    /**
     * 流程控制
     * 基本流程设计：
     * 1、验证来源（初检，构造函数里完成，对URL验证）
     * 2、获取微信公众号设置
     * 3、验证来源（密码验证）
     * 3、解析微信发来的信息（除明文外涉及加密/解密，建议开启加密）
     * 4、对获取信息分类处理
     * 5、获取对应处理的数据
     * 6、返回处理结果
     * 7、记录处理结果
     */
    public function run() {
        /*
         * 获得指定微信信息,
         * 如果未找到相关信息，则中断后续操作
         */
        $this->getWx ();
        // 验证来源（密码验证，判断是否为微信官方发来的信息，与token相关）
        $this->valid ();
        $this->getInputContent (); // 获得微信传递的内容，赋值到$this->my_input
        if ($this->my_input) {
            // 开始分类处理
            $this->getReplyMsg(NULL, NULL);
            
            echo $this->responseMsg; // 最终的回复内容，此处为正常预料之内回复的值！
            $this->afterReply(NULL, NULL);
            exit ();
        } else {
            echo '';
            exit ();
        }
    }
    private function checkFrom() {
        if (isset ( $_GET ['wx'] )) {
            // 第一步，验证来源
            if ($_GET ['wx'] > 0) { // 对URL做一个基本判断
                return true; // 进入下一步
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * wayFromText
     * 消息为文本时的分类操作
     * <br>主体内容是获取回复内容
     * <br>赋值给$this->my_output
     */
    private function wayFromText() {
        if ($this->my_input) {
            $this->my_output = '';
        }
        $keyword = trim ( $this->my_input ['Content'] );
        
        if ((! empty ( $keyword ) || $keyword === 0) && isset ( $this->my_wx )) { // 判断，关键词消息
            $res = $this->findReplyByKeyword ( $keyword );
            if ($res ['errCode']) {
                $this->my_output = '';
            } else {
                $this->my_output = $res ['data'];
            }
        } else {
            $this->my_output = '';
        }
    }
    
    /**
     * wayFromEventKey
     * 消息是自定义菜单key值时的分类操作
     */
    private function wayFromEventKey() {
        if ($this->my_input) {
            $this->my_output = '';
        }
        $keyword = trim ( $this->my_input ['EventKey'] );
        
        if ((! empty ( $keyword ) || $keyword === 0) && isset ( $this->my_wx )) { // 判断，关键词消息
            // 查询回复内容
            $WxReply = new WxReply();
            $where = [
                'keyword' => $keyword,
                'aid' => $this->my_wx ['id'],
                'is_ok' => 1,
                'type' => 1,
                'is_menu_key' => 1
            ];
            $r = $WxReply->where ( $where )->limit ( 1 )->find ();
            if (! empty ( $r )) {
                $r = $r->toArray ();
                $r = $this->transReplyContent ( $r );
                $this->my_output = $r;
            } else {
                $r ['msg_type'] = 'text';
                $r ['text'] = '资源已下架！';
                $this->my_output = $r;
            }
        } else { // 关键词为空，这是意外情况，可能性极小
            $this->my_output = '';
        }
    }
    
    /**
     * wayFromSubscribe
     * 关注公众号时的分类操作
     */
    private function wayFromSubscribe() {
        if ($this->my_input) {
            $this->my_output = '';
        }
        // 		$keyword = trim ( $this->my_input ['EventKey'] );
        
        // 		if (isset ( $this->my_wx ) ){ // 判断，关键词消息
        // 查询回复内容
        $WxReply = new WxReply ();
        $where = [
            'aid' => $this->my_wx ['id'],
            'is_ok' => 1,
            'type' => 2
        ];
        $r = $WxReply->where ( $where )->limit ( 1 )->find ();
        if (! empty ( $r )) {
            $r = $r->toArray ();
            $r = $this->transReplyContent ( $r );
            $this->my_output = $r;
        } else {
            $this->my_output = '';
        }
        //} else { // 关键词为空，这是意外情况，可能性极小
        //$this->my_output = '';
        //}
    }
    
    /**
     * findReplyByKeyword
     * 通过关键词检索出回复数据
     *
     * @param string $keyword
     * @return number[]|string[]|mixed[]
     */
    private function findReplyByKeyword($keyword = NULL) {
        $keyword = $keyword === NULL ? $this->my_input ['Content'] : $keyword;
        $WxReply = new WxReply ();
        $where = [
            'keyword' => $keyword,
            'aid' => $this->my_wx ['id'],
            'is_ok' => 1,
            'type' => 1,
            // 				'is_menu_key' => [
                // 						'exp',
                // 						'in(0) or isnull(is_menu_key)'
                // 				]
        ];
        $r = $WxReply->where ( $where )->limit ( 1 )->find ();
        if (! empty ( $r )) {
            $r = $r->toArray ();
            $r = $this->transReplyContent ( $r );
            return [
                'errCode' => 0,
                'errMsg' => '获取信息成功',
                'data' => $r
            ];
        } else {
            // 寻找非全匹配关键词
            $res = $this->getReplyLikeMatch ( $keyword );
            if (empty ( $res )) {
                // 返回无匹配回复
                $res = $this->replyNoMatch ( $this->my_wx ['id'] );
                if (empty ( $res )) {
                    $res = [
                        'errCode' => 0,
                        'errMsg' => '找不到任何自定义的回复，看来只能由程序员代劳了',
                        'data' => ''
                    ];
                } else {
                    $res = [
                        'errCode' => 0,
                        'errMsg' => '获取无匹配回复成功',
                        'data' => $res
                    ];
                }
            } else {
                $res = array (
                    'errCode' => 0,
                    'errMsg' => '获取非完全匹配关键词回复成功',
                    'data' => $res
                );
            }
            
            return $res;
        }
    }
    
    /**
     * transReplyContent
     * 回复数据格式化
     * <br>将非文本内容进行重新赋值，并返回新的数据
     *
     * @param array $r 数据库中的原始数据
     * @return array 结果数组
     */
    private function transReplyContent($r = NULL) {
        $err = [
            'msg_type'=>'text',
            'text'=>'信息已收到'
        ];
        if (empty ( $r ) || $r ['msg_type'] == 'text') {
            // 下一步
        } elseif ($r ['msg_type'] == 'news') {
            if(is_array($r ['news'])){
                $newsIDs = $r ['news'];
            }else{
                $newsIDs = json_decode ( $r ['news']);
            }
            foreach ( $newsIDs as $k => $v ) {
                $news [] = $this->getOneNews ( $v );
            }
            $r ['news'] = $news;
        } elseif ($r ['msg_type'] == 'img') {
            $res = UpService::getImgShortMedia(new WxApp($this->my_wx['id']), $r ['img']);
            if ($res->ok()) {
                $r ['media_id'] = $res->data['media_id'];
            } else {
                $r ['msg_type'] = 'text';
                $r ['text'] = '';
            }
        }elseif ($r['msg_type'] == 'video'){
            $wf = new WxFile();
            $file = $wf->get($r['video']);
            if(!$file){
                return $err;
            }
            $app = new WxApp($this->my_wx['id']);
            $ret = UpService::getVideoLongMedia($app, $file);
            if(!$ret->ok()){
                return $err;
            }
            $r['media_id'] = $ret->data['media_id'];
            $r['title'] = $file['title'];
            $r['description'] = $file['description'];
        }elseif($r['msg_type'] == 'voice'){
            $app = new WxApp($this->my_wx['id']);
            $ret = UpService::getVoiceShortMedia($app, $r['voice']);
            if(!$ret->ok()){
                return $err;
            }
            $r['media_id'] = $ret->data['media_id'];
        }else{
            return [
                'msg_type'=>'text',
                'text'=>''
            ];
        }
        return $r;
    }
    
    private function getOneNews($id){
        $News = new WxNews();
        return $News->get($id)->toArray();
    }
    /**
     * getReplyLikeMatch
     * 通过关键词模糊查找自动回复
     *
     * @param string $k
     */
    public function getReplyLikeMatch($k = NULL) {
        // 一、分词
        // 二、关键词去重复
        // 三、循环查找
        // 四、返回结果
        
        // 分词
        $keywords = $this->cutWord ( $k ); // $keywords为数组
        // 去重复
        $keywords = array_unique ( $keywords );
        
        rsort ( $keywords, SORT_STRING ); // 重排列，以便将匹配度高的关键词前置；
        $reply = array ();
        $WxReply = new WxReply ();
        // 查找
        foreach ( $keywords as $k => $v ) {
            $where = [
                'aid' => $this->my_wx ['id'],
                'keyword' => $v,
                'type' => 1,
                'is_ok' => 1,
                'is_like' => 1,
                // 'is_menu_key'=>0, //扩大搜索范围，则此处不对是否为自定义菜单做限制
            ];
            $res = $WxReply->where ( $where )->order ( 'level desc,create_time desc' )->limit ( 1 )->find ();
            if (! empty ( $res )) {
                $reply = $res->toArray (); // 一旦找到一个合适的，立即退出循环
                break;
            }
        }
        $reply = $this->transReplyContent ( $reply );
        // 返回结果
        return $reply;
    }
    
    /**
     * cutWord
     * 分词
     * @param mixed $k
     * @return array 分割后的关键词数组
     */
    public function cutWord($k = NULL) {
        require_once APP_PATH . '../extend/cutWord/phpanalysis.class.php';
        
        $do_fork = true; // 岐义处理
        $do_unit = true; // 新词识别
        $do_multi = false; // 多元切分
        $do_prop = false; // 词性标注
        $pri_dict = true; // 是否预装载全部词条
        
        // PhpAnalysis::$loadInit = false; // 参考demo--初始化类
        
        $pa = new \PhpAnalysis ( 'utf-8', 'utf-8', $pri_dict );
        // 载入词典
        $pa->LoadDict ();
        
        // 执行分词
        $pa->SetSource ( $k );
        $pa->differMax = $do_multi;
        $pa->unitWord = $do_unit;
        $pa->StartAnalysis ( $do_fork );
        
        $okresult = $pa->GetFinallyResult ( ' ', $do_prop ); // 结果，用空格分割
        $okresult = trim ( $okresult );
        $okresult = explode ( ' ', $okresult );
        return $okresult;
    }
    /**
     * writeInput
     * 记录用户发送的消息
     */
    private function writeInput() {
        $r = $this->transInputType2Table ();
        $data ['aid'] = $this->my_wx ['id'];
        $r = array_merge ( $r, $data );
        $WxMsg = new WxMsg();
        $res = $WxMsg->allowField ( true )->save ( $r );
        return $WxMsg->id;
    }
    /**
     * writeOutput
     * 记录回复的消息
     */
    private function writeOutput($id = 0) {
        if (empty ( $this->my_output )){
            return false;
        }
        $WxMsgreply = new WxMsgreply();
        // 参数设置
        $this->my_output ['my_name'] = $this->my_input ['ToUserName'];
        $this->my_output ['user_name'] = $this->my_input ['FromUserName'];
        $this->my_output ['msg_id'] = $id;
        if (isset ( $this->my_output ['id'] )){
            unset ( $this->my_output ['id'] );
        }
        if($this->my_output['msg_type']=="news"){
            $ids = [];
            foreach ($this->my_output['news'] as $v){
                $ids[] = $v['id'];
            }
            $this->my_output['news'] = json_encode($ids);
        }
        $res = $WxMsgreply->allowField ( true )->save ( $this->my_output );
        // 待开发
    }
    /**
     * replyNoMatch
     * 无匹配的自动回复
     * @param number $aid 公众号id
     * @return mixed[]
     */
    public function replyNoMatch($aid = NULL) {
        if ($aid === NULL) {
            return [
                'error' => 2,
                'message' => '系统没有正确交互内部信息',
                'result' => [
                    'msg_type' => 'text',
                    'text' => '抱歉，系统开小差了，请稍后再试'
                ]
            ];
        }
        $WxReply = new WxReply ();
        $where = [
            'type' => 3,
            'aid' => $this->my_wx ['id'],
            'is_ok' => 1
        ];
        $res = $WxReply->where ( $where )->order ( 'level desc,create_time desc' )->limit ( 1 )->find();
        if (! empty ( $res )) {
            $res = $res->toArray ();
            if (count ( $res ) > 0) {
                $res = $this->transReplyContent ( $res );
            } else {
                $res = ''; // 测试中
            }
        }
        return $res;
    }
    /**
     * transInputType2Table
     * 参数赋值
     * <br>将微信提供的参数转化为本地数据库需要的参数
     *
     * @return mixed[]
     */
    private function transInputType2Table() {
        $r = [];
        $r ['my_name'] = $this->my_input ['ToUserName'];
        $r ['user_name'] = $this->my_input ['FromUserName'];
        $r ['create_time'] = $this->my_input ['CreateTime'];
        $r ['msg_type'] = $this->my_input ['MsgType'];
        $r ['msg_id'] = $this->my_input ['MsgId'];
        $r ['content'] = isset($this->my_input ['Content'])?$this->my_input ['Content']:'';
        $r ['media_id'] = isset ( $this->my_input ['MediaId'] ) ? $this->my_input ['MediaId'] : '';
        $r ['img_url'] = isset ( $this->my_input ['PicUrl'] ) ? $this->my_input ['PicUrl'] : '';
        $r ['format'] = isset ( $this->my_input ['Format'] ) ? $this->my_input ['Format'] : '';
        $r ['thumb_media_id'] = isset ( $this->my_input ['ThumbMediaId'] ) ? $this->my_input ['ThumbMediaId'] : '';
        $r ['location_x'] = isset ( $this->my_input ['Location_X'] ) ? $this->my_input ['Location_X'] : '';
        $r ['location_y'] = isset ( $this->my_input ['Location_Y'] ) ? $this->my_input ['Location_Y'] : '';
        $r ['scale'] = isset ( $this->my_input ['Scale'] ) ? $this->my_input ['Scale'] : '';
        $r ['label'] = isset ( $this->my_input ['Label'] ) ? $this->my_input ['Label'] : '';
        $r ['title'] = isset ( $this->my_input ['Title'] ) ? $this->my_input ['Title'] : '';
        $r ['description'] = isset ( $this->my_input ['Description'] ) ? $this->my_input ['Description'] : '';
        $r ['url'] = isset ( $this->my_input ['Url'] ) ? $this->my_input ['Url'] : '';
        if ($r ['msg_type'] == 'event') {
            $r ['event'] = isset ( $this->my_input ['Event'] ) ? $this->my_input ['Event'] : '';
            $r ['event_key'] = isset ( $this->my_input ['EventKey'] ) ? $this->my_input ['EventKey'] : '';
            $r ['ticket'] = isset ( $this->my_input ['Ticket'] ) ? $this->my_input ['Ticket'] : '';
            $r ['latitude'] = isset ( $this->my_input ['Latitude'] ) ? $this->my_input ['Latitude'] : '';
            $r ['longitude'] = isset ( $this->my_input ['Longitude'] ) ? $this->my_input ['Longitude'] : '';
            $r ['precision'] = isset ( $this->my_input ['Precision'] ) ? $this->my_input ['Precision'] : '';
        }
        if (empty ( $this->my_output )) {
            $r ['is_reply'] = 0;
        } else {
            $r ['is_reply'] = 1;
        }
        if ($r ['msg_type'] == 'image'){
            $r ['msg_type'] = 'img';
        }
        if (isset ( $this->my_output ['type'] ) && $this->my_output ['type'] == 1){
            $r ['is_keyword'] = 1;
        }else{
            $r ['is_keyword'] = 0;
        }
        return $r;
    }
    /**
     * wayFromAnyone
     * 默认回复
     * @access private
     * @goal 对于未知类型和部分多媒体类型消息的默认回复，改善用户体验
     * @param String $text
     */
    private function wayFromAnyone($text = NULL) {
        $this->my_output ['msg_type'] = 'text';
        $text = $text === NULL ? '消息收到，谢谢！' : $text;
        $this->my_output ['text'] = $text;
    }
    /**
     * getWx
     * 获取本地保存的的公众号数据
     * @goal 将公众号数据赋值给$this->my_wx
     */
    private function getWx() {
        $aid = $_GET ['wx'];
        $WxWx = new WxWx();
        $res = $WxWx->get ( $aid ); // 获取微信数据
        
        if (empty ( $res )) {
            echo '';
            exit ();
        }
        $this->my_wx = $res->toArray (); // 转换数据格式并赋值给类私有变量，供其他函数调用
    }
    /**
     * getInputContent
     * 获取微信官方发送的输入内容
     * @goal 将解析后的内容赋值给$this->my_input
     *
     */
    private function getInputContent() {
        // 		$postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];
        //替换为更强大的“方法”获取输入的数据，防止部分服务器中由于php.ini设置不能正常获取的情况
        $postStr = file_get_contents("php://input");
        
        if (empty ( $postStr )) {
            $this->my_input = NULL;
        } else {
            if (isset ( $_GET ['encrypt_type'] ) && ($_GET ['encrypt_type'] == 'aes')) { // 判断是否加密，若加密，则执行解密
                $timestamp = $_GET ["timestamp"];
                $nonce = $_GET ["nonce"];
                $msg_signature = $_GET ["msg_signature"];
                require_once APP_PATH . '../extend/wxKey/wxBizMsgCrypt.php';
                $this->my_pc = new \WXBizMsgCrypt ( $this->my_wx->token, $this->my_wx->encoding_aes_key, $this->my_wx->app_id );
                $errCode = $this->my_pc->decryptMsg ( $msg_signature, $timestamp, $nonce, $postStr, $decryptMsg );
                if ($errCode) {
                    echo $errCode;
                    exit ();
                }
                $postStr = $decryptMsg;
            }
            
            libxml_disable_entity_loader ( true );
            $postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
            $this->my_input = json_encode ( $postObj );
            $this->my_input = json_decode ( $this->my_input, 1 );
        }
    }
    /**
     * valid
     * 双向验证
     * @goal 在微信公众号官方管理后台填入URL和Token时，验证连接。
     */
    private function valid() {
        // 第一次在微信端填写URL和Token时会验证
        if (isset ( $_GET ["echostr"] ) && $_GET ['echostr']) {
            $echoStr = $_GET ["echostr"];
            // valid signature , option
            if ($this->checkSignature ()) {
                echo $echoStr;
                exit ();
            } else {
                echo '验证失败';
                exit ();
            }
        }
    }
    /**
     * writeMassResult
     * 记录群发结果
     *
     */
    private function writeMassResult() {
        $this->load->database ();
        // 数据初始化
        $msgID = $this->my_input ['MsgID']; // 注意这里的ID字母均大写
        $r ['msg_status'] = $this->my_input ['Status'];
        $r ['total_count'] = $this->my_input ['TotalCount'];
        $r ['filter_count'] = $this->my_input ['FilterCount'];
        $r ['sent_count'] = $this->my_input ['SentCount'];
        $r ['error_count'] = $this->my_input ['ErrorCount'];
        $a = $this->db->list_fields ( 'wx_mass' );
        $a = array_flip ( $a );
        $r = array_intersect_key ( $r, $a );
        $this->db->where ( 'msg_id', $msgID )->update ( 'wx_mass', $r );
    }
    /**
     * responseMsg
     * 获取本地设置的自动回复数据
     * @return string 可用于回复的xml格式字符串；尚未加密
     */
    private function responseMsg() {
        if (empty ( $this->my_output ) || $this->my_input === NULL) {
            return '';
        } else {
            $out = $this->my_output;
            $in = $this->my_input;
            if ($this->my_output ['msg_type'] == 'text') {
                if ($out['text']) {
                    $time = time ();
                    $msg = ReplyMsg::getTextMsg($this->my_input ['FromUserName'], $this->my_input ['ToUserName'] , $this->my_output ['text'], $time);
                } else {
                    $msg = '';
                }
            } elseif ($out['msg_type'] == 'img') {
                $msg = ReplyMsg::getImageMsg($in ['FromUserName'], $in ['ToUserName']
                    , $out ['media_id'], time ());
            } elseif ($out ['msg_type'] == 'voice') {
                $msg = ReplyMsg::getVoiceMsg($in ['FromUserName'], $in ['ToUserName']
                    , $out['media_id'], time());
            } elseif ($out ['msg_type'] == 'video') {
                $msg = ReplyMsg::getVideoMsg($in ['FromUserName'], $in ['ToUserName'] 
                    , $out['media_id'], $out['title'], $out['description']);
            } elseif ($out ['msg_type'] == 'news') {
                $msg = $this->getNewsReplyMsg ();
            } else {
                $msg = $this->getDefaultReplyMsg ();
            }
            return $msg; // 不直接对$this->my_output赋值，是为了使后续步骤中有可能利用该值，而$msg是String形式，不便使用
        }
    }

    /**
     * getNewsReplyMsg
     * 拼装图文类型的回复
     * @return string 可回复的xml格式字符串；未加密
     */
    private function getNewsReplyMsg() {
        $url= 'http://' . $_SERVER ['HTTP_HOST'] . "/e/extend/".$this->getRootDir()."/public/view.php?type=news&id=";
        $news = [];
        foreach ( $this->my_output ['news'] as $k => $v ) {
            if ($v["is_open_outside"] == 1) {
                $tempUrl = $v['outside_url'];
            }else{
                $tempUrl = $url . $v ['id'];
            }
            $new = new NewItem();
            $new->title = $v['title'];
            $new->description = $v['abstract'];
            $new->url = $tempUrl;
            $new->picUrl = 'http://' . $_SERVER ['HTTP_HOST'] . $v ['title_img'];
            $news[] = $new;
        }
        return ReplyMsg::getNewsMsg($this->my_input ['FromUserName'], $this->my_input ['ToUserName'], time(), ...$news);
    }
    public function getRootDir(){
        if(PHP_OS === 'Linux'){
            $arr = explode('/',rtrim(ROOT_PATH,'/'));
        }else{
            $arr = explode('\\',rtrim(ROOT_PATH,'\\'));
        }
        return $arr[count($arr)-1];
    }
    /**
     * getDefaultReplyMsg
     * 拼装文本类型的回复
     * @param string $word 回复给用户的文字内容
     * @return string 可回复的xml格式字符串；未加密
     */
    private function getDefaultReplyMsg($word = '谢谢关注！') {
        $this->my_output ['text'] = $word;
        $time = time ();
        return ReplyMsg::getTextMsg($this->my_input ['FromUserName'], $this->my_input ['ToUserName'] , $this->my_output ['text'], $time);
    }
    /**
     * checkSignature
     * 验证令牌
     * @throws \Exception 异常：未设置本地令牌常量
     * @return boolean 验证结果，通过则返回真
     */
    private function checkSignature() {
        // you must define TOKEN by yourself
        if (! isset ( $this->my_wx ['token'] )) {
            throw new \Exception ( 'TOKEN is not defined!' );
        }
        
        $signature = $_GET ["signature"];
        $timestamp = $_GET ["timestamp"];
        $nonce = $_GET ["nonce"];
        $token = $this->my_wx ['token'];
        $tmpArr = array (
            $token,
            $timestamp,
            $nonce
        );
        // use SORT_STRING rule
        sort ( $tmpArr, SORT_STRING );
        $tmpStr = implode ( $tmpArr );
        $tmpStr = sha1 ( $tmpStr );
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
    public function getReplyMsg(\WxSDK\core\model\WxMsg $wxMsg = NULL, IApp $app = NULL)
    {
        if (strtolower ( $this->my_input ['MsgType'] ) == 'event') {
            if ($this->my_input ['Event'] == 'MASSSENDJOBFINISH') { // 微信群发的结果通知
                $this->writeMassResult (); // 记录;
                exit ();
            } elseif ($this->my_input ['Event'] == 'CLICK') { // 自定义菜单点击事件
                if (isset ( $this->my_input ['EventKey'] ) && ! empty ( $this->my_input ['EventKey'] )) {
                    $this->wayFromEventKey ();
                }
            } elseif ($this->my_input ['Event'] == 'scancode_waitmsg') {
                /*
                 * Event=>scancode_waitmsg
                 * EventKey=> 自己定义的
                 * ScanCodeInfo=> 这是一个数组（或许也有可能不是数组，待验证）
                 */
                $this->wayFromAnyone ();
            } elseif($this->my_input['Event']=='subscribe'){
                $this->wayFromSubscribe();
            }else { // 其他类型暂不回复
                echo '';
                exit ();
            }
        } elseif ($this->my_input ['MsgType'] == 'text') {
            $this->wayFromText ();
        } elseif ($this->my_input ['MsgType'] == 'image') {
            $this->wayFromAnyone (); // 待开发，暂时以默认方式处理
        } elseif ($this->my_input ['MsgType'] == 'voice') {
            $this->wayFromAnyone (); // 待开发，暂时以默认方式处理
        } elseif ($this->my_input ['MsgType'] == 'video') {
            $this->wayFromAnyone (); // 待开发，暂时以默认方式处理
        } elseif ($this->my_input ['MsgType'] == 'shortvideo') {
            $this->wayFromAnyone (); // 待开发，暂时以默认方式处理
        } elseif ($this->my_input ['MsgType'] == 'location') {
            $this->wayFromAnyone (); // 待开发，暂时以默认方式处理
        } elseif ($this->my_input ['MsgType'] == 'link') {
            $this->wayFromAnyone (); // 待开发，暂时以默认方式处理
        }
        
        /*
         * 分类处理完毕，下面获取回复信息
         * 注意：有可能在分类处理过程中就终止了，而无需接下来的步骤
         * 当然，建议除了特殊的消息类型（如用户点击输入框等）都给予用户相应的回复
         */
        $r = $this->responseMsg (); // 转化为直接回复的字符串，此时尚未加密
        $reply = new ReplyMsg();
        $reply->msg = $r;
        // 判断‘way_of_key’是加强安全性,这是本地可以设置的强制加密参数，防止第三方窃取消息
        if (! empty ( $r )) {
            if ((isset ( $_GET ['encrypt_type'] ) && $_GET ['encrypt_type'] == 'aes') || $this->my_wx ['way_of_key'] == 1) {
                $errCode = $this->my_pc->encryptMsg ( $r, $_GET ['timestamp'], $_GET ["nonce"], $encryptMsg );
                $r = $encryptMsg; // 获取加密字符串
            }
        }
        $this->responseMsg = $r;
    }
    
    public function afterReply(\WxSDK\core\model\WxMsg $wxMsg = NULL, ReplyMsg $replyMsg = NULL)
    {
        $id = $this->writeInput (); // 将用户发送内容写入数据库
        $this->writeOutput ( $id ); // 将发送给用户的内容记录到数据库中
    }
    
}

