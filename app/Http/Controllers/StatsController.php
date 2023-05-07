<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StatsController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $data = $request->all();

        $v = Validator::make($data, [
            'event_name' => ['required'],
        ]);

        if ($v->fails())
            return Response::json([
                "error" => "Request body is incorrect"
            ])->setStatusCode(400);

        $user = Auth::user();

        $event = new Event();
        $event->event_name = $data["event_name"];
        $event->user_id = ($user == null) ? 0 : $user->id;
        $event->ip_address = $request->ip();
        $event->save();

        return Response::json($event)->setStatusCode(201);
    }

    public function stats(Request $request): JsonResponse
    {
        $data = $request->all();

        $v = Validator::make($data, [
            'type' => Rule::in([Event::TYPE_PER_EVENT, Event::TYPE_PER_USER, Event::TYPE_PER_AUTH_STATUS]),
            'event_name' => ['required'],
            'event_date_begin' => ['required', 'date'],
            'event_date_end' => ['required', 'date'],
        ]);

        if ($v->fails())
            return Response::json([
                "error" => "Request body is incorrect"
            ])->setStatusCode(400);


        // TODO

        return Response::json([]);
    }

}
