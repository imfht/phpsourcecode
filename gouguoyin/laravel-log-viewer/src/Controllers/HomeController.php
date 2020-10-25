<?php

namespace Gouguoyin\LogViewer\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function home(Request $request)
    {
        if ($request->has('file')) {
            $this->service->setLogPath($request->input('file'));
            $viewName = 'detail';
        } else {
            $viewName = 'home';
        }

        return view($this->packageName .'::'. $viewName, [
            'service'  => $this->service,
            'keywords' => $request->input('keywords'),
        ]);
    }

    /**
     * 文件下载
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Request $request)
    {
        $this->service->setLogPath($request->input('file'));
        return response()->download($this->service->getLogPath());
    }

    /**
     * 删除文件
     * @param Request $request
     * @return array
     */
    public function delete(Request $request)
    {
        $this->service->setLogPath($request->input('file'));
        if (File::delete($this->service->getLogPath())) {
            return ['status' => 'success', 'message' => trans($this->packageName . '::log-viewer.delete.success_message'), 'redirect' => route('home')];
        }
        return ['status' => 'fail', 'message' => trans($this->packageName . '::log-viewer.delete.success_fail')];
    }

}
