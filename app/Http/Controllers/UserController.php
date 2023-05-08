<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Registering user for statistics
     * @param Request $request
     * @return JsonResponse
     */
    public function registration(Request $request): JsonResponse
    {
        $data = $request->all();

        $v = Validator::make($data, [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Validating fields
        if ($v->fails())
            return Response::json([
                "error" => "Request body is incorrect"
            ])->setStatusCode(400);


        // Whether the email exists
        if (
            User::query()
                ->where("email", "=", $data["email"])
                ->first() != null
        )
            return Response::json([
                "error" => "Email exists"
            ])->setStatusCode(409);

        $user = new User();
        $user->name = $data["name"];
        $user->password = Hash::make($data["password"]);
        $user->email = $data["email"];
        $user->save();

        return Response::json($user)->setStatusCode(201);
    }
}
