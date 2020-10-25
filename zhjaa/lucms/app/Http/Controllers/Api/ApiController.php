<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\ApiResponseTraits;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{

    use ApiResponseTraits;

    public function __construct()
    {
        parent::__construct();
    }




}
