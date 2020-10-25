<?php
namespace WxSDK\core\model\tpl;

use WxSDK\core\model\Model;

class TplModel extends Model
{
    public $touser;
    public $topcolor;
    public $template_id;
    public $url='';
    public $miniprogram;
    public $data;
    function __construct(string $toUser, string $templateId, TplDataArray $tplData
        , $url='', string $topColor ="#ffffff", Miniprogram $miniProgram = NULL){
        $this->data = $tplData->data;
        $this->touser = $toUser;
        $this->template_id = $templateId;
        $this->url = $url;
        $this->topcolor = $topColor;
        $this->miniprogram = $miniProgram;
    }
}

