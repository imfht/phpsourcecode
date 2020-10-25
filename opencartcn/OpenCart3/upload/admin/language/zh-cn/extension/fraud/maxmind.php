<?php
/**
 *
 * @copyright        2017 www.guangdawangluo.com - All Rights Reserved
 * @author           opencart.cn <support@opencart.cn>
 * @created          2017-10-05 09:40:20
 * @modified         2017-10-07 11:45:05
 */

// Heading
$_['heading_title']                           = 'MaxMind 反欺诈';

// Text
$_['text_extension']                          = '扩展';
$_['text_success']                            = '成功：您已经修改了高敏反欺诈！';
$_['text_edit']                               = '编辑高敏反欺诈';
$_['text_signup']                             = '高灵是一种欺诈检测服务。如果你没有一个许可证密钥你可以 <a href="http://www.maxmind.com/?rId=opencart" target="_blank"><u>在这里注册</u></a>.';
$_['text_country_match']                      = '国家对比';
$_['text_country_code']                       = '国家代码';
$_['text_high_risk_country']                  = '高危国家';
$_['text_distance']                           = '距离';
$_['text_ip_region']                          = 'IP 地区';
$_['text_ip_city']                            = 'IP 城市';
$_['text_ip_latitude']                        = 'IP 维度';
$_['text_ip_longitude']                       = 'IP 经度';
$_['text_ip_isp']                             = 'ISP';
$_['text_ip_org']                             = 'IP 组织';
$_['text_ip_asnum']                           = 'ASNUM';
$_['text_ip_user_type']                       = 'IP 用户类型';
$_['text_ip_country_confidence']              = 'IP 国家信用';
$_['text_ip_region_confidence']               = 'IP 地区信用';
$_['text_ip_city_confidence']                 = 'IP 城市信用';
$_['text_ip_postal_confidence']               = 'IP 邮政信用';
$_['text_ip_postal_code']                     = 'IP 邮政编码';
$_['text_ip_accuracy_radius']                 = 'IP 精度半径';
$_['text_ip_net_speed_cell']                  = 'IP 网速单元';
$_['text_ip_metro_code']                      = 'IP 地铁代码';
$_['text_ip_area_code']                       = 'IP 地区代码';
$_['text_ip_time_zone']                       = 'IP 时区';
$_['text_ip_region_name']                     = 'IP 地域名称';
$_['text_ip_domain']                          = 'IP 域';
$_['text_ip_country_name']                    = 'IP 国家名称';
$_['text_ip_continent_code']                  = 'IP 大陆代码';
$_['text_ip_corporate_proxy']                 = 'IP 公司代理: ';
$_['text_anonymous_proxy']                    = '匿名代理';
$_['text_proxy_score']                        = '代理评分';
$_['text_is_trans_proxy']                     = '透明代理';
$_['text_free_mail']                          = '免费邮件';
$_['text_carder_email']                       = '主要邮件';
$_['text_high_risk_username']                 = '高风险用户名称';
$_['text_high_risk_password']                 = '高风险密码';
$_['text_bin_match']                          = 'Bin 对比';
$_['text_bin_country']                        = 'Bin 国家';
$_['text_bin_name_match']                     = 'Bin 名称比较';
$_['text_bin_name']                           = 'Bin 名称';
$_['text_bin_phone_match']                    = 'Bin 电话比较';
$_['text_bin_phone']                          = 'Bin 电话';
$_['text_customer_phone_in_billing_location'] = '客户电话号码在帐单位置';
$_['text_ship_forward']                       = '装运前';
$_['text_city_postal_match']                  = '城市邮政匹配';
$_['text_ship_city_postal_match']             = '航运城市邮政匹配';
$_['text_score']                              = '分数';
$_['text_explanation']                        = '说明';
$_['text_risk_score']                         = '风险评分';
$_['text_queries_remaining']                  = '剩余查询';
$_['text_maxmind_id']                         = '高敏 ID';
$_['text_error']                              = '错误';

// Entry
$_['entry_key']                               = '高敏许可证 License Key';
$_['entry_score']                             = '风险评分';
$_['entry_order_status']                      = '订单状态';
$_['entry_status']                            = '状态';

