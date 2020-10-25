<?php
namespace App\Service;
/**
 * 模板消息服务类
 * @package App\Service
 */
class WxTemplate
{
    /**
     * @var \App\Model\WxTemplate
     */
    private $wxTemplateModel;
    /**
     * 模板关键词usekey列表
     * @var array
     */
    private $usekeyList = [
        'score_change' => '积分变更提醒',
        'order_submit' => '订单提交提醒',
        'order_cancel' => '订单取消提醒',
        'order_refund' => '订单退货提醒',
    ];

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->wxTemplateModel = model('WxTemplate');
    }

    /**
     * 获取KEY列表
     * @return array
     */
    public function getKeyList()
    {
        return $this->usekeyList;
    }
    /**
     * 获取模板关键词名称
     * @param string $usekey
     */
    public function getKeyName($usekey = '')
    {
        return isset($this->usekeyList[$usekey]) ? $this->usekeyList[$usekey] : '';
    }

    /**
     * 设置使用场景
     * @param $templateId
     * @param $usekey
     */
    public function setKey($templateId, $usekey)
    {
        $templateData = $this->wxTemplateModel->getone(['templateId'=>$templateId, 'isDel'=>0]);
        if (empty($templateData)){
            throw new \Exception('模板数据不存在');
        }
        if ($usekey){
            if ($templateData['usekey'] == $usekey){
                throw new \Exception('该模板已设置该场景');
            }
            $findOther = $this->wxTemplateModel->getone(['usekey'=>$usekey,'isDel'=>0]);
            if ($findOther){
                throw new \Exception('该场景已被占用');
            }
        }
        return $this->wxTemplateModel->set($templateId, ['usekey'=>$usekey]);
    }
    /**
     * 同步拉取线上模板消息列表
     */
    public function syncOnline()
    {
        $templateList = \Swoole::$php->easywechat->notice->getPrivateTemplates();
        $templateList = $templateList->toArray();
        if ($templateList['template_list']){
            $this->wxTemplateModel->start();
            try{
                $this->wxTemplateModel->sets(['isDel'=>1], ['isDel' => 0]);
                foreach ($templateList['template_list'] as $templateData){
                    $wxTemplateId = $templateData['template_id'];
                    $data = [
                        'wxTemplateId' => $templateData['template_id'],
                        'title' => $templateData['title'],
                        'primaryIndustry' => $templateData['primary_industry'] ?? '',
                        'deputyIndustry' => $templateData['deputy_industry'] ?? '',
                        'content' => $templateData['content'] ?? '',
                        'example' => $templateData['example'] ?? '',
                        'keywords' => json_encode([]),
                        'isDel' => 0,
                    ];
                    $findData = $this->wxTemplateModel->getone(['wxTemplateId'=>$wxTemplateId, 'select'=>'templateId']);
                    if ($findData){
                        $this->wxTemplateModel->set($findData['templateId'], $data);
                    }else{
                        $data['addUserId'] = (new \Swoole\Auth())->getUid();
                        $data['addTime'] = time();
                        $this->wxTemplateModel->put($data);
                    }
                }
                $this->wxTemplateModel->commit();
                return true;
            }catch (\Exception $e){
                $this->wxTemplateModel->rollback();
                throw new \Exception($e->getMessage());
            }
        }
        throw new \Exception('模板列表为空');
    }

    /**
     * 设置账号所属行业
     */
    public function setIndustry($industry1, $industry2)
    {
        return \Swoole::$php->easywechat->notice->setIndustry($industry1, $industry2);
    }

    /**
     * 根据模板库中的编号添加模板
     * @param $shortId
     */
    public function add($shortId)
    {
        \Swoole::$php->easywechat->notice->addTemplate($shortId);
        $this->syncOnline();
        return true;
    }

    /**
     * 设置模板启用状态
     * @param $templateId
     * @param $status
     * @throws \Exception
     */
    public function setStatus($templateId, $status)
    {
        $templateData = $this->wxTemplateModel->getone(['templateId'=>$templateId, 'select'=>'templateId,wxTemplateId']);
        if (empty($templateData)){
            throw new \Exception('模板数据不存在');
        }
        return $this->wxTemplateModel->set($templateData['templateId'], ['statusIs'=>$status]);
    }

    /**
     * 删除
     * @param $templateId
     */
    public function del($templateId)
    {
        $templateData = $this->wxTemplateModel->getone(['templateId'=>$templateId, 'isDel'=>0, 'select'=>'templateId,wxTemplateId']);
        if (empty($templateData)){
            throw new \Exception('模板数据不存在');
        }
        \Swoole::$php->easywechat->notice->deletePrivateTemplate($templateData['wxTemplateId']);
        $this->wxTemplateModel->set($templateData['templateId'], ['isDel'=>1]);
        return true;
    }

    /**
     * 发送模板消息
     * @param $usekey
     * @param array $templateData
     */
    public function send($usekey, $defineData = [])
    {
        $templateData = $this->wxTemplateModel->getone(['usekey'=>$usekey, 'isDel'=>0, 'select'=>'templateId,wxTemplateId,statusIs']);
        if (empty($templateData)){
            throw new \Exception('模板数据不存在');
        }
        if ($templateData['statusIs'] != 1){
            throw new \Exception('该模板消息已禁用');
        }
        if (empty($defineData['touser'])){
            throw new \Exception('请设置接收模板消息用户');
        }
        if (empty($defineData['data'])){
            throw new \Exception('请设置模板消息数据');
        }
        $sendData = [
            'touser' => $defineData['touser'],
            'template_id' => $templateData['wxTemplateId'],
            'data' => $defineData['data'],
        ];
        if (!empty($defineData['url'])){
            $sendData['url'] = $defineData['url'];
        }
        if (!empty($defineData['miniprogram'])){
            $sendData['miniprogram'] = $defineData['miniprogram'];
        }
        \Swoole::$php->easywechat->notice->send($sendData);

        return true;
    }
}