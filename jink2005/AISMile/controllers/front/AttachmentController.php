<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AttachmentControllerCore extends FrontController
{
	public function postProcess()
	{
		$a = new Attachment(Tools::getValue('id_attachment'), $this->context->language->id);
		if (!$a->id)
			Tools::redirect('index.php');

		header('Content-Transfer-Encoding: binary');
		header('Content-Type: '.$a->mime);
		header('Content-Length: '.filesize(_PS_DOWNLOAD_DIR_.$a->file));
		header('Content-Disposition: attachment; filename="'.utf8_decode($a->file_name).'"');
		readfile(_PS_DOWNLOAD_DIR_.$a->file);
		exit;
	}
}