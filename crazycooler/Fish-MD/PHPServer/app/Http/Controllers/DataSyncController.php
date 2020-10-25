<?php

namespace App\Http\Controllers;

use App\Common\StValidator;

use App\Directory;
use App\Document;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DataSyncController extends Controller
{

    //update-data
    public function getData(Request $request)
    {
        StValidator::make($request->all(),[
            'lastVersion' => 'required',
            'dirVersion' => 'required',
        ]);

        $user = Auth::user();
        $dir = Directory::select('id','content','version')
            ->where('userId',$user['id'])
            ->first();
        if($dir == null){
            $dir = Directory::create([
                'userId' => $user['id'],
                'content'=> '[]',
                'version'=>time(),
            ]);
        }

        $docs = Document::select('id','content','version')
            ->where('userId',$user['id'])
            ->where('version','>',$request['lastVersion'])
            ->get();

        $res = ['error' => 0];
        if($request['dirVersion'] < $dir['version']){
            $res['dir'] = $dir;
        } else {
            $res['dir'] = [];
        }
        $res['docs'] = $docs;
        return $res;
    }

    public function setData(Request $request)
    {
        //docs , dir
        $time = time();
        $user = Auth::user();

        if(isset($request['docs'])){
            $docs = $request['docs'];
            foreach($docs as $doc){
                if($doc['create_flag'] == 1){
                    Document::create([
                        'id'=>$doc['id'],
                        'userId' => $user['id'],
                        'content' => $doc['content'],
                        'version' => $time,
                    ]);
                } else {
                    Document::where('id',$doc['id'])
                        ->where('userId',$user['id'])
                        ->update([
                            'content' => $doc['content'],
                            'version' => $time,
                        ]);
                }

            }
        }

        if(isset($request['dir'])){
            $dir = $request['dir'];
            Directory::where('userId',$user['id'])
                ->update([
                    'content' => $dir['content'],
                    'version' => $time,
                ]);
        }

        return [
            'error' => 0,
            'version' => $time,
        ];
    }

    //no auth
    public function uploadImage(Request $request)
    {
        StValidator::make($request->all(),[
            'md5' => 'required'
        ]);

        $imageDir = config('qmd.imagePath');

        $file = $_FILES['file'];
        $imagePath = $imageDir.'/'.$request['md5'].'.jpg';
        move_uploaded_file($file['tmp_name'], $imagePath);

        $uri = $_SERVER['REQUEST_URI'];
        $uri = str_replace("upload-image","image",$uri);
        $host = $_SERVER['HTTP_HOST'];
        $url = 'http://'.$host.$uri.'?imageId='.$request['md5'];

        return [
            'error' => 0,
            'imageUrl' => $url,
        ];
    }

    public function image(Request $request)
    {
        StValidator::make($request->all(),[
            'imageId' => 'required'
        ]);

        if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
            return response("",304,['Last-Modified' => $_SERVER['HTTP_IF_MODIFIED_SINCE']]);
        }


        $validTime = 265*24*60*60;
        $head = [
            'Cache-Control' => 'max-age='.$validTime,
            'Last-Modified' => gmdate('D, d M Y H:i:s').'GMT',
            'Expires' => gmdate('D, d M Y H:i:s', time() + $validTime).'GMT',
            'Content-Type' => 'image/jpeg',
        ];

        $imageDir = config('qmd.imagePath');
        $imagePath = $imageDir.'/'.$request['imageId'].'.jpg';

        $brokenImagePath = $imageDir.'/broken.jpg';

        if(file_exists($imagePath)){
            return response(
                file_get_contents($imagePath),
                200,
                $head
            );
        } else {
            return response(
                file_get_contents($brokenImagePath),
                200,
                $head
            );
        }
    }
}
