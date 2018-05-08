<?php

namespace NotificationChannels\Mobtexting;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use NotificationChannels\Mobtexting\Exceptions\CouldNotSendNotification;

class MobtextingChannel
{
    /**
     * @var Mobtexting
     */
    protected $client;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * MobtextingChannel constructor.
     *
     * @param Mobtexting     $client
     * @param Dispatcher $events
     */
    public function __construct(Mobtexting $client, Dispatcher $events)
    {
        $this->client = $client;
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed                                  $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return mixed
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        try {
            $to = $this->getTo($notifiable);
            $message = $notification->toMobtexting($notifiable);

            if (is_string($message)) {
                $message = new MobtextingSmsMessage($message);
            }

            if (!$message instanceof MobtextingMessage) {
                throw CouldNotSendNotification::invalidMessageObject($message);
            }

            return $this->client->sendMessage($message, $to);
        } catch (Exception $exception) {
            $event = new NotificationFailed($notifiable, $notification, 'mobtexting', ['message' => $exception->getMessage(), 'exception' => $exception]);
            if (function_exists('event')) { // Use event helper when possible to add Lumen support
                event($event);
            } else {
                $this->events->fire($event);
            }
        }
    }

    /**
     * Get the address to send a notification to.
     *
     * @param mixed $notifiable
     * @return mixed
     */
    protected function getTo($notifiable)
    {
        if ($notifiable->routeNotificationFor('mobtexting')) {
            return $notifiable->routeNotificationFor('mobtexting');
        }

        if (isset($notifiable->phone_number)) {
            return $notifiable->phone_number;
        }

        if (isset($notifiable->mobile)) {
            return $notifiable->mobile;
        }

        return false;
    }
}
