<?php

namespace app\core;

use yii\base\Action;

class ExcelTemplateAction extends Action
{
    public $title = '数据导入Excel模板';

    public $cells = [];

    public function run()
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator(\Yii::$app->name)
            ->setLastModifiedBy(\Yii::$app->name)
            ->setTitle($this->title)
            ->setSubject($this->title)
            ->setDescription($this->title);
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        foreach($this->cells as $corp => $data) {
            $sheet->setCellValue($corp,$data['val']);
            if(isset($data['comment'])) {
                $sheet->getComment($corp)->getText()->createTextRun($data['comment']);
            }
        }

        $filename = $this->title . '.xlsx';
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename=' . $filename . '');
        header("Content-Transfer-Encoding:binary");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}
