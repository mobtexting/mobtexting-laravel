<?php

namespace NotificationChannels\Mobtexting;

use Mobtexting\Rest\Client as MobtextingService;
use NotificationChannels\Mobtexting\Exceptions\CouldNotSendNotification;

class Mobtexting
{
    /**
     * @var MobtextingService
     */
    protected $client;

    /**
     * @var MobtextingConfig
     */
    private $config;

    /**
     * Mobtexting constructor.
     *
     * @param MobtextingService $client
     * @param MobtextingConfig $config
     */
    public function __construct(MobtextingService $client, MobtextingConfig $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * Send a MobtextingMessage to the a phone number.
     *
     * @param MobtextingMessage $message
     * @param string $to
     * @param bool $useAlphanumericSender
     * @return mixed
     * @throws CouldNotSendNotification
     */
    public function sendMessage(MobtextingMessage $message, $to)
    {
        if ($message instanceof MobtextingSmsMessage) {
            return $this->sendSmsMessage($message, $to);
        }

        throw CouldNotSendNotification::invalidMessageObject($message);
    }

    /**
     * Send an sms message using the Mobtexting Service.
     *
     * @param MobtextingSmsMessage $message
     * @param string $to
     * @return \Mobtexting\Rest\Api\V2010\Account\MessageInstance
     * @throws CouldNotSendNotification
     */
    protected function sendSmsMessage(MobtextingSmsMessage $message, $to)
    {
        $params = [
            'from' => $this->getFrom($message),
            'body' => $message->getText(),
            'to' => $message->getTo() ?: $to,
        ];

        $params = array_merge($message->getParams(), $params);

        return $this->client->messages->send($to, $params);
    }

    /**
     * Get the from address from message, or config.
     *
     * @param MobtextingMessage $message
     * @return string
     * @throws CouldNotSendNotification
     */
    protected function getFrom(MobtextingMessage $message)
    {
        if (!$from = $message->getFrom() ?: $this->config->getFrom()) {
            throw CouldNotSendNotification::missingFrom();
        }

        return $from;
    }
}
