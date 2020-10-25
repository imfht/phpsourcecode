<?php

namespace Freyo\Xinge\Notifications;

use Freyo\Xinge\Client;
use Freyo\Xinge\Client\MessageIOS;
use Freyo\Xinge\Client\XingeApp;
use Freyo\Xinge\iOSChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class iOSPushSingleAccount extends Notification
{
    protected $content;
    protected $custom;

    /**
     * Create a new notification instance.
     */
    public function __construct($content, $custom = null)
    {
        $this->content = $content;
        $this->custom = $custom;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return [iOSChannel::class];
    }

    /**
     * @param              $notifiable
     * @param Notification $notification
     *
     * @return \Closure
     */
    public function toXinge($notifiable, Notification $notification)
    {
        $account = $notifiable instanceof Model
            ? $notifiable->routeNotificationFor('Xinge') : $notifiable;

        $environment = config('app.env') === 'production'
            ? XingeApp::IOSENV_PROD : XingeApp::IOSENV_DEV;

        $message = new MessageIOS();
        $message->setAlert($this->content);
        $message->setBadge(1);
        $message->setCustom($this->custom);

        return function (Client $client) use ($account, $message, $environment) {
            return $client->PushSingleAccount(0, (string) $account, $message, $environment);
        };
    }
}
