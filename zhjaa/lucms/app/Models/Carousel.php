<?php

namespace App\Models;

use DB;

class Carousel extends Model
{
    protected $fillable = [
        'cover_image', 'description', 'url', 'weight'
    ];

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
        try {
            if ($input['cover_image']) {
                $this->saveAttachmentAfterSave($input['cover_image']);
            }
            $this->fill($input);
            $this->save();
            return $this->baseSucceed([], '操作成功');
        } catch (\Exception $e) {
            throw $e;
            return $this->baseFailed('内部错误');
        }
    }


    public function updateAction($input, $m_carsousel)
    {
        $old_cover_image = $m_carsousel->cover_image['attachment_id'];
        $new_cover_image = $input['cover_image'];
        try {
            if (($old_cover_image != $new_cover_image)) {
                if ($old_cover_image > 0) {
                    $this->updateAttachmentAfterNotUseAgain($old_cover_image);
                }
                if ($new_cover_image > 0) {
                    $this->saveAttachmentAfterSave($new_cover_image);
                }
            }
            $m_carsousel->saveData($input);
            return $this->baseSucceed([], '操作成功');
        } catch (\Exception $e) {
            return $this->baseFailed('内部错误');
        }
    }

    public function destroyAction($m_carsousel)
    {
        DB::beginTransaction();
        try {
            $attachment_id = $m_carsousel->cover_image['attachment_id'];
            if ($attachment_id) {
                $this->deleteAttachmentAfterDelete($attachment_id);
            }
            $m_carsousel->delete();

            DB::commit();
            return $this->baseSucceed([], '删除成功');
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return $this->baseFailed('内部错误');
        }
    }

}
