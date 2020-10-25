<?php
namespace app\wechat\service;
/**
 * 系统支付接口
 */
class PayService {

    /**
     * 获取支付类型接口
     */
    public function getTypePay() {
        return [
            'wechat_mobile' => [
                'name' => '微信支付',
                'desc' => '微信公众号支付手机接口',
                'target' => 'wechat/WechatMobile',
                'platform' => 'mobile',
                'order' => 1,
                'configRule' => [
                    'app_id' => '公众号APPID',
                    'secret' => '公众号密钥',
                    'mch_id' => '商户ID',
                    'md5_key' => '商户密钥',
                    'app_cert_pem_file' => '证书PEM',
                    'app_key_pem_file' => '证书密钥PEM',
                ],
                'env' => isWechat(),
            ],
            'wechat_h5' => [
                'name' => '微信支付',
                'desc' => '微信wap端支付接口',
                'target' => 'wechat/WechatH5',
                'platform' => 'mobile',
                'order' => 1,
                'configRule' => [
                    'app_id' => '公众号APPID',
                    'mch_id' => '商户ID',
                    'md5_key' => '商户密钥',
                    'app_cert_pem_file' => '证书PEM',
                    'app_key_pem_file' => '证书密钥PEM',
                ],
                'env' => !isWechat(),
            ],
            'wechat_web' => [
                'name' => '微信支付',
                'desc' => '微信支付电脑扫码支付接口',
                'target' => 'wechat/WechatWeb',
                'platform' => 'web',
                'order' => 2,
                'configRule' => [
                    'app_id' => '公众号APPID',
                    'mch_id' => '商户ID',
                    'md5_key' => '商户密钥',
                    'app_cert_pem_file' => '证书PEM',
                    'app_key_pem_file' => '证书密钥PEM',
                ],
            ],
            'wechat_app' => [
                'name' => '微信支付',
                'desc' => '微信APP支付支付接口',
                'target' => 'wechat/WechatApp',
                'platform' => 'api',
                'order' => 2,
                'configRule' => [
                    'appid' => '应用ID',
                    'app_id' => '公众号APPID',
                    'mch_id' => '商户ID',
                    'md5_key' => '商户密钥',
                    'app_cert_pem_file' => '证书PEM',
                    'app_key_pem_file' => '证书密钥PEM',
                ],
            ],
        ];
    }
}
