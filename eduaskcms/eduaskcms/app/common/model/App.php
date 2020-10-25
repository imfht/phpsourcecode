<?php
namespace app\common\model;

use think\Model;
use app\common\utility\Hash;
use think\Loader;

class App extends Model
{
    ##开启时间戳为datetime
    protected $autoWriteTimestamp = 'datetime';
    ##创建日期字段
    protected $createTime = false;
    ##最后修改日期字段
    protected $updateTime = false;
    ##定义当前模型表单
    public $form;
    ##表单分组
    /*
    表单分组 格式：组键 => 组名称
    form中每个字段elem_group属性可以指定一个 “组键” ，用于表单分组；不要定义 'basic'=>'基本选项'，没有定义elem_group的字段会默认归到basic里面
    表单分组 默认没有定义 表示不分组
    public $formGroup = array(
        'advanced'=>'高级选项'
    );
    */
    public $formGroup = array();
    ##字段响应 -- 根据一个字段的值不同控制另外其他字段的是否显示
    /*
    public $fieldRespond = array(
        'type'=>array( ##字段名
            'RespondField' => array('ex_link'), ##受type响应需要隐藏的字段列表
            'ExLink' => array('ex_link')##值=>字段列表  ，当type为ExLink时需要显示的字段
        )
    );
    */
    public $fieldRespond = [];

    ##主要展示字段，一般为title
    public $display = 'id';
    ##关联模型，将所有关联模型方法名汇总
    /*
    public $assoc = [
        'assocModel'=>[
            'type'=>'hasOne'
        ]
    ];
    */
    public $assoc = array();
    ##关联过渡属性，不用做任何赋值
    public $assocUse = array();


    ##当前模型名称
    public $cname;
    ##当前类验证规则
    /*
    格式：
    array(
        字段=>array(
            'rule'=>  规则
            'on'=> 场景 add、edit
            'message'=> 提示
            'allowEmpty'=> 可以有空
        )
    )
    */
    protected $validate = [];
    ##下面几个属性无需自己定义
    protected $validate_rule = array();
    protected $validate_msg = array();
    protected $validate_scene = array();
    ##验证对象 
    protected $validate_object = null;
    ##支持保持、修改前是否自动验证
    public $is_validate = true;
    public $old_data = array();##修改前的原数据
    protected $error = [];
    

    public function initialize()
    {
        if ($this->form) {
            $this->field = array_keys($this->form);
        }
        if (!empty($this->formGroup)) {
            $this->formGroup = array('basic' => '基本选项') + $this->formGroup;
        }
        call_user_func(array('parent', __FUNCTION__));
        $this->cname = $GLOBALS['Model_title'][$this->name];

        if (isset($this->form['created']) && !$this->createTime) {
            $this->createTime = 'created';
        }
        if (isset($this->form['modified']) && !$this->updateTime) {
            $this->updateTime = 'modified';
        }
    }

    protected static function init()
    {
        ##新增前回调 这里的$model就是$this
        self::beforeInsert(function ($model) {
            if (method_exists($model, 'before_insert')) {
                return $model->before_insert();
            }
        });
        ##新增后回调
        self::afterInsert(function ($model) {
            if (method_exists($model, 'after_insert')) {
                return $model->after_insert();
            }
        });
        ##修改前回调
        self::beforeUpdate(function ($model) {
            if (method_exists($model, 'before_update')) {
                return $model->before_update();
            }
        });
        ##修改后回调
        self::afterUpdate(function ($model) {
            if (method_exists($model, 'after_update')) {
                return $model->after_update();
            }
        });
        ##写入前回调（insert和update都会先调这个方法）
        self::beforeWrite(function ($model) {
            if (method_exists($model, 'before_write')) {
                return $model->before_write();
            }
        });
        ##写入后回调
        self::afterWrite(function ($model) {
            if (method_exists($model, 'after_write')) {
                return $model->after_write();
            }
        });
        ##删除前回调
        self::beforeDelete(function ($model) {
            if (method_exists($model, 'before_delete')) {
                return $model->before_delete();
            }
        });
        ##删除后回调
        self::afterDelete(function ($model) {
            if (method_exists($model, 'after_delete')) {
                return $model->after_delete();
            }
        });
    }

    public function before_insert()
    {
        
    }

    public function after_insert()
    {
        $this->counterCache();
        if (setting('is_admin_cache')) {
            \Cache::clear($this->name);
        }
    }

    public function before_update()
    {
        ##2017-10-29 核心的时间戳，有点不智能，先自己写死吧
        /*if ($this->form['modified'] && $this->updateTime) {
            $this['modified'] = date('Y-m-d H:i:s');
        }*/        
        return true;
    }

    public function after_update()
    {
        if (setting('is_admin_cache')) {
            \Cache::clear($this->name);
        }
    }

    public function before_write()
    { 
        if ($this->isUpdate) {
            $this->old_data =  [];            
            $scene = 'edit';
            if (isset($this['id'])) {
                $old_data = $this->where(array('id' => $this['id']))->find();
                if ($old_data) {
                    $this->old_data = $old_data->toArray();
                }
            }
        } else {            
            $scene = 'add';
        }
        return $this->validateCheck(null, null, true, $scene);
    }

    public function after_write()
    {
    }

    public function before_delete()
    {
    }

