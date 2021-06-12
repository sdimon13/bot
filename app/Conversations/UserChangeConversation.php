<?php

namespace App\Conversations;

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class UserChangeConversation extends Conversation
{
    /**
     * Start the conversation
     */
    public function run()
    {
        $this->changeType();
    }

    public function changeType()
    {
        $botUser = $this->bot->getUser();
        $id = $botUser->getId();

        $user = User::where('telegram_id', $id)->first();
        $user->type_id = 2;
        $user->save();

        $this->say(
            "Здравствуйте Ваш тип изменен на Администратора" .
            "\n\nВаше имя:  " . $user->name .
            "\n\nВаш идентификатор: " . $user->telegram_id
        );
    }

}
