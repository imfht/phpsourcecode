<?php
function commentList($nid, $pid, $limit = 10)
{
    global $di;
    $query = array(
        'limit' => $limit,
        'paginator' => true,
        'andWhere' => array(
            array(
                'conditions' => '%nid% = :nid: AND %pid% = :pid:',
                'bind' => array(
                    'nid' => $nid,
                    'pid' => $pid,
                ),
            ),
        ),
    );
    $user = $di->getShared('entityManager')->get('comment');
    return $user->find($query);
}

function comment($node)
{
    global $di;
    $query = array(
        'limit' => 10,
        'paginator' => true,
        'andWhere' => array(
            array(
                'conditions' => '%nid% = :nid: AND %pid% = :pid:',
                'bind' => array('nid' => $node->id, 'pid' => 0),
            ),
        ),
    );

    $contentModel = $node->contentModel;
    $nodeEntity = $di->getShared('entityManager')->get('node');
    $nodeFields = $nodeEntity->getFields($contentModel);
    $commentEntity = $di->getShared('entityManager')->get('comment');
    if (isset($nodeFields['commentContentModel']) && is_string($nodeFields['commentContentModel'])) {
        if ($commentEntity->hasContentModel($nodeFields['commentContentModel'])) {
            $commentForm = $commentEntity->addForm($nodeFields['commentContentModel'], array(
                'nid' => $node->id,
                'pid' => 0,
                'contentModel' => $nodeFields['commentContentModel'],
            ));
        } else {
            $commentForm = false;
        }
    } else {
        $commentForm = false;
    }
    $data = $commentEntity->find($query);
    $output = array(
        '#templates' => 'comment',
        '#module' => 'comment',
        'node' => $node,
        'commentForm' => $commentForm ? $commentForm->renderForm('comment') : false,
        'data' => array(
            '#templates' => 'commentList',
            '#module' => 'comment',
            'data' => $data,
        ),
    );
    return $output;
}
