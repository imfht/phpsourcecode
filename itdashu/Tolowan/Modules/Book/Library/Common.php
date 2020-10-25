<?php
namespace Modules\Book\Library;

use Modules\Book\Models\NodeBook;
use Modules\Node\Entity\Node;

class Common
{
    public static function bookItemSave($form)
    {
        global $di;
        $data = $form->getData();
        $formEntity = $form->formEntity;
        if(isset($formEntity['settings']['id'])){
            $book = NodeBook::findFirst($formEntity['settings']['id']);
            if(!$book){
                return false;
            }
        }else{
            $book = new NodeBook();
        }
        if(!isset($data['title']) || empty($data['title'])){
            $bookNode = Node::findFirst($data['nid']);
            if(!$bookNode){
                return false;
            }
            $data['title'] = (string)$bookNode->getTitle();
        }
        foreach ($data as $key => $value){
            $book->{$key} = $value;
        }
        if($book->save()){
            return $book;
        }
        return false;
    }
    //术语排序
    public static function saveBookItemSort($hierarchy)
    {
        $widget = 1;
        $start = 1;
        global $di;
        //Config::printCode($hierarchy);
        $db = $di->getShared('db');
        foreach($hierarchy as $key => $value){
            $db->begin();
            $nodeBook = NodeBook::findFirst($key);
            if($start == 1){
                $widget = $nodeBook->widget;
                if($nodeBook->pid != 0){
                    $nodeBook->pid = 0;
                }
                $start++;
                $state = $nodeBook->save();
            }else{
                $nodeBook->pid = 0;
                $nodeBook->widget = $widget;
                $state = $nodeBook->save();
            }
            if($state){
                $db->commit();
            }else{
                $db->rollback();
            }
            $widget++;
            if(!empty($value)){
                $widget = self::_saveNodeBookSort($value,$widget,$nodeBook->id);
            }
        }
        return true;
    }
    protected static function _saveNodeBookSort($hierarchy,$widget,$pid)
    {
        global $di;
        $db = $di->getShared('db');
        foreach($hierarchy as $key => $value){
            $db->begin();
            $nodeBook = NodeBook::findFirst($key);
            $nodeBook->widget = $widget;
            $nodeBook->pid = $pid;
            $state = $nodeBook->save();
            if($state){
                $db->commit();
            }else{
                $db->rollback();
            }
            $widget++;
            $pid = $nodeBook->id;
            if(!empty($value)){
                self::_saveNodeBookSort($value,$widget,$pid);
            }
        }
        return $widget;
    }
    //术语排序结束
}