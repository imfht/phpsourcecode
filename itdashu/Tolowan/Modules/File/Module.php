<?php
function filePath($file)
{
    global $di;
    if ($file->access == 1) {
        return $di->getShared('url')->get(array(
            'for' => 'privateFile',
            'id' => $fileModel->id,
        ));
    } else {
        return '/' . $file->path;
    }
}
function fileTypeIcon($content_type)
{
    if (file_exists(WEB_DIR . 'modules/file/images/' . $content_type . '.png')) {
        return '/modules/file/images/' . $content_type . '.png';
    } else {
        return '/modules/file/images/none.png';
    }
}
