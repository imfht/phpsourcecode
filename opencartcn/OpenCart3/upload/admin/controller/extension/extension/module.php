<?php
class ControllerExtensionExtensionModule extends Controller {
	const FEATURE_PRO = array(
		'module' => array(
				array('name' => '拼团', 'description' => '参考拼多多。可通过朋友圈分享邀请，成团后按照拼团价格，不成团超时系统自动取消并退款', 'href' => 'https://mall.opencart.cn/'),
				array('name' => '砍价', 'description' => '每砍一次，可优惠相应金额；优惠金额随机', 'href' => 'https://mall.opencart.cn/'),
				array('name' => '秒杀', 'description' => '增加秒杀功能模块，显示距离结束还有XX天XX小时XX秒，可按时间段设置商品', 'href' => 'https://mall.opencart.cn/'),
				array('name' => '凑单', 'description' => '提示消费增加购买相关商品，会减少 x 费用', 'href' => 'https://mall.opencart.cn/'),
				array('name' => '满件减额', 'description' => '将现有 购买x件优惠y元，前端展示', 'href' => 'https://mall.opencart.cn/'),
				array('name' => '降价提醒', 'description' => '商品售价下降时邮件/短信提醒', 'href' => 'https://mall.opencart.cn/'),
				array('name' => '套装商品组合', 'description' => '将多个商品组合成一个套装购买，可设置金额折扣', 'href' => 'https://mall.opencart.cn/'),
				array('name' => '添加订单号Order Number', 'description' => '用于外部API对接, 譬如 2019021500001', 'href' => 'https://mall.opencart.cn/admin/index.php?route=sale/order'),
				array('name' => '商品采集', 'description' => '第三方平台数据如京东,天猫,淘宝,Ebay,AliExpress,1688, 苏宁易购, 唯品会，简单操作采集', 'href' => 'https://mall.opencart.cn/admin/index.php?route=catalog/collector'),
				array('name' => '第三方登录注册绑定老用户', 'description' => '后台增加开关是否强制绑定', 'href' => 'https://mall.opencart.cn/account/login'),
				array('name' => '产品名称描述自动翻译', 'description' => '一键式翻译, 采用 google translate', 'href' => 'https://mall.opencart.cn/admin/index.php?route=catalog/product/edit&product_id=42'),
				array('name' => '产品分类页高级筛选', 'description' => '后台可以指定筛选条件(品牌, 属性, 选项, 类型)', 'href' => 'https://mall.opencart.cn/index.php?route=product/category&path=59'),
		 ),
		'shipping' => array(
				array('name' => 'FlexShipping 功能优化提升,可以参考 XShippingPro', 'description' => '', 'href' => 'https://mall.opencart.cn/admin/index.php?route=extension/shipping/flex'),
		 ),
		'payment' => array(
				array('name' => '虚拟货币支付', 'description' => '添加 BTC, ETH, XRP, 本阶段只添加 XRP，集成了支付平台 coinpayments', 'href' => 'https://mall.opencart.cn/admin/index.php?route=extension/payment/coinpayments'),
		 )
	);

	private $error = array();

	public function index() {
		$this->load->language('extension/extension/module');

		$this->load->model('setting/extension');

		$this->load->model('setting/module');

		$this->getList();
	}

	public function install() {
		$this->load->language('extension/extension/module');

		$this->load->model('setting/extension');

		$this->load->model('setting/module');

		if ($this->validate()) {
			$this->model_setting_extension->install('module', $this->request->get['extension']);

			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/' . $this->request->get['extension']);

			// Call install method if it exsits
			$this->load->controller('extension/module/' . $this->request->get['extension'] . '/install');

			$this->session->data['success'] = $this->language->get('text_success');
		} else {
			$this->session->data['error'] = $this->error['warning'];
		}

		$this->getList();
	}

	public function uninstall() {
		$this->load->language('extension/extension/module');

		$this->load->model('setting/extension');

		$this->load->model('setting/module');

		if ($this->validate()) {
			$this->model_setting_extension->uninstall('module', $this->request->get['extension']);

			$this->model_setting_module->deleteModulesByCode($this->request->get['extension']);

			// Call uninstall method if it exsits
			$this->load->controller('extension/module/' . $this->request->get['extension'] . '/uninstall');

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/extension/module');

		$this->load->model('setting/extension');

		$this->load->model('setting/module');

		if ($this->validate()) {
			$this->load->language('module' . '/' . $this->request->get['extension']);

			$this->model_setting_module->addModule($this->request->get['extension'], $this->language->get('heading_title'));

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->getList();
	}

	public function delete() {
		$this->load->language('extension/extension/module');

		$this->load->model('setting/extension');

		$this->load->model('setting/module');

		if (isset($this->request->get['module_id']) && $this->validate()) {
			$this->model_setting_module->deleteModule($this->request->get['module_id']);

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->getList();
	}

	protected function getList() {
		$data['text_layout'] = sprintf($this->language->get('text_layout'), $this->url->link('design/layout', 'user_token=' . $this->session->data['user_token']));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$extensions = $this->model_setting_extension->getInstalled('module');

		foreach ($extensions as $key => $value) {
			if (!is_file(DIR_APPLICATION . 'controller/extension/module/' . $value . '.php') && !is_file(DIR_APPLICATION . 'controller/module/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('module', $value);

				unset($extensions[$key]);

				$this->model_setting_module->deleteModulesByCode($value);
			}
		}

		$data['extensions'] = array();

		$data['feature_pro'] = self::FEATURE_PRO;

		// Create a new language container so we don't pollute the current one
		$language = new Language($this->config->get('config_language'));

		// Compatibility code for old extension folders
		$files = glob(DIR_APPLICATION . 'controller/extension/module/*.php');

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				$this->load->language('extension/module/' . $extension, 'extension');

				$module_data = array();

				$modules = $this->model_setting_module->getModulesByCode($extension);

				foreach ($modules as $module) {
					if ($module['setting']) {
						$setting_info = json_decode($module['setting'], true);
					} else {
						$setting_info = array();
					}

					$module_data[] = array(
						'module_id' => $module['module_id'],
						'name'      => $module['name'],
						'status'    => (isset($setting_info['status']) && $setting_info['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
						'edit'      => $this->url->link('extension/module/' . $extension, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id']),
						'delete'    => $this->url->link('extension/extension/module/delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'])
					);
				}

				$data['extensions'][] = array(
					'name'      => $this->language->get('extension')->get('heading_title'),
					'status'    => $this->config->get('module_' . $extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
					'module'    => $module_data,
					'install'   => $this->url->link('extension/extension/module/install', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension),
					'uninstall' => $this->url->link('extension/extension/module/uninstall', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension),
					'installed' => in_array($extension, $extensions),
					'edit'      => $this->url->link('extension/module/' . $extension, 'user_token=' . $this->session->data['user_token'])
				);
			}
		}

		$sort_order = array();

		foreach ($data['extensions'] as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $data['extensions']);

		$this->response->setOutput($this->load->view('extension/extension/module', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/extension/module')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function feature_pro_msg() {
		$data = self::FEATURE_PRO;
		$this->json_output($data);
	}
}
