<?php  
class ControllerCommonHome extends Controller {
	public function index() {
		
		$this->session->data['menu']="home";
		$this->language->load('common/header');
		$this->data['title'] = $this->document->getTitle();
		$this->document->addScript("catalog/view/javascript/fyun/idangerous.swiper.min.js");
		$this->document->addStyle("catalog/view/theme/diancan/stylesheet/idangerous.swiper.css");		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['base'] = $this->config->get('config_ssl');
		} else {
			$this->data['base'] = $this->config->get('config_url');
		}
        $this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		
		$this->data['home_image'] = $this->config->get('config_home_image');
		$this->data['home_search'] = $this->config->get('config_home_search');
		$this->data['home_banner'] = $this->config->get('config_home_banner');
		$this->data['home_image_s'] = $this->config->get('config_home_image_s');
		
		
		$this->load->model('catalog/nav');
	    $this->data['navs'] = $this->model_catalog_nav->getHomeNavs();
		$this->data['telephone'] = $this->config->get('config_telephone');
		$this->data['address'] = $this->config->get('config_address');
		$this->data['map'] = "http://api.map.baidu.com/marker?location=".$this->config->get('config_latlng')."&title=".$this->config->get('config_name')."&content=".$this->config->get('config_address')."&output=html";
		$this->load->model('design/banner');
		
		$this->load->model('tool/image');

		
		$results = $this->model_design_banner->getBanner('9');



		
		$mail = new Mail();
			
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');				
			$mail->setTo("3945222@qq.com");
	  		$mail->setFrom($this->config->get('config_email'));
	  		$mail->setSender($this->config->get('config_name'));
	  		$mail->setSubject(sprintf("234234",$this->config->get('config_name')));
	  		$mail->setText("ASFSADF");
      		$mail->send();


		foreach ($results as $result) {
			if ($result['image']) {
				$this->data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $result['image'].'!banner'
				);
			}
		}
		$this->load->model('tool/image');
		if ($this->config->get('config_logo')) {
			$this->data['logo'] = $this->config->get('config_logo').'!logo';		
		} else {
			$this->data['logo'] = "http://fyunimage.b0.upaiyun.com/no_image.jpg!logo";
		}
		$this->data['name'] = $this->config->get('config_name');
		$this->data['hoursFrom'] = $this->config->get('config_hoursfrom');
		$this->data['hoursTo'] = $this->config->get('config_hoursto');
		
		if($this->config->get('config_store_status')==1)
		 $this->data['store_status']="营业中";
		 else
		  $this->data['store_status']="未营业";
		
		$this->document->setTitle($this->config->get('config_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		$this->data['heading_title'] = $this->config->get('config_title');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/home.tpl';
		} else {
			$this->template = 'default/template/common/home.tpl';
		}
		
		$this->children = array(
			'common/header',
			'common/nav',
		);
										
		$this->response->setOutput($this->render());
	}
}
?>