    public function after_delete()
    {
        $this->counterCache();
        $this->deleteWith();
        $is_dustbin = Hash::combine($GLOBALS['Model'], '{n}[model=' . $this->name . '].model', '{n}[model=' . $this->name . '].is_dustbin');
        settype($is_dustbin, 'array');
        if ($is_dustbin[$this->name] && $this->name != 'Dustbin') {
            $this->add_dustbin();
        }
        if (setting('is_admin_cache')) {
            \Cache::clear($this->name);
        }
    }

    protected function add_dustbin()
    {
        if ($this->getData()) {            
            $data['model'] = $this->name;
            $data['model_id'] = $this['id'];
            $data['title'] = $this[$this->display];
            $data['data'] = gzcompress(serialize($this->getData()));
            $data['status'] = 0;
            $dustbinModel = model('Dustbin');
            $dustbinModel->data($data);
            $dustbinModel->isUpdate(false)->save();
        }
    }

    protected function counterCache()
    {
        if ($this->assoc) {
            
            $data = $this;
            foreach ($this->assoc as $assocModel => $assocInfo) {
                if ($assocInfo['type'] != 'belongsTo' || !$assocInfo['counterCache']) continue;

                $counter_foreign = $assocInfo['foreignKey'] ? $assocInfo['foreignKey'] : Loader::parseName($assocModel) . '_id';
                if (empty($data[$counter_foreign])) continue;

                $counter_field = is_string($assocInfo['counterCache']) ? $assocInfo['counterCache'] : Loader::parseName($this->name) . '_count';

                $counter_where = array($counter_foreign => $data[$counter_foreign]);
                if ($assocInfo['countWhere'] && is_array($assocInfo['countWhere'])) $counter_where += $assocInfo['countWhere'];
                $count = $this->where($counter_where)->count();

                $assocModelObj = model($assocModel);

                $assocModelObj->is_validate = false;
                $assocModelObj->where($assocModelObj->getPk(), $data[$counter_foreign])->update(array($counter_field => $count));
                if (setting('is_admin_cache')) {
                    \Cache::clear($assocModel);
                }
            }
        }
    }

    protected function deleteWith()
    {
        if ($this->assoc && isset($this['id'])) {
            foreach ($this->assoc as $assocModel => $assocInfo) {
                if (in_array($assocInfo['type'], ['hasOne', 'hasMany']) && $assocInfo['deleteWith'] === true) {
                    $foreignKey = $assocInfo['foreignKey'] ? $assocInfo['foreignKey'] : Loader::parseName($this->name) . '_id';
                    $assocModel = !$assocInfo['foreign'] ? $assocModel : $assocInfo['foreign'];
                    model($assocModel)->where([$foreignKey => $this['id']])->delete();
                }
            }
        }
    }


    protected function before_validate()
    {
        //上传
        $this->processUpload();
        
        //数据处理
        foreach ($this->getData() as $field => $value) {
            if (isset($this->form[$field]['filter'])) {
                $function  = trim($this->form[$field]['filter']);
                if (function_exists($function)) {
                    $this[$field] = $function($value);
                }
            }      
            
            if ($this->form[$field]['type'] === 'integer') {
                $this[$field] = intval($value);
            } elseif ($this->form[$field]['type'] === 'float') {
                $this[$field] = floatval($value);
            } elseif ($this->form[$field]['type'] === 'time') {
                $this[$field] = strtotime($value);
            } elseif ($this->form[$field]['type'] === 'array' && is_array($value)) {
                $this[$field] = json_encode($value);
            } elseif ($this->form[$field]['type'] === 'blob') {
                $this[$field] = gzcompress($value);
            } elseif ($this->form[$field]['type'] === 'blob.array') {
                $this[$field] = gzcompress(serialize($value));
            } elseif ($this->form[$field]['type'] === 'string' && is_string($this[$field])){
                $this[$field] = strip_tags($this[$field]);
            } elseif ($this->form[$field]['type'] === 'none') {
                unset($this[$field]);
            } else {
                if (is_array($value)) {
                    $this[$field] = json_encode($value);
                }
            }
        }
    }

    public function isValidate($is = true)
    {
        $is = !!$is;
        $this->is_validate = $is;
        return $this;
    }

    /**
     * 自动验证数据
     * @access protected
     * @param array $data 需要验证的数据
     * @param mixed $rule 验证规则
     * @param bool $batch 批量验证
     * @param string $scene 场景  add 、edit
     * @return bool
     */
    public function validateCheck($data = null, $rule = null, $batch = null, $scene = null)
    {
        ##验证前执行函数      
        
        $this->before_validate();
        if ($this->is_validate === false) {
            return true; ##不需要验证数据
        }

        
        ##创建一个验证对象
        if (!is_object($this->validate_object)) {
            $field = array();
            if ($this->form) {
                foreach ($this->form as $key => $info) {
                    $field[$key] = $info['name'];
                }
            }
            $this->validate_object = new \think\Validate([], [], $field);
        }
        ##处理自己定义的验证规则到TP识别格式
        if (empty($this->validate_rule)) {
            $this->validateFormat($this->validate);
        }
        if (empty($rule)) {
            $rule = $this->validate_rule;
        }

        
        
        $this->validate_object->rule($rule);
        if ($scene) {
            $rule = $this->validate_scene[$scene];
        }
        
        ##没有规则不做任何验证
        if (empty($rule)) {
            return true;
        }
        

        $this->validate_object->message($this->validate_msg);
        ##验证会批量验证
        if (empty($data)) {
            $data = $this->getData();
        }
        if (!$this->validate_object->batch(true)->check($data, $rule)) {
            $this->error = $this->validate_object->getError();
        }
        ##有误
        if (!empty($this->error)) {
            return false;
        }
        return true;
    }

