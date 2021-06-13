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

        $objectsEvent = ObjectsEvent
            ::orderBy('event_datetime',  'desc')
            ->first();

        User
            ::where('type_id', 2)
            ->orWhereHas('objects', function ($query) use ($objectsEvent) {
                $query->where('object_id', $objectsEvent->object_id);
            })
            ->each(function ($user) use ($objectsEvent, $botman) {
                $botman->say(
                    "Время: " . $objectsEvent->event_datetime .
                    "\n\nОбьект: " . $objectsEvent->object_number . ' ( ' . $objectsEvent->object_name . ' )' .
                    "\n\nСобытие: " . $objectsEvent->description_name . ' ( ' . $objectsEvent->description_comment . ' )' .
                    "\n\nЗона: " . $objectsEvent->zone_name .
                    "\n\nУстройство: " . $objectsEvent->device_type . ' ( ' . $objectsEvent->device_name . ' )' .
                    "\n\nАдрес: " . $objectsEvent->address .
                    "\n\nКонтактные лица:\n" . implode(",\n", $objectsEvent->contacts)
                    , $user->telegram_id, TelegramDriver::class);
            });
        return 0;
    }
}
