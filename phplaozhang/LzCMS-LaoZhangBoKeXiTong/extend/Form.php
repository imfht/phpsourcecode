<?php

class Form {
	/**
     * 单行文本
     * @param string $name   表单名称
     * @param string $value  默认值
     * @param array $label   参数
     * @param array $description   表单提示
     * @param array $verify   验证方式
     * @return string
     */
    static public function input($type = 'text',$name, $value, $label, $description='',$placeholder='',$verify='',$is_readonly = false) {
  		$string = '<div class="layui-form-item"><label class="layui-form-label">'.$label.'</label>';
        $string .= '<div class="layui-input-inline input-custom-width"><input type="'.$type.'" name="'.$name.'" value="'.$value.'" lay-verify="'.$verify.'" autocomplete="off" placeholder="'.$placeholder.'" '.($is_readonly ? 'readonly' : '').' class="layui-input"></div>';
        if($description) {
            $string .= '<div class="layui-form-mid layui-word-aux">'.$description.'</div>';
        }
        $string .= '</div>';
        return $string;
    }

    /**
     * 多行文本
     * @param string $name   表单名称
     * @param string $value  默认值
     * @param array $label   参数
     * @param array $description   表单提示
     * @param array $verify   验证方式
     * @return string
     */
    static public function textarea($name, $value, $label, $description='',$placeholder='',$verify='') {
        $string = '<div class="layui-form-item"><label class="layui-form-label">'.$label.'</label>';
        $string .= '<div class="layui-input-inline input-custom-width"><textarea name="'.$name.'" lay-verify="'.$verify.'" autocomplete="off" placeholder="'.$placeholder.'" class="layui-textarea">'.$value.'</textarea></div>';
        if($description) {
            $string .= '<div class="layui-form-mid layui-word-aux">'.$description.'</div>';
        }
        $string .= '</div>';
        return $string;
    }

    /**
     * layui编辑器
     * @param string $name   表单名称
     * @param string $value  默认值
     * @param array $label   参数
     * @param array $description   表单提示
     * @param array $verify   验证方式
     * @return string
     */
    static public function layedit($name, $value, $label, $description='',$placeholder='',$verify='', $id = 'layedit') {
        $string = '<div class="layui-form-item"><label class="layui-form-label">'.$label.'</label>';
        $string .= '<div class="layui-input-block"><textarea name="'.$name.'" lay-verify="'.$verify.'" autocomplete="off" placeholder="'.$placeholder.'" class="layui-textarea layui-hide" id="'.$id.'">'.htmlentities($value).'</textarea></div>';
        if($description) {
            $string .= '<div class="layui-form-mid layui-word-aux">'.$description.'</div>';
        }
        $string .= '</div>';
        return $string;
    }

    /**
     * 开启/关闭
     * @param string $name  表单名称
     * @param string $value 默认值
     * @param array $description   表单提示
     * @return string
     */
    static public function enabled($name, $value = 'on', $label, $description='') {
        $string = '<div class="layui-form-item"><label class="layui-form-label">'.$label.'</label><div class="layui-input-inline input-custom-width">';
        $checked = ($value == 'on') ? ' checked' : '';
        $string .= '<input type="checkbox"  name="'.$name.'" '.$checked.' lay-skin="switch" title="开关">';
        $string .='</div><div class="layui-form-mid layui-word-aux">'.$description.'</div></div>';
        return $string;
    }

    /**
     * 单选框
     * @param string $name  表单名称
     * @param string $value 默认值
     * @param array $items 选项列表
     * @return mixed
     */
    static public function radio($name, $value, $label, $description='', $items = array(),$filter='') {
        if(!is_array($items) || empty($items)) return false;
        $string = '<div class="layui-form-item"><label class="layui-form-label">'.$label.'</label><div class="layui-input-inline input-custom-width">';
        foreach( $items as $key => $item ) {
            $checked = ($value == $key) ? ' checked' : '';
            $string .= '<input type="radio" name="'.$name.'" value="'.$key.'" title="'.$item.'" lay-filter="'.$filter.'" '.$checked.'>';
        }
        $string .= '</div><div class="layui-form-mid layui-word-aux">'.$description.'</div></div>';
        return $string;
    }

    /**
     * 多选框
     * @param string $name  表单名称
     * @param string $value 默认值 以(,)隔开的字符串
     * @param array $items 选项列表
     * @return mixed
     */
    static public function checkbox($name, $value, $label, $description='', $items = array()) {
        if(!is_array($items) || empty($items)) return false;
        $string = '<div class="layui-form-item"><label class="layui-form-label">'.$label.'</label><div class="layui-input-inline input-custom-width">';
        $value = explode(',', $value);
        foreach( $items as $key => $item ) {
            $checked = (in_array($key, $value)) ? ' checked' : '';
            $string .= '<input type="checkbox" name="'.$name.'['.$key.']" value="'.$key.'" title="'.$item.'" '.$checked.'>';
        }
        $string .= '</div><div class="layui-form-mid layui-word-aux">'.$description.'</div></div>';
        return $string;
    }

    /**
     * 下拉框
     * @param string $name  表单名称
     * @param string $value 默认值
     * @param array $options 选项列表
     * @return string
     */
    static public function select($name, $value, $label, $description='', $options = array(), $verify='' ) {
        if(!is_array($options) || empty($options)) return false;
        $string = '<div class="layui-form-item"><label class="layui-form-label">'.$label.'</label><div class="layui-input-inline input-custom-width"><select name="'.$name.'" lay-verify="'.$verify.'">';
        $string .= '<option value="" >请选择</option>';
        foreach( $options as $key => $option ) {
            $selected = ($value == $key) ? true : false;
            $string .= '<option value="'.$key.'" '.($selected ? ' selected="" ' : '').' >'.$option.'</option>';
        }
        $string .= '</select></div><div class="layui-form-mid layui-word-aux">'.$description.'</div></div>';
        return $string;
    }
    
