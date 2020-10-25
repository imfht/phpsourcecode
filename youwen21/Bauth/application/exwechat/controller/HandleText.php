<?php
namespace app\exwechat\controller;

/**
 * 微信事件消息－控制器
 *
 */
class HandleText extends AbstractHandle
{

    private $msg;
    public function handle($arrayMsg='')
    {
        $this->msg = empty($arrayMsg) ? $this->exRequest->getMsg() : $arrayMsg;
        $bool = $this->_saveToDB();

        // 用户场景捕获
        $scene = new SceneCatch();
        $sceneValue = $scene->check($this->msg['Content'], $this->msg['FromUserName'], 'chat');
        if(!is_bool($sceneValue)){
            $this->response($sceneValue);
        }elseif(true === $sceneValue){
            echo (new \youwen\exwechat\exXMLMaker())->createText('场景值操作成功');
            exit();
        }

        // 优先关键词
        $ret1 = $this->_priorityKeyword($this->msg['Content']);
        if(!$ret1){
            // 数据库关键词
            $ret2 = $this->_dbKeyword($this->msg['Content']);
            if(!$ret2){
                // 默认消息
                $this->_defaultReply();
            }
        }
        exit; //阻止DEBUG信息输出
    }

    // 保存到数据
    // 对应数据库为 msg_text
    private function _saveToDB()
    {
        $data = $this->msg;
        $data['id'] = '';
        $data['status'] = 1;
        if(isset($data['Encrypt'])) unset($data['Encrypt']);
        $ret = db('we_msg_text')->insert($data);
        return $ret;
    }
    // 设置和删除场景值
    private function _sceneCheck()
    {
        $keyWord = $this->msg['Content'];
        // 设置场景
        if(substr($keyWord, 0, 8) == 'setScene'){
            $value = substr($keyWord, 9);
            if(false !== strpos($value, ':')){
                $arr = explode(':', $value);
                $this->setScene($this->msg['FromUserName'], $arr[0], $arr[1]);
            }else{
                $this->setScene($this->msg['FromUserName'], $value);
            }
            exit();
        }
    }
    // 网站业务自定义的最高优先级关键字，个人一般情况会有一个开发关键字
    // 进入某聊天场景的关键字等
    private function _priorityKeyword($keyWord='')
    {
        $keyWord = trim($keyWord);
        switch ($keyWord) {
            //部分自定义优先关键字
            case 'subscribe':$this->response('subscribe');
                break;
            case 'openid':$this->response($this->msg['FromUserName']);
                break;
            case 'scene':
            case 'getScene':
                $ret = $this->getScene($this->msg['FromUserName']);
                $msg = $ret ? $ret['sceneValue'] : '无场景';
                $this->response($msg);
                break;
            default:
                break;
        }
        return false;
    }
    // 数据库中的关键字
    private function _dbKeyword($keyWord='')
    {
        return false;
    }
    // 上面两种情况关键字都没有匹配成功的话
    // 执行默认的回复，如接入聊天机器人，介绍公司业务，公众号使用方法等
    private function _defaultReply()
    {
        $this->response($this->msg['Content']);
    }
}
