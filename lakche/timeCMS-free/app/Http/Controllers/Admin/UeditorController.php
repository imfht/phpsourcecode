<?php

namespace App\Http\Controllers\Admin;

use Request;
use Debugbar;
use App\Http\Controllers\Controller;

class UeditorController extends Controller
{
    public function index()
    {
        Debugbar::disable();
        $action = Request::get('action','');

        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(app_path()."/Libs/ueditor/config.json")), true);

        switch ($action) {
            case 'config':
                $result =  json_encode($CONFIG);
                break;

            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $result = include(app_path()."/Libs/ueditor/action_upload.php");
                break;

            /* 列出图片 */
            case 'listimage':
                $result = include(app_path()."/Libs/ueditor/action_list.php");
                break;
            /* 列出文件 */
            case 'listfile':
                $result = include(app_path()."/Libs/ueditor/action_list.php");
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result = include(app_path()."/Libs/ueditor/action_crawler.php");
                break;

            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                return htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                return json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            return $result;
        }
    }
}
