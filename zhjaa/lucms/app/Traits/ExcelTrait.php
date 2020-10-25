<?php

namespace App\Traits;

use App\Models\AdvertisementPosition;
use Excel;
use DB;

trait ExcelTrait
{
    public function excelAdvertisementPosition($data)
    {
        $cellData = [];
        $data->each(function ($item) use (&$cellData) {
            $cellData[$item->id] = [
                $item->id, $item->name, $item->description, $item->type, $item->created_at, $item->updated_at
            ];
        });
        Excel::create('广告位', function ($excel) use ($cellData) {
            $excel->setTitle('广告位数据导出');

            $excel->sheet('score', function ($sheet) use ($cellData) {
                $sheet->setStyle([
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 8,
                    ]
                ]);
                $sheet->setAllBorders('thin');
                $sheet->setHeight([
                    1 => 50,
                    2 => 30,
                    3 => 30,
                    4 => 40,
                ]);
                $sheet->cells('A1:Y1', function ($cells) {
                    $cells->setBackground('#8eb4e3');
                    $cells->setFont([
                        'bold' => true,
                        'size' => 11,
                    ]);
#
                });
                $sheet->cells('A2:Y2', function ($cells) {
                    $cells->setBackground('#8eb4e3');
                    $cells->setFont([
                        'bold' => true,
                        'size' => 11,
                    ]);
                });
                $sheet->cells('A3:Y3', function ($cells) {
                    $cells->setBackground('#8eb4e3');
                    $cells->setFont([
                        'bold' => true,
                        'size' => 11,
                    ]);
                });
                $sheet->cells('A4:Y4', function ($cells) {
                    $cells->setBackground('#8eb4e3');
                    $cells->setFont([
                        'bold' => true,
                        'size' => 11,
                    ]);
                });
                $sheet->mergeCells('A1:Y1');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('报单数据底稿明细');
                    $cell->setFont(array(
                        'family' => 'Calibri',
                        'size' => '16',
                        'bold' => true,
                    ));

                });

                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('报单数据底稿明细');
                    $cell->setFont([
                        'family' => 'Calibri',
                        'size' => '16',
                        'bold' => true,
                    ]);
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->setMergeColumn([
                    'columns' => ['A', 'B', 'Y'],
                    'rows' => [
                        [2, 4],
                        [2, 4],
                        [2, 4],
                    ]
                ]);
                $sheet->cell('A2', function ($cell) {
                    $cell->setValue('序号');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->cell('B2', function ($cell) {
                    $cell->setValue('日期');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });

                $sheet->mergeCells('C2:M2');
                $sheet->cell('C2', function ($cell) {
                    $cell->setValue('购货方明细');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');

                });
                $sheet->mergeCells('N2:V2');
                $sheet->cell('N2', function ($cell) {
                    $cell->setValue('推荐人明细');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');

                });
                $sheet->mergeCells('W2:X2');
                $sheet->cell('W2', function ($cell) {
                    $cell->setValue('履约保证金');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });
                $sheet->setWidth('Y', 25);
                $sheet->cell('Y2', function ($cell) {
                    $cell->setValue('备注');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });

                $sheet->mergeCells('C3:G3');
                $sheet->cell('C3', function ($cell) {
                    $cell->setValue('基础资料');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });
                $sheet->mergeCells('H3:M3');
                $sheet->cell('H3', function ($cell) {
                    $cell->setValue('缴款明细');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });
                $sheet->mergeCells('N3:Q3');
                $sheet->cell('N3', function ($cell) {
                    $cell->setValue('基础资料');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });
                $sheet->mergeCells('R3:V3');
                $sheet->cell('R3', function ($cell) {
                    $cell->setValue('收款明细');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });


                $sheet->setMergeColumn([
                    'columns' => ['W', 'X'],
                    'rows' => [
                        [3, 4],
                        [3, 4],
                    ]
                ]);
                $sheet->cell('W3', function ($cell) {
                    $cell->setValue('金额（元）');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });
                $sheet->setWidth('X', 25);
                $sheet->cell('X3', function ($cell) {
                    $cell->setValue('是否已转存专用账户');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });
                $sheet->setWidth('C', 25);
                $sheet->cell('C4', function ($cell) {
                    $cell->setValue('姓名');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('D', 8);
                $sheet->cell('D4', function ($cell) {
                    $cell->setValue('性别');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('E', 20);
                $sheet->cell('E4', function ($cell) {
                    $cell->setValue('身份证号码');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('F', 14);
                $sheet->cell('F4', function ($cell) {
                    $cell->setValue('联系电话');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('G', 18);
                $sheet->cell('G4', function ($cell) {
                    $cell->setValue('合同编号');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('H', 10);
                $sheet->cell('H4', function ($cell) {
                    $cell->setValue('缴款方式');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('I', 20);
                $sheet->cell('I4', function ($cell) {
                    $cell->setValue('缴款账号');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('J', 13);
                $sheet->cell('J4', function ($cell) {
                    $cell->setValue('缴款金额');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('K', 13);
                $sheet->cell('K4', function ($cell) {
                    $cell->setValue('是否已入账');
                });
                $sheet->setWidth('L', 14);
                $sheet->cell('L4', function ($cell) {
                    $cell->setValue('交易回单号');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });
                $sheet->setWidth('M', 14);
                $sheet->cell('M4', function ($cell) {
                    $cell->setValue('交易回单号');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });
                $sheet->setWidth('N', 25);
                $sheet->cell('N4', function ($cell) {
                    $cell->setValue('姓名');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('O', 8);
                $sheet->cell('O4', function ($cell) {
                    $cell->setValue('性别');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('P', 20);
                $sheet->cell('P4', function ($cell) {
                    $cell->setValue('身份证号码');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('Q', 16);
                $sheet->cell('Q4', function ($cell) {
                    $cell->setValue('联系电话');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('R', 20);
                $sheet->cell('R4', function ($cell) {
                    $cell->setValue('收款账号');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('S', 14);
                $sheet->cell('S4', function ($cell) {
                    $cell->setValue('开启行信息');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('T', 15);
                $sheet->cell('T4', function ($cell) {
                    $cell->setValue('业务推广费金额（元）');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#E22222');
                });
                $sheet->setWidth('U', 14);
                $sheet->cell('U4', function ($cell) {
                    $cell->setValue('奖励是否已发放');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });
                $sheet->setWidth('V', 14);
                $sheet->cell('V4', function ($cell) {
                    $cell->setValue('交易流水号');
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });
                $sheet->rows($cellData);
            });


        })->export('xls');
    }


    public function importExcelAdvertisementPositionExcute($file)
    {
        $m_advertisementPosition = new AdvertisementPosition();
        $date = date('Y-m-d H:i:s');
        Excel::load($file, function ($reader) use ($m_advertisementPosition, $date) {
            $array = $reader->select([
                'name', 'type', 'description'
            ])->toArray();

            $new_array = collect($array)->chunk(400);

            DB::beginTransaction();
            try {
                foreach ($new_array as $v) {
                    $m_advertisementPosition->insert($v->toArray());
                }
                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                return false;
            }
        });
        return true;
    }
}
