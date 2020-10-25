<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1\Member;

use App\Http\Controllers\V1\BaseController;
use App\Models\Article;
use App\Models\Favorite;
use App\Models\Goods;
use App\Models\Seller;
use Illuminate\Http\Request;

class FavoriteController extends BaseController
{
    /**
     * 商品收藏
     * @param Request $request
     */
    public function goods(Request $request)
    {
        $m_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        $where = [
            'm_id' => $m_id,
            'type' => Favorite::TYPE_GOODS
        ];
        $res_list = Favorite::where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->pluck('object_id');
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $object_res = Goods::select('id', 'title', 'image')->whereIn('id', $res_list->toArray())->get();
        if ($object_res->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $object_res = array_column($object_res->toArray(), null, 'id');
        $data_list = array();
        foreach ($res_list as $value) {
            if (isset($object_res[$value])) {
                $data_list[] = $object_res[$value];
            }
        }
        $total = Favorite::where($where)->count();
        $return = [
            'lists' => $data_list,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 商家收藏
     * @param Request $request
     */
    public function seller(Request $request)
    {
        $m_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        $where = [
            'm_id' => $m_id,
            'type' => Favorite::TYPE_SELLER
        ];
        $res_list = Favorite::where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->pluck('object_id');
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $object_res = Seller::select('id', 'title', 'image')->whereIn('id', $res_list->toArray())->get();
        if ($object_res->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $object_res = array_column($object_res->toArray(), null, 'id');
        $data_list = array();
        foreach ($res_list as $value) {
            if (isset($object_res[$value])) {
                $data_list[] = $object_res[$value];
            }
        }
        $total = Favorite::where($where)->count();
        $return = [
            'lists' => $data_list,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 文章收藏
     * @param Request $request
     */
    public function article(Request $request)
    {
        $m_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        $where = [
            'm_id' => $m_id,
            'type' => Favorite::TYPE_ARTICLE
        ];
        $res_list = Favorite::where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->pluck('object_id');
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $object_res = Article::select('id', 'title', 'image')->whereIn('id', $res_list->toArray())->get();
        if ($object_res->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $object_res = array_column($object_res->toArray(), null, 'id');
        $data_list = array();
        foreach ($res_list as $value) {
            if (isset($object_res[$value])) {
                $data_list[] = $object_res[$value];
            }
        }
        $total = Favorite::where($where)->count();
        $return = [
            'lists' => $data_list,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 添加/取消收藏
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function set(Request $request)
    {
        $id = (int)$request->post('id');
        $type = (int)$request->post('type', 1);
        if (!$id || !$type) {
            api_error(__('api.missing_params'));
        }

        $data = array(
            'm_id' => $this->getUserId(),
            'object_id' => $id,
            'type' => $type
        );

        if (Favorite::where($data)->exists()) {
            //已经存在就取消
            $res = Favorite::where($data)->delete();
            $action = 'del';
        } else {
            $res = Favorite::create($data);
            $action = 'add';
        }
        if ($res) {
            return $this->success(['action' => $action]);
        } else {
            api_error(__('api.fail'));
        }
    }
}
