<?php
// 公共Builder表单构建器控制器
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
// | 原作者：心云间、凝听
// +----------------------------------------------------------------------
namespace tpvue\admin\builder;



class AdminFormBuilder extends AdminBuilder
{
    private $_meta_title;            // 页面标题
    private $_tab_nav = array();     // 页面Tab导航
    private $_group_tab_nav=array(); //页面Tab导航
    private $_post_url;              // 表单提交地址
    private $_buttonList = array();    //按钮组
    private $_form_items = array();  // 表单项目
    private $_extra_items = array(); // 额外已经构造好的表单项目
    private $_form_data = array();   // 表单数据
    private $_extra_html;            // 额外功能代码
    private $_ajax_submit = true;    // 是否ajax提交

    /**
     * 设置页面标题
     * @param string $title 标题文本
     * @return $this    
     */
    public function setMetaTitle($meta_title) {
        $this->_meta_title = $meta_title;
        return $this;
    }

    /**
     * 设置Tab按钮列表
     * @param array $tab_list    Tab列表  array('title' => '标题', 'href' => 'http://www.xxx.com')
     * @param string $current_tab 当前tab
     * @return $this
     */
    public function setTabNav($tab_list, $current_tab) {
        $this->_tab_nav = array('tab_list' => $tab_list, 'current_tab' => $current_tab);
        return $this;
    }
    /**
     * 组tab
     * @param array $tab_list    Tab列表  array('title' => '标题', 'href' => 'http://www.xxx.com')
     * @param string $current_tab 当前tab
     * @return $this
     */
    public function setGTabNav($tab_list, $current_tab) {
        $this->_group_tab_nav = array('tab_list' => $tab_list, 'current_tab' => $current_tab);
        return $this;
    }

    /**插入配置分组
     * @param string $name 组名
     * @param array $list 组内字段列表
     * @return $this.
     * @auth 肖骏涛
     */
    public function group($name, $list = array())
    {
        !is_array($list) && $list = explode(',', $list);
        $this->_group_tab_nav[$name] = $list;
        return $this;
    }

    public function groups($list = array())
    {
        foreach ($list as $key => $v) {
            $this->_group_tab_nav[$key] = is_array($v) ? $v : explode(',', $v);
        }
        return $this;
    }
    /**
     * 直接设置表单项数组
     * @param array $form_items 表单项数组
     * @return $this
     */
    public function setExtraItems($extra_items) {
        $this->_extra_items = $extra_items;
        return $this;
    }

    /**
     * 设置表单提交地址
     * @param string $url 提交地址
     * @return $this
     */
    public function setPostUrl($post_url) {
        $this->_post_url = $post_url;
        return $this;
    }

    /**
     * 加入一个表单项
     * @param string $name 表单名
     * @param string $title 表单标题
     * @param string $type 表单类型(取值参考系统配置FORM_ITEM_TYPE)
     * @param string $tip 表单提示说明
     * @param array $options 表单options
     * @param string $confirm 表单验证
     * @param string $extra_class 表单项是否隐藏
     * @param string $extra_attr 表单项额外属性
     * @return $this
     */
    public function addFormItem($name, $title='', $tip='',$type='text',$options = array(),$confirm='',$extra_attr = '',$extra_class = '') {
        $item['name'] = $name;
        $item['type'] = $type;
        $item['title'] = $title;
        $item['tip'] = $tip;
        $item['options'] = $options;
        $item['confirm'] = $confirm;//验证。required必填，
        $item['extra_class'] = $extra_class;
        $item['extra_attr'] = $extra_attr;
        $this->_form_items[] = $item;
        return $this;
    }

    /**
     * 设置表单表单数据
     * @param array $form_data 表单数据
     * @return $this
     */
    public function setFormData($form_data) {
        $this->_form_data = $form_data;
        return $this;
    }

