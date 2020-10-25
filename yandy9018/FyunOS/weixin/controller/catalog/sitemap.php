<?php  
class ControllerCatalogSitemap extends Controller {
	public function index() {
		$this->load_language('catalog/sitemap');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/sitemap', 'token=' . $this->session->data['token'] , 'SSL'),
      		'separator' => $this->language->get('text_breadcrumb_separator')
   		);
	
   		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=catalog/sitemap/creat&token=' . $this->session->data['token'];
		
		
		$this->template = 'catalog/sitemap.tpl';
		$this->id = 'content';
		$this->layout = 'layout/default';
		$this->render();
	}
	
	
	public function creat() {
		$urls=array();
		
		$this->load->model('tool/seo_url');
		$this->load->model('catalog/sitemap');
	
		foreach ($this->model_catalog_sitemap->getInformations() as $result) {
		
      	$urls[]=array(
        	'url'      =>$this->model_tool_seo_url->rewrite($this->url('information/information&information_id=' . $result['information_id'])), 
        	'name'      => $result['title']
			);
		};
	
		$results = $this->model_catalog_sitemap->getCategorieForSitemap(0);

		$current_path = '';
		foreach ($results as $result) {	
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}
			$urls[]=array(
        	'url'      =>$this->model_tool_seo_url->rewrite($this->url('product/category&path=' . $new_path)), 
        	'name'      => $result['name']
			);
		}
	
		$results1 = $this->model_catalog_sitemap->getAllProducts();
	
		foreach ($results1 as $result) {
			$urls[]=array(
        	'url'      =>$this->model_tool_seo_url->rewrite($this->url('product/product&product_id=' . $result['product_id'])), 
        	'name'      => $result['model']
			);
		}
		
		$sitemap =new Sitemaps();
		$sitemap->createSitemap ($urls, $this->request->post['priority'], $this->request->post['freq'] );
		$this->load_language('catalog/sitemap');
		$this->session->data['success'] = $this->language->get('text_success');
		$this->index();
	}
	
	public function url($route) {
		return HTTP_CATALOG . 'index.php?route=' . str_replace('&', '&amp;', $route);
  	}
}
?>