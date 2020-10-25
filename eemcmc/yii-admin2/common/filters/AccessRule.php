<?php

namespace common\filters;

/**
 * Description of AccessRule
 *
 * @author ken <vb2005xu@qq.com>
 */
class AccessRule extends \yii\filters\AccessRule
{

	/**
	 * @param User $user the user object
	 * @return boolean whether the rule applies to the role
	 */
	protected function matchRole($user)
	{
		if (empty($this->roles))
		{
			return true;
		}
		if (!empty($user->identity) && in_array($user->identity->role, $this->roles))
		{
			return true;
		}
		foreach ($this->roles as $role)
		{
			if ($role === '?')
			{
				if ($user->getIsGuest())
				{
					return true;
				}
			}
			elseif ($role === '@')
			{
				if (!$user->getIsGuest())
				{
					return true;
				}
			}
			elseif ($user->can($role))
			{
				return true;
			}
		}

		return false;
	}

}
