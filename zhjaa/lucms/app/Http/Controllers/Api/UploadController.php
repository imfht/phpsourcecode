<?php

namespace App\Http\Controllers\Api;



class UploadController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
}
