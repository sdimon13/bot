<?php

namespace App\Conversations;

use App\Models\ObjectsEvent;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class EventConversation extends Conversation
{
    /**
     * Start the conversation
     */
    public function run()
    {
        $this->showEvent();
    }


    public function showEvent()
    {
        $objectEvent = ObjectsEvent
            ::orderBy('event_datetime',  'desc')
            ->first();

        $this->say(
            "Время: " . $objectEvent->event_datetime .
            "\n\nОбьект: " . $objectEvent->object_number . ' ( ' . $objectEvent->object_name . ' )' .
            "\n\nСобытие: " . $objectEvent->description_name . ' ( ' . $objectEvent->description_comment . ' )' .
            "\n\nЗона: " . $objectEvent->zone_name .
            "\n\nУстройство: " . $objectEvent->device_type . ' ( ' . $objectEvent->device_name . ' )' .
            "\n\nАдрес: " . $objectEvent->address .
            "\n\nКонтактные лица:\n" . implode(",\n", $objectEvent->contacts)
        );
    }

}
