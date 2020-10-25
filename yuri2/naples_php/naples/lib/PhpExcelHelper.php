<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2017/2/24
 * Time: 13:54
 */

namespace naples\lib;

use naples\lib\base\Service;

/**
 * PHPExcel的辅助类
 * 傻瓜化使用常用功能
 */
class PhpExcelHelper extends Service
{
    private $excel=false;//excel对象存储
    private $writer=false;//writer对象存储

    public function __construct()
    {

    }

    /**
     * 获得excel对象，如果为空，生成一个空对象
     * @return \PHPExcel
     */
    public function getExcelObj(){
        if (!$this->excel){
            $this->excel=Factory::getPHPExcel();
        }
        return $this->excel;
    }

    /**
     * 设置excel对象，覆盖原有的
     * @param $excelObj \PHPExcel
     * @return PhpExcelHelper
     */
    public function setExcelObj($excelObj){
        $this->excel=$excelObj;
        return $this;
    }

    /**
     * 从本地excel文件生成对象
     * @param $fromFile string
     * @return PhpExcelHelper
     */
    public function loadFromFile($fromFile){
        $this->excel= \PHPExcel_IOFactory::load($fromFile);
        return $this;
    }

    /**
     * excel转换为数组
     * @return array
     */
    public function ObjToArray(){
        $objPHPExcel = $this->getExcelObj();
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }

    /**
     * 获得writer对象，如果为空，生成一个空对象
     * @return \PHPExcel_Writer_IWriter
     */
    public function getExcelWriter(){
        if (!$this->writer){
            $this->writer=Factory::getPHPExcelWriter($this->getExcelObj());
        }
        return $this->writer;
    }

    /**
     * 设置excelWriter对象，覆盖原有的
     * @param $excelWriterObj \PHPExcel_Writer_IWriter
     * @return PhpExcelHelper
     */
    public function setExcelWriterObj($excelWriterObj){
        $this->writer=$excelWriterObj;
        return $this;
    }

    /**
     * 保存为excel文件，默认2007格式
     * @param $fileFullPath string
     * @return string filePathReal
     */
    public function saveFile($fileFullPath){
        $ext=\Yuri2::getExtension($fileFullPath);
        if ($ext==''){
            $fileFullPath.='.xlsx';
        }
        $this->getExcelWriter()->save($fileFullPath);
        return $fileFullPath;
    }

    /**
     * 直接下载为excel文件
     * @param $filename string 文件名
     * @return PhpExcelHelper
     */
    public function downloadFile($filename='export.xlsx'){
        $ext=\Yuri2::getExtension($filename);
        if ($ext==''){
            $filename.='.xlsx';
        }
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = $this->getExcelWriter();
        $objWriter->save('php://output');
        config('show_debug_btn',false);//避免其他html内容污染
        return $this;
    }

}