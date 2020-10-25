<?php

/**
 * 图文素材
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\wechat\admin;


class MaterialNewsAdmin extends \app\wechat\admin\WechatAdmin {


    protected $_model = 'WechatMaterialNews';
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
                'name' => '图文素材',
                'description' => '管理微信素材库素材',
            ),
            'fun' => [
                'index' => true
            ]
        );
    }

    public function _indexOrder() {

        return 'time desc, material_id desc';
    }

    /**
     * 同步素材
     */
    public function sync() {
        $stats = $this->material->stats();
        $newsCount = $stats->news_count;
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
        $pagesObj = new \dux\lib\Pagination($newsCount, $curPage, $page);
        $pageInfo = $pagesObj->build();
        if ($curPage > $pageInfo['page']) {
            $this->error('同步完成!', url('index'));
        }
        $offset = $pageInfo['offset'];
        $data = $this->material->lists('news', $offset, $page);
        $list = $data->item;
        foreach ($list as $key => $vo) {
            if (!$this->getData($vo['content']['news_item'], $vo['media_id'], $vo['update_time'])) {
                return false;
            }
        }
        $data = [
            'max' => $pageInfo['page'],
            'num' => $curPage
        ];
        $this->success($data);
    }

    private function getData($data, $mediaId, $time) {
        $dir = ROOT_PATH . '/upload/weichat/image/';
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                $this->error('目录没有写入权限!');
            }
        }
        foreach($data as $key => $vo) {
            $urlParams = $this->getUrlParams($vo['thumb_url']);
            $ext = $urlParams['wx_fmt'] ? $urlParams['wx_fmt'] : 'jpg';
            $ext = ($ext == 'jpeg') ? 'jpg' : $ext;
            $imgData = $this->material->get($vo['thumb_media_id']);
            if (empty($imgData)) {
                $this->error('缩略图采集失败!');
            }
            $image = '/upload/weichat/image/' . $vo['thumb_media_id'] . '.' . $ext;
            if (!file_put_contents(ROOT_PATH . $image, $imgData)) {
                $this->error('本地素材抓取失败,请刷新再试!');
            }
            $data[$key]['image'] = $image;
        }

        $data = [
            'media_id' => $mediaId,
            'data' => serialize($data),
            'time' => $time
        ];
        if (!target($this->_model)->add($data)) {
            $this->error('素材保存失败!');
        }
        return true;
    }

    private function getUrlParams($url) {
        $urlInfo = parse_url($url);
        $query = $urlInfo['query'];
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }

    public function add() {
        if (!isPost()) {
            $this->systemDisplay('info');
        } else {
            $postData = request('post', 'data');
            if (empty($postData)) {
                $this->error('请添加图文信息!');
            }
            $data = [];
            $i = 0;
            foreach ($postData as $key => $vo) {
                $result = $this->material->uploadThumb(ROOT_PATH . $vo['image']);
                $thumbMediaId = $result->media_id;
                if (empty($thumbMediaId)) {
                    $this->error('上传缩略图失败!');
                }
                $content = $this->getContent($vo['content']);
                if (!$content) {
                    $this->error('图片同步至微信失败,请重试!');
                }
                $data[$i] = [
                    'title' => $vo['title'],
                    'author' => $vo['author'],
                    'content' => $content,
                    'thumb_media_id' => $thumbMediaId,
                    'content_source_url' => $vo['url'],
                    'show_cover_pic' => 1,
                    'image' => $vo['image'],
                    'digest' => $vo['digest']
                ];
                $i++;
            }

            $result = $this->material->uploadArticle($data);

            $resource = $this->material->get($result->media_id);

            if(!$this->getData($resource['news_item'], $result->media_id, time())){
                $this->error('素材同步失败,请手动同步!');
            }
            $this->success('素材上传成功!', url('index'));
        }
    }

    private function getContent($content) {
        preg_match_all("/<img([^>]*)\s*src=('|\")([^'\"]+)('|\")/", $content, $matches);
        $newArr = array_unique($matches[3]);
        foreach ($newArr as $img) {
            if (strpos($img, '/') === false) {
                continue;
            }
            $result = $this->material->uploadArticleImage(ROOT_PATH . $img);
            $url = $result->url;
            if (empty($url)) {
                return false;
            }
            $images[$img] = $url;
            $content = str_replace($img, $url, $content);
        }
        return $content;
    }

    public function del() {
        $id = request('post', 'id');
        if (empty($id)) {
            $this->error('参数不正确!');
        }
        $info = target($this->_model)->getInfo($id);
        if (empty($info)) {
            $this->error('该素材不存在!');
        }
        $this->material->delete($info['media_id']);
        target($this->_model)->del($id);
        $this->success('素材删除成功!');

    }

    public function dialog() {
        $where = [];
        $count = target($this->_model)->countList($where);
        $pageData = $this->pageData($count, 12);
        $list = target($this->_model)->loadList($where, $pageData['limit'], 'material_id desc');

        $this->assign('list', $list);
        $this->assign('page', $pageData['html']);
        $this->dialogDisplay();

    }


}