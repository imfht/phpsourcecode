<?php

namespace App\WechatHandler;

/**
 * 微信消息结构类，不做任何功能，仅仅为了IDE语法自动提示.
 */
class WxRecMessage
{
    //公共属性
    public $ToUserName;
    public $FromUserName;
    public $CreateTime;
    public $MsgType;
    //普通消息公共属性
    public $MsgId;
    //事件消息公共属性
    public $Event;
    //普通消息-文本
    public $Content;
    //普通消息-图片、语音、视频、小视频、地理位置、链接
    public $MediaId;
    //普通消息-视频、小视频
    public $ThumbMediaId;
    //普通消息-图片
    public $PicUrl;
    //普通消息-语音
    public $Format;
    //普通消息-语音
    public $Recognition;
    //普通消息-地理位置
    public $Location_X;
    public $Location_Y;
    public $Scale;
    public $Label;
    //普通消息-链接
    public $Title;
    public $Description;
    public $Url;
    //事件消息-扫描带参数二维码、自定义菜单
    public $EventKey;
    //事件消息-扫描带参数二维码
    public $Ticket;
    //事件消息-上报地理位置
    public $Latitude;
    public $Longitude;
    public $Precision;
    //事件消息-自定义菜单
    public $MenuID;
    //事件消息-自定义菜单(扫码推事件的事件推送、扫码推事件且弹出“消息接收中”提示框的事件推送）
    /**
     * @var \App\WechatHandler\ScanCodeInfo
     */
    public $ScanCodeInfo;//扫描信息
    //事件消息-自定义菜单（弹出系统拍照发图的事件推送、 ）
    /**
     * @var \App\WechatHandler\SendPicsInfo
     */
    public $SendPicsInfo;
    /**
     * 发送的位置信息
     * @var \App\WechatHandler\SendLocationInfo
     */
    public $SendLocationInfo;
    #-------------------群发消息属性-------------------------
    /**
     * 群发的消息ID、模板消息ID
     * @var
     */
    public $MsgID;
    //事件消息-群发、模板消息
    /**
     * 群发的结构，为“send success”或“send fail”或“err(num)”。但send success时，也有可能因用户拒收公众号的消息、系统错误等原因造成少量用户接收失败。err(num)是审核失败的具体原因，可能的情况如下：.
     err(10001), //涉嫌广告 err(20001), //涉嫌政治 err(20004), //涉嫌社会 err(20002), //涉嫌色情 err(20006), //涉嫌违法犯罪 err(20008), //涉嫌欺诈 err(20013), //涉嫌版权 err(22000), //涉嫌互推(互相宣传) err(21000), //涉嫌其他
     err(30001) // 原创校验出现系统错误且用户选择了被判为转载就不群发
     err(30002) // 原创校验被判定为不能群发
     err(30003) // 原创校验被判定为转载文且用户选择了被判为转载就不群发
     * 模板消息，发送状态为成功
     *
     * 券点流水详情事件:
     * 本次订单号的状态,ORDER_STATUS_WAITING 等待支付 ORDER_STATUS_SUCC 支付成功 ORDER_STATUS_FINANCE_SUCC 加代币成功
     * ORDER_STATUS_QUANTITY_SUCC 加库存成功 ORDER_STATUS_HAS_REFUND 已退币 ORDER_STATUS_REFUND_WAITING 等待退币确认
     * ORDER_STATUS_ROLLBACK 已回退,系统失败 ORDER_STATUS_HAS_RECEIPT 已开发票
     *
     * @var
     */
    public $Status;
    /**
     * tag_id下粉丝数；或者openid_list中的粉丝数.
     *
     * @var
     */
    public $TotalCount;
    /**
     * 过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数，原则上，FilterCount = SentCount + ErrorCount.
     *
     * @var
     */
    public $FilterCount;
    /**
     * 发送成功的粉丝数.
     *
     * @var
     */
    public $SentCount;
    /**
     * 发送失败的粉丝数.
     *
     * @var
     */
    public $ErrorCount;
    /**
     * @var \App\WechatHandler\WxRecCopyrightCheckResult
     */
    public $CopyrightCheckResult;
    #---------------------卡券属性部分-----------------------
    /**
     * 卡券ID
     * @var
     */
    public $CardId;
    /**
     * 审核不通过原因
     * @var
     */
    public $RefuseReason;
    /**
     * 是否为转赠领取，1代表是，0代表否。
     * @var
     */
    public $IsGiveByFriend;
    /**
     * 1、当IsGiveByFriend为1时填入的字段，表示发起转赠用户的openid
     * 2、接收卡券用户的openid
     * @var
     */
    public $FriendUserName;
    /**
     * code序列号
     * @var
     */
    public $UserCardCode;
    /**
     * 为保证安全，微信会在转赠发生后变更该卡券的code号，该字段表示转赠前的code.
     * @var
     */
    public $OldUserCardCode;
    /**
     * 1、领取场景值，用于领取渠道数据统计。可在生成二维码接口及添加Addcard接口中自定义该字段的字符串值。
     * 2、开发者发起核销时传入的自定义参数，用于进行核销渠道统计
     * 3、商户自定义二维码渠道参数，用于标识本次扫码打开会员卡来源来自于某个渠道值的二维码
     * @var
     */
    public $OuterStr;
    /**
     * 用户删除会员卡后可重新找回，当用户本次操作为找回时，该值为1，否则为0
     * @var
     */
    public $IsRestoreMemberCard;
    /**
     * 是否转赠退回，0代表不是，1代表是。
     * @var
     */
    public $IsReturnBack;
    /**
     * 是否是群转赠
     * @var
     */
    public $IsChatRoom;
    /**
     * 核销来源。支持开发者统计API核销（FROM_API）、公众平台核销（FROM_MP）、卡券商户助手核销（FROM_MOBILE_HELPER）（核销员微信号）
     * @var
     */
    public $ConsumeSource;
    /**
     * 门店ID，当前卡券核销的门店ID（只有通过卡券商户助手和买单核销时才会出现）
     * @var
     */
    public $LocationId;
    /**
     * 门店名称，当前卡券核销的门店名称（只有通过自助核销和买单核销时才会出现该字段）
     * @var
     */
    public $LocationName;
    /**
     * 核销该卡券核销员的openid（只有通过卡券商户助手核销时才会出现）
     * @var
     */
    public $StaffOpenId;
    /**
     * 自助核销时，用户输入的验证码
     * @var
     */
    public $VerifyCode;
    /**
     * 自助核销时，用户输入的备注金额
     * @var
     */
    public $RemarkAmount;
    /**
     * 微信支付交易订单号（只有使用买单功能核销的卡券才会出现）
     * @var
     */
    public $TransId;
    /**
     * 实付金额，单位为分
     * @var
     */
    public $Fee;
    /**
     * 应付金额，单位为分
     * @var
     */
    public $OriginalFee;
    /**
     * 变动的积分值。
     * @var
     */
    public $ModifyBonus;
    /**
     * 变动的余额值。
     * @var
     */
    public $ModifyBalance;
    /**
     * 报警详细信息
     * @var
     */
    public $Detail;
    /**
     * 本次推送对应的订单号
     * @var
     */
    public $OrderId;
    /**
     * 购买券点时，支付二维码的生成时间
     * @var
     */
    public $CreateOrderTime;
    /**
     * 购买券点时，实际支付成功的时间
     * @var
     */
    public $PayFinishTime;
    /**
     * 支付方式，一般为微信支付充值
     * @var
     */
    public $Desc;
    /**
     * 剩余免费券点数量
     * @var
     */
    public $FreeCoinCount;
    /**
     * 剩余付费券点数量
     * @var
     */
    public $PayCoinCount;
    /**
     * 本次变动的免费券点数量
     * @var
     */
    public $RefundFreeCoinCount;
    /**
     * 本次变动的付费券点数量
     * @var
     */
    public $RefundPayCoinCount;
    /**
     * 所要拉取的订单类型
    ORDER_TYPE_SYS_ADD 平台赠送券点 ORDER_TYPE_WXPAY 充值券点 ORDER_TYPE_REFUND 库存未使用回退券点 ORDER_TYPE_REDUCE 券点兑换库存 ORDER_TYPE_SYS_REDUCE 平台扣减
     * @var
     */
    public $OrderType;
    /**
     * 系统备注，说明此次变动的缘由，如开通账户奖励、门店奖励、核销奖励以及充值、扣减。
     * @var
     */
    public $Memo;
    /**
     * 所开发票的详情
     * @var
     */
    public $ReceiptInfo;
    #-------------------微信门店相关属性----------------------
    /**
     * 商户自己内部ID，即字段中的sid
     * @var
     */
    public $UniqId;
    /**
     * 微信的门店ID，微信内门店唯一标示ID
     * @var
     */
    public $PoiId;
    /**
     * 审核结果，成功succ 或失败fail
     * @var
     */
    public $Result;
    /**
     * 成功的通知信息，或审核失败的驳回理由
     * @var
     */
    public $msg;
    #-----------------客服会话相关属性------------------
    /**
     * 客服账号
     * @var
     */
    public $KfAccount;
    /**
     * 转接客服-原客服账号
     * @var
     */
    public $FromKfAccount;
    /**
     * 转接客服-新客服账号
     * @var
     */
    public $ToKfAccount;
    #-------------WIFI连网后下发消息-----------------
    /**
     * 连网时间（整型）
     * @var
     */
    public $ConnectTime;
    /**
     * 系统保留字段，固定值
     * @var
     */
    public $ExpireTime;
    /**
     * 系统保留字段，固定值
     * @var
     */
    public $VendorId;
    /**
     * 门店ID，即shop_id
     * @var
     */
    public $ShopId;
    /**
     * 连网的设备无线mac地址，对应bssid
     * @var
     */
    public $DeviceNo;
}

