<?php

namespace app\exwechat\controller;

use youwen\exwechat\api\message\template;

/**
 * 客服案例
 */
class Demotemplate
{

    public function get_template_id()
    {
        $class = new template($_GET['token']);
        $ret = $class->get_template_id();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function send()
    {
        $data = [];
        $data['touser'] = 'o5dxUt4XMyD4R9jLrkeKhhFnMYKA';
        $data['template_id'] = 'Q6BXyVjhmstxeCrzO8uOGpRQOqGeVZ-K8yyh_eVLZyg';
        $data['url'] = 'http://www.exwechat.com';
        $data['data'] = 'haha';
        $class = new template($_GET['token']);
        $ret = $class->send($data);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function set_industry()
    {
        $data = [];
        $class = new template($_GET['token']);
        
        // $data['qRnM2JNwe7eyKBHAwyvuRMaHqKzhV8p4K74n9sz9iTY'] = 1;
        // $data[1] = 'qRnM2JNwe7eyKBHAwyvuRMaHqKzhV8p4K74n9sz9iTY';
        $data['industry_id1'] = 1;
        $ret = $class->set_industry($data);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function get_industry()
    {
        $class = new template($_GET['token']);
        $ret = $class->get_industry();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function get_all_template()
    {
        $class = new template($_GET['token']);
        $ret = $class->get_all_template();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

}
