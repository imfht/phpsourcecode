<?php
/**
 * social.php
 *
 * @copyright 2020 opencart.cn - All Rights Reserved
 * @link https://www.guangdawangluo.com
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2020-06-2020/6/29 14:17
 * @modified 2020-06-2020/6/29 14:17
 */

class ControllerAccountSocial extends Controller
{
    private $socialProviders = ['facebook', 'twitter', 'instagram', 'google', 'paypal'];
    private $chSocialProviders = ['wechatofficial', 'qq', 'weibo', 'wechat'];
    private $error = array();
    private $provider = '';
    private $loginKey = '';
    private $logger;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('extension/module/social');
        $this->load->model('account/customer');
        $this->load->model('account/customer_group');
        $this->load->language('account/login');

        $provider = strtolower(array_get($this->request->get, 'provider'));
        if (empty($provider)) {
            $provider = strtolower(array_get($this->request->post, 'provider'));
        }
        $this->provider = $provider;
        if (!in_array($this->provider, $this->socialProviders)) {
            throw new Exception('Invalid social provider');
        }
        $this->logger = new \Log("social/" . $this->provider);
    }

    public function getLoginKey()
    {
        if (empty($this->provider)) {
            throw new Exception('Empty social provider');
        } elseif (empty($this->loginKey)) {
            $this->loginKey = "{$this->provider}_login";
        }
        return $this->loginKey;
    }

    public function redirect()
    {
        $social = Social::getInstance($this->provider);
        $url = $social->redirectAuthUrl();
        $this->response->redirect($url);
    }

    /**
     * callback/{provider}?code=xxxx
     * @return bool
     */
    public function index()
    {
        if ($this->customer->isLogged()) {
            $this->session->data['associate'] = true;
        }

        $social = Social::getInstance($this->provider);
        $this->logInfo('Provider: ', $this->provider);
        $this->logInfo('Code: ', array_get($this->request->get, 'code'));
        $socialData = $social->getUserData();

        // User exist in database
        $customerInfo = $this->model_account_customer->getCustomerFromAuth($socialData);
        $this->logInfo('CustomerInfo:', $customerInfo);
        if ($customerInfo) {
            $customerId = $customerInfo['customer_id'];
            $this->model_account_customer->saveAvatarFromSocial($customerId, $socialData);
            if ($this->validate($customerId)) {
                $authData = $this->model_account_customer->getAuthData($socialData);
                $this->logInfo('AuthData:', $authData);
                $this->completeLogin($customerInfo['customer_id'], $customerInfo['email'], $authData);
                return true;
            } else {
                $this->logInfo('Could not login to - ID: ' . $customerInfo['customer_id'] . ', Email: ' . $customerInfo['email']);
                return $this->jsRedirect();
            }
        }

        // User not exist in database, then create it.
        $this->logInfo('User not exist');
        if ($this->customer->isLogged()) {
            $customerId = $this->customer->getId();
            $this->model_account_customer->createAuth($customerId, $socialData);
        } else {
            if (config('module_omni_auth_bind')) {
                $this->session->data['social_data'] = $socialData;
                return $this->jsRedirect('prompt');
            } else {
                $customerId = $this->model_account_customer->createCustomer($socialData);
            }
        }
        $this->model_account_customer->saveAvatarFromSocial($customerId, $socialData);
        $this->logInfo('Customer ID date_added: ' . $customerId);
        $customerInfo = $this->model_account_customer->getCustomerFromAuth($socialData);
        if ($customerInfo && $this->validate($customerInfo['customer_id'])) {
            $authData = $this->model_account_customer->getAuthData($socialData);
            $this->completeLogin($customerId, $customerInfo['email'], $authData);
        } else {
            $user = array_get($socialData, 'user');
            $this->logInfo('Could not login to - ID: ' . $customerId);
            $this->logInfo($user);
            return $this->jsRedirect();
        }
    }

    public function disconnect()
    {
        $customerId = $this->customer->getId();
        $provider = array_get($this->request->post, 'provider');
        $error = '';
        if (!in_array($provider, $this->socialProviders)) {
            $error = 'Invalid provider!';
        } elseif (empty($customerId)) {
            $error = 'Not logged in!';
        }
        $customer = Models\Customer::find($customerId);
        if (empty($customer)) {
            $error = 'Invalid customer!';
        }
        if ($error) {
            $this->jsonOutput(array('error' => $error));
        }
        $customer->authentications()->byProvider($provider)->delete();
        $this->jsonOutput(array('success' => 'Success'));
    }

    public function logout()
    {
        if (isset($this->session->data["{$this->getLoginKey()}"])) {
            unset($this->session->data["{$this->getLoginKey()}"]);
        }
    }


    private function logInfo(...$messages)
    {
        if (!$this->config->get('module_omni_auth_debug')) {
            return false;
        }
        foreach ($messages as $message) {
            $this->logger->write($message);
        }
    }

    private function jsRedirect($message = '')
    {
        if ($isAssociating = array_get($this->session->data, 'associate')) {
            $url = $this->url->link('account/edit');
            unset($this->session->data['associate']);
        } elseif (!$this->customer->isLogged() && array_get($this->session->data, 'social_data')) {
            $url = $this->url->link('account/bind');
        } elseif ($redirect = array_get($this->session->data, 'redirect')) {
            $url = $redirect;
        } else {
            $url = $this->url->link('account/account');
        }
        echo '<!--' . $message . '-->';
        echo '<script type="text/javascript">';
        echo 'if(window.opener === null) {window.location.href = "' . $url . '";} else {window.opener.location = "' . $url . '"; window.close();}';
        echo '</script>';
        return false;
    }

    protected function validate($customer_id)
    {
        // Check how many login attempts have been made.
        $login_info = $this->model_account_customer->getLoginAttempts($customer_id);

        if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
            $this->error['warning'] = $this->language->get('error_attempts');
        }

        // Check if customer has been approved.
        $customerInfo = $this->model_account_customer->getCustomer($customer_id);

        $this->logInfo("Validation Customer:", $customerInfo);
        if ($customerInfo && !$customerInfo['status']) {
            $this->error['warning'] = $this->language->get('error_approved');
        }

        if (!$this->error) {
            if (!$this->customer->login($customer_id, '', true)) {
                $this->error['warning'] = $this->language->get('error_login');

                $this->model_account_customer->addLoginAttempt($customer_id);
            } else {
                $this->model_account_customer->deleteLoginAttempts($customer_id);
            }
        }
        $this->logInfo("Validation Error:", $this->error);
        if (array_get($this->error, 'warning')) {
            $this->session->data['error'] = $this->error['warning'];
        }
        return !$this->error;
    }

    protected function completeLogin($customer_id, $email, $authData)
    {
        unset($this->session->data['guest']);

        // Add to activity log
        $this->load->model('account/activity');

        $activity_data = array(
            'customer_id' => $this->customer->getId(),
            'name' => get_name($this->customer->getFirstName(), $this->customer->getLastName())
        );
        $this->model_account_activity->addActivity('login', $activity_data);
        $this->model_account_customer->getModel($this->provider)->updateAuthentication($authData);

        if (isset($this->session->data["{$this->getLoginKey()}"]['seamless'])) {
            unset($this->session->data["{$this->getLoginKey()}"]['seamless']);
        }

        $this->logInfo('Customer logged in - ID: ' . $customer_id . ', Email: ' . $email);
        $this->jsRedirect();
    }
}