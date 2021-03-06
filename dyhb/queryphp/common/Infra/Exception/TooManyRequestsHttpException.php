<?php

declare(strict_types=1);

/*
 * This file is part of the your app package.
 *
 * The PHP Application For Code Poem For You.
 * (c) 2018-2099 http://yourdomian.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Infra\Exception;

use Leevel\Kernel\Exception\TooManyRequestsHttpException as BaseTooManyRequestsHttpException;

/**
 * 请求过于频繁异常.
 *
 * - 用户在给定的时间内发送了太多的请求: 429.
 */
class TooManyRequestsHttpException extends BaseTooManyRequestsHttpException
{
}
