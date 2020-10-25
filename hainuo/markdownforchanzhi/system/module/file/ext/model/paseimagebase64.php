<?php
/**
 * Created by PhpStorm.
 * User: fengliu
 * Date: 15/8/18
 * Time: 上午12:13
 */

/**
 * Paste image in kindeditor at firefox and chrome.
 *
 * @param  string $data
 * @param  string $uid
 * @access public
 * @return string
 */
public function pasteImageBase64($data, $uid)
{
    if (!$this->checkSavePath()) return false;

    ini_set('pcre.backtrack_limit', strlen($data));
    preg_match('/data:image\/(\S+);base64,(\S+)/', $data, $out);
    if($out && !empty($out[2])) {
        $imageData = base64_decode($out[2]);

        $file['extension'] = $out[1];
        $file['pathname'] = $this->setPathName($key, $file['extension']);
        $file['size'] = strlen($imageData);
        $file['addedBy'] = $this->app->user->account;
        $file['addedDate'] = helper::today();
        $file['title'] = basename($file['pathname']);
        $file['editor'] = 1;

        file_put_contents($this->savePath . $file['pathname'], $imageData);
        $this->compressImage($this->savePath . $file['pathname']);

        $imageSize = $this->getImageSize($this->savePath . $file['pathname']);
        $file['width'] = $imageSize['width'];
        $file['height'] = $imageSize['height'];
        $file['lang'] = 'all';

        $this->dao->insert(TABLE_FILE)->data($file)->exec();
        $_SESSION['album'][$uid][] = $this->dao->lastInsertID();

        $data = $this->webPath . $file['pathname'];

        return $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$data;
    }else{
        return ' ';
    }
}
