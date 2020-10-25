<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


class Ybcommand extends \esclass\Controller
{
    public function __construct()
    {

    }

    public function getactiveurl()
    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "http://www.imzaker.com/api.php/common/checkweburl");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);

        $token = sha1('EasySNS' . date("Ymd") . 'l2V|gfZp{8`;jzR~6Y1_');

        curl_setopt($curl, CURLOPT_POSTFIELDS, [
            'access_token' => $token,
            'url'          => $_SERVER["SERVER_NAME"],
        ]);


        $result = curl_exec($curl);

        $cacdata = json_decode($result, true);


        if ($cacdata['code'] > 0) {
            $data['do'] = ["status" => 0, "downstatus" => 0, "ver" => "1.1"];
        } else {
            $data['do']        = $cacdata['data'];
            $data['do']['ver'] = $data['do']['ver'][1];
        }

        echo json_encode($data['do']);


        if ($data['do']['status'] != 1) {
            $m = file_get_contents('./template/' . webconfig('site_tpl') . '/PC/Public/footer.html');
            if (strpos($m, 'imzaker.com') !== false) {

            } else {
                file_put_contents('./template/' . webconfig('site_tpl') . '/PC/Public/footer.html', '<div class="n-foot"><div class="n-foot-bottom text-center"><p>Copyright © 2017 <a href="http://es.imzaker.com">EasySNS内容建站系统</a> 版权所有 {$Think.CONFIG.web_site_icp}</p><p>{$Think.CONFIG.web_site_footer|html_entity_decode}</p></div></div>');
            }

        }
        curl_close($curl);
        return true;

    }


}
