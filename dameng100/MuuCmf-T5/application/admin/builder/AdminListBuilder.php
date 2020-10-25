<?php
namespace app\admin\builder;

use think\Db;

class AdminListBuilder extends AdminBuilder
{
    private $_title;
    private $_suggest;
    private $_tips;
    private $_keyList = array();
    private $_buttonList = array();
    private $_explain = array();
    private $_data;
    private $_setStatusUrl;
    private $_searchPostUrl;
    private $_selectPostUrl;
    private $_setDeleteTrueUrl;
    private $_page; //分页HTML代码

    private $_search = array();
    private $_select = array();

    function __construct() {
       parent::__construct();
       //初始化值
       $this->_selectPostUrl = url();
       $this->_searchPostUrl = url();
   }
    /**设置页面标题
     * @param $title 标题文本
     * @return $this
     * @auth 陈一枭
     */
    public function title($title)
    {
        $this->_title = $title;
        $this->meta_title = $title;
        return $this;
    }

    /**
     * suggest  页面标题边上的提示信息
     * @param $suggest
     * @return $this
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function suggest($suggest)
    {
        $this->_suggest = $suggest;
        return $this;
    }

    public function tips($content)
    {
        $this->_tips = $content;
        return $this;
    }
    public function page($page)
    {
        $this->_page = $page; 
        return $this;
    }
    /**
     * @param $url string 已被U函数解析的地址
     * @return $this
     */
    public function setStatusUrl($url)
    {
        $this->_setStatusUrl = $url;
        return $this;
    }

    /**设置回收站根据ids彻底删除的URL
     * @param $url
     * @return $this
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setDeleteTrueUrl($url)
    {
        $this->_setDeleteTrueUrl = $url;
        return $this;
    }

    /**
     * 筛选下拉选择url
     * @param $url string 已被U函数解析的地址
     * @return $this
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setSelectPostUrl($url)
    {
        $this->_selectPostUrl = $url;
        return $this;
    }

    /**设置搜索提交表单的URL
     * @param $url
     * @return $this
     * @auth 陈一枭
     */
    /**原@auth 陈一枭
     *public function setSearchPostUrl($url)
     *{
     *  $this->_searchPostUrl = $url;
     *  return $this;
     *}
     */
    /**更新筛选搜索功能 ，修正连续提交多出N+个GET参数的BUG
     * @param $url   提交的getURL
     */
    public function setSearchPostUrl($url)
    {
        $this->_searchPostUrl = $url;
        return $this;
    }

    /**加入一个按钮
     * @param $title
     * @param $attr
     * @return $this
     * @auth 陈一枭
     */

    public function button($title, $attr = [])
    {
        $this->_buttonList[] = ['title' => $title, 'attr' => $attr];
        return $this;
    }

    /**加入新增按钮
     * @param        $href
     * @param string $title
     * @param array $attr
     * @return AdminListBuilder
     * @auth 大蒙<59262424@qq.com>
     */
    public function buttonNew($href, $title = '新增', $attr = [])
    {
        $attr['href'] = $href;
        $attr['class']='btn btn-ajax btn-info';
        return $this->button($title, $attr);
    }

    public function buttonAjax($url, $params, $title, $attr = [])
    {
        $attr['class'] = 'btn ajax-post';
        $attr['url'] = $this->addUrlParam($url, $params);
        $attr['target-form'] = 'ids';
        return $this->button($title, $attr);
    }

    /**加入模态弹窗按钮
     * @param $url
     * @param $params
     * @param $title
     * @param array $attr
     * @return $this
     * @author 郑钟良<zzl@ourstu.com> 大蒙<59262424@qq.com>
     */
    public function buttonModalPopup($url, $params, $title, $attr = [])
    {   
        if(!isset($attr['data-title'])){
           $attr['data-title'] = $title; 
        }
        //$attr中可选参数，data-title：模态框标题，target-form：要传输的数据
        $attr['modal-url'] = $this->addUrlParam($url, $params);
        $attr['data-role'] = 'modal_popup';
        $attr['class']='btn btn-warning';
        return $this->button($title, $attr);
    }

