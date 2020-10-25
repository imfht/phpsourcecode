<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/2
 * Time: 10:13
 */

namespace application\modules\report\core;


use application\core\utils\Ibos;

class ReportField
{

    /**
     * 处理表单字段
     * @param array $fields 表单字段数组
     * @param $repid 汇报id
     * @return mixed
     */
    public static function handleField($fields, $repid)
    {
        $count = count($fields);
        for ($i = 0; $i < $count; $i++){
            //长文本进行数据分割
            $fields[$i]['content'] = \CHtml::encode($fields[$i]['content']);
            if ($fields[$i]['fieldtype'] == 1){
                $fields[$i]['content'] = str_replace("\n", "<br >", $fields[$i]['content']);
            }
            $fields[$i]['repid'] = $repid;
        }
        return $fields;
    }

    /**
     * 过滤表单字段数组
     * @param array $field 表单字段数组
     * @return array
     */
    public static function filterField($field)
    {
        $error = '';
        $count  = count($field);
        for ($i =0; $i < $count; $i++) {
            if ($field[$i]['fieldtype'] == 3){
                $rule = self::filterNumber($field[$i]['iswrite'], $field[$i]['content']);
                if (!$rule){
                    $error = $field[$i]['fieldname']. Ibos::lang('Empty or error type');
                    break;
                }
            }elseif ($field[$i]['fieldtype'] == 4){
                $rule = self::filterDatetime($field[$i]['iswrite'], $field[$i]['content']);
                if (!$rule){
                    $error = $field[$i]['fieldname']. Ibos::lang('Empty or error type');
                    break;
                }
            }elseif ($field[$i]['fieldtype'] == 5){
                $rule = self::filterTime($field[$i]['iswrite'], $field[$i]['content']);
                if (!$rule){
                    $error = $field[$i]['fieldname']. Ibos::lang('Empty or error type');
                    break;
                }
            }elseif ($field[$i]['fieldtype'] == 6){
                $rule = self::filterDate($field[$i]['iswrite'], $field[$i]['content']);
                if (!$rule){
                    $error = $field[$i]['fieldname']. Ibos::lang('Empty or error type');
                    break;
                }
            }else{
                $rule = self::isEmptyField($field[$i]['iswrite'], $field[$i]['content']);
                if (!$rule){
                    $error = $field[$i]['fieldname']. Ibos::lang('Empty or error type');
                    break;
                }
            }
        }
        return $error;
    }

    /**
     * 判断表单值是否为数字类型
     * @param integer $iswrite 是否必填
     * @param number $value 字段值
     * @return bool
     */
    public static function filterNumber($iswrite, $value)
    {
        if ($iswrite){
            if ($value === ''){
                return false;
            }else{
                if (is_numeric($value)){
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            if ($value !== ''){
                if (is_numeric($value)){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param integer $iswrite 是否必填
     * @param date $value 日期
     * @return bool
     */
    public static function filterDate($iswrite, $value)
    {
        if ($iswrite){
            if (empty($value)){
                return false;
            }else{
                if (strtotime(date('Y-m-d', strtotime($value))) == strtotime($value)){
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            if (!empty($value)){
                if (strtotime(date('Y-m-d', strtotime($value))) == strtotime($value)){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 验证日期时间类型
     * @param integer $iswrite 是否必填
     * @param date $value 日期
     * @return bool
     */
    public static function filterDatetime($iswrite, $value)
    {
        if ($iswrite){
            if (empty($value)){
                return false;
            }else{
                if (strtotime(date('Y-m-d H:i',strtotime($value))) == strtotime($value)){
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            if (!empty($value)){
                if (strtotime(date('Y-m-d H:i',strtotime($value))) == strtotime($value)){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 验证时间类型
     * @param integer $iswrite 是否必填
     * @param date $value 日期
     * @return bool
     */
    public static function filterTime($iswrite, $value)
    {
        if ($iswrite){
            if (empty($value)){
                return false;
            }else{
                if (strtotime(date('H:i',strtotime($value))) == strtotime($value)){
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            if (!empty($value)){
                if (strtotime(date('H:i',strtotime($value))) == strtotime($value)){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 字段值是否必填
     * @param integer $iswrite 是否必填
     * @param date $value 日期
     * @return bool
     */
    public static function isEmptyField($iswrite, $value)
    {
        if ($iswrite){
            if (empty($value)){
                return false;
            }else{
                return true;
            }
        }
        return true;
    }

    /**
     * 转义表单字段
     * @param $fields
     * @return array
     */
    public static function transferField($fields)
    {
        return array_map(function ($v){
            $v['content'] = mb_convert_encoding(html_entity_decode($v['content']), 'UTF-8', 'UTF-8');
            return $v;
        }, $fields);
    }
}