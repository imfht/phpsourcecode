<?php

namespace api\models\definitions;

/**
 * @SWG\Definition(required={"access_token", "username"}, @SWG\Xml(name="UserIdList"))
 */
class Help
{
    /**
     * Access Token
     *
     * @SWG\Property()
     *
     * @var string
     */
    public $access_token;
    /**
     * @SWG\Property()
     *
     * @var Id[]
     */
    public $idList;
    public $id;
    /**
     * @SWG\Property()
     *
     * @var ssss[]
     */
    public $ssss;
}
