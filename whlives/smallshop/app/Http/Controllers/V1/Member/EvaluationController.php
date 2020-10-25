<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1\Member;

use App\Http\Controllers\V1\BaseController;
use App\Models\Evaluation;
use App\Models\EvaluationImage;
use App\Models\Goods;
use App\Services\GoodsService;
use Illuminate\Http\Request;

class EvaluationController extends BaseController
{
    /**
     * 我的评价
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        $m_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        $where = [
            'm_id' => $m_id,
            'status' => Evaluation::STATUS_ON
        ];
        $res_list = Evaluation::select('id', 'goods_id', 'spec_value', 'level', 'content', 'is_image', 'created_at as create_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }

        $goods_ids = array();
        $comment_ids = array();
        foreach ($res_list as $value) {
            $goods_ids[] = $value['goods_id'];
            if ($value['is_image'] == Evaluation::IS_IMAGE_TRUE) {
                $comment_ids[] = $value['id'];
            }
        }

        $goods_res = Goods::select('id', 'title', 'image')->whereIn('id', array_unique($goods_ids))->get();
        if (!$goods_res->isEmpty()) {
            $goods_res = array_column($goods_res->toArray(), null, 'id');
        }
        $image_list = array();
        $image_res = EvaluationImage::select('e_id', 'image')->whereIn('id', array_unique($comment_ids))->get();
        if (!$image_res->isEmpty()) {
            foreach ($image_res as $value) {
                $image_list[$value['e_id']][] = $value['image'];
            }
        }

        $data_list = array();
        foreach ($res_list as $value) {
            $_item = array(
                'id' => $value['id'],
                'spec_value' => GoodsService::formatSpecValue($value['spec_value']),
                'level' => $value['level'],
                'content' => $value['content'],
                'create_at' => $value['create_at'],
                'goods' => isset($goods_res[$value['goods_id']]) ? $goods_res[$value['goods_id']] : [],
                'image' => isset($image_list[$value['id']]) ? $image_list[$value['id']] : [],
            );
            $data_list[] = $_item;
        }
        $total = Evaluation::where($where)->count();
        $return = [
            'lists' => $data_list,
            'total' => $total,
        ];
        return $this->success($return);
    }
}
