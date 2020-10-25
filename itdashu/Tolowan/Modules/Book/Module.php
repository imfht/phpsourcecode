<?php
$di->getShared('eventsManager')->attach('form:initialize', function ($event, $form) {
    if ($form->formId == 'entityNode' && isset($form->formEntity['settings']['contentModel']) && $form->formEntity['settings']['contentModel'] != 'book') {
        $data = $form->getData();
        if(isset($data['id'])){
            $bookModel = \Modules\Book\Models\NodeBook::findFirstByNid($data['id']);
            if($bookModel){
                $data['book_id'] = $bookModel->bid;
                $data['book_pid'] = $bookModel->pid;
                $data['book_weight'] = $bookModel->weight;
                $form->setData($data);
            }
        }
        $form->formEntity['book'] = \Core\Config::get('book.nodeBookHook');
    }
});
$di->getShared('eventsManager')->attach('entity:saveAfter', function ($event, $entity) {
    global $di;
    $entityId = $entity->getEntityId();
    if ($entityId === 'node') {
        $entityForm = $entity->entityForm;
        $formData = $entityForm->getData();
        if (isset($formData['book_id']) && $formData['book_id']) {
            $bookModel = \Modules\Book\Models\NodeBook::findFirstByNid($entity->entityModel->id);
            if(!$bookModel){
                $bookModel = new \Modules\Book\Models\NodeBook();
            }
            $bookModel->nid = $entity->entityModel->id;
            $bookModel->created = $entity->entityModel->created;
            $bookModel->changed = $entity->entityModel->changed;
            $bookModel->bid = $formData['book_id'];
            $bookModel->title = $entity->entityModel->getTitle();
            if ($bookModel->save()) {
                $di->getShared('flash')->success('成功保存文章到书本目录');
            } else {
                $di->getShared('flash')->error('保存文章到书本目录失败');
            }
        }
    }
});
$di->getShared('eventsManager')->attach('entity:links', function ($event, $entity) {
    global $di;
    $entityId = $entity->getEntityId();
    if ($entityId === 'node' && isset($entity->contentModel) && $entity->contentModel == 'book') {
        $links = $entity->getLinks();
        $links['bookSort'] = array(
            'href' => array(
                'for' => 'adminBookTocSort',
                'id' => $entity->id,
            ),
            'icon' => 'danger',
            'name' => '排序书本目录',
        );
        $entity->setLinks($links);
    }
});
//函数库
function bookToc($id,$contentModel='article'){
    $data = true;
    if($contentModel != 'book'){
        $nodeBook = \Modules\Book\Models\NodeBook::findFirstByNid($id);
        if(!$nodeBook){
            return false;
        }else{
            $bid = $nodeBook->bid;
        }
    }else{
        $bid = $id;
    }
    $bookNode = \Modules\Node\Entity\Node::findFirst($bid);
    if($data === true) {
        $data = \Modules\Book\Models\NodeBook::find(array(
            'order' => 'weight DESC,created ASC',
            'conditions' => 'bid = :bid: AND pid = :pid:',
            'bind' => array(
                'pid' => 0,
                'bid' => $bid
            ),
        ));
    }
    if(!$data){
        return false;
    }
    $output = array(
        '#templates' => array(
            'bookToc',
            'bookToc-'.$id
        ),
        'bookNode' => $bookNode,
        'data' => $data
    );
    return $output;
}