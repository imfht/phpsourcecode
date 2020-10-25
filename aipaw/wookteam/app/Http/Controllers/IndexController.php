<?php

namespace App\Http\Controllers;

use App\Module\Base;
use Redirect;


/**
 * 页面
 * Class IndexController
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{

    public function __invoke($method, $action = '', $child = '')
    {
        $app = $method ? $method : 'main';
        if ($action) {
            $app .= "__" . $action;
        }
        if (!method_exists($this, $app)) {
            $app = 'main';
        }
        return $this->$app($child);
    }

    /**
     * 首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function main()
    {
        return view('main', ['version' => Base::getVersion()]);
    }

    /**
     * 接口文档
     * @return \Illuminate\Http\RedirectResponse
     */
    public function api()
    {
        return Redirect::to(Base::fillUrl('docs'), 301);
    }
}
