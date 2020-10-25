<?php
namespace Jykj\Donation\Controller;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Cache\CacheManager;

/***
 *
 * This file is part of the "Payment Module" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 王宏彬 <wanghongbin@ngoos.org>, 宁夏极益科技邮箱公司
 *
 ***/
/**
 * PayController
 */
class PayController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * payRepository
     * 
     * @var \Jykj\Donation\Domain\Repository\PayRepository
     * @inject
     */
    protected $payRepository = null;

    var $getArray = [];
    public function initializeAction()
    {
        $GLOBALS['TSFE']->showHiddenRecords = 0;

        //hide hidden records
        if (in_array($this->request->getControllerActionName(), ['success', 'create'])) {
            $GLOBALS['TSFE']->showHiddenRecords = 1;
        }
        $this->getArray = GeneralUtility::explodeUrl2Array($_SERVER['QUERY_STRING']);
        if (isset($this->getArray['id'])) {
            unset($this->getArray['id']);
        }
        if (isset($this->getArray['cHash'])) {
            unset($this->getArray['cHash']);
        }
        if (isset($this->getArray['code'])) {
            unset($this->getArray['code']);
        }
    }

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        if ($_GET["tx_donation_pi1"]["@widget_0"]["currentPage"]) {
            $page = $_GET["tx_donation_pi1"]["@widget_0"]["currentPage"];
        } else {
            $page = 1;
        }
        $this->view->assign('page', $page);
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        $this->payRepository->setDefaultOrderings(['uid' => 'DESC']);
        $pays = $this->payRepository->findAll($keyword);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('pays', $pays);

        //excel export
        if ($this->request->hasArgument('excelExport')) {
            $phpExcelService = GeneralUtility::makeInstanceService('phpexcel');
            $phpExcel = $phpExcelService->getPHPExcel();
            $sheet = $phpExcel->setActiveSheetIndex(0);
            $dataArray[] = ['序号', '名称', '金额', '时间', '付款方式', '标题', '邮箱', '手机', '订单编号'];
            if (count($pays)) {
                foreach ($pays as $pay) {
                    $dataArray[] = [
                        $pay->getUid(),
                        $pay->getName(),
                        $pay->getMoney(),
                        date('Y-m-d H:i', $pay->getCrdate()),
                        $pay->getPayment(),
                        $pay->getTitle(),
                        $pay->getEmail(),
                        ' ' . $pay->getTelephone(),
                        ' ' . $pay->getOrdernumber()
                    ];
                }
                $sheet->fromArray($dataArray, NULL, 'A1');
                $objWriter = $phpExcelService->getInstanceOf('PHPExcel_Writer_Excel2007', $phpExcel);
                $fileName = '财务列表_' . date('Y-m-d');
                header('Pragma: public');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Type: application/force-download');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
                exit;
            }
        }

        //excel import
        if ($this->request->hasArgument('excelImport')) {
            $file = $this->request->getArgument('excelImport');
            $fI = GeneralUtility::split_fileref($file['name']);
            if (GeneralUtility::inList('xls,xlsx', $fI['fileext'])) {
                $objPHPReader = $this->objectManager->get($fI['fileext'] == 'xls' ? 'PHPExcel_Reader_Excel5' : 'PHPExcel_Reader_Excel2007');
                $objPHPExcel = $objPHPReader->load($file['tmp_name']);
                $currentSheet = $objPHPExcel->getSheet(0);
                $allRow = $currentSheet->getHighestRow();
                $i = 0;
                for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
                    if ($currentSheet->getCell('B' . $currentRow)->getValue() != '') {
                        $data = new \Jykj\Donation\Domain\Model\Pay();
                        $data->setCrdate($this->getExcelTime($currentSheet->getCell('A' . $currentRow)->getValue(), true));
                        $data->setName($currentSheet->getCell('B' . $currentRow)->getValue());
                        $data->setMoney($currentSheet->getCell('C' . $currentRow)->getValue());
                        $data->setPayment($currentSheet->getCell('D' . $currentRow)->getValue());
                        $this->payRepository->add($data);
                        $persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
                        $persistenceManager->persistAll();
                        $cert_number = strtoupper($GLOBALS['TYPO3_CONF_VARS']['CERTIFICATE']['prefix'] . '-' . date('Ymd', time()) . '-' . sprintf("%05d", $data->getUid()));
                        $data->setCertnumber($cert_number);
                        $this->payRepository->update($data);

                        /** @var \Jykj\Siteconfig\Controller\ConfigController $certManager */
                        $certManager = $this->objectManager->get(\Jykj\Siteconfig\Controller\ConfigController::class);
                        $certManager->generateCertificate($data->getName(), $data->getMoney(), $cert_number);
                        $i++;
                    }
                }
                $this->addFlashMessage('导入成功,本次共导入 ' . $i . ' 条记录。', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
            } else {
                $this->addFlashMessage('文件格式不正确，请上传xls, xlsx格式文件。', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            }
            $this->redirect('list');
        }
    }

    /**
     * action search for FE
     * 
     * @return void
     */
    public function searchAction()
    {
        if ($_GET["tx_donation_pi1"]["@widget_0"]["currentPage"]) {
            $page = $_GET["tx_donation_pi1"]["@widget_0"]["currentPage"];
        } else {
            $page = 1;
        }
        $this->view->assign('page', $page);
        $arguments = $this->request->getArguments();
        $this->payRepository->setDefaultOrderings(['uid' => 'DESC']);
        $pays = $this->payRepository->findAll('', $arguments);
        $this->view->assign('arguments', $arguments);
        $this->view->assign('pays', $pays);
    }

    /**
     * action show
     * 
     * @param \Jykj\Donation\Domain\Model\Pay $pay
     * @return void
     */
    public function showAction(\Jykj\Donation\Domain\Model\Pay $pay)
    {
        $this->view->assign('pay', $pay);
    }

    /**
     * action new
     * 
     * @return void
     */
    public function newAction()
    {
        if (GeneralUtility::_GP('data')) {
            $this->redirect('success', 'Pay', null, ['data' => GeneralUtility::_GP('data')]);
        }else{
            //if module parameter set, the button will be hide if GET url haven't this parameter.
            $access = 1;
            if ($this->settings['parameter']) {
                if (!isset($this->getArray[$this->settings['parameter']])) {
                    $access = 0;
                }
            }
            $this->view->assignMultiple(
                [
                    'contentObject' => $this->configurationManager->getContentObject()->data,
                    'access' => $access,
                    'url' => GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL')
                ]
            );
            $this->view->assign('test', $_GET['test']);
        }
        if ($this->is_weixin() && $GLOBALS['TYPO3_CONF_VARS']['WECHAT']['appid']!="" && $GLOBALS['TYPO3_CONF_VARS']['WECHAT']['appsecret']!="") {
            //=========步骤1：网页授权获取用户openid============
            $this->initializePaymentConfiguration();
            require_once ExtensionManagementUtility::extPath('donation') . 'lib/wechat/WxPayPubHelper.php';

            //触发微信返回code码, 获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
            // $redirect_uri = GeneralUtility::locationHeaderUrl($this->uriBuilder->reset()->setCreateAbsoluteUri(true)->setArguments(array_merge($this->getArray, array('showwxpaytitle'=>1)))->uriFor('create', array('newPay'=>$newPay->getUid())));
            $redirect_uri = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
            //使用jsapi接口
            $jsApi = new \Jykj\Donation\Weixin\JsApi_pub();

            //通过code获得openid
            if (!isset($_GET['code'])) {
                $code_url = $jsApi->createOauthUrlForCode(urlencode($redirect_uri));
            // var_dump($code_url);exit;
                header("Location: " . $code_url);
                exit;
            } else {
                //获取code码，以获取openid
                $jsApi->setCode($_GET['code']);
                $openID = $jsApi->getOpenId();
                if ($openID) {
                    $this->view->assign('openID', $openID);
                } else {
                    $code_url = $jsApi->createOauthUrlForCode(urlencode($redirect_uri));
                    header("Location: " . $code_url);
                    exit;
                }
            }
        }
    }

    /**
     * action create
     * 
     * @param \Jykj\Donation\Domain\Model\Pay $newPay
     * @return void
     */
    public function createAction(\Jykj\Donation\Domain\Model\Pay $newPay)
    {
        // var_dump($this->request->getArguments());exit;
        //insert new
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            //set url
            // $newPay->setUrl(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));

            //set module
            $newPay->setModule($this->settings['module']);

            //set title
            $newPay->setTitle($this->getCurrentPageTitle());
            if ($this->settings['parameter']) {
                $newPay->setDatauid($this->getArray[$this->settings['parameter']]);
            }
            $newPay->setHidden(1);

            //Inactive before really pay.
            if ($this->is_weixin()) {
                $newPay->setPayment('微信');
            } else {
                $newPay->setPayment('支付宝');
            }

            //check name
            if (!$newPay->getName()) {
                $newPay->setName('匿名');
            }
            $this->payRepository->add($newPay);
            // then persist everything get last sql insert id
            $persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
            $persistenceManager->persistAll();

            //刷新前台缓存
            GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        }

        //开始付款
        if ($this->is_weixin()) {
            $openid = $this->request->getArgument('openid');
            $this->wxPay($newPay, $openid);
        } else {
            $this->aliPay($newPay);
        }
    }

    /**
     * action edit
     * 
     * @param \Jykj\Donation\Domain\Model\Pay $pay
     * @ignorevalidation $pay
     * @return void
     */
    public function editAction(\Jykj\Donation\Domain\Model\Pay $pay)
    {
        $this->view->assign('pay', $pay);
    }

    /**
     * action update
     * 
     * @param \Jykj\Donation\Domain\Model\Pay $pay
     * @return void
     */
    public function updateAction(\Jykj\Donation\Domain\Model\Pay $pay)
    {
        $this->addFlashMessage('数据已更新!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->payRepository->update($pay);

        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action delete
     * 
     * @param \Jykj\Donation\Domain\Model\Pay $pay
     * @return void
     */
    public function deleteAction(\Jykj\Donation\Domain\Model\Pay $pay)
    {
        $this->addFlashMessage('数据已删除!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->payRepository->remove($pay);
        $this->redirect('list');
    }

    /**
     * action 成功
     * 
     * @return void
     */
    public function successAction()
    {
        $paymentObject = $this->payRepository->findByUid($this->request->getArgument('data'));

        //证书申请
        /** @var \Jykj\Siteconfig\Controller\ConfigController $certManager */
        $certManager = $this->objectManager->get(\Jykj\Siteconfig\Controller\ConfigController::class);
        $cert_number = strtoupper($GLOBALS['TYPO3_CONF_VARS']['CERTIFICATE']['prefix'] . '-' . date('Ymd', time()) . '-' . sprintf("%05d", $paymentObject->getUid()));
        $certManager->generateCertificate($paymentObject->getName(), $paymentObject->getMoney(), $cert_number);
        $paymentObject->setCertnumber($cert_number);
        $this->payRepository->update($paymentObject);

        // then persist everything get last sql insert id
        $persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
        $persistenceManager->persistAll();
        if (is_file('uploads/certificate/' . $cert_number . '.jpg') && $paymentObject->getEmail() && GeneralUtility::validEmail($paymentObject->getEmail())) {

            //send email to user.
            if ($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] != '') {
                $mail = $this->objectManager->get(\TYPO3\CMS\Core\Mail\MailMessage::class);
                $mail->setFrom([$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] => $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName']]);
                $mail->setTo([$paymentObject->getEmail() => $paymentObject->getName()]);
                $mail->setSubject($this->settings['mailsubject']);
                $mail->setBody(nl2br($this->settings['mailbody']), 'text/html', 'utf-8');
                $mail->attach(\Swift_Attachment::fromPath(PATH_site . 'uploads/certificate/' . $cert_number . '.jpg')->setFilename($cert_number . '.jpg'));
                $mail->send();
            }
        }
        $this->view->assignMultiple(
            [
                'paymentObject' => $paymentObject,
                'contentObject' => $this->configurationManager->getContentObject()->data,
                'baseURL' => GeneralUtility::getIndpEnv('TYPO3_SITE_URL')
            ]
        );
        $this->addFlashMessage($this->view->render());
        // header('Location: ' . $paymentObject->getUrl());
        // exit;
    }

    /*
     * 证书查询
     */
    public function certificateAction()
    {
        if ($this->request->hasArgument('certnumber')) {
            if (is_file('uploads/certificate/' . $this->request->getArgument('certnumber') . '.jpg')) {
                $this->view->assign('certnumber', $this->request->getArgument('certnumber'));
            } else {
                $this->view->assign('no-result', 1);
            }
        }
    }

    /**
	 * 批量删除
	 * @return void
	 */
	public function multideleteAction(){
	
		$items = $this->request->hasArgument('datas') ? $this->request->getArgument('datas') : array();
		if($items['items']){
			$item =  substr($items['items'], 0, strlen($items['items']) - 1);
			$iRet = $this->payRepository->deleteByUidstring($item);
			if($iRet>0){
			    $this->addFlashMessage('删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
			    //刷新前台缓存
			    GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
			}else{
			    $this->addFlashMessage('删除失败！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
            $this->redirect('list');
		}
        $this->addFlashMessage('没有可删除对象！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->redirect('list');
    }
    
    //Check 微信
    public function is_weixin()
    {
        if (preg_match("/micromessenger/is", $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
        return false;
    }

    /*
     * 初始化账号配置
     */
    private function initializePaymentConfiguration()
    {

        //从插件获取配置信息
        $paymentConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['payment']);

        //=======【支付宝基本信息设置】=====================================
        $GLOBALS['ALIPAY_ACCOUNT'] = $paymentConfig['alipay_account'] ? $paymentConfig['alipay_account'] : $GLOBALS['TYPO3_CONF_VARS']['ALIPAY']['account'];

        //支付宝账号
        $GLOBALS['ALIPAY_PARTNER'] = $paymentConfig['alipay_partner'] ? $paymentConfig['alipay_partner'] : $GLOBALS['TYPO3_CONF_VARS']['ALIPAY']['partner'];

        //合作者身份
        $GLOBALS['ALIPAY_KEY'] = $paymentConfig['alipay_key'] ? $paymentConfig['alipay_key'] : $GLOBALS['TYPO3_CONF_VARS']['ALIPAY']['key'];

        //安全校验码
        //=======【微信基本信息设置】=====================================
        //微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
        $GLOBALS['WX_APPID'] = $paymentConfig['wx_appid'] ? $paymentConfig['wx_appid'] : $GLOBALS['TYPO3_CONF_VARS']['WECHAT']['appid'];

        //受理商ID，身份标识
        $GLOBALS['WX_MCHID'] = $paymentConfig['wx_mchid'] ? $paymentConfig['wx_mchid'] : $GLOBALS['TYPO3_CONF_VARS']['WECHAT']['mchid'];

        //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
        $GLOBALS['WX_KEY'] = $paymentConfig['wx_key'] ? $paymentConfig['wx_key'] : $GLOBALS['TYPO3_CONF_VARS']['WECHAT']['key'];

        //JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
        $GLOBALS['WX_APPSECRET'] = $paymentConfig['wx_appsecret'] ? $paymentConfig['wx_appsecret'] : $GLOBALS['TYPO3_CONF_VARS']['WECHAT']['appsecret'];

        //=======【curl超时设置】===================================
        //本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
        $GLOBALS['WX_CURL_TIMEOUT'] = 60;

        //=======【证书路径设置】=====================================
        //证书路径,注意应该填写绝对路径
        $GLOBALS['WX_SSLCERT_PATH'] = '/xxx/xxx/xxxx/WxPayPubHelper/cacert/apiclient_cert.pem';
        $GLOBALS['WX_SSLKEY_PATH'] = '/xxx/xxx/xxxx/WxPayPubHelper/cacert/apiclient_key.pem';
    }

    /*
     * 支付宝支付
     */
    /**
     * @param $newPay
     */
    private function aliPay($newPay)
    {
        $this->initializePaymentConfiguration();
        require_once ExtensionManagementUtility::extPath('donation') . 'lib/alipay/alipay_submit.class.php';

        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner'] = $GLOBALS['ALIPAY_PARTNER'];

        //安全检验码，以数字和字母组成的32位字符
        $alipay_config['key'] = $GLOBALS['ALIPAY_KEY'];

        //签名方式 不需修改
        $alipay_config['sign_type'] = strtoupper('MD5');

        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset'] = strtolower('utf-8');

        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert'] = ExtensionManagementUtility::extPath('donation') . 'lib/alipay/cacert.pem';

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport'] = 'http';

        /**************************请求参数**************************/
        //支付类型 必填，不能修改
        $payment_type = "1";

        //服务器异步通知页面路径
        $notify_url = GeneralUtility::locationHeaderUrl('typo3conf/ext/donation/api/alipay.php');

        //页面跳转同步通知页面路径
        $return_url = $this->add_querystring_var($newPay->getUrl(),'data',$newPay->getUid());
        // $return_url = GeneralUtility::locationHeaderUrl($this->uriBuilder->reset()->setCreateAbsoluteUri(true)->uriFor('new', ['data' => $newPay->getUid()]));

        //卖家支付宝帐户
        $seller_email = $GLOBALS['ALIPAY_ACCOUNT'];

        //必填        //商户订单号
        $out_trade_no = 'DD-' . date('Ymd', time()) . '-' . $newPay->getUid();

        //商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject = $newPay->getTitle();

        //必填        //付款金额
        $total_fee = $newPay->getMoney();

        //必填        //订单描述
        $body = '捐款人: ' . $newPay->getName();

        //商品展示地址
        $show_url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');

        //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html        //防钓鱼时间戳
        $anti_phishing_key = "";

        //若要使用请调用类文件submit中的query_timestamp函数        //客户端的IP地址 
        $exter_invoke_ip = "";

        //非局域网的外网IP地址，如：221.0.0.1
        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = [
            "service" => $GLOBALS['TYPO3_CONF_VARS']['ALIPAY']['service'],
            "partner" => trim($alipay_config['partner']),
            "payment_type" => $payment_type,
            "notify_url" => $notify_url,
            "return_url" => $return_url,
            "seller_email" => $seller_email,
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "body" => $body,
            "show_url" => $show_url,
            "anti_phishing_key" => $anti_phishing_key,
            "exter_invoke_ip" => $exter_invoke_ip,
            "_input_charset" => trim(strtolower($alipay_config['input_charset']))
        ];
        // var_dump($parameter);exit;
        //建立请求
        $alipaySubmit = new \Jykj\Donation\Controller\AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter, "post", "确认");
        $this->view->assign('payHtml', $html_text);
    }

    /*
     * 微信支付
     */
    /**
     * @param $newPay
     * @param $openid
     */
    public function wxPay($newPay, $openid)
    {
        $this->initializePaymentConfiguration();
        require_once ExtensionManagementUtility::extPath('donation') . 'lib/wechat/WxPayPubHelper.php';

        //=========步骤2：使用统一支付接口，获取prepay_id============
        $jsApi = new \Jykj\Donation\Weixin\JsApi_pub();
        $out_trade_no = 'DD-' . date('Ymd', time()) . '-' . $newPay->getUid();

        //使用统一支付接口
        $unifiedOrder = new \Jykj\Donation\Weixin\UnifiedOrder_pub();

        //设置统一支付接口参数
        $unifiedOrder->setParameter("openid", $openid);

        //商品描述
        $unifiedOrder->setParameter("body", $newPay->getTitle());

        //商品描述
        $unifiedOrder->setParameter("out_trade_no", $out_trade_no);

        //商户订单号
        $unifiedOrder->setParameter("total_fee", $newPay->getMoney() * 100);

        //总金额, 微信中单位为分
        $unifiedOrder->setParameter("notify_url", GeneralUtility::locationHeaderUrl('typo3conf/ext/donation/api/wxpay.php'));

        //异步通知url
        $unifiedOrder->setParameter("trade_type", "JSAPI");
        //交易类型
        //非必填参数，商户可根据实际情况选填
        //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
        //$unifiedOrder->setParameter("device_info","XXXX");//设备号
        //$unifiedOrder->setParameter("attach","XXXX");//附加数据
        //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
        //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
        //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
        //$unifiedOrder->setParameter("openid","XXXX");//用户标识
        //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
        $prepay_id = $unifiedOrder->getPrepayId();
        // dump($unifiedOrder);
        // dump($prepay_id);exit;

        //=========步骤3：使用jsapi调起支付============
        $jsApi->setPrepayId($prepay_id);
        $jsApiParameters = $jsApi->getParameters();
        // $successUrl = $this->uriBuilder->reset()->setCreateAbsoluteUri(true)->setArguments(['news-id' => $newPay->getDatauid()])->uriFor('success', ['data' => $newPay->getUid()]);
        $successUrl = $this->add_querystring_var($newPay->getUrl(),'data',$newPay->getUid());
        $filedUrl = $newPay->getUrl();
        $html_text = '
            <script type="text/javascript">
                function onBridgeReady() {
                    WeixinJSBridge.invoke(
                        "getBrandWCPayRequest", ' . $jsApiParameters . ',
                        function (res) {
                            if (res.err_msg == "get_brand_wcpay_request:ok") {
                                window.location.href = "' . $successUrl . '";
                            } else {
                                window.location.href = "' . $filedUrl . '";
                            }
                        }
                    );
                }
                if (typeof WeixinJSBridge == "undefined") {
                    if (document.addEventListener) {
                        document.addEventListener("WeixinJSBridgeReady", onBridgeReady, false);
                    } else if (document.attachEvent) {
                        document.attachEvent("WeixinJSBridgeReady", onBridgeReady);
                        document.attachEvent("onWeixinJSBridgeReady", onBridgeReady);
                    }
                } else {
                    onBridgeReady();
                }
            </script>
        ';
        $this->view->assign('payHtml', $html_text);
    }
    function getCurrentPageTitle()
    {
        if ($GLOBALS['TSFE']->indexedDocTitle) {
            return $GLOBALS['TSFE']->indexedDocTitle;
        }
        if ($GLOBALS['TSFE']->page['title']) {
            return $GLOBALS['TSFE']->page['title'];
        }
        return 'Page id: ' . $GLOBALS['TSFE']->id;
    }

    function add_querystring_var($url, $key, $value) {
        $url=preg_replace('/(.*)(?|&)'.$key.'=[^&]+?(&)(.*)/i','$1$2$4',$url.'&');
        $url=substr($url,0,-1);
        if(strpos($url,'?') === false){
            return ($url.'?'.$key.'='.$value);
        } else {
            return ($url.'&'.$key.'='.$value);
        }
    }

    /**
     * phpexcel 读取时间
     * 
     * @param $date
     * @param $timestamp
     */
    protected function getExcelTime($date, $timestamp = false)
    {
        if (\is_numeric($date)) {
            $n = intval(($date - 25569) * 3600 * 24);

            //转换成1970年以来的秒数
            if ($timestamp) {
                return $n;
            } else {
                return gmdate('Y-m-d H:i:s', $n);
            }

            //格式化时间
        } else {
            if ($timestamp) {
                return strtotime($date);
            } else {
                return $date;
            }

            //格式化时间
        }
    }
}
