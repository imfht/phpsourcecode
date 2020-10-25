<?php
namespace app\common\behavior;



class Auth  
{
    public function run()
    {
        if (input('post.access_key') !== config('access_key')) {
            
            exit;
        }
        

        
    }
}
