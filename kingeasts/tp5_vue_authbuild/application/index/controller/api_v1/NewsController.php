<?php
namespace app\index\controller\api_v1;


use app\common\controller\Resful;

class NewsController extends Resful
{
    public function index()
    {
        $result = [];

        for ($i = 1; $i <= 100; $i++) {
            $result[] = ['id'=>$i, 'title'=>'测试文章' . $i];
        }
        return $this->success([
            'items'=>$result
        ]);
    }

}