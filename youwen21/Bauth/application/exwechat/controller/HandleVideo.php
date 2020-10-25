<?php
namespace app\exwechat\controller;

/**
 * 微信事件消息－控制器
 *
 */
class HandleVideo extends AbstractHandle
{

    private $msg;
    public function handle($arrayMsg='')
    {
        $this->msg = empty($arrayMsg) ? $this->exRequest->getMsg() : $arrayMsg;
        $this->_saveToDB();
        $this->response('你上传了视频: '.$this->msg['MediaId']);
        exit; //阻止DEBUG信息输出
    }

    // 保存到数据
    // 对应数据库为 msg_text
    private function _saveToDB()
    {
        $data = [];
        $data['id'] = '';
        $data['status'] = 1;

        foreach ($this->msg as $key => $value) {
            if(in_array($key, ['PicUrl', 'Format', 'ThumbMediaId'])){
                $data['other'] = $value;
            }else{
                $data[$key] = $value;
            }
        }

        $ret = db('we_msg_media')->insert($data);
        return $ret;
    }
}
