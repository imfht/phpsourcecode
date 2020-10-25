<?php
use Phalcon\Http\Response;

class CommentController extends ControllerBase
{

    public function indexAction($object,$object_id)
    {
        if(!in_array($object, ["category","article","album","link","topic","picture"]))
            $this->jsonResponse(true,[],"object is out");

    	$result = Comment::find("object = '$object' AND object_id = $object_id AND is_visible = 1 AND is_delete = 0")->toArray();
    	
    	$this->jsonResponse(false,$result);
    }

    public function storeAction($object,$object_id)
    {
        if(!in_array($object, ["category","article","album","link","topic","picture"]))
            $this->jsonResponse(true,[],"object is out");
        if(!$this->request->has("father_id"))
            $this->jsonResponse(true,[],"father_id is missing");
        if(!$this->request->has("content"))
            $this->jsonResponse(true,[],"content is missing");

        $father_id  = $this->request->get("father_id","int");
        $content    = $this->request->get("content");

    	$data = [
    		"object" 		=>$object,
    		"object_id" 	=>$object_id,
    		"father_id" 	=>$father_id,
    		"content"		=>$content
    	];
    	$model = new Comment();
        $result = $model->save($data);
    	$this->jsonResponse(false,$result);
    }

    public function destroyAction($object,$object_id,$comment_id)
    {
        if(!in_array($object, ["category","article","album","link","topic","picture"]))
            $this->jsonResponse(true,[],"object is out");

    	$model = new Comment();
    	$result = $model->findFirst("object = '$object' AND object_id = $object_id AND id = $comment_id")->delete();
    	$this->jsonResponse(false,$result);
    }
}