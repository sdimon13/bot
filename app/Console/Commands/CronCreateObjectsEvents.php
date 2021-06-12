<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\ObjectsEvent;
use App\Services\ObjectsEventService;
use Illuminate\Console\Command;

class CronCreateObjectsEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:objects-events:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create objects events';

    public function handle()
    {
        $i = 0;
        while( ++$i <= 12 ) {
            $this->createObjectsEvents();
            sleep(5);
        }
        return 0;
    }

    private function createObjectsEvents()
    {
        $eventId = ObjectsEvent::orderBy('event_id', 'desc')->first()->event_id ?? null;

        $events = Event::has('object')
            ->when($eventId, function ($query) use ($eventId) {
                $query->where('ID', '>', $eventId);
            });

            //$this->info('Количество новых событий' . $events->count());

            $events->each(function ($event) {
                \App::make(ObjectsEventService::class)->makeObjectEvent($event);
            });


    }
}
