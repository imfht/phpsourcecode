<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace app\common\widget;

use app\common\widget\form\ButtonDropdownForm;
use app\common\widget\form\ButtonForm;
use app\common\widget\form\ButtonGroupForm;
use app\common\widget\form\CaptchaForm;
use app\common\widget\form\CheckBoxForm;
use app\common\widget\form\ColorForm;
use app\common\widget\form\DateForm;
use app\common\widget\form\DateRangeForm;
use app\common\widget\form\DateTimeForm;
use app\common\widget\form\FileForm;
use app\common\widget\form\FilesForm;
use app\common\widget\form\GroupForm;
use app\common\widget\form\IconForm;
use app\common\widget\form\ImageForm;
use app\common\widget\form\ImagesForm;
use app\common\widget\form\JcropForm;
use app\common\widget\form\LinkageForm;
use app\common\widget\form\MaskForm;
use app\common\widget\form\RadioForm;
use app\common\widget\form\RangeForm;
use app\common\widget\form\SelectForm;
use app\common\widget\form\SelectsForm;
use app\common\widget\form\SwitchForm;
use app\common\widget\form\TableForm;
use app\common\widget\form\TagForm;
use app\common\widget\form\TextAreaForm;
use app\common\widget\form\TextForm;
use app\common\widget\form\TimeForm;
use app\common\widget\form\UeditorForm;
use think\facade\Env;

/**
 * 构造器
 * @Author: rainfer <rainfer520@qq.com>
 */
class Widget
{

    /**
     * 映射
     *
     * @var array
     */
    protected $mapping = [
        'button'         => ButtonForm::class,
        'buttondropdown' => ButtonDropdownForm::class,
        'buttongroup'    => ButtonGroupForm::class,
        'captcha'        => CaptchaForm::class,
        'checkbox'       => CheckBoxForm::class,
        'color'          => ColorForm::class,
        'date'           => DateForm::class,
        'daterange'      => DateRangeForm::class,
        'datetime'       => DateTimeForm::class,
        'file'           => FileForm::class,
        'files'          => FilesForm::class,
        'group'          => GroupForm::class,
        'icon'           => IconForm::class,
        'image'          => ImageForm::class,
        'images'         => ImagesForm::class,
        'jcrop'          => JcropForm::class,
        'linkage'        => LinkageForm::class,
        'mask'           => MaskForm::class,
        'radio'          => RadioForm::class,
        'range'          => RangeForm::class,
        'select'         => SelectForm::class,
        'selects'        => SelectsForm::class,
        'switch'         => SwitchForm::class,
        'table'          => TableForm::class,
        'tag'            => TagForm::class,
        'text'           => TextForm::class,
        'textarea'       => TextAreaForm::class,
        'time'           => TimeForm::class,
        'ueditor'        => UeditorForm::class
    ];
    //html代码
    protected $html = '';
    //待加载的js列表
    protected $js_list = [];
    //待加载的css列表
    protected $css_list = [];
    protected $data     = [];
    //s css等静态资源路径
    protected $staticPath = '';
    //模板触发器
    protected $trigger = [];
    //文件上传路径
    protected $file_upload_url = '';
    //图片上传路径
    protected $img_upload_url = '';
    //模板
    protected $template = '';
    //按钮
    protected $form_buttons   = [];
    protected $vars           = [];
    protected $topbutton_html = '';
    protected $topsearch_html = '';

    public function __construct()
    {
        $this->html     = '';
        $this->js_list  = [];
        $this->css_list = [];
        if (!defined('__ROOT__')) {
            define('__ROOT__', request()->rootUrl());
        }
        $this->staticPath      = __ROOT__ . '/public';
        $this->data            = [];
        $this->trigger         = [];
        $this->file_upload_url = url('admin/Ueditor/upload', ['action' => 'uploadfile']);
        $this->img_upload_url  = url('admin/Ueditor/upload', ['action' => 'uploadimage']);
        $this->template        = Env::get('app_path') . 'common/widget/form/layout_content.html';
        $this->form_buttons    = [
            ['确定', ['class' => 'btn btn-info', 'type' => 'submit', 'icon_l' => 'ace-icon fa fa-check bigger-110']],
            ['重置', ['class' => 'btn', 'type' => 'reset', 'icon_l' => 'ace-icon fa fa-undo bigger-110']]
        ];
    }

    /**
     * 渲染表单,返回单个表单html
     *
     * @return string
     */
    public function form()
    {
        $args = func_get_args();
        return call_user_func_array([$this, 'getHtml'], $args);
    }

    /**
     * 渲染表单,返回对象
     *
     * @param string $type
     *
     * @return \app\common\widget\Widget
     */
    public function addItem($type)
    {
        if ($type) {
            $args = func_get_args();
            //去除$type参数
            array_shift($args);
            $method = 'add' . ucfirst($type);
            call_user_func_array([$this, $method], $args);
        }
        return $this;
    }

    /**
     * 渲染表单,返回对象
     *
     * @param array $items
     *
     * @return \app\common\widget\Widget
     */
    public function addItems($items = [])
    {
        if (is_array($items) && $items) {
            foreach ($items as $item) {
                $type   = array_shift($item);
                $method = 'add' . ucfirst($type);
                call_user_func_array([$this, $method], $item);
            }
        }
        return $this;
    }

    /**
     * 按钮
     *
     * @param string $title 标题
     * @param array  $attr  属性，
     * @param string $id
     * @param string $type  ''或'a'
     *
     * @return \app\common\widget\Widget
     */
    public function addButton($title = '', $attr = [], $id = '', $type = '')
    {
        $this->html .= $this->getHtml('button', $title, $attr, $id, $type);
        return $this;
    }

