<?php
/**
 * sendcloud.php
 *
 * @copyright  2018 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2018-01-04 17:04
 * @modified   2018-01-04 17:04
 */

namespace Mail;

class SendCloud
{
    public function send()
    {
        $sendCloudClient = new \SendCloudApi();
        $data = array(
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'plain' => $this->text,
            'html' => $this->html,
            'sender' => $this->sender,
            'reply_to' => $this->reply_to
        );
        $response = $sendCloudClient->send($data);
        return $response;
    }
}