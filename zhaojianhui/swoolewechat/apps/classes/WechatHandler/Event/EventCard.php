<?php
namespace App\WechatHandler\Event;

use App\WechatHandler\Base;
use App\WechatHandler\InterfaceHandler;

/**
 * 群发消息推送处理.
 */
class EventCard extends Base implements InterfaceHandler
{
    /**
     * 主入口方法.
     */
    public function main()
    {
        $event = trim(strtolower($this->recMessage->Event));
        switch ($event){
            case 'card_pass_check'://券通过审核
                return '卡券通过审核';
                break;
            case 'card_not_pass_check'://卡券未通过审核
                return '卡券未通过审核';
                break;
            case 'user_get_card'://用户领取卡券
                return '用户领取卡券';
                break;
            case 'user_gifting_card'://用户转赠卡券
                break;
            case 'user_del_card'://用户删除卡券
                return '用户删除卡券';
                break;
            case 'user_consume_card'://核销事件
                break;
            case 'user_pay_from_pay_cell'://微信买单事件
                break;
            case 'user_view_card'://用户点击会员卡
                break;
            case 'user_enter_session_from_card'://用户从卡券进入公众号会话
                break;
            case 'update_member_card'://会员卡内容更新
                break;
            case 'card_sku_remind'://库存报警
                break;
            case 'card_pay_order'://券点流水详情事件
                break;
            case 'submit_membercard_user_info'://会员卡激活事件推送
                break;
            default:
        }
    }
}