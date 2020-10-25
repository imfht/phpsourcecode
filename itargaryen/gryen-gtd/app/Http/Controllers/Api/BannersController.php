<?php

namespace App\Http\Controllers\Api;

use App\Banner;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBannerRequest;

class BannersController extends Controller
{
    public function set(CreateBannerRequest $request)
    {
        // 是否已经设置过了
        $oldBanner = Banner::where('article_id', $request->get('article_id'))->first();

        if (empty($oldBanner)) {
            $msg = Banner::create(array_merge($request->all(), [
                'status' => 1,
                'weight' => Banner::max('weight') + 1,
            ]));
        } else {
            $msg = $oldBanner->update(array_merge($request->all(),
                ['weight' => Banner::max('weight') + 1]
            ));
        }

        return response()->json([
            'code' => 200,
            'msg' => $msg,
        ]);
    }

    public function delete($id)
    {
        Banner::destroy($id);

        return response()->json([
            'code' => 200,
            'msg' => 'success',
        ]);
    }

    public function top($id)
    {
        $returnMsg = [
            'code' => 200,
            'msg' => '设置成功',
        ];
        $banner = Banner::find($id);
        $max = Banner::max('weight');
        if ($max === $banner->weight && $max !== 0) {
            $returnMsg['msg'] = '已经置顶了';
        } else {
            $returnMsg['msg'] = $banner->update(['weight' => Banner::max('weight') + 1]);
        }

        return response()->json($returnMsg);
    }
}
