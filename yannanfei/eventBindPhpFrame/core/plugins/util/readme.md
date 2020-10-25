####添加sqlit缓存方式
使用方法：
      $time1=time();
        $utilPlugin= plugin('util');
           $utilPlugin=$utilPlugin?$utilPlugin:new utilPlugin();
       //  $flag=   $utilPlugin->set_sqlite_cache('test','myval');

         $flag=   $utilPlugin->set_sqlite_cache('test','myval2');
         $flag=   $utilPlugin->set_sqlite_cache(array(
            'a'=>'a2',
            'b'=>'b2',
            'c'=>'c2'
        ));
        //测试删除

       // $flag=   $utilPlugin->delete_sqlite_cache('test');
        //write(ModelLit::get_last_sql());


        //测试获取值

        $flag=  $utilPlugin->get_sqlite_cache('a,b,c');
        echo time_end($time1);

    //清除所有缓存
    delete_sqlite_cache('all');