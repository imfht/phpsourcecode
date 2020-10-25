<?php
namespace Addon\Bridge\Api;

use Core\Model\Addon;
use Core\Model\Utility;

class Application {
    /**
     * @var Addon
     */
    public $addon;
    
    private $installSql = <<<'DOC'
CREATE TABLE IF NOT EXISTS `mb_br_bridges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `processor` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `token` varchar(32) NOT NULL,
  `remark` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  KEY `processor` (`processor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
DOC;

    private $uninstallSql = <<<'DOC'
DROP TABLE IF EXISTS mb_br_bridges; 
DOC;

    
    public function install() {
        $u = new Utility();
        $u->dbRunQuery(trim($this->installSql));
        $this->addon->pasteBenchEntry('接入第三方微信平台', 'connect/weixin', '可以用于对接已有的第三方微信公众号平台');
        return true;
    }
    
    public function uninstall() {
        $u = new Utility();
        $u->dbRunQuery(trim($this->uninstallSql));
    }
    
    public function upgrade($versionOriginal, $versionNew) {
        
    }
}
