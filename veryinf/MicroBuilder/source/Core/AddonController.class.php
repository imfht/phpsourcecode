<?php
namespace Core;
use Core\Model\Addon;
use Think\Controller;

class AddonController extends Controller {
    /**
     * @var array
     */
    private $params;
    /**
     * @var Addon
     */
    protected $addon;
    
    function __construct($params, $addon) {
        $this->params = $params;
        parent::__construct();
        $this->addon = $addon;
        $this->assign('__addon', $this->addon->getCurrentAddon());
        $this->assign('__controller', $this);
        $this->assign('C', $this);
    }
    
    public function U($url='', $vars='') {
        $entry = strtolower(MODULE_NAME);
        return $this->addon->U($entry, $url, $vars);
    }
    
    protected function getParams() {
        return $this->params;
    }

    protected function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
        parent::display($this->parseTmplateName($templateFile), $charset, $contentType, $content, $prefix);
    }

    protected function fetch($templateFile = '', $content = '', $prefix = '') {
        return parent::fetch($this->parseTmplateName($templateFile), $content, $prefix);
    }

    private function parseTmplateName($templateFile) {
        $tmp = $templateFile;
        if(empty($templateFile)) {
            $templateFile = $this->params['Action'];
        }
        $pieces = explode('/', $templateFile);
        if(count($pieces) <= 3) {
            if(count($pieces) == 1) {
                $templateFile = MB_ROOT . "addons/{$this->params['Addon']}/{$this->params['Entry']}/View/{$this->params['Controller']}/{$pieces[0]}.html";
            }
            if(count($pieces) == 2) {
                $templateFile = MB_ROOT . "addons/{$this->params['Addon']}/{$this->params['Entry']}/View/{$this->params['Controller']}/{$pieces[1]}.html";
            }
            if(count($pieces) == 3) {
                $entry = ucfirst($pieces[0]);
                $templateFile = MB_ROOT . "addons/{$this->params['Addon']}/{$entry}/View/{$this->params['Controller']}/{$pieces[2]}.html";
            }
            if(!is_file($templateFile)) {
                $templateFile = $tmp;
            }
        }
        return $templateFile;
    }
}
