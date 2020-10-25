<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Util;
use Tang\Services\ConfigService;
use Tang\Services\FileService;
use Tang\Services\RequestService;

/**
 * 表单生成器
 * Class Form
 * @package Tang\Util
 */
class Form
{
    private static $isLoadCalendar = false;
    private static $isLoadEditor = false;
    public static function createByArray(array $components,array $values = array())
    {
        foreach($components as $component)
        {
            if(!isset($component['name']) || !$component['name'])
            {
                continue;
            }
            $component['value'] = isset($values[$component['name']]) ? $values[$component['name']] : '';
			echo static::makeComponent($component);
        }
    }

	/**
	 * 从file文件创建表单信息
	 * @param string $name
	 */
	public static function createByFile($name,array $values = array())
	{
		$file = ConfigService::get('applicationDirectory').DIRECTORY_SEPARATOR.'Lib'.DIRECTORY_SEPARATOR.'Forms'.DIRECTORY_SEPARATOR.ucfirst($name).'.php';
		if(!file_exists($file))
		{
			echo $file.' form can\'t found';
		} else
		{
			$formArray = include($file);
			if(!is_array($formArray))
			{
				echo $name.' is not retrurn an array!';
				return;
			}
			static::createByArray($formArray,$values);
		}
	}
    public static function makeComponent(array $component)
    {
        if (!$component || !isset($component['name']) || !$component['name'])
        {
            return '';
        }
        $args = array();
        $args[] = $component['tipsName'];
        $component['name2'] = $component['name'];
        $component['name'] = 'data['.$component['name'].']';
        if (!isset($component['id']) || !$component['id'])
        {
            $component['id'] = $component['name2'];
        }
        $componentString = '';
        $type = isset($component['type']) && $component['type'] ? strtolower($component['type']):'';
        switch($type)
        {
			case 'input':
				$componentString = static::input($component['name'],$component['id'],$component['value'],$component['javascript'],$component['class'],$component['style']);
				break;
			case 'password':
				$componentString = static::password($component['name'],$component['id'],$component['javascript'],$component['class'],$component['style']);
				break;
			case 'select':
				$componentString = static::select($component['name'],$component['id'],$component['options'],$component['value'],$component['javascript'],$component['class'],$component['style']);
				break;
			case 'calendar':
				$componentString = static::calendar($component['name'],$component['id'],$component['value'],$component['javascript'],$component['class'],$component['style'],$component['format']);
				break;
			case 'textarea':
				!isset($component['rows']) && $component['rows'] = 6;
				!isset($component['cols']) && $component['cols'] = 50;
				$componentString = static::textarea($component['name'],$component['id'],$component['value'],$component['javascript'],$component['class'],$component['rows'],$component['cols'],$component['style']);
				break;
			case 'radio':
				$componentString = static::radio($component['name'],$component['value'],$component['options'],$component['javascript'],$component['class'],$component['style']);
				break;
			case 'checkbox':
				$componentString = static::checkbox($component['name'],$component['value'],$component['options'],$component['javascript'],$component['class'],$component['style'],$component['valueCallback']);
				break;
			case 'file':
				$componentString = static::file($component['name'],$component['id'],$component['value'],$component['javascript'],$component['class'],$component['style']);
				break;
			case 'editor':
				$componentString = static::editor($component['name'],$component['id'],$component['value'],$component['style']);
				break;
			case 'inputSelect':
				$componentString = static::inputSelect($component['name'],$component['id'],$component['options'],$component['value'],$component['javascript'],$component['class'],$component['style']);
				break;
			default:
				$componentString = 'not found '.$type.' ;';
        }
		$args[] = $componentString;
		$args[] = isset($component['description']) ? $component['description']:'';
		$format = '';
		$formatArray = ConfigService::get('form');
		if(!is_array($formatArray) || !$formatArray)
		{
			$format = '<tr><td class=td_right width=100>%s：</td><td>%s %s</td></tr>';
		} else
		{
			$moduleName = RequestService::getService()->getRouter()->getModuleValue();
            $moduleName = lcfirst($moduleName);
			if(!isset($formatArray[$moduleName]))
			{
				$format = reset($formatArray);
			} else
			{
				$format = $formatArray[$moduleName];
			}
		}
		return vsprintf($format,$args);
    }

    /**
     * INPUT控件
     * @param string $name 控件name属性
     * @param string $id 控件id属性
     * @param string $value 控件value属性
     * @param string $javascript 控件js
     * @param string $className 控件class属性
     * @param string $style 控件style属性
     * @return string
     */
    public static function input($name, $id = '',$value = '',$javascript = '',$className = '', $style = '')
    {
        return '<input name="'.$name.'" id="'. $id.'" value="'.$value.'" '.$javascript.' class="'.static::getDefault($className, 'tang-input-text').'" type="text" '.static::createStyle($style).'>';
    }

