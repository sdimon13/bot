<?php

namespace App\Http\Controllers;

use App\Models\ObjectsEvent;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $objectEvents = ObjectsEvent
            ::simplePaginate(15);
        return view('home', compact('objectEvents'));
    }
}
