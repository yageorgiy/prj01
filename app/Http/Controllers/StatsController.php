<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StatsController extends Controller
{

    /**
     * Create event type from name
     * @param Request $request
     * @return JsonResponse
     */
    public function createEventType(Request $request): JsonResponse
    {
        $data = $request->all();

        $v = Validator::make($data, [
            'event_name' => ['required'],
        ]);

        // Whether the validation is incorrect
        if ($v->fails())
            return Response::json([
                "error" => "Request body is incorrect"
            ])->setStatusCode(400);

        // Enquiring event type
        if (
            EventType::query()
                ->where("event_name", "=", $data["event_name"])
                ->first() != null
        )
            return Response::json([
                "error" => "EventType already exists"
            ])->setStatusCode(400);

        $eventType = new EventType();
        $eventType->event_name = $data["event_name"];
        $eventType->save();

        return Response::json($eventType)->setStatusCode(201);
    }

    /**
     * Submit event for specific user (1 = anonymous, other = any user)
     * @param Request $request
     * @return JsonResponse
     */
    public function submit(Request $request): JsonResponse
    {
        $data = $request->all();

        $v = Validator::make($data, [
            'user_id' => ['integer', 'required'],
            'event_name' => ['required'],
        ]);

        // Whether the validation is incorrect
        if ($v->fails())
            return Response::json([
                "error" => "Request body is incorrect"
            ])->setStatusCode(400);


        // Enquiring user
        if (
            User::query()
                ->where("id", "=", $data["user_id"])
                ->first() == null
        )
            return Response::json([
                "error" => "User not found"
            ])->setStatusCode(400);

        // Enquiring event type
        $eventType = EventType::query()
            ->where("event_name", "=", $data["event_name"])
            ->first();

        if ($eventType == null)
            return Response::json([
                "error" => "EventType not found"
            ])->setStatusCode(400);

        $event = new Event();
        $event->event_type_id = $eventType->id;
        $event->user_id = $data["user_id"];
        $event->ip_address = $request->ip();
        $event->save();

        return Response::json($event)->setStatusCode(201);
    }

    /**
     * Retrieve statistics (name, date required)
     * @param Request $request
     * @return JsonResponse
     */
    public function stats(Request $request): JsonResponse
    {
        $data = $request->all();

        $v = Validator::make($data, [
            'type' => Rule::in([Event::TYPE_PER_EVENT, Event::TYPE_PER_USER, Event::TYPE_PER_AUTH_STATUS]),
            'event_type_name' => ['required'],
            'event_date_begin' => ['required', 'date'],
            'event_date_end' => ['required', 'date'],
        ]);

        // Whether the validation is incorrect
        if ($v->fails())
            return Response::json([
                "error" => "Request body is incorrect"
            ])->setStatusCode(400);

        // Requesting event type instance
        $eventType = EventType::query()
            ->where("event_name", "=", $data["event_type_name"])
            ->first();

        // Validating whether the event type exists
        if ($eventType == null)
            return Response::json([
                "error" => "Event type not found"
            ])->setStatusCode(400);

        // Response depends on type
        $response = match ((int)$data["type"]) {
            Event::TYPE_PER_EVENT => $this->statsPerEvent($eventType, $data),
            Event::TYPE_PER_USER => $this->statsPerUser($eventType, $data),
            Event::TYPE_PER_AUTH_STATUS => $this->statsPerAuthStatus($eventType, $data)
        };


        return Response::json($response);
    }

    /**
     * Retrieve statistics per event
     * @param Model $eventType
     * @param array $data
     * @return Collection|array
     */
    private function statsPerEvent(Model $eventType, array $data): Collection|array
    {
        return Event::query()
            // Selecting count and event type id
            ->select(DB::raw("COUNT(id) as count"), "event_type_id")

            // Request params
            ->where("event_type_id", "=", $eventType->id)
            ->where("created_at", ">=", $data["event_date_begin"])
            ->where("created_at", "<=", $data["event_date_end"])

            // Grouping by event type
            ->groupBy("event_type_id")
            ->with("eventType")
            ->get();
    }

    /**
     * Retrieve statistics per user
     * @param Model $eventType
     * @param array $data
     * @return Collection|array
     */
    private function statsPerUser(Model $eventType, array $data): Collection|array
    {
        return Event::query()
            // Selecting count and user id
            ->select(DB::raw("COUNT(id) as count"), "user_id")

            // Request params
            ->where("event_type_id", "=", $eventType->id)
            ->where("created_at", ">=", $data["event_date_begin"])
            ->where("created_at", "<=", $data["event_date_end"])

            // Grouping by user id
            ->groupBy("user_id")
            ->with("user")
            ->get();
    }

    /**
     * Retrieve statistics per authentication status
     * @param Model $eventType
     * @param array $data
     * @return Collection|array
     */
    private function statsPerAuthStatus(Model $eventType, array $data): Collection|array
    {
        return Event::query()
            // Selecting sum per authorized status and virtual authorized status column
            ->select(
                DB::raw("COUNT(id) as count"),
                DB::raw("CASE WHEN user_id = 1 THEN 0 ELSE 1 END AS authorized")
            )

            // Request params
            ->where("event_type_id", "=", $eventType->id)
            ->where("created_at", ">=", $data["event_date_begin"])
            ->where("created_at", "<=", $data["event_date_end"])

            // Grouping by authorized status
            ->groupBy("authorized")
            ->get();
    }

}