    ##处理自己定义的验证规则$_validate到TP识别格式
    protected function validateFormat($valiArr)
    {
        if (empty($valiArr) || !is_array($valiArr)) {
            return true;
        }

        foreach ($valiArr as $field => $info) {
            if (isset($info['rule'])) {
                if (is_array($info['rule'])) {
                    $rule = $info['rule'];
                    $key = $rule[0];
                    if ($key == 'call') {
                        $method = $rule[1];
                        unset($rule[0]);
                        unset($rule[1]);
                        $value = implode(',', $rule);
                        $this->validate_rule[$field][$method] = $value;
                        if (isset($info['message'])) {
                            $this->validate_msg["{$field}.$method"] = $info['message'];##无效，需要在自己的function中return错误信息
                        }
                        $this->validate_object->extend($method, array($this, $method));
                        if (!$info['on']) {
                            $this->validate_scene['add'][$field][$method] = $value;
                            $this->validate_scene['edit'][$field][$method] = $value;
                        } else {
                            $this->validate_scene[$info['on']][$field][$method] = $value;
                        }
                    } else {
                        unset($rule[0]);
                        $value = implode(',', $rule);
                        $this->validate_rule[$field][$key] = $value;
                        if (isset($info['message'])) $this->validate_msg["{$field}.{$key}"] = $info['message'];
                        if (!$info['on']) {
                            $this->validate_scene['add'][$field][$key] = $value;
                            $this->validate_scene['edit'][$field][$key] = $value;
                        } else {
                            $this->validate_scene[$info['on']][$field][$key] = $value;
                        }
                    }
                } else {
                    $rule = strval($info['rule']);
                    if (!in_array($rule, (array)$this->validate_rule[$field])) {
                        $this->validate_rule[$field][] = $rule;
                        if (isset($info['message'])) {
                            $this->validate_msg["{$field}.{$rule}"] = $info['message'];
                        }
                        if (!$info['on']) {
                            $this->validate_scene['add'][$field][] = $rule;
                            $this->validate_scene['edit'][$field][] = $rule;
                        } else {
                            $this->validate_scene[$info['on']][$field][] = $rule;
                        }
                    }
                }

                ##默认有需要验证的字段都不可以为空，会自动加上require规则；如果允许为空可以加上allowEmpty属性；如果
                if (!$info['allowEmpty'] && !in_array('require', (array)$this->validate_rule[$field])) {
                    $this->validate_rule[$field][] = 'require';
                    $this->validate_msg["{$field}.require"] = '该字段不能为空';
                    $this->validate_scene['add'][$field][] = 'require';
                    $this->validate_scene['edit'][$field][] = 'require';
                }
            } else {
                if (is_array($info)) {
                   foreach ($info as $rule) {
                        $this->validateFormat(array($field => $rule));
                    } 
                }
            }
        }
    }