    /**
     * 下拉按钮组
     *
     * @param array  $button    顶部按钮,元素参数见button,须为关联数组
     * @param array  $groups    每个元素参数见button,须为关联数组
     * @param bool   $has_drbtn 是否单独下拉按钮
     * @param string $class     dropup 上拉显示 ''下拉
     * @param string $dr_class  下拉class dropdown-menu-right
     *                          //颜色 dropdown-default dropdown-danger ...
     *                          //下拉菜单方向 dropdown-menu-right
     *
     * @return \app\common\widget\Widget
     */
    public function addButtondropdown($button = [], $groups = [], $has_drbtn = false, $class = '', $dr_class = '')
    {
        $this->html .= $this->getHtml('buttondropdown', $button, $groups, $has_drbtn, $class, $dr_class);
        return $this;
    }

    /**
     * 按钮组
     *
     * @param array  $groups 每个元素参数见button,须为关联数组
     * @param string $class  btn-corner2端按钮圆角 btn-group-vertical 垂直按钮组
     *
     * @return \app\common\widget\Widget
     */
    public function addButtongroup($groups = [], $class = '')
    {
        $this->html .= $this->getHtml('buttongroup', $groups, $class);
        return $this;
    }

    /**
     * 添加验证码
     *
     * @param string $name        验证码名
     * @param string $id          验证码标识id
     * @param string $title       标题
     * @param array  $attr        属性
     * @param string $extra_attr_input  input额外属性
     * @param string $extra_class_input input额外css类名
     * @param string $extra_css_input   input额外style
     * @param string $extra_attr_img  img额外属性
     * @param string $extra_class_img img额外css类名
     * @param string $extra_css_img   img额外style
     * @return \app\common\widget\Widget
     */
    public function addCaptcha($name, $id = '', $title = '', $attr = [], $extra_attr_input = '', $extra_class_input = 'col-xs-10 col-sm-5', $extra_css_input = '', $extra_attr_img = '', $extra_class_img = 'col-xs-10 col-sm-3', $extra_css_img = 'cursor: pointer;border: 1px solid #d5d5d5;height:34px;margin-left:10px;')
    {
        $this->html .= $this->getHtml('captcha', $name, $id, $title, $attr, $extra_attr_input, $extra_class_input, $extra_css_input, $extra_attr_img, $extra_class_img, $extra_css_img);
        return $this;
    }

    /**
     * 添加复选框
     *
     * @param string $name        复选框名
     * @param string $title       复选框标题
     * @param array  $options     复选框数据
     * @param string $default     默认值
     * @param array  $disabled    复选禁止
     * @param array  $attr        属性，
     *                            size-尺寸(sm,lg)，默认sm
     *                            style-样式(1,2)，默认1
     * @param string $extra_class 额外css类名
     * @param string $extra_attr  额外属性
     *
     * @return \app\common\widget\Widget
     */
    public function addCheckbox($name, $title, $options = [], $default = '', $disabled = [], $attr = [], $extra_class = '', $extra_attr = '')
    {
        $this->html .= $this->getHtml('checkbox', $name, $title, $options, $default, $disabled, $attr, $extra_class, $extra_attr);
        return $this;
    }

    /**
     * 颜色选择
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param string $format      格式
     * @param array  $attr        属性
     * @param string $extra_class 额外css类
     * @param string $extra_attr  额外属性
     *
     * @return \app\common\widget\Widget
     */
    public function addColor($name, $title, $default = '', $help_text = '', $format = 'hex', $attr = [], $extra_class = 'col-xs-10 col-sm-5', $extra_attr = '')
    {
        $this->html .= $this->getHtml('color', $name, $title, $default, $help_text, $format, $attr, $extra_class, $extra_attr);
        return $this;
    }

    /**
     * 日期
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $format      格式
     * @param string $help_text   帮助文本
     * @param array  $attr        属性
     * @param string $extra_class 额外css类
     * @param string $extra_attr  额外属性
     *
     * @return \app\common\widget\Widget
     */
    public function addDate($name, $title, $default = '', $help_text = '', $format = 'yyyy-mm-dd', $attr = [], $extra_class = 'col-xs-10 col-sm-5', $extra_attr = '')
    {
        $this->html .= $this->getHtml('date', $name, $title, $default, $help_text, $format, $attr, $extra_class, $extra_attr);
        return $this;
    }

