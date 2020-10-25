<?php
namespace Modules\file\Controllers;

use Core\Config;
use Core\Db\Query;
use Core\File;
use Core\Mvc\Controller;
use Modules\File\Models\File as FileModel;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

/**
 */
class AdminController extends Controller
{

    public $user;

    public function indexAction()
    {
        extract($this->variables['router_params']);
        $query = array();
        $contentType = Config::get('contentType');
        $params = $this->request->getQuery();
        if (isset($params['content_type']) && isset($contentType[$params['content_type']])) {
            $type = $params['content_type'];
        } else {
            $type = 'all';
        }
        $query = $this->_filterQuery($query);
        $results = FileModel::find($query);
        $data   = new PaginatorModel(
            array(
                "data"  => $results,
                "limit" => 10,
                "page"  => $page
            )
        );

        $this->variables += array(
            'title' => strtoupper($type) . ' 列表',
            'description' => '第' . $page . '页',
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
                'module' => array(
                    'name' => strtoupper($type) . ' 列表',
                ),
            ),
            'content' => array(),
        );
        $filterForm = $this->form->create(Config::get('file.adminFilterForm'));
        $content['filter'] = array(
            '#templates' => 'box',
            'max' => false,
            'color' => 'widget',
            'hiddenTitle' => false,
            'size' => '12',
            'wrapper' => true,
            'content' => array(
                'filterForm' => $filterForm->renderForm(),
            ),
        );
        $content['ConfigList'] = array(
            '#templates' => 'box',
            'title' => '列表',
            'max' => false,
            'color' => 'primary',
            'size' => '12',
            'wrapper' => true,
            'content' => array(
                'dataList' => array(
                    '#templates' => array(
                        'file-manage',
                        'file-manage-' . $type,
                    ),
                    'data' => $data->getPaginate(),
                ),
            ),
        );
        $this->variables['content'] += $content;
    }
    protected function _filterQuery($query)
    {
        $params = $this->request->getQuery();
        $temQuery = array('conditions'=>array(),'bind'=>array());
        foreach (array('state', 'access') as $value) {
            if (isset($params[$value])) {
                $temQuery['conditions'][] = "$value = :$value:";
                $temQuery['bind'][$value] = ntval($params[$value]);
            }
        }
        if (isset($params['content_type'])) {
            $contentType = Config::get('contentType');
            if (isset($contentType[$params['content_type']])) {
                $temQuery['conditions'][] = 'content_type = :content_type:';
                $temQuery['bind']['content_type'] = $params['content_type'];
            }
        }
        $query['conditions'] = implode(' AND ',$temQuery['conditions']);
        $query['bind'] = $temQuery;
        return $query;
    }
    public function deleteAction()
    {
        extract($this->variables['router_params']);
        $this->variables['#templates'] = 'json';
        $file = FileModel::findFirst($id);
        if (!$file) {
            $this->variables['data'] = json_encode(array(
                'state' => false,
                'flash' => '文件不存在',
            ));
            return;
        }
        $state = false;
        $this->db->begin();
        $fileModelDelte = $file->delete();
        $fileDelte = File::rm($file->path);
        if ($fileModelDelte && $fileDelte === true) {
            $this->db->commit();
            $this->variables['data'] = json_encode(array(
                'state' => true,
                'flash' => '文件删除成功',
            ));
            return;
        } else {
            $this->db->rollback();
            $flash = '';
            if ($fileDelte !== true) {
                $flash .= $fileDelte . '<br />';
            }
            if (!$fileModelDelte) {
                foreach ($fileModelDelte->getMessages() as $message) {
                    $flash .= $message . "<br />";
                }
            }
            $this->variables['data'] = json_encode(array(
                'state' => false,
                'flash' => '文件删除成功',
            ));
        }
    }

    public function imageEditorAction()
    {
        extract($this->variables['router_params']);
    }

    public function imagesUploadAction()
    {
        extract($this->variables['router_params']);
        $content = array();
        $this->variables += array(
            'module' => '绯荤粺',
            'title' => '鎺у埗鍙�',
        );
        $this->variables['page'] = array(
            '#templates' => array(
                'name' => 'page',
            ),
            'pageTabs' => array(),
            'breadcrumbs' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '鎺у埗鍙�',
                ),
            ),
            'content' => array(),
        );
        $content['serverInfo'] = array(
            '#templates' => array(
                'name' => 'box',
            ),
            'title' => '鍥剧墖鍒楄〃',
            'max' => false,
            'color' => 'blue',
            'size' => '12',
            'links' => array(
                'nodeList' => array(
                    'class' => '',
                    'href' => $this->url->get(array('for' => 'adminImagesManage', 'type' => 'page', 'dirname' => '')),
                    'name' => '鍥惧簱绠＄悊',
                ),
                'nodeAdd' => array(
                    'class' => 'active',
                    'href' => $this->url->get(array('for' => 'adminImagesUpload')),
                    'name' => '涓婁紶鍥剧墖',
                ),
            ),
            'content' => array(
                'body' => array(
                    '#templates' => array(
                        'name' => 'dropzone',
                    ),
                ),
            ),
        );
        if ($this->request->hasFiles() == true) {
            $config = Config::get('config');
            // Print the real file names and sizes
            foreach ($this->request->getUploadedFiles() as $file) {

                //Print file details
                echo $file->getName(), " ", $file->getSize(), "\n";

                //Move the file into the application
                $dir = $config['dir']['imageBaseDir'] . Config::$encode . '/' . date('Y/m/d/');
                File::createDir($dir);
                if (!is_writable($dir)) {
                    chmod($dir, 0777);
                }
                $file->moveTo($dir . base64_encode($file->getName()) . '.' . end(explode('.', $file->getName())));
            }
        }
        $this->variables['page']['content'] += $content;
    }

    public function imAction()
    {
        extract($this->variables['router_params']);
        if ($this->request->hasFiles() == true) {
            $config = Config::get('config');
            // Print the real file names and sizes
            foreach ($this->request->getUploadedFiles() as $file) {

                //Print file details
                echo $file->getName(), " ", $file->getSize(), "\n";

                //Move the file into the application
                $dir = $config['dir']['imageBaseDir'] . Config::$encode . '/' . date('Y/m/d/');
                if (!file_exists($dir)) {
                    createDir(dirname($path));
                    mkdir($path, 0777);
                }
                if (!is_writable($dir)) {
                    chmod($dir, 0777);
                }
                $file->moveTo($dir . $file->getName());
            }
        }
    }

    /*
     * 鍒犻櫎澶氫釜鏂囦欢
     */
    public function manageAction()
    {
        extract($this->variables['router_params']);
        $formArray = array(
            'formId' => 'nodeTypeForm',
            'form' => array(
                'action' => '',
                'method' => 'post',
                'class' => array(),
                'accept-charset' => 'utf-8',
                'role' => 'form',
            ),
            'name' => array(
                'label' => '绫诲瀷鍚嶇О',
                'description' => '杩欐槸涓�涓弿杩�',
                'field' => 'file',
                'userOptions' => array(
                    'labelAttributes' => array(
                        'class' => array(),
                    ),
                    'groupAttributes' => array(
                        'class' => array(),
                        'id' => 'name',
                    ),
                    'widgetBoxAttributes' => array(
                        'class' => array(),
                    ),
                    'helpAttributes' => array(
                        'class' => array(),
                    ),
                ),
                'widget' => 'File',
                'validate' => array(),
                'attributes' => array(),
                'required' => true,
            ),
        );
        $form = $this->form->create($formArray);
        $this->variables['title'] = '鏂囦欢璁剧疆';
        $this->variables['page'] = array(
            '#templates' => array(
                'name' => 'page',
            ),
            'pageTabs' => array(),
            'breadcrumbs' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '鎺у埗鍙�',
                ),
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '鏂囦欢',
                ),
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '璁剧疆',
                ),
            ),
            'content' => array(),
        );

        $content['setting'] = array(
            '#templates' => array(
                'name' => 'box',
            ),
            'title' => '鏂囦欢璁剧疆',
            'max' => false,
            'color' => 'blue',
            'size' => '12',
            'content' => array(
                'nodeTypeEditor' => array(
                    '#templates' => array(
                        'name' => 'form',
                    ),
                    'id' => 'textFormat',
                    'data' => $form->get(),
                ),
            ),
        );
        if ($this->request->isPost()) {
            foreach ($this->request->getUploadedFiles() as $file) {

                //Print file details
                echo '鏂囦欢鍚嶏細' . $file->getName() . "<br />";
                echo '鏂囦欢灏哄锛�' . $file->getSize() . "<br>";
                echo '鏂囦欢绫诲瀷锛�' . $file->getType() . '<br>';
                echo '鏂囦欢鐪熷疄绫诲瀷锛�' . $file->getRealType() . '<br>';
                echo '鏂囦欢鍚庣紑鍚嶏細' . end(explode('.', $file->getName())) . '<br>';
                //Move the file into the application
                //$file->moveTo('files/' . $file->getName());
            }
        }
        $this->variables['page']['content'] += $content;
    }

    /*
     * 璁剧疆妯″潡
     */
    public function settingAction()
    {
        extract($this->variables['router_params']);
        $this->flash->notice('鏇村璁剧疆鍓嶅線<a href="/"><b>绯荤粺鏉冮檺</b></a>璁剧疆椤甸潰杩涜璁剧疆銆�');
        $settingForm = array();
        $formArray = Config::get('setting', 'module', 'file');
        $settingForm['formId'] = 'adminFileSetting';
        $settingForm['form'] = array(
            'action' => '',
            'method' => 'post',
            'class' => array(),
            'accept-charset' => 'utf-8',
            'role' => 'form',
        );
        $settingForm['anonymousFileMax'] = $formArray['anonymousFileMax'];
        $settingForm['anonymousFileMin'] = $formArray['anonymousFileMin'];
        $settingForm['anonymousType'] = $formArray['anonymousType'];
        $settingForm['anonymousRename'] = $formArray['anonymousRename'];
        unset($formArray['anonymousFileMax']);
        unset($formArray['anonymousFileMin']);
        unset($formArray['anonymousType']);
        unset($formArray['anonymousRename']);
        $settingForm['setting'] = array(
            'label' => '瑙掕壊璁剧疆',
            'description' => '鎸夎鑹插鏂囦欢缁勪欢杩涜璁剧疆',
            'widget' => 'GroupTab',
            'attributes' => array(),
            'userOptions' => array(
                'labelAttributes' => array(
                    'class' => array(),
                ),
                'tabFloat' => 'left',
                'groupAttributes' => array(
                    'class' => array(),
                    'id' => 'machine',
                ),
                'widgetBoxAttributes' => array(
                    'class' => array(),
                ),
                'helpAttributes' => array(
                    'class' => array(),
                ),
            ),
            'group' => array(),
        );
        $rolesList = Config::get('rolesList');
        foreach ($rolesList as $rKey => $rValue) {
            $settingForm['setting']['group'][$rKey] = array(
                'label' => $rValue['name'],
                'description' => '鍏佽鍝簺瑙掕壊浣跨敤鏀规枃鏈牸寮�',
                'widget' => 'Group',
                'attributes' => array(),
                'userOptions' => array(
                    'labelAttributes' => array(
                        'class' => array(),
                    ),
                    'tabFloat' => 'left',
                    'groupAttributes' => array(
                        'class' => array(),
                        'id' => 'machine',
                    ),
                    'widgetBoxAttributes' => array(
                        'class' => array(),
                    ),
                    'helpAttributes' => array(
                        'class' => array(),
                    ),
                ),
                'group' => array(),
            );
            foreach ($formArray as $fKey => $fValue) {
                $settingForm['setting']['group'][$rKey]['group'][$rKey . '[' . $fKey . ']'] = $fValue;
            }
        }
        $form = $this->form->create($settingForm);
        $this->variables['title'] = '鏂囦欢璁剧疆';
        $this->variables['page'] = array(
            '#templates' => array(
                'name' => 'page',
            ),
            'pageTabs' => array(),
            'breadcrumbs' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '鎺у埗鍙�',
                ),
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '鏂囦欢',
                ),
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '璁剧疆',
                ),
            ),
            'content' => array(),
        );

        $content['setting'] = array(
            '#templates' => array(
                'name' => 'box',
            ),
            'title' => '鏂囦欢璁剧疆',
            'max' => false,
            'color' => 'blue',
            'size' => '12',
            'content' => array(
                'nodeTypeEditor' => array(
                    '#templates' => array(
                        'name' => 'form',
                    ),
                    'id' => 'textFormat',
                    'data' => $form->get(),
                ),
            ),
        );
        if ($this->request->isPost()) {
        }
        $this->variables['page']['content'] += $content;
    }
}
