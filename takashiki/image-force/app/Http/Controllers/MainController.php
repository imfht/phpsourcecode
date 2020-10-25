<?php

namespace App\Http\Controllers;

use App\Jobs\CheckImage;
use App\Jobs\DuplicateImage;
use App\Models\Image;
use Input;
use Redirect;

class MainController extends Controller
{
    public function upload()
    {
        $file = Input::file('file');
        if (!$file || !$file->isValid()) {
            return false;
        }
        $image = Image::getModel($file->getRealPath());

        return response($image->getUrl());
    }

    public function view($sha1)
    {
        $image = Image::where(['sha1' => $sha1])->firstOrFail();

        return Redirect::away($image->getRealUrl(), 301);
    }
}