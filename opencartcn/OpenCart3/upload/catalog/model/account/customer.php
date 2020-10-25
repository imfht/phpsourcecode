<?php
class ModelAccountCustomer extends Model {
	public function addCustomer($data) {
		if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $data['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$this->load->model('account/customer_group');

		$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

        $calling_code = array_get($data, 'calling_code', '');
        $calling_code = $calling_code ?: config('config_calling_code');

		$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$customer_group_id . "', store_id = '" . (int)$this->config->get('config_store_id') . "', language_id = '" . (int)$this->config->get('config_language_id') . "', firstname = '" . $this->db->escape((string)$data['firstname']) . "', lastname = '" . $this->db->escape((string)$data['lastname']) . "', email = '" . $this->db->escape((string)$data['email']) . "', calling_code = '" . $this->db->escape($calling_code) . "', telephone = '" . $this->db->escape((string)$data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "', salt = '', password = '" . $this->db->escape(password_hash($data['password'], PASSWORD_DEFAULT)) . "', newsletter = '" . (isset($data['newsletter']) ? (int)$data['newsletter'] : 0) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = '" . (int)!$customer_group_info['approval'] . "', date_added = NOW()");

		$customer_id = $this->db->getLastId();

		if ($customer_group_info['approval']) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_approval` SET customer_id = '" . (int)$customer_id . "', type = 'customer', date_added = NOW()");
		}

		return $customer_id;
	}

	public function editCustomer($customer_id, $data) {
        $calling_code = array_get($data, 'calling_code', '');
        $calling_code = $calling_code ?: config('config_calling_code');
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape((string)$data['firstname']) . "', lastname = '" . $this->db->escape((string)$data['lastname']) . "', email = '" . $this->db->escape((string)$data['email']) . "', calling_code = '" . $this->db->escape($data['calling_code']) . "', telephone = '" . $this->db->escape((string)$data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function editPassword($customer_id, $password) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET salt = '', password = '" . $this->db->escape(password_hash($password, PASSWORD_DEFAULT)) . "', code = '' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function editAddressId($customer_id, $address_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function editCode($customer_id, $code) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET code = '" . $this->db->escape($code) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function editToken($customer_id, $token) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function editNewsletter($newsletter) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET newsletter = '" . (int)$newsletter . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}

	public function getCustomerByEmail($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getCustomerByTelephone($telephone, $callingCode = '') {
        $builder = table('customer')->where('telephone', $telephone);
        if ($callingCode) {
            $builder->where('calling_code', $callingCode);
        }
        $result = $builder->first();
        return $this->toArray($result);
	}

	public function getCustomerByCode($code) {
		$query = $this->db->query("SELECT customer_id, firstname, lastname, email FROM `" . DB_PREFIX . "customer` WHERE code = '" . $this->db->escape($code) . "' AND code != ''");

		return $query->row;
	}

	public function getCustomerByToken($token) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE token = '" . $this->db->escape($token) . "' AND token != ''");

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET token = ''");

		return $query->row;
	}

	public function getTotalCustomersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}

	public function getTotalCustomersByTelephone($telephone) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE telephone = '" . $this->db->escape($telephone) . "'");

		return $query->row['total'];
	}

	public function addTransaction($customer_id, $description, $amount = '', $order_id = 0) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$customer_id . "', order_id = '" . (float)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");
	}

	public function deleteTransactionByOrderId($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getTransactionTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalTransactionsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getRewardTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getIps($customer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->rows;
	}

	public function addLogin($customer_id, $ip, $country = '') {
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . (int)$customer_id . "', store_id = '" . (int)$this->config->get('config_store_id') . "', ip = '" . $this->db->escape($ip) . "', country = '" . $this->db->escape($country) . "', date_added = NOW()");
	}

	public function addLoginAttempt($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_login WHERE customer_id = '" . (int)$customer_id . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");

		if (!$query->num_rows) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_login SET customer_id = '" . (int)$customer_id . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', total = 1, date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "customer_login SET total = (total + 1), date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE customer_login_id = '" . (int)$query->row['customer_login_id'] . "'");
		}
	}

	public function getLoginAttempts($customer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($customer_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE customer_id = '" . (int)$customer_id . "'");
	}

    public function getCustomerFromAuth($socialData)
    {
        $provider = array_get($socialData, 'provider');
        $customerInfo = array();
        $uid = array_get($socialData, 'uid');
        if ($uid && in_array($provider, ['facebook', 'twitter'])) {
            $customerInfo = $this->getModel($provider)->getCustomerByUid($uid, $provider);
        } elseif (strtolower($provider) == 'instagram') {
            $customerInfo = $this->getModel($provider)->getCustomerByUid($uid, $provider);
        } elseif (strtolower($provider) == 'paypal') {
            $customerInfo = $this->getModel($provider)->getCustomerByUid($uid, $provider);
        }
        return $customerInfo;
    }

    /**
     * @param $customerId
     * @return array|bool
     * @throws Exception
     */
    public function bindCustomer($customerId)
    {
        $socialData = array_get($this->session->data, 'social_data');
        if (empty($socialData)) {
            return false;
        }
        $auth = $this->createAuth($customerId, $socialData);
        //$this->saveAvatarFromSocial($customerId, $socialData);
        unset($this->session->data['social_data']);
        return $auth;
    }

    /**
     * @param $customerId
     * @param $socialData
     * @return array
     * @throws Exception
     */
    public function createAuth($customerId, $socialData)
    {
        $provider = array_get($socialData, 'provider');
        $authData = array(
            'customer_id' => $customerId,
            'uid' => array_get($socialData, 'uid'),
            'unionid' => array_get($socialData, 'union_id'),
            'provider' => $provider,
            'access_token' => array_get($socialData, 'access_token'),
            'token_secret' => array_get($socialData, 'token_secret', ''),
            'avatar' => array_get($socialData, 'avatar', ''),
            'date_added' => date('Y-m-d H:i:s'),
            'date_modified' => date('Y-m-d H:i:s')
        );
        $authentication = $this->getModel($provider)->createAuthentication($authData);
        return $authentication;
    }

    /**
     * @param $socialData
     * @return int
     * @throws Exception
     */
    public function createCustomer($socialData)
    {
        $provider = array_get($socialData, 'provider');
        $customer_group_id = $this->config->get('config_customer_group_id');
        $data = array(
            'customer_group_id' => (int)$customer_group_id,
            'firstname' => array_get($socialData, 'name'),
            'lastname' => '',
            'email' => array_get($socialData, 'email', ''),
            'telephone' => '',
            'fax' => '',
            'password' => '',
            'company' => '',
            'from' => $provider
        );
        $customerId = $this->addCustomer($data);
        if (!array_get($socialData, 'name')) {
            $this->updateName($customerId, $socialData);
        }
        $this->createAuth($customerId, $socialData);
        return $customerId;
    }

    /**
     * @param $customerId
     * @param $socialData
     */
    public function saveAvatarFromSocial($customerId, $socialData)
    {
        $provider = array_get($socialData, 'provider');
        $user = array_get($socialData, 'user');
        $imageUrl = $this->getRemoteAvatarUrl($provider, $user);
        if (empty($imageUrl)) {
            return;
        }
        $this->saveRemoteAvatar($customerId, $imageUrl);
    }

    private function getRemoteAvatarUrl($provider, $user)
    {
        $avatar = '';

        if ($provider == 'google' || $provider == 'facebook') {
            $avatar = array_get($user, 'avatar');
        }

        return $avatar;
    }

    /**
     * @param $customerId
     * @param $avatarUrl
     */
    public function saveRemoteAvatar($customerId, $avatarUrl)
    {
        $existAvatar = DIR_IMAGE . 'avatar/' . $customerId . '.jpg';
        if (file_exists($existAvatar) && filesize($existAvatar)) {
            return;
        }
        $this->load->model('tool/image');
        $this->model_tool_image->getImage($avatarUrl, DIR_IMAGE . 'avatar/', $customerId . '.jpg');
    }

    /**
     * @param $socialData
     * @return array
     */
    public function getAuthData($socialData)
    {
        $provider = array_get($socialData, 'provider');
        return array(
            'uid' => array_get($socialData, 'uid'),
            'unionid' => array_get($socialData, 'union_id'),
            'access_token' => array_get($socialData, 'access_token', ''),
            'token_secret' => array_get($socialData, 'token_secret', ''),
            'provider' => $provider,
            'date_modified' => date('Y-m-d H:i:s')
        );
    }

    /**
     * @param $provider
     * @return ModelExtensionModuleSocial
     * @throws Exception
     */
    public function getModel($provider)
    {
        $modelKey = $provider . '_login';
        $modelName = "model_extension_module_{$modelKey}";
        if (class_exists($modelName)) {
            return $this->$modelName;
        } else {
            model('extension/module/social');
            model("extension/module/{$modelKey}");
            return $this->$modelName;
        }
    }
}
