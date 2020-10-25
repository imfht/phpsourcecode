<?php

namespace App\Models;

use App\Handlers\FileuploadHandler;
use DB;


class Attachment extends Model
{
    protected $fillable = [
        'user_id', 'ip', 'original_name', 'mime_type', 'size', 'type', 'storage_position',
        'domain', 'storage_path', 'link_path', 'storage_name', 'enable', 'use_status', 'remark'
    ];

    protected function setIpAttribute()
    {
        $this->attributes['ip'] = get_client_ip();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function scopeUseStatusSearch($query, $value)
    {
        return $query->where('use_status', $value);
    }

    public function scopeTypeSearch($query, $value)
    {
        return $query->where('type', $value);
    }

    public function scopeStoragePositionSearch($query, $value)
    {
        return $query->where('storage_position', $value);
    }

    public function destroyAttachment()
    {
        $base_image_up_dir = 'images';
        $rest_delet_file = (new FileuploadHandler)->fileDelete($base_image_up_dir . '/' . $this->type . '/' . $this->storage_name);

        DB::beginTransaction();
        try {
            $this->delete();
            if ($rest_delet_file) {
                $tip = '';
            } else {
                $tip = '：附件找不到';
            }
            DB::commit();
            return $this->baseSucceed([], '附件删除成功' . $tip);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->baseFailed('内部错误');
        }
    }

}