    function processUpload()
    {
        $_uploads_info = array();
        ##处理当前模型上传数据
        if ($_FILES['data']['name'][$this->name]) {
            foreach ($_FILES['data']['name'][$this->name]['upload'] as $field => $info) {
                $_uploads_info[$field]['name'] = $_FILES['data']['name'][$this->name]['upload'][$field];
                $_uploads_info[$field]['type'] = $_FILES['data']['type'][$this->name]['upload'][$field];
                $_uploads_info[$field]['tmp_name'] = $_FILES['data']['tmp_name'][$this->name]['upload'][$field];
                $_uploads_info[$field]['error'] = $_FILES['data']['error'][$this->name]['upload'][$field];
                $_uploads_info[$field]['size'] = $_FILES['data']['size'][$this->name]['upload'][$field];
            }
            unset($_FILES['data']['name'][$this->name]);
        }
        
        if ($_uploads_info) { 
            foreach ($_uploads_info as $field => $info) {
                if ($info['error'] == 4) {
                    continue;
                }
                if ($info['error']) {
                    $error_map = array(
                        '1' => '文件大小超过了php.ini定义的upload_max_filesize值',
                        '2' => '文件大小超过了HTML定义的max_file_size值',
                        '3' => '文件上传不完全',
                        '4' => '无文件上传',
                        '6' => '缺少临时文件夹',
                        '7' => '写文件失败',
                        '8' => '上传被其它扩展中断',
                        '' => '上传表单错误'
                    );
                    $error = $error_map[$info['error']];
                    if (!isset($error)) {
                        $error = "未知错误[代码:{$info['error']}]";
                    }
                    $this->forceError($field, $error);
                    continue;
                }

                if (!is_uploaded_file($info['tmp_name'])) {
                    $this->forceError($field, '非上传文件');
                    continue;
                }

                list($elem, $sub_elem) = PluginSplit($this->form[$field]['elem']);
                if (!$elem) {
                    $elem = $sub_elem;
                }

                $filename_parts = explode('.', $info['name']);
				$ext = strtolower(array_pop($filename_parts));
                
                if ($elem === 'image') {
                    if (!isset($this->form[$field]['upload']['validExt'])) {
                        $this->form[$field]['upload']['validExt'] = array('png', 'gif', 'jpg', 'jpeg');
                    }
                }
                settype($this->form[$field]['upload']['validExt'], 'array');
                if ($this->form[$field]['upload']['validExt'] && !in_array($ext, $this->form[$field]['upload']['validExt'])) {
                    $this->forceError($field, '上传文件只接受后缀名为 ' . implode(',', $this->form[$field]['upload']['validExt']) . ' 的文件');
                    continue;
                }
                if (!isset($this->form[$field]['upload']['notValidExt'])) {
                    $this->form[$field]['upload']['notValidExt'] = array('exe', 'php', 'asp', 'bat', 'asa', 'vbs');
                }
                settype($this->form[$field]['upload']['notValidExt'], 'array');
                if ($this->form[$field]['upload']['notValidExt'] && in_array($ext, $this->form[$field]['upload']['notValidExt'])) {
                    $this->forceError($field, '上传文件不接受后缀名为 ' . implode(',', $this->form[$field]['upload']['notValidExt']) . ' 的文件');
                    continue;
                }

                if ($this->form[$field]['upload']['maxSize'] && $info['size'] > $this->form[$field]['upload']['maxSize'] * 1024) {
                    $this->forceError($field, '上传文件大小[' . return_size($info['size']) . ']超过允许最大值' . return_size($this->form[$field]['upload']['maxSize'] * 1024));
                    continue;
                }

                $file = uploadFile($info, $this->name, null, $this->form[$field]['upload']['notValidExt']);
                
                if (!$file) {
                    $this->forceError($field, $GLOBALS['upload_file_error']);
                    continue;
                }
                
                if ($elem === 'image') {
                    if (isset($this->form[$field]['image']['thumb'])) { 
                        $thumb_width = intval(isset($this->form[$field]['image']['thumb']['width']) ? $this->form[$field]['image']['thumb']['width'] : 0);
						$thumb_height = intval(isset($this->form[$field]['image']['thumb']['height']) ? $this->form[$field]['image']['thumb']['height'] : 0);
						$thumb_method = intval(isset($this->form[$field]['image']['thumb']['method']) ? $this->form[$field]['image']['thumb']['method'] : 0);
                        
                        
                        list($thumb_width, $thumb_height, $thumb_method) = $this->getThumbInfo([$thumb_width, $thumb_height, $thumb_method], $this, $this->getData());
                        
                        $file_size  = getimagesize(WWW_ROOT . $file);
                        $thumb_width = $thumb_width > 0 ? $thumb_width : $file_size[0];
                        $thumb_height = $thumb_height > 0 ? $thumb_height : $file_size[1];
                        $thumb_method = in_array($thumb_method, [1,2,3,4,5,6]) ? $thumb_method : 3;
                        
                        $basepath = WWW_ROOT . 'upload' . DS . 'thumbs' . DS;
                    	if (!file_exists($basepath)) mkdir($basepath);
                    	$basepath = $basepath.date('Ym');
                    	if (!file_exists($basepath)) mkdir($basepath);                        
                        $filename = substr(array_pop(explode('/', $file)), 0, -(strlen($ext) + 1)) . '_' . $thumb_width . '_' . $thumb_height . '_' . $thumb_method;
                        
                        $image = \think\Image::open(WWW_ROOT . $file);
                        $rslt  = $image->thumb($thumb_width, $thumb_height, $thumb_method)->save($basepath . DS . $filename . '.' . $ext);
                        if ($rslt) {
                            $thumb = 'upload/thumbs/' . date('Ym') . '/' . $filename . '.' . $ext;
                            if (!isset($this->form[$field]['image']['thumb']['field'])) {
    							$this->form[$field]['image']['thumb']['field'] = 'thumb';
    						}
                            $this[$this->form[$field]['image']['thumb']['field']] = $thumb;
                        }
                    }
                    
                    if (isset($this->form[$field]['image']['resize'])) {
                        $resize_width = intval(isset($this->form[$field]['image']['resize']['width']) ? $this->form[$field]['image']['resize']['width'] : 0);
						$resize_height = intval(isset($this->form[$field]['image']['resize']['height']) ? $this->form[$field]['image']['resize']['height'] : 0);
						$resize_method = intval(isset($this->form[$field]['image']['resize']['method']) ? $this->form[$field]['image']['resize']['method'] : 0);
                        
                        list($resize_width, $resize_height, $resize_method) = $this->getResizeInfo([$resize_width, $resize_height, $resize_method], $this, $this->getData());
                        
                        $file_size  = getimagesize(WWW_ROOT . $file);
                        $resize_width = $resize_width > 0 ? $resize_width : $file_size[0];
                        $resize_height = $resize_height > 0 ? $resize_height : $file_size[1];
                        $resize_method = in_array($resize_method, [1,2,3,4,5,6]) ? $resize_method : 3;
                        
                        $basepath = WWW_ROOT . 'upload' . DS . $this->name . DS;
                    	if (!file_exists($basepath)) mkdir($basepath);
                    	$basepath = $basepath.date('Ym');
                    	if (!file_exists($basepath)) mkdir($basepath);                        
                        $filename = substr(array_pop(explode('/', $file)), 0, -(strlen($ext) + 1)) . '_' . $resize_width . '_' . $resize_height . '_' . $resize_method;
                        
                        $image = \think\Image::open(WWW_ROOT . $file);
                        $rslt  = $image->thumb($resize_width, $resize_height, $resize_method)->save($basepath . DS . $filename . '.' . $ext);
                        if ($rslt) {
                            $old_file = $file;
                            $file = 'upload/' . $this->name . '/' . date('Ym') . '/' . $filename . '.' . $ext;
                            @unlink(WWW_ROOT . $old_file);
                        }
                    }
                    
                    ##图片水印
                    if (setting('is_water') && setting('water_model')) {
                        $water_model = json_decode(setting('water_model'), true);
                        if (in_array($this->name, $water_model)) {
                            $water_type = setting('water_type');
                            if ($water_type === 'text' && setting('water_text')){
                                $water_size = intval(setting('water_text_size')) > 0 ? intval(setting('water_text_size')) : 20;
                                $water_location = in_array(intval(setting('water_location')), [1,2,3,4,5,6,7,8,9]) ? intval(setting('water_location')) : 9;
                                $water_color  = setting('water_text_color') ? setting('water_text_color') : '#FFFFFF';
                                $water_font = WWW_ROOT . 'font' . DS . 'FZLTCXHJW.ttf';
                                $image = \think\Image::open(WWW_ROOT . $file);
                                $image->text(setting('water_text'), $water_font, $water_size, $water_color)->save(WWW_ROOT . $file);
                            }
                            
                            if ($water_type === 'image' && setting('water_image')) {
                                $water_location = in_array(intval(setting('water_location')), [1,2,3,4,5,6,7,8,9]) ? intval(setting('water_location')) : 9;
                                $water_image = trim(setting('water_image'));
                                $water_arr = explode('.', $water_image);
                                $water_ext = strtolower(array_pop($water_arr));
                                if (in_array($water_ext, ['jpg', 'png', 'gif', 'jpeg'])) {
                                    $water_opacity = intval(setting('water_image_opacity')) <= 100 ? intval(setting('water_image_opacity')) : 80;
                                    $image = \think\Image::open(WWW_ROOT . $file);
                                    $image->water(WWW_ROOT . $water_image, $water_location, $water_opacity)->save(WWW_ROOT . $file);
                                }
                            }
                        }
                    }
                    $this[$field] = $file;
                } elseif ($elem === 'file') {
                    $this[$field] = $file;
                } else {
                    $this[$field] = $file;
                }
                if ($this->form[$field]['upload']['nameField']) {
                    $this[$this->form[$field]['upload']['nameField']] = $info['name'];
                }
                if ($this->form[$field]['upload']['sizeField']) {
                    $this[$this->form[$field]['upload']['sizeField']] = $info['size'];
                }                
                //\think\Hook::listen('upload', ['file' => $file, 'size' => $info['size'], 'basename' => implode(',',$filename_parts), 'ext' => $ext, 'field' => $field, 'model' => $this->name]);
            }
        }
        return true;
    }
    
