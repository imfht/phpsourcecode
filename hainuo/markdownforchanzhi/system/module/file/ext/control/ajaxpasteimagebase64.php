<?php
/**
 * Created by PhpStorm.
 * User: fengliu
 * Date: 15/8/18
 * Time: 上午12:11
 */
include '../../control.php';
class myFile extends file
{
    /**
     * Paste image in kindeditor at firefox and chrome.
     *
     * @param  string uid
     * @access public
     * @return void
     */
    public function ajaxPasteImageBase64($uid)
    {
        if($_POST)
        {
            echo $this->file->pasteImageBase64($_POST['base64Date'], $uid);
        }
    }
}