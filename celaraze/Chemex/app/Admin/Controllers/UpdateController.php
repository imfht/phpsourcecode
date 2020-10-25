<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Support\System;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Card;
use Illuminate\Support\Facades\Http;

class UpdateController extends Controller
{
    public function index(Content $content)
    {
        $response = Http::get('https://api.github.com/repos/Celaraze/Chemex/releases/latest')->json();
        if (isset($response['name'])) {
            $new = $response['name'];
        } else {
            $new = '0.0.0';
        }
        $res = System::diffVersion(config('admin.chemex_version'), $new, '.');
        $data['old'] = config('admin.chemex_version');
        $data['new'] = $new;
        $data['res'] = $res;
        $data['url'] = $response['assets'][0]['browser_download_url'];

        return $content
            ->header('更新')
            ->description('使应用保持最新')
            ->body(function (Row $row) use ($data) {
                $row->column(2, new Card(view('update')->with('data', $data)));
            });
    }
}
