<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-26 08:15:38
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-26 12:46:23
 */
 

namespace common\helpers\phpexcel;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\i18n\Formatter;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * 基于 PhpSpreadsheet, 用于将数据模型导出为 Excel 表格.
 *
 * @author  zhangmoxuan <1104984259@qq.com>
 * @link  http://www.zhangmoxuan.com
 * @QQ  1104984259
 * @Date  2018-9-15
 * @see https://github.com/PHPOffice/PhpSpreadsheet
 */
class ExportModel extends Widget
{
    /**
     * @var ActiveRecord[] 数据模型数组, eg: Post::find()->all().
     */
    public $models;
    /**
     * @var array 从模型中获取的属性列表, 未设置则获取该模型的所有属性.
     */
    public $columns = [];
    /**
     * @var array 第一行的标题栏, 未设置则获取该模型的属性标签.
     * 如果`$columns`子数组设置了`header`, 此属性将对应的忽略.
     * Warning: 第一行有汉字时, 自动计算宽度无效!
     */
    public $headers = [];
    /**
     * @var bool 是否在一个 Excel 中导出多个工作表.
     * 此属性为`true`时, `$models`应该是一个二维数组, 每个子数组表示一个工作表的数据;
     * `$columns`和`$headers`应该是一个二维数组, 每个子数组的键必须是`$models`中子数组的键.
     * `$sheetTitle`应该是一个一维数组, 每个元素的键必须是`$data`中子数组的键.
     */

    public $mergeCells = [];
     
    public $isMultipleSheet = false;
    /**
     * @var Formatter|null
     */
    public $formatter;


    public function run()
    {
        if(empty($this->models)){
            throw new InvalidConfigException('Config models must be set.');
        }
        return self::exportModel();
    }

