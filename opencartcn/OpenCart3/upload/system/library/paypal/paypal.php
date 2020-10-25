<?php
/**
 * paypal.php
 *
 * @copyright 2019 opencart.cn - All Rights Reserved
 * @link https://www.guangdawangluo.com
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2019-12-2019-12-02 11:17
 * @modified 2019-12-2019-12-02 11:17
 */

namespace Paypal;

class Paypal
{
    private static $singleton;
    private $registry;

    public function __construct()
    {
        $this->registry = \Registry::getSingleton();
    }

    public static function getSingleton()
    {
        if (!(self::$singleton instanceof self)) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }

    public function __set($key, $value)
    {
        // TODO: Implement __set() method.
        $this->registry->set($key, $value);
    }

    public function __get($key)
    {
        // TODO: Implement __get() method.
        return $this->registry->get($key);
    }

    public function confirmExpress()
    {
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            return [
                'redirect' => $redirect = $this->url->link('checkout/cart')
            ];
        }

        $order_info = model('checkout/order')->getOrder($this->session->data['order_id']);

        $max_amount = $this->cart->getTotal() * 1.5;
        $max_amount = $this->currency->format($max_amount, config('payment_pp_express_currency'), '', false);
        if ($this->cart->hasShipping()) {
            $shipping = 0;

            // PayPal requires some countries to use zone code (not name) to be sent in SHIPTOSTATE
            $ship_to_state_codes = array(
                '30', // Brazil
                '38', // Canada
                '105', // Italy
                '138', // Mexico
                '223', // USA
            );

            if (in_array($order_info['shipping_country_id'], $ship_to_state_codes)) {
                $ship_to_state = $order_info['shipping_zone_code'];
            } else {
                $ship_to_state = $order_info['shipping_zone'];
            }

            $data_shipping = array(
                'PAYMENTREQUEST_0_SHIPTONAME'        => html_entity_decode($order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8'),
                'PAYMENTREQUEST_0_SHIPTOSTREET'      => html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8'),
                'PAYMENTREQUEST_0_SHIPTOSTREET2'     => html_entity_decode($order_info['shipping_address_2'], ENT_QUOTES, 'UTF-8'),
                'PAYMENTREQUEST_0_SHIPTOCITY'        => html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8'),
                'PAYMENTREQUEST_0_SHIPTOSTATE'       => html_entity_decode($ship_to_state, ENT_QUOTES, 'UTF-8'),
                'PAYMENTREQUEST_0_SHIPTOZIP'         => html_entity_decode($order_info['shipping_postcode'], ENT_QUOTES, 'UTF-8'),
                'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => $order_info['shipping_iso_code_2'],
                'ADDROVERRIDE' 						 => 1,
            );
        } else {
            $shipping = 1;
            $data_shipping = array();
        }

        $data = array(
            'METHOD'             => 'SetExpressCheckout',
            'MAXAMT'             => $max_amount,
            'RETURNURL'          => $this->url->link('extension/payment/pp_express/checkoutReturn'),
            'CANCELURL'          => $this->url->link('checkout/checkout'),
            'REQCONFIRMSHIPPING' => 0,
            'NOSHIPPING'         => $shipping,
            'LOCALECODE'         => 'EN',
            'LANDINGPAGE'        => 'Login',
            'HDRIMG'             => model('tool/image')->resize($this->config->get('payment_pp_express_logo'), 750, 90),
            'PAYFLOWCOLOR'       => config('payment_pp_express_colour'),
            'CHANNELTYPE'        => 'Merchant',
            'ALLOWNOTE'          => config('payment_pp_express_allow_note')
        );

        $data = array_merge($data, $data_shipping);
        if (isset($this->session->data['pp_login']['seamless']['access_token']) && (isset($this->session->data['pp_login']['seamless']['customer_id']) && $this->session->data['pp_login']['seamless']['customer_id'] == $this->customer->getId()) && $this->config->get('module_pp_login_seamless')) {
            $data['IDENTITYACCESSTOKEN'] = $this->session->data['pp_login']['seamless']['access_token'];
        }

        $data = array_merge($data, model('extension/payment/pp_express')->paymentRequestInfo());

        $result = model('extension/payment/pp_express')->call($data);

        $ack = strtolower(array_get($result, 'ACK', ''));
        if (!$ack || $ack == 'failure') {
            $error_code = $result['L_ERRORCODE0'];
            $error_detail = $result['L_LONGMESSAGE0'];
            return [
                'error' => 'PayPal Express Checkout: ' . $error_detail . ' (code: ' . $error_code . ') '
            ];
        }

        $token = array_get($result, 'TOKEN', '');
        if (!$token) {
            return [
                'error' => 'PayPal Express Checkout: Unable to create PayPal Express Checkout session'
            ];
        }

        return [
            'token' => $token
        ];
    }
}