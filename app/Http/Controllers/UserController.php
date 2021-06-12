<?php

namespace App\Http\Controllers;

use App\Conversations\UserConversation;
use App\Conversations\UserChangeConversation;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use App\Conversations\EventConversation;

class UserController extends Controller
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
        $bot->startConversation(new UserConversation());
    }

    public function changeType(BotMan $bot)
    {
        $bot->startConversation(new UserChangeConversation());
    }
}
