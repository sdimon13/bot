<?php

namespace App\Console\Commands;

use App\Models\ObjectsEvent;
use App\Models\User;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Console\Command;

class TestSendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test send message';

    public function handle()
    {
        $botman = resolve('botman');

        $objectEvent = ObjectsEvent
            ::orderBy('event_datetime',  'desc')
            ->first();

        $user = User::first();

        $botman->say(
            "Время: " . $objectEvent->event_datetime .
            "\n\nОбьект: " . $objectEvent->object_number . ' ( ' . $objectEvent->object_name . ' )' .
            "\n\nСобытие: " . $objectEvent->description_name . ' ( ' . $objectEvent->description_comment . ' )' .
            "\n\nЗона: " . $objectEvent->zone_name .
            "\n\nУстройство: " . $objectEvent->device_type . ' ( ' . $objectEvent->device_name . ' )' .
            "\n\nАдрес: " . $objectEvent->address .
            "\n\nКонтактные лица:\n" . implode(",\n", $objectEvent->contacts)
        , $user->telegram_id, TelegramDriver::class);
        return 0;
    }
}
