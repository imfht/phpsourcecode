<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $request;
    public $response;

    public function __construct(Request $request)
    {
        $this->request  = $request;
        $this->response = response();
    }

    /**
     * 判断是否是POST请求
     * @return bool
     */
    public function isPost()
    {
        return strcasecmp($this->request->getMethod(), 'post') === 0;
    }

    /**
     * 判断是否是get请求
     * @return bool
     */
    public function isGet()
    {
        return strcasecmp($this->request->getMethod(), 'get') === 0;
    }

    /**
     * 输出 Json 信息
     * @param $status
     * @param null $data
     * @param null $message
     * @return $this|Response
     */
    public function setJson($status, $message = null, $data = null)
    {
        $message = empty($message) ? config('errors.' . $status, '') : $message;
        $content = ['status' => $status, 'msg' => $message];
        if (empty($data) === false) {
            $content['data'] = $data;
        }
        $content['time']   = time();
        $this->response = $this->response->json($content)
            ->header('Pragma', 'no-cache')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        return $this->response;
    }

    /**
     * 自定义
     * @param $content
     * @return $this|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function setContentJson($content)
    {
        $this->response = $this->response->json($content)
            ->header('Pragma', 'no-cache')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        return $this->response;
    }
}
