<?php

/**
 * 语音素材
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\wechat\admin;


class MaterialVoiceAdmin extends \app\wechat\admin\WechatAdmin {


    protected $_model = 'WechatMaterialVoice';
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
                'name' => '语音素材',
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
        $imageCount = $stats->voice_count;
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
        $data = $this->material->lists('voice', $offset, $page);
        $list = $data->item;

        foreach ($list as $key => $vo) {
            if (!$this->getVoice($vo['name'], $vo['media_id'], $vo['update_time'])) {
                return false;
            }
        }
        $data = [
            'max' => $pageInfo['page'],
            'num' => $curPage
        ];
        $this->success($data);
    }

    private function getVoice($name, $mediaId, $time) {
        $data = $this->material->get($mediaId);
        if (empty($data)) {
            $this->error('素材采集失败!');
        }
        $dir = ROOT_PATH . '/upload/weichat/voice/';
        $file = '/upload/weichat/voice/' . $name;
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                $this->error('目录没有写入权限!');
            }
        }
        if (!file_put_contents(ROOT_PATH . $file, $data)) {
            $this->error('本地素材抓取失败,请刷新再试!');
        }
        $data = [
            'media_id' => $mediaId,
            'url' => $file,
            'title' => $mediaId,
            'time' => $time
        ];
        if (!target($this->_model)->add($data)) {
            $this->error('素材保存失败!');
        }
        return true;
    }

    public function add() {
        if (!isPost()) {
            $this->systemDisplay('info');
        } else {
            $post = request('post');
            $voice = $post['voice'];
            if (empty($voice)) {
                $this->error('请先上传声音!');
            }
            $result = $this->material->uploadvoice(ROOT_PATH . $voice);
            if (!$result->media_id) {
                $this->error('素材上传失败');
            }
            $data = [
                'media_id' => $result->media_id,
                'url' => $voice,
                'title' => $post['title'],
                'time' => time()
            ];
            if(!target($this->_model)->add($data)) {
                $this->error('素材数据保存失败!');
            }
            $this->success('素材上传成功!', url('index'));
        }
    }

    public function del() {
        $id = request('post', 'id');
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