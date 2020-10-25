<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Admin\Validator;

use Admin\Entity\AdminUser;
use Zend\Validator\AbstractValidator;

/**
 * 检查账户是否已经存在或者重复
 * Class UserExistsValidator
 * @package Admin\Validator
 */
class UserExistsValidator extends AbstractValidator
{

    const NOT_SCALAR    = 'notScalar';
    const USER_EXISTS   = 'userExists';

    protected $options = [
        'entityManager',
        'user'
    ];

    protected $messageTemplates = [
        self::NOT_SCALAR    => "这不是一个标准输入值",
        self::USER_EXISTS   => "该账户已经存在"
    ];

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->options['entityManager'] = $options['entityManager'];
            if(isset($options['user']))             $this->options['user']          = $options['user'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $entityManager  = $this->options['entityManager'];
        $user           = $entityManager->getRepository(AdminUser::class)->findOneByAdminName($value);

        if($this->options['user'] == null) {
            $isValid = ($user==null);
        } else {
            if($this->options['user']->getAdminName() != $value && $user != null)
                $isValid = false;
            else
                $isValid = true;
        }

        if(!$isValid) $this->error(self::USER_EXISTS);

        return $isValid;
    }
}