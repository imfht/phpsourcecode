<?php
namespace app\run\controller;

use think\Loader;
use app\common\controller\Run;

class Tool extends Run
{
    public function addv()
    {
        $this->setTitle("模板创建", 'operation');
        $this->assign->warning = '非程序员请谨慎操作！目前仅支持创建Home模块的模板页面';
        $dir_list  = $GLOBALS['Model_map'];
        unset($dir_list['Exlink']);
        $dir_list['custom'] = '自定义';
        $this->mdl->fieldRespond = array(
            'dir' => array(
                'RespondField' => array('custom'),
                'custom' => array('custom')
            )
        );
        
        $this->mdl->form = array(
            'dir' => array(
                'type' => 'string',
                'name' => '模板目录',
                'elem' => 'select',
                'options' => $dir_list,
            ),
            'custom' => array(
                'type' => 'string',
                'name' => '自定义目录名',
                'elem' => 'text',
                'info' => '请自行控制目录名大小写'
            ),
            'title' => array(
                'type' => 'string',
                'name' => '模板名',
                'elem' => 'text',
                'info' => '无需带后缀'
            ),
            'is_head' => array(
                'type' => 'integer',
                'name' => '重写页头',
                'elem' => 'checker'
            ),
            'is_content' => array(
                'type' => 'integer',
                'name' => '重写主体',
                'elem' => 'checker'
            ),
            'is_foot' => array(
                'type' => 'integer',
                'name' => '重写页尾',
                'elem' => 'checker'
            ),
        );
        if ($this->request->isPost()) {
            $data = $this->Form->data[$this->m];
            $dirname = trim($data['dir']) != 'custom' ? trim($data['dir']) : trim($data['custom']);
            if (!$dirname) {
                $this->assign->mdl_error['dir'] = '模板目录有误';
            }
            $view_name = trim($data['title']);
            if (!$view_name) {
                $this->assign->mdl_error['title'] = '请填写模板名称';
            }
            
            if (!isset($this->assign->mdl_error)) {
                $dir = APP_PATH . 'home' . DS . 'view' . DS . $dirname . DS;
                if (!file_exists($dir)) {
                    mkdir($dir, 0777);
                }                
                /*if (!is_writeable($dir)) {
                    return $this->message('error', "模板所在{$dir}文件夹没有可写权限");
                }*/
                $path = $dir . $view_name . '.html';
                if (file_exists($path)) {
                    return $this->message('error', "模板home\\view\\{$dirname}\\{$view_name}.html已经存在");
                }
                $head_html = $data['is_head'] ? '{block name="head"}{/block}' : '';
                $cont_html = $data['is_content'] ? '{block name="content"}{/block}' : '{block name="insider"}{/block}';
                $foot_html = $data['is_foot'] ? '{block name="foot"}{/block}' : '';
                
                $addString = <<<HTML
{extends file="../insider_base.html"}

$head_html
$cont_html
$foot_html
HTML;
                file_put_contents($path, $addString);
                return $this->message('success', "模板home\\view\\{$dirname}\\{$view_name}.html创建成功");
            }
        }        
        $this->fetch = 'form';
    }
    public function addm()
    {
        $this->setTitle("模型生成", 'operation');
        $this->assign->warning = '非程序员请谨慎操作！';
        $this->mdl->form = array(
            'model' => array(
                'type' => 'string',
                'name' => '模型文件名',
                'elem' => 'text',
            ),
            'cname' => array(
                'type' => 'string',
                'name' => '模型名称',
                'elem' => 'text',
                'list' => 'show',
                'info' => '比如：栏目'
            ),
            'is_menu' => array(
                'type' => 'boolean',
                'name' => '是否栏目',
                'elem' => 'checker',
                'list' => 'checker',
            ),
            'is_dustbin' => array(
                'type' => 'boolean',
                'name' => '删除回收',
                'elem' => 'checker',
                'list' => 'checker',
            ),
        );
        if ($this->request->isPost()) {
            $data = $this->Form->data[$this->m];
            $model = Loader::parseName(trim($data['model']), 1);
            if (!$model) {
                $this->assign->mdl_error['model'] = '请填写模型名称';
            }
            if (!isset($this->assign->mdl_error)) {
                $dir = APP_PATH . 'common' . DS . 'model' . DS;
                /*if (!is_writeable($dir)) {
                    return $this->message('error', "模型common/model文件夹没有可写权限");
                }*/
                $path = $dir . $model . '.php';
                if (file_exists($path)) {
                    return $this->message('error', "模型common\\model\\{$model}已经存在");
                }
                $addString = <<<MODEL
<?php
namespace app\common\model;

class $model extends App
{
    //关联模型
    public \$assoc = [];
    
    public function initialize()
    {        
        \$this->form = [
            'id' => [
            	'type' => 'integer',
            	'name' => 'ID',
            	'elem' => 'hidden',
            ],
            //其他字段
        ];
        call_user_func_array(['parent', __FUNCTION__], func_get_args());
    }
    
    /*
    //表单分组
    public \$formGroup = [
        'advanced' => '高级选项'
    ];
    */
    
    //数据验证    
    protected \$validate = [];
}

MODEL;
                file_put_contents($path, $addString);
                
                $this->loadModel('Model');
                $this->Model->save([
                    'model' => $model,
                    'cname' => $data['cname'],
                    'is_menu' => (int)$data['is_menu'],
                    'is_power' => (int)$data['is_power']
                ]);
                return $this->message('success', '操作已成功');
            }
        }
        $this->fetch = 'form';
    }

