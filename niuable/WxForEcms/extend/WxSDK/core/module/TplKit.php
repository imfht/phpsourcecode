<?php
namespace WxSDK\core\module;

use WxSDK\core\common\IApp;
use WxSDK\resource\Config;
use WxSDK\core\model\tpl\Miniprogram;
use WxSDK\core\utils\Tool;
use WxSDK\core\model\Model;
use WxSDK\core\model\tpl\TplDataArray;
use WxSDK\core\model\tpl\TplModel;
use WxSDK\Request;
use WxSDK\Url;

class TplKit
{
    /**
     *
     * @param IApp $App
     * @param string $industry_id1 主行业
     * @param string $industry_id2 副行业
     * @return \WxSDK\core\common\Ret
     */
    public static function setIndustry(IApp $App, string $industry_id1, string $industry_id2) {
        $model = new Model(array(
            "industry_id1" => $industry_id1,
            "industry_id2" => $industry_id2,
        ));
        
        $request = new Request($App, $model, new Url(Config::$tpl_set_industry));
        return $request->run();
    }
    
    /**
     * 
     * @param IApp $App
     * @return \WxSDK\core\common\Ret data数组：
     * {
            * "primary_industry":{"first_class":"运输与仓储","second_class":"快递"},
            * "secondary_industry":{"first_class":"IT科技","second_class":"互联网|电子商务"}
        * }
     */
    public static function getIndustry(IApp $App) {
        $request = new Request($App, new Model(), new Url(Config::$tpl_get_industry));
        return $request->run();
    }
    
    /**
     * 从模板库中导入
     * @param IApp $App
     * @param string $template_id_short
     * @return \WxSDK\core\common\Ret
     */
    public static function addTpl(IApp $App, string $template_id_short) {
        $model = new Model(array(
            "template_id_short" => $template_id_short,
        ));
        
        $request = new Request($App, $model, new Url(Config::$tpl_add_import));
        return $request->run();
    }
    /**
     * 删除模板
     * @param IApp $App
     * @param string $template_id
     * @return \WxSDK\core\common\Ret
     */
    public static function deleteTpl(IApp $App, string $template_id) {
        $model = new Model(array(
            "template_id" => $template_id,
        ));
        
        $request = new Request($App, $model, new Url(Config::$tpl_delete));
        return $request->run();
    }
    /**
     * 
     * @param IApp $App
     * @param string $touserOpenId
     * @param string $template_id
     * @param TplDataArray $data 转为json后形如：
     * {
                   * "first": {
                       * "value":"恭喜你购买成功！",
                       * "color":"#173177"
                   * },
                   * "keyword1":{
                       * "value":"巧克力",
                       * "color":"#173177"
                   * }
           * }
     * @param string $url
     * @param Miniprogram $miniprogram
     * @return \WxSDK\core\common\Ret
     */
    public static function sendMsg(IApp $App, TplModel $model) {
        $request = new Request($App, $model, new Url(Config::$tpl_send_msg));
        return $request->run();
    }
    
    /**
     * 获取列表
     *
     * @param IApp $App
     * @return \WxSDK\core\common\Ret data参数
     * {    
             * "template_list": [{
              * "template_id": "iPk5sOIt5X_flOVKn5GrTFpncEYTojx6ddbt8WYoV5s",
              * "title": "领取奖金提醒",
              * "primary_industry": "IT科技",
              * "deputy_industry": "互联网|电子商务",
              * "content": "{ {result.DATA} }\n\n领奖金额:{ {withdrawMoney.DATA} }\n领奖  时间:    { {withdrawTime.DATA} }\n银行信息:{ {cardInfo.DATA} }\n到账时间:  { {arrivedTime.DATA} }\n{ {remark.DATA} }",
              * "example": "您已提交领奖申请\n\n领奖金额：xxxx元\n领奖时间：2013-10-10 12:22:22\n银行信息：xx银行(尾号xxxx)\n到账时间：预计xxxxxxx\n\n预计将于xxxx到达您的银行卡"
           * }]
        * }
     */
    public static function getList(IApp $App) {
        $request = new Request($App, new Model(), new Url(Config::$tpl_get_list));
        return $request->run();
    }
}

