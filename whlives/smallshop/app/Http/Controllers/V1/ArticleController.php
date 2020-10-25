<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends BaseController
{
    /**
     * 文章列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        $category_id = (int)$request->category_id;
        if (!$category_id) {
            api_error(__('api.missing_params'));
        }
        list($page, $limit, $offset) = get_page_params();
        $where = [
            'category_id' => $category_id,
            'status' => Article::STATUS_ON
        ];
        $res_list = Article::select('id', 'title', 'image', 'created_at as create_at')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $total = Article::where($where)->count();
        $return = [
            'lists' => $res_list,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 文章详情
     * @param Request $request
     * @return array
     */
    public function detail(Request $request)
    {
        $id = (int)$request->id;
        $data = array();
        if ($id) {
            $data = Article::select('id', 'title', 'image', 'created_at as create_at')->where('id', $id)->first();
            if ($data) {
                $data['content'] = $data->content()->value('content');
            }
        }
        return $this->success($data);
    }
}
