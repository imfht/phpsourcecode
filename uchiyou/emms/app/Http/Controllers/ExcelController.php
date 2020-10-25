<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;


class ExcelController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}
	//Excel文件导出功能 By Laravel学院
	public function export(){
		$cellData = [
				['学号','姓名','成绩'],
				['10001','AAAAA','99'],
				['10002','BBBBB','92'],
				['10003','CCCCC','95'],
				['10004','DDDDD','89'],
				['10005','EEEEE','96'],
		];
		Excel::create('学生成绩',function($excel) use ($cellData){
			$excel->sheet('score', function($sheet) use ($cellData){
				$sheet->rows($cellData);
			});
		})->export('xls');
	}
	public function exportHistory($where,$type){
		
	}
}