<?php
require_once dirname(__FILE__) . '/../component/PApi.php';

class AllTests extends TestSuite {
    function AllTests() {
        $this->addFile(dirname(__FILE__) . '/demo.php');
    }
    
   
}
?>