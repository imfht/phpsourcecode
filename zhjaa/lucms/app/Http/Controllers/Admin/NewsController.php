<?php

namespace App\Http\Controllers\Admin;

use App\Models\Carousel;
use Illuminate\Http\Request;

class NewsController extends AdminController
{

    public function carousels(Carousel $model)
    {
        $model = $model->orderBy('weight', 'asc');
        return $this->success($model->get());
    }

    public function showCarousels($id, Carousel $model)
    {
        return $this->success($model->findOrFail($id));
    }

    public function storeCarousel(Request $request, Carousel $model)
    {
        $request_data = $request->all();
        if (isset($request_data['cover_image']['attachment_id'])) {
            $request_data['cover_image'] = $request_data['cover_image']['attachment_id'];
        } else {
            return $this->failed('必须上传图片');
        }
        if(!$request_data['url']) $request_data['url'] = '';
        if(!$request_data['description']) $request_data['description'] = '';

        $res = $model->storeAction($request_data);
        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);
    }


    public function updateCarousel(Request $request, Carousel $model, $id)
    {
        $request_data = $request->all();
        if (isset($request_data['cover_image']['attachment_id'])) {
            $request_data['cover_image'] = $request_data['cover_image']['attachment_id'];
        } else {
            return $this->failed('必须上传图片');
        }
        $m_carousel = $model->findOrFail($id);

        if(!$request_data['url']) $request_data['url'] = '';
        if(!$request_data['description']) $request_data['description'] = '';
        $res = $model->updateAction($request_data, $m_carousel);
        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);
    }

    public function destroyCarousel($id, Carousel $model)
    {
        $res = $model->destroyAction($model->findOrFail($id));
        if ($res['status'] === true) return $this->message($res['message']);
        return $this->failed($res['message']);
    }

}
