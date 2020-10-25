<?php
namespace app\common\behavior;

class SetBaseDir  
{
    public function run()
    {
        $base_dir = input('post.base_dir');
        $is_point_begin = \preg_match("/^\..*/",$base_dir);
        if($is_point_begin){
            
        }

    }
}