    /**
     * 设置额外功能代码
     * @param string $extra_html 额外功能代码
     * @return $this
     */
    public function setExtraHtml($extra_html) {
        $this->_extra_html = $extra_html;
        return $this;
    }
    /**
     * 公共按钮方法
     * @param  string $title 按钮标题
     * @param  array $attr 按钮属性
     * @return $this
     */
    public function button($title, $attr = array())
    {
        $this->_buttonList[] = array('title' => $title, 'attr' => $attr);
        return $this;
    }
    /**
    *添加按钮
    *@param string $type 按钮类型
    *@param string $title 按钮标题
    *@param string $title 提交地址
    *@return $this
    */
    public function addButton($type='submit',$title='',$url=''){
        switch ($type) {
            case 'submit'://确认按钮
                if ($url!= '') {
                    $this->setPostUrl($url);
                }
                if ($title == '') {
                    $title ='确定';
                }
                
                $ajax_submit='';
                if ($this->_ajax_submit==true) {
                    $ajax_submit='ajax-post';
                }
                $attr = array();
                $attr['class'] = "btn btn-block btn-primary submit {$ajax_submit} ";
                //$attr['class']="radius ud-button bg-color-blue submit {$ajax_submit} ud-shadow";
                $attr['type'] = 'submit';
                $attr['target-form'] = 'form-builder';
                break;
            case 'back'://返回
                if ($title == '') {
                    $title ='返回';
                }
                $attr = array();
                $attr['onclick'] = 'javascript:history.back(-1);return false;';
                $attr['class'] = 'btn btn-block btn-default return';
                //$attr['class'] = 'radius ud-button color-5 submit ud-shadow';
                break;
            case 'reset'://重置
                if ($title == '') {
                    $title ='重置';
                }
                $attr = array();
                $attr['onclick'] = 'javascript:document.getElementById("form1").reset();return false;';
                $attr['class'] = 'btn btn-block btn-warning';
                //$attr['class'] = 'radius ud-button color-5 submit ud-shadow';
                break;
            case 'link'://链接
                if ($title == '') {
                    $title ='按钮';
                }
                $attr['onclick'] = 'javascript:location.href=\''.$url.'\';return false;';
                break;
            
            default:
                # code...
                break;
        }
        return $this->button($title, $attr);
    }
    /**
     * 设置提交方式
     * @param string $title 标题文本
     * @return $this
     */
    public function setAjaxSubmit($ajax_submit = true) {
        $this->_ajax_submit = $ajax_submit;
        return $this;
    }

    private function assign_list($templateFile='',$vars =array(), $replace ='', $config = '')
    {
        //额外已经构造好的表单项目与单个组装的的表单项目进行合并
        if (!empty($this->_extra_items)) {
            $this->_form_data = array_merge($this->_form_data, $this->_extra_items);
        }

        //设置post_url默认值
        $this->_post_url = $this->_post_url ? $this->_post_url : '';
        //编译表单值
        if ($this->_form_items) {
            foreach ($this->_form_items as &$item) {
                if (isset($this->_form_data[$item['name']])) {
                    $item['value'] = $this->_form_data[$item['name']];
                }

            }
        }
        //编译按钮的html属性
        foreach ($this->_buttonList as &$button) {
            $button['attr'] = $this->compileHtmlAttr($button['attr']);
        }
        //dump($this->_form_items);
        $this->assign('meta_title', $this->_meta_title);  //页面标题
        $this->assign('group_tab_nav', $this->_group_tab_nav);//页面Tab导航
        $this->assign('post_url', $this->_post_url);    //标题提交地址
        $this->assign('fieldList', $this->_form_items);  //表单项目
        $this->assign('ajax_submit', $this->_ajax_submit);//是否ajax提交
        $this->assign('buttonList', $this->_buttonList);//按钮组
        $this->assign('extra_html', $this->_extra_html);  //额外HTML代码
    }


    /**
     * 直接渲染模板成字符串
     * @param  string $templateFile 模板名
     * @param  array $vars 模板变量
     * @param  string $replace
     * @param  string $config
     * @return parent::fetch('formbuilder');
     */
    public function fetch($templateFile='',$vars =array(), $replace ='', $config = '')
    {
        $this->assign_list($templateFile,$vars, $replace, $config);
        return $this->view->fetch('builder/formbuilder');
    }
}