    public function buttonSetStatus($url, $status, $title, $attr = [])
    {
        $attr['class'] = isset($attr['class'])?$attr['class']: 'btn ajax-post';
        $attr['url'] = $this->addUrlParam($url, array('status' => $status));
        $attr['target-form'] = 'ids';
        return $this->button($title, $attr);
    }

    public function buttonDisable($url = null, $title = '禁用', $attr = [])
    {
        if (!$url) $url = $this->_setStatusUrl;
        $attr['class']='btn ajax-post btn-warning';
        return $this->buttonSetStatus($url, 0, $title, $attr);
    }

    public function buttonEnable($url = null, $title = '启用', $attr = [])
    {
        if (!$url) $url = $this->_setStatusUrl;
        $attr['class']='btn ajax-post btn-success';
        return $this->buttonSetStatus($url, 1, $title, $attr);
    }

    /**
     * 删除到回收站
     */
    public function buttonDelete($url = null, $title = '删除', $attr = [])
    {
        if (!$url) $url = $this->_setStatusUrl;
        $attr['class']='btn ajax-post btn-danger confirm';
        $attr['data-confirm'] = lang('_CONFIRM_DELETE_COMPLETELY_');
        return $this->buttonSetStatus($url, -1, $title, $attr);
    }

    public function buttonRestore($url = null, $title = '还原', $attr = [])
    {
        if (!$url) $url = $this->_setStatusUrl;
        return $this->buttonSetStatus($url, 1, $title, $attr);
    }

    /**清空回收站
     * @param null $model
     * @return $this
     * 该操作太尼玛危险~先不处理
     */
    public function buttonClear($model = null)
    {
        return $this->button(lang('_CLEAR_OUT_'), ['class' => 'btn btn-danger ajax-post tox-confirm', 'data-confirm' => lang('_CONFIRM_CLEAR_OUT_'), 'url' => url('', ['model' => $model]), 'target-form' => 'ids', 'hide-data' => 'true']);
    }

    /**彻底删除
     * @param null $url
     * @return $this
     */
    public function buttonDeleteTrue($url = null)
    {
        if (!$url) $url = $this->_setDeleteTrueUrl;//还未定义
        $attr['class'] = 'btn btn-danger ajax-post tox-confirm';
        $attr['data-confirm'] = lang('_CONFIRM_DELETE_COMPLETELY_');
        $attr['url'] = $url;
        $attr['target-form'] = 'ids';
        return $this->button(lang('_DELETE_COMPLETELY_'), $attr);
    }

    public function buttonSort($href, $title = '排序', $attr = [])
    {
        $attr['href'] = $href;
        return $this->button($title, $attr);
    }

    /**搜索
     * @param string $title 标题
     * @param string $name 键名
     * @param string $type 类型，默认文本
     * @param string $des 描述
     * @param        $attr 标签文本
     * @return $this
     * @auth 陈一枭
     */
    /**原@auth 陈一枭
     * public function search($title = '搜索', $name = 'key', $type = 'text', $des = '', $attr )
     * {
     * $this->_search[] = array('title' => $title, 'name' => $name, 'type' => $type, 'des' => $des, 'attr' => $attr);
     * return $this;
     * }
     */

