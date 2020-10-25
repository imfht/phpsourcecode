<?php

namespace App\Validates;

use DB;

class  AttachmentValidate extends Validate
{
    protected $message = '操作成功';
    protected $data = [];

    public function destroyValidate($attachment)
    {
        if ($attachment->enable === 'T') return $this->baseFailed('启用状态的附件不允许删除');
        switch ($attachment->storage_position) {
            case 'local':
                break;
            case 'oss':
//                return $this->baseFailed('oss 附件的删除方法还未实现');
                break;
            case 'api_local':
                break;
            case 'api_oss':
                break;
            default :
                $this->baseFailed('未知的存储方式');
                break;
        }

        switch ($attachment->type) {
            case 'avatars' :
                $use_status = DB::table('users')->where('head_image', $attachment->id)->count();
                break;
            case 'advertisements' :
                $use_status = DB::table('advertisements')->where('cover_image', $attachment->id)->count();
                if (!$use_status) {
                    $use_status = DB::table('categories')->where('cover_image', $attachment->id)->count();
                    if (!$use_status) {
                        $use_status = DB::table('articles')->where('cover_image', $attachment->id)->count();
                    }
                }
                break;
            case 'agreements' :
                break;
            case 'businesses' :
                $use_status = DB::table('dls_companies')->where('business', $attachment->id)->count();
                break;
            default:
                $use_status = 0;
        }

        if ($use_status) return $this->baseFailed('图片被使用，无法删除');

        return $this->baseSucceed($this->data, $this->message);
    }
}
