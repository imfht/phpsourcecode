<?php
namespace Addon\Develop\Api;

use Core\Model\Addon;

class Application {
    /**
     * @var Addon
     */
    public $addon;
    
    private $installSql = <<<'DOC'
DOC;

    private $uninstallSql = <<<'DOC'
DOC;

    
    public function install() {
        $this->addon->pasteControlEntry('发布微构系统', 'publish/exec', '将当前运行代码发行为特定版本');
        $this->addon->pasteControlEntry('发布扩展', 'publish/addons', '打包指定的模块');
        return true;
    }
    
    public function uninstall() {
    }
    
    public function upgrade($versionOriginal, $versionNew) {
        
    }
}