// Help
$_['help_order_status']                       = 'Orders that have a score over your set risk score will be assigned this order status and will not be allowed to reach the complete status automatically.';
$_['help_country_match']                      = 'Whether country of IP address matches billing address country (mismatch = higher risk).';
$_['help_country_code']                       = 'Country Code of the IP address.';
$_['help_high_risk_country']                  = 'Whether IP address or billing address country is in Ghana, Nigeria, or Vietnam.';
$_['help_distance']                           = 'Distance from IP address to Billing Location in kilometers (large distance = higher risk).';
$_['help_ip_region']                          = 'Estimated State/Region of the IP address.';
$_['help_ip_city']                            = 'Estimated City of the IP address.';
$_['help_ip_latitude']                        = 'Estimated Latitude of the IP address.';
$_['help_ip_longitude']                       = 'Estimated Longitude of the IP address.';
$_['help_ip_isp']                             = 'ISP of the IP address.';
$_['help_ip_org']                             = 'Organization of the IP address.';
$_['help_ip_asnum']                           = 'Estimated Autonomous System Number of the IP address.';
$_['help_ip_user_type']                       = 'Estimated user type of the IP address.';
$_['help_ip_country_confidence']              = 'Representing our confidence that the country location is correct.';
$_['help_ip_region_confidence']               = 'Representing our confidence that the region location is correct.';
$_['help_ip_city_confidence']                 = 'Representing our confidence that the city location is correct.';
$_['help_ip_postal_confidence']               = 'Representing our confidence that the postal code location is correct.';
$_['help_ip_postal_code']                     = 'Estimated Postal Code of the IP address.';
$_['help_ip_accuracy_radius']                 = 'The average distance between the actual location of the end user using the IP address and the location returned by the GeoIP City database, in miles.';
$_['help_ip_net_speed_cell']                  = 'Estimated network type of the IP address.';
$_['help_ip_metro_code']                      = 'Estimated Metro Code of the IP address.';
$_['help_ip_area_code']                       = 'Estimated Area Code of the IP address.';
$_['help_ip_time_zone']                       = 'Estimated Time Zone of the IP address.';
$_['help_ip_region_name']                     = 'Estimated Region name of the IP address.';
$_['help_ip_domain']                          = 'Estimated domain of the IP address.';
$_['help_ip_country_name']                    = 'Estimated Country name of the IP address.';
$_['help_ip_continent_code']                  = 'Estimated Continent code of the IP address.';
$_['help_ip_corporate_proxy']                 = 'Whether the IP is an Corporate Proxy in the database or not.';
$_['help_anonymous_proxy']                    = 'Whether IP address is Anonymous Proxy (anonymous proxy = very high risk).';
$_['help_proxy_score']                        = 'Likelihood of IP Address being an Open Proxy.';
$_['help_is_trans_proxy']                     = 'Whether IP address is in our database of known transparent proxy servers, returned if forwardedIP is passed as an input.';
$_['help_free_mail']                          = 'Whether e-mail is from free e-mail provider (free e-mail = higher risk).';
$_['help_carder_email']                       = 'Whether e-mail is in database of high risk e-mails.';
$_['help_high_risk_username']                 = 'Whether usernameMD5 input is in database of high risk usernames. Only returned if usernameMD5 is included in inputs.';
$_['help_high_risk_password']                 = 'Whether passwordMD5 input is in database of high risk passwords. Only returned if passwordMD5 is included in inputs.';
$_['help_bin_match']                          = 'Whether country of issuing bank based on BIN number matches billing address country.';
$_['help_bin_country']                        = 'Country Code of the bank which issued the credit card based on BIN number.';
$_['help_bin_name_match']                     = 'Whether name of issuing bank matches inputted  BIN name. A return value of Yes provides a positive indication that cardholder is in possession of credit card.';
$_['help_bin_name']                           = 'Name of the bank which issued the credit card based on BIN number. Available for approximately 96% of BIN numbers.';
$_['help_bin_phone_match']                    = 'Whether customer service phone number matches inputed BIN Phone. A return value of Yes provides a positive indication that cardholder is in possession of credit card.';
$_['help_bin_phone']                          = 'Customer service phone number listed on back of credit card. Available for approximately 75% of BIN numbers. In some cases phone number returned may be outdated.';
$_['help_customer_phone_in_billing_location'] = 'Whether the customer phone number is in the billing zip code. A return value of Yes provides a positive indication that the phone number listed belongs to the cardholder. A return value of No indicates that the phone number may be in a different area, or may not be listed in our database. NotFound is returned when the phone number prefix cannot be found in our database at all. Currently we only support US Phone numbers.';
$_['help_ship_forward']                       = 'Whether shipping address is in database of known mail drops.';
$_['help_city_postal_match']                  = 'Whether billing city and state match zipcode. Currently available for US addresses only, returns empty string outside the US.';
$_['help_ship_city_postal_match']             = 'Whether shipping city and state match zipcode. Currently available for US addresses only, returns empty string outside the US.';
$_['help_score']                              = 'Overall fraud score based on outputs listed above. This is the original fraud score, and is based on a simple formula. It has been replaced with risk score (see below), but is kept for backwards compatibility.';
$_['help_explanation']                        = 'A brief explanation of the score, detailing what factors contributed to it, according to our formula. Please note this corresponds to the score, not the riskScore.';
$_['help_risk_score']                         = 'New fraud score representing the estimated probability that the order is fraud, based off of analysis of past minFraud transactions. Requires an upgrade for clients who signed up before February 2007.';
$_['help_queries_remaining']                  = 'Number of queries remaining in your account, can be used to alert you when you may need to add more queries to your account.';
$_['help_maxmind_id']                         = 'Unique identifier, used to reference transactions when reporting fraudulent activity back to MaxMind. This reporting will help MaxMind improve its service to you and will enable a planned feature to customize the fraud scoring formula based on your chargeback history.';
$_['help_error']                              = 'Returns an error string with a warning message or a reason why the request failed.';

// Error
$_['error_permission']                        = '警告：您没有权限修改高敏反欺诈！';
$_['error_key']		                      = '许可证密钥！';
