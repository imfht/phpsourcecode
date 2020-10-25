<?php

namespace AuroraLZDF\Bigfile\Controllers;

use AuroraLZDF\Bigfile\Traits\TraitBigfileChunk;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Log;

class BigfileController extends Controller
{
    use TraitBigfileChunk;

    public function loadView()
    {
        $data = [
            'chunk_size' => config('bigfile.chunk_size'),
            'max_size' => config('bigfile.max_size'),
            'user_id' => Auth()->id(),
        ];
        return view('vendor.bigfile.bigfile', compact('data'));
    }

    public function upload(Request $request)
    {
        $data = $request->all();
        $action = $request->input('act');

        if (isset($action) && $action == 'upload') {

            $result = $this->uploadToServer($data);
            return response()->json($result);
        }

        $result = $this->uploadChunk($request);
        return response()->json($result);
    }
}
