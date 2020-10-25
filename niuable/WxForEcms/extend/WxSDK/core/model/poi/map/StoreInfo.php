<?php
namespace WxSDK\core\model\poi\map;

use WxSDK\core\model\Model;

class StoreInfo extends Model
{
    public $map_poi_id;//从腾讯地图换取的位置点id， 即search_map_poi接口返回的sosomap_poi_uid字段
    public $pic_list;//门店图片，可传多张图片; pic_list 字段是一个 json
    public $contract_phone;//联系电话
    public $hour;//营业时间，格式11:11-12:12
    public $credential;//经营资质证件号
    public $company_name;//主体名字 临时素材mediaid 如果复用公众号主体，则company_name为空 如果不复用公众号主体，则company_name为具体的主体名字
    public $qualification_list;//相关证明材料   临时素材mediaid 不复用公众号主体时，才需要填 支持0~5个mediaid，例如mediaid1 或 mediaid2
    public $card_id;//卡券id，如果不需要添加卡券，该参数可为空 目前仅开放支持会员卡、买单和刷卡支付券，不支持自定义code，需要先去公众平台卡券后台创建cardid
    public $poi_id;//如果是从门店管理迁移门店到门店小程序，则需要填该字段
}

