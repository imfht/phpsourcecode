<?php

namespace App\Models\Traits;

use App\Models\Attachment;

trait ExcuteTrait
{
    public function getById($id)
    {
        return $this->findOrFail($id);
    }

    public function saveData($input)
    {
        $this->fill($input)->save();
        return $this;
    }

    public function getFirstRecordByWhere($where)
    {
        return $this->where($where)->first();
    }

    public function saveAttachmentAfterSave($attachment_id)
    {
        if (is_array($attachment_id)) {
            Attachment::whereIn('id', $attachment_id)
                ->update([
                    'enable' => 'T',
                    'use_status' => 'T',
                ]);
        } else {
            $attachment_info = Attachment::find($attachment_id);
            if ($attachment_info) {
                $attachment_info->enable = 'T';
                $attachment_info->use_status = 'T';
                $attachment_info->save();
            }
        }

    }

    public function updateAttachmentAfterNotUseAgain($attachment_id)
    {
        if (is_array($attachment_id)) {
            Attachment::whereIn('id', $attachment_id)
                ->update([
                    'enable' => 'F',
                    'use_status' => 'F',
                ]);
        } else {
            $attachment_info = Attachment::find($attachment_id);
            if ($attachment_info) {
                $attachment_info->enable = 'F';
                $attachment_info->use_status = 'F';
                $attachment_info->save();
            }
        }
    }

    public function deleteAttachmentAfterDelete($attachment_id)
    {
        if (is_array($attachment_id)) {
            Attachment::whereIn('id', $attachment_id)
                ->update([
                    'enable' => 'F',
                    'use_status' => 'F',
                ]);
        } else {
            $attachment_info = Attachment::find($attachment_id);
            if ($attachment_info) {
                $attachment_info->enable = 'F';
                $attachment_info->use_status = 'F';
                $attachment_info->save();
            }
        }
    }
}
