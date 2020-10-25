<?php
namespace app\cms\index\wxapp;

use app\common\controller\IndexBase;
use app\cms\model\Content AS ContentModel; 

//小程序 帮助文档
class Help extends IndexBase
{
    public function index($fid=0){
        $map = [];
        $order='id desc';
        $rows = 10;
        $array = getArray( ContentModel::getListByMid(1,$map,$order,$rows) );

        $items = [];
        foreach($array['data'] AS $rs){
            $items[] = [
                    '_id' => $rs['id'],
                    'title' => $rs['title'],
                    'content' => get_word($rs['content'], 200),
                    '__v' => 0,
                    'create_at' => $rs['create_time'],
            ];
        }
        
        $data = [
                'meta' => [  'code' => 0,  'message' => '调用成功', ],
                'data' =>[
                        'items' =>$items,
                        'paginate' =>[
                                'page' => $array['current_page'],
                                'pages' => 1,
                                'perPage' => $array['per_page'],
                                'total' => $array['total'],
                                'prev' => 1,
                                'next' => 1,
                                'hasNext' => false,
                                'hasPrev' => false,
                        ],
                ],
        ];
        return json($data);        
//         echo var_export(json_decode($code,true),true);exit;

    }
    
    public function show($id=0){
        $info = ContentModel::getInfoByid($id);
        
        $data = [
                'meta' =>[
                        'code' => 0,
                        'message' => '调用成功',
                ],
                'data' =>[
                        '_id' => $id,
                        'title' => $info['title'],
                        'content' => ($info['content']),
                        '__v' => 0,
                        'create_at' => date('Y-m-d H:i',$info['create_time']),
                ],
        ];
        
        return json($data);
    }    
}