/**
 * 扫描信息
 * @package App\WechatHandler
 */
class ScanCodeInfo{
    /**
     * 扫描类型，一般是qrcode
     * @var
     */
    public $ScanType;
    /**
     * 扫描结果，即二维码对应的字符串信息
     * @var
     */
    public $ScanResult;
}

/**
 * 发送的图片信息
 * @package App\WechatHandler
 */
class SendPicsInfo{
    /**
     * 发送的图片数量
     * @var
     */
    public $Count;
    /**
     * 图片列表
     * @var
     */
    public $PicList;
}

/**
 * 发送的位置信息
 * @package App\WechatHandler
 */
class SendLocationInfo{
    /**
     * X坐标信息
     * @var
     */
    public $Location_X;
    /**
     * Y坐标信息
     * @var
     */
    public $Location_Y;
    /**
     * 精度，可理解为精度或者比例尺、越精细的话 scale越高
     * @var
     */
    public $Scale;
    /**
     * 地理位置的字符串信息
     * @var
     */
    public $Label;
    /**
     * 朋友圈POI的名字，可能为空
     * @var
     */
    public $Poiname;
}
class WxRecCopyrightCheckResult
{
    /**
     * 图文总数.
     *
     * @var
     */
    public $Count;
    /**
     * 各个单图文校验结果.
     *
     * @var
     */
    public $ResultList;
    /**
     * 1-未被判为转载，可以群发，2-被判为转载，可以群发，3-被判为转载，不能群发.
     *
     * @var
     */
    public $CheckState;
}