    /**更新筛选搜索功能 ，修正连续提交多出N+个GET参数的BUG
     * @param string $title 标题
     * @param string $name 键名
     * @param string $type 类型，默认文本
     * @param string $des 描述
     * @param        $attr  标签文本
     * @param string $arrdb 择筛选项数据来源
     * @param string $arrvalue 筛选数据（包含ID 和value的数组:array(array('id'=>1,'value'=>'系统'),array('id'=>2,'value'=>'项目'),array('id'=>3,'value'=>'机构'));）
     * @return $this
     * @auth MingYang <xint5288@126.com>
     */
    public function search($title = '搜索', $name = 'key', $type = 'text', $des = '', $attr = '', $arrdb = '', $arrvalue = null)
    {

        if (empty($type) && $type = 'text') {
            $this->_search[] = ['title' => $title, 'name' => $name, 'type' => $type, 'des' => $des, 'attr' => $attr];
//            $this->setSearchPostUrl('');
        } else {
            if (empty($arrdb)) {
                $this->_search[] = ['title' => $title, 'name' => $name, 'type' => $type, 'des' => $des, 'attr' => $attr];
                $this->setSearchPostUrl('');
            } else {
                //TODO:呆完善如果$arrdb存在的就把当前数据表的$name字段的信息全部查询出来供筛选。
            }
        }
        return $this;
    }

    /**
     * 添加筛选功能
     * @param string $title 标题
     * @param string $name 键名
     * @param string $type 类型，默认文本
     * @param string $des 描述
     * @param        $attr  标签文本
     * @param string $arrdb 择筛选项数据来源
     * @param string $arrvalue 筛选数据（包含ID 和value的数组:array(array('id'=>1,'value'=>'系统'),array('id'=>2,'value'=>'项目'),array('id'=>3,'value'=>'机构'));）
     * @return $this
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function select($title = '筛选', $name = 'key', $type = 'select', $des = '', $attr, $arrdb = '', $arrvalue = null)
    {
        if (empty($arrdb)) {
            $this->_select[] = ['title' => $title, 'name' => $name, 'type' => $type, 'des' => $des, 'attr' => $attr, 'arrvalue' => $arrvalue];
        } else {
            //TODO:呆完善如果$arrdb存在的就把当前数据表的$name字段的信息全部查询出来供筛选。
        }
        return $this;
    }

    public function key($name, $title, $type, $opt = null, $width = '150px')
    {
        $key = ['name' => $name, 'title' => $title, 'type' => $type, 'opt' => $opt, 'width' => $width];
        $this->_keyList[] = $key;
        return $this;
    }

    /**显示纯文本
     * @param $name 键名
     * @param $title 标题
     * @return AdminListBuilder
     * @auth 陈一枭
     */
    public function keyText($name, $title)
    {
        return $this->key($name, text($title), 'text');
    }

    /**显示html
     * @param $name 键名
     * @param $title 标题
     * @return AdminListBuilder
     * @auth 陈一枭
     */
    public function keyHtml($name, $title, $width = '150px')
    {
        return $this->key($name, html($title), 'html', null, $width);
    }

    public function keyMap($name, $title, $map)
    {
        return $this->key($name, $title, 'map', $map);
    }

    public function keyId($name = 'id', $title = 'ID')
    {
        return $this->keyText($name, $title);
    }

    /**
     * 图标展示
     * @param string $name
     * @param string $title
     * @return $this
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function keyIcon($name = 'icon', $title = '图标')
    {
        return $this->key($name, $title, 'icon');
    }

    /**
     * @param $name
     * @param $title
     * @param $getUrl Closure|string
     * 可以是函数或url函数解析的字符串。如果是字符串，该函数可附带一个$flag定义的参数
     *
     * @return $this
     */
    public function keyLink($name, $title, $getUrl, $flag='id')
    {
        //如果getUrl是一个字符串，则表示getUrl是一个U函数解析的字符串
        if (is_string($getUrl)) {
            $getUrl = $this->createDefaultGetUrlFunction($getUrl, $flag);
        }

        //修整添加多个空字段时显示不正常的BUG@mingyangliu
        if (empty($name)) {
            $name = $title;
        }

        //添加key
        return $this->key($name, $title, 'link', $getUrl);
    }

