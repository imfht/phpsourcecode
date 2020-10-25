<?php

/**
 * @copyright        2019 opencart.cn - All Rights Reserved
 * @link             http://www.guangdawangluo.com
 * @author:          Sam Chen <sam.chen@opencart.cn>
 * @created:         2017-07-31 11:48:04
 * @modified by:     Sam Chen <sam.chen@opencart.cn>
 * @modified:        2020-01-10 14:25:51
 */

class ControllerCheckoutCheckout extends Controller
{
    protected $ADDRESS_FIELDS = array(
        'firstname',
        'lastname',
        'email',
        'calling_code',
        'telephone',
        'company',
        'address_1',
        'address_2',
        'city',
        'postcode',
        'country_id',
        'zone_id',
        'city_id',
        'county_id',
        'custom_field',
    );

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->language('checkout/cart');
        $this->load->language('checkout/coupon');
        $this->load->language('checkout/checkout');
        $this->load->model('account/activity');
        $this->load->model('account/custom_field');
        $this->load->model('tool/upload');
        $this->load->model('account/address');
        $this->load->model('account/customer');
        $this->load->model('account/customer_group');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $this->load->model('checkout/checkout');
        $this->load->model('checkout/order');
    }

    public function index()
    {
        if (!$this->isValidCart()) {
            $this->log('Cart invalid');
            $this->session->data['error'] = t('error_cart_invalid');
            $this->response->redirect($this->url->link('checkout/cart'));
            return;
        }

        // Redirect if guest checkout disabled
        if (!$this->customer->isLogged() && !$this->isGuestCheckoutEnabled()) {
            $this->session->data['redirect'] = $this->url->link('checkout/cart');
            $this->session->data['error'] = t('warning_login');
            $this->response->redirect($this->url->link('account/login'));
            return;
        }

        // Shipping address
        $this->initAddressSession('shipping');

        // Payment address
        $this->initAddressSession('payment');

        // Init pickup
        if ($this->cart->hasShipping(0)) {
            unset($this->session->data['pickup_id']);
        }

        if ($this->cart->hasShipping()) {
            $this->log($this->session->data['shipping_address']);
        }
        $this->log($this->session->data['payment_address']);

        $this->load->language('checkout/checkout');
        $this->document->setTitle(t('heading_title'));
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

        $breadcrumbs = new Breadcrumb();
        $breadcrumbs->add(t('text_home'), $this->url->link('common/home'));
        $breadcrumbs->add(t('text_cart'), $this->url->link('checkout/cart'));
        $breadcrumbs->add(t('heading_title'), $this->url->link('checkout/checkout'));
        $data['breadcrumbs'] = $breadcrumbs->all();

        if (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error_warning'] = '';
        }

        $data['logged'] = $this->customer->isLogged();
        $data['shipping_required'] = $this->cart->hasShipping();
        $data['payment_address_required'] = $this->isPaymentAddressRequired();

        if ($this->cart->hasShipping()) {
            $data['shipping_address_section'] = $this->renderAddressSection('shipping');
        }

        if ($this->isPaymentAddressRequired()) {
            $data['payment_address_section'] = $this->renderAddressSection('payment');
        }

        if (!$this->customer->isLogged()) {
            $data['guest_address_section'] = $this->renderGuestAddressSection();
        }

        $data['shipping_method_section'] = $this->renderShippingMethodSection();

        $data['payment_method_section'] = $this->renderPaymentMethodSection();

        $data['cart_section'] = $this->renderCartSection();
        $data['comment_section'] = $this->renderCommentSection();
        $data['agree_section'] = $this->renderAgreeSection();

        $data['href'] = [
            'connect' => $this->url->link('checkout/checkout/connect'),
        ];

        if ($this->customer->isLogged()) {
            $this->load->model('account/address');
            $data['total_addresses'] = (int)$this->model_account_address->getTotalAddresses();
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('checkout/checkout/checkout', $data));
    }

    /**
     * Update checkout
     * @throws Exception
     */
    public function update()
    {
        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $this->response->redirect($this->url->link('checkout/cart'));
        }

        $this->log(__FUNCTION__);
        $this->log($this->request->post);

        $redirect = '';
        $error = array();

        if (!$this->customer->isLogged() && !$this->isGuestCheckoutEnabled()) {
            $redirect = $this->url->link('account/login');
            $this->printJson($error, $redirect);
            return;
        }

        if (!$this->isValidCart()) {
            $redirect = $this->url->link('checkout/cart');
            $this->printJson($error, $redirect);
            return;
        }

        // Reload
        if (isset($this->request->post['reload'])) {
            $this->printJson($error, $redirect, $this->reload());
            return;
        }

        // Guest shipping address
        if (isset($this->request->post['guest_shipping_address'])) {
            $data = $this->request->post['guest_shipping_address'];

            $countryId = (int)array_get($data, 'country_id', config('config_country_id'));
            $zoneId = (int)array_get($data, 'zone_id', config('config_zone_id'));
            $cityId = (int)array_get($data, 'city_id');
            $countyId = (int)array_get($data, 'county_id');

            $this->session->data['shipping_address']['country_id'] = $countryId;
            $this->session->data['shipping_address']['zone_id'] = $zoneId;
            $this->session->data['shipping_address']['city_id'] = $cityId;
            $this->session->data['shipping_address']['county_id'] = $countyId;

            if (!$this->isPaymentAddressRequired()) {
                $this->session->data['payment_address']['country_id'] = $countryId;
                $this->session->data['payment_address']['zone_id'] = $zoneId;
                $this->session->data['payment_address']['city_id'] = $cityId;
                $this->session->data['payment_address']['county_id'] = $countyId;
            }

            $this->printJson($error, $redirect, $this->reload());
            return;
        }

        if (isset($this->request->post['guest_payment_address'])) {
            $data = $this->request->post['guest_payment_address'];

            $countryId = (int)array_get($data, 'country_id', config('config_country_id'));
            $zoneId = (int)array_get($data, 'zone_id', config('config_zone_id'));
            $cityId = (int)array_get($data, 'zone_id');
            $countyId = (int)array_get($data, 'county_id');

            $this->session->data['payment_address']['country_id'] = $countryId;
            $this->session->data['payment_address']['zone_id'] = $zoneId;
            $this->session->data['payment_address']['city_id'] = $cityId;
            $this->session->data['payment_address']['county_id'] = $countyId;

            $this->printJson($error, $redirect, $this->reload());
            return;
        }

        // Shipping address id
        if ($addressId = array_get($this->request->post, 'shipping_address_id')) {
            if (!$this->cart->hasShipping()) {
                unset($this->session->data['shipping_address']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['shipping_method']);
            } else {
                $address = $this->model_account_address->getAddress($addressId);
                $this->syncAddressSession('shipping', $address);

                if (! $this->isPaymentAddressRequired()) {
                    $this->syncAddressSession('payment', $address);
                }

                $code = array_get($this->session->data, 'shipping_method.code');
                if (!$this->model_checkout_checkout->setShippingMethod($code)) {
                    $this->model_checkout_checkout->setShippingMethod();
                }

                if (! $this->isPaymentAddressRequired()) {
                    $code = array_get($this->session->data, 'payment_method.code');
                    if (!$this->model_checkout_checkout->setPaymentMethod($code)) {
                        $this->model_checkout_checkout->setPaymentMethod();
                    }
                }
            }

            $this->printJson($error, $redirect, $this->reload());
            return;
        }

        // Payment address id
        if ($addressId = array_get($this->request->post, 'payment_address_id')) {
            $address = $this->model_account_address->getAddress($addressId);
            $this->syncAddressSession('payment', $address);

            $code = array_get($this->session->data, 'payment_method.code');
            if (!$this->model_checkout_checkout->setPaymentMethod($code)) {
                $this->model_checkout_checkout->setPaymentMethod();
            }

            $this->printJson($error, $redirect, $this->reload());
            return;
        }

        // Payment method
        if ($code = array_get($this->request->post, 'payment_method')) {
            if (!array_get($this->session->data, 'payment_address')) {
                $redirect = $this->url->link('checkout/cart');
                $this->printJson($error, $redirect);
                return;
            }

            if (!$this->model_checkout_checkout->setPaymentMethod($code)) {
                $this->session->data['error'] = t('error_payment_unavailable');
                $redirect = $this->url->link('checkout/checkout');
            }

            $this->printJson($error, $redirect, $this->reload());
            return;
        }

        // Shipping method
        if ($code = array_get($this->request->post, 'shipping_method')) {
            if (!array_get($this->session->data, 'shipping_address')) {
                $redirect = $this->url->link('checkout/cart');
                $this->printJson($error, $redirect);
                return;
            }

            if (!$this->model_checkout_checkout->setShippingMethod($code)) {
                $this->session->data['error'] = t('error_shipping_unavailable');
                $redirect = $this->url->link('checkout/checkout');
            }

            $this->printJson($error, $redirect, $this->reload());
            return;
        }

        // Total
        $totalType = (string)array_get($this->request->post, 'total_type');
        if ($totalType) {
            switch ($totalType) {
                case 'coupon':
                    $code = (string)array_get($this->request->post, 'value');
                    if (trim($code)) {
                        $code = trim($code);
                        $this->load->model('extension/total/coupon');
                        $coupon = $this->model_extension_total_coupon->getCoupon($code);
                        if (! $coupon) {
                            $error['warning'] = t('error_coupon_unavailable');
                            $this->printJson($error, $redirect);
                            return;
                        }

                        $this->session->data['coupon'] = $code;
                        $this->printJson($error, $redirect, $this->reload());
                        return;
                    }
                    break;
                case 'reward':
                    $reward = (int)array_get($this->request->post, 'value');

                    $points = $this->customer->getRewardPoints();

                    $points_total = 0;
                    foreach ($this->cart->getProducts() as $product) {
                        if ($product['points']) {
                            $points_total += $product['points'];
                        }
                    }

                    if ($reward > $points) {
                        $error['warning'] = sprintf(t('error_rewards'), $reward);
                        $this->printJson($error, $redirect);
                        return;
                    }

                    if ($reward > $points_total) {
                        $error['warning'] = sprintf(t('error_rewards_maximum'), $points_total);
                        $this->printJson($error, $redirect);
                        return;
                    }

                    $this->session->data['reward'] = abs($reward);
                    $this->printJson($error, $redirect, $this->reload());
                    break;
                case 'credit':
                    $this->session->data['credit'] = (bool)array_get($this->request->post, 'value');
                    $this->printJson($error, $redirect, $this->reload());
                    break;
            }
        }
    }

    /**
     * Validate and submit order
     * @throws Exception
     */
    public function confirm()
    {
        $this->log(__FUNCTION__);
        $redirect = '';
        $error = array();

        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $this->response->redirect($this->url->link('checkout/cart'));
            return;
        }

        if (!$this->customer->isLogged() && !$this->isGuestCheckoutEnabled()) {
            $redirect = $this->url->link('checkout/checkout/login');
            $this->printJson($error, $redirect);
            return;
        }

        if (!$this->isValidCart()) {
            $redirect = $this->url->link('checkout/cart');
            $this->printJson($error, $redirect);
            return;
        }

        $this->log($this->request->post);

        $data = array();

        $data['payment_address'] = array();
        $data['shipping_address'] = array();

        // Shipping address
        if ($this->cart->hasShipping()) {
            if ($this->customer->isLogged()) {
                $addressId = (int)array_get($this->request->post, 'shipping_address_id');
                if (! $addressId) {
                    $error['shipping_address'] = t('error_address');
                    $this->printJson($error, $redirect);
                    return;
                }

                $address = $this->model_account_address->getAddress($addressId);
                // If paypal express checkout, check city and postcode
                $payment_method = array_get($this->request->post, 'payment_method', '');
                if ($payment_method == 'pp_express' && (!$address['postcode'] || !$address['city'])) {
                    $error['paypal_express'] = t('error_address_paypal');
                }
                // Selected address not exists anymore
                if (!$address) {
                    $this->session->data['warning_error'] = t('error_address_not_exist');
                    $redirect = $this->url->link('checkout/checkout');
                    $this->printJson($error, $redirect);
                    return;
                }
            } else {
                // Guest checkout
                $address = [];
                foreach ($this->ADDRESS_FIELDS as $field) {
                    $address[$field] = array_get($this->request->post, 'shipping_address.' . $field);
                };
                if ($_error = $this->validateAddress($address)) {
                    $error['shipping_address'] = $_error;
                    $this->printJson($error, $redirect);
                    return;
                }
            }

            $data['shipping_address'] = $address;
            $this->syncAddressSession('shipping', $address);

            // 不需要 payment_address 时，将 shipping_address 复制给 payment_address
            if (!$this->isPaymentAddressRequired()) {
                $data['payment_address'] = $address;
                $this->syncAddressSession('payment', $address);
            }
        } else {
            // 不需要配送，清除 session
            unset($this->session->data['shipping_address']);
        }

        // Payment address
        if ($this->isPaymentAddressRequired()) {
            if ($this->customer->isLogged()) {
                $addressId = (int)array_get($this->request->post, 'payment_address_id');
                if (!$addressId) {
                    $error['payment_address'] = t('error_address');
                    $this->printJson($error, $redirect);
                    return;
                }

                $address = $this->model_account_address->getAddress($addressId);
                // Selected address not exists anymore
                if (!$address) {
                    $this->session->data['warning_error'] = t('error_address_not_exist');
                    $redirect = $this->url->link('checkout/checkout');
                    $this->printJson($error, $redirect);
                    return;
                }
            } else {
                // guest checkout，配送地址与账单地址是否相同？
                $guestShippingPaymentAddressSame = (int)array_get($this->request->post, 'guest_shipping_payment_address_same');
                if ($guestShippingPaymentAddressSame) {
                    // 相同时，直接复制 shipping_address
                    $address = $data['shipping_address'];
                } else {
                    $address = [];
                    foreach ($this->ADDRESS_FIELDS as $field) {
                        $address[$field] = array_get($this->request->post, 'payment_address.' . $field);
                    };
                    if ($_error = $this->validateAddress($address)) {
                        $error['payment_address'] = $_error;
                    }
                }
            }

            $data['payment_address'] = $address;
            $this->syncAddressSession('payment', $address);
        }

        // guest checkout：不需要 shipping address 及 payment address 时，只需要输入简单付款人联系信息，用于发送短信、邮件
        if (!$this->customer->isLogged() && !$this->cart->hasShipping() && !$this->isPaymentAddressRequired()) {
            if ($_error = $this->validateGuestBasicPaymentAddressFields()) {
                $error['payment_address'] = $_error;
                $this->printJson($error, $redirect);
                return;
            }
            foreach (['firstname', 'lastname', 'email', 'calling_code', 'telephone'] as $key) {
                $this->session->data['payment_address'][$key] = array_get($this->request->post, "payment_address.{$key}");
            }
        }

        // Payment method
        if (!array_get($this->request->post, 'payment_method')) {
            $error['payment_method']['warning'] = t('error_payment');
        } else {
            $code = array_get($this->request->post, 'payment_method');
            if (!$this->model_checkout_checkout->setPaymentMethod($code)) {
                $error['payment_method']['warning'] = t('error_payment_unavailable');
            } else {
                $data['payment_method'] = $code;
            }
        }

        // Shipping method
        if ($this->cart->hasShipping(0)) {
            if (empty($error['shipping_address'])) {
                if (!array_get($this->request->post, 'shipping_method')) {
                    $error['shipping_method']['warning'] = t('error_shipping');
                } else {
                    $code = array_get($this->request->post, 'shipping_method');
                    if (!$this->model_checkout_checkout->setShippingMethod($code)) {
                        $error['shipping_method']['warning'] = t('error_shipping_unavailable');
                    } else {
                        $shipping = explode('.', $code);
                        $data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                    }
                }
            }
        } else {
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['shipping_method']);
        }

        // Comment
        $data['comment'] = array_get($this->request->post, 'comment', '');

        // Terms & conditions agreement
        if ($configCheckoutId = (int)config('config_checkout_id')) {
            $this->load->model('catalog/information');
            $information = $this->model_catalog_information->getInformation($configCheckoutId);
            if ($information && !array_get($this->request->post, 'terms')) {
                $error['agree']['terms'] = sprintf(t('error_agree'), $information['title']);
            }
        }

        // 验证有错误，退出程序执行后续操作
        if ($error) {
            $this->printJson($error, $redirect);
            return;
        }

        // ALL set, update address session then submit the order
        // Guest data
        if ($this->cart->hasShipping()) {
            $this->session->data['shipping_address'] = $data['shipping_address'];
        } else {
            unset($this->session->data['shipping_address']);
        }

        // Comment
        $this->session->data['comment'] = $data['comment'];

        try {
	       $orderId = $this->model_checkout_checkout->createOrder();

            // session for PayPal express checkout
            $this->session->data['cart_paypal'] = array(
                'cart_has_products' => $this->cart->hasProducts(),
                'cart_has_stock' => $this->cart->hasStock(),
                'cart_has_download' => $this->cart->hasDownload(),
                'cart_has_recurring_products' => $this->cart->hasRecurringProducts(),
                'cart_has_shipping' => $this->cart->hasShipping(),
                'cart_total' => $this->cart->getTotal(),
                'cart_tax' => $this->cart->getTaxes(),
                'cart_products' => $this->cart->getProducts(),
                'cart_sub_total' => $this->cart->getSubTotal()
            );

            $this->cart->clear();
            unset($this->session->data['comment']);
            unset($this->session->data['checkout_terms']);

            // Change order status to Unpaid
            $orderStatusId = config('config_unpaid_status_id');
            $this->model_checkout_order->addOrderHistory($orderId, $orderStatusId);

            $this->printJson($error, $redirect);
        } catch (\Exception $e) {
            $error['checkout'] = $e->getMessage();
            $this->printJson($error, $redirect);
            return;
        }
    }

    public function reload()
    {
        $data = [];
        if ($this->cart->hasShipping()) {
            $data['shipping_address_section'] = $this->renderAddressSection('shipping');
        }

        if ($this->isPaymentAddressRequired()) {
            $data['payment_address_section'] = $this->renderAddressSection('payment');
        }

        $data['payment_method_section'] = $this->renderPaymentMethodSection();
        $data['shipping_method_section'] = $this->renderShippingMethodSection();
        $data['cart_section'] = $this->renderCartSection();
        return $data;
    }

    // Address form
    public function address_form()
    {
        if (!$this->customer->isLogged() && !$this->isGuestCheckoutEnabled()) {
            $this->session->data['redirect'] = $this->url->link('checkout/cart');
            $this->session->data['error'] = t('warning_login');
            $this->response->redirect($this->url->link('account/login'));
        }

        $data['type'] = $type = array_get($this->request->get, 'type', 'shipping');
        $data['logged'] = $this->customer->isLogged();

        if ($addressId = array_get($this->request->get, 'address_id')) {
            $address = $this->model_account_address->getAddress($addressId);
            if (!$address) {
                $addressId = 0;
            } else {
                $data['firstname'] = $address['firstname'];
                $data['lastname'] = $address['lastname'];
                $data['calling_code'] = $address['calling_code'];
                $data['telephone'] = $address['telephone'];
                $data['company'] = $address['company'];
                $data['address_1'] = $address['address_1'];
                $data['address_2'] = $address['address_2'];
                $data['postcode'] = $address['postcode'];
                $data['city'] = $address['city'];
                $data['zone_id'] = $address['zone_id'];
                $data['zone'] = $address['zone'];
                $data['zone_code'] = $address['zone_code'];
                $data['country_id'] = $address['country_id'];
                $data['country'] = $address['country'];
                $data['city_id'] = $address['city_id'];
                $data['county_id'] = $address['county_id'];
                $data['address_custom_field'] = $address['custom_field'];
                $data['default'] = $this->customer->getAddressId() == $address['address_id'];
            }
        }

        // 添加新地址，设置默认值
        if (!$addressId) {
            $data['country_id'] = config('config_country_id');
            $data['zone_id'] = config('config_zone_id');
            $data['calling_code'] = config('config_calling_code');
        }

        $data['address_id'] = $addressId;

        $this->load->model('localisation/country');
        $data['countries'] = $this->model_localisation_country->getCountries();

        // Custom Fields
        $this->load->model('account/custom_field');
        $custom_fields = $this->model_account_custom_field->getCustomFields(config('config_customer_group_id'));
        $data['custom_fields'] = [];
        foreach ($custom_fields as $custom_field) {
            if ($custom_field['location'] == 'address') {
                $data['custom_fields'][] = $custom_field;
            }
        }

        $data['config_select_country'] = config('config_select_country');

        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('checkout/checkout/_address_form', $data));
    }

    public function save_address()
    {
        $redirect = '';
        $error = [];

        if (!$this->customer->isLogged() && !$this->isGuestCheckoutEnabled()) {
            $redirect = $this->url->link('account/login');
            $this->printJson($error, $redirect);
            return;
        }

        if (($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $redirect = $this->url->link('checkout/cart');
            $this->printJson($error, $redirect);
            return;
        }

        $type = array_get($this->request->get, 'type', 'shipping');

        if ($error = $this->validateAddress($this->request->post)) {
            $this->printJson($error, $redirect);
            return;
        }

        $addressId = (int)array_get($this->request->get, 'address_id');
        if ($addressId > 0) {
            $this->model_account_address->editAddress($addressId, $this->request->post);
        } else {
            $addressId = $this->model_account_address->addAddress($this->customer->getId(), $this->request->post);
        }

        $address = $this->model_account_address->getAddress($addressId);

        $this->syncAddressSession($type, $address);

        // 修改了 shipping_address 如果不需要 payment_address，则自动更新 payment_address
        if ($type == 'shipping' && !$this->isPaymentAddressRequired()) {
            $this->syncAddressSession('payment', $address);
        }

        $this->printJson($error, $redirect);
    }

    // Payment connect page when order created
    public function connect()
    {
        $this->log(__FUNCTION__);
        $orderId = (int)array_get($this->session->data, 'order_id');
        if ($orderId < 1) {
            $this->response->redirect($this->url->link('common/home'));
        }

        $data['order_id'] = $orderId;
        $order = $this->model_checkout_order->getOrder($orderId);
        if (!$order) {
            $this->response->redirect($this->url->link('common/home'));
        }
        $data['order_id'] = array_get($order, 'order_id');

        $this->load->language('checkout/connect');

        $this->document->setTitle(t('heading_title'));
        $data['heading_title'] = t('heading_title');

        $data['text_success'] = t('text_success');
        $data['column_order_id'] = t('column_order_id');
        $data['column_total'] = t('column_total');
        $data['column_shipping_method'] = t('column_shipping_method');
        $data['column_payment_method'] = t('column_payment_method');
        $data['button_view'] = t('button_view');

        $data['total'] = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']);

        $data['shipping_method'] = $order['shipping_method'] ?: false;
        $data['payment_method'] = $order['payment_method'];

        $payment_code = $order['payment_code'];
        if ($payment_code == 'pp_express') {
            $data['payment_view'] = $this->load->controller("extension/payment/{$payment_code}", $orderId);
        } else {
            $data['payment_view'] = $this->load->controller("extension/payment/{$payment_code}");
        }

        $data['href'] = $this->url->link('account/order/info', 'order_id=' . $order['order_id']);

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('checkout/connect', $data));
    }

    // Helpers
    protected function isValidCart()
    {
        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers']) && empty($this->session->data['recharges'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            return false;
        }

        // Validate minimum quantity requirements.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $total = 0;

            foreach ($products as $product2) {
                if ($product2['product_id'] == $product['product_id']) {
                    $total += $product2['quantity'];
                }
            }

            if ($product['minimum'] > $total) {
                return false;
            }
        }

        return true;
    }

    protected function printJson($error = array(), $redirect = '', $html = null)
    {
        $json = array(
            'error' => $error ? (object)$error : null,
            'redirect' => $redirect,
            'html' => $html,
        );
        $this->json_output($json);
    }

    /**
     * 是否需要账单地址？
     */
    protected function isPaymentAddressRequired()
    {
        return is_ft();
    }

    /**
     * If guest checkout enabled?
     */
    protected function isGuestCheckoutEnabled()
    {
        return config('config_checkout_guest');
    }

    // Views
    public function renderGuestAddressSection()
    {
        $data['login'] = $this->url->link('account/login', ['redirect' => $this->url->link('checkout/checkout')]);
        $data['register'] = $this->url->link('account/register', ['redirect' => $this->url->link('checkout/checkout')]);

        $this->load->model('localisation/country');
        $data['countries'] = $this->model_localisation_country->getCountries();

        // Custom Fields
        $this->load->model('account/custom_field');
        $custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));
        foreach ($custom_fields as $custom_field) {
            if ($custom_field['location'] == 'address') {
                $data['custom_fields'][] = $custom_field;
            }
        }

        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

        $data['payment_address_required'] = $this->isPaymentAddressRequired();
        $data['has_shipping'] = $this->cart->hasShipping();

        // 默认值
        $data['config_calling_code'] = config('config_calling_code');
        $data['config_country_id'] = config('config_country_id');
        $data['config_zone_id'] = config('config_zone_id');

        // 省市县是否需要显示国家选择？
        $data['config_select_country'] = config('config_select_country');

        return $this->load->view("checkout/checkout/_guest_address", $data);
    }

    protected function renderAddressSection($type = 'shipping')
    {
        $this->log(__FUNCTION__);
        $data['logged'] = $this->customer->isLogged();

        if ($this->customer->isLogged()) {
            $addressId = (int)array_get($this->session->data, "{$type}_address.address_id");
            if ($addressId) {
                if(! $this->model_account_address->getAddress($addressId)) {
                    $addressId = 0;
                }
            }

            $data['address_id'] = $addressId ?: $this->customer->getAddressId();
            $data['addresses'] = $this->model_account_address->getAddresses();

            foreach ($data['addresses'] as $addressId => $address) {
                if ($addressId == $data['address_id']) {
                    $defaultAddress = $address;
                    unset($data['addresses'][$addressId]);
                    array_unshift($data['addresses'], $defaultAddress);
                    break;
                }
            }
        } else {
            if ($address = array_get($this->session->data, "guest.{$type}_address")) {
                $data['addresses'][] = $address;
            } else {
                $data['addresses'] = [];
            }
        }

        // 需要账单地址情况下，没有地址时，不显示添加新地址框
        if ($type == 'payment' && $this->isPaymentAddressRequired() && $this->cart->hasShipping() && !$data['addresses']) {
            return;
        }

        return $this->load->view("checkout/checkout/_{$type}_address", $data);
    }

    protected function renderPaymentMethodSection()
    {
        $this->log(__FUNCTION__);
        if (isset($this->session->data['payment_address'])) {
            $this->model_checkout_checkout->getPaymentMethods();

            $code = array_get($this->session->data, 'payment_method.code');
            if (! $this->model_checkout_checkout->setPaymentMethod($code)) {
                $this->model_checkout_checkout->setPaymentMethod();
            }
        }

        if (empty($this->session->data['payment_methods'])) {
            $data['error_warning'] = sprintf(t('error_no_payment'), $this->url->link('information/contact'));
        } else {
            $data['error_warning'] = '';
        }

        $data['payment_methods'] = array_get($this->session->data, 'payment_methods', []);
        $data['code'] = array_get($this->session->data, 'payment_method.code');

        return $this->load->view('checkout/checkout/_payment_method', $data);
    }

    // 切换 自提/商家配送
    public function shipping_type()
    {
        if ((bool)array_get($this->request->get, 'is_pickup')) {
            $this->session->data['is_pickup'] = 1;
        } else {
            unset($this->session->data['is_pickup']);
        }

        $url = $this->url->getQueriesExclude(['is_pickup']);
        $this->response->redirect($this->url->link('checkout/checkout', $url));
    }

    protected function renderShippingMethodSection()
    {
        $this->log(__FUNCTION__);
        $data['shipping'] = $this->cart->hasShipping(0);

        if ($this->cart->hasShipping(0)) {
            if (isset($this->session->data['shipping_address'])) {
                // Shipping Methods
                $this->model_checkout_checkout->getShippingMethods();
            }

            if (empty($this->session->data['shipping_methods'])) {
                $data['error_warning'] = sprintf(t('error_no_shipping'), $this->url->link('information/contact'));
            } else {
                $data['error_warning'] = '';
            }

            $data['shipping_methods'] = array_get($this->session->data, 'shipping_methods');
            $data['code'] = array_get($this->session->data, 'shipping_method.code');
        } else {
            $data['text_shipping_not_required'] = t('text_shipping_not_required');
        }

        return $this->load->view('checkout/checkout/_shipping_method', $data);
    }

    protected function renderCommentSection()
    {
        $this->log(__FUNCTION__);
        $data['comment'] = array_get($this->session->data, 'comment', '');

        return $this->load->view('checkout/checkout/_comment', $data);
    }

    protected function renderCartSection()
    {
        $this->log(__FUNCTION__);
        $data['products'] = $this->getProducts();
        $data['vouchers'] = $this->getVouchers();
        $data['recharges'] = $this->getRecharges();
        $data['totals'] = $this->getTotals();

        return $this->load->view('checkout/checkout/_confirm', $data);
    }

    protected function renderAgreeSection()
    {
        $this->log(__FUNCTION__);

        // Payment method
        if (config('config_checkout_id')) {
            $this->load->model('catalog/information');
            $information_info = $this->model_catalog_information->getInformation(config('config_checkout_id'));
            if ($information_info) {
                $data['text_payment_method'] = sprintf(t('text_agree'), $this->url->link('information/information/agree', 'information_id=' . config('config_checkout_id')), $information_info['title'], $information_info['title']);
            } else {
                $data['text_payment_method'] = '';
            }
        } else {
            $data['text_payment_method'] = '';
        }

        $data['terms'] = (int)array_get($this->session->data, 'checkout_terms');

        return $this->load->view('checkout/checkout/_agree', $data);
    }

    // protected
    protected function getProducts()
    {
        $this->load->model('tool/image');
        $products = array();

        foreach ($this->cart->getProducts() as $product) {
            $image = $this->model_tool_image->resize($product['image'] ?: 'placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));

            $option_data = array();
            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['value'];
                } else {
                    $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
                    if ($upload_info) {
                        $value = $upload_info['name'];
                    } else {
                        $value = '';
                    }
                }

                $option_data[] = array(
                    'name' => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                );
            }

            $products[] = array(
                'cart_id' => $product['cart_id'],
                'product_id' => $product['product_id'],
                'image' => $image,
                'name' => $product['name'],
                'model' => $product['model'],
                'option' => $option_data,
                'quantity' => $product['quantity'],
                'subtract' => $product['subtract'],
                'price' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                'total' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']),
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'])
            );
        }

        return $products;
    }

    protected function getVouchers()
    {
        $vouchers = array();

        if (empty($this->session->data['vouchers'])) {
            return $vouchers;
        }

        foreach ($this->session->data['vouchers'] as $voucher) {
            $vouchers[] = array(
                'description' => $voucher['description'],
                'amount' => $this->currency->format($voucher['amount'], $this->session->data['currency'])
            );
        }

        return $vouchers;
    }

    protected function getRecharges()
    {
        $recharges = array();

        if (empty($this->session->data['recharges'])) {
            return $recharges;
        }

        foreach ($this->session->data['recharges'] as $recharge) {
            $recharges[] = array(
                'description' => $recharge['description'],
                'amount' => $this->currency->format($recharge['amount'], $this->session->data['currency'])
            );
        }

        return $recharges;
    }

    protected function getTotals()
    {
        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Because __call can not keep var references so we put them into an array.
        $total_data = array(
            'totals' => &$totals,
            'taxes' => &$taxes,
            'total' => &$total
        );

        $this->load->model('setting/extension');
        $sort_order = array();
        $results = $this->model_setting_extension->getExtensions('total');
        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);
        foreach ($results as $result) {
            if ($this->config->get('total_' . $result['code'] . '_status')) {
                $this->load->model('extension/total/' . $result['code']);

                // We have to put the totals in an array so that they pass by reference.
                $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
            }
        }

        $sort_order = array();
        foreach ($totals as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }
        array_multisort($sort_order, SORT_ASC, $totals);

        $results = array();

        foreach ($totals as $total) {
            $results[] = array(
                'title' => $total['title'],
                'text' => $this->currency->format($total['value'], $this->session->data['currency'])
            );
        }

        return $results;
    }

    // Address
    protected function initAddressSession($type = 'shipping')
    {
        if ($type == 'shipping') {
            if (!$this->cart->hasShipping()) {
                $this->log('Shipping not required.');
                unset($this->session->data['shipping_address']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['shipping_method']);
                return;
            }
        }

        if ($this->customer->isLogged()) {
            // Use previous selected address
            if ($addressId = array_get($this->session->data, "{$type}_address.address_id")) {
                $this->log("{$type}_address_id: {$addressId}");
                if ($address = $this->model_account_address->getAddress($addressId)) {
                    $this->syncAddressSession($type, $address);
                } else {
                    $this->log("{$type}_address_id: {$addressId} not found.");
                    unset($this->session->data["{$type}_address"]);
                    unset($this->session->data["{$type}_methods"]);
                    unset($this->session->data["{$type}_method"]);
                }
            }

            // Use customer default address
            if (!array_get($this->session->data, "{$type}_address.address_id")) {
                $address = $this->model_account_address->getAddress($this->customer->getAddressId());
                if ($address) {
                    $this->syncAddressSession($type, $address);
                } else {
                    unset($this->session->data["{$type}_address"]);
                    unset($this->session->data["{$type}_methods"]);
                    unset($this->session->data["{$type}_method"]);
                }
            }

            // User customer first address
            if (!array_get($this->session->data, "{$type}_address.address_id")) {
                $addresses = $this->model_account_address->getAddresses();
                if ($addresses) {
                    $firstAddress = reset($addresses);
                    $this->syncAddressSession($type, $firstAddress);
                } else {
                    unset($this->session->data["{$type}_address"]);
                    unset($this->session->data["{$type}_methods"]);
                    unset($this->session->data["{$type}_method"]);
                }
            }
        } else {
            if ($address = array_get($this->session->data, "guest.{$type}_address")) {
                $this->syncAddressSession($type, $address);
            }
        }

        // Use dummy address
        if (!array_get($this->session->data, "{$type}_address")) {
            $this->fakeGuestAddressSession($type);
        }
    }

    protected function syncAddressSession($type, $address)
    {
        if (!in_array($type, ['payment', 'shipping'])) {
            return false;
        }

        if ($type == 'shipping' && !$this->cart->hasShipping()) {
            unset($this->session->data['shipping_address']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['shipping_method']);
            return false;
        }

        $this->session->data["{$type}_address"] = $address;

        $method = 'set' . ucfirst($type) . 'Method';
        if ($code = array_get($this->session->data, "{$type}_method.code")) {
            if (!$this->model_checkout_checkout->{$method}($code)) {
                $this->model_checkout_checkout->{$method}();
            }
        } else {
            $this->model_checkout_checkout->{$method}();
        }
    }

    protected function fakeGuestAddressSession($type)
    {
        if (!in_array($type, ["payment", "shipping"])) {
            return;
        }

        $address['country_id'] = config('config_country_id');
        $address['zone_id'] = config('config_zone_id');

        $this->session->data[$type . '_address'] = $address;

        $this->syncAddressSession($type, $address);
    }

    protected function validateAddress($data)
    {
        $error = [];
        $this->load->language('account/address');

        $firstname = trim((string)array_get($data, 'firstname'));
        if ((utf8_strlen($firstname) < 1) || (utf8_strlen($firstname) > 32)) {
            $error['firstname'] = t('error_firstname');
        }

        if (is_ft()) {
            $lastname = trim((string)array_get($data, 'lastname'));
            if ((utf8_strlen($lastname) < 1) || (utf8_strlen($lastname) > 32)) {
                $error['lastname'] = t('error_lastname');
            }
        }

        // $prefix = payment_address 时，如果 不需要配送，则 payment_address 会有 email 字段
        if (!$this->customer->isLogged()) {
            if (isset($data['email'])) {
                $email = trim((string)array_get($data, 'email'));
                if ((utf8_strlen($email) > 96) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error['email'] = t('error_email');
                }
            }
        }

        $telephone = trim((string)array_get($data, 'telephone'));
        if ((utf8_strlen($telephone) < 5) || (utf8_strlen($telephone) > 32)) {
            $error['telephone'] = t('error_telephone');
        }

        $address1 = trim((string)array_get($data, 'address_1'));
        if ((utf8_strlen($address1) < 3) || (utf8_strlen($address1) > 128)) {
            $error['address_1'] = t('error_address_1');
        }

        $countryId = (int)array_get($data, 'country_id');
        if ($countryId < 1) {
            $error['country_id'] = t('error_country');
        }

        if ($countryId > 0) {
            $this->load->model('localisation/country');
            $countryInfo = $this->model_localisation_country->getCountry($countryId);

            // postcode required?
            $postcode = trim((string)array_get($data, 'postcode'));
            if ($countryInfo && $countryInfo['postcode_required']) {
                if ((utf8_strlen($postcode) < 2) || (utf8_strlen($postcode) > 10)) {
                    $error['postcode'] = t('error_postcode');
                }
            }
        }

        $zoneId = (int)array_get($data, 'zone_id');
        if ($zoneId < 1) {
            $error['zone_id'] = t('error_zone');
        }

        if ($zoneId > 0) {
            $this->load->model('localisation/zone');
            $zone = $this->model_localisation_zone->getZone($zoneId);
            if (!$zone || $zone['country_id'] != $countryId) {
                $error['zone_id'] = t('error_zone');
            }
        }

        $cityId = (int)array_get($data, 'city_id');
        if ($zoneId > 0) {
            $this->load->model('localisation/city');
            $cities = $this->model_localisation_city->getCitiesByZoneId($zoneId, true);
            // 当前 zone 下有 city, 但 city 没有选择
            if (!empty($cities)) {
                if ($cityId < 1) {
                    $error['city_id'] =  t('error_city_id');
                } else {
                    $this->request->post['city'] = '';
                }
            } else { // 当前 zone 下没有 city
                $cityId = 0;
                $this->request->post['city_id'] = 0;
                $this->request->post['county_id'] = 0;
            }
        }

        $countyId = (int)array_get($data, 'county_id');
        if ($cityId > 0) {
            $this->load->model('localisation/city');
            $counties = $this->model_localisation_city->getCitiesByZoneId($cityId, false);
            // 当前 city 下有 county, 但 county 没有选择
            if (!empty($counties)) {
                if ($countyId < 1) {
                    $error['county_id'] =  t('error_county_id');
                }
            }
        }

        // Custom field validation
        $this->load->model('account/custom_field');
        $custom_fields = $this->model_account_custom_field->getCustomFields(config('config_customer_group_id'));
        foreach ($custom_fields as $custom_field) {
            if ($custom_field['location'] != 'address') {
                continue;
            }
            $customFieldValue = array_get($data, 'custom_field.' . $custom_field['custom_field_id']);
            if ($custom_field['required'] && empty($customFieldValue)) {
                $error["custom_field_{$custom_field['custom_field_id']}"] = sprintf(t('error_custom_field'), $custom_field['name']);
            } elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($customFieldValue, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
                $error["custom_field_{$custom_field['custom_field_id']}"] = sprintf(t('error_custom_field'), $custom_field['name']);
            }
        }
        return $error;
    }

    protected function validateGuestBasicPaymentAddressFields()
    {
        $error = [];
        $this->load->language('account/address');

        $data = $this->request->post['payment_address'];

        $firstname = trim((string)array_get($data, 'firstname'));
        if ((utf8_strlen($firstname) < 1) || (utf8_strlen($firstname) > 32)) {
            $error['firstname'] = t('error_firstname');
        }

        if (is_ft()) {
            $lastname = trim((string)array_get($data, 'lastname'));
            if ((utf8_strlen($lastname) < 1) || (utf8_strlen($lastname) > 32)) {
                $error['lastname'] = t('error_lastname');
            }
        }

        $email = trim((string)array_get($data, 'email'));
        if ((utf8_strlen($email) > 96) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error['email'] = t('error_email');
        }

        $telephone = trim((string)array_get($data, 'telephone'));
        if ((utf8_strlen($telephone) < 5) || (utf8_strlen($telephone) > 32)) {
            $error['telephone'] = t('error_telephone');
        }

        return $error;
    }

    protected function log($data = null)
    {
        if ($data) {
            $this->model_checkout_checkout->log($data);
        }
    }

    // Original
    public function country()
    {
        $json = array();

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

        if ($country_info) {
            $this->load->model('localisation/zone');

            $json = array(
                'country_id' => $country_info['country_id'],
                'name' => $country_info['name'],
                'iso_code_2' => $country_info['iso_code_2'],
                'iso_code_3' => $country_info['iso_code_3'],
                'address_format' => $country_info['address_format'],
                'postcode_required' => $country_info['postcode_required'],
                'zone' => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
                'status' => $country_info['status']
            );
        }

        $this->json_output($json);
    }

    public function customfield()
    {
        $json = array();

        $this->load->model('account/custom_field');

        // Customer Group
        if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
            $customer_group_id = $this->request->get['customer_group_id'];
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

        foreach ($custom_fields as $custom_field) {
            $json[] = array(
                'custom_field_id' => $custom_field['custom_field_id'],
                'required' => $custom_field['required']
            );
        }

        $this->json_output($json);
    }
}
