<?php

namespace App\Models;
use Auth;
use DB;

class AppVersion extends Model
{

    protected $fillable = [
        'port', 'system', 'version_sn', 'version_intro', 'package'
    ];

    protected function getPackageAttribute($value)
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

    public function storeAppVersion($input)
    {
        DB::beginTransaction();
        try {
            if ($input['package']) {
                $this->saveAttachmentAfterSave($input['package']);
            }
            $this->fill($input);
            $this->save();

            DB::commit();
            return $this->baseSucceed([], '操作成功');
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return $this->baseFailed('内部错误');
        }

    }

    public function updateAppVersion($input)
    {
        $old_cover_image = $this->package['attachment_id'];
        $new_cover_image = $input['package'];
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


    public function destroyAppVersion()
    {
        DB::beginTransaction();
        try {
            $attachment_id = $this->package['attachment_id'];
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