    /**
     * 构建PASSWORD
     * @param string $name 控件name属性
     * @param string $id 控件id属性
     * @param string $javascript 控件Js
     * @param string $className 控件class属性
     * @param string $style 控件的style属性
     * @return string
     */
    public static function password($name,$id = '',$javascript= '',$className = '', $style = '')
    {
        return '<input name="'.$name.'" id="'.$id.'" '.$javascript.' class="'.static::getDefault($className, 'tang-input-password').'" type="password" '.static::createStyle($style).'>';
    }

    /**
     * 下拉框
     * @param string $name 控件name属性
     * @param string $id 控件id属性
     * @param array $options 控件下拉框键值对应数组
     * @param string $value 值
     * @param string $javascript 控件Js
     * @param string $className 控件class属性
     * @param string $style  控件的style属性
     * @return string
     */
    public static function select($name,$id = '',$options = array(),$value = '',$javascript='',$className = '',$style='')
    {
        $string = '<select name="' . $name . '" class="' . static::getDefault($className,'tang-select') .'" id="'. $id .'" '.static::createStyle($style).' '.$javascript.'>';
        $select = '';
        if (is_array($options) && $options)
        {
            foreach ($options as $key => $opValue)
            {
                $select = '';
                if ($key == $value)
                {
                    $select = ' selected="true" ';
                }
                $string .= '<option value="'.$key.'" '.$select.'>'.$opValue.'</option>';
            }
        }
        $string .= '</select>';
        return $string;
    }

    /**
     * 文本下拉框
     * @param string $name 控件name属性
     * @param string $id 控件id属性
     * @param array $options 控件下拉框键值对应数组
     * @param string $value 值
     * @param string $javascript 控件Js
     * @param string $className 控件class属性
     * @param string $style  控件的style属性
     * @return string
     */
    public static function inputSelect($name, $id = '',$options = array(),$value = '', $javascript = '', $className = '',$style='')
    {
        $string = '<div class="'.static::getDefault($className, 'tang-input-select').'" '.static::createStyle($style).'>';
        $string .= static::input($name,$id,$value,$javascript);
        $string .= static::select('',$id.'-select',$options,$value,' onchange="document.getElementById(\''.$id.'\').value=this.value"');
        return $string;
    }

    /**
     * 日历控件
     * 默认使用my97日历控件
     * 默认需要将my97日历控件放在/Public/Javascript/My97目录下
     * 用户可使用setCalendarDirectory(calendarPath)来设置日历文件目录
     * @param string $name 控件name属性
     * @param string $id 控件id属性
     * @param string $value 值
     * @param string $javascript 控件Js
     * @param string $className 控件class属性
     * @param string $style  控件的style属性
     * @param string $dateFormat 日期格式化 参见my97日历控件格式化
     * @return string
     */
    public static function calendar($name, $id = '', $value = '', $javascript = '', $className = '',$style='', $dateFormat = '')
    {
        $string = '';
        !$id && $id = $name;
        if(!static::$isLoadCalendar)
        {
            $string .= ' <script language="javascript" type="text/javascript" src="'.ConfigService::get('form.calendarDirectory').'/WdatePicker.js"></script>';
            static::$isLoadCalendar = true;
        }
        $javascript .= 'onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:\''.static::getDefault($dateFormat, 'yyyy-MM-dd HH:mm:ss').'\',autoPickDate:true})"';
        $string .= static::input($name,$id,$value,$javascript,static::getDefault($className, 'Wdate'),$style);
        return $string;
    }

    /**
     * 多行输入文本框
     * @param string $name 控件name属性
     * @param string $id 控件id属性
     * @param string $value 控件值
     * @param string $javascript 控件js
     * @param string $className 控件class属性
     * @param int $rows 控件可见行数
     * @param int $cols 控件可见宽度
     * @param string $style 控件style属性
     * @return string
     */
    public static function textarea($name,$id = '', $value = '', $javascript = '', $className = '', $rows = 6, $cols = 50,$style='')
    {
        return '<textarea id="'.$id.'" rows="'.$rows.'" cols="'.$cols.'" class="' .static::getDefault($className, 'tang-text') .'" '.$javascript.' name="'.$name.'" '.static::createStyle($style).'>'.$value.'</textarea>';
    }

