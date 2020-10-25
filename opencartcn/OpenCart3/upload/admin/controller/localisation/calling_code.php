<?php
/**
 * calling_code.php
 *
 * @copyright 2020 opencart.cn - All Rights Reserved
 * @link https://www.guangdawangluo.com
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2020-06-2020/6/12 10:42
 * @modified 2020-06-2020/6/12 10:42
 */

class ControllerLocalisationCallingCode extends Controller
{
    private $error = [];

    public function index()
    {
        $this->load->language('localisation/calling_code');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('localisation/calling_code');

        $this->getList();
    }

    public function add() {
        $this->load->language('localisation/calling_code');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/calling_code');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_localisation_calling_code->addCallingCode($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('localisation/calling_code', 'user_token=' . $this->session->data['user_token'] . $url));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('localisation/calling_code');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/calling_code');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_localisation_calling_code->editCallingCode($this->request->get['calling_code_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('localisation/calling_code', 'user_token=' . $this->session->data['user_token'] . $url));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('localisation/calling_code');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/calling_code');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $calling_code_id) {
                $this->model_localisation_calling_code->deleteCallingCode($calling_code_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('localisation/calling_code', 'user_token=' . $this->session->data['user_token'] . $url));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('localisation/calling_code', 'user_token=' . $this->session->data['user_token'] . $url)
        );

        $data['add'] = $this->url->link('localisation/calling_code/add', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['delete'] = $this->url->link('localisation/calling_code/delete', 'user_token=' . $this->session->data['user_token'] . $url);

        $data['calling_codes'] = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $calling_code_total = $this->model_localisation_calling_code->getTotalCallingCodes();

        $results = $this->model_localisation_calling_code->getCallingCodes($filter_data);

        foreach ($results as $result) {
            $data['calling_codes'][] = array(
                'calling_code_id' => $result['calling_code_id'],
                'name'       => $result['name'] . (($result['code'] == $this->config->get('config_calling_code')) ? $this->language->get('text_default') : null),
                'code'       => $result['code'],
                'sort_order'       => $result['sort_order'],
                'edit'       => $this->url->link('localisation/calling_code/edit', 'user_token=' . $this->session->data['user_token'] . '&calling_code_id=' . $result['calling_code_id'] . $url)
            );
        }

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

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('localisation/calling_code', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);
        $data['sort_code'] = $this->url->link('localisation/calling_code', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url);
        $data['sort_sort_order'] = $this->url->link('localisation/calling_code', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $calling_code_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('localisation/calling_code', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($calling_code_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($calling_code_total - $this->config->get('config_limit_admin'))) ? $calling_code_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $calling_code_total, ceil($calling_code_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('localisation/calling_code_list', $data));
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['calling_code_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('localisation/calling_code', 'user_token=' . $this->session->data['user_token'] . $url)
        );

        if (!isset($this->request->get['calling_code_id'])) {
            $data['action'] = $this->url->link('localisation/calling_code/add', 'user_token=' . $this->session->data['user_token'] . $url);
        } else {
            $data['action'] = $this->url->link('localisation/calling_code/edit', 'user_token=' . $this->session->data['user_token'] . '&calling_code_id=' . $this->request->get['calling_code_id'] . $url);
        }

        $data['cancel'] = $this->url->link('localisation/calling_code', 'user_token=' . $this->session->data['user_token'] . $url);

        if (isset($this->request->get['calling_code_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $calling_code_info = $this->model_localisation_calling_code->getCallingCode($this->request->get['calling_code_id']);
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($calling_code_info)) {
            $data['name'] = $calling_code_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['code'])) {
            $data['code'] = $this->request->post['code'];
        } elseif (!empty($calling_code_info)) {
            $data['code'] = $calling_code_info['code'];
        } else {
            $data['code'] = '';
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($calling_code_info)) {
            $data['sort_order'] = $calling_code_info['sort_order'];
        } else {
            $data['sort_order'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($calling_code_info)) {
            $data['status'] = $calling_code_info['status'];
        } else {
            $data['status'] = '1';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('localisation/calling_code_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'localisation/calling_code')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 128)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'localisation/calling_code')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('localisation/calling_code');
        foreach ($this->request->post['selected'] as $calling_code_id) {
            $calling_code_info = $this->model_localisation_calling_code->getCallingCode($calling_code_id);
            if ($this->config->get('config_calling_code') == $calling_code_info['code']) {
                $this->error['warning'] = t('error_default');
            }
        }

        return !$this->error;
    }
}