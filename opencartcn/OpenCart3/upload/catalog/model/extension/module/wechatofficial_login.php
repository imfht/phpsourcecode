<?php

/**
 * ModelModuleWeiboLogin
 *
 * @copyright  2016 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2016-11-18 16:00
 * @modified   2016-11-18 16:00
 */

class ModelExtensionModuleWechatofficialLogin extends ModelExtensionModuleSocial
{
    public function handleWeChat($unionId, $uid, $accessToken)
    {
        if ($unionId) {
            $customer = $this->getCustomerByUnionId($unionId, 'wechatofficial');
            if ($customer) {
                return $customer;
            }
            $customer = $this->getCustomerByUnionId($unionId);
            if ($customer) {
                $authData = array(
                    'customer_id' => $customer['customer_id'],
                    'uid' => $uid,
                    'unionid' => $unionId,
                    'provider' => 'wechatofficial',
                    'avatar' => '',
                    'access_token' => $accessToken,
                    'date_added' => date('Y-m-d H:i:s'),
                    'date_modified' => date('Y-m-d H:i:s')
                );
                $this->createAuthentication($authData);
                return $customer;
            }
        } elseif ($uid) {
            $customer = $this->getCustomerByUid($uid, 'wechatofficial');
            return $customer;
        }
        return array();
    }
}