    public function addc()
    {
        $this->setTitle("控制器生成", 'operation');
        $this->assign->warning = '非程序员请谨慎操作！';
        $this->mdl->form = array(
            'controller' => array(
                'type' => 'string',
                'name' => '控制器名称',
                'elem' => 'text',
            ),
            'module' => array(
                'type' => 'string',
                'name' => '生成模块',
                'elem' => 'checkbox',
                'options' => ['run' => '后台', 'home' => '前台', 'manage' => '用户']
            ),
        );

        if ($this->request->isPost()) {
            $data = $this->Form->data[$this->m];
            $controller = Loader::parseName(trim($data['controller']), 1);
            $modules = $data['module'];
            if (!$controller) {
                $this->assign->mdl_error['controller'] = '请填写控制器的名称';
            }
            if (empty($modules)) {
                $this->assign->mdl_error['module'] = '请选择控制器生成模块';
            }

            if (!isset($this->assign->mdl_error)) {
                foreach ($modules as $module) {
                    $dir = APP_PATH . $module . DS . 'controller' . DS;
                    /*
                    if (!is_writeable($dir)) {
                        return $this->message('error', "模块{$module}/controller文件夹没有可写权限");
                    }*/
                    $path = $dir . $controller . '.php';
                    if (file_exists($path)) {
                        return $this->message('error', "控制器{$module}\\controller\\{$controller}已经存在");
                    }

                    $extends = ucfirst($module);
                    if ($module == 'run') {
                        $addString = <<<CONTROLLER
<?php
namespace app\\$module\controller;

use app\common\controller\Run;

class $controller extends $extends
{
    //初始化 需要调父级方法
    public function initialize()
    {        
        call_user_func(['parent', __FUNCTION__]); 
    }
    
    //列表 
    public function lists()
    {
        ##搜索字段
        if (!\$this->local['filter']) {
            \$this->local['filter'] = [
                'title'
            ];
        }
        ##列表字段
        if (!\$this->local['list_fields']) {
            \$this->local['list_fields'] = [
                'title'
            ];
        }
        call_user_func(['parent', __FUNCTION__]);
    }
    
    //添加
    public function create()
    {   ##设置默认值
        //\$this->assignDefault('字段名', '默认值');
        ##字段白名单
        //\$this->local['whiteList'] = ['id', 'title', ...允许添加的字段列表];   
        call_user_func(['parent', __FUNCTION__]);
    }
    
    //修改
    public function modify()
    {   
        ##字段白名单
        //\$this->local['whiteList'] = ['id', 'title', ...允许修改的字段列表];
        call_user_func(['parent', __FUNCTION__]);
    } 
    
    //删除
    public function delete()
    {   
        ## 设置额外的删除条件 .eg
        //\$this->local['where'] = ['is_verify' => 0];
        call_user_func(['parent', __FUNCTION__]);
    }  
}

CONTROLLER;
                    } elseif ($module == 'home') {
                        $addString = <<<CONTROLLER
<?php
namespace app\\$module\controller;

use app\common\controller\Home;

class $controller extends $extends
{
    //初始化 需要调父级方法
    public function initialize()
    {        
        call_user_func(['parent', __FUNCTION__]); 
    }
}

CONTROLLER;
                    } elseif ($module == 'manage') {
                        $addString = <<<CONTROLLER
<?php
namespace app\\$module\controller;

use app\common\controller\Manage;

class $controller extends $extends
{
    //初始化 需要调父级方法
    public function initialize()
    {        
        call_user_func(['parent', __FUNCTION__]); 
    }
}

CONTROLLER;
               
                    }
                    file_put_contents($path, $addString);
                }
                return $this->message('success', '操作已成功');
            }
        }
        $this->fetch = 'form';
    }
    