    /**
     * 导出操作
     * @return bool|string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \yii\base\Exception
     */
    private function exportModel()
    {
        $spreadsheet = new Spreadsheet();
        if(!empty($this->properties)){
            self::setProperties($spreadsheet, $this->properties);  // 设置 Excel 文件的属性
        }

        if($this->isMultipleSheet){  // 导出多个工作表
            $sheetIndex = 0;
            foreach($this->models as $key => $models){
                $worksheet = $sheetIndex >= 1 ? $spreadsheet->createSheet($sheetIndex) : $spreadsheet->getActiveSheet();
                if(is_string($sheetTitle = ArrayHelper::getValue($this->sheetTitle, $key)) && $sheetTitle !== ''){
                    $worksheet->setTitle($sheetTitle);  // 设置工作表的标题
                }
                $columns = ArrayHelper::getValue($this->columns, $key, []);
                $headers = ArrayHelper::getValue($this->headers, $key, []);
                self::executeColumns($worksheet, $models, self::populateColumns($columns), $headers);  // 遍历设置行数据
                $sheetIndex++;
            }
        }else{
            $worksheet = $spreadsheet->getActiveSheet();  // 获取活动的表
            if(is_string($this->sheetTitle) && $this->sheetTitle !== ''){
                $worksheet->setTitle($this->sheetTitle);  // 设置工作表的标题
            }
            self::executeColumns($worksheet, $this->models, self::populateColumns($this->columns), $this->headers);  // 遍历设置行数据
        }

        if($this->mergeCells){
            foreach ($this->mergeCells as $key => $value) {
                $worksheet->mergeCells($value);
            }   
        }

        $objectWriter = IOFactory::createWriter($spreadsheet, $this->format);
        if($this->asAttachment){  // 直接下载
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $this->fileName .'"');
            header('Cache-Control: max-age=0');
            $objectWriter->save('php://output');
            exit();
        }elseif(FileHelper::createDirectory($this->savePath)){  // 保存到服务器, 首先目录存在并可读写
            $path = $this->savePath . $this->fileName;
            $objectWriter->save($path);
            return $path;
        }else{
            return false;
        }
    }

    /**
     * 从模型中获取数据
     * @param Worksheet $activeSheet
     * @param ActiveRecord[] $models
     * @param array $columns
     * @param array $headers
     */
    private function executeColumns(&$activeSheet, $models, $columns = [], $headers = [])
    {
        $hasHeader = false;  // 是否设置标题行
        $row = 1;  // 工作表的行数
        $char = 26;  // 工作表的列数
        foreach($models as $model){
            if(empty($columns)){
                $columns = $model->attributes();  // 获取模型的所有属性
            }
            if($this->setFirstTitle && !$hasHeader){  // 设置标题行
                $isPlus = false;
                $colPlus = 0;
                $colNum = 1;
                foreach($columns as $column){
                    $col = '';
                    if($colNum > $char){
                        $colPlus += 1;
                        $colNum = 1;
                        $isPlus = true;
                    }
                    if($isPlus){
                        $col .= chr(64 + $colPlus);
                    }
                    $col .= chr(64 + $colNum);
                    if($row === 1){
                        $activeSheet->getColumnDimension($col)->setAutoSize(true);  // 设置自适应宽度
                    }
                    $header = '';
                    if(is_array($column)){
                        if(isset($column['header'])){
                            $header = $column['header'];
                        }elseif(isset($column['attribute'])){
                            $header = ArrayHelper::getValue($headers, $column['attribute'], $model->getAttributeLabel($column['attribute']));
                        }
                    }else{
                        $header = ArrayHelper::getValue($headers, $column, $model->getAttributeLabel($column));
                    }
                    $activeSheet->setCellValue($col . $row, $header);  // 设置单元格的值
                    $colNum++;
                }
                $hasHeader = true;
                $row++;
            }

            $isPlus = false;
            $colPlus = 0;
            $colNum = 1;
            foreach($columns as $column){
                $col = '';
                if($colNum > $char){
                    $colPlus += 1;
                    $colNum = 1;
                    $isPlus = true;
                }
                if($isPlus){
                    $col .= chr(64 + $colPlus);
                }
                $col .= chr(64 + $colNum);
                if($row === 1){
                    $activeSheet->getColumnDimension($col)->setAutoSize(true);  // 设置自适应宽度
                }
                if(is_array($column)){
                    $value = self::executeGetColumnData($model, $column);
                }else{
                    $value = self::executeGetColumnData($model, ['attribute' => $column]);
                }
                //$activeSheet->setCellValue($col . $row, $value);  // 设置单元格的值
                $activeSheet->setCellValueExplicit($col . $row, $value, DataType::TYPE_STRING2);  // 设置单元格的值, 防止长数字变成科学计数法
                $colNum++;
            }
            $row++;
        }
    }

    /**
     * 获取每一列的值.
     * @param ActiveRecord $model
     * @param array $params
     * @return mixed|null|string
     */
    private function executeGetColumnData($model, $params)
    {
        $columnValue = null;
        if(($value = ArrayHelper::getValue($params, 'value')) !== null){
            if(is_string($value)){
                $columnValue = ArrayHelper::getValue($model, $value);
            }else{
                $columnValue = call_user_func($value, $model, $this);
            }
        }elseif(($attribute = ArrayHelper::getValue($params, 'attribute')) !== null){
            $columnValue = ArrayHelper::getValue($model, $attribute);
        }
        if(($format = ArrayHelper::getValue($params, 'format')) !== null){
            $columnValue = self::formatter()->format($columnValue, $format);
        }
        return $columnValue;
    }

    /**
     * 检查列是字符串还是数组.
     * 如果是字符串, 这将检查是否有格式化程序(formatter)或标题.
     * @param array $columns
     * @return array
     */
    private function populateColumns($columns = [])
    {
        $_columns = [];
        foreach($columns as $key => $value){
            if(is_string($value)){
                $value_log = explode(':', $value);
                $_columns[$key] = ['attribute' => $value_log[0]];
                if(isset($value_log[1]) && $value_log[1] !== null){
                    $_columns[$key]['format'] = $value_log[1];
                }
                if(isset($value_log[2]) && $value_log[2] !== null){
                    $_columns[$key]['header'] = $value_log[2];
                }
            }else{
                if(!isset($value['attribute']) && !isset($value['value'])){
                    throw new InvalidParamException('Attribute or Value must be defined.');
                }else{
                    $_columns[$key] = $value;
                }
            }
        }
        return $_columns;
    }

    /**
     * Formatter for i18n.
     * @return null|Formatter
     */
    private function formatter()
    {
        if(!isset($this->formatter)){
            $this->formatter = Yii::$app->formatter;
        }
        return $this->formatter;
    }
}
