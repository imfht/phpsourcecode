<?php

class uploadFileManager{
    public function findUploadFile($app,$name){
        $ret = false;
        $res = $app->modelsManager->createBuilder()
            ->from('UploadFile')
            ->where('name',$name)
            ->getQuery()
            ->execute();
        foreach($res as $f){
            $ret = array(
                'name' => $f->name,
                'app_type' => $f->app_type,
                'mime_type' => $f->mime_type
            );
            break;
        }
        return $ret;
    }



    public function increaseRefrenceCount($app,$name){
        $phql = "UPDATE RjUploadFile set refrence_count = refrence_count + 1 WHERE RjUploadFile.name = :name:";
        $res = $app->modelsManager->executeQuery($phql,array(
            'name' => $name
        ));
        return $res->success()?$name:null;
    }

    public function reduceRefrenceCount($app,$name){
        global $config;
        $phql = "SELECT * FROM RjUploadFile WHERE name=:name:";
        $res = $app->modelsManager->executeQuery($phql,array(
            'name' => $name
        ));
        foreach($res as $item){
            if($item->refrence_count > 1){
                $phql = "UPDATE RjUploadFile set refrence_count = refrence_count - 1 WHERE RjUploadFile.name = :name:";
                $res = $app->modelsManager->executeQuery($phql,array(
                    'name' => $name
                ));
                return $res->success();
            }
            else{
                $res = RjUploadFile::findFirst(array('name=:name:','bind' => array(
                    'name' => $name
                )));
                if($res === false){
                    return false;
                }
                $transaction = $app->TxManager->get();
                $res->setTransaction($transaction);
                if($res->delete()){

                    $res = @unlink ('../public/'.$config->application->imgUrl.$name);
                    if($res){
                        $transaction->commit();
                        return true;
                    }
                    else{
                        $transaction->rollback();
                        return false;
                    }
                }
                return $res->success();
            }
            break;
        }
        return true;
    }

    private function generateFileName($upload){
        $name = uniqid(false,true) . md5($upload->getName()) . '.' . $upload->getExtension();
        return $name;
    }

    public function  upload($app,$appType,$userId,$patht,$checkUploadFileLimit = true){
        $files = array();
        $ufli = null;
        if($checkUploadFileLimit){//是否开启上传数量限制
            if(!$app->request->hasPost('upload_file_limit_id')){
                return $files;
            }
            $ufli = $app->request->getPost('upload_file_limit_id');
            $ufl = RjUploadFileLimit::findFirst(array('id=:id:','bind'=>array(
                'id' => $ufli
            )));
            if($ufl === false){
                return $files;
            }
            if($ufl->count <= 0){
                return $files;
            }
        }
        $name = null;
        if($app->request->hasFiles() == true){
            $uploads = $app->request->getUploadedFiles();
            foreach($uploads as $upload){
                $transaction = $app->TxManager->get();
                if($checkUploadFileLimit){
                    $ufl->setTransaction($transaction);
                    $count = $ufl->count;
                    if(!$ufl->save(array(
                        'count' => $count-1
                    ))){
                        continue;
                    }
                    $ufl->count = $count-1;
                }
                $name = $this->generateFileName($upload);
                $mimeType = $upload->getType();
                $path = $patht . $name ;
                if(!file_exists($patht)){
                    mkdir($patht);
                }
                $res = new RjUploadFile();
                $res->setTransaction($transaction);
                $res->user_id = $userId;
                $res->app_type = $appType;
                $res->mime_type = $mimeType;
                $res->name = $name;
                $res->refrence_count = 0;

                if(!$res->create()){
                    $transaction->rollback();
                    continue;
                }
                if($upload->moveTo($path)){
                    $files[] = array(
                        'file_name' => $upload->getName(),
                        'upload_file_name' => $name
                    );
                    $transaction->commit();
                    continue;
                }
                else{
                    $transaction->rollback();
                    continue;
                }
            }
        }
        else{
            return $files;
        }
        return $files;
    }
}