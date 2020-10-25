<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-08 03:04:55
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-20 09:46:58
 */

namespace api\modules\wechat;

use common\helpers\FileHelper;
use common\helpers\StringHelper;
use common\models\DdCorePaylog;
use Yii;
use yii\di\ServiceLocator;

/**
 * wechat module definition class.
 */
class module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'api\modules\wechat\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        global $_GPC;
        $logPath = Yii::getAlias('@runtime/wechat/payparameters'.date('ymd').'.log');
        parent::init();

        /* 加载语言包 */
        if (!isset(Yii::$app->i18n->translations['wechat'])) {
            Yii::$app->i18n->translations['wechat'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@api/modules/wechat/messages',
            ];
        }

        $config = require __DIR__.'/config.php';
        // 获取应用程序的组件
        $components = \Yii::$app->getComponents();

        // 遍历子模块独立配置的组件部分，并继承应用程序的组件配置
        foreach ($config['components'] as $k => $component) {
            if (isset($component['class']) && isset($components[$k]) == false) {
                continue;
            }
            $config['components'][$k] = array_merge($components[$k], $component);
        }

        // 微信回调跟进订单初始化
        $settings = Yii::$app->settings;

        $input = file_get_contents('php://input');
        FileHelper::writeLog($logPath, '入口配置回来的值'.$input);
        FileHelper::writeLog($logPath, '入口配置回来的值0-5'.substr($input, 0, 5));
        if (substr($input, 0, 5) == '<xml>') {
            FileHelper::writeLog($logPath, '准备处理');
            $xmldata = StringHelper::getXml($input);
            FileHelper::writeLog($logPath, 'xml解析后'.$xmldata['trade_type'].'/'.json_encode($xmldata));
            if ($xmldata['trade_type'] == 'JSAPI') {
                $out_trade_no = $xmldata['out_trade_no'];
                FileHelper::writeLog($logPath, '入口配置回来的订单编号：'.$out_trade_no);
                $DdCorePaylog = new DdCorePaylog();
                $orderInfo = $DdCorePaylog->find()->where([
                    'uniontid' => trim($out_trade_no),
                ])->select(['bloc_id', 'store_id', 'module'])->asArray()->one();
                FileHelper::writeLog($logPath, '入口配置回来的xml值订单日志sql'.$DdCorePaylog->find()->where([
                    'uniontid' => trim($out_trade_no),
                ])->select(['bloc_id', 'store_id', 'module'])->createCommand()->getRawSql());
                FileHelper::writeLog($logPath, '入口配置回来的xml值订单日志'.json_encode($orderInfo));

                Yii::$app->service->commonGlobalsService->initId($orderInfo['bloc_id'], $orderInfo['store_id'], $orderInfo['module']);
                Yii::$app->service->commonGlobalsService->getConf($orderInfo['bloc_id']);
                Yii::$app->params['bloc_id'] = $orderInfo['bloc_id'];
                Yii::$app->params['store_id'] = $orderInfo['store_id'];
            }

            FileHelper::writeLog($logPath, '_W_W_W'.json_encode(Yii::$app->params['bloc_id']));
        }

        $params = Yii::$app->params;
        $conf = $params['conf'];

        $Wechatpay = $conf['wechatpay'];
        $Wxapp = $conf['wxapp'];
        // 支付参数设置
        $config['params']['wechatPaymentConfig'] = [
            'app_id' => $Wechatpay['app_id'],
            'mch_id' => $Wechatpay['mch_id'],
            'key' => $Wechatpay['key'],  // API 密钥
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            // 'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
            // 'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！
            'notify_url' => Yii::$app->request->hostInfo.'/api/wechat/basics/notify',
        ];

        FileHelper::writeLog($logPath, '入口配置'.json_encode($config['params']['wechatPaymentConfig']));
        FileHelper::writeLog($logPath, '总配置'.json_encode($conf));
        FileHelper::writeLog($logPath, '小程序配置'.json_encode($Wxapp));

        // 小程序参数设置
        $config['params']['wechatMiniProgramConfig'] = [
            'app_id' => $Wxapp['AppId'],
            'secret' => $Wxapp['AppSecret'],
             // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => Yii::getAlias('@runtime/miniprogram'),
            ],
        ];
        
        $params = Yii::$app->params;
        
        foreach ($params as $key => $value) {
           if(isset($config['params'][$key])){
                $config['params'][$key] = $config['params'][$key];
           }else{
                $config['params'][$key] = $value;
           } 
        }
        
        // 将新的配置设置到应用程序
        // 很多都是写 Yii::configure($this, $config)，但是并不适用子模块，必须写 Yii::$app
        \Yii::configure(\Yii::$app, $config);
    }
}
