<?php
/**
 * instagram_login.php
 *
 * @copyright 2019 opencart.cn - All Rights Reserved
 * @link https://www.guangdawangluo.com
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2019-09-2019-09-19 11:14
 * @modified 2019-09-2019-09-19 11:14
 */

class ModelExtensionModuleInstagramLogin extends ModelExtensionModuleSocial
{
    public function handleInstagram($uid, $access_token)
    {
        $customer = $this->getCustomerByUid($uid, 'instagram');
    }
}