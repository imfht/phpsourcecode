<?php

namespace App\Plugin;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/24
 * Time: 21:29
 */
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_MemoryDrawing;
use PHPExcel_Writer_Excel2007;


/**
 * phpexcel助手类
 * Class ExcelHelp
 * @package App\Plus
 */
class PHPExcelHelp
{
    /**
     * 导出数据
     * @param $header
     * @param $data
     * @return bool
     */
    public static function export($header, $data)
    {
        if (!$header) {
            dd('数据为空');
        }
        $file_name   = 'report_' . date('Y-m-d') . '.xlsx';
        $objPHPExcel = new PHPExcel();
        // 设置文档信息，这个文档信息windows系统可以右键文件属性查看
        $objPHPExcel->getProperties()->setCreator("作者简庆旺")
            ->setLastModifiedBy("最后更改者")
            ->setTitle("文档标题")
            ->setSubject("文档主题")
            ->setDescription("文档的描述信息")
            ->setKeywords("设置文档关键词")
            ->setCategory("设置文档的分类");
        //设置头
        $char_range = range('A', 'Z');
        foreach ($header as $key => $val) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($char_range[$key] . '1', $val);
        }
        //数据
        if ($data) {
            foreach ($data as $index => $item) {
                $index = $index + 2;
                foreach ($item as $key => $val) {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($char_range[$key] . $index, $val);
                }
            }
        }
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="' . $file_name . '"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 处理导入文件
     * @param $filePath
     * @return array|bool
     */
    public static function import($filePath)
    {
        if (!is_file($filePath)) {
            return false;
        }
        $objPHPExcel = PHPExcel_IOFactory::load($filePath);
        $sheet       = $objPHPExcel->getActiveSheet();
        //获取行数
        $highestRow = $sheet->getHighestRow();
        //获取列数
        $lastChar    = $sheet->getHighestColumn();
        $rangeColumn = range('A', $lastChar);
        //循环输出数据
        $data = array();
        for ($row = 1; $row <= $highestRow; ++$row) {
            foreach ($rangeColumn as $column) {
                $val                 = $sheet->getCell($column . $row)->getValue();
                $data[$row][$column] = trim($val);
            }
        }
        return $data;
    }

    /**
     * 导入带图片的
     * @param $filePath string 文件路径
     * @param $imageSavePath string 图片存储路径
     * @param int $maxRow 上传最大行
     * @return array|bool
     */
    public function importAtImage($filePath, $imageSavePath, $maxRow = 5000)
    {
        if (!is_file($filePath)) {
            return $this->setResult(1, $filePath . 'file not exists');
        }
        $objPHPExcel = PHPExcel_IOFactory::load($filePath);
        $sheet       = $objPHPExcel->getActiveSheet();
        //获取行数
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > $maxRow) {
            $format = 'total %s line,limit %s line';
            return $this->setResult(2, sprintf($format, $highestRow, $maxRow));
        }
        //获取列数
        $lastChar    = $sheet->getHighestColumn();
        $rangeColumn = range('A', $lastChar);
        //循环输出数据
        $data = array();
        for ($row = 1; $row <= $highestRow; ++$row) {
            foreach ($rangeColumn as $column) {
                $val                 = $sheet->getCell($column . $row)->getValue();
                $data[$row][$column] = trim($val);
            }
        }
        $images_list = [];
        if (count($sheet->getDrawingCollection()) > 0) {
            foreach ($sheet->getDrawingCollection() as $drawing) {
                list ($startColumn, $startRow) = PHPExcel_Cell::coordinateFromString($drawing->getCoordinates());//获取列与行号
                $indexStart = $startColumn . '-' . $startRow;
                if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {
                    ob_start();
                    call_user_func(
                        $drawing->getRenderingFunction(),
                        $drawing->getImageResource()
                    );
                    $imageContents = ob_get_contents();
                    ob_end_clean();
                    switch ($drawing->getMimeType()) {
                        case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG :
                            $extension = 'png';
                            break;
                        case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_GIF:
                            $extension = 'gif';
                            break;
                        case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG :
                            $extension = 'jpg';
                            break;
                    }
                } else {
                    $zipReader     = fopen($drawing->getPath(), 'r');
                    $imageContents = '';
                    while (!feof($zipReader)) {
                        $imageContents .= fread($zipReader, 1024);
                    }
                    fclose($zipReader);
                    $extension = $drawing->getExtension();
                }
                $myFileName                           = 'Excel_Image_' . $indexStart . '.' . $extension;
                $images_list[$startRow][$startColumn] = $myFileName;
                file_put_contents($imageSavePath . $myFileName, $imageContents);
            }
        }
        foreach ($data as $rowIndex => $row) {
            foreach ($row as $columnIndex => $value) {
                if (isset($images_list[$rowIndex][$columnIndex])) {
                    $data[$rowIndex][$columnIndex] = $images_list[$rowIndex][$columnIndex];
                }
            }
        }
        return $this->setResult(0, 'ok', $data);
    }

    /**
     * 返回结果wys
     * @param $status
     * @param string $msg
     * @param null $data
     * @return array
     */
    private function setResult($status, $msg = '', $data = null)
    {
        return [
            'status' => $status,
            'msg'    => $msg,
            'data'   => $data
        ];
    }
}