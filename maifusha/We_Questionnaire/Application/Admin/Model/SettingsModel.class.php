<?php 
namespace Admin\Model;
use Think\Model;

class SettingsModel extends Model
{
	/**
	 * 将系统配置入库
	 * @param array $configList  配置数组
	 * @return bool  入库成功返回true，反之false并设置错误消息
	 */
	public function saveConfig($configList)
	{
		$configList['weixin_domain'] = I('server.SERVER_NAME');

		foreach ($configList as $name => $value) {
			$state = $this->where("name='$name'")->setField('value', $value);

			if( $state === false ){ //配置更新失败
				$this->error = $this->getDbError();
				return false;
			}
		}

		return true; //配置更新成功
	}
}