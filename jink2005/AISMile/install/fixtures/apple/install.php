<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * This class is only here to show the possibility of extending InstallXmlLoader, which is the
 * class parsing all XML files, copying all images, etc.
 *
 * Please read documentation in ~/install/dev/ folder if you want to customize MileBiz install / fixtures.
 */
class InstallFixturesApple extends InstallXmlLoader
{
	public function createEntityCustomer($identifier, array $data, array $data_lang)
	{
		if ($identifier == 'John')
			$data['passwd'] = Tools::encrypt('123456789');

		return $this->createEntity('customer', $identifier, 'Customer', $data, $data_lang);
	}
}
