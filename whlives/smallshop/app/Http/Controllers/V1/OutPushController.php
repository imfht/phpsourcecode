<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1;

use App\Models\DeliveryTraces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OutPushController extends BaseController
{

    /**
     * 快递鸟推送
     * @param Request $request
     * @return array
     */
    public function kdniao(Request $request)
    {
        $kdniao_id = config('other.kdniao.id');
        $return = array(
            "EBusinessID" => $kdniao_id,
            "UpdateTime" => get_date(),
            "Success" => true,
            "Reason" => ""
        );

        $data = $request->input('RequestData');
        Log::channel('kdniao_push')->info(json_encode($data, JSON_UNESCAPED_UNICODE));
        $data = json_decode($data, true);
        if (isset($data['EBusinessID']) && $data['EBusinessID'] == $kdniao_id) {
            if (isset($data['Data']) && $data['Data']) {
                foreach ($data['Data'] as $value) {
                    if ($value['Success'] === true) {
                        DeliveryTraces::where(['company_code' => $value['ShipperCode'], 'code' => $value['LogisticCode']])->delete();
                        if ($value['Traces']) {
                            $traces = array();
                            foreach ($value['Traces'] as $detail) {
                                $traces[] = array(
                                    'company_code' => $value['ShipperCode'],
                                    'code' => $value['LogisticCode'],
                                    'accept_time' => $detail['AcceptTime'],
                                    'info' => $detail['AcceptStation'],
                                    'status' => $value['State'],
                                    'created_at' => get_date(),
                                    'updated_at' => get_date()
                                );
                            }
                            if ($traces) {
                                DeliveryTraces::insert($traces);
                            }
                        }
                    }
                }
            } else {
                $return['Success'] = false;
            }
        } else {
            $return['Success'] = false;
        }
        return response()->json($return);
    }
}
