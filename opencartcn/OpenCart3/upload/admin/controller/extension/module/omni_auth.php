<?php
/**
 * omni_auth.php
 *
 * @copyright 2020 opencart.cn - All Rights Reserved
 * @link https://www.guangdawangluo.com
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2020-06-2020/6/29 13:44
 * @modified 2020-06-2020/6/29 13:44
 */

class ControllerExtensionModuleOmniAuth extends Controller
{
    private $error = [];
    private $data = [];
    private $providers = [];
    private $socialProviders = [];

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->language('extension/module/omni_auth');
        $this->load->model('setting/setting');
        $socialClasses = Social::getProviderList();
        if (is_ft()) {
            $socialProviders = Social::getSocialProviders();
        } else {
            $socialProviders = Social::getCnSocialProviders();
        }

        $this->socialProviders = $socialProviders;

        foreach ($socialClasses as $socialClass) {
            if (!in_array(strtolower($socialClass), $socialProviders)) {
                continue;
            }
            $this->providers[$socialClass] = $this->language->get($socialClass);
        }
    }

    public function index()
    {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (isset($this->request->post['module_omni_auth_items'])) {
                $sort_order = array();
                foreach ($this->request->post['module_omni_auth_items'] as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }
                array_multisort($sort_order, SORT_ASC, $this->request->post['module_omni_auth_items']);
            }

            $this->model_setting_setting->editSetting('module_omni_auth', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module'));
        }

        $this->data['heading_title'] = strip_tags($this->language->get('heading_title'));

        // Process Errors
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        // Generate Breadcrumbs
        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/omni_auth', 'user_token=' . $this->session->data['user_token']),
            'separator' => ' :: '
        );

        // Set Page Title
        $this->document->setTitle($this->language->get('heading_title'));
        $this->data['providers'] = $this->providers;

        // Basic Variables
        $this->data['action'] = $this->url->link('extension/module/omni_auth', 'user_token=' . $this->session->data['user_token']);
        $this->data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

        // Process Variables
        if (isset($this->request->post['module_omni_auth_items'])) {
            $module_omni_auth_items = $this->request->post['module_omni_auth_items'];
        } elseif ($this->config->get('module_omni_auth_items')) {
            $module_omni_auth_items = $this->config->get('module_omni_auth_items');
        } else {
            $module_omni_auth_items = array();
        }

        foreach ($module_omni_auth_items as $module_omni_auth_item) {
            $item_provider = $module_omni_auth_item['provider'];
            if (!in_array($item_provider, $this->socialProviders)) {
                continue;
            }
            $this->data['module_omni_auth_items'][] = $module_omni_auth_item;
        }


        foreach ($this->data['module_omni_auth_items'] as $key => $item) {

            $this->data['module_omni_auth_items'][$key]['callback'] = base_url() . 'callback/' . $item['provider'];
        }

        if (isset($this->request->post['module_omni_auth_debug'])) {
            $this->data['module_omni_auth_debug'] = $this->request->post['module_omni_auth_debug'];
        } elseif ($this->config->get('module_omni_auth_debug')) {
            $this->data['module_omni_auth_debug'] = $this->config->get('module_omni_auth_debug');
        } else {
            $this->data['module_omni_auth_debug'] = 0;
        }

        if (isset($this->request->post['module_omni_auth_status'])) {
            $this->data['module_omni_auth_status'] = $this->request->post['module_omni_auth_status'];
        } elseif ($this->config->get('module_omni_auth_status')) {
            $this->data['module_omni_auth_status'] = $this->config->get('module_omni_auth_status');
        } else {
            $this->data['module_omni_auth_status'] = 0;
        }

        if (isset($this->request->post['module_omni_auth_bind'])) {
            $this->data['module_omni_auth_bind'] = $this->request->post['module_omni_auth_bind'];
        } elseif ($this->config->get('module_omni_auth_bind')) {
            $this->data['module_omni_auth_bind'] = $this->config->get('module_omni_auth_bind');
        } else {
            $this->data['module_omni_auth_bind'] = 0;
        }


        // Load Template
        $this->data['header'] = $this->load->controller('common/header');
        $this->data['column_left'] = $this->load->controller('common/column_left');
        $this->data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/omni_auth', $this->data));
    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/account')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    public function install()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "customer_authentication` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `customer_id` int(11) NOT NULL,
                `uid` varchar(255) NOT NULL DEFAULT '',
                `unionid` varchar(50) NOT NULL DEFAULT '',
                `provider` varchar(20) NOT NULL DEFAULT '',
                `access_token` varchar(255) DEFAULT '',
                `token_secret` varchar(255) DEFAULT '',
                `avatar` varchar(255) DEFAULT '',
                `date_added` datetime DEFAULT NULL,
                `date_modified` datetime DEFAULT NULL,
                PRIMARY KEY (`id`,`customer_id`),
                UNIQUE KEY `id_UNIQUE` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function uninstall()
    {
        $this->db->query('DROP TABLE IF EXISTS ' . DB_PREFIX . 'customer_authentication;');
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('module_omni_auth');
    }
}