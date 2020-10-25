<?php

namespace Freyo\Xinge;

use Freyo\Xinge\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;

class AndroidChannel
{
    /** @var Client */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     *
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $callback = $notification->toXinge($notifiable, $notification);

        tap($this->client, $callback);
    }
}
