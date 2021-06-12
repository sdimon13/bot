<?php

namespace App\Http\Controllers;

use App\Models\Event;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\EventConversation;

class EventController extends Controller
{

    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    public $response = [];

    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new EventConversation());
    }

    public function list()
    {
        return Event
            ::select('ID', 'E_DATETIME', 'SOURCE_OBJECT', 'HW_SOURCE_OBJECT', 'DESCRIPTION_REF', 'RESPONSIBILITY_REF')
            ->orderBy('ID', 'desc')
            ->take(10)
            ->get()
            ->toArray();
    }
}
