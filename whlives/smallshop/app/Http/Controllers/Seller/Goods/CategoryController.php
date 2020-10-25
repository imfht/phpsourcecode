<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/16
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\Seller\Goods;

use App\Http\Controllers\Seller\BaseController;
use App\Models\Category;
use Illuminate\Http\Request;
use Validator;

/**
 * 分类管理
 * Class MenuController
 * @package App\Http\Controllers\Admin\System
 */
class CategoryController extends BaseController
{

    /**
     * 获取包含下级的下拉列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function selectAll(Request $request)
    {
        $seller_id = $this->getUserId();
        $data = Category::getSelect(0, true);
        return $this->success($data);
    }

    /**
     * 获取下拉列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function select(Request $request)
    {
        $parent_id = $request->input('parent_id', 0);
        $data = Category::getSelect($parent_id);
        return $this->success($data);
    }
}