weibo.PHP
-------------
新浪微博的账号登录及api操作，使用oauth 2.0  
官方提供的sdk太过庞大，这是我自己简化的，提供简单的账号登录、获取个人信息、发布分享等功能，如果需要其他功能可以根据官方的api文档自行添加

    //示例：根据uid获取用户信息
    $result=$sina->api('users/show', array('uid'=>$uid), 'GET');

文件说明
-------------
>**sina.php** 主文件  
>**demo.php** 示例程序  
>**config.php** 示例程序配置  
>**callback.php** 示例程序回调文件

开发者信息
-------------
[PiscDong studio](http://www.piscdong.com/)
