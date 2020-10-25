<?php
namespace Modules\Taxonomy\Controllers;

use Core\Config;
use Core\Mvc\Controller;
use Core\Model;
use Modules\Taxonomy\Models\Term;

class IndexController extends Controller
{

    public function autoTermJsonAction()
    {
        extract($this->variables['router_params']);
        $output = array();
        $this->variables['page'] = array(
            '#templates' => 'term-json',
        );
        $termList = Term::find(array(
            'conditions' => 'name LIKE :name:',
            'bind' => array('name' => $name . '%'),
        ));
        foreach ($termList as $item) {
            $output[$item->{$key}] = $item->{$value};
        }
        $this->response->setContentType('application/json', 'UTF-8');
        echo json_encode($output);
    }
}
