<?php
namespace Modules\Node\Library;

use Core\Config;
use Library\Scsw\Scsw;

class Search{
    public static function search($data){
        global $di;
        $query = array(
            'limit' => 15,
            'page' => $data['page'],
            'paginator' => true,
            'match' => array()
        );
        $nodeEntity = $di->getShared('entityManager')->get('node');

        $query['match'][] = array(
            'conditions' => 'MATCH(%body%) AGAINST(:body:)',
            'bind' => array('body' => Scsw::shortWord($data['word'])),
            'in' => true
        );
        $data = $nodeEntity->find($query);
        return $data;
    }
}