<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'min:1', 'max:100'],
            'place' => ['required', 'string', 'min:1', 'max:255'],
            'date' => ['required', 'date', 'date_format:Y-m-d']
        ]);

        $id = Event::saveToCache($request->all());

        return response()->json([
            'success' => true,
            'id' => $id
        ]);
    }

    public function index($id)
    {
        $event = Event::getFromCache($id);
        return  $event === false ? response()->json(['answer' => 'not found'], 404) :
            response()->json(['answer' => $event->toText()]);
    }
}
