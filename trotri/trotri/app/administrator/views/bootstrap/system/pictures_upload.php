<?php
$html = $this->getHtml();
$urlManager = $this->getUrlManager();

echo $html->openTag('div', array('class' => 'col-lg-6'));
echo $html->tag('div', array('id' => 'batch_upload_picture_file', 'url' => $urlManager->getUrl('ajaxupload', '', ''), 'name' => 'upload'), $this->CFG_SYSTEM_GLOBAL_UPLOAD);
echo $html->closeTag('/div');
?>