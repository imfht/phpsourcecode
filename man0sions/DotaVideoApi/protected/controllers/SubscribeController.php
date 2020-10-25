<?php

class SubscribeController extends Controller
{


    public function actionUserSubscribe($user_id){
        $res = Main::apiCodeInit(1);
        $res['data'] =   Subscribe::model()->findAll("user_id=:user_id",array('user_id'=>$user_id));
        die(CJSON::encode($res));
    }
    /**
     * 用户收藏
     */
    public function actionAdd(){
        $res = Main::apiCodeInit(0);

        if(Yii::app()->request->isPostRequest)
        {
            $user_id =  @intval($_POST['user_id']);
            $vid = @$_POST['vid'];
            $model = Subscribe::model()->find("user_id=:user_id and vid=:vid",array('user_id'=>$user_id,'vid'=>$vid));
            if(!$model)
            {
                $model = new Subscribe();
                $model->createtime = $model->updatetime = date("Y-m-d H:i:s");
             }
            $model->user_id = $user_id;
            $model->vid = $vid;
            $model->content = @$_POST['content'];
            $model->status = @intval($_POST['status']);
            $model->updatetime = date("Y-m-d H:i:s");

            if($model->validate() && $model->save())
            {
                $res = Main::apiCodeInit(1);
                $res['data'] =   $model;
                die(CJSON::encode($res));
            }
            else
            {
                $err = $model->getErrors();

                $res['status']['errorinfo'] =  Main::getErrors($err);
                die(CJSON::encode($res));

            }
        }
    }
}