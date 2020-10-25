<?php


namespace WxSDK\core\model\card;


use WxSDK\core\model\Model;

class BaseInfo extends Model
{

    /**
     * @var string 必填，卡券的商户logo，建议像素为300*300
     */
    public $logo_url;
    /**
     * @var string(16)	必填，码型：
     * "CODE_TYPE_TEXT"文 本 ；
     * "CODE_TYPE_BARCODE"一维码；
     * "CODE_TYPE_QRCODE"二维码；
     * "CODE_TYPE_ONLY_QRCODE"二维码无code显示；
     * "CODE_TYPE_ONLY_BARCODE"一维码无code显示；
     * CODE_TYPE_NONE， 不显示code和条形码类型
     */
    public $code_type;
    /**
     * @var string	必填	海底捞	商户名字,字数上限为12个汉字。
     */
    public $brand_name;
    /**
     * @var string	必填	双人套餐100元兑换券	卡券名，字数上限为9个汉字。(建议涵盖卡券属性、服务及金额)。
     */
    public $title;
    /**
     * @var string	必填	Color010	券颜色。按色彩规范标注填写Color010-Color100。
     */
    public $color;
    /**
     * @var string	必填	请出示二维码	卡券使用提醒，字数上限为16个汉字。
     */
    public $notice;
    /**
     * @var string	必填	不可与其他优惠同享	卡券使用说明，字数上限为1024个汉字。
     */
    public $description;


    /**
     * @var DateInfo 必填。使用日期，有效期的信息。
     */
    public $date_info;

    /**
     * @var Sku 必须,商品信息。
     */
    public $sku;

    /**
     * @var bool	非必填	true	是否自定义Code码 。填写true或false，默认为false。
     * 通常自有优惠码系统的开发者选择 自定义Code码，并在卡券投放时带入 Code码，详情见 是否自定义Code码 。
     */
    public $use_custom_code;
    /**
     * @var string(32)	非必填	GET_CUSTOM_COD E_MODE_DEPOSIT
     * 填入 GET_CUSTOM_CODE_MODE_DEPOSIT 表示该卡券为预存code模式卡券， 须导入超过库存数目的自定义code后方可投放，
     * 填入该字段后，quantity字段须为0,须导入code 后再增加库存
     */
    public $get_custom_code_mode;
    /**
     * @var bool	非必填	true	是否指定用户领取，填写true或false 。默认为false。通常指定特殊用户群体 投放卡券或防止刷券时选择指定用户领取。
     */
    public $bind_openid;
    /**
     * @var string	非必填	40012234	客服电话。
     */
    public $service_phone;
    /**
     * @var array	非必填。门店位置poiid。 调用 POI门店管理接 口 获取门店位置poiid。具备线下门店 的商户为必填。
     */
    public $location_id_list;
    /**
     * @var bool	非必填	true	设置本卡券支持全部门店，与location_id_list互斥
     */
    public $use_all_locations;
    /**
     * @var string	非必填	立即使用	卡券顶部居中的按钮，仅在卡券状 态正常(可以核销)时显示
     */
    public $center_title;
    /**
     * @var string	非必填	立即享受优惠	显示在入口下方的提示语 ，仅在卡券状态正常(可以核销)时显示。
     */
    public $center_sub_title;
    /**
     * @var string	非必填	www.qq.com	顶部居中的url ，仅在卡券状态正常(可以核销)时显示。
     */
    public $center_url;
    /**
     * @var string	非必填	gh_86a091e50ad4@app	卡券跳转的小程序的user_name，仅可跳转该 公众号绑定的小程序 。
     */
    public $center_app_brand_user_name;
    /**
     * @var string	非必填	API/cardPage	卡券跳转的小程序的path
     */
    public $center_app_brand_pass;
    /**
     * @var string	非必填	立即使用	自定义跳转外链的入口名字。
     */
    public $custom_url_name;
    /**
     * @var string	非必填	www.qq.com	自定义跳转的URL。
     */
    public $custom_url;
    /**
     * @var string	非必填	更多惊喜	显示在入口右侧的提示语。
     */
    public $custom_url_sub_title;
    /**
     * @var string	非必填	gh_86a091e50ad4@app	卡券跳转的小程序的user_name，仅可跳转该 公众号绑定的小程序 。
     */
    public $custom_app_brand_user_name;
    /**
     * @var string	非必填	API/cardPage	卡券跳转的小程序的path
     */
    public $custom_app_brand_pass;
    /**
     * @var string	非必填	产品介绍	营销场景的自定义入口名称。
     */
    public $promotion_url_name;
    /**
     * @var string	非必填	www.qq.com	入口跳转外链的地址链接。
     */
    public $promotion_url;
    /**
     * @var string	非必填	卖场大优惠。	显示在营销入口右侧的提示语。
     */
    public $promotion_url_sub_title;
    /**
     * @var string	非必填	gh_86a091e50ad4@app	卡券跳转的小程序的user_name，仅可跳转该 公众号绑定的小程序 。
     */
    public $promotion_app_brand_user_name;
    /**
     * @var string	非必填	API/cardPage	卡券跳转的小程序的path
     */
    public $promotion_app_brand_pass;
    /**
     * @var int	非必填	1	每人可领券的数量限制,不填写默认为50。
     */
    public $get_limit;
    /**
     * @var int	非必填	100	每人可核销的数量限制,不填写默认为50。
     */
    public $use_limit;
    /**
     * @var bool	非必填	false	卡券领取页面是否可分享。
     */
    public $can_share;
    /**
     * @var bool	非必填	false	卡券是否可转赠。
     */
    public $can_give_friend;

    /**
     * BaseInfo constructor.
     * @param string $logo_url 卡券的商户logo，建议像素为300*300。
     * @param string $code_type 码型： "CODE_TYPE_TEXT"文 本 ； "CODE_TYPE_BARCODE"一维码 "CODE_TYPE_QRCODE"二维码 "CODE_TYPE_ONLY_QRCODE",二维码无code显示； "CODE_TYPE_ONLY_BARCODE",一维码无code显示；CODE_TYPE_NONE， 不显示code和条形码类型
     * @param string $brand_name 商户名字,字数上限为12个汉字。
     * @param string $title 卡券名，字数上限为9个汉字。(建议涵盖卡券属性、服务及金额)。
     * @param string $color 券颜色。按色彩规范标注填写Color010-Color100。
     * @param string $notice 卡券使用提醒，字数上限为16个汉字。
     * @param string $description 卡券使用说明，字数上限为1024个汉字。
     * @param Sku $sku 商品信息。
     * @param DateInfo $date_info 使用日期，有效期的信息。

     */
    public function __construct(string $logo_url, string $code_type, string $brand_name, string $title, string $color, string $notice, string $description, Sku $sku, DateInfo $date_info)
    {
        $this->logo_url = $logo_url;
        $this->code_type = $code_type;
        $this->brand_name = $brand_name;
        $this->title = $title;
        $this->color = $color;
        $this->notice = $notice;
        $this->description = $description;
        $this->sku = $sku;
        $this->date_info = $date_info;
    }

}