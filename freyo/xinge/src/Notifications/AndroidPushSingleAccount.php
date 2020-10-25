<?php

namespace Freyo\Xinge\Notifications;

use Freyo\Xinge\AndroidChannel;
use Freyo\Xinge\Client;
use Freyo\Xinge\Client\ClickAction;
use Freyo\Xinge\Client\Message;
use Freyo\Xinge\Client\Style;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class AndroidPushSingleAccount extends Notification
{
    protected $content;
    protected $title;
    protected $custom;

    /**
     * Create a new notification instance.
     */
    public function __construct($content, $title = '', $custom = null)
    {
        $this->title = $title;
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
        return [AndroidChannel::class];
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

        $message = new Message();
        $message->setTitle($this->title);
        $message->setContent($this->content);
        $message->setType(Message::TYPE_MESSAGE);
        $message->setStyle(new Style(0, 1, 1, 1, 0));

        $action = new ClickAction();
        $action->setActionType(ClickAction::TYPE_ACTIVITY);
        $message->setAction($action);

        $message->setCustom($this->custom);

        return function (Client $client) use ($account, $message) {
            return $client->PushSingleAccount(0, (string) $account, $message);
        };
    }
}