    protected  function getResizeInfo($info, $mdl, $data)
    {
        list($resize_width, $resize_height, $resize_method) = $info;
        
        if($resize_width === 0 || $resize_height === 0 || $resize_method === 0){
            if (isset($mdl->parentModel)) {
                
                if ($mdl->parentModel != 'parent') {
                    $parent_conj = isset($mdl->assoc[$mdl->parentModel]['foreignKey']) ? $mdl->assoc[$mdl->parentModel]['foreignKey'] : Loader::parseName($mdl->parentModel) . '_id';
                    $parent_mdl = model($mdl->parentModel);
                }else{
                    $parent_conj = 'parent_id';
                    $parent_mdl = $mdl;
                }
                
                $parent_m = $parent_mdl->name;
                
                if (isset($parent_mdl->parentModel)) {
					if ($parent_mdl->parentModel != 'parent') {
                        $pp_conj = isset($parent_mdl->assoc[$parent_mdl->parentModel]['foreignKey']) ? $parent_mdl->assoc[$mdl->parentModel]['foreignKey'] : Loader::parseName($parent_mdl->parentModel) . '_id';
					} else {
		                 $pp_conj = 'parent_id';
					}
				}
                
                if (!in_array($parent_m, ['Menu'])) {
                    $parent_fields = [];
                    foreach (['resize_width', 'resize_height', 'resize_method'] as $field){
						if (isset($parent_mdl->form[$field])) {
							$parent_fields[] = $field;
						}
					}
                    
					if(isset($pp_conj)){
						$parent_fields[] = $pp_conj;
					}
                    if ($data[$parent_conj]) {
                        $parent_data = $parent_mdl->where(['id' => $data[$parent_conj]])->field($parent_fields)->find();
                        if ($parent_data) {
                            $parent_data = $parent_data->getArray();
                        }
                        
                    } else {
                        $parent_data = [];
                    }
                    
                } else {
                    switch($parent_m){
						case 'Menu':
							$parent_data = menu($data[$parent_conj]);
                            if (!$parent_data) $parent_data = [];
							break;
						default:
							exception("未知的缓存读取方法[{$parent_m}]");
					}
                    
                }
                
                if ($parent_data) {
                    if ($resize_width === 0 && isset($parent_data['resize_width'])){
						$resize_width = intval($parent_data['resize_width']);
					}
					if ($resize_height === 0 && isset($parent_data['resize_height'])){
						$resize_height = intval($parent_data['resize_height']);
					}
					if ($resize_method === 0 && isset($parent_data['resize_method'])){
						$resize_method = intval($parent_data['resize_method']);
					}
                } else {
                    if($resize_width === 0)
						$resize_width = intval(setting('thumb_width'));
					if($resize_height === 0)
						$resize_height = intval(setting('thumb_height'));
					if($resize_method === 0)
						$resize_method = intval(setting('thumb_method'));
                }
                
                if($resize_width === 0 || $resize_height === 0 || $resize_method === 0){
					list($resize_width, $resize_height, $resize_method) = $this->getThumbInfo([$resize_width, $resize_height, $resize_method], $parent_mdl, $parent_data);
				}
            } else {
                if($resize_width === 0)
					$resize_width = intval(setting('thumb_width'));
				if($resize_height === 0)
					$resize_height = intval(setting('thumb_height'));
				if($resize_method === 0)
					$resize_method = intval(setting('thumb_method'));
            } 
        }
            
        return [$resize_width, $resize_height, $resize_method];
    }
    
