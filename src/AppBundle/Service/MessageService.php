<?php

namespace AppBundle\Service;

use AppBundle\Entity\Message;

class MessageService
{
    /**
     * @param Array $messages
     *
     * @return false|string
     */
    public function messagesToJson($messages)
    {
        $obj = [];

        foreach ($messages as $message) {
            $m = [];
            $m['id'] = $message->getId();
            $m['text'] = stripslashes($message->getText());
            $m['type'] = $message->getType();
            $m['datetime'] = $message->getDatetime();

            $obj['messages'][] = $m;
        }

        return json_encode($obj);
    }
}
