<?php
use App\Http\Controllers\EventController;
use \App\Http\Controllers\UserController;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});

$botman->hears('/events', EventController::class.'@startConversation');
$botman->hears('/sign', UserController::class.'@startConversation');
$botman->hears('/change_type', UserController::class.'@changeType');