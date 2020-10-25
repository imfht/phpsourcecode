<?php

namespace App\Http\Controllers\Admin;

use App\Model\Log;
use App\Http\Controllers\Controller;
use Theme;

class LogsController extends Controller
{
  public function index()
  {
    $logs = Log::sortByDesc('id')->paginate(20);
    return Theme::view('logs.index',compact('logs'));
  }
}
