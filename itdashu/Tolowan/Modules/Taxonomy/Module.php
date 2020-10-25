<?php
function termList($limit = null, $taxonomy = null, $parent = 0, $query = array())
{
    global $di;
    $query += array(
        'andWhere' => array(),
    );
    if (!is_null($limit)) {
        $query['limit'] = $limit;
    }
    if (!is_null($taxonomy)) {
        $query['andWhere'][] = array(
            'conditions' => '%contentModel% = :contentModel:',
            'bind' => array('contentModel' => $taxonomy),
        );
    }
    $query['andWhere'][] = array(
        'conditions' => '%parent% = :parent:',
        'bind' => array('parent' => $parent),
    );
    $term = $di->get('entityManager')->get('term');
    return $term->find($query);
}
