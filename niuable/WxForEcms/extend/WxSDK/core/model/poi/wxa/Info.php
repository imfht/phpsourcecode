<?php
namespace WxSDK\core\model\poi\wxa;

class Info
{
    public $first_catid;// 476, //get_store_category接口获取的一级类目id
    public $second_catid;// 477, //get_store_category接口获取的二级类目id
    public $qualification_list;//"RTZgKZ386yFn5k...",类目相关证件的临时素材mediaid 如果second_catid对应的sensitive_type为1 ，则qualification_list字段需要填 支持0~5个mediaid，例如mediaid1 或 mediaid2
    public $headimg_mediaid;// "RTZgKZ386...",头像 --- 临时素材mediaid mediaid 用现有的media/upload接口得到的
    public $nickname; // "hardenzhang308",
    public $intro; // "hardenzhangtest",
    public $org_code;//": "",营业执照或组织代码证 --- 临时素材mediaid 如果返回错误码85024，则该字段必填，否则不用填
    public $other_files;//补充材料 --- 临时素材mediaid 如果返回错误码85024，则可以选填 支持0~5个mediaid，例如mediaid1 或 mediaid2
}

