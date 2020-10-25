<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\CommonCollection;
use App\Models\Attachment;
use App\Validates\AttachmentValidate;
use Illuminate\Http\Request;

class AttachmentsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function attachmentList(Request $request, Attachment $attachment)
    {
        $per_page = $request->get('per_page', 10);

        $search_data = json_decode($request->get('search_data'), true);
        $enable = isset_and_not_empty($search_data, 'enable');
        if ($enable) {
            $attachment = $attachment->enableSearch($enable);
        }

        $use_status = isset_and_not_empty($search_data, 'use_status');
        if ($use_status) {
            $attachment = $attachment->useStatusSearch($use_status);
        }

        $type = isset_and_not_empty($search_data, 'type');
        if ($type) {
            $attachment = $attachment->typeSearch($type);
        }
        $storage_position = isset_and_not_empty($search_data, 'storage_position');
        if ($storage_position) {
            $attachment = $attachment->storagePositionSearch($storage_position);
        }

        $order_by = isset_and_not_empty($search_data, 'order_by');
        if ($order_by) {
            $order_by = explode(',', $order_by);
            $attachment = $attachment->orderBy($order_by[0], $order_by[1]);
        }

        $attachment = $attachment->with('user')->paginate($per_page);
        return new CommonCollection($attachment);
    }

    public function destroy(Attachment $attachment, AttachmentValidate $attachmentValidate)
    {
        if (!$attachment) return $this->failed('找不到附件', 404);
        $rest_destroy_validate = $attachmentValidate->destroyValidate($attachment);
        if ($rest_destroy_validate['status'] === true) {
            $rest_destroy = $attachment->destroyAttachment();
            if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
            return $this->failed($rest_destroy['message'], 500);
        } else {
            return $this->failed($rest_destroy_validate['message']);
        }
    }

    public function forceDestroy(Attachment $attachment)
    {
        if (!$attachment) return $this->failed('找不到附件', 404);
        $rest_destroy = $attachment->destroyAttachment();
        if ($rest_destroy['status'] === true) return $this->message($rest_destroy['message']);
        return $this->failed($rest_destroy['message'], 500);
    }
}
