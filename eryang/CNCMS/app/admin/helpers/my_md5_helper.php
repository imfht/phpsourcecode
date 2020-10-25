<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * 自定义 md5 加密算法
 *
 * @access   public
 * @param    string   需加密的字符串
 * @return   string   加密后的字符串
 */
function str_md5($str) {
	//123456=>3a0f0fee92a4fd13fb3f1c2bfcb3eac5 
	return md5(base64_encode(SITE_ADMIN_ENCRYPTION_KEY_BEGIN) . md5($str) . base64_encode(SITE_ADMIN_ENCRYPTION_KEY_END));
}
// ------------------------------------------------------------------------
/* End of file my_md5_helper.php */
/* Location: ./app/admin/helpers/my_md5_helper.php */
