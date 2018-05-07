<?php

namespace NotificationChannels\Mobtexting;

class MobtextingSmsMessage extends MobtextingMessage
{
    /**
     * @var null|string
     */
    public $alphaNumSender = null;

    /**
     * Get the from address of this message.
     *
     * @return null|string
     */
    public function getFrom()
    {
        if ($this->from) {
            return $this->from;
        }

        if ($this->alphaNumSender && strlen($this->alphaNumSender) > 0) {
            return $this->alphaNumSender;
        }
    }

    /**
     * Set the alphanumeric sender.
     *
     * @param string $sender
     * @return $this
     */
    public function sender($sender)
    {
        $this->alphaNumSender = $sender;

        return $this;
    }

}
