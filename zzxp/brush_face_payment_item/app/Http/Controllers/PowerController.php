<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Lib\Api\AdminApi AS AdminApi;
use Illuminate\Support\Facades\Cache;
use Session;

use Illuminate\Http\Request;


class PowerController extends Controller
{
    public function __construct(Request $req)
    {
        $this->admin = new AdminApi;
        $this->request = $req;
    }
    
    public function index(){
        $data = $this->request->all();
        \Log::info($data);
        // \Log::info($_SERVER);
    }
}