<?php

/**
 * 视频素材
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\wechat\admin;


class MaterialVideoAdmin extends \app\wechat\admin\WechatAdmin {


    protected $_model = 'WechatMaterialVideo';
    private $material = null;

    public function __construct() {
        parent::__construct();
        $this->material = $this->wechat->material;
    }

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return array(
            'info' => array(
                'name' => '视频素材',
                'description' => '管理微信素材库素材',
            ),
            'fun' => [
                'index' => true
            ]
        );
    }

    /**
     * 同步素材
     */
    public function sync() {
        $stats = $this->material->stats();
        $imageCount = $stats->video_count;
        $curPage = request('post', 'page', 1);
        if ($curPage == 1) {
            $status = target($this->_model)->where([
                '_sql' => 'material_id > 0'
            ])->delete();
            if (!$status) {
                $this->error('清空本地数据失败!');
            }
        }
        $page = 20;
        $pagesObj = new \dux\lib\Pagination($imageCount, $curPage, $page);
        $pageInfo = $pagesObj->build();
        if ($curPage > $pageInfo['page']) {
            $this->error('同步完成!', url('index'));
        }
        $offset = $pageInfo['offset'];
        $data = $this->material->lists('video', $offset, $page);
        $list = $data->item;
        foreach ($list as $key => $vo) {
            $info = $this->getVideoInfo($vo['media_id']);
            if(empty($info['down_url'])) {
                $this->error('视频信息获取失败!');
            }
            $data = [
                'media_id' => $vo['media_id'],
                'url' => $info['down_url'],
                'title' => $info['title'],
                'description' => $info['description'],
                'time' => $vo['update_time']
            ];
            if (!target($this->_model)->add($data)) {
                $this->error('素材保存失败!');
            }
        }
        $data = [
            'max' => $pageInfo['page'],
            'num' => $curPage
        ];
        $this->success($data);
    }

    private function getVideoInfo($mediaId) {
        return $this->material->get($mediaId);
    }

    public function add() {
        if (!isPost()) {
            $this->systemDisplay('info');
        } else {
            $post = request('post');
            $video = $post['video'];
            if (empty($video)) {
                $this->error('请先上传视频!');
            }
            if(empty($post['title']) || empty($post['desc'])) {
                $this->error('视频信息未填写完整!');
            }
            $video = ROOT_PATH . $video;
            $result = $this->material->uploadVideo($video, $post['title'], $post['desc']);
            if (!$result->media_id) {
                $this->error('素材上传失败');
            }
            $info = $this->getVideoInfo($result->media_id);
            $data = [
                'media_id' => $result->media_id,
                'url' => $info['down_url'],
                'title' => $info['title'],
                'description' => $info['description'],
                'time' => time()
            ];
            if(!target($this->_model)->add($data)) {
                $this->error('素材数据保存失败!');
            }
            $this->success('素材上传成功!', url('index'));
        }
    }

    public function del() {
        $id = request('', 'id');
        if(empty($id)) {
            $this->error('参数不正确!');
        }
        $info = target($this->_model)->getInfo($id);
        if(empty($info)) {
            $this->error('该素材不存在!');
        }
        $this->material->delete($info['media_id']);
        target($this->_model)->del($id);
        $this->success('素材删除成功!');
    }

    public function dialog() {
        $where = [];
        $count = target($this->_model)->countList($where);
        $pageData = $this->pageData($count, 10);
        $list = target($this->_model)->loadList($where, $pageData['limit'], 'material_id desc');

        $this->assign('list', $list);
        $this->assign('page', $pageData['html']);
        $this->dialogDisplay();

    }


}