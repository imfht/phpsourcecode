<?php
$tablesArr=array(
    0=>array(
        'name'=>'article',
        'fields'=>array( 'id', 'channelId', 'pageId', 'title','keywords', 'description', 'content', 'originalPic', 'indexPic', 'dtTime','counts' ),
    ),
    1=>array(
        'name'=>'calllist',
        'fields'=>array( 'id', 'channelId', 'callId' )
    ),
    2=>array(
        'name'=>'comment',
        'fields'=>array( 'id', 'recordId', 'channelId', 'name', 'content', 'email', 'homepage', 'ip', 'dtTime', 'auditing', 'ordering', 'memberId', 'memberTableName', 'answerId' )
    ),
    3=>array(
        'name'=>'download',
        'fields'=>array( 'id', 'channelId', 'title', 'keywords', 'description', 'content', 'filePath' ,'fileSize' , 'dtTime', 'counts',  'ordering')
    ),
    4=>array(
        'name'=>'flash',
        'fields'=>array( 'id', 'title', 'description', 'url', 'picpath', 'group_id', 'ordering', 'dtTime' )
    ),
    5=>array(
        'name'=>'flash_group',
        'fields'=>array( 'id', 'title', 'summary', 'type', 
            'boxId', 'pattern', 'times', 'adTrigger' , 'auto', 'width', 'height', 'txtHeight' , 'dtTime')
    ),
    6=>array(
        'name'=>'guestbook',
        'fields'=>array('id', 'name', 'contact', 'custom','content','content1','channelId','ip','uid','dtTime','auditing','isPublic'),
    ),
    7=>array(
        'name'=>'jobs',
        'fields'=>array( 'id', 'channelId', 'title', 'keywords', 'description', 'content', 'jobKind', 'requireNum', 'experience', 
                      'address', 'lastTime', 'salary', 'educational', 'isHouse', 'telphone', 'email', 'dtTime', 'ordering' )
    ),
    8=>array(
        'name'=>'linkers',
        'fields'=>array( 'id', 'channelId', 'links', 'title', 'linkAddress', 'originalPic', 'smallPic',
                           'description', 'dtTime', 'ordering' )
    ),
    9=>array(
        'name'=>'list',
        'fields'=>array( 'id', 'channelId', 'title', 'style', 'keywords', 'description','content', 'author', 'source',
                 'sourceUrl', 'counts','originalPic','indexPic', 'recommend', 'ordering', 'dtTime', 'editTime', 'hassplitpages' )
    ),
    10=>array(
        'name'=>'menu',
        'fields'=>array( 'id', 'menuName', 'title', 'keywords', 'description', 'type', 'deep', 'parentId', 'isComment',
                 'level', 'isHidden', 'originalPic', 'smallPic', 'width', 'hight', 'isExternalLinks', 'redirectUrl', 'related_common',
                         'ordering','isTarget' )
    ),
    11=>array(
        'name'=>'order',
        'fields'=>array( 'id','title', 'custom', 'remark', 'handling', 'result', 'ispay', 'channelId', 'payprice', 'orderId', 'customer', 'dtTime'  ),
    ),
    12=>array(
        'name'=>'picture',
        'fields'=>array( 'id', 'channelId', 'title', 'keywords', 'description', 'content', 'indexPic', 'originalPic',
                       'middlePic', 'smallPic', 'counts', 'ordering', 'dtTime', 'hassplitpages')
    ),
    13=>array(
        'name'=>'poll',
        'fields'=>array( 'id', 'channelId', 'choice', 'categoryId', 'isdefault', 'ordering', 'num' )
    ),
    14=>array(
        'name'=>'poll_category',
        'fields'=>array( 'id', 'title', 'choice', 'client_ip', 'channelId', 'dtTime', 'ordering' )
    ),
    15=>array(
        'name'=>'product',
        'fields'=>array( 'id', 'channelId', 'title', 'keywords', 'description', 'content' , 'sn', 'spec', 'sellingPrice',
                   'preferPrice', 'indexPic', 'originalPic', 'middlePic', 'smallPic', 'categoryId', 'counts', 'ordering', 
                   'dtTime', 'ispush', 'hassplitpages')
    ),
    16=>array(
        'name'=>'jobs_resume',
        'fields'=>array( 'id', 'parentId', 'channelId', 'name', 'sex', 'birthday', 'nation', 'isMarried',
                  'nowJob', 'nowAddress' , 'residence', 'educational', 'height', 'finishSchool', 'finishTime', 'speciality',
                  'experience', 'selfAppreciation', 'languageSkill', 'email', 'telphone', 'mobile', 'address', 'resume', 'dtTime')
    ),
    17=>array(
        'name'=>'video',
        'fields'=>array( 'id', 'channelId','title', 'keywords', 'description', 'content', 'filePath', 'fileSize', 'picture', 'counts', 'ordering', 'dtTime' )
    ),
    18=>array(
        'name'=>'user',
        'fields'=>array( 'id', 'nickname', 'email', 'username', 'pwd', 'role', 'right', 'name', 'sex', 'age', 'qq',
               'msn','mtel', 'address', 'ip', 'auditing', 'dtTime', 'lastlogin','originalPic','smallPic','cropPic' )
    ),
    19=>array(
        'name'=>'models_reg',
        'fields'=>array( 'id', 'type', 'model_name', 'config', 'install', 'unstall', 'summary', 'version', 'readonly' )
    ),
	20=>array(
        'name'=>'models_set',
        'fields'=>array( 'id', 'channelId', 'type', 'field', 'field_tab' )
    ),
    21=>array(
        'name'=>'product_order',
        'fields'=>array( 'id', 'usertype', 'userid', 'customer', 'm_tel', 'address', 'orederinfo', 'dtTime', 'stauts' ,
                          'orderId','ispay','payprice','remark')
    ),
    22=>array(
        'name'=>'mapshow',
        'fields'=>array( 'id', 'channelId', 'title', 'content', 'mapKey', 'lat', 'lng', 'width', 'height', 
                  'phone', 'address', 'keywords', 'description' )
    )
);
?>