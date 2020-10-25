<?php

include_once "common.php";
function checkCommentPermission_normal($app,$user){
    return hasPermission($user,PERMISSION_USER);
}
function addCommentMap($app,$user_id,$product_id){
    $ufl = new RjUploadFileLimit();
    $ufl->id = generateCommonId($app);
    $ufl->count = MAKE_COMMENT_IMAGES_COUNT;
    if(!$ufl->create()){
        return false;
    }
    $cm = new RjCommentMap();
    $cm->user_id = $user_id;
    $cm->product_id = $product_id;
    $cm->upload_file_limit_id = $ufl->id;
    return $cm->create();
}

function addComment($app,$product_id,$content,$creator_id,$rating,$date = null){
    if($date == null){
        $date = getMysqlDateTimeNow();
    }
    $com = new RjComment();
    $com->id = generateCommonId($app);
    $com->product_id = $product_id;
    $com->content = $content;
    $com->creator_id = $creator_id;
    $com->date = $date;
    $com->rating = $rating;
    if($com->create()){
        return $com->id;
    }
    return false;
}

function getDBCommentMap($app,$user_id,$product_id,$upload_file_limit_id = null){
    $cond = 'user_id=:uid: AND product_id=:pid:';
    $conf = array();
    $conf['uid'] = $user_id;
    $conf['pid'] = $product_id;
    if($upload_file_limit_id != null){
        $cond = $cond.' AND upload_file_limit_id=:ufli:';
        $conf['ufli'] = $upload_file_limit_id;
    }
    $res = RjCommentMap::findFirst(array(
        $cond,
        'bind' => $conf
    ));
    return $res;
}

function removeDBComment($comment){
    $comment->uploadFileLimit->delete();
    return $comment->delete();
}

function getComments($app,$product_id){
    $res = RjComment::find(array(
        'product_id=:pid:',
        'bind' => array(
            'pid' => $product_id
        )
    ));
    $ret = array();
    foreach($res as $item){
        $t = $item->toArray();
        $app->utility->addFiles2Array($t,'images',$item->images,'getImageUrl');
        $t['user_name'] = getUserNameById($t['creator_id']);
        $ret[] = $t;
    }
    return $ret;
}

$app->post('/comment/make',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    if(!checkCommentPermission_normal($app,$user)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    }
    $data = getPostJsonObject();
    $cm = getDBCommentMap($app,$user['id'],$data->product_id,$data->upload_file_limit_id);
    if($cm != false){
        $app->db->begin();
        $id = addComment($app,$data->product_id,$data->content,$user['id'],$data->rating);
        if($id != null){
            $uti->setSuccessTrue();
            foreach($data->upload_file_names as $ufn){
                if(null == $app->UFM->increaseRefrenceCount($app,$ufn)){
                    $uti->addError(ERROR_EXECUTE_FAIL);
                    $app->db->rollback();
                    return;
                };
                $res = new RjCommentImage();
                $res->comment_id = $id;
                $res->upload_file_name = $ufn;
                if(!$res->create()){
                    $uti->addError(ERROR_EXECUTE_FAIL);
                    $app->db->rollback();
                    return;
                }
            }
            if(!removeDBComment($cm)){
                $uti->addError(ERROR_EXECUTE_FAIL);
                $app->db->rollback();
                return;
            };
            $app->db->commit();

        }
        else{
            $app->db->rollback();
            $uti->addError(ERROR_NO_CURRENT_RECORD);
            return;
        }
    }
});
