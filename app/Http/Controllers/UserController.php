<?php

namespace App\Http\Controllers;

use App\Conversations\UserConversation;
use App\Conversations\UserChangeConversation;
use App\Models\Event;
use App\Models\GuardObject;
use App\Models\User;
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

    public function list()
    {
        $users = User
            ::orderBy('id', 'desc')
            ->get()
            ->transform(function ($item) {
                $item->setAppends(['type_title']);
                return $item;
            });
        return view('users', compact('users'));
    }

    public function get(int $id)
    {
        return \DB::transaction(function() use ($id)
        {
            $user = User::with('objects')->findOrFail($id);

            $user->objects->transform(function ($object) {
                $guardObject = GuardObject
                    ::with('entity')
                    ->find($object->object_id);

                $object->name = $guardObject->NAME;
                $object->number = $guardObject->entity->STR_IDENT;
                return $object;
            });

            $objects = GuardObject
                ::with('entity:ID,STR_IDENT')
                ->select('ID', 'ENTITY_ID', 'NAME')
                ->whereNotIn('ID', $user->objects->pluck('object_id')->toArray())
                ->get()
                ->transform(function ($guardObject) {
                    $guardObject->number = $guardObject->entity->STR_IDENT;
                    return $guardObject;
                })
                ->sortBy('number');

            $result = ['user' => $user, 'objects' => $objects];

            return view('user', $result);
        });
    }

    public function add(Request $request)
    {
        return \DB::transaction(function() use ($request)
        {
            $data = \Request::input();
            $data['email'] = $data['name'] . '@bot.api';
            User
                ::fields('name', 'telegram_id', 'email')
                ->validate($data)
                ->create($data);

            return redirect()->route('users');
        });
    }

    public function delete(int $id)
    {
        return \DB::transaction(function() use ($id)
        {
            $user = User::findOrFail($id);
            $user->objects()->delete();
            $user->delete();

            return redirect()->route('users');
        });
    }

    public function upgrade(int $id)
    {
        return \DB::transaction(function() use ($id)
        {
            $user = User::findOrFail($id);
            $user->type_id = 2;
            $user->save();

            return redirect()->route('users');
        });
    }
}
