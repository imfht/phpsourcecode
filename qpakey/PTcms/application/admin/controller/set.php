<?php
class SetController extends AdminController{

    /**
     * @var configModel
     */
    protected $model;
    public function init() {
        parent::init();
        $this->config->set('water_type',0);
        $this->config->set('storage_path', $this->config->get('upload_path','uploads'));
        $this->model=$this->model('config');
    }

    //网站图片上传
    public function picAction() {
        if ($this->request->ispost()){
            if ($_POST['ico']!='favicon.ico'){
                if (stripos($_POST['ico'], 'http') === 0) {
                    $content=F($_POST['ico']);
                } elseif (is_file($_POST['ico'])) {
                    $content=F($_POST['ico']);
                }  elseif (is_file(PT_ROOT.'/'.ltrim($_POST['ico'],'/'))) {
                    $content=F(PT_ROOT.'/'.ltrim($_POST['ico'],'/'));
                }
                if (isset($content)){
                    F(PT_ROOT.'/favicon.ico',$content);
                }
            }
            if ($_POST['logo']!=$this->config->get('logo')){
                $this->model->where(array('key'=>'logo'))->edit(array('value'=>$_POST['logo']));
            }
            if ($_POST['water_image']!=$this->config->get('water_image')){
                $this->model->where(array('key'=>'water_image'))->edit(array('value'=>$_POST['water_image']));
            }
            $this->success('操作成功');
        }
    }
}