    protected  function getThumbInfo($info, $mdl, $data)
    {
        list($thumb_width, $thumb_height, $thumb_method) = $info;
        
        if($thumb_width === 0 || $thumb_height === 0 || $thumb_method === 0){
            if (isset($mdl->parentModel)) {
                
                if ($mdl->parentModel != 'parent') {
                    $parent_conj = isset($mdl->assoc[$mdl->parentModel]['foreignKey']) ? $mdl->assoc[$mdl->parentModel]['foreignKey'] : Loader::parseName($mdl->parentModel) . '_id';
                    $parent_mdl = model($mdl->parentModel);
                }else{
                    $parent_conj = 'parent_id';
                    $parent_mdl = $mdl;
                }
                
                $parent_m = $parent_mdl->name;
                
                if (isset($parent_mdl->parentModel)) {
					if ($parent_mdl->parentModel != 'parent') {
                        $pp_conj = isset($parent_mdl->assoc[$parent_mdl->parentModel]['foreignKey']) ? $parent_mdl->assoc[$mdl->parentModel]['foreignKey'] : Loader::parseName($parent_mdl->parentModel) . '_id';
					} else {
		                 $pp_conj = 'parent_id';
					}
				}
                
                if (!in_array($parent_m, ['Menu'])) {
                    $parent_fields = [];
                    foreach (['thumb_width', 'thumb_height', 'thumb_method'] as $field){
						if (isset($parent_mdl->form[$field])) {
							$parent_fields[] = $field;
						}
					}
                    
					if(isset($pp_conj)){
						$parent_fields[] = $pp_conj;
					}
                    if ($data[$parent_conj]) {
                        $parent_data = $parent_mdl->where(['id' => $data[$parent_conj]])->field($parent_fields)->find();
                        if ($parent_data) {
                            $parent_data = $parent_data->getArray();
                        }
                        
                    } else {
                        $parent_data = [];
                    }
                    
                } else {
                    switch($parent_m){
						case 'Menu':
							$parent_data = menu($data[$parent_conj]);
                            if (!$parent_data) $parent_data = [];
							break;
						default:
							exception("未知的缓存读取方法[{$parent_m}]");
					}
                    
                }
                
                if ($parent_data) {
                    if ($thumb_width === 0 && isset($parent_data['thumb_width'])){
						$thumb_width = intval($parent_data['thumb_width']);
					}
					if ($thumb_height === 0 && isset($parent_data['thumb_height'])){
						$thumb_height = intval($parent_data['thumb_height']);
					}
					if ($thumb_method === 0 && isset($parent_data['thumb_method'])){
						$thumb_method = intval($parent_data['thumb_method']);
					}
                } else {
                    if($thumb_width === 0)
						$thumb_width = intval(setting('thumb_width'));
					if($thumb_height === 0)
						$thumb_height = intval(setting('thumb_height'));
					if($thumb_method === 0)
						$thumb_method = intval(setting('thumb_method'));
                }
                
                if($thumb_width === 0 || $thumb_height === 0 || $thumb_method === 0){
					list($thumb_width, $thumb_height, $thumb_method) = $this->getThumbInfo([$thumb_width, $thumb_height, $thumb_method], $parent_mdl, $parent_data);
				}
            } else {
                if($thumb_width === 0)
					$thumb_width = intval(setting('thumb_width'));
				if($thumb_height === 0)
					$thumb_height = intval(setting('thumb_height'));
				if($thumb_method === 0)
					$thumb_method = intval(setting('thumb_method'));
            } 
        } 
            
        return [$thumb_width, $thumb_height, $thumb_method];
    }
    
    

    public function forceError($field, $error)
    {
        $this->error[$field] = $error;
        return true;
    }

    ##自动获取关联查询数据
    public function getAssocData($assocList = null)
    {
        if ($assocList === null) {
            return $this->toArray();
        }
        if ($assocList === true) {
            $assocList = array_keys($this->assoc);
        }
        settype($assocList, 'array');
        $assocList = Hash::normalize($assocList);
        foreach ($assocList as $assocModel => $assocInfo) {
            $this->assocUse[$assocModel] = (array)$assocInfo + $this->assoc[$assocModel];
            $this->$assocModel;
        }
        return $this->toArray();
    }


