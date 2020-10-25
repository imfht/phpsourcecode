<?php

class themeController extends AdminController {

    /**
     * @var ThemeModel
     */
    protected $model;

    public function init() {
        parent::init();
        $this->model=$this->model('theme');
    }

    public function indexAction() {
        $config = pt::import(APP_PATH . '/common/config.php');
        $this->view->config=$config;
        $this->view->list = $this->model->getlist();
    }

    public function setAction() {
        $key = $this->input->get('tpl', 'str', '');
        $info=$this->model->getinfo($key);
        $this->cookie->set('theme' . MODULE_NAME, null);
        /* @var $configModel ConfigModel */
        $configModel=$this->model('config');
        switch($info['type']){
            case 'wap':
                $configModel->where(array('key'=>'wap_theme'))->edit(array('value'=>$key));
                $this->config->save('wap_theme',$key);
                break;
            case 'all':
                $configModel->where(array('key'=>'wap_theme'))->edit(array('value'=>$key));
                $configModel->where(array('key'=>'tpl_theme'))->edit(array('value'=>$key));
                break;
            default:
                $configModel->where(array('key'=>'tpl_theme'))->edit(array('value'=>$key));
        }
        $this->success('设置默认模版成功');
    }

    public function configAction() {
        $key = $this->input->get('tpl', 'str', '');
        $file = TPL_PATH . '/' . $key . '/config.php';
        if (!$this->view->config = pt::import($file)) {
            $this->view->config = array();
        }
        if ($this->request->ispost()) {
            $data = array();
            foreach ($_POST as $k => $v) {
                if ($v['name'] && $v['key']) {
                    $data[$v['key']] = array(
                        'name' => $v['name'],
                        'value' => $v['value'],
                    );
                }
            }
            if (!F($file, $data)){
                $this->error('修改失败，请检查'.$file . '文件权限',0,0);
            };
            $this->success('修改成功');
        }
        $this->view->tpl = $this->input->get('tpl', 'str');
    }

    /**
     * 根据选项跳转设置？
     */
    public function pcsetAction() {
        $config=pt::import(APP_PATH.'/common/config.php');
        $this->redirect(U('config',array('tpl'=>$config['tpl_theme'])));
    }

    public function wapsetAction() {
        $config=pt::import(APP_PATH.'/common/config.php');
        $this->redirect(U('config',array('tpl'=>$config['wap_theme'])));
    }

    public function delAction() {
        $key = $this->input->get('tpl', 'str', '');
        $id = $this->input->get('id', 'str', '');
        $file = TPL_PATH . '/' . $key . '/config.php';
        $data = pt::import($file);
        if (isset($data[$id])) {
            unset($data[$id]);
            if (!F($file, $data)){
                $this->error('修改失败，请检查'.$file . '文件权限',0,0);
            };
            $this->success('删除成功');
        }
        $this->error('没有找到对应的配置项');
    }
}