    /**
     * radio
     * @param string $name
     * @param string $value
     * @param array $options
     * @param string $className
     * @param string $javascript
     * @param string $style
     * @return string
     */
    public static function radio($name,$value = '',$options = array(),$javascript = '',$className = '',$style= '')
    {
        if (!$options)
        {
            return '';
        }
        !$value && $value = array_keys($options)[0];
        $string = $checked = '';
        $style = static::createStyle($style);
        $className = static::getDefault($className, 'tang-radio');
        foreach ($options as $optionKey => $optionValue)
        {
            $checked = $optionKey == $value ? 'checked="true"' : '';
            $string .= '<input type="radio" name="' . $name . '" class="' .
                $className . '" value="' . $optionKey . '" ' . $javascript . ' ' .
                $checked . ' '.$style.'>' . $optionValue . ' &nbsp;&nbsp;';
        }
        return $string;
    }

    /**
     * checkbox
     * @param $name
     * @param array $values
     * @param array $options
     * @param string $className
     * @param string $javascript
     * @param string $style
     * @return string
     */
    public static function checkbox($name,$values = array(),$options = array(),$javascript = '',$className = '',$style='',$valueCallback = null)
    {
        if (!$options)
        {
            return '';
        }
        $string = $checked = '';

        $style = static::createStyle($style);
        $className = static::getDefault($className,'tang-checkbox');
        if($valueCallback && is_callable($valueCallback))
        {

        } else
        {
            $hasValue = is_array($values) && $values;

            $valueCallback = function($values,$key) use($hasValue)
            {
                return $hasValue && in_array($key,$values);
            };
        }
        foreach ($options as $optionKey => $optionValue)
        {
            $checked = $valueCallback($values,$optionKey) ? 'checked="true"' : '';
            $string .= '<input type="checkbox" name="' . $name . '[]" class="' .
                $className . '" value="' . $optionKey . '" ' . $javascript .$style. ' ' .
                $checked . '>' . $optionValue . '&nbsp;&nbsp;';
        }
        return $string;
    }

    /**
     * 文件上传控件
     * @param $name
     * @param string $id
     * @param string $value
     * @param string $className
     * @param string $javascript
     * @param string $style
     * @return string
     */
    public static function file($name,$id = '',$value='',$javascript = '',$className = '',$style='')
    {
        $string = '';
        if($value)
        {
            $string .= '<div id="'.$id.'Thumb">';
            if(in_array(FileService::getService()->getExtension($value), array('jpeg','jpg','gif','png')))
            {
                $string .= '<img height="80px" src="'.$value.'">';
            } else
            {
                $string .= '<a href="'.$value.'" target="_blank">'.$value.'</a>';
            }
            $string .= '<input class="button" type="button" onclick="$(\'#'.$id.'\').show();$(\'#'.$id.'Thumb\').hide()" value=" 重新上传 "></div>';
            $javascript .= ' style="display:none" ';
        }
        $className = static::getDefault($className, 'tang-file');
        return $string.'<input type="file" id="'.$id.'" name="' .$name. '" class="' .$className . '" ' . $javascript . ' '.static::createStyle($style).'></input>';
    }

    /**
     * 编辑器
     * 本函数使用的是baidu编辑器。需要在网站根目录添加ueditor百度编辑器目录
     * @param $name
     * @param $id
     * @param string $value
     * @param string $style
     * @return string
     */
    public static function editor($name,$id,$value = '',$style='')
    {
        $string = '';
        if(!static::$isLoadEditor)
        {
            $editorDirectory = ConfigService::get('form.editorDirectory').'/';
            $string .= '<script type="text/javascript" charset="utf-8" src="'.$editorDirectory.'ueditor.config.js"></script>
    					<script type="text/javascript" charset="utf-8" src="'.$editorDirectory.'ueditor.all.min.js"> </script>
    					<script type="text/javascript" src="'.$editorDirectory.'lang/zh-cn/zh-cn.js"></script>
		            	<link rel="stylesheet" type="text/css" href="'.$editorDirectory.'themes/default/css/ueditor.min.css"/>';
            static::$isLoadEditor = true;
        }
        $string .= '<script type="text/plain" name="'.$name.'" id="'.$id.'" '.static::createStyle($style).'>' .htmlspecialchars_decode($value) . '</script>';
        $string .= '<script>
					var a = UE.getEditor("'.$id.'");
					</script>';
        return $string;
    }

    protected static function getDefault($value,$default)
    {
        return $value ? $value : $default;
    }
    protected static function createStyle($style = '')
    {
        return $style ? ' style="'.$style.'"':'';
    }
}