    public function keyStatus($name = 'status', $title = '状态')
    {
        $map = [-1 => lang('_DELETE_'), 0 => lang('_DISABLE_'), 1 => lang('_ENABLED_'), 2 => lang('_UNAUDITED_')];
        return $this->key($name, $title, 'status', $map);
    }

    public function keyYesNo($name, $title)
    {
        $map = [0 => lang('_NO_'), 1 => lang('_YES_')];
        return $this->keymap($name, $title, $map);
    }

    public function keyBool($name, $title)
    {
        return $this->keyYesNo($name, $title);
    }

    public function keyImage($name, $title)
    {
        return $this->key($name, $title, 'image');
    }

    public function keyTime($name, $title)
    {
        return $this->key($name, $title, 'time');
    }

    public function keyCreateTime($name = 'create_time', $title = '创建时间')
    {
        return $this->keyTime($name, $title);
    }

    public function keyUpdateTime($name = 'update_time', $title = '更新时间')
    {
        return $this->keyTime($name, $title);
    }

    public function keyUid($name = 'uid', $title = '用户')
    {
        return $this->key($name, $title, 'uid');
    }

    public function keyNickname($name = 'uid', $title, $subtitle = null)
    {
        return $this->key($name, $title, $subtitle, 'nickname');
    }

    public function keyTitle($name = 'title', $title = '标题')
    {
        return $this->keyText($name, $title);
    }

    //关联表字段显示+URL连接
    public function keyJoin($name, $title, $mate, $return, $model, $url = '')
    {
        $map = array('mate' => $mate, 'return' => $return, 'model' => $model, 'url' => $url);
        return $this->key($name, $title, 'Join', $map);
    }

    /**
     * 模态弹窗
     * @param $getUrl
     * @param $text
     * @param $title
     * @param array $attr
     * @param str $class
     * @return $this
     * @author 大蒙<59262424@qq.com> 完善
     * $hide 根据条件判断是否隐藏该操作，如根据状态字段status=1可这样设置['status','=','1']
     */
    public function keyDoActionModalPopup($getUrl, $text, $title, $attr = [] ,$class='btn-primary',$hide=[], $flag = 'id')
    {
        //attr中需要设置data-title，用于设置模态弹窗标题
        $attr['data-title'] = $text;
        $attr['data-role'] = 'modal_popup';

        //获取默认getUrl函数
        if (is_string($getUrl)) {
            $getUrl = $this->createDefaultGetUrlFunction($getUrl,$flag);
        }
        //确认已经创建了DOACTIONS字段
        $doActionKey = null;
        foreach ($this->_keyList as $key) {
            if ($key['name'] === 'DOACTIONS') {
                $doActionKey = $key;
                break;
            }
        }
        if (!$doActionKey) {
            $this->key('DOACTIONS', $title, 'doaction', $attr);
        }

        //找出第一个DOACTIONS字段
        $doActionKey = null;
        foreach ($this->_keyList as &$key) {
            if ($key['name'] == 'DOACTIONS') {
                $doActionKey = &$key;
                break;
            }
        }

        //在DOACTIONS中增加action
        $doActionKey['opt']['actions'][] = ['text' => $text, 'get_url' => $getUrl, 'opt' => $attr, 'class' => $class, 'hide' => $hide];
        return $this;
    }

