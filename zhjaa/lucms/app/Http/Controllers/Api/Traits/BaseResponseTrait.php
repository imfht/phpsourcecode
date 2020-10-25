<?php

namespace App\Http\Controllers\Api\Traits;

trait BaseResponseTrait
{
    public function respond($status, $respond_data, $message)
    {
        return ['status' => $status, 'data' => $respond_data, 'message' => $message];
    }

    public function baseSucceed($respond_data = [], $message = 'Request success!', $status = true)
    {
        return $this->respond($status, $respond_data, $message);
    }

    public function baseFailed($message = 'Request failed!', $respond_data = [], $status = false)
    {
        return $this->respond($status, $respond_data, $message);
    }
}
