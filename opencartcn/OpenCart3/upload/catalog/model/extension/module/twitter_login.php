<?php
/**
 * twitter_login.php
 *
 * @copyright 2018 OpenCart.cn
 *
 * All Rights Reserved
 * @link http://guangdawangluo.com
 *
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2018-08-02 12:18
 * @modified 2018-08-02 12:18
 */

class ModelExtensionModuleTwitterLogin extends ModelExtensionModuleSocial
{
    public function createAuthentication($data)
    {
        if ($data['uid']) {
            $selectSql = "SELECT * FROM " . DB_PREFIX . "customer_authentication WHERE uid = '{$data['uid']}' AND provider = '{$data['provider']}'";
        } else {
            throw new Exception('Invalid uid or unionid when create authentication');
        }
        $query = $this->db->query($selectSql);
        if ($query->row) {
            $updateSql = "UPDATE " . DB_PREFIX . "customer_authentication SET `customer_id` = '{$data['customer_id']}' WHERE `id` = '{$query->row['id']}'";
            return $this->db->query($updateSql);
        }

        $insertSql = "INSERT INTO " . DB_PREFIX . "customer_authentication (`customer_id`, `uid`, `unionid`, `provider`, `access_token`, `token_secret`, `avatar`, `date_added`, `date_modified`) VALUES ('{$data['customer_id']}', '{$data['uid']}', '{$data['unionid']}', '{$data['provider']}', '{$data['access_token']}', '{$data['token_secret']}', '{$data['avatar']}', '{$data['date_added']}', '{$data['date_modified']}')";
        $this->db->query($insertSql);
    }

    public function updateAuthentication($data)
    {
        $selectSql = "SELECT * FROM " . DB_PREFIX . "customer_authentication WHERE (uid = '{$data['uid']}' and unionid = '{$data['unionid']}') AND provider = '{$data['provider']}'";
        $query = $this->db->query($selectSql);
        if ($query->row) {
            $updateSql = "UPDATE " . DB_PREFIX . "customer_authentication SET uid = '{$data['uid']}', unionid = '{$data['unionid']}', access_token = '{$data['access_token']}', token_secret = '{$data['token_secret']}', date_modified = '{$data['date_modified']}' WHERE id = '{$query->row['id']}'";
            $this->db->query($updateSql);
        }
    }
}