<?php

namespace App\Conversations;

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class UserConversation extends Conversation
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
        $user = $this->bot->getUser();
        $id = $user->getId();
        $name = $user->getUsername();

        $userData = [
            'telegram_id' => $id,
            'name' => $name,
        ];

        $user = User::fields(array_keys($userData))->create($userData);

        $this->say(
            "Здравствуйте" .
            "\n\nВаше имя:  " . $user->name .
            "\n\nВаш идентификатор: " . $user->telegram_id
        );
    }

}
