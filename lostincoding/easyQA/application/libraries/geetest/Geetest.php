<?php

class Geetest
{
    /**
     * 验证二次认证(服务器端验证)
     */
    public function verify($type)
    {
        $ci = &get_instance();

        //如果未开启极验验证则直接返回true
        if (!$ci->config->config['geetest']['open']) {
            return true;
        }

        $geetest_challenge = $ci->input->post('geetest_challenge');
        $geetest_validate = $ci->input->post('geetest_validate');
        $geetest_seccode = $ci->input->post('geetest_seccode');

        $geetest_params = array(
            'captcha_id' => $ci->config->config['geetest']['CAPTCHA_ID'],
            'private_key' => $ci->config->config['geetest']['PRIVATE_KEY'],
        );
        if ($type == 'mobile') {
            $geetest_params = array(
                'captcha_id' => $ci->config->config['geetest']['MOBILE_CAPTCHA_ID'],
                'private_key' => $ci->config->config['geetest']['MOBILE_PRIVATE_KEY'],
            );
        }
        $ci->load->library('geetest/lib/geetestlib', $geetest_params);
        if ($_SESSION['gtserver'] == 1) {
            //服务器正常
            $result = $ci->geetestlib->success_validate($geetest_challenge, $geetest_validate, $geetest_seccode);
            if ($result) {
                return true;
            } else {
                return false;
            }
        } else {
            //服务器宕机,走failback模式
            if ($ci->geetestlib->fail_validate($geetest_challenge, $geetest_validate, $geetest_seccode)) {
                return true;
            } else {
                return false;
            }
        }
    }
}
