<?php
namespace App\Controller\Admin;
use App\BaseController\AdminBaseController as Base;

/**
 * 图片素材管理
 * @package App\Controller\Admin
 */
class WxMediaImage extends Base
{
    /**
     * @var \App\Model\WxMedia
     */
    private $wxMediaModel;
    /**
     * @var \App\Service\WxMedia
     */
    private $wxMediaSer;
    /**
     * 构造函数
     * @param \Swoole $swoole
     */
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $this->addBreadcrumb('素材管理', '/Admin/WxMediaImage/index');
        $this->wxMediaModel = model('WxMedia');
        $this->wxMediaSer = new \App\Service\WxMedia();
    }

    /**
     * 图片素材列表
     */
    public function index()
    {
        $this->setSeoTitle('图片素材管理');
        $this->addBreadcrumb('图片素材管理', $this->currentUrl);

        $this->display();
    }

    /**
     * 获取图片素材列表
     * @return array
     */
    public function getPageList()
    {
        //绘制计数器。
        $draw = (int) ($this->request->request['draw'] ?? 0);
        $where = [
            'select' => '`mediaId`,`wxMediaId`,`title`,`intro`,`filePath`,`remoteUrl`,`wxRemoteUrl`,`statusIs`',
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
                    $where['order'] = '`addTime` '.$order[0]['dir'];
                    break;
                default:
                    $where['order'] = '`mediaId` DESC';
            }
        }

        $data  = [
            'draw' => $draw,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ];
        $data['recordsTotal'] = $this->wxMediaModel->count($where);
        $list = $this->wxMediaModel->getList($where);
        if ($list){
            foreach ($list as $k => $v){
                $v['DT_RowId'] = $v['mediaId'];
                $list[$k] = $v;
            }
        }
        $data['data'] = $list;
        $data['recordsFiltered'] = count($list);

        return $data;
    }

    /**
     * 新增图片素材
     */
    public function add()
    {
        try {
            $this->upload->sub_dir = 'images';
            $uprs = $this->upload->save('mediafile', null, ['gif','jpeg','jpg','png']);
            if (!$uprs){
                throw new \Exception($this->upload->error_msg());
            }
            $pathinfo = pathinfo($uprs['url']);
            $imgData = [
                'title' => $this->request->post['title'] ?? '',
                'intro' => $this->request->post['intro'] ?? '',
                'mediaType' => $this->request->post['mediaType'] ?? '',
                'fileName' => $pathinfo['basename'],
                'filePath' => $uprs['url'],
                'fileSize' => $uprs['size'],
                'fileExt' => $uprs['type'],
            ];
            $rs = $this->wxMediaSer->saveNormal($imgData);
            if ($rs) {
                return $this->showMsg('success', '新增图片素材成功', '/Admin/WxTemplate/index');
            }
            throw new \Exception('新增图片素材失败');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 同步线上数据
     */
    public function syncOnline()
    {
        try {
            $wxMediaSer = new \App\Service\WxMedia();
            $rs = $wxMediaSer->syncOnline('image');
            if ($rs) {
                return $this->showMsg('success', '同步成功', '/Admin/WxMedia/index');
            }
            throw new \Exception('同步失败');
        } catch (\Exception $e) {
            //throw new \Exception($e->getMessage());
            return $this->showMsg('error', $e->getMessage());
        }
    }
}