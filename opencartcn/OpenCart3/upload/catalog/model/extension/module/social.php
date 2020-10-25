<?php
/**
 * social.php
 *
 * @copyright 2020 opencart.cn - All Rights Reserved
 * @link https://www.guangdawangluo.com
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2020-06-2020/6/29 14:19
 * @modified 2020-06-2020/6/29 14:19
 */

class ModelExtensionModuleSocial extends Model
{
    public function getCustomerByUid($uid, $type)
    {
        $sql = "SELECT auth.id, auth.access_token, c.* FROM " . DB_PREFIX . "customer_authentication AS auth INNER JOIN " . DB_PREFIX . "customer AS c ON auth.customer_id = c.customer_id WHERE auth.provider = '" . $type . "' AND auth.uid = '" . $uid . "'";
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function getCustomerByUnionId($unionId, $type = "")
    {
        $providerFilter = "";
        if ($type) {
            $providerFilter = " AND auth.provider = '" . $type . "'";
        }
        $sql = "SELECT auth.id, auth.access_token, c.* FROM " . DB_PREFIX . "customer_authentication AS auth INNER JOIN " . DB_PREFIX . "customer AS c ON auth.customer_id = c.customer_id WHERE auth.unionid = '" . $unionId . "'" . $providerFilter;
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function createAuthentication($data)
    {
        if ($data['uid']) {
            $selectSql = "SELECT * FROM " . DB_PREFIX . "customer_authentication WHERE uid = '{$data['uid']}' AND provider = '{$data['provider']}'";
        } elseif ($data['unionid']) {
            $selectSql = "SELECT * FROM " . DB_PREFIX . "customer_authentication WHERE unionid = '{$data['unionid']}' AND provider = '{$data['provider']}'";
        } else {
            throw new Exception('Invalid uid or unionid when create authentication');
        }
        $query = $this->db->query($selectSql);
        if ($query->row) {
            $updateSql = "UPDATE " . DB_PREFIX . "customer_authentication SET `customer_id` = '{$data['customer_id']}' WHERE `id` = '{$query->row['id']}'";
            return $this->db->query($updateSql);
        }

        $insertSql = "INSERT INTO " . DB_PREFIX . "customer_authentication (`customer_id`, `uid`, `unionid`, `provider`, `access_token`, `avatar`, `date_added`, `date_modified`) VALUES ('{$data['customer_id']}', '{$data['uid']}', '{$data['unionid']}', '{$data['provider']}', '{$data['access_token']}', '{$data['avatar']}', '{$data['date_added']}', '{$data['date_modified']}')";
        $this->db->query($insertSql);
    }

    public function updateAuthentication($data)
    {
        $selectSql = "SELECT * FROM " . DB_PREFIX . "customer_authentication WHERE (uid = '{$data['uid']}' and unionid = '{$data['unionid']}') AND provider = '{$data['provider']}'";
        $query = $this->db->query($selectSql);
        if ($query->row) {
            $updateSql = "UPDATE " . DB_PREFIX . "customer_authentication SET uid = '{$data['uid']}', unionid = '{$data['unionid']}', access_token = '{$data['access_token']}', date_modified = '{$data['date_modified']}' WHERE id = '{$query->row['id']}'";
            $this->db->query($updateSql);
        }
    }

    public function getAuthenticationByCustomerId($customer_id, $type) {
        $sql = "SELECT * FROM " . DB_PREFIX . "customer_authentication WHERE provider = '" . $type . "' AND customer_id = '" . (int)$customer_id . "'";
        $query = $this->db->query($sql);

        return $query->row;
    }

    public function log($data)
    {
        $backtrace = debug_backtrace();
        $this->log->write('Log In with social login debug (' . $backtrace[1]['class'] . '::' . $backtrace[1]['function'] . ') - ' . $data);
    }
}