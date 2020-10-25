<?php

declare(strict_types=1);

namespace TencentAI\Tests;

class TencentAITest extends TencentAITestCase
{
    public function test(): void
    {
        $this->assertEquals('18.06.08', $this->ai()->getVersion());
    }
}
