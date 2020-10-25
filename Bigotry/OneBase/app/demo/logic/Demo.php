<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\demo\logic;

use app\common\logic\LogicBase;

/**
 * 演示逻辑
 */
class Demo extends LogicBase
{
    
    /**
     * 各层引用演示
     */
    public function demoExecute()
    {
        
        /**
         * 控制器层引用业务逻辑层
         * 执行查询文章列表逻辑
         * 前缀 logic
         */
        $this->logicArticle->getArticleList();
        
        /**
         * 业务逻辑层引用模型层
         * 执行模型查询文章列表
         * 前缀 model
         */
        $this->modelArticle->getList();
        
        /**
         * 业务逻辑层引用验证层
         * 执行文章分类数据编辑场景验证
         * 前缀 validate
         */
        $this->validateArticleCategory->scene('edit')->check([]);
        
        /**
         * 业务逻辑层引用服务层
         * 执行存储服务下的七牛驱动进行文件上传
         * 前缀 service | driver
         */
        $this->serviceStorage->driverQiniu->uploadFile(130);
    }
    
    /**
     * 事务控制
     */
    public function demoTransaction()
    {
        
        $func1 = function() { $this->modelMember->setFieldValue(['username' => 'demo'], 'nickname', 'test_demo'); };
                
        $func2 = function() { $a = 1/0; [$a]; };
        
        closure_list_exe([$func1, $func2]);
    }

    /**
     * 云存储服务
     */
    public function demoStorage()
    {
        
        return $this->serviceStorage->driverQiniu->uploadFile(130);
    }
    
    /**
     * 支付服务
     */
    public function demoPay()
    {
        
        $test_order['order_sn']         =   date('ymdhis', time()) . rand(10000, 99999);
        $test_order['body']             =   '测试';
        $test_order['order_amount']     =   0.01;
        
        // （微信公众号下使用JSAPI支付时才需要此参数，用于跳转授权）
        $test_order['redirect_uri']     =   'http://ob.xxx.cn';
        
        //-------------- 支付宝相关支付-----------------
        
        // （电脑网站环境下）支付宝PC网站发起支付
        echo $this->servicePay->driverAlipay->pay($test_order);
        
        // （移动端非微信环境浏览器下）支付宝H5支付
        echo $this->servicePay->driverAlipay->pay($test_order, 'h5');

        // （支付宝APP支付） 返回给IOS或安卓 客户端处理
        dump($this->servicePay->driverAlipay->pay($test_order, 'app'));
        
        
        //-------------- 微信相关支付------------------

        // （电脑网站环境下）微信PC网站发起支付
        echo $this->servicePay->driverWxpay->pay($test_order);

        // （移动端非微信环境的浏览器下）微信 H5 支付
        echo '<a href="'.$this->servicePay->driverWxpay->pay($test_order, 'h5').'">点击跳转H5微信支付</a>';
        
        // （微信公众号环境下） JSAPI 支付
        echo $this->servicePay->driverWxpay->pay($test_order, 'JSAPI');
        
        // （微信APP支付） 返回给IOS或安卓 客户端处理
        dump($this->servicePay->driverWxpay->pay($test_order, 'app'));
    }
    
    /**
     * 前端支付状态检测
     */
    public function demoCheckPayStatus($param = [])
    {

        // 业务逻辑代码块...
        
        dump($param['order_sn']);
        
        // 未支付
        die('error');
        
        
        // 已支付
        die('succeed');
    }

    /**
     * 支付异步通知处理
     */
    public function demoPayNotify()
    {
        
        // 获取订单号
        $order_sn = get_order_sn();
        
        // 获取订单信息
        $info = $this->modelOrder->getInfo(['order_sn' => $order_sn]);
        
        // 验证订单是否存在
        empty($info) && die('不存在订单号');
        
        // 获取支付驱动
        $select_driver = SYS_DRIVER_DIR_NAME . $info['pay_type'];
        
        // 验证通知是否合法
        $result = $this->servicePay->$select_driver->notify();
        
        /**
         * @todo 支付完成后通过订单号处理相应业务逻辑
         */
        if ($result) {
            
            // 执行支付成功业务逻辑代码块...
        }
        
    }
    
    /**
     * 短信服务
     */
    public function demoSendSms()
    {
        
        // 短信发送
        $parameter['sign_name']      = 'OneBase架构';
        $parameter['template_code']  = 'SMS_113455309';
        $parameter['phone_number']   = '18555550710';
        $parameter['template_param'] = ['code' => '123456'];
        
        return $this->serviceSms->driverAlidy->sendSms($parameter);
        
        /*
        // 短信验证码验证
        $check_data['phone_number']   = '18555550710';
        $check_data['code']           = '123456';
        
        $check_result = $this->serviceSms->driverAlidy->checkSmsCode($check_data);
        
        if ($check_result) {
            
            // 短信验证码验证通过
        } else {
            
            // 短信验证码不正确
        }
        */
    }
    
    /**
     * 数据导入
     */
    public function demoDataImport($test_url = 'F:\\test.xlsx')
    {
        
        $data = get_excel_data($test_url);
        
        dump($data);
        
        // 此处已经将表格中的数据保存到$data数组中，后续根据自己的业务逻辑将数据写入某表
    }
    
    /**
     * 数据导出
     */
    public function demoDataExport()
    {
        
        $list   = $this->modelMember->getList([], true, 'id', false);
        
        $titles = "昵称,用户名,邮箱,注册时间";
        $keys   = "nickname,username,email,create_time";
        
        export_excel($titles, $keys, $list, '会员列表');
    }
    
    /**
     * 二维码 条形码
     */
    public function demoQrcodeBarcode()
    {
        
        // 生成二维码
        $qr_data  = create_qrcode('onebase.org');
        
        // 生成条形码
        $bar_data = create_barcode('onebase.org', 'onebase');
        
        dump($qr_data);
        dump($bar_data);
    }
    
    /**
     * 生成海报
     */
    public function demoPoster()
    {
        
        // windows
        $poster_data = create_poster(
                "D:/xampp/htdocs/OneBase/public/upload/extend/qrcode/e9ff27b4d969cfad54b5388c381e2022.png",
                "D:/xampp/htdocs/OneBase/public/upload/extend/poster/poster_bg.jpg",
                 200,
                 [200,100]
                );
        
        // linux
        
        /*
        $poster_data = create_poster(
                "./upload/extend/qrcode/e9ff27b4d969cfad54b5388c381e2022.png",
                "./upload/extend/poster/poster_bg.jpg",
                 200,
                 [200,100]
                );
        */
        
        dump($poster_data);
    }
    
    /**
     * 邮件发送
     */
    public function demoSendEmail()
    {
        
        $data = send_email('3162875@qq.com', '测试', '这是一封测试邮件');
        
        dump($data);
    }
    
    /**
     * 视频点播服务
     */
    public function demoVod()
    {
        
        $obj = $this->serviceVod->driverAlivod->createUploadVideo();
        
        $video_info = $this->serviceVod->driverAlivod->uploadVideo($obj, './test.mp4');
        
        dump($video_info);
    }
}
