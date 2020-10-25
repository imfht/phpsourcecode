<?php
namespace app\common\util;
use app\common\traits\AddEditList;
use app\common\controller\IndexBase;

class Form extends IndexBase{
    use AddEditList;
    protected static $instance;
    protected $info=[];   //内容信息
    
    /**
     * 创建表单
     * @param array $tab_list 表格参数
     * 比如 [['title','标题','text'],['mid','模型名称','select2','',[1=>'文章',2=>'图片']]],]
     * 第三项常用类型 比如 text select select2 text.edit
     * 第四项是默认值，第五项是参数，常用为数组
     * @param array $info 从数据库取出的内容数据，如果是新发布，可以为空
     */
    public static function make($tab_list=[],$info=[]){        
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        self::$instance->info = $info;
        self::$instance->form_items = $tab_list;
        return self::$instance;
    }
    
    
    /**
     * 添加多行文本框
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addTextarea($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['textarea' , $name , $title , $tips , $default , $extra_attr , $extra_class ];
        return self::$instance;
    }
    
    /**
     * 添加百度编辑器
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addUeditor($name = '', $title = '', $tips = '', $default = '', $extra_class = ''){
        self::$instance->form_items[] = ['ueditor' , $name , $title , $tips , $default , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加单行文本框
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param array $group 标签组，可以在文本框前后添加按钮或者文字
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addText($name = '', $title = '', $tips = '', $default = '', $group = [], $extra_attr = '', $extra_class = '',$extra_html=''){
        self::$instance->form_items[] = ['text' , $name , $title , $tips , $default , $group , $extra_attr , $extra_class ,$extra_html];
        return self::$instance;
    }
    
    /**
     * 添加标签
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addTags($name = '', $title = '', $tips = '', $default = '', $extra_class = ''){
        self::$instance->form_items[] = ['tags' , $name , $title , $tips , $default , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加开关
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param array $attr 属性，
     *      color-颜色(default/primary/info/success/warning/danger)，默认primary
     *      size-尺寸(sm,nm,lg)，默认sm
     *      shape-形状(rounded,square)，默认rounded
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addSwitch($name = '', $title = '', $tips = '', $default = '', $attr = [], $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['switch' , $name , $title , $tips , $default , $attr , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加只读字段
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_class 额外css类
     * @return mixed
     */
    public static function addStatic($name = '', $title = '', $tips = '', $default = '',  $extra_class = ''){
        self::$instance->form_items[] = ['static' , $name , $title , $tips , $default , $hidden , $extra_class];
        return self::$instance;
    }
    

    
    /**
     * 添加普通下拉菜单
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param array $options 选项
     * @param string $default 默认值
     * @param string $extra_attr 额外属性  可以设置为多选，属性为“multiple”多选的话，发送到服务器的则为数组形式。默认值可设置多个，值之间用逗号隔开。
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addSelect($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['select' , $name , $title , $tips , $options,  $default , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加范围
     * @param string $name 表单项名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param array $options 参数 数组格式，比如['double'=>true]
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addRange($name = '', $title = '', $tips = '', $default = '', $options = [], $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['range' , $name , $title , $tips , $default , $options , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加单选
     * @param string $name 表单字段变量名
     * @param string $title 单选标题
     * @param string $tips 提示
     * @param array $options 单选数据
     * @param string $default 默认值
     * @param array $attr 属性，
     *      color-颜色(default/primary/info/success/warning/danger)，默认primary
     *      size-尺寸(sm,nm,lg)，默认sm
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addRadio($name = '', $title = '', $tips = '', $options = [], $default = '', $attr = [], $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['radio' , $name , $title , $tips , $options , $default , $attr , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加密码框
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addPassword($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['password' , $name , $title , $tips , $default , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加数字输入框
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $min 最小值
     * @param string $max 最大值
     * @param string $step 步进值
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类
     * @return mixed
     */
    public static function addNumber($name = '', $title = '', $tips = '', $default = '', $min = '', $max = '', $step = '', $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['number',$name , $title , $tips , $default , $min , $max , $step , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加金额输入框
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $min 最小值
     * @param string $max 最大值
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类
     * @return mixed
     */
    public static function addMoney($name = '', $title = '', $tips = '', $default = '', $min = '', $max = '',  $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['money' , $name , $title , $tips , $default , $min , $max , $step=0.01 , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 图片裁剪
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param array $options 参数 数组
     * minSize	[ 8, 8 ]	选框最小尺寸，代表宽和高
     * maxSize	[ 0, 0 ]	选框最大尺寸， 代表宽和高
     * aspectRatio	0	选框宽高比，它的值为width/height，例如：1表示正方形
     * bgColor	null]	背景颜色。颜色关键字、HEX、RGB 均可。
     * bgOpacity	null	背景透明度，比如0.5
     * edge	[ 'n' => 0, 's' => 0, 'e' => 0, 'w' => 0 ]	选框距四边的距离，其中s和e要写负值
     * canDrag	true	选框是否可拖拽
     * canResize	true	选框是否可改变大小
     * canSelect	true	是否可以新建选框
     * setSelect	null	设置选框大小和位置
     * saveWidth	null	保存的图片宽度
     * saveHeight	null	保存的图片高度
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addCutImage($name = '', $title = '', $tips = '', $default = '', $options = [], $extra_class = ''){
        self::$instance->form_items[] = ['cutImage' , $name , $title , $tips , $default , $options , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加多图片上传
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $size 图片大小，单位为kb，0为不限制
     * @param string $ext 文件后缀
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addImages($name = '', $title = '', $tips = '', $default = '', $size = '', $ext = '', $extra_class = ''){
        self::$instance->form_items[] = ['images' , $name , $title , $tips , $default , $size , $ext , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加单图片上传
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $size 图片大小，单位为kb，0为不限制
     * @param string $ext 文件后缀
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addImage($name = '', $title = '', $tips = '', $default = '', $size = '', $ext = '', $extra_class = ''){
        self::$instance->form_items[] = ['image' , $name , $title , $tips , $default , $size , $ext , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加图标选择器
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addIcon($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['icon' ,$name , $title , $tips , $default , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加隐藏表单项
     * @param string $name 表单字段变量名
     * @param string $default 默认值
     * @return mixed
     */
    public static function addHidden($name = '', $default = ''){
        self::$instance->form_items[] = ['hidden' , $name , $default];
        return self::$instance;
    }
    
    /**
     * 添加多文件上传
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $size 图片大小，单位为kb
     * @param string $ext 文件后缀
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addFiles($name = '', $title = '', $tips = '', $default = '', $size = '', $ext = '', $extra_class = ''){
        self::$instance->form_items[] = ['files' , $name , $title , $tips , $default , $size , $ext , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加单文件上传
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $size 文件大小，单位为kb
     * @param string $ext 文件后缀
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addFile($name = '', $title = '', $tips = '', $default = '', $size = '', $ext = '', $extra_class = ''){
        self::$instance->form_items[] = ['file' , $name , $title , $tips , $default , $size , $ext , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加时间
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $format 日期时间格式
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addTime($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['time' , $name , $title , $tips , $default , $format , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加日期时间
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $format 日期时间格式
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addDatetime($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['datetime' , $name , $title , $tips , $default , $format , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加日期范围
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $format 格式
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addDates($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['dates' , $name , $title , $tips , $default , $format , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加日期
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $format 日期格式
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addDate($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['date' , $name , $title , $tips , $default , $format , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加取色器
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 提示
     * @param string $default 默认值
     * @param string $mode 模式：默认为rgba(含透明度)，也可以是rgb
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addColor($name = '', $title = '', $tips = '', $default = '', $mode = 'rgba', $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['color' , $name , $title , $tips , $default , $mode , $extra_attr , $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加百度地图
     * @param string $name 表单字段变量名
     * @param string $title 标题
     * @param string $tips 帮助提示语
     * @param string $default 默认坐标
     * @param string $address 默认地址
     * @param string $level 地图显示级别
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addMapBaidu($name = '', $title = '',  $tips = '', $default = '', $address = '', $level = '', $extra_class = ''){
        $ak = config('baidu_map_ak');
        self::$instance->form_items[] = ['bmap',$name, $title, $ak, $tips, $default , $address, $level, $extra_class];
        return self::$instance;
    }
    
    
    /**
     * 添加树状复选框
     * @param string $name 复选框名
     * @param string $title 复选框标题
     * @param string $tips 提示
     * @param array $options 复选框数据
     * @param string $default 默认值
     * @param array $attr 属性，
     *      color-颜色(default/primary/info/success/warning/danger)，默认primary
     *      size-尺寸(sm,nm,lg)，默认sm
     *      shape-形状(rounded,square)，默认rounded
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addCheckboxtree($name = '', $title = '', $tips = '', $options = [], $default = '', $attr = [], $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['checkboxtree',$name, $title, $tips, $options, $default, $attr, $extra_attr, $extra_class];
        return self::$instance;
    }
    
    /**
     * 添加复选框
     * @param string $name 复选框变量名
     * @param string $title 复选框标题
     * @param string $tips 提示
     * @param array $options 复选框数据 只能是数组
     * @param string $default 复选框默认选中值
     * @param array $attr 属性，
     *      color-颜色(default/primary/info/success/warning/danger)，默认primary
     *      size-尺寸(sm,nm,lg)，默认sm
     *      shape-形状(rounded,square)，默认rounded
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @return mixed
     */
    public static function addCheckbox($name = '', $title = '', $tips = '', $options = [], $default = '', $attr = [], $extra_attr = '', $extra_class = ''){
        self::$instance->form_items[] = ['checkbox', $name, $title, $tips, $options, $default, $attr, $extra_attr, $extra_class];
        return self::$instance;
    }
    
    /**
     * 隐藏按钮
     * @param array|string $btn 要隐藏的按钮，如：['submit']，其中'submit'->确认按钮，'back'->返回按钮
     * @return $this
     */
    public static function addButonHide($btn = []){
        self::$instance -> tab_ext['hidebtn'] = $btn;
        return self::$instance;
    }
    
    /**
     * 设置按钮标题，只有两个按钮可修改
     * @param string|array $btn 按钮名 'submit' -> “提交”，'back' -> “返回”
     * @param string $title 按钮标题
     * @return $this
     */
    public static function addButtonTitle($btn = '', $title = ''){
        //self::$instance -> tab_ext['hidebtn'] = $btn;
        return self::$instance;
    }
    
    /**
     * 添加底部更多的按钮
     * @param string $btn 按钮HTML代码
     * @return $this
     */
    public static function addButton($btn = ''){
        self::$instance -> tab_ext['addbtn'] = $btn;
        return self::$instance;
    }
    
    /**
     * 设置表单提交地址,不设置就提交到当前页面
     * @param string $post_url 提交地址
     * @return $this
     */
    public static function addUrl($post_url = ''){
        //self::$instance -> tab_ext['addbtn'] = $btn;
        return self::$instance;
    }
    
    /**
     * 设置JS触发事件
     * @param string $form_name 需要触发的表单字段名，目前支持select（单选类型）、text、radio三种
     * @param string $form_values 触发的值
     * @param string $show_form_name 触发后要显示的表单项名，目前不支持普通联动、范围、拖动排序、静态文本
     * @param bool $clear 是否清除值
     * @return $this
     */
    public static function addJs($form_name = '', $form_values = '', $show_form_name = '', $clear = true){
        self::$instance -> tab_ext['trigger'][] = [$form_name, $form_values, $show_form_name, $clear];
        return self::$instance;
    }
    
    /**
     * 设置分组导航列表
     * @param array $tab_list Tab列表  [1=>['title' => '标题A', 'href' => 'http://xxxx.com'],2=>['title' => '标题B', 'href' => 'http://xxxx.com']]
     * @param string $curr_id 当前分组ID
     * @return $this
     */
    public static function addNav($tab_list = [], $curr_id = ''){
        self::$instance -> tab_ext['nav'] = [$tab_list,$curr_id];
        return self::$instance;
    }
    

    
    /**
     * 设置页面提示
     * @param string $msg 提示信息
     * @param string $type 提示类型：success/info/warning/danger，默认info
     * @return $this
     */
    public static function addPageTips($msg = '', $type = 'info'){
        //self::$instance -> tab_ext['warn_msg'] = $msg;
        self::$instance -> tab_ext['help_msg'] = $msg;
        return self::$instance;
    }
    
    /**
     * 设置页面标题
     * @param string $title 页面标题
     * @return $this
     */
    public static function addPageTitle($title = ''){
        self::$instance -> tab_ext['page_title'] = $title;
        return self::$instance;
    }
    
    /**
     * 加载模板输出
     * @param string $template 模板文件名
     * @param array  $vars     模板输出变量
     * @param array  $replace  模板替换
     * @param array  $config   模板参数
     * @return mixed
     */
    public static function fetchs($template = '', $vars = [], $replace = [], $config = []){
        if (self::$instance ->info) {
            return self::$instance ->editContent(self::$instance ->info);
        }else{
            return self::$instance ->addContent();
        }
    }
}