    public function keyDoAction($getUrl, $text, $title = '操作', $class = 'btn-primary', $hide=[], $flag = 'id')
    {
        //获取默认getUrl函数
        if (is_string($getUrl)) {
            $getUrl = $this->createDefaultGetUrlFunction($getUrl,$flag);
        }

        //确认已经创建了DOACTIONS字段
        $doActionKey = null;
        foreach ($this->_keyList as $key) {
            if ($key['name'] === 'DOACTIONS') {
                $doActionKey = $key;
                break;
            }
        }
        
        if (!$doActionKey) {
            $this->key('DOACTIONS', $title, 'doaction', array());
        }
        
        //找出第一个DOACTIONS字段
        $doActionKey = null;
        foreach ($this->_keyList as &$key) {
            if ($key['name'] == 'DOACTIONS') {
                $doActionKey = &$key;
                break;
            }
        }

        //在DOACTIONS中增加action
        $doActionKey['opt']['actions'][] = ['text' => $text, 'get_url' => $getUrl, 'class' => $class, 'hide' => $hide];
        
        return $this;
    }
    /**
     * ajax操作链接
     * @param  [type] $getUrl [description]
     * @param  string $text   [description]
     * @return [type]         [description]
     */
    public function keyDoActionAjax($getUrl, $text = 'Ajax', $class = 'btn-primary' ,$hide=[])
    {
        return $this->keyDoAction($getUrl, $text, '操作', 'ajax-get '. $class, $hide);
    }
    /**
     * 编辑操作
     * @param  [type] $getUrl [description]
     * @param  string $text   [description]
     * @return [type]         [description]
     */
    public function keyDoActionEdit($getUrl, $text = '编辑',$hide=[])
    {
        return $this->keyDoAction($getUrl, '<i class="icon icon-edit"></i> '.$text, '操作', 'btn-success', $hide);
    }
    /**
     * 禁用操作
     * @param  [type] $getUrl [description]
     * @param  string $text   [description]
     * @return [type]         [description]
     */
    public function keyDoActionDisable($getUrl, $text = '禁用', $hide=[])
    {
        return $this->keyDoAction($getUrl, '<i class="icon icon-minus-sign"></i> '. $text, '禁用', 'btn-warning ajax-get', $hide);
    }
    /**
     * 删除操作
     * @param  [type] $getUrl [description]
     * @param  string $text   [description]
     * @return [type]         [description]
     */
    public function keyDoActionDelete($getUrl, $text = '删除', $hide=[])
    {
        return $this->keyDoAction($getUrl, '<i class="icon icon-trash"></i> '.$text, '操作','btn-danger ajax-get confirm', $hide);
    }
    /**
     * 还原操作，存在获取数据ID BUG
     * @param  string $text [description]
     * @return [type]       [description]
     */
    public function keyDoActionRestore($text = '还原', $hide=[])
    {
        $that = $this;
        $setStatusUrl = $this->_setStatusUrl;
        $getUrl = function () use ($that, $setStatusUrl) {
            return $that->addUrlParam($setStatusUrl, array('status' => 1));
        };
        return $this->keyDoAction($getUrl, $text,'操作','btn-primary ajax-get',$hide);
    }

    public function keyTruncText($name, $title, $length)
    {
        return $this->key($name, $title, 'trunktext', $length);
    }

    /**
     * 不要给listRows默认值，因为开发人员很可能忘记填写listRows导致翻页不正确
     * @param $totalCount
     * @param $listRows
     * @return $this
     *
     * 已弃用
     */
    public function pagination($totalCount, $listRows)
    {
        $this->_pagination = array('totalCount' => $totalCount, 'listRows' => $listRows);
        return $this;
    }

    /**
     * 列表说明文字,位于列表页最下部
     * @param $title
     * @param $content
     * @return $this
     */
    public function explain($title, $content)
    {
        $this->_explain = ['title' => $title, 'content' => $content];
        return $this;
    }

    public function data($list)
    {
        if(is_object($list)) {
            $list = $list->toArray()['data'];
        }
        $this->_data = $list;
        return $this;
    }

