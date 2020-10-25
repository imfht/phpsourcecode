<?php

namespace Bluehouseapp\Bundle\CoreBundle\Service;

use Bluehouseapp\Bundle\CoreBundle\Entity\Post;
use Bluehouseapp\Bundle\CoreBundle\Entity\PostComment;
use Bluehouseapp\Bundle\CoreBundle\Entity\Member;
class PostService {

    protected $mail;
    protected $em;
    protected $security;
    protected $route;
    protected $req;

    public function __construct($em,$mail,$security,$route,$req)
    {
        $this->mail = $mail;
        $this->em   = $em;
        $this->security = $security;
        $this->route = $route;
        $this->req = $req;
    }


} 