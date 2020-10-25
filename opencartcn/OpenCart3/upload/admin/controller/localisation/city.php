<?php

/**
 * description
 *
 * @copyright        2017/11/29 opencart.cn - All Rights Reserved
 * @link             http://www.guangdawangluo.com
 * @author           Eric Yang <yangyw@opencart.cn>
 * @created          2017/11/29 14:12
 * @modified         2017/11/29 14:12
 */
class ControllerLocalisationCity extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('localisation/city');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/city');

        $this->getList();
    }

    public function add()
    {
        $this->load->language('localisation/city');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/city');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $data = $this->request->post;

            if ($data['type'] == 'city') {
                $data['parent_id'] = 0;
            }

            $this->model_localisation_city->addCity($data);

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

            $this->response->redirect($this->url->link('localisation/city', 'user_token=' . $this->session->data['user_token'] . $url));
        }

        $this->getForm();
    }

    public function edit()
    {
        $this->load->language('localisation/city');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/city');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $data = $this->request->post;

            if ($data['type'] == 'city') {
                $data['parent_id'] = 0;
            }

            $this->model_localisation_city->editCity($this->request->get['city_id'], $data);

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

            $this->response->redirect($this->url->link('localisation/city', 'user_token=' . $this->session->data['user_token'] . $url));
        }

        $this->getForm();
    }

    public function delete()
    {
        $this->load->language('localisation/city');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/city');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $city_id) {
                $this->model_localisation_city->deleteCity($city_id);
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

            $this->response->redirect($this->url->link('localisation/city', 'user_token=' . $this->session->data['user_token'] . $url));
        }

        $this->getList();
    }

    public function getList()
    {

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'zone';
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

        if (isset($this->request->get['filter_name'])) {
            $data['filter_name'] = $this->request->get['filter_name'];
        } else {
            $data['filter_name'] = '';
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

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('localisation/city', 'user_token=' . $this->session->data['user_token'] . $url),
        );

        $data['add'] = $this->url->link('localisation/city/add', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['delete'] = $this->url->link('localisation/city/delete', 'user_token=' . $this->session->data['user_token'] . $url);

        $data['cities'] = array();

        $filter_data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin'),
            'filter_name' => $data['filter_name']
        );

        $city_total = $this->model_localisation_city->getTotalCities($filter_data);

        $results = $this->model_localisation_city->getCities($filter_data);

        foreach ($results as $result) {
            $data['cities'][] = array(
                'city_id' => $result['city_id'],
                'name' => $result['name'],
                'zone' => $result['zone'],
                'status' => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'selected' => isset($this->request->post['selected']) && in_array($result['city_id'], $this->request->post['selected']),
                'edit' => $this->url->link('localisation/city/edit', 'user_token=' . $this->session->data['user_token'] . '&city_id=' . $result['city_id'] . $url),
            );
        }

        $data['user_token'] = $this->session->data['user_token'];

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

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }

        $data['sort_name'] = $this->url->link('localisation/city', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);
        $data['sort_zone'] = $this->url->link('localisation/city', 'user_token=' . $this->session->data['user_token'] . '&sort=zone' . $url);


        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }

        $pagination = new Pagination();
        $pagination->total = $city_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('localisation/city', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($city_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($city_total - $this->config->get('config_limit_admin'))) ? $city_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $city_total, ceil($city_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('localisation/city_list', $data));
    }

    public function getForm()
    {
        $data['text_form'] = !isset($this->request->get['city_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['country'])) {
            $data['error_country'] = $this->error['country'];
        } else {
            $data['error_country'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['zone'])) {
            $data['error_zone'] = $this->error['zone'];
        } else {
            $data['error_zone'] = '';
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
            'href' => $this->url->link('localisation/city', 'user_token=' . $this->session->data['user_token'] . $url),
        );

        if (!isset($this->request->get['city_id'])) {
            $data['action'] = $this->url->link('localisation/city/add', 'user_token=' . $this->session->data['user_token'] . $url);
        } else {
            $data['action'] = $this->url->link('localisation/city/edit', 'user_token=' . $this->session->data['user_token'] . '&city_id=' . $this->request->get['city_id'] . $url);
        }

        $data['cancel'] = $this->url->link('localisation/city', 'user_token=' . $this->session->data['user_token'] . $url);

        if (isset($this->request->get['city_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $city_info = $this->model_localisation_city->getCity($this->request->get['city_id']);
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($city_info)) {
            $data['name'] = $city_info['name'];
        } else {
            $data['name'] = '';
        }

        $this->load->model('localisation/zone');

        $data['zones'] = array();

        if (isset($this->request->post['zone_id'])) {
            $data['zone_id'] = $this->request->post['zone_id'];
        } elseif (!empty($city_info)) {
            $data['zone_id'] = $city_info['zone_id'];
        } else {
            $data['zone_id'] = 0;
        }

        $data['parent_data'] = array();

        if ($data['zone_id']) {
            $data['parent_data'] = $this->model_localisation_city->getCitiesByZoneId($data['zone_id']);
        }

        $data['parent_id'] = '';

        if (isset($this->request->post['parent_id'])) {
            $data['parent_id'] = $this->request->post['parent_id'];
        } elseif (isset($city_info) && $city_info) {
            $data['parent_id'] = $city_info['up_id'];
        }

        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();

        if (isset($this->request->post['country_id'])) {
            $data['country_id'] = $this->request->post['country_id'];
        } elseif (!empty($city_info)) {
            $zone = $this->model_localisation_zone->getZone($city_info['zone_id']);
            $data['country_id'] = $zone['country_id'];
        } else {
            $data['country_id'] = 0;
        }

        if ($data['country_id']) {
            $data['zones'] = $this->model_localisation_zone->getZonesByCountryId($data['country_id']);
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (isset($city_info)) {
            $data['status'] = $city_info['status'];
        } else {
            $data['status'] = '1';
        }

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('localisation/city_form', $data));
    }

    public function city()
    {
        $json = array();
        $this->load->model('localisation/city');
        $city_info = $this->model_localisation_city->getCitiesByZoneId($this->request->get['zone_id']);
        if ($city_info) {
            $json = array(
                'code' => 1,
                'city_data' => $city_info
            );
        } else {
            $json = array(
                'code' => 0,
                'city_data' => array()
            );
        }
        $this->response->setOutput(json_encode($json));
    }


    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'localisation/city')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 2) || (utf8_strlen($this->request->post['name']) > 128)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (!$this->request->post['zone_id']) {
            $this->error['zone'] = $this->language->get('error_zone');
        }

        if (!$this->request->post['country_id']) {
            $this->error['country'] = $this->language->get('error_country');
        }

        return !$this->error;
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'localisation/city')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}