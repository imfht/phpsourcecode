<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-26 08:15:11
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-26 16:58:16
 */

namespace common\helpers\phpexcel;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * 基于 PhpSpreadsheet, 用于将数据导出为 Excel 表格.
 *
 * @author  zhangmoxuan <1104984259@qq.com>
 * @link  http://www.zhangmoxuan.com
 * @QQ  1104984259
 * @Date  2018-9-15
 * @see https://github.com/PHPOffice/PhpSpreadsheet
 */
class ExportData extends Widget
{
    /**
     * @var array 二维数组, 每一个子数组表示一行.
     */
    public $data = [];

    public $mergeCells = [];

    public function run()
    {
        if(empty($this->data)){
            throw new InvalidConfigException('Config data must be set.');
        }
        return self::exportData();
    }

    /**
     * 导出操作
     * @return bool|string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \yii\base\Exception
     */
    private function exportData()
    {
        $spreadsheet = new Spreadsheet();
        if(!empty($this->properties)){
            self::setProperties($spreadsheet, $this->properties);  // 设置 Excel 文件的属性
        }

        if($this->isMultipleSheet){  // 导出多个工作表
            $sheetIndex = 0;
            foreach($this->data as $key => $datum){
                $worksheet = $sheetIndex >= 1 ? $spreadsheet->createSheet($sheetIndex) : $spreadsheet->getActiveSheet();
                if(is_string($sheetTitle = ArrayHelper::getValue($this->sheetTitle, $key)) && $sheetTitle !== ''){
                    $worksheet->setTitle($sheetTitle);  // 设置工作表的标题
                }
                self::executeColumns($worksheet, $datum, ArrayHelper::getValue($this->headers, $key, []));  // 遍历设置行数据
                $sheetIndex++;
            }
        }else{
            $worksheet = $spreadsheet->getActiveSheet();  // 获取活动的表
            
            if(is_string($this->sheetTitle) && $this->sheetTitle !== ''){
                $worksheet->setTitle($this->sheetTitle);  // 设置工作表的标题
            }
            self::executeColumns($worksheet, $this->data, $this->headers);  // 遍历设置行数据
        }

        $worksheet->setMergeCells($this->mergeCells);
        // if($this->mergeCells){
        //     foreach ($this->mergeCells as $key => $value) {
              
        //     }   
        // }

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
     * 遍历设置行数据
     * @param Worksheet $activeSheet
     * @param array $data
     * @param array $headers
     */
    private function executeColumns(&$activeSheet, $data, $headers = [])
    {
        $hasHeader = false;  // 是否设置标题行
        $row = 1;  // 工作表的行数
        $char = 26;  // 工作表的列数
        foreach($data as $datum){
            if($this->setFirstTitle && !$hasHeader){  // 设置标题行
                $isPlus = false;
                $colPlus = 0;
                $colNum = 1;
                foreach($datum as $key => $value){
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
                    $activeSheet->setCellValue($col . $row, ArrayHelper::getValue($headers, $key, $key));  // 设置单元格的值
                    $colNum++;
                }
                $hasHeader = true;
                $row++;
            }

            $isPlus = false;
            $colPlus = 0;
            $colNum = 1;
            foreach($datum as $key => $value){
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
                //$activeSheet->setCellValue($col . $row, $value);  // 设置单元格的值
                $activeSheet->setCellValueExplicit($col . $row, $value, DataType::TYPE_STRING2);  // 设置单元格的值, 防止长数字变成科学计数法
                $colNum++;
            }
            $row++;
        }
    }
}
