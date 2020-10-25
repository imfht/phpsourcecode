<?php
namespace Modules\Ckeditor\Controllers;

use Core\Config;
use Core\Mvc\Controller;
use Modules\File\Models\File as FileModel;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Modules\File\Library\FileHandle;

class IndexController extends Controller
{
    protected $root = '/';
    protected $imgext = ['bmp', 'gif', 'jpg', 'jpe', 'jpeg', 'png']; // allowed image extensions
    protected $imgdr = ''; // current folder (in $root) with images
    public $config;

    public function initialize()
    {
        $this->config = Config::get('config');
        if (isset($_POST['imgroot'])) {
            $this->root = trim(strip_tags($_POST['imgroot']));
        }

        $this->root = trim($this->root, '/') . '/';
        $this->imgdr = isset($_POST['imgdr']) ? trim(trim(strip_tags($_POST['imgdr'])), '/') . '/' : '';
    }

    public function indexAction()
    {
        extract($this->variables['router_params']);
        $getData = $this->request->getQuery();
        $one = preg_match_all('/([a-z]+)/', $getData['CKEditor']);
        if ($one > 1 || $one = 0) {
            return false;
        }
        $one = preg_match_all('/([0-9]+)/', $getData['CKEditorFuncNum']);
        if ($one > 1 || $one = 0) {
            return false;
        }
        $one = preg_match_all('/([a-z\-]+)/', $getData['CKEditorFuncNum']);
        if ($one > 1 || $one = 0) {
            return false;
        }
        $this->variables += array(
            '#templates' => 'ckBrowseImage',
            'CKEditor' => $getData['CKEditor'],
            'CKEditorFuncNum' => (int)$getData['CKEditorFuncNum'],
            'langCode' => $getData['langCode'],
        );
    }

    public function browseImageListAction()
    {
        extract($this->variables['router_params']);

        $query = array(
            'conditions' => 'uid = :uid: AND content_type IN (\'jpeg\',\'png\',\'jpg\',\'gif\') AND access > :access:',
            'bind' => array(
                'uid' => 0,
                'access' => 19
            ),
            'order' => 'changed DESC'
        );
        $query = FileModel::find($query);
        $data = new PaginatorModel(
            array(
                "data" => $query,
                "limit" => 16,
                "page" => $page
            )
        );
        $this->variables += array(
            '#templates' => 'ckBrowseImageList',
            'data' => $data->getPaginate(),
            'page' => $page,
        );
    }

    public function uploadImageAction()
    {
        extract($this->variables['router_params']);
        $data = '';
        if ($this->request->hasFiles() == true) {
            $output = FileHandle::upload(array(), 'upload');
            if (!empty($output['success']) && isset($output['success'][0]) && $output['success'][0]['url']) {
                $callback = $this->request->get('CKEditorFuncNum');
                $data .= '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction(' . $callback . ',"' . $output['success'][0]['url'] . '","");</script>';
            } else {
                $data .= '<script type="text/javascript">alert("上传图片失败啦")</script>';
            }
        }else{
            $data .= '<script type="text/javascript">alert("上传图片失败啦")</script>';
        }

        $this->variables += array(
            '#templates' => 'upload_image',
            'data' => &$data
        );
    }
}
