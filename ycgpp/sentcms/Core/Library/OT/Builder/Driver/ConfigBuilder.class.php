<?php
namespace OT\Builder\Driver;

class ConfigBuilder extends \OT\Builder\Builder{
	
    private $_savePostUrl = array();
    private $_group = array();
    private $_callback = null;

    public function callback($callback){
        $this->_callback = $callback;
        return $this;
    }

   /**键，一般用于内部调用
     * @param      $name
     * @param      $title
     * @param null $subtitle
     * @param      $type
     * @param null $opt
     * @return $this
     * @auth 陈一枭
     */
    public function key($name, $title, $subtitle = null, $type, $opt = null)
    {
        $key = array('name' => $name, 'title' => $title, 'subtitle' => $subtitle, 'type' => $type, 'opt' => $opt);
        $this->_keyList[] = $key;
        return $this;
    }

    /**只读文本
     * @param      $name
     * @param      $title
     * @param null $subtitle
     * @return AdminConfigBuilder
     * @auth 陈一枭
     */
    public function keyHidden($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'hidden');
    }

    /**只读文本
     * @param      $name
     * @param      $title
     * @param null $subtitle
     * @return AdminConfigBuilder
     * @auth 陈一枭
     */
    public function keyReadOnly($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'readonly');
    }

    /**文本输入框
     * @param      $name
     * @param      $title
     * @param null $subtitle
     * @return AdminConfigBuilder
     * @auth 陈一枭
     */
    public function keyText($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'text');
    }

    public function keyLabel($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'label');
    }

    public function keyTextArea($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'textarea');
    }

    public function keyInteger($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'integer');
    }

    public function keyUid($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'uid');
    }

    public function keyStatus($name = 'status', $title = '状态', $subtitle = null)
    {
        $map = array(-1 => '删除', 0 => '禁用', 1 => '启用', 2 => '未审核');
        return $this->keySelect($name, $title, $subtitle, $map);
    }

    public function keySelect($name, $title, $subtitle = null, $options)
    {
        return $this->key($name, $title, $subtitle, 'select', $options);
    }

    public function keyRadio($name, $title, $subtitle = null, $options)
    {
        return $this->key($name, $title, $subtitle, 'radio', $options);
    }

    public function keyCheckBox($name, $title, $subtitle = null, $options)
    {
        return $this->key($name, $title, $subtitle, 'checkbox', $options);
    }

    public function keyEditor($name, $title, $subtitle = null,$config='',$style = array('width'=>'500px','height'=>'400px'))
    {
        $toolbars ="toolbars:[[".$config."]]";
        if(empty($config)){
            $toolbars ="toolbars:[['source','|','bold','italic','underline','fontsize','forecolor','justifyleft','fontfamily','|','map','emotion','insertimage','insertcode']]";
        }
        if($config =='all'){
            $toolbars='all';
        }
        $key = array('name' => $name, 'title' => $title, 'subtitle' => $subtitle, 'type' => 'editor', 'config' => $toolbars,'style'=>$style);
        $this->_keyList[] = $key;
        return $this;
    }

    public function keyTime($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'datetime');
    }

    public function keyCreateTime($name = 'create_time', $title = '创建时间', $subtitle = null)
    {
        return $this->keyTime($name, $title, $subtitle);
    }

    public function keyBool($name, $title, $subtitle = null)
    {
        $map = array(1 => '是', 0 => '否');
        return $this->keyRadio($name, $title, $subtitle, $map);
    }

    public function keyUpdateTime($name = 'update_time', $title = '修改时间', $subtitle = null)
    {
        return $this->keyTime($name, $title, $subtitle);
    }

    public function keyKanban($name, $title, $subtitle=null, $opt){
        return $this->key($name, $title, $subtitle, 'kanban', $opt);
    }

    public function keyTitle($name = 'title', $title = '标题', $subtitle = null)
    {
        return $this->keyText($name, $title, $subtitle);
    }

    public function keyId($name = 'id', $title = '编号', $subtitle = null)
    {
        return $this->keyReadOnly($name, $title, $subtitle);
    }

    public function keyMultiUserGroup($name, $title, $subtitle = null)
    {
        $options = $this->readUserGroups();
        return $this->keyCheckBox($name, $title, $subtitle, $options);
    }

    public function keySingleImage($name, $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'picture');
    }

    public function keyMultiImage($name, $title, $subtitle = null,$limit='')
    {
         return $this->key($name, $title, $subtitle, 'pictures',$limit);
    }

    public function keySingleUserGroup($name, $title, $subtitle = null)
    {
        $options = $this->readUserGroups();
        return $this->keySelect($name, $title, $subtitle, $options);
    }

    /**
     * 添加城市选择（需安装城市联动插件）
     * @param  $title
     * @param  $subtitle
     * @return hook ChinaCity
     * @author LaoYang
     * @author @MingYangliu <xint5288@126.com>
     */
    public function keyCity($title,$subtitle)
    {
        //修正在编辑信息时无法正常显示已经保存的地区信息
        return $this->key(array('province','city','district'), $title, $subtitle, 'city');
    }


    /**
     * 增加数据时通过列表页选择相应的关联数据ID  -_-。sorry！表述不清楚..
     * @param  unknown $name     字段名
     * @param  unknown $title    标题
     * @param  string  $subtitle 副标题（说明）
     * @param  unknown $url      选择数据的列表页地址，U方法地址'index/index'
     * @return $this
     * @author @MingYangliu <xint5288@126.com>
     */
    public function keyDataSelect($name, $title, $subtitle = null, $url)
    {
        $urls = U($url,array('inputid'=>$name));
        return $this->key($name, $title, $subtitle, 'dataselect', $urls);
    }

    public function button($title, $attr = array(),$position = 'bottom')
    {
        $this->_buttonList[] = array('title' => $title, 'attr' => $attr, 'position' => $position);
        return $this;
    }

    public function buttonSubmit($url = '', $title = '确定')
    {
        if ($url == '') {
            $url = __SELF__;
        }
        $this->savePostUrl($url);

        $attr = array();
        $attr['class'] = "am-btn am-btn-primary submit-btn ajax-post";
        $attr['id'] = 'submit';
        $attr['type'] = 'submit';
        $attr['target-form'] = 'am-form-horizontal';
        return $this->button($title, $attr);
    }

    public function buttonBack($title = '返回')
    {
        $attr = array();
        $attr['onclick'] = 'javascript:history.back(-1);return false;';
        $attr['class'] = 'am-btn am-btn-default btn-return';
        return $this->button($title, $attr);
    }

    public function savePostUrl($url)
    {
        if ($url) {
            $this->_savePostUrl = $url;
        }
    }

    public function display($template){

        //数据处理
        $this->_keyList = $this->_formatData($this->_keyList);
        //分组数据处理
        foreach ($this->_group as $key => $value) {
            $this->_group[$key] = $this->_formatData($value);
        }

        //编译按钮的html属性
        foreach ($this->_buttonList as &$button) {
            $button['attr'] = $this->compileHtmlAttr($button['attr']);
        }

        //显示页面
        $this->assign('group', $this->_group);
        $this->assign('title', $this->_title);
        $this->assign('suggest', $this->_suggest);
        $this->assign('keyList', $this->_keyList);
        $this->assign('buttonList', $this->_buttonList);
        $this->assign('savePostUrl', $this->_savePostUrl);

        parent::display($template);
    }

    protected function _formatData($data){
        //将数据融入到key中
        foreach ($data as &$e) {
            if($e['type'] == 'multiInput'){
                $e['name'] = explode('|',$e['name']);
            }

            //修正在编辑信息时无法正常显示已经保存的地区信息/***修改的代码****/
            if(is_array($e['name'])){
                $i=0;
                $n = count($e['name']);
                while ($n>0) {
                    $e['value'][$i] = $this->_data[$e['name'][$i]];
                    $i++;
                    $n--;
                }
            } else {
                $e['value'] = $e['value'] ? $e['value'] : $this->_data[$e['name']];
            }
        }
        return $data;
    }

    /**
     * keyChosen  多选菜单
     * @param $name
     * @param $title
     * @param null $subtitle
     * @param $options
     * @return $this
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function keyChosen($name, $title, $subtitle = null, $options)
    {
        // 解析option数组
        if (key($options) === 0) {
            if (!is_array(reset($options))) {
                foreach ($options as $key => &$val) {
                    $val = array($val, $val);
                }
                unset($key, $val);
            }
        }else{
            foreach($options as $key=>&$val){
                foreach($val as $k=>&$v){
                    if(!is_array($v)){
                        $v = array($v, $v);
                    }
                }
                unset($k, $v);
            }
            unset($key, $val);
        }
        return $this->key($name, $title, $subtitle, 'chosen', $options);
    }


    /**
     * keyMultiInput  输入组组件
     * @param $name
     * @param $title
     * @param $subtitle
     * @param $config
     * @param null $style
     * @return $this
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function keyMultiInput($name,$title,$subtitle,$config,$style = null){
        empty($style) && $style = 'width:400px;';
        $key = array('name' => $name, 'title' => $title, 'subtitle' => $subtitle, 'type' => 'multiInput', 'config' => $config,'style'=>$style);
        $this->_keyList[] = $key;
        return $this;
    }

    /**插入配置分组
     * @param       $name 组名
     * @param array $list 组内字段列表
     * @return $this
     * @auth 肖骏涛
     */
    public function group($id,$list = array()){
        !is_array($list) && $list = explode(',',$list);
        $this->_group[$id] = $list;
        return $this;
    }

    public function groups($list = array()){
        foreach($list as $key =>$v){
            $this->_group[$key] =  is_array($v) ? $v :explode(',',$v);
        }
        return $this;
    }


    /**自动处理配置存储事件，配置项必须全大写
     * @auth 陈一枭
     */
    public function handleConfig()
    {
        if (IS_POST) {
            $success = false;
            $configModel = D('Config');
            foreach (I('') as $k => $v) {
                $config['name'] = '_' . strtoupper(CONTROLLER_NAME) . '_' . strtoupper($k);
                $config['type'] = 0;
                $config['title'] = '';
                $config['group'] = 0;
                $config['extra'] = '';
                $config['remark'] = '';
                $config['create_time'] = time();
                $config['update_time'] = time();
                $config['status'] = 1;
                $config['value'] = is_array($v)?implode(',',$v): $v;
                $config['sort'] = 0;
                if ($configModel->add($config, null, true)) {
                        $success = 1;
                }
                $tag = 'conf_' . strtoupper(CONTROLLER_NAME) . '_' . strtoupper($k);
                S($tag, null);
            }
            if ($success) {
                if($this->_callback){
                    $str = $this->_callback;
                    A(CONTROLLER_NAME)->$str(I(''));
                }
                header('Content-type: application/json');
                exit(json_encode(array('info' => '保存配置成功。', 'status' => 1, 'url' => __SELF__)));
            } else {
                header('Content-type: application/json');
                exit(json_encode(array('info' => '保存配置失败。', 'status' => 0, 'url' => __SELF__)));
            }


        } else {
            $configs = D('Config')->where(array('name' => array('like', '_' . strtoupper(CONTROLLER_NAME) . '_' . '%')))->limit(999)->select();
            $data = array();
            foreach ($configs as $k => $v) {
                $key = str_replace('_' . strtoupper(CONTROLLER_NAME) . '_', '', strtoupper($v['name']));
                $data[$key] = $v['value'];
            }
            return $data;
        }
    }

    private function readUserGroups()
    {
        $list = M('AuthGroup')->where(array('status' => 1))->order('id asc')->select();
        $result = array();
        foreach ($list as $group) {
            $result[$group['id']] = $group['title'];
        }
        return $result;
    }

    /**
     * parseKanbanArray  解析看板数组
     * @param $data
     * @param array $item
     * @param array $default
     * @return array|mixed
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function parseKanbanArray($data,$item=array(),$default=array()){

        if (empty($data)) {
            $head = reset($default);
            if(!array_key_exists("items",$head)){
                $temp=array();
                foreach($default as $k=>$v){
                    $temp[] = array('data-id'=>$k,'title'=>$k,'items'=>$v);
                }
                $default = $temp;
            }
            $data =$default;
        } else {
            $data = json_decode($data, true);
            $all = array();
            foreach ($data as $v) {
                $all = array_merge($all, $v['items']);
            }

            unset($v);
            foreach ($item as $val) {
                if (!in_array($val, $all)) {
                    $data[0]['items'][] = $val;
                }
            }
            foreach ($all as $v) {
                if (!in_array($v, $item)) {
                    foreach ($data as $key => $val) {
                        $key_search = array_search($v, $val['items']);
                        if (!is_bool($key_search)) {
                            unset($data[$key]['items'][$key_search]);
                        }
                    }
                }
            }
        }
        return $data;
    }
}