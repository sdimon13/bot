<?php

namespace App\Jobs;

use App\Models\ObjectsEvent;
use App\Models\User;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ObjectsEventsSendingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $objectsEventId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $objectsEventId)
    {
        $this->objectsEventId = $objectsEventId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $objectsEvent = ObjectsEvent::find($this->objectsEventId);
        $botman = resolve('botman');

        User::where('type_id', 1)->each(function ($user) use ($objectsEvent, $botman) {
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

        $objectsEvent->is_processed = true;
        $objectsEvent->save();
    }
}
