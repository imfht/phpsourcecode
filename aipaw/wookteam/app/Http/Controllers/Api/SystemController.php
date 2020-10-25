<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Module\Base;
use App\Module\Users;
use Request;

/**
 * @apiDefine system
 *
 * 系统
 */
class SystemController extends Controller
{
    public function __invoke($method, $action = '')
    {
        $app = $method ? $method : 'main';
        if ($action) {
            $app .= "__" . $action;
        }
        return (method_exists($this, $app)) ? $this->$app() : Base::ajaxError("404 not found (" . str_replace("__", "/", $app) . ").");
    }

    /**
     * 获取设置、保存设置
     *
     * @apiParam {String} type
     * - get: 获取（默认）
     * - save: 保存设置（参数：logo、github、reg、callav、autoArchived、archivedDay）
     */
    public function setting()
    {
        $type = trim(Request::input('type'));
        if ($type == 'save') {
            if (env("SYSTEM_SETTING") == 'disabled') {
                return Base::retError('当前环境禁止修改！');
            }
            $user = Users::authE();
            if (Base::isError($user)) {
                return $user;
            } else {
                $user = $user['data'];
            }
            if (Base::isError(Users::identity('admin'))) {
                return Base::retError('权限不足！', [], -1);
            }
            $all = Request::input();
            foreach ($all AS $key => $value) {
                if (!in_array($key, ['logo', 'github', 'reg', 'callav', 'autoArchived', 'archivedDay'])) {
                    unset($all[$key]);
                }
            }
            $all['logo'] = is_array($all['logo']) ? $all['logo'][0]['path'] : $all['logo'];
            $all['archivedDay'] = intval($all['archivedDay']);
            if ($all['autoArchived'] == 'open') {
                if ($all['archivedDay'] <= 0) {
                    return Base::retError(['自动归档时间不可小于%天！', 1]);
                } elseif ($all['archivedDay'] > 100) {
                    return Base::retError(['自动归档时间不可大于%天！', 100]);
                }
            }
            $setting = Base::setting('system', Base::newTrim($all));
        } else {
            $setting = Base::setting('system');
        }
        $setting['logo'] = Base::fillUrl($setting['logo']);
        $setting['enterprise'] = env('ENTERPRISE_SHOW') ? 'show': '';
        return Base::retSuccess('success', $setting ? $setting : json_decode('{}'));
    }

    /**
     * 获取终端详细信息
     */
    public function get__info()
    {
        if (Request::input("key") !== env('APP_KEY')) {
            return [];
        }
        return Base::retSuccess('success', [
            'ip' => Base::getIp(),
            'ip-info' => Base::getIpInfo(Base::getIp()),
            'ip-iscn' => Base::isCnIp(Base::getIp()),
            'header' => Request::header(),
            'token' => Base::getToken(),
            'url' => url('') . Base::getUrl(),
        ]);
    }

    /**
     * 获取IP地址
     */
    public function get__ip() {
        return Base::getIp();
    }

    /**
     * 是否中国IP地址
     */
    public function get__cnip() {
        return Base::isCnIp(Request::input('ip'));
    }

    /**
     * 获取IP地址详细信息
     */
    public function get__ipinfo() {
        return Base::getIpInfo(Request::input("ip"));
    }

    /**
     * 获取websocket地址
     */
    public function get__wsurl() {
        $wsurl = env('LARAVELS_PROXY_URL');
        if (!$wsurl) {
            $wsurl = url('');
            $wsurl = str_replace('https://', 'wss://', $wsurl);
            $wsurl = str_replace('http://', 'ws://', $wsurl);
            $wsurl.= '/ws';
        }
        return Base::retSuccess('success', [
            'wsurl' => $wsurl,
        ]);
    }

    /**
     * 上传图片
     */
    public function imgupload()
    {
        if (Users::token2userid() === 0) {
            return Base::retError('身份失效，等重新登录！');
        }
        $scale = [intval(Request::input('width')), intval(Request::input('height'))];
        if (!$scale[0] && !$scale[1]) {
            $scale = [2160, 4160, -1];
        }
        $path = "uploads/picture/" . Users::token2userid() . "/" . date("Ym") . "/";
        $image64 = trim(Base::getPostValue('image64'));
        $fileName = trim(Base::getPostValue('filename'));
        if ($image64) {
            $data = Base::image64save([
                "image64" => $image64,
                "path" => $path,
                "fileName" => $fileName,
                "scale" => $scale
            ]);
        } else {
            $data = Base::upload([
                "file" => Request::file('image'),
                "type" => 'image',
                "path" => $path,
                "fileName" => $fileName,
                "scale" => $scale
            ]);
        }
        if (Base::isError($data)) {
            return Base::retError($data['msg']);
        } else {
            return Base::retSuccess('success', $data['data']);
        }
    }

