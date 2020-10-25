<?php

namespace App\Http\Controllers\Api;

use App\Handlers\FileuploadHandler;
use App\Models\AdvertisementPosition;
use App\Traits\ExcelTrait;
use Illuminate\Http\Request;
use Auth;

class ExcelController extends ApiController
{
    use ExcelTrait;

    public function __construct()
    {
        $this->middleware('auth:api')->only(['importExcelAdvertisementPosition']);
    }

    public function exportAdvertisementPosition(Request $request, AdvertisementPosition $advertisementPosition)
    {

        $search_data = json_decode($request->get('search_data'), true);
        $name = isset_and_not_empty($search_data, 'name');
        if ($name) {
            $advertisementPosition = $advertisementPosition->columnLike('name', $name);
        }
        $type = isset_and_not_empty($search_data, 'type');
        if ($type) {
            $advertisementPosition = $advertisementPosition->typeSearch($type);
        }
        $order_by = isset_and_not_empty($search_data, 'order_by');
        if ($order_by) {
            $order_by = explode(',', $order_by);
            $advertisementPosition = $advertisementPosition->orderBy($order_by[0], $order_by[1]);
        }
        $this->excelAdvertisementPosition($advertisementPosition->get());

    }


    public function importExcelAdvertisementPosition(Request $request, FileuploadHandler $fileuploadHandler)
    {
        $file = $request->file('file');
        $rest_upload_file = $fileuploadHandler->uploadfile($file, Auth::id());
        $file = $rest_upload_file['data']['storage_path'] . '/' . $rest_upload_file['data']['storage_name'];

//        $file = '/srv/wwwroot/one_plus_one/bdxt/storage/app/public/files/e5ecc5e19bf9c3183ea2bebf4c9d2aea71634.xlsx';
        if ($rest_upload_file['status'] === true) {
            $import_rest = $this->importExcelAdvertisementPositionExcute($file);
            if ($import_rest) {
                return $this->success($rest_upload_file['data']);
            }
            return $this->failed('出错了');
        } else {
            return $this->failed($rest_upload_file['message']);
        }


    }
}
