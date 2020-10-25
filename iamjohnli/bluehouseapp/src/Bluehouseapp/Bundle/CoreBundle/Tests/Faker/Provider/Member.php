<?php

namespace Bluehouseapp\Bundle\CoreBundle\Tests\Faker\Provider;

class Member extends \Faker\Provider\Base
{
    public function username($nbWords = 5)
    {
        $sentence = $this->generator->sentence($nbWords);
        return substr($sentence,0,strlen($sentence) -1);n
    }

    public function password()
    {
        $sentence = $this->generator->sentence($nbWords);
        return substr($sentence,0,strlen($sentence) -1);        
    }
}