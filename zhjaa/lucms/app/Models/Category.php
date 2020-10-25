<?php

namespace App\Models;

use DB;

class Category extends Model
{
    protected $fillable = [
        'name', 'cover_image', 'description','weight'
    ];

    public function articles()
    {
        return $this->hasMany('App\Models\Article');
    }

    protected function getCoverImageAttribute($value)
    {
        $attachment_info = Attachment::enable()->find($value);
        if ($attachment_info) {
            $url = $attachment_info->domain . '/' . $attachment_info->link_path . '/' . $attachment_info->storage_name;
            $attachment_id = $attachment_info->id;
        } else {
            $url = '';
            $attachment_id = $value;
        }
        return [
            'url' => $url,
            'attachment_id' => $attachment_id
        ];
    }

    public function storeAction($input)
    {
        DB::beginTransaction();
        try {
            if ($input['cover_image']) {
                $this->saveAttachmentAfterSave($input['cover_image']);
            }
            $this->fill($input)->save();

            DB::commit();
            return $this->baseSucceed([], '操作成功');
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return $this->baseFailed('内部错误');
        }
    }

    public function updateAction($input)
    {
        $old_cover_image = $this->cover_image['attachment_id'];
        $new_cover_image = $input['cover_image'];
        DB::beginTransaction();
        try {
            if (($old_cover_image != $new_cover_image)) {
                if ($old_cover_image > 0) {
                    $this->updateAttachmentAfterNotUseAgain($old_cover_image);
                }
                if ($new_cover_image > 0) {
                    $this->saveAttachmentAfterSave($new_cover_image);
                }
            }
            $this->fill($input)->save();

            DB::commit();
            return $this->baseSucceed([], '操作成功');
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return $this->baseFailed('内部错误');
        }
    }

    public function destroyAction()
    {
        DB::beginTransaction();
        try {
            $attachment_id = $this->cover_image['attachment_id'];
            if ($attachment_id) {
                $this->deleteAttachmentAfterDelete($attachment_id);
            }
            $this->delete();
            DB::commit();
            return $this->baseSucceed([], '删除成功');
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return $this->baseFailed('内部错误');
        }
    }
}
