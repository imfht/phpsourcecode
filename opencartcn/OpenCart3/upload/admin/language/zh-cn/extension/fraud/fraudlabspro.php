<?php
/**
 *
 * @copyright        2017 www.guangdawangluo.com - All Rights Reserved
 * @author           opencart.cn <support@opencart.cn>
 * @created          2016-10-22 09:12:56
 * @modified         2016-11-05 17:35:22
 */

// Heading
$_['heading_title']              = 'FraudLabs Pro';

// Text
$_['text_extension']             = '扩展';
$_['text_success']               = '成功：您已经修改了欺诈实验室的设置！';
$_['text_edit']                  = '设置';
$_['text_signup']                = '欺诈是一项检查服务，请通过此链接 <a href="http://www.fraudlabspro.com/plan?ref=1730" target="_blank"><u>注册</u></a> 并获取免费的API key。';
$_['text_id']                    = '欺诈实验 ID';
$_['text_ip_address']            = 'IP 地址';
$_['text_ip_net_speed']          = 'IP 网速';
$_['text_ip_isp_name']           = 'IP ISP 名称';
$_['text_ip_usage_type']         = 'IP 使用类型';
$_['text_ip_domain']             = 'IP 域名';
$_['text_ip_time_zone']          = 'IP 时区';
$_['text_ip_location']           = 'IP 位置';
$_['text_ip_distance']           = 'IP 距离';
$_['text_ip_latitude']           = 'IP 维度';
$_['text_ip_longitude']          = 'IP 经度';
$_['text_risk_country']          = '高危国家';
$_['text_free_email']            = '免费 Email';
$_['text_ship_forward']          = 'Ship Forward';
$_['text_using_proxy']           = '使用代理';
$_['text_bin_found']             = 'BIN Found';
$_['text_email_blacklist']       = '电子邮件黑名单';
$_['text_credit_card_blacklist'] = '信用卡黑名单';
$_['text_score']                 = '实验室分数';
$_['text_status']                = '实验室状态';
$_['text_message']               = '信息';
$_['text_transaction_id']        = '交易 ID';
$_['text_credits']               = '余额';
$_['text_error']                 = '错误';
$_['text_flp_upgrade']           = '<a href="http://www.fraudlabspro.com/plan" target="_blank">[升级]</a>';
$_['text_flp_merchant_area']     = '请登录 <a href="http://www.fraudlabspro.com/login" target="_blank">FraudLabs Pro Merchant Area</a> 了解订单详情。';

// Entry
$_['entry_status']               = '状态';
$_['entry_key']                  = 'API Key';
$_['entry_score']                = '风险分数';
$_['entry_order_status']         = '订单状态';
$_['entry_review_status']        = '评论状态';
$_['entry_approve_status']       = '审核状态';
$_['entry_reject_status']        = '拒绝状态';
$_['entry_simulate_ip']          = '模拟 IP';

// Help
$_['help_order_status']          = 'Orders that have a score over your set risk score will be assigned this order status.';
$_['help_review_status']         = 'Orders that marked as review by FraudLabs Pro will be assigned this order status.';
$_['help_approve_status']        = 'Orders that marked as approve by FraudLabs Pro will be assigned this order status.';
$_['help_reject_status']         = 'Orders that marked as reject by FraudLabs Pro will be assigned this order status.';
$_['help_simulate_ip']           = 'Simulate the visitor IP address for testing. Leave blank to disable it.';
$_['help_fraudlabspro_id']       = 'Unique identifier for a transaction screened by FraudLabs Pro system.';
$_['help_ip_address']            = 'IP Address.';
$_['help_ip_net_speed']          = 'Connection speed.';
$_['help_ip_isp_name']           = 'ISP of the IP address.';
$_['help_ip_usage_type']         = 'Usage type of the IP address. E.g, ISP, Commercial, Residential.';
$_['help_ip_domain']             = 'Domain name of the IP address.';
$_['help_ip_time_zone']          = 'Time zone of the IP address.';
$_['help_ip_location']           = 'Location of the IP address.';
$_['help_ip_distance']           = 'Distance from IP address to Billing Location.';
$_['help_ip_latitude']           = 'Latitude of the IP address.';
$_['help_ip_longitude']          = 'Longitude of the IP address.';
$_['help_risk_country']          = 'Whether IP address country is in the latest high risk country list.';
$_['help_free_email']            = 'Whether e-mail is from free e-mail provider.';
$_['help_ship_forward']          = 'Whether shipping address is a freight forwarder address.';
$_['help_using_proxy']           = 'Whether IP address is from Anonymous Proxy Server.';
$_['help_bin_found']             = 'Whether the BIN information matches our BIN list.';
$_['help_email_blacklist']       = 'Whether the email address is in our blacklist database.';
$_['help_credit_card_blacklist'] = 'Whether the credit card is in our blacklist database.';
$_['help_score']                 = 'Risk score, 0 (low risk) - 100 (high risk).';
$_['help_status']                = 'FraudLabs Pro status.';
$_['help_message']               = 'FraudLabs Pro error message description.';
$_['help_transaction_id']        = 'Unique identifier for a transaction screened by FraudLabs Pro system.';
$_['help_credits']               = 'Balance of the credits available after this transaction.';

// Error
$_['error_permission']           = 'Warning: You do not have permission to modify FraudLabs Pro settings!';
$_['error_key']                  = 'API Key Required!';
