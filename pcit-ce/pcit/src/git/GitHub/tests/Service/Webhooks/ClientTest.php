<?php

declare(strict_types=1);

namespace PCIT\GitHub\Tests\Service\Webhooks;

use PCIT\PCIT;
use Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * @var PCIT
     */
    public $pcit;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->pcit = app('pcit');
        parent::setUp();
    }

    /**
     * @param $event
     *
     * @throws \Exception
     */
    public function common($event): void
    {
        $algo = 'sha1';

        $request_body = file_get_contents(__DIR__.'/../../webhooks/github/'.$event.'.json');

        $secret = hash_hmac($algo, $request_body, config('git.webhooks.token'));

        $response = $this->request(
            '/',
            'POST',
            [],
            [],
            [],
            [
                'HTTP_X-Github-Event' => $event,
                'HTTP_X-Hub-Signature' => 'sha1='.$secret,
                'REQUEST_TIME_FLOAT' => microtime(true),
            ],
            $request_body
        );

        $this->assertStringMatchesFormat('%s', (string) $response->getContent());
    }

    /**
     * @throws \Exception
     */
    public function testPush(): void
    {
        $event = 'push';

        $this->common($event);
    }
}
