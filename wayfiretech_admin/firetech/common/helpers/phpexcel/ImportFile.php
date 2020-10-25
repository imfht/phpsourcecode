<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-26 08:15:46
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-26 08:15:49
 */
 

namespace common\helpers\phpexcel;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

/**
 * 基于 PhpSpreadsheet, 用于读取 Excel 表格的内容.
 *
 * 注意: 本类仅返回一个数组, 不处理具体导入到数据库的操作.
 *
 * @author zhangmoxuan <1104984259@qq.com>
 * @link http://www.zhangmoxuan.com
 * @QQ 1104984259
 * @Date 2019-3-13
 * @see https://github.com/PHPOffice/PhpSpreadsheet
 */
class ImportFile
{
    /**
     * @param string $file 导入文件的路径
     * @param bool $setFirstRecordAsKeys 是否将 Excel 文件中的第一行记录设置为每行数据的键; 为`false`时将使用字母列(eg: A,B,C).
     * @param bool $setIndexSheetByName 如果 Excel 文件中有多个工作表, 是否以表名(eg:sheet1,sheet2)作为键名; 为 false 时使用数字(eg:0,1,2).
     * @param bool|string|array $getOnlySheet 当 Excel 文件中有多个工作表时, 指定仅获取某个工作表(eg:sheet1)或某几个工作表(eg:[sheet1,sheet2]).
     * 当`$setIndexSheetByName`为`false`时, `$getOnlySheet`只能是数组, 且数组元素的类型必须是整数;
     * 当`$setIndexSheetByName`为`true`时, `$getOnlySheet`可以是字符串或数组, 数组元素的类型必须是字符串;
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public static function getData($file, $setFirstRecordAsKeys = true, $setIndexSheetByName = true, $getOnlySheet = false)
    {
        $ext = strtolower(substr($file, strrpos($file, '.') + 1));  // 文件扩展名
        if($ext == "csv"){
            $reader = new Csv();
            $spreadsheet = $reader->setInputEncoding("GBK")->load($file);  // 不设置将导致中文列内容返回boolean(false)或乱码
        }else{
            $spreadsheet = IOFactory::load($file);  // 载入 Excel 表格
        }
        $sheetCount = $spreadsheet->getSheetCount();  // 获取 Excel 中工作表的数量
        $sheetData = [];
        if(is_string($getOnlySheet)){
            $sheetData = $spreadsheet->getSheetByName($getOnlySheet)->toArray(null, true, true, true);  // 返回一个二维数组, 数组键是行的id, 子数组键是列的大写英文字母
            if($setFirstRecordAsKeys){
                $sheetData = self::setFirstRecordAsLabel($sheetData);  // 将第一行记录设置为每行数据的键
            }
        }elseif($sheetCount <= 1){
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);  // 返回一个二维数组, 数组键是行的id, 子数组键是列的大写英文字母
            if($setFirstRecordAsKeys){
                $sheetData = self::setFirstRecordAsLabel($sheetData);  // 将第一行记录设置为每行数据的键
            }
        }else{
            foreach($spreadsheet->getSheetNames() as $sheetIndex => $sheetName){
                if($setIndexSheetByName){
                    $indexed = $sheetName;  // 索引类型: 表名索引
                    $sheet = $spreadsheet->getSheetByName($indexed);  // 按表名获取表
                }else{
                    $indexed = $sheetIndex;  // 索引类型: 数字索引
                    $sheet = $spreadsheet->getSheet($indexed);  // 按索引获取工作表
                }
                if(is_array($getOnlySheet) && !in_array($indexed, $getOnlySheet, true)){
                    continue;  // 如果不在数组`$getOnlySheet`中, 则跳过当前循环
                }
                $sheetData[$indexed] = $sheet->toArray(null, true, true, true);  // 返回一个二维数组, 数组键是行的id, 子数组键是列的大写英文字母
                if($setFirstRecordAsKeys){
                    $sheetData[$indexed] = self::setFirstRecordAsLabel($sheetData[$indexed]);  // 将第一行记录设置为每行数据的键
                }
            }
        }
        return $sheetData;
    }

    /**
     * 将第一行记录设置为每行数据的键, 然后返回新数组.
     * @param array $sheetData
     * @return array
     */
    public static function setFirstRecordAsLabel($sheetData)
    {
        $keys = array_shift($sheetData);  // 从数组移除第一行并返回该行的值(会重置原数组的下标)
        $newData = [];
        foreach($sheetData as $data){
            $newData[] = array_combine($keys, $data);  // 合并两个数组来创建一个新数组, $keys为键名, $v为键值
        }
        return $newData;
    }
}
