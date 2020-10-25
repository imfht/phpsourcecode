<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    /**
     * 商品分类列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        $parent_id = (int)$request->parent_id;
        if (!is_numeric($parent_id)) {
            api_error(__('api.missing_params'));
        }
        $where = [
            'parent_id' => $parent_id,
            'status' => Category::STATUS_ON
        ];
        $res_list = Category::select('id', 'title')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'desc')
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        return $this->success($res_list);
    }
}
