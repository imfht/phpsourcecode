<?php

class ControllerAccountBind extends Controller
{
    const HIDDEN_NAME = true;

    public function index()
    {
        if ($this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/account'));
        }

        $socialData = array_get($this->session->data, 'social_data');
        if (empty($socialData)) {
            $this->response->redirect($this->url->link('account/login'));
        }

        $this->load->language('account/bind');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_bind'),
            'href' => $this->url->link('account/bind')
        );

        if (isset($this->request->post['type'])) {
            $data['type'] = $this->request->post['type'];
        } else {
            $data['type'] = 'email';
        }

        $data['calling_code'] = array_get($this->request->post, 'calling_code', config('config_calling_code'));

        // Captcha
        if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
            $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
        } else {
            $data['captcha'] = '';
        }

        $data['redirect']  = array_get($this->session->data, 'redirect');
        unset($this->session->data['redirect']);

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $data['login_url'] = $this->url->link('account/login');
        $data['register_url'] = $this->url->link('account/register');
        $data['hidden_name'] = self::HIDDEN_NAME;

        $this->response->setOutput($this->load->view('account/bind', $data));
    }
}
