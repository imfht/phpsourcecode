<?php
/**
 * 所属项目 OnePlus.
 * 开发者: 陈一枭
 * 创建日期: 5/11/14
 * 创建时间: 9:44 PM
 * 版权所有 嘉兴想天信息科技有限公司(www.ourstu.com)
 */

namespace Common\Model;


/**内容处理模型，专门用于预处理各类文本
 * Class ContentHandlerModel
 * @package Common\Model
 * @auth 陈一枭
 */
class ContentHandlerModel {

    /**处理@
     * @auth 陈一枭
     */
    public function handleAtWho($content,$url='',$app_name='',$escap_first=false){
        $uids = get_at_uids($content);

        $uids = array_unique($uids);
        $sender=query_user(array('nickname'));
        $first=true;
        foreach ($uids as $uid) {
            if($escap_first && $first){
                $first=false;
                continue;
            }
            //$user = D('User/UcenterMember')->find($uid);
            $title = $sender['nickname'] . '@了您';
            $message = '评论内容：' . mb_substr(op_t( $content),0,50,'utf-8');
            if($url==''){//如果未设置来源的url，则自动跳转到来源页面
                $url = $_SERVER['HTTP_REFERER'];
            }

            D('Common/Message')->sendMessage($uid, $message, $title, $url, get_uid(), 0, $app_name);
        }
    }
} 