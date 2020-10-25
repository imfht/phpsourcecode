<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends BaseController
{
    /**
     * 品牌列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        list($page, $limit, $offset) = get_page_params();
        $where = [
            'status' => Brand::STATUS_ON
        ];
        $res_list = Brand::select('id', 'title', 'image')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $total = Brand::where($where)->count();
        $return = [
            'lists' => $res_list,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 品牌详情
     * @param Request $request
     * @return array
     */
    public function detail(Request $request)
    {
        $id = (int)$request->id;
        $data = array();
        if ($id) {
            $data = Brand::select('id', 'title', 'image', 'content')->where('id', $id)->first();
        }
        return $this->success($data);
    }
}
