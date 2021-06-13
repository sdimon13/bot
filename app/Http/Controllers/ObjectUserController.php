<?php

namespace App\Http\Controllers;

use App\Models\ObjectUser;
use Illuminate\Http\Request;

class ObjectUserController extends Controller
{
    public function add(Request $request)
    {
        return \DB::transaction(function() use ($request)
        {
            ObjectUser
                ::fields('object_id', 'user_id')
                ->validate(\Request::input())
                ->create(\Request::input());

            return redirect()->route('user.get', $request->user_id);
        });
    }

    public function delete(int $id)
    {
        return \DB::transaction(function() use ($id)
        {
            $objectUser = ObjectUser::findOrFail($id);
            $userId = $objectUser->user_id;

            $objectUser->delete();

            return redirect()->route('user.get', $userId);
        });
    }
}