    /**
     * 日期区间
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param array  $attr        属性
     * @param string $extra_class 额外css类
     * @param string $extra_attr  额外属性
     *
     * @return \app\common\widget\Widget
     */
    public function addDaterange($name, $title, $default = '', $help_text = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5', $extra_attr = '')
    {
        $this->html .= $this->getHtml('daterange', $name, $title, $default, $help_text, $attr, $extra_class, $extra_attr);
        return $this;
    }

    /**
     * 日期时间
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param string $format      格式
     * @param array  $attr        属性
     * @param string $extra_class 额外css类
     * @param string $extra_attr  额外属性
     *
     * @return \app\common\widget\Widget
     */
    public function addDatetime($name, $title, $default = '', $help_text = '', $format = 'yyyy-mm-dd hh:ii:ss', $attr = [], $extra_class = 'col-xs-10 col-sm-5', $extra_attr = '')
    {
        $this->html .= $this->getHtml('datetime', $name, $title, $default, $help_text, $format, $attr, $extra_class, $extra_attr);
        return $this;
    }

    /**
     * 单文件上传
     *
     * @param string $name
     * @param string $title 标题
     * @param string $default
     * @param string $help_text
     * @param array  $attr  属性，
     * @param string $extra_class
     *
     * @return \app\common\widget\Widget
     */
    public function addFile($name, $title = '', $default = '', $help_text = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5')
    {
        $this->html .= $this->getHtml('file', $name, $title, $default, $help_text, $attr, $extra_class);
        return $this;
    }

    /**
     * 多文件上传
     *
     * @param string $name
     * @param string $title 标题
     * @param string $default
     * @param string $help_text
     * @param array  $attr  属性，
     * @param string $extra_class
     *
     * @return \app\common\widget\Widget
     */
    public function addFiles($name, $title = '', $default = '', $help_text = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5')
    {
        $this->html .= $this->getHtml('files', $name, $title, $default, $help_text, $attr, $extra_class);
        return $this;
    }

    /**
     * TabGroup
     *
     * @param array   $groups           分组数据
     *                                  结构 = [
     *                                  [
     *                                  'title'=>'',
     *                                  'href'=>'',
     *                                  'items'=>[
     *                                  //[type,...] 第1个参数是类型,其它按照其参数顺序提供
     *                                  ],
     *                                  'is_active'=>false,
     *                                  'form_url'=>'',
     *                                  'form_class'=>'',
     *                                  'form_name'=>'',
     *                                  'attr_left'=>'',
     *                                  'attr_right'=>'',
     *                                  'dropdown'=>[
     *                                  [
     *                                  'title'=>'',
     *                                  'href'=>'',
     *                                  'is_active'=>false,
     *                                  'items'=>[
     *                                  //[type,...] 第1个参数是类型,其它按照其参数顺序提供
     *                                  'html'=>''
     *                                  ],
     *                                  []...
     *                                  ],
     *                                  'html'=>''
     *                                  ],[]...
     *                                  ]
     * @param string  $id               tabs的id
     * @param string  $position         tab的位置 //tabs-below tabs-left tabs-right
     * @param string  $color            tab的颜色  ''或blue
     * @param int     $tab_space        tab的间距 0-4
     * @param int     $tab_padding      tab的左间距 0 2 4 ...32
     * @param int     $content_padding  内容的内间距 0 2 4 ...32
     * @param boolean $content_noborder 内容的边框
     *
     * @return \app\common\widget\Widget
     */
    public function addGroup($groups = [], $id = '', $position = '', $color = '', $tab_space = 0, $tab_padding = 0, $content_padding = 0, $content_noborder = false)
    {
        //处理groups
        $has_active = false;
        foreach ($groups as $k => $v) {
            if (isset($v['dropdown']) && $v['dropdown']) {
                $has_active_dropdown = false;
                foreach ($v['dropdown'] as $kk => $dropdown) {
                    $groups[$k]['dropdown'][$kk]['html'] = isset($dropdown['html']) ? $dropdown['html'] : '';
                    $items                               = isset($dropdown['items']) ? $dropdown['items'] : [];
                    if ($items) {
                        foreach ($items as $item) {
                            $groups[$k]['dropdown'][$kk]['html'] .= call_user_func_array([$this, 'form'], $item);
                        }
                    }
                    if (isset($dropdown['is_active']) && $dropdown['is_active']) {
                        $has_active_dropdown = true;
                    }
                }
                if ($has_active_dropdown == false && is_array($v['dropdown'])) {
                    $groups[$k]['dropdown'][0]['is_active'] = true;
                }
            } else {
                $items              = isset($v['items']) ? $v['items'] : [];
                $groups[$k]['html'] = isset($v['html']) ? $v['html'] : '';
                if ($items) {
                    foreach ($items as $vv) {
                        $groups[$k]['html'] .= call_user_func_array([$this, 'form'], $vv);
                    }
                }
            }
            if (isset($v['is_active']) && $v['is_active']) {
                $has_active = true;
            }
        }
        if ($has_active == false && $groups && is_array($groups)) {
            $groups[0]['is_active'] = true;
        }
        $this->html .= $this->getHtml('group', $groups, $id, $position, $color, $tab_space, $tab_padding, $content_padding, $content_noborder);
        return $this;
    }

    /**
     * 图标选择
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param array  $attr        属性，
     * @param string $extra_class 额外css类名
     * @param string $extra_attr  额外属性
     *
     * @return \app\common\widget\Widget
     */
    public function addIcon($name, $title, $default = '0', $help_text = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5', $extra_attr = '')
    {
        $this->html .= $this->getHtml('icon', $name, $title, $default, $help_text, $attr, $extra_class, $extra_attr);
        return $this;
    }

    /**
     * 单图上传
     *
     * @param string $name
     * @param string $title 标题
     * @param string $default
     * @param string $help_text
     * @param array  $attr  属性，
     * @param string $extra_class
     *
     * @return \app\common\widget\Widget
     */
    public function addImage($name, $title = '', $default = '', $help_text = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5')
    {
        $this->html .= $this->getHtml('image', $name, $title, $default, $help_text, $attr, $extra_class);
        return $this;
    }

    /**
     * 多图上传
     *
     * @param string $name
     * @param string $title 标题
     * @param string $default
     * @param string $help_text
     * @param array  $attr  属性，
     * @param string $extra_class
     *
     * @return \app\common\widget\Widget
     */
    public function addImages($name, $title = '', $default = '', $help_text = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5')
    {
        $this->html .= $this->getHtml('images', $name, $title, $default, $help_text, $attr, $extra_class);
        return $this;
    }

    /**
     * 头像剪裁
     *
     * @param string $name
     * @param string $title 标题
     * @param string $default
     * @param array  $attr  属性，
     * @param string $extra_class
     *
     * @return \app\common\widget\Widget
     */
    public function addJcrop($name, $title = '', $default = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5')
    {
        $this->html .= $this->getHtml('jcrop', $name, $title, $default, $attr, $extra_class);
        $this->data['jcrop'] = [
            'upload_path' => (isset($attr['upload_path']) && $attr['upload_path']) ? $attr['upload_path'] : '/data/upload/avatar',
            'upload_url'  => (isset($attr['upload_url']) && $attr['upload_url']) ? $attr['upload_url'] : '',
            'id'          => (isset($attr['id']) && $attr['id']) ? $attr['id'] : ('jcrop_' . $name),
            'title'       => $title ?: '图片剪裁'
        ];
        return $this;
    }

    /**
     * 多级联动
     *
     * @param string $title 标签
     * @param array  $data  //每个元素结构['name'=>'',title'=>'','data'=>data数组,'url'=>通过url获取新data,'id'=>'','value'=>'']
     * @param array  $attr
     *
     * @return \app\common\widget\Widget
     */
    public function addLinkage($title = '', $data = [], $attr = [])
    {
        $this->html .= $this->getHtml('linkage', $title, $data, $attr);
        return $this;
    }

    /**
     * 添加格式文本
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param string $format      格式
     * @param array  $attr        属性
     * @param string $extra_class 额外css类
     * @param string $extra_attr  额外属性
     *
     * @return \app\common\widget\Widget
     */
    public function addMask($name, $title, $default = '', $help_text = '', $format = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5', $extra_attr = '')
    {
        $this->html .= $this->getHtml('mask', $name, $title, $default, $help_text, $format, $attr, $extra_class, $extra_attr);
        return $this;
    }

    /**
     * 添加单选
     *
     * @param string $name        单选名
     * @param string $title       单选标题
     * @param array  $options     单选数据
     * @param string $default     默认值
     * @param array  $disabled    单选禁止
     * @param array  $attr        属性，
     *                            size-尺寸(sm,lg)，默认sm
     *                            label_class(col-sm-x) 标签栅格样式,默认col-sm-3
     *                            div_class(col-sm-x) input上层栅格样式,默认col-sm-9
     * @param string $extra_class 额外css类名
     * @param string $extra_attr  额外属性
     *
     * @return \app\common\widget\Widget
     */
    public function addRadio($name, $title, $options = [], $default = '', $disabled = [], $attr = [], $extra_class = '', $extra_attr = '')
    {
        $this->html .= $this->getHtml('radio', $name, $title, $options, $default, $disabled, $attr, $extra_class, $extra_attr);
        return $this;
    }

    /**
     * range
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param array  $attr        属性，//具体查看https://github.com/IonDen/ion.rangeSlider
     * @param string $extra_class 额外css类名
     * @param string $extra_attr  额外属性
     *
     * @return \app\common\widget\Widget
     */
    public function addRange($name, $title, $default = '0', $help_text = '', $attr = [], $extra_class = '', $extra_attr = '')
    {
        $this->html .= $this->getHtml('range', $name, $title, $default, $help_text, $attr, $extra_class, $extra_attr);
        return $this;
    }

    /**
     * 添加普通下拉菜单
     *
     * @param string $name        下拉菜单名
     * @param string $title       标题
     * @param array  $options     选项 ['value'=>'name'] 或['value','value1'..]或'a,b,c,d'
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param string $extra_attr  额外属性
     * @param array  $attr        属性
     * @param string $extra_class 额外css类名
     *
     * @return \app\common\widget\Widget
     */
    public function addSelect($name, $title, $options = [], $default = '', $help_text = '', $extra_attr = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5')
    {
        if (is_string($options)) {
            $options = explode(',', $options);
        }
        $this->html .= $this->getHtml('select', $name, $title, $options, $default, $help_text, $extra_attr, $attr, $extra_class);
        return $this;
    }

    /**
     * 添加下拉菜单(可多选)
     *
     * @param string $name        下拉菜单名
     * @param string $title       标题
     * @param array  $options     选项(普通情况使用) ['value'=>'name','divider',...],'divider'表示为分隔线
     *                            $optgroups、$options选择1个，普通选择$options，复杂含分组选$optgroups
     * @param string $default     默认值 多个值以,隔开
     * @param string $help_text   帮助文本
     * @param string $extra_attr  额外属性
     * @param array  $optgroups   选项(复杂含分组时使用)
     *                            optgroups格式
     *                            [
     *                            [
     *                            'label'=>'',
     *                            'options'=>[
     *                            [
     *                            'divider'=>false,
     *                            'title'=>'',
     *                            'value'=>'',
     *                            'name'=>'',
     *                            'class'=>'',
     *                            'style'=>'',
     *                            'icon'=>'',
     *                            'subtext'=>'',
     *                            'disabled'=>false
     *                            ] ,
     *                            []
     *                            ],
     *                            'max'=>0,
     *                            'disabled'=>false
     *                            ],
     *                            []
     *                            ];
     * @param array  $attr        属性
     * @param string $extra_class 额外css类名
     *
     * @return \app\common\widget\Widget
     */
    public function addSelects($name, $title, $options = [], $default = '', $help_text = '', $extra_attr = '', $optgroups = [], $attr = [], $extra_class = '')
    {
        $this->html .= $this->getHtml('selects', $name, $title, $options, $default, $help_text, $extra_attr, $optgroups, $attr, $extra_class);
        return $this;
    }

    /**
     * 添加开关
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param array  $attr        属性，
     *                            style-形状(1,2,3,4,5,6,7)，默认4
     *                            text(['on','off']),默认为[]
     *                            btn('rotate','empty','flat'),默认'flat' 按钮样式
     *                            disabled 默认false
     * @param string $extra_class 额外css类名
     *
     * @return \app\common\widget\Widget
     */
    public function addSwitch($name, $title, $default = '0', $extra_attr = '', $attr = [], $extra_class = '')
    {
        $this->html .= $this->getHtml('switch', $name, $title, $default, $extra_attr, $attr, $extra_class);
        return $this;
    }

    /**
     * table
     *
     * @param array          $fields        字段
     * @param string         $pk            主键
     * @param array          $datas         数据
     * @param array          $right_actions 右侧操作
     * @param string         $page          分页
     * @param boolean|string $order         排序
     * @param boolean|string $delall        有全删
     * @param boolean        $ajax          是否ajax
     *
     * @return \app\common\widget\Widget
     */
    public function addTable($fields, $pk = 'id', $datas = [], $right_actions = [], $page = '', $order = false, $delall = false, $ajax = false)
    {
        $this->html .= $this->getHtml('table', $fields, $pk, $datas, $right_actions, $page, $order, $delall, $ajax);
        return $this;
    }

    /**
     * tag输入
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param array  $data        标签数据组
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param array  $attr        属性
     * @param string $extra_class 额外css类
     * @param string $extra_attr  额外属性
     *
     * @return \app\common\widget\Widget
     */
    public function addTag($name, $title, $data = [], $default = '', $help_text = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5', $extra_attr = '')
    {
        $this->html .= $this->getHtml('tag', $name, $title, $data, $default, $help_text, $attr, $extra_class, $extra_attr);
        return $this;
    }

    /**
     * 文本
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param string $extra_attr  额外属性
     * @param string $type        默认值
     * @param array  $attr        属性本
     * @param string $extra_class 额外css类
     * @param string $extra_css   额外style
     *
     * @return \app\common\widget\Widget
     */
    public function addText($name, $title, $default = '', $help_text = '', $extra_attr = '', $type = 'text', $attr = [], $extra_class = 'col-xs-10 col-sm-5', $extra_css = '')
    {
        $this->html .= $this->getHtml('text', $name, $title, $default, $help_text, $extra_attr, $type, $attr, $extra_class, $extra_css);
        return $this;
    }

    /**
     * 添加多行文本框
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param string $extra_attr  额外属性
     * @param array  $attr        属性
     * @param string $extra_class 额外css类名
     *
     * @return \app\common\widget\Widget
     */
    public function addTextarea($name, $title, $default = '', $help_text = '', $extra_attr = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5')
    {
        $this->html .= $this->getHtml('textarea', $name, $title, $default, $help_text, $extra_attr, $attr, $extra_class);
        return $this;
    }

    /**
     * 时间
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param array  $attr        属性
     * @param string $extra_class 额外css类
     * @param string $extra_attr  额外属性
     *
     * @return \app\common\widget\Widget
     */
    public function addTime($name, $title, $default = '', $help_text = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5', $extra_attr = '')
    {
        $this->html .= $this->getHtml('time', $name, $title, $default, $help_text, $attr, $extra_class, $extra_attr);
        return $this;
    }

    /**
     * addToparea
     *
     * @param array $default ['add'=>[$href,$is_pop],'delete','disable','enable'] //顶部默认按钮
     * @param array $custom  每个元素参数见button,须为关联数组 //顶部自定义按钮
     * @param array $items   //顶部搜索表单项
     * @param array $form    ['href'=>'','class'=>'','id'=>''] //顶部搜索form属性
     *
     * @return \app\common\widget\Widget
     */
    public function addToparea($default = [], $custom = [], $items = [], $form = [])
    {
        $html = '';
        if ($default || $custom) {
            $html .= $this->getHtml('topbutton', $default, $custom);
        }
        if ($items) {
            $html .= $this->getHtml('topsearch', $items, $form);
        }
        if ($html) {
            $html = '<div class="row maintop"><div class="col-xs-12 ">' . $html . '</div></div>';
        }
        $this->html .= $html;
        return $this;
    }

    /**
     * 百度编辑器
     *
     * @param string $name
     * @param string $title 标题
     * @param string $default
     * @param string $help_text
     * @param array  $attr  属性，
     * @param string $extra_class
     *
     * @return \app\common\widget\Widget
     */
    public function addUeditor($name, $title = '', $default = '', $help_text = '', $attr = [], $extra_class = 'col-xs-12')
    {
        $this->html .= $this->getHtml('ueditor', $name, $title, $default, $help_text, $attr, $extra_class);
        return $this;
    }

    /**
     * 设置form页page_header
     *
     * @param array $header ['','',0] 表单页->表单操作 返回上页
     *
     * @return \app\common\widget\Widget
     */
    public function setHeader($header = [])
    {
        $this->data['page_header'] = $header;
        return $this;
    }

    /**
     * 设置form页alert框
     *
     * @param array $alert ['','']
     *
     * @return \app\common\widget\Widget
     */
    public function setAlert($alert = [])
    {
        $this->data['form_alert'] = $alert;
        return $this;
    }

    /**
     * 设置模板
     *
     * @param string $template
     *
     * @return \app\common\widget\Widget
     */
    public function setTemplate($template = '')
    {
        $this->template = $template ?: $this->template;
        return $this;
    }

    /**
     * 设置form的提交url
     *
     * @param string $url
     *
     * @return \app\common\widget\Widget
     */
    public function setUrl($url = '')
    {
        $this->data['url'] = $url ? trim($url) : '';
        return $this;
    }

    /**
     * 设置form的提交button
     *
     * @param array $buttons
     *
     * @return \app\common\widget\Widget
     */
    public function setButton($buttons = [])
    {
        $this->form_buttons = $buttons;
        return $this;
    }

    /**
     * 设置form的提交方式
     *
     * @param string $ajax_class
     *                      "ajaxForm" 失败跳转，不检查表单
     *                      "ajaxForm-noJump" 失败不跳转 不检查表单
     *                      "ajaxForm-hasVerify" 失败不跳转 验证码表单
     *                      "ajaxForm-allDel" 多选删除 检查是否选择
     *                      "ajaxForm-checkForm" 失败跳转，检查表单
     *                      可以js检查 #chk_username #chk_tel,其他复杂的表单验证，请自行修改
     *
     * @return \app\common\widget\Widget
     */
    public function setAjax($ajax_class = 'ajaxForm')
    {
        $this->data['ajax_class'] = $ajax_class;
        return $this;
    }

    /**
     * 设置tab_home_title
     *
     * @param string $title
     *
     * @return \app\common\widget\Widget
     */
    public function setTabHomeTitle($title = '首页')
    {
        $this->data['tab_home_title'] = $title;
        return $this;
    }

    /**
     * 设置触发
     *
     * @param string $trigger 需要触发的表单项名
     * @param string $values  触发的值
     * @param string $show    触发后要显示的表单项名
     * @param bool   $clear   是否清除值
     *
     * @return \app\common\widget\Widget
     */
    public function setTrigger($trigger = '', $values = '', $show = '', $clear = true)
    {
        if (!empty($trigger)) {
            if (is_array($trigger)) {
                foreach ($trigger as $item) {
                    if (count($item) > 2) {
                        $this->trigger[] = [
                            'show'    => $item[2],
                            'values'  => $item[1],
                            'trigger' => (string)$item[0],
                            'clear'   => isset($item[3]) ? ($item[3] === true ? 1 : 0) : 1
                        ];
                    }
                }
            } else {
                $this->trigger[] = [
                    'show'    => $show,
                    'values'  => (string)$values,
                    'trigger' => $trigger,
                    'clear'   => $clear === true ? 1 : 0
                ];
            }
        }
        return $this;
    }
    /**
     * 设置额外JS代码
     * @param string $extra_js 额外JS代码
     *
     * @return $this
     */
    public function setExtraJs($extra_js = '')
    {
        if ($extra_js != '') {
            $this->data['extra_js'] = $extra_js;
        }
        return $this;
    }

    /**
     * 设置额外CSS代码
     * @param string $extra_css 额外CSS代码
     *
     * @return $this
     */
    public function setExtraCss($extra_css = '')
    {
        if ($extra_css != '') {
            $this->data['extra_css'] = $extra_css;
        }
        return $this;
    }
    /**
     * 添加额外js文件
     * @param string $js js文件路径
     *
     * @return $this
     */
    public function addJs($js)
    {
        if ($js != '' && !in_array($js, $this->js_list)) {
            $this->js_list[] = $js;
        }
        return $this;
    }
    /**
     * 添加额外css文件
     * @param string $css css文件路径
     *
     * @return $this
     */
    public function addCss($css)
    {
        if ($css != '' && !in_array($css, $this->css_list)) {
            $this->css_list[] = $css;
        }
        return $this;
    }
    /**
     * 返回生成的表单数组
     *
     * @return array
     */
    public function make()
    {
        return ['html' => $this->html, 'js_list' => $this->js_list, 'css_list' => $this->css_list, 'attr_data' => $this->data];
    }

    /**
     * 渲染表单
     *
     * @param string $template
     * @param array  $vars
     *
     * @return \think\response\View
     */
    public function fetch($template = '', $vars = [])
    {
        if ($template != '') {
            $this->template = $template;
        }
        //表单按钮
        $form_buttons_html = $this->getFormButtonHtml();
        $this->html .= $form_buttons_html;
        $trigger    = $this->trigger ?: [];
        $this->vars = ['html' => $this->html, 'js_list' => $this->js_list, 'css_list' => $this->css_list, 'file_upload_url' => $this->file_upload_url, 'img_upload_url' => $this->img_upload_url, 'triggers' => $trigger, 'attr_data' => $this->data];
        if (!empty($vars)) {
            $this->vars = array_merge($this->vars, $vars);
        }
        return view($this->template, $this->vars);
    }

    /**
     * 获取单个表单html
     *
     * @param string $name
     *
     * @return string
     */
    private function getHtml($name)
    {
        $method = 'fetch';
        $class  = isset($this->mapping[$name]) ? $this->mapping[$name] : '';
        if ($class && class_exists($class) && method_exists($class, $method)) {
            $this->attchJsCss($name);
            $args = func_get_args();
            array_shift($args);
            return call_user_func_array([new $class(), $method], $args);
        } elseif ($name == 'toparea') {
            $html = '';
            $args = func_get_args();
            //删除$name
            array_shift($args);
            if ($args) {
                $default = isset($args[0]) ? $args[0] : [];
                $custom  = isset($args[1]) ? $args[1] : [];
                $items   = isset($args[2]) ? $args[2] : [];
                $form    = isset($args[3]) ? $args[3] : [];
                if ($default || $custom) {
                    $html .= $this->getHtml('topbutton', $default, $custom);
                }
                if ($items) {
                    $html .= $this->getHtml('topsearch', $items, $form);
                }
                if ($html) {
                    $html = '<div class="row maintop"><div class="col-xs-12 ">' . $html . '</div></div>';
                }
            }
            return $html;
        } elseif ($name == 'topbutton') {
            $html = '';
            $args = func_get_args();
            //删除$name
            array_shift($args);
            if ($args) {
                $default = isset($args[0]) ? $args[0] : [];
                $custom  = isset($args[1]) ? $args[1] : [];
                $groups  = $this->getTopButtons($default, $custom);
                $html    = '<div class="pull-left">';
                $html .= $this->getHtml('buttongroup', $groups);
                $html .= '</div>';
            }
            return $html;
        } elseif ($name == 'topsearch') {
            $html = '';
            $args = func_get_args();
            //删除$name
            array_shift($args);
            if ($args) {
                $items = isset($args[0]) ? $args[0] : [];
                $form  = isset($args[1]) ? $args[1] : [];
                if ($items) {
                    $html .= '<div class="pull-right">';
                    $html .= '<form class="' . (isset($form['class']) ? $form['class'] : '') . '" id="' . (isset($form['id']) ? $form['id'] : '') . '" method="post" action="' . (isset($form['href']) ? $form['href'] : '') . '">';
                    foreach ($items as $item) {
                        $html .= call_user_func_array([$this, 'form'], $item);
                    }
                    //全部显示
                    $html .= '<a href="' . (isset($form['href']) ? $form['href'] : '') . '">';
                    $html .= '<button type="button" class="btn btn-sm  btn-purple ajax-display-all">';
                    $html .= '<span class="ace-icon fa fa-globe icon-on-right bigger-110"></span>显示全部</button></a>';
                    $html .= '</form>';
                    $html .= '</div>';
                }
            }
            return $html;
        }
        return '';
    }

    /**
     * 附加js css
     *
     * @param string $name
     */
    private function attchJsCss($name)
    {
        switch ($name) {
            case 'ueditor':
                if (!in_array($this->staticPath . '/ueditor/ueditor.config.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/ueditor/ueditor.config.js';
                }
                if (!in_array($this->staticPath . '/ueditor/ueditor.all.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/ueditor/ueditor.all.js';
                }
                break;
            case 'images':
                //上传js css
                if (!in_array($this->staticPath . '/webuploader/webuploader.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/webuploader/webuploader.js';
                }
                if (!in_array($this->staticPath . '/webuploader/webuploader.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/webuploader/webuploader.css';
                }
                //查看大图js css
                if (!in_array($this->staticPath . '/magnific-popup/magnific-popup.min.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/magnific-popup/magnific-popup.min.js';
                }
                if (!in_array($this->staticPath . '/magnific-popup/magnific-popup.min.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/magnific-popup/magnific-popup.min.css';
                }
                break;
            case 'image':
                //上传js css
                if (!in_array($this->staticPath . '/webuploader/webuploader.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/webuploader/webuploader.js';
                }
                if (!in_array($this->staticPath . '/webuploader/webuploader.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/webuploader/webuploader.css';
                }
                //查看大图js css
                if (!in_array($this->staticPath . '/magnific-popup/magnific-popup.min.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/magnific-popup/magnific-popup.min.js';
                }
                if (!in_array($this->staticPath . '/magnific-popup/magnific-popup.min.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/magnific-popup/magnific-popup.min.css';
                }
                break;
            case 'file':
            case 'files':
                //上传js css
                if (!in_array($this->staticPath . '/webuploader/webuploader.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/webuploader/webuploader.js';
                }
                if (!in_array($this->staticPath . '/webuploader/webuploader.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/webuploader/webuploader.css';
                }
                break;
            case 'range':
                if (!in_array($this->staticPath . '/rangeslider/ion.rangeSlider.min.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/rangeslider/ion.rangeSlider.min.js';
                }
                if (!in_array($this->staticPath . '/rangeslider/ion.rangeSlider.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/rangeslider/ion.rangeSlider.css';
                }
                if (!in_array($this->staticPath . '/rangeslider/normalize.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/rangeslider/normalize.css';
                }
                if (!in_array($this->staticPath . '/rangeslider/ion.rangeSlider.skinNice.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/rangeslider/ion.rangeSlider.skinNice.css';
                }
                break;
            case 'selects':
                if (!in_array($this->staticPath . '/bootstrap-select/js/bootstrap-select.min.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/bootstrap-select/js/bootstrap-select.min.js';
                }
                if (!in_array($this->staticPath . '/bootstrap-select/css/bootstrap-select.min.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/bootstrap-select/css/bootstrap-select.min.css';
                }
                break;
            case 'textarea':
                if (!in_array($this->staticPath . '/jquery-inputlimiter/jquery.inputlimiter.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/jquery-inputlimiter/jquery.inputlimiter.js';
                }
                if (!in_array($this->staticPath . '/autosize/dist/autosize.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/autosize/dist/autosize.js';
                }
                break;
            case 'mask':
                if (!in_array($this->staticPath . '/jquery.maskedinput/dist/jquery.maskedinput.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/jquery.maskedinput/dist/jquery.maskedinput.js';
                }
                break;
            case 'tag':
                if (!in_array($this->staticPath . '/_mod/bootstrap-tag/bootstrap-tag.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/_mod/bootstrap-tag/bootstrap-tag.js';
                }
                break;
            case 'color':
                if (!in_array($this->staticPath . '/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.js';
                }
                if (!in_array($this->staticPath . '/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.css';
                }
                break;
            case 'time':
                if (!in_array($this->staticPath . '/bootstrap-timepicker/js/bootstrap-timepicker.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/bootstrap-timepicker/js/bootstrap-timepicker.js';
                }
                if (!in_array($this->staticPath . '/bootstrap-timepicker/css/bootstrap-timepicker.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/bootstrap-timepicker/css/bootstrap-timepicker.css';
                }
                break;
            case 'datetime':
                if (!in_array($this->staticPath . '/datetimepicker/bootstrap-datetimepicker.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/datetimepicker/bootstrap-datetimepicker.js';
                }
                if (!in_array($this->staticPath . '/datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js';
                }
                if (!in_array($this->staticPath . '/datetimepicker/bootstrap-datetimepicker.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/datetimepicker/bootstrap-datetimepicker.css';
                }
                break;
            case 'daterange':
                if (!in_array($this->staticPath . '/sldate/moment.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/sldate/moment.js';
                }
                if (!in_array($this->staticPath . '/sldate/daterangepicker.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/sldate/daterangepicker.js';
                }
                if (!in_array($this->staticPath . '/sldate/daterangepicker-bs3.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/sldate/daterangepicker-bs3.css';
                }
                break;
            case 'date':
                if (!in_array($this->staticPath . '/datePicker/bootstrap-datepicker.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/datePicker/bootstrap-datepicker.js';
                }
                if (!in_array($this->staticPath . '/datePicker/bootstrap-datepicker.css', $this->css_list)) {
                    $this->css_list[] = $this->staticPath . '/datePicker/bootstrap-datepicker.css';
                }
                break;
            case 'icon':
                $this->data['has_icon'] = true;
                break;
            case 'jcrop':
                if (!in_array($this->staticPath . '/shearphoto/js/ShearPhoto.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/shearphoto/js/ShearPhoto.js';
                }
                if (!in_array($this->staticPath . '/shearphoto/js/alloyimage.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/shearphoto/js/alloyimage.js';
                }
                if (!in_array($this->staticPath . '/shearphoto/js/handle.js', $this->js_list)) {
                    $this->js_list[] = $this->staticPath . '/shearphoto/js/handle.js';
                }
                break;
        }
    }

    /*
     * 表单按钮html
     *
     * @retrun string
     */
    private function getFormButtonHtml()
    {
        $html = '';
        if ($this->form_buttons && is_array($this->form_buttons)) {
            $html .= '<div class="clearfix form-actions">';
            $html .= '<div class="col-xs-offset-3 col-xs-9">';
            foreach ($this->form_buttons as $button) {
                array_unshift($button, 'button');
                $html .= call_user_func_array([$this, 'getHtml'], $button);
            }
            $html .= '</div></div>';
        }
        return $html;
    }

    /*
     * 表单顶部按钮处理
     * @param array $default
     * @param array $custom
     * @retrun array
     */
    private function getTopButtons($default = [], $custom = [])
    {
        $groups = [];
        $button = [
            'title' => '',//标签
            'type'  => '',
            'attr'  => [
                'class'    => 'btn btn-primary',
                //'' 'submit' 'reset' 'back'
                'type'     => '',
                'icon_l'   => '',
                'icon_r'   => '',
                //是否只读
                'disabled' => false,
                //提示
                'tips'     => '',
                //按钮后面的标签badge ['title'=>'','class'=>'']
                'span'     => [],
                'href'     => '',
                'is_pop'   => false,
                'target'   => '_self',
                //附加属性
                'data'     => []
            ]
        ];
        //系统默认按钮
        if (is_array($default)) {
            foreach ($default as $key => $value) {
                $button_ = [];
                switch ($key) {
                    case 'add':
                        $button_ = ['title' => '新增', 'type' => (!isset($value['is_pop']) || !$value['is_pop']) ? 'a' : '', 'attr' => ['icon_l' => 'ace-icon fa fa-plus-circle', 'href' => (isset($value['href'])) ? $value['href'] : '', 'is_pop' => (isset($value['is_pop'])) ? $value['is_pop'] : false]];
                        break;
                    case 'delete':
                        $button_ = ['title' => '删除', 'attr' => ['class' => 'btn btn-danger', 'icon_l' => 'ace-icon fa fa-times-circle-o', 'href' => (isset($value['href'])) ? $value['href'] : '']];
                        break;
                    case 'disable':
                        $button_ = ['title' => '禁用', 'attr' => ['class' => 'btn btn-warning', 'icon_l' => 'ace-icon fa fa-ban', 'href' => (isset($value['href'])) ? $value['href'] : '']];
                        break;
                    case 'enable':
                        $button_ = ['title' => '启用', 'attr' => ['class' => 'btn btn-success', 'icon_l' => 'ace-icon fa fa-check-circle-o', 'href' => (isset($value['href'])) ? $value['href'] : '']];
                        break;
                }
                if ($button_) {
                    $button_['attr'] = array_merge($button['attr'], $button_['attr']);
                    $groups[]        = array_merge($button, $button_);
                }
            }
        }
        //自定义按钮
        if (is_array($custom) && $custom) {
            $groups = array_merge($groups, $custom);
        }
        return $groups;
    }
}
