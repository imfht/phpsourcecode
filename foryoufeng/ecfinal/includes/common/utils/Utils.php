<?php
/**
 * 用于封装一些常用的方法
 * Created by PhpStorm.
 * User: root
 * Date: 6/12/16
 * Time: 6:59 PM
 */
include_once 'Cache.php';//文件缓存
include_once 'Email.php';//邮件发送
include_once 'Vcode.php';//图形验证码
trait Utils
{
    /**
     * 缓存管理
     * @param $name　缓存名称，如果为数组表示进行缓存设置
     * @param string $value　缓存值　为''的时候表示获取$name的值，为null的时候表示删除$name文件　否则就是设置值
     * @param null $options　设置缓存的参数
     * @return mixed|string 成功返回true 失败返回false
     */
    public function static_cache($name,$value='',$options=null){
        static $cache   =   '';
        if(is_array($options)){
            // 缓存操作的同时初始化
            $type       =   isset($options['type'])?$options['type']:'';
            $cache      =   Cache::getInstance($type,$options);
        }elseif(is_array($name)) { // 缓存初始化
            $type       =   isset($name['type'])?$name['type']:'';
            $cache      =   Cache::getInstance($type,$options);
            return $cache;
        }elseif(empty($cache)) { // 自动初始化
            $cache      =   Cache::getInstance();
        }
        if(''=== $value){ // 获取缓存
            return $cache->get($name);
        }elseif(is_null($value)) { // 删除缓存
            return $cache->rm($name);
        }else { // 缓存数据
            if(is_array($options)) {
                $expire     =   isset($options['expire'])?$options['expire']:NULL;
            }else{
                $expire     =   is_numeric($options)?$options:NULL;
            }
            return $cache->set($name, $value, $expire);
        }
    }
    /**
     * 数据不存在
     */
    protected function not_found() {
        header("Location:/404.html");
        exit();
    }
    /**
     * 获取分页的界面
     * @param $count　数据的总数
     * @param null $url　需要跳转的url 不传这是请求的url
     * @param int $limit 限制页面条数　不传为10条
     */
    protected function page($count,$url=null,$limit=Controllers::LIMIT){
        $page=new Page($count,$limit);
        $to_url=isset($url)?$url:$_SERVER['REDIRECT_URL'];
        $show=$page->show($to_url);
        $this->assign('page', $show);
    }
    /**
     * 获取分类的id
     * @param $cat_id
     * @return string
     */
    protected function getCatId($cat_id){
        $data='';
        $sql = 'SELECT 	cat_id, parent_id FROM ecs_category WHERE parent_id IN ('. $cat_id. ')';
        $temp = $this->select($sql);
        if($temp){
            foreach($temp as $v){
                $data.=$v['cat_id'].',';
                $data.=$this->getCatId($v['cat_id']);
            }
        }
        return $data;
    }
    /**
     * 过滤用户输入的基本数据，防止script攻击
     * @param $str
     * @return string
     */
    protected function compile_str($str){
        $arr = array('<' => '＜', '>' => '＞','"'=>'”',"'"=>'’');

        return strtr($str, $arr);
    }

    /**
     * 实现发送邮件
     * @param $to 发送人
     * @param $title 邮件标题
     * @param $content 邮件内容
     * @return bool 返回发送状态
     */
    public function email($to,$title,$content){
        $mail=new Email();
        $mail->IsSMTP(); // 启用SMTP
        $mail->Host=$this->config['smtp_host']; //smtp服务器的名称（这里以QQ邮箱为例）
        $mail->SMTPAuth = TRUE; //启用smtp认证
        $mail->Username = $this->config['smtp_user']; //你的邮箱名
        $mail->Password = $this->config['smtp_pass'] ; //邮箱密码
        $mail->From = $this->config['smtp_user']; //发件人地址（也就是你的邮箱地址）
        $mail->FromName = $this->config['shop_name']; //发件人姓名
        $mail->AddAddress($to,"尊敬的客户");//发送邮件地址
        $mail->WordWrap = 50; //设置每行字符长度
        $mail->IsHTML('TRUE'); // 是否HTML格式邮件
        $mail->CharSet='utf-8'; //设置邮件编码
        $mail->Subject =$title; //邮件主题
        $mail->Body = $content; //邮件内容
        $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
        return($mail->Send());
    }

    /**
     * 生成图形验证码
     */
    public function vcode(){
        $vcode=new Vcode();
        $vcode->length=4;
        $vcode->useNoise = false;
        $vcode->imageW=200;
        $vcode->imageH=50;
        $vcode->entry();
    }

    /**
     * 检验图形验证码
     * @return bool
     */
    public function check_vcode(){
        $vcode=new Vcode();
        $code=I('vcode',0);
        return $vcode->check($code,'');
    }

    /**
     * 检验短信验证码
     * @param $phone 手机号
     * @param $code  验证码
     * @param $type  短信类型
     */
    public final function check_msgcode($phone,$code,$type){
        $time=time();
        $sql="select * from ecs_phone_msg WHERE phone='{$phone}' AND code={$code} AND type={$type} ORDER BY id DESC limit 1";
        $data=$this->find($sql);
        if(!$data ||$data['end_time']<$time){
            $this->error('短信验证失败');
        }
    }
}