    /**
     * 无option下拉框
     * @param string $name  表单名称
     * @param string $value 默认值
     * @param array $options 选项列表
     * @return string
     */
    static public function select_no_option($name, $value, $label, $description='', $options = '<option value="" >请选择</option>', $verify='' ) {
        if(empty($options)) return false;
        $string = '<div class="layui-form-item"><label class="layui-form-label">'.$label.'</label><div class="layui-input-inline input-custom-width"><select name="'.$name.'" lay-verify="'.$verify.'">';
        $string .= $options;
        $string .= '</select></div><div class="layui-form-mid layui-word-aux">'.$description.'</div></div>';
        return $string;
    }

    /**
     * 文件上传
     * @param string $name  表单名称
     * @param string $value 默认值
     * @param array $file_type 文件类型
     * @param array $file_ext 文件后缀
     * @param array $title 显示文字
     * @param array $id input的id
     * @return string
     */
    static public function file($name, $value, $label, $description='', $placeholder='', $file_type='images', $file_ext='', $title='',$id='image' ) {
        $string = '<div class="layui-form-item"><label class="layui-form-label">'.$label.'</label><div class="layui-input-inline input-custom-width">';
        $string .= '<input type="text" name="'.$name.'" value="'.$value.'" lay-verify="" autocomplete="off" placeholder="'.$placeholder.'" class="layui-input"><input type="file" name="file" lay-method="post" lay-type="'.$file_type.'" lay-ext="'.$file_ext.'" lay-title="'.$title.'" class="layui-upload-file" id="'.$id.'">';
        $string .= '</div><div class="layui-form-mid layui-word-aux">'.$description.'</div></div>';
        return $string;
    }

    /**
     * 日期时间
     * @param string $name  表单名称
     * @param string $value 默认值
     * @param array $options 选项列表
     * @return string
     */
    static public function date($name, $value, $label, $description='',$placeholder='YYYY-MM-DD hh:mm:ss',$verify='datetime' ,$istime = true) {
        $string = '<div class="layui-form-item"><label class="layui-form-label">'.$label.'</label>';
        $string .='<div class="layui-input-inline input-custom-width"><input type="text" name="'.$name.'" value="'.$value.'" id="date" lay-verify="'.$verify.'" placeholder="'.$placeholder.'" autocomplete="off" class="layui-input" onclick="layui.laydate({elem: this,istime: '.$istime.', format: \'YYYY-MM-DD hh:mm:ss\' })"></div>';
        if($description) {
            $string .= '<div class="layui-form-mid layui-word-aux">'.$description.'</div>';
        }
        $string .= '</div>';
        return $string;
    }

    /**
     * 百度编辑器
     * @param string $name   表单名称
     * @param string $value  默认值
     * @param string $width 宽度
     * @param string $height 高度
     * @return string
     */
    static public function umeditor($name, $value='',$label, $width ='100%', $height = '500',$toolbar = FALSE,$id='umeditor') {
        $string = '<div class="layui-form-item"><label class="layui-form-label">'.$label.'</label>';
        $string .= '<div class="layui-input-block"><script type="text/plain" id="'.$id.'" style="width:'.$width.';height:'.$height.';">'.$value.'</script></div>';
        $string .= '</div>';

        $string .= '<link rel="stylesheet" href="/static/umeditor/themes/default/css/umeditor.css">';
        $string .= '<script type="text/javascript" src="/static/umeditor/third-party/jquery.min.js"></script>';
        $string .= '<script type="text/javascript" src="/static/umeditor/umeditor.config.js"></script>';
        $string .= '<script type="text/javascript" src="/static/umeditor/umeditor.min.js"></script>';
        $string .= '<script type="text/javascript" src="/static/umeditor/lang/zh-cn/zh-cn.js"></script>';

        $width = (!empty($width)) ? $width : '100%';
        $height = (!empty($height)) ? $height : '500';

        $string .= '<script type="text/javascript">';

        $string .= 'var um_'.$id.' = UM.getEditor(\''.$id.'\', {
            textarea : \''.$name.'\'
            ,initialFrameWidth:\''.$width.'\'
            ,initialFrameHeight:\''.$height.'\'
            ,imageUrl:\''.url('upload/umeditor_upimage').'\'
            ,imageFieldName:\'file\'';
        if($toolbar){
            $string .= ',toolbar:[ \''.$toolbar .'\']';
        }
            
        $string .= '});';
        $string .= '</script>';
        return $string;
    }

    /**
     * 图集
     * @param string $name  表单名称
     * @param array $value 默认值
     * @return string
     */
    static public function images($name, $value, $label, $description='',$id = 'images') {
        $string = '<div class="layui-form-item"><label class="layui-form-label">'.$label.'</label><div class="layui-input-block images-block-container">';
        if(is_array($value)){
            foreach ($value as $k => $v) {
                $string .= '<div class="image-block"><input type="hidden" name="images['.$k.']" value="'.$v.'" class="images-input"><img class="img" src="'.$v.'"><div class="image-block-mask"><span class="del_btn"><i class="layui-icon">&#x1006;</i></span><a class="layui-btn set-index">设为主图</a></div></div>';
            }
        }
        $string .= '<div class="image-add-blcok"><input type="file" name="file[]" lay-method="post" lay-type="images"  lay-title="" class="layui-upload-file" id="'.$id.'" multiple="multiple"></div></div>';
        if($description) {
            $string .= '<div class="layui-form-mid layui-word-aux">'.$description.'</div>';
        }
        $string .= '</div>';
        return $string;
    }

}
?>