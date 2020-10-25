<?php
// 公共Builder排序构建器控制器 
/*部分代码源自opensns*/

// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
// | 原作者：心云间、凝听
// +----------------------------------------------------------------------


namespace tpvue\admin\Builder;


class AdminSortBuilder extends AdminBuilder
{
    private $_meta_title;                  // 页面标题
    private $_list;
    private $_buttonList;
    private $_post_url;              // 表单提交地址
    private $_extra_html;            // 额外功能代码
    private $_ajax_submit = true;    // 是否ajax提交

    /**
     * 设置页面标题
     * @param $title 标题文本
     * @return $this
     */
    public function setMetaTitle($meta_title) {
        $this->_meta_title = $meta_title;
        return $this;
    }
    /**
     * 设置额外功能代码
     * @param $extra_html 额外功能代码
     * @return $this
     */
    public function setExtraHtml($extra_html) {
        $this->_extra_html = $extra_html;
        return $this;
    }

    public function setListData($list) {
        $this->_list = $list;
        return $this;
    }

    public function button($title, $attr = array())
    {
        $this->_buttonList[] = array('title' => $title, 'attr' => $attr);
        return $this;
    }
    /**
    *添加按钮
    *@param $type 按钮类型
    *@param $title 按钮标题
    *@param $title 提交地址
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
                
                $attr = array();
                $attr['class'] = "btn btn-primary submit sort_confirm";
                //$attr['class']="radius ud-button bg-color-blue submit {$ajax_submit} ud-shadow";
                $attr['type'] = 'button';
                $attr['target-form'] = 'sort-builder';
                break;
            case 'back'://返回
                if ($title == '') {
                    $title ='返回';
                }
                $attr = array();
                $attr['onclick'] = 'javascript:history.back(-1);return false;';
                $attr['class'] = 'btn btn-default return';
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
     * 设置表单提交地址
     * @param $url 提交地址
     * @return $this
     */
    public function setPostUrl($post_url) {
        $this->_post_url = $post_url;
        return $this;
    }

    public function display() {
        //设置post_url默认值
        $this->_post_url=$this->_post_url? $this->_post_url:U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
        //编译按钮的属性
        foreach($this->_buttonList as &$e) {
            $e['attr'] = $this->compileHtmlAttr($e['attr']);
        }
        unset($e);

        //显示页面
        $this->assign('meta_title', $this->_meta_title);
        $this->assign('list', $this->_list);
        $this->assign('buttonList', $this->_buttonList);
        $this->assign('post_url', $this->_post_url);
        parent::display('sortbuilder');
    }

    public function doSort($table, $ids) {
        $ids = explode(',', $ids);
        $res = 0;
        foreach ($ids as $key=>$value){
            $res += M($table)->where(array('id'=>$value))->setField('sort', $key+1);
        }
        if(!$res) {
            $this->error('排序失败');
        } else {
            $this->success('成功排序');
        }
    }
}