    public function getArray($data = null, $assocList = true)
    {
        if ($data === null) {
            $data = $this;
        }
        if (!is_object($data)) {
            return $data;
        }
        if (strpos('think\model\Collection', get_class($data)) === false) {
            if ($assocList && $assocList !== true) {
                return $data->getAssocData($assocList);
            } else {
                $assoc = array_keys(Hash::normalize((array)$data->assoc));
                $data = $data->toArray();
                $midd_data = [];
                foreach ($data as $key => $value) {
                    if ((!in_array(parse_name($key, 1), (array)$assoc))) {
                        $midd_data[$key] = $value;
                        continue;
                    }
                    $midd_data[parse_name($key, 1)] = $value ? $value : [];
                }
                return $midd_data;
            }
        } else {
            $return_data = [];
            foreach ($data as $key => $value) {
                $return_data[$key] = $this->getArray($value, $assocList);
            }
            return $return_data;
        }
    }


    public function parseWith($with = [])
    {
        $return = [];
        $with = Hash::normalize((array)$with);
        foreach ($with as $assoc => $info) {
            $assocModel = !isset($this->assoc[$assoc]['foreign']) ? $assoc : $this->assoc[$assoc]['foreign'];
            if ($info['field']) {
                $info['field'] = array_merge([model($assocModel)->getPk()], (array)$info['field'], (array)$this->assoc[$assoc]['field']);
            }
            $GLOBALS['assocUse'][$assoc] = array_merge((array)$info, $this->assoc[$assoc]);
            $return[] = $assoc;
        }
        return $return;
    }


    public function __get($name)
    {
        if (array_key_exists($name, (array)$this->assoc)) {
            $modelRelation = $this->$name();
            $value = $this->getRelationData($modelRelation);
            $this[$name] = $value;
            return $this;
        }
        //核心代码      
        return $this->getAttr($name);
    }


    public function __call($method, $args)
    {

        if (array_key_exists(Loader::parseName($method, 1), (array)$this->assoc)) {
            $method = Loader::parseName($method, 1);
            $this->assocUse[$method] = $GLOBALS['assocUse'][$method];
            if (empty($this->assocUse[$method])) {
                $this->assocUse[$method] = $this->assoc[$method];
            }
            $assocInfo = $this->assocUse[$method];
            $assocType = isset($assocInfo['type']) ? $assocInfo['type'] : null;
            $assocModel = !isset($assocInfo['foreign']) ? $method : $assocInfo['foreign'];
            if ($assocType == null) {
                exception("关联模型【{$assocModel}】没有定义type类型？\r\npublic \$assoc   = array('{$assocModel}'=>array('type'=>'belongsTo或hasOne或hasMany'))");
            }
            switch ($assocType) {
                case 'belongsTo':
                    $foreignKey = isset($assocInfo['foreignKey']) ? $assocInfo['foreignKey'] : Loader::parseName($method) . '_id';
                    $assocObj = $this->belongsTo($assocModel, $foreignKey, isset($assocInfo['localKey']) ? $assocInfo['localKey'] : "", isset($assocObj['alias']) ? $assocObj['alias'] : [], isset($assocInfo['joinType']) ? strtolower($assocInfo['joinType']) : 'INNER')->setEagerlyType(1);
                    break;
                case 'hasOne':
                    $foreignKey = isset($assocInfo['foreignKey']) ? $assocInfo['foreignKey'] : Loader::parseName($this->name) . '_id';
                    if (!empty($assocInfo['field'])) {
                        if (!in_array($foreignKey, $assocInfo['field'])) {
                            $assocInfo['field'] = array_merge($assocInfo['field'], [$foreignKey]);
                        }
                    }
                    $assocObj = $this->hasOne($assocModel, $foreignKey, isset($assocInfo['localKey']) ? $assocInfo['localKey'] : "", isset($assocObj['alias']) ? $assocObj['alias'] : [], isset($assocInfo['joinType']) ? strtolower($assocInfo['joinType']) : 'INNER')->setEagerlyType(1);
                    break;
                case 'hasMany':
                    $foreignKey = isset($assocInfo['foreignKey']) ? $assocInfo['foreignKey'] : Loader::parseName($this->name) . '_id';
                    if (!empty($assocInfo['field'])) {
                        if (!in_array($foreignKey, $assocInfo['field'])) {
                            $assocInfo['field'] = array_merge($assocInfo['field'], [$foreignKey]);
                        }
                    }
                    $assocObj = $this->hasMany($assocModel, $foreignKey, isset($assocInfo['localKey']) ? $assocInfo['localKey'] : "");
                    break;
                case 'hasManyThrough':
                    $assocObj = $this->hasManyThrough($assocModel, $assocInfo['through'], isset($assocInfo['foreignKey']) ? $assocInfo['foreignKey'] : "", $assocInfo['throughKey'] ? $assocInfo['throughKey'] : "", $assocInfo['localKey'] ? $assocInfo['localKey'] : "");
                    break;
                case 'belongsToMany':
                    $assocObj = $this->belongsToMany($assocModel, isset($assocInfo['table']) ? $assocInfo['table'] : "", $assocInfo['foreignKey'] ? $assocInfo['foreignKey'] : "", $assocInfo['localKey'] ? $assocInfo['localKey'] : "");
                    break;
                default:
                    exception("关联类型【{$assocType}】未定义");
                    break;
                //多态关联
            }

            //连贯操作
            foreach ($assocInfo as $assocMethod => $assocArgs) {
                if (in_array($assocMethod, array('field', 'where', 'order', 'limit', 'bind'))) {
                    $assocObj = $assocObj->$assocMethod($assocArgs);
                }
            }
            $this->assocUse[$method] = array();
            return $assocObj;
        } else {
            ##直接复制Model的__call代码
            return call_user_func_array([$this->db(), $method], $args);
        }
    }

    public function checkTypeOfMenu($value, $rule, $data)
    {
        $menuType = menu($value, 'type');
        if (empty($menuType)) {
            $menuType = model('Menu')->where('id', $value)->column('type');
            if ($menuType) {
                $menuType = $menuType[0];
            }
        }
        if ($menuType != $this->name) {
            return '你选择的栏目【所属类型】并非' . $this->cname;
        }
        return true;
    }
    
    public function getFamily($field, $id){
        if ($this->form[$field]['foreign']) {
            list($foreign_model, $foreign_field) = PluginSplit(trim($this->form[$field]['foreign'])); 
            $foreign_model = model($foreign_model); 
        } else {
            $foreign_model = $this;
            if ($this->display) {
                $foreign_field = $this->display;
            } else {
                $foreign_field = $this->getPk();
            }
        }
        if ($foreign_model->form['family']) {
            $family = $foreign_model->where([$foreign_model->getPk() => $id])->value('family');
            $family = explode(',', $family);
            array_pop($family);
            array_shift($family);
            return $family;
        } else {
            $parents = $foreign_model->getParentIds($id, 0);
            if (!empty($parents)) {
                $parents = array_reverse($parents);
                array_shift($parents);
            }
            $family = array_merge($parents, [$id]);
            return $family;
        }
    }
    
    public function getParentIds($child_id, $deep = 1, $now_deep = 1) {
        $list = $this->field(['parent_id'])->where(['id' => $child_id])->select();   
        $list = $this->getArray($list);
        $returnRet = [];
        if (!empty($list)) {
            $list = array_keys(Hash::combine($list, '{n}.parent_id'));
            $returnRet = array_merge($returnRet, $list);
            if ($deep === 0 || $now_deep < $deep) {
                foreach ($list as $parent) {
                    $returnRet = array_merge($returnRet, $this->getParentIds($parent, $deep, $now_deep++));
                }
            }
        }
        return array_unique($returnRet);
    }
    
    public function getChildrenIds($parent_id, $deep = 1, $now_deep = 1) {
        $list = $this->field(['id'])->where(['parent_id' => $parent_id])->select();   
        $list = $this->getArray($list);
        
        $returnRet = [];
        if (!empty($list)) {
            $list = array_keys(Hash::combine($list, '{n}.id'));
            $returnRet = array_merge($returnRet, $list) ;
            if ($deep === 0 || $now_deep < $deep) {
                foreach ($list as $child) {
                    $returnRet = array_merge($returnRet, $this->getChildrenIds($child, $deep, $now_deep++));
                }
            }
        }
        return array_unique($returnRet);
    }
    
    protected function getChildren($threaded, $key = 'children')
    {
        foreach ((array)$threaded as $parent_id => $childen_threaded) {
            $this->cache[$key][$parent_id] = array_keys((array)$childen_threaded);
            if ($childen_threaded) {
                $this->getChildren($childen_threaded, $key);
            }
        }
    }

    ##自行准备数据$listData，实现无限极数据处理，提高数据库效率
    protected function threaded($parent_id = 1, $listData = array())
    {
        if (empty($listData)) {
            return false;
        }
        $treeObj = Hash::combine($listData, '{n}[parent_id=' . $parent_id . '].id', '{n}[parent_id=' . $parent_id . ']');
        $treeList = [];
        if ($treeObj) {
            foreach ($treeObj as $item) {
                $treeList[$item['id']] = $this->threaded($item['id'], $listData);
            }
        }
        return $treeList;
    }
    
    /**
    * 多级联动数据处理方法
    * @param $objects  一般为你select查询以后的结果，里面包含了所有联动数据对象，  数据里面必须包含id、parent_id、展示等字段（如果数据量大建议，查询的时候只查询该3个字段）
    * @param $name   给你的联动取一个名字，定义form字段属性时会使用到 ，用来标识你的联动数据，所以不要和其他联动数据重复
    * @param $field  展示字段 ，默认为title
    */
    protected function multi_select($objects, $name = null, $field = 'title') 
    {
        if (empty($objects) || !is_array($objects)) {
            return false;
        }
        if ($name === null) {
            $name = $this->name;
        }
        $name = 'multi_select_' . parse_name($name);
        if (is_object($objects[0])) {
            $objects = $this->getArray($objects);
        }
        $objects = Hash::combine($objects, '{n}.id', '{n}');
        
        $cache['data']['top_id'] = min(array_keys($objects));
        $cache['data']['count'] = count($objects);
        $cache['data']['field'] = $field;
        $threaded[$cache['data']['top_id']] = $this->threaded($cache['data']['top_id'], $objects);
        $this->getChildren($threaded, 'options');
        $cache['options'] = $this->cache['options'];
        unset($this->cache['options']);
        $cache['list'] = $objects;
        write_file_cache($name, $cache);
        return true;
    }
    
    
}