    /**
     * $solist 判断是否属于选择返回数据的列表页，如果是在列表页->display('admin_solist');@mingyangliu
     * */
    public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '', $solist = '')
    {

        //key类型的等价转换
        //map转换成text
        $this->convertKey('map', 'text', function ($value, $key) {
            if(!empty($value) || $value == 0) {
                return $key['opt'][$value];
            }
            return '';
        });

        //uid转换成text
        $this->convertKey('uid', 'text', function ($value) {

            $value = query_user(array('nickname', 'uid', 'space_url'), $value);
            if(!empty($value['uid'])){
                return "<a href='" . $value['space_url'] . "' target='_blank'>[{$value['uid']}]" . $value['nickname'] . '</a>';
            }
            return '';
        });

        //nickname转换成text
        $this->convertKey('nickname', 'text', function ($value) {
            $value = query_user(array('nickname', 'uid', 'space_url'), $value);
            return "<a href='" . $value['space_url'] . "' target='_blank'>[{$value['uid']}]" . $value['nickname'] . '</a>';
        });

        //time转换成text
        $this->convertKey('time', 'text', function ($value) {
            if ($value != 0) {
                return time_format($value);
            } else {
                return '-';
            }
        });

        //trunctext转换成text
        $this->convertKey('trunktext', 'text', function ($value, $key) {
            $length = $key['opt'];
            return msubstr($value, 0, $length);
        });

        //text转换成html
        $this->convertKey('text', 'html', function ($value) {
            return $value;
        });

        //link转换为html
        $this->convertKey('link', 'html', function ($value, $key, $item) {
            $value = htmlspecialchars($value);
            $getUrl = $key['opt'];
            $url = $getUrl($item);
            //允许字段为空，如果字段名为空将标题名填充到A变现里
            if (!$value) {
                return "<a href=\"$url\" target=\"_blank\">" . $key['title'] . "</a>";
            } else {
                return "<a href=\"$url\" target=\"_blank\">$value</a>";
            }
        });

        //如果icon为空
        $this->convertKey('icon', 'html', function ($value, $key, $item) {
            $value = htmlspecialchars($value);
            if ($value == '') {
                $html = lang('_NONE_');
            } else {
                $html = "<i class=\"$value\"></i> $value";
            }
            return $html;
        });

        //image转换为图片
        //大蒙 <修复无图片ID时默认判断为图片路径的BUG>
        $this->convertKey('image', 'html', function ($value, $key, $item) {

            if (is_numeric($value)) {//value是图片id
                if($value===0 || $value==='0'){
                    $html = '<div class="popup-gallery"><img src="' .STATIC_URL. '/common/images/nopic.png" style="width:80px;height:80px"></div>';
                    return $html;
                }
                $value = htmlspecialchars($value);
                $sc_src = get_cover($value, 'path');
                $src = getThumbImageById($value, 80, 80);
                $sc_src = $sc_src == '' ? $src : $sc_src;
                $html = "<div class='popup-gallery'><a title=\"" . lang('_VIEW_BIGGER_') . "\" href=\"$sc_src\"><img src=\"$sc_src\"/ style=\"width:80px;height:80px\"></a></div>";
            } else {//value是图片路径
                $sc_src = $value;
                $html = "<div class='popup-gallery'><a title=\"" . lang('_VIEW_BIGGER_') . "\" href=\"$sc_src\"><img src=\"$sc_src\"/ style=\"width:80px;height:80px\"></a></div>";
            }
            return $html;
        });

        //doaction转换为html
        $this->convertKey('doaction', 'html', function ($value, $key, $item) {
            $actions = $key['opt']['actions'];
            $result = array();
            foreach ($actions as $action) {
                $getUrl = $action['get_url'];
                $linkText = $action['text'];
                $url = $getUrl($item);
                $class = $action['class'];

                //是否设置了根据条件隐藏操作按钮,以下写法支持，多条件时第4个留空默认&&
                //['status',['>',1],['<',3],'&&'];
                //['status',['>',1],['<',3]];
                //['status','>1','<3','||'];
                //['status','>1','<3'];
                //['status','>',1];
                if(isset($action['hide'])){
                    $hide_arr = $action['hide'];
                    
                    if(!empty($hide_arr)){ 

                        $a = $hide_arr;
                        //判断是数字或字符串
                        if(is_numeric($item[$a[0]])){
                            $p = $item[$a[0]];
                        }else{
                            $p = '"'.$item[$a[0]].'"';
                        }

                        if(is_array($a[1]) && is_array($a[2])){
                            if(isset($a[3])){
                                $d = $p.$a[1][0].$a[1][1].' '.$b[3].' '.$p.$a[2][0].$a[2][1];   
                            }else{
                                $d = $p.$a[1][0].$a[1][1].' && '.$p.$a[2][0].$a[2][1];
                            }
                        }else if(is_string($a[1]) && (is_string($a[2]) || is_int($a[2]))){
                            if(isset($a[3])){
                                $d = $p.$a[1].' '.$a[3].' '.$p.$a[2];
                            }else{
                                if(strstr($a[2],'<') || strstr($a[2],'>') || strstr($a[2],'=')){
                                    $d = $p.$a[1].' && '.$p.$a[2];
                                }else{
                                    $d = $p.$a[1].$a[2];
                                }
                            }
                        }
                        
                        $hide_str_res =  eval("return $d;");

                        if($hide_str_res){
                            //符合条件跳出本次循环
                            continue;
                        }
                    }
                }

                if (isset($action['opt'])) {
                    
                    $content = array();
                    foreach ($action['opt'] as $key => $value) {
                        $value = htmlspecialchars($value);
                        $content[] = "$key=\"$value\"";
                    }
                    $content = implode(' ', $content);
                    

                    if (isset($action['opt']['data-role']) && $action['opt']['data-role'] == "modal_popup") {//模态弹窗
                        $result[] = "<a href=\" javascrapt:void(0);\" class=\"$class btn btn-mini $class\" modal-url=\"$url\" " . $content . ">$linkText</a>";
                    } else {
                        $result[] = "<a href=\"$url\" class=\"btn btn-mini $class\" " . $content . ">$linkText</a>";
                    }
                } else {
                    $result[] = "<a href=\"$url\" class=\"btn btn-mini $class\">$linkText</a>";
                }
            }
            return implode(' ', $result);
        });

        //Join转换为html
        $this->convertKey('Join', 'html', function ($value, $key) {
            if ($value != 0) {
                $val = get_table_field($value, $key['opt']['mate'], $key['opt']['return'], $key['opt']['model']);
                if (!$key['opt']['url']) {
                    return $val;
                } else {
                    $urld = url($key['opt']['url'], array($key['opt']['return'] => $value));
                    return "<a href=\"$urld\">$val</a>";
                }
            } else {
                return '-';
            }
        });

        //status转换为html
        $setStatusUrl = $this->_setStatusUrl;
        $that = &$this;
        $this->convertKey('status', 'html', function ($value, $key, $item) use ($setStatusUrl, $that) {
            //如果没有设置修改状态的URL，则直接返回文字
            $map = $key['opt'];
            $text = $map[$value];
            if (!$setStatusUrl) {
                return $text;
            }

            //返回带链接的文字
            $switchStatus = $value == 1 ? 0 : 1;
            $url = $that->addUrlParam($setStatusUrl, array('status' => $switchStatus, 'ids' => $item['id']));
            return "<a href=\"{$url}\" class=\"ajax-get\">$text</a>";
        });

        //如果html为空
        $this->convertKey('html', 'html', function ($value) {
            if ($value === '') {
                return '<span style="color:#bbb;">' . lang('_EMPTY_BRACED_') . '</span>';
            }
            return $value;
        });


        //编译buttonList中的属性
        foreach ($this->_buttonList as &$button) {
            $button['tag'] = isset($button['attr']['href']) ? 'a' : 'button';
            $this->addDefaultCssClass($button);
            $button['attr'] = $this->compileHtmlAttr($button['attr']);
        }

        $this->setTitle($this->_title);
        //显示页面
        $this->assign('title', $this->_title);
        $this->assign('suggest', $this->_suggest);
        $this->assign('keyList', $this->_keyList);
        $this->assign('buttonList', $this->_buttonList);
        $this->assign('page',$this->_page);
        $this->assign('explain', $this->_explain);
        $this->assign('list', $this->_data);
        /*加入搜索 陈一枭*/
        $this->assign('searches', $this->_search);
        $this->assign('searchPostUrl', $this->_searchPostUrl);

        /*加入筛选select 郑钟良*/
        $this->assign('selects', $this->_select);
        $this->assign('selectPostUrl', $this->_selectPostUrl);
        //如果是选择返回数据的列表页就调用admin_solist模板文件，否则编译原有模板
        
        if ($solist) {
            parent::display('admin_solist');
        } else {
            parent::display('admin_list');
        }
    }

    public function doSetStatus($model, $ids, $status = 1)
    {
        $id = array_unique((array)$ids);
        $id = implode(',',$id);
        $rs = Db::name($model)->where(['id' => ['in', $id]])->update(['status' => $status]);
        if ($rs) {
            $this->success(lang('_SUCCESS_SETTING_'), $_SERVER['HTTP_REFERER']); 
        }else{
            $this->error(lang('_ERROR_SETTING_') . lang('_PERIOD_'));
        }
        
    }


    private function convertKey($from, $to, $convertFunction)
    {
        foreach ($this->_keyList as &$key) {
            if ($key['type'] == $from) {
                $key['type'] = $to;
                foreach ($this->_data as &$data) {
                if(is_array($data)){
                    $value = &$data[$key['name']];
                    $value = $convertFunction($value, $key, $data);
                    unset($value);
                }  
                }
                unset($data);
            }
        }
        unset($key);
    }

    private function addDefaultCssClass(&$button)
    {
        if (!isset($button['attr']['class'])) {
            $button['attr']['class'] = 'btn';
        } else {
            $button['attr']['class'] .= ' btn';
        }
    }

    public function addUrlParam($url, $params)
    {
        if (strpos($url, '?') === false) {
            $seperator = '?';
        } else {
            $seperator = '&';
        }
        if(is_array($params)){
            $params = http_build_query($params);
            return $url . $seperator . $params;
        }
        return $url;
        
    }

    /**自动处理清空回收站
     * @param string $model 要清空的模型
     */
    public function clearTrash($model = '')
    {
        if (request()->isPost()) {
            if ($model != '') {
                $aIds = input('post.ids', array());
                if (!empty($aIds)) {
                    $map['id'] = array('in', $aIds);
                } else {
                    $map['status'] = -1;
                }

                $result = Db::name($model)->where($map)->delete();
                if ($result) {
                    $this->success(lang('_SUCCESS_TRASH_CLEARED_', array('result' => $result)));
                }
                $this->error(lang('_TRASH_ALREADY_EMPTY_'));
            } else {
                $this->error(lang('_TRASH_SELECT_'));
            }
        }
    }

    /**
     * 执行彻底删除数据，只适用于无关联的数据表
     * @param $model
     * @param $ids
     * @author 大蒙<59262424@qq.com>
     */
    public function doDeleteTrue($model, $ids)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        Db::name($model)->where(['id' => ['in', $ids]])->delete();
        $this->success(lang('_SUCCESS_DELETE_COMPLETELY_'), $_SERVER['HTTP_REFERER']);
    }

    /**
     * @param $pattern Url函数解析的URL字符串，例如 Admin/Test/index?test_id=###
     * Admin/Test/index?test_id={other_id}
     * ###将被id替换
     * {other_id}将被替换
     * @return callable
     */
    private function createDefaultGetUrlFunction($pattern, $flag='id')
    {
        $explode = explode('|', $pattern);
        $pattern = $explode[0];
        $fun = empty($explode[1]) ? 'url' : $explode[1];

        return function ($item) use ($pattern, $fun, $flag) {
            $pattern = str_replace('###', $item[$flag], $pattern);
            return $fun($pattern);
        };
    }
}