    /**
     * 浏览图片空间
     */
    public function imgview()
    {
        if (Users::token2userid() === 0) {
            return Base::retError('身份失效，等重新登录！');
        }
        $publicPath = "uploads/picture/" . Users::token2userid() . "/";
        $dirPath = public_path($publicPath);
        $dirs = $files = [];
        //
        $path = Request::input('path');
        if ($path && is_string($path)) {
            $path = str_replace(array('||', '|'), '/', $path);
            $path = trim($path, '/');
            $path = str_replace('..', '', $path);
            $path = Base::leftDelete($path, $publicPath);
            if ($path) {
                $path = $path . '/';
                $dirPath .= $path;
                //
                $dirs[] = [
                    'type' => 'dir',
                    'title' => '...',
                    'path' => substr(substr($path, 0, -1), 0, strripos(substr($path, 0, -1), '/')),
                    'url' => '',
                    'thumb' => Base::fillUrl('images/other/dir.png'),
                    'inode' => 0,
                ];
            }
        } else {
            $path = '';
        }
        $list = glob($dirPath . '*', GLOB_BRACE);
        foreach ($list as $v) {
            $filename = basename($v);
            $pathTemp = $publicPath . $path . $filename;
            if (is_dir($v)) {
                $dirs[] = [
                    'type' => 'dir',
                    'title' => $filename,
                    'path' => $pathTemp,
                    'url' => Base::fillUrl($pathTemp),
                    'thumb' => Base::fillUrl('images/other/dir.png'),
                    'inode' => fileatime($v),
                ];
            } elseif (substr($filename, -10) != "_thumb.jpg") {
                $array = [
                    'type' => 'file',
                    'title' => $filename,
                    'path' => $pathTemp,
                    'url' => Base::fillUrl($pathTemp),
                    'thumb' => $pathTemp,
                    'inode' => fileatime($v),
                ];
                //
                $extension = pathinfo($dirPath . $filename, PATHINFO_EXTENSION);
                if (in_array($extension, array('gif', 'jpg', 'jpeg', 'png', 'bmp'))) {
                    if (file_exists($dirPath . $filename . '_thumb.jpg')) {
                        $array['thumb'] .= '_thumb.jpg';
                    }
                    $array['thumb'] = Base::fillUrl($array['thumb']);
                    $files[] = $array;
                }
            }
        }
        if ($dirs) {
            $inOrder = [];
            foreach ($dirs as $key => $item) {
                $inOrder[$key] = $item['title'];
            }
            array_multisort($inOrder, SORT_DESC, $dirs);
        }
        if ($files) {
            $inOrder = [];
            foreach ($files as $key => $item) {
                $inOrder[$key] = $item['inode'];
            }
            array_multisort($inOrder, SORT_DESC, $files);
        }
        //
        return Base::retSuccess('success', ['dirs' => $dirs, 'files' => $files]);
    }

    /**
     * 上传文件
     */
    public function fileupload()
    {
        if (Users::token2userid() === 0) {
            return Base::retError('身份失效，等重新登录！');
        }
        $path = "uploads/files/" . Users::token2userid() . "/" . date("Ym") . "/";
        $image64 = trim(Base::getPostValue('image64'));
        $fileName = trim(Base::getPostValue('filename'));
        if ($image64) {
            $data = Base::image64save([
                "image64" => $image64,
                "path" => $path,
                "fileName" => $fileName,
            ]);
        } else {
            $data = Base::upload([
                "file" => Request::file('files'),
                "type" => 'file',
                "path" => $path,
                "fileName" => $fileName,
            ]);
        }
        //
        return $data;
    }

    /**
     * 清理opcache数据
     * @return int
     */
    public function opcache()
    {
        opcache_reset();
        return Base::time();
    }
}
