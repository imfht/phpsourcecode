<?php
/**
 * form 生成类
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date 2013-1-19
 **/
namespace framework\libraries;
require_once 'HtmlCreate.php';


class FormHtml extends HtmlCreate
{
    /**
     * 表单配置
     * @var array
     */
    protected $_config = array(
            'inputStyle'=>'form-input',
            'formStyle'=>'form',
            'rowStyle'=>'form-row',
            'labelStyle'=>'form-label'
        );


    public function __construct(array $formConf=array()){
        foreach($formConf as $key=>$val){
            $this->$key = $val;
        }
    }

    /**
     * 表单开始
     * @param array $attr
     * @return string
     */
    public function fromStart($attr=array())
    {
        if(empty($attr['method'])){
             $attr['method'] = 'get';
        }
        $fromStr= '<form class="'.$this->formStyle.'"';
        foreach ($attr as $key=>$val){
            $fromStr .= ' '.$key.'="'.$val.'"';
        }
        $fromStr =' >';
        return $fromStr;
    }

    /**
     * 表单结束
     * @return string
     */
    public function formEnd()
    {
        return '</form>';
    }


    /**
     * 标签
     * @param array $attr
     * @return string
     */
    public function label(array $attr)
    {
        if(empty($attr['value'])){
            return ;
        }
        if(empty($attr['class'])){
            $attr['class'] = $this->labelStyle;
        }
        $htmlStr= '<label';
        foreach ($attr as $key=>$val){
            if ($key=='value'){continue;}
            $htmlStr .= ' '.$key.'="'.$val.'"';
        }
        $htmlStr .=' >'.$attr['value'].'</label>';
        return $htmlStr;
    }
    /**
     * text 文本框
     * @param array $attr
     * @return string
     */
    public function text(array $attr)
    {
        $attr['type'] = 'text';
        return $this->input($attr);
    }

    /**
     * 密码输入框
     * @param $attr
     * @return string
     */
    public function password(array $attr)
    {
        $attr['type'] = 'password';
        return $this->input($attr);
    }

    /**
     * select
     * @param array $attr
     * @param array $options
     * @param null $selected
     * @return string
     */
    public function select(array $attr, array $options, $selected=null)
    {
        $htmlStr ='<select ';
        foreach ($attr as $key=>$val){
            $htmlStr .= ' '.$key.'="'.$val.'"';
        }
        $htmlStr .= ' >';
        $htmlStr .= $this->options($options, $selected);
        $htmlStr .= ' </select>';
        return $htmlStr;
    }

    /**
     * options
     */
    public function options(array $options, $selected=null)
    {
        $htmlStr= '';
        foreach ($options as $key=>$val)
        {
            $_selected = '';
            if($selected==$key)
            {
                $_selected='selected="selected"';
            }
            $htmlStr .='<option value="'.$key.'" '.$_selected.'>'.$val.'</option>';
        }

    }
    /**
     * 单选框
     * @param $attr
     * @return string
     */
    public function radio(array $attr)
    {
        $attr['type'] = 'radio';
        $attrVals = $attr['value'];
        $attr['value'] = '';
        $htmlStr = $this->rowStart();
        if (isset($attr['label'])) {
            $htmlStr .=$this->label(array('value'=>$attr['label']));
            unset($attr['label']);
        }
        if(is_array($attrVals)){
            foreach($attrVals as $label=>$val){
                $attr['value'] = $val;
                $attr['label'] = $label;
                $htmlStr .= $this->input($attr);
            }
        }
        $htmlStr .= $this->rowEnd();
        return $htmlStr;
    }

    /**
     * 多选框
     * @param array $attr
     * @return string
     */
    public function checkbox(array $attr)
    {
        $attr['type'] = 'checkbox';
        $attrVals = $attr['value'];
        $attr['value'] = '';
        $htmlStr = $this->rowStart();
        if (isset($attr['label'])) {
            $htmlStr .= $this->label(array('value'=>$attr['label']));
        }
        if(is_array($attrVals)){
            foreach($attrVals as $key=>$val){
                $attr['value'] = $val;
                $attr['label'] = $key;
                $htmlStr .= $this->input($attr);
            }
        }
        $htmlStr .=$this->rowEnd();
        return $htmlStr;
    }
    /**
     * 按钮
     * @param array $attr
     * @return string
     */
    public function button(array $attr)
    {
        $attr['type'] ='button';
        return $this->input($attr);
    }
    /**
     * 提交按钮
     * @param array $attr
     * @return string
     */
    public function submit(array $attr)
    {
        $attr['type'] ='submit';
        return $this->input($attr);
    }
    /**
     * 重置按钮
     * @param array $attr
     * @return string
     */
    public function rest(array $attr)
    {
        $attr['type'] ='rest';
        return $this->input($attr);
    }
    /**
     * input 输入框
     * @param array $attr 属性配置
     * @return string
     */
    public function input(array $attr)
    {
        $htmlStr ='';
        if (isset($attr['label'])) {
            $htmlStr .= $this->label(array('value'=> $attr['label']));
        }
        $htmlStr .= '<input ';
        foreach($attr as $key=>$val){
            if($key=='label'){
                continue;
            }
            $htmlStr .= ' '.$key.'="'.$val.'"';
        }
        $htmlStr .= ' />';
        return $htmlStr;
    }
    /**
     * 文本域
     * @param array $attr
     * @return string
     */
    public function textarea($attr=array())
    {
        $htmlStr = '';
        if (isset($attr['label'])) {
            $htmlStr .= $this->label(array('value'=> $attr['label']));
        }
        $htmlStr .= '<textarea ';
        foreach($attr as $key=>$val){
            if($key == 'value' || $key == 'label') continue;
            $htmlStr .= ' '.$key.'="'.$val.'"';
        }
        $htmlStr .= '>';
        $htmlStr .= $attr['value'];
        $htmlStr .= ' </textarea>';
        return $htmlStr;
    }
}