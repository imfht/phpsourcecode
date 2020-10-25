<?php
namespace App\Controller\Admin;
use App\BaseController\AdminBaseController as Base;

class WxTemplate extends Base
{
    /**
     * @var \App\Model\WxTemplate
     */
    private $wxTemplateModel;

    /**
     * @var \App\Service\WxTemplate
     */
    private $wxTemplateSer;

    /**
     * 构造函数
     * @param \Swoole $swoole
     */
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $this->addBreadcrumb('模板消息管理', '/Admin/WxTemplate/index');
        $this->wxTemplateModel = model('WxTemplate');
        $this->wxTemplateSer = new \App\Service\WxTemplate();
    }

    /**
     * 模板消息列表
     */
    public function index()
    {
        $this->setSeoTitle('模板消息管理');
        $this->addBreadcrumb('模板消息管理', $this->currentUrl);
        //使用key列表
        $keyList = $this->wxTemplateSer->getKeyList();
        $this->assign('keyList', $keyList);
        $this->display();
    }

    /**
     * 获取模板消息列表
     * @return array
     */
    public function getPageList()
    {
        //绘制计数器。
        $draw = (int) ($this->request->request['draw'] ?? 0);
        $where = [
            'select' => '`templateId`,`usekey`,`wxTemplateId`,`title`,`primaryIndustry`,`deputyIndustry`,`content`,`example`,`statusIs`',
        ];
        //开始位置
        $start = (int) ($this->request->request['start'] ?? 0);
        //长度
        $length = (int) ($this->request->request['length'] ?? 10);
        $where['limit'] = $start . ',' . $length;
        $where['where'] = 'isDel=0';
        //搜索关键字
        $keyword = $this->request->request['search']['value'] ?? '';
        if ($keyword){
            $where['where'] = (isset($where['where']) && $where['where'] ? ' AND ' : '') . "`title` like '$keyword%'";
        }
        //排序字段
        $order = $this->request->request['order'] ?? [];
        if ($order){
            switch ($order[0]['column']){
                case 1:
                    $where['order'] = '`usekey` '.$order[0]['dir'];
                    break;
                default:
                    $where['order'] = '`templateId` DESC';
            }
        }

        $data  = [
            'draw' => $draw,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ];
        $data['recordsTotal'] = $this->wxTemplateModel->count($where);
        $list = $this->wxTemplateModel->getList($where);
        if ($list){
            foreach ($list as $k => $v){
                $v['DT_RowId'] = $v['templateId'];
                $v['keyName'] = $this->wxTemplateSer->getKeyName($v['usekey']);
                $v['content'] = str_replace(PHP_EOL, "<br>", $v['content']);
                $v['example'] = str_replace(PHP_EOL, "<br>", $v['example']);
                $list[$k] = $v;
            }
        }
        $data['data'] = $list;
        $data['recordsFiltered'] = count($list);

        return $data;
    }

    /**
     * 获取模板消息数据
     * @return bool
     */
    public function get()
    {
        try {
            $id   = $this->request->get['id'] ?? 0;
            $data = $this->wxTemplateModel->getone([
                'where'=>"`templateId`=$id",
                'select' => "`templateId`,`usekey`,`wxTemplateId`",
            ]);
            return $this->showMsg('success', '获取成功', '', $data);
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
    /**
     * 同步线上模板
     * @return bool
     */
    public function syncOnline()
    {
        try {
            $rs = $this->wxTemplateSer->syncOnline();
            if ($rs){
                return $this->showMsg('success', '同步所有用户成功');
            }
            throw new \Exception('同步所有用户失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 新增模板
     * @return bool
     */
    public function add()
    {
        try {
            $templateIdShort              = $this->request->post['templateIdShort'];
            $rs = $this->wxTemplateSer->add($templateIdShort);
            if ($rs) {
                return $this->showMsg('success', '新增模板成功', '/Admin/WxTemplate/index');
            }
            throw new \Exception('新增模板失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
    /**
     * 设置使用场景
     * @return bool
     */
    public function setKey()
    {
        try {
            $id   = $this->request->post['id'] ?? 0;
            $usekey = $this->request->post['usekey'] ?? '';

            $rs = $this->wxTemplateSer->setKey($id, $usekey);
            if ($rs){
                return $this->showMsg('success', '设置使用场景成功');
            }
            throw new \Exception('设置使用场景失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 设置模板启用状态
     * @return bool
     */
    public function setStatus()
    {
        try {
            $id   = $this->request->post['id'] ?? 0;
            $status = (int) $this->request->post['status'] ?? 0;
            $actName = $status == 1 ? '启用' : '禁用';
            if (!$id){
                throw new \Exception('请指定要'.$actName.'的模板');
            }
            $rs = $this->wxTemplateSer->setStatus($id, $status);
            if ($rs){
                return $this->showMsg('success', $actName . '成功');
            }
            throw new \Exception($actName . '失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 删除
     * @return bool
     */
    public function del()
    {
        try {
            $id   = $this->request->post['id'] ?? 0;
            if (!$id){
                throw new \Exception('请指定要删除的模板消息');
            }
            if ($this->wxTemplateSer->del($id)){
                return $this->showMsg('success', '删除成功');
            }
            throw new \Exception('删除失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
}