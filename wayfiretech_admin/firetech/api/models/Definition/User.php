<?php

namespace api\models\definitions;

/**
 * @SWG\Definition(required={"username", "email"})
 *
 * @SWG\Property(property="success", type="string")
 * @SWG\Property(property="code", type="integer")
 * @SWG\Property(property="message", type="string")
 * @SWG\Property(property="data", type="object")
 */
class User
{
}
