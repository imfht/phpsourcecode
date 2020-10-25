<?php

namespace app\core;

use yii\web\UploadedFile;
use yii\base\Model;

class ExcelImportAction extends BaseAction
{
    public $formName;

    public $cellMap = [];

    public function run()
    {
        if (\Yii::$app->request->isPost) {
            if ($file = UploadedFile::getInstanceByName('file')) {

                $objExcel = \PHPExcel_IOFactory::load($file->tempName);
                $sheet = $objExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow(); // 取得总行数
                $highestColumn = $sheet->getHighestColumn(); // 取得总列数
                $rows = [];
                //将表格里面的数据循环到数组中
                for ($i = 2; $i <= $highestRow; $i++) {
                    //*为什么$i=2? (因为Excel表格第一行是标题)
                    $row = [];
                    foreach ($this->cellMap as $col => $field) {
                        $row[$field] = $objExcel->getActiveSheet()->getCell($col . $i)->getValue();
                    }
                    if (empty($row)) {
                        continue;
                    }
                    $rows[] = $row;
                }

                $count = count($rows);
                $models = [];
                for ($i = 0; $i < $count; $i++) {
                    $model = \Yii::createObject($this->modelClass);
                    // 动态绑定行为
                    $model->attachBehaviors($this->modelBehaviors);
                    $models[] = $model;
                }

                if (($loaded = Model::loadMultiple($models, [$this->formName=>$rows]))
                && Model::validateMultiple($models)) {
                    \Yii::$app->db->transaction(function ($db) use ($models) {
                        foreach ($models as $model) {
                            $model->save(false);
                        }
                    });
                    return $this->controller->redirectOnSuccess(\Yii::$app->request->referrer, $this->successMsg);
                }


                if ($loaded === false) {
                    return $this->controller->redirectOnSuccess(\Yii::$app->request->referrer, '可能表达的字段跟服务端不一致');
                }
                return $this->controller->redirectOnSuccess(\Yii::$app->request->referrer, '数据不正确');
            }
        }

        return $this->controller->render("@app/views/excel-import");
    }
}
