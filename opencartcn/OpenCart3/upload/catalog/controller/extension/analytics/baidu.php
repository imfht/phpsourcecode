<?php
class ControllerExtensionAnalyticsBaidu extends Controller {
    public function index() {
		return html_entity_decode($this->config->get('analytics_baidu_code'), ENT_QUOTES, 'UTF-8');
	}
}