    function getSiteSize()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        set_time_limit(0);
        echo $this->ajax('success',return_size(getDirSize(dirname(APP_PATH))));
        exit ;
    }
    
    function getLog()
    {        
        include  \Env::get('root_path') . 'vendor' . DS . 'Pclzip' . DS . 'pclzip.lib.php';
        $filepath = WWW_ROOT . 'tempfile' . DS . 'log_' . date('Ymd') . '.zip';
        $logszip = new \PclZip($filepath);
        $zipList = $logszip->create(\Env::get('runtime_path') . 'log' . DS, PCLZIP_OPT_REMOVE_ALL_PATH);  
        if ($zipList == 0) {
            $this->message('error', '日志文件压缩失败:' . $logszip->errorInfo(true));
        }
        ob_end_clean();
        header("Content-Type: application/force-download;"); 
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($filepath));
        header("Content-Disposition: attachment; filename=" . 'log_' . date('Ymd') . '.zip'); 
        header("Expires: 0");
        header("Cache-control: private");
        header("Pragma: no-cache"); 
        readfile($filepath);         
        exit ;
    }
    
    function removeLog()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        removeDir(\Env::get('runtime_path') . 'log' . DS);
        return $this->ajax('success', '日志清除成功！');
    }
    
    function removeTemp()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        removeDir(\Env::get('runtime_path') . DS . 'temp');
        removeDir(WWW_ROOT . 'tempfile');
        return $this->ajax('success', '临时文件清除成功！');
    }
    
    public function clearCache()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        \Cache::clear(); 
        return $this->ajax('success', '缓存清除成功！');
    }
    
    public function switchTrace()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        $is_trace = intval(!config('app_trace'));
        $this->loadModel('Setting');
        $this->Setting->where(['vari' => 'is_trace'])->update(['value' => $is_trace]);
        $this->Setting->write_cache();
        if($is_trace)
            return $this->ajax('success', 'Trace已启用');
        else
            return $this->ajax('success', 'Trace已关闭');
    }
    
    public function lock_screen()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        cookie(['prefix' => 'think_', 'expire' => 3600]);
        cookie('is_lock_screen', 1, 86400);
        return $this->ajax('success', '锁屏成功');
    }
    
    public function relieve_screen()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        
        $data = $this->request->post();
        if (trim($data['pwd'])) {
            $lock_pwd = $this->Auth->password($data['pwd']);
            $this->loadModel('User');
            $check_pwd = $this->User->where(['id' => $this->login['id']])->value('password');
            if ($lock_pwd == $check_pwd) {
                cookie(['prefix' => 'think_', 'expire' => 3600]);
                cookie('is_lock_screen', null);
                return $this->ajax('success', '解屏成功');
            } else {
                return $this->ajax('error', '密码输入不一致');
            }
        } else {
            return $this->ajax('error', '请输入密码');
        }
    }
    
    public function set_skin()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        $data = $this->request->post();
        $skin = trim($data['skin']);
        cookie(['prefix' => 'think_', 'expire' => 3600]);
        cookie('skin_name', $skin, 2592000);
        return $this->ajax('success', '皮肤切换成功');
    }
    
    public function get_awesome()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        $return = \Cache::get('awesome');
        if (empty($return)) {
            $url  = "http://code.zoomla.cn/boot/font.html"; 
            $content = send(['url' => $url]);
            preg_match_all('/<i\s+class="fa\s+([^"]+)"\s+aria-hidden="true">/is', $content['content'], $icons);
            $return = $icons[1] ? $icons[1] : [];
            \Cache::set('awesome', $return, 604800);
        }
        return $this->ajax('success', '', $return);
    }
}
  