<?php
return [
    "db"=>[
        "host"=>"localhost", //数据库地址
        "port"=>3306, //端口号
        "name"=>"typecho", //数据库
        "user"=>"root", //数据库用户名
        "password"=>"", //数据库密码
        "prefix"=>"typecho"  //表前缀
    ],
    "is_gbk"=>false, //是否开启gbk转utf8（有些数据库里面储存的不是utf8格式需要开启这个选项）
    //附件相关
    "attachment"=>[
        "is_download"=>true, //是否下载附件
        "type"=>"file" //附件保存类型：file 或者 qiniu
    ],
    //七牛云储存相关
    "qiniu"=>[
        "access_key"=>"",
        "secret_key"=>"",
        "bucket_name"=>"blog", //七牛空间名
        "domain"=>"http://" //七牛外链域名，必须设置